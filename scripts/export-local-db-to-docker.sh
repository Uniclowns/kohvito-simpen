#!/usr/bin/env bash
# ============================================================================
# export-local-db-to-docker.sh
# Migrasi database SIMPEN Kohvito: MySQL Laragon (host) -> MySQL Docker.
# Aman: SELALU backup database Docker dulu, tidak pernah drop otomatis.
#
# Jalankan dari WSL/Linux/macOS, dari root proyek:
#   bash scripts/export-local-db-to-docker.sh
# ============================================================================
set -euo pipefail

cd "$(dirname "$0")/.."
BACKUP_DIR="backup-database"
TS="$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

# --- Baca konfigurasi lokal dari .env --------------------------------------
env_get() { grep -E "^$1=" .env | head -1 | cut -d= -f2- | tr -d '"\r'; }
LOCAL_DB="$(env_get DB_DATABASE)";  LOCAL_DB="${LOCAL_DB:-kohvito-simpen}"
LOCAL_USER="$(env_get DB_USERNAME)"; LOCAL_USER="${LOCAL_USER:-root}"
LOCAL_PASS="$(env_get DB_PASSWORD)"
LOCAL_HOST="127.0.0.1"   # selalu TCP host, jangan socket
LOCAL_PORT="3306"        # MySQL Laragon

# --- Konfigurasi Docker (dari compose.yaml) ---------------------------------
DB_SERVICE="db"          # nama service MySQL di compose.yaml
APP_SERVICE="app"        # nama service Laravel
DOCKER_DB="kohvito-simpen"
# Password root TIDAK ditulis di sini: dibaca dari env container ($MYSQL_ROOT_PASSWORD).

# --- mysqldump lokal: pakai milik Laragon bila `mysqldump` tak ada di PATH --
MYSQLDUMP="mysqldump"
if ! command -v mysqldump >/dev/null 2>&1; then
    for d in /mnt/d/laragon/bin/mysql/mysql-*/bin /mnt/c/laragon/bin/mysql/mysql-*/bin /mnt/c/xampp/mysql/bin; do
        [ -x "$d/mysqldump.exe" ] && MYSQLDUMP="$d/mysqldump.exe" && break
    done
fi
echo "==> mysqldump: $MYSQLDUMP"

PASS_ARG=()
[ -n "$LOCAL_PASS" ] && PASS_ARG=("-p$LOCAL_PASS")

echo "==> [1/6] Cek container Docker..."
docker compose ps --format 'table {{.Service}}\t{{.Status}}' | sed 's/^/    /'
docker compose ps "$DB_SERVICE" | grep -qi "healthy\|Up" || { echo "!! Service $DB_SERVICE tidak jalan. docker compose up -d dulu."; exit 1; }

echo "==> [2/6] Backup database Docker (sebelum import) ..."
DOCKER_BACKUP="$BACKUP_DIR/docker-before-import-$TS.sql"
docker compose exec -T "$DB_SERVICE" sh -c \
  'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --single-transaction --quick --routines --triggers --events --default-character-set=utf8mb4 --set-gtid-purged=OFF '"$DOCKER_DB" \
  > "$DOCKER_BACKUP" 2>/dev/null || { echo "!! Backup Docker gagal"; exit 1; }
grep -q "Dump completed" "$DOCKER_BACKUP" || echo "   (peringatan: penanda 'Dump completed' tidak ditemukan — cek file)"
echo "    -> $DOCKER_BACKUP ($(du -h "$DOCKER_BACKUP" | cut -f1))"

echo "==> [3/6] Export database lokal Laragon ($LOCAL_HOST:$LOCAL_PORT/$LOCAL_DB) ..."
LOCAL_EXPORT="$BACKUP_DIR/local-export-$TS.sql"
"$MYSQLDUMP" -h "$LOCAL_HOST" -P "$LOCAL_PORT" -u "$LOCAL_USER" "${PASS_ARG[@]}" \
  --single-transaction --quick --routines --triggers --events \
  --default-character-set=utf8mb4 --set-gtid-purged=OFF \
  "$LOCAL_DB" > "$LOCAL_EXPORT"
grep -q "Dump completed" "$LOCAL_EXPORT" || { echo "!! Export lokal tampak tidak lengkap"; exit 1; }
echo "    -> $LOCAL_EXPORT ($(du -h "$LOCAL_EXPORT" | cut -f1))"

echo "==> [4/6] Bersihkan klausa DEFINER (kompatibilitas user antar server) ..."
# Hapus DEFINER=`user`@`host` dari routines/triggers/views agar tidak error
# "user doesn't exist" di server tujuan.
sed -i.bak -E 's/DEFINER=`[^`]+`@`[^`]+`//g; s/\/\*!50017 \*\///g' "$LOCAL_EXPORT" && rm -f "$LOCAL_EXPORT.bak"

echo "==> [5/6] Import ke MySQL Docker (database: $DOCKER_DB) ..."
# CREATE DATABASE dulu bila belum ada (aman/idempoten), lalu import sebagai root.
docker compose exec -T "$DB_SERVICE" sh -c \
  'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" --default-character-set=utf8mb4 -e "CREATE DATABASE IF NOT EXISTS \`'"$DOCKER_DB"'\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"' 2>/dev/null
docker compose exec -T "$DB_SERVICE" sh -c \
  'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" --default-character-set=utf8mb4 '"$DOCKER_DB" \
  < "$LOCAL_EXPORT" 2>/dev/null
echo "    Import selesai."

echo "==> [6/6] Bersihkan cache Laravel + verifikasi data ..."
docker compose exec -T "$APP_SERVICE" php artisan optimize:clear 2>/dev/null | sed 's/^/    /' || true
docker compose exec -T "$DB_SERVICE" sh -c \
  'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" '"$DOCKER_DB"' -e "SHOW TABLES; SELECT COUNT(*) AS pesanan FROM pesanan; SELECT COUNT(*) AS menu FROM menu; SELECT COUNT(*) AS users FROM users; SELECT COUNT(*) AS meja FROM meja;"' 2>/dev/null | sed 's/^/    /'

echo ""
echo "SELESAI. Rollback bila perlu:"
echo "  docker compose exec -T $DB_SERVICE sh -c 'exec mysql -uroot -p\"\$MYSQL_ROOT_PASSWORD\" $DOCKER_DB' < $DOCKER_BACKUP"
