#!/usr/bin/env bash
# Nạp 100 laptop (DB + ảnh) lên VPS, GIỮ NGUYÊN tài khoản và đơn hàng đang có.
# Chạy trên máy dev:  ./scripts/vps-load-laptops.sh
# Cần có sẵn gói dữ liệu đã giải nén: db/seed-products.sql và src/uploads/products/
# Chạy lại nhiều lần được (nạp lại sạch 100 sản phẩm, không nhân đôi).
set -euo pipefail
cd "$(dirname "$0")/.."

HOST=phren-vps
DIR=/opt/web-phren
BACKUP_DIR=/root/backups   # để NGOÀI $DIR, vì deploy.sh dùng rsync --delete

[ -f db/seed-products.sql ] || { echo "Thiếu db/seed-products.sql (giải nén gói dữ liệu vào thư mục project trước)"; exit 1; }
[ -d src/uploads/products ] || { echo "Thiếu src/uploads/products/ (giải nén gói dữ liệu trước)"; exit 1; }

# Chạy SQL từ stdin vào DB trên VPS (utf8mb4 là bắt buộc, thiếu là tiếng Việt lỗi font)
vps_sql() {
  ssh "$HOST" "cd $DIR && docker compose -f docker-compose.prod.yml exec -T db sh -c 'MYSQL_PWD=\"\$MYSQL_ROOT_PASSWORD\" mysql --default-character-set=utf8mb4 -uroot shop'"
}

echo "==> 1/6 Backup DB trên VPS -> $BACKUP_DIR"
ssh "$HOST" "set -e; mkdir -p $BACKUP_DIR; chmod 700 $BACKUP_DIR; F=$BACKUP_DIR/shop-\$(date +%F-%H%M%S)-truoc-khi-nap-laptop.sql
  cd $DIR && docker compose -f docker-compose.prod.yml exec -T db sh -c 'MYSQL_PWD=\"\$MYSQL_ROOT_PASSWORD\" mysqldump --default-character-set=utf8mb4 --single-transaction --routines -uroot shop' > \$F
  chmod 600 \$F; echo \"   \$F (\$(wc -c < \$F) bytes)\""

echo "==> 2/6 Nâng cấp cấu trúc bảng (bỏ qua nếu đã nâng cấp)"
HAS=$(echo "select count(*) from information_schema.columns where table_schema='shop' and table_name='products' and column_name='slug';" | vps_sql | tail -1)
if [ "$HAS" = "0" ]; then vps_sql < db/migrations/001-laptop-schema.sql; echo "   đã thêm cột/bảng mới"; else echo "   đã có sẵn, bỏ qua"; fi

echo "==> 3/6 Nạp 100 sản phẩm (1 transaction: lỗi giữa chừng thì tự rollback)"
{
  echo "SET NAMES utf8mb4; START TRANSACTION;"
  echo "DELETE FROM products WHERE image LIKE 'https://placehold.co/%' OR source_url LIKE 'https://gearvn.com/%';"
  echo "DELETE FROM categories WHERE name = 'Phụ kiện';"
  cat db/seed-products.sql
  echo "COMMIT;"
} | vps_sql
echo "   xong"

echo "==> 4/6 Copy ảnh lên VPS"
ssh "$HOST" "mkdir -p $DIR/src/uploads/products"
rsync -az --delete src/uploads/products/ "$HOST:$DIR/src/uploads/products/"

echo "==> 5/6 Build lại container web (ảnh nằm trong image)"
ssh "$HOST" "cd $DIR && docker compose -f docker-compose.prod.yml up -d --build web" 2>&1 | tail -3

echo "==> 6/6 Kiểm tra"
echo "select (select count(*) from products) products, (select count(*) from product_specs) specs, (select count(*) from products where image like '%placehold%') placeholder, (select count(*) from users) users, (select count(*) from orders) orders;" | vps_sql
sleep 3
curl -s -o /dev/null -w 'web.phren.dev -> %{http_code}\n' https://web.phren.dev/index.php
