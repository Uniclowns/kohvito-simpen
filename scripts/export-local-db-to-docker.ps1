# ============================================================================
# export-local-db-to-docker.ps1
# Migrasi database SIMPEN Kohvito: MySQL Laragon (Windows) -> MySQL Docker.
# Aman: SELALU backup database Docker dulu, tidak pernah drop otomatis.
#
# Jalankan dari PowerShell, dari root proyek:
#   powershell -ExecutionPolicy Bypass -File scripts\export-local-db-to-docker.ps1
# ============================================================================
$ErrorActionPreference = "Stop"
Set-Location (Split-Path $PSScriptRoot -Parent)

$BackupDir = "backup-database"
$TS = Get-Date -Format "yyyyMMdd-HHmmss"
New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null

# --- Baca konfigurasi lokal dari .env ---------------------------------------
function Get-EnvVal($key, $default) {
    $line = Select-String -Path ".env" -Pattern "^$key=" | Select-Object -First 1
    if ($line) { return ($line.Line -replace "^$key=", "").Trim('"') }
    return $default
}
$LocalDb   = Get-EnvVal "DB_DATABASE" "kohvito-simpen"
$LocalUser = Get-EnvVal "DB_USERNAME" "root"
$LocalPass = Get-EnvVal "DB_PASSWORD" ""
$LocalHost = "127.0.0.1"
$LocalPort = "3306"    # MySQL Laragon

# --- Konfigurasi Docker (dari compose.yaml) ----------------------------------
$DbService  = "db"
$AppService = "app"
$DockerDb   = "kohvito-simpen"
# Password root TIDAK ditulis di sini: dibaca dari env container ($MYSQL_ROOT_PASSWORD).

# --- mysqldump lokal: cari milik Laragon/XAMPP bila tak ada di PATH ----------
$MysqlDump = "mysqldump"
if (-not (Get-Command mysqldump -ErrorAction SilentlyContinue)) {
    $candidates = @(
        "D:\laragon\bin\mysql\mysql-*\bin\mysqldump.exe",
        "C:\laragon\bin\mysql\mysql-*\bin\mysqldump.exe",
        "C:\xampp\mysql\bin\mysqldump.exe"
    )
    foreach ($pat in $candidates) {
        $found = Get-Item $pat -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($found) { $MysqlDump = $found.FullName; break }
    }
}
Write-Host "==> mysqldump: $MysqlDump"

Write-Host "==> [1/6] Cek container Docker..."
docker compose ps
$dbUp = docker compose ps $DbService
if (-not ($dbUp -match "Up")) { throw "Service $DbService tidak jalan. Jalankan: docker compose up -d" }

Write-Host "==> [2/6] Backup database Docker (sebelum import)..."
$DockerBackup = "$BackupDir\docker-before-import-$TS.sql"
docker compose exec -T $DbService sh -c "exec mysqldump -uroot -p`"`$MYSQL_ROOT_PASSWORD`" --single-transaction --quick --routines --triggers --events --default-character-set=utf8mb4 --set-gtid-purged=OFF $DockerDb" 2>$null |
    Out-File -FilePath $DockerBackup -Encoding utf8
Write-Host "    -> $DockerBackup"

Write-Host "==> [3/6] Export database lokal Laragon ($LocalHost`:$LocalPort/$LocalDb)..."
$LocalExport = "$BackupDir\local-export-$TS.sql"
$dumpArgs = @("-h", $LocalHost, "-P", $LocalPort, "-u", $LocalUser)
if ($LocalPass) { $dumpArgs += "-p$LocalPass" }
$dumpArgs += @("--single-transaction", "--quick", "--routines", "--triggers", "--events",
               "--default-character-set=utf8mb4", "--set-gtid-purged=OFF", $LocalDb)
& $MysqlDump @dumpArgs | Out-File -FilePath $LocalExport -Encoding utf8
if (-not (Select-String -Path $LocalExport -Pattern "Dump completed" -Quiet)) {
    throw "Export lokal tampak tidak lengkap: $LocalExport"
}
Write-Host "    -> $LocalExport"

Write-Host "==> [4/6] Bersihkan klausa DEFINER..."
(Get-Content $LocalExport -Raw) -replace "DEFINER=``[^``]+``@``[^``]+``", "" |
    Set-Content $LocalExport -Encoding utf8

Write-Host "==> [5/6] Import ke MySQL Docker (database: $DockerDb)..."
docker compose exec -T $DbService sh -c "exec mysql -uroot -p`"`$MYSQL_ROOT_PASSWORD`" --default-character-set=utf8mb4 -e 'CREATE DATABASE IF NOT EXISTS \``$DockerDb\`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'" 2>$null
Get-Content $LocalExport -Raw |
    docker compose exec -T $DbService sh -c "exec mysql -uroot -p`"`$MYSQL_ROOT_PASSWORD`" --default-character-set=utf8mb4 $DockerDb" 2>$null
Write-Host "    Import selesai."

Write-Host "==> [6/6] Bersihkan cache Laravel + verifikasi data..."
docker compose exec -T $AppService php artisan optimize:clear
docker compose exec -T $DbService sh -c "exec mysql -uroot -p`"`$MYSQL_ROOT_PASSWORD`" $DockerDb -e 'SHOW TABLES; SELECT COUNT(*) AS pesanan FROM pesanan; SELECT COUNT(*) AS menu FROM menu; SELECT COUNT(*) AS users FROM users; SELECT COUNT(*) AS meja FROM meja;'" 2>$null

Write-Host ""
Write-Host "SELESAI. Rollback bila perlu:"
Write-Host "  Get-Content $DockerBackup -Raw | docker compose exec -T $DbService sh -c 'exec mysql -uroot -p`"`$MYSQL_ROOT_PASSWORD`" $DockerDb'"
