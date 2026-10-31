#!/usr/bin/env bash
# Đưa project lên VPS và chạy. Dùng: ./deploy.sh
# Yêu cầu: VPS đã cài Docker, và SSH được bằng alias "phren-vps" (trong ~/.ssh/config).
set -euo pipefail

HOST=phren-vps
DIR=/opt/web-phren

echo "==> Đồng bộ code lên $HOST:$DIR"
ssh "$HOST" "mkdir -p $DIR"
rsync -az --delete \
  --exclude '.git' --exclude '.env' --exclude '.DS_Store' --exclude '.idea' \
  --exclude 'data' --exclude 'src/uploads/products' \
  ./ "$HOST:$DIR/"
# Ảnh sản phẩm không nằm trong git (repo public) nên được copy tay lên VPS một lần:
#   rsync -az src/uploads/products/ phren-vps:/opt/web-phren/src/uploads/products/
# Dòng --exclude ở trên giữ cho --delete không xóa chúng mỗi lần deploy.

echo "==> Tạo .env (mật khẩu DB ngẫu nhiên) nếu chưa có"
ssh "$HOST" "cd $DIR && [ -f .env ] || printf 'DB_ROOT_PASS=%s\nDB_PASS=%s\n' \"\$(openssl rand -hex 16)\" \"\$(openssl rand -hex 16)\" > .env && chmod 600 .env"

echo "==> Build và chạy"
ssh "$HOST" "cd $DIR && docker compose -f docker-compose.prod.yml up -d --build"

echo "==> Áp migration DB (file mới trong db/migrations/, nếu có)"
ssh "$HOST" "cd $DIR && COMPOSE='docker compose -f docker-compose.prod.yml' bash scripts/migrate.sh"

echo "==> Xong. Trạng thái:"
ssh "$HOST" "cd $DIR && docker compose -f docker-compose.prod.yml ps"
