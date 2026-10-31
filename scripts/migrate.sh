#!/usr/bin/env bash
# Áp các file db/migrations/*.sql CHƯA chạy lên DB. Chạy bao nhiêu lần cũng an toàn:
# file nào đã áp thì được ghi vào bảng schema_migrations và bị bỏ qua.
#
# Máy dev (Docker đang chạy):  ./scripts/migrate.sh
# VPS: deploy.sh tự gọi sau mỗi lần deploy, mày không cần chạy tay.
set -euo pipefail
cd "$(dirname "$0")/.."

COMPOSE=${COMPOSE:-docker compose}

# Chạy SQL trong container db. Truyền thêm tham số cho mysql, vd: sql -N -e "select 1"; hoặc sql < file.sql
sql() {
  $COMPOSE exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql --default-character-set=utf8mb4 -uroot shop "$@"' sh "$@"
}

# Chờ DB sẵn sàng (vừa khởi động xong có thể chưa nhận kết nối)
for i in $(seq 1 30); do
  sql -N -e "select 1" >/dev/null 2>&1 && break
  [ "$i" = 30 ] && { echo "DB chưa sẵn sàng sau 30 lần thử. Docker đang chạy chưa? (docker compose up -d)"; exit 1; }
  sleep 2
done

sql -e "CREATE TABLE IF NOT EXISTS schema_migrations (
  name VARCHAR(255) PRIMARY KEY,
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)"

# 001 đã được đưa sẵn vào init.sql (DB mới) hoặc chạy tay trước đây (VPS): nếu DB đã có cột slug thì chỉ ghi nhận, không chạy lại
HAS_SLUG=$(sql -N -e "select count(*) from information_schema.columns where table_schema='shop' and table_name='products' and column_name='slug'" | tr -d '\r')
[ "$HAS_SLUG" = "1" ] && sql -e "INSERT IGNORE INTO schema_migrations (name) VALUES ('001-laptop-schema.sql')"

applied=0
for f in db/migrations/*.sql; do
  [ -e "$f" ] || continue
  name=$(basename "$f")
  done_already=$(sql -N -e "select count(*) from schema_migrations where name='$name'" | tr -d '\r')
  if [ "$done_already" = "0" ]; then
    echo "==> Áp dụng $name"
    sql < "$f"
    sql -e "INSERT INTO schema_migrations (name) VALUES ('$name')"
    applied=$((applied + 1))
  fi
done
echo "Xong: áp dụng $applied migration mới."
