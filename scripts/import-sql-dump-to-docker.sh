#!/usr/bin/env bash
# Import file dump SQL (mis. hasil ekspor HeidiSQL) ke MySQL Docker kohvito-simpen.
#
# Pemakaian:
#   ./scripts/import-sql-dump-to-docker.sh /path/ke/dump.sql
#
# Catatan:
# - Import dilakukan sebagai root (root_dev) karena dump berisi
#   CREATE DATABASE / USE yang tidak bisa dijalankan user `kohvito`.
# - Dump HeidiSQL berisi CREATE TABLE IF NOT EXISTS + data. Jika tabel
#   sudah ada berisi data lama, jalankan dengan --fresh untuk drop
#   database dulu (DESTRUKTIF terhadap data di container, bukan Laragon).

set -euo pipefail

COMPOSE_FILE="$(cd "$(dirname "$0")/.." && pwd)/compose.yaml"
DB_NAME="kohvito-simpen"
ROOT_PW="root_dev"

FRESH=0
DUMP=""
for arg in "$@"; do
  case "$arg" in
    --fresh) FRESH=1 ;;
    *) DUMP="$arg" ;;
  esac
done

if [[ -z "$DUMP" || ! -f "$DUMP" ]]; then
  echo "ERROR: file dump tidak ditemukan: '$DUMP'" >&2
  echo "Pemakaian: $0 [--fresh] /path/ke/dump.sql" >&2
  exit 1
fi

echo "==> Menyalakan service db..."
docker compose -f "$COMPOSE_FILE" up -d db

echo "==> Menunggu MySQL sehat..."
for i in $(seq 1 60); do
  status=$(docker compose -f "$COMPOSE_FILE" ps --format '{{.Health}}' db 2>/dev/null || true)
  [[ "$status" == "healthy" ]] && break
  sleep 2
done
if [[ "${status:-}" != "healthy" ]]; then
  echo "ERROR: MySQL tidak sehat setelah 120 detik. Cek: docker compose logs db" >&2
  exit 1
fi

if [[ $FRESH -eq 1 ]]; then
  echo "==> --fresh: DROP DATABASE \`$DB_NAME\` di container (data Docker lama hilang)..."
  docker compose -f "$COMPOSE_FILE" exec -T db \
    mysql -uroot -p"$ROOT_PW" -e "DROP DATABASE IF EXISTS \`$DB_NAME\`;"
fi

echo "==> Mengimpor $DUMP ..."
docker compose -f "$COMPOSE_FILE" exec -T db \
  mysql -uroot -p"$ROOT_PW" < "$DUMP"

echo "==> Memastikan user kohvito punya akses ke \`$DB_NAME\`..."
docker compose -f "$COMPOSE_FILE" exec -T db \
  mysql -uroot -p"$ROOT_PW" -e "GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO 'kohvito'@'%'; FLUSH PRIVILEGES;"

echo "==> Verifikasi tabel:"
docker compose -f "$COMPOSE_FILE" exec -T db \
  mysql -uroot -p"$ROOT_PW" -e "SHOW TABLES FROM \`$DB_NAME\`;"

echo "==> Selesai. Jalankan seluruh stack dengan:"
echo "    docker compose -f $COMPOSE_FILE up -d"
