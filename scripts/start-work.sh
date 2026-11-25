#!/usr/bin/env bash
# Bắt đầu một việc mới: luôn kéo main MỚI NHẤT rồi mới tách nhánh, để không làm tiếp trên code cũ.
# Dùng:  ./scripts/start-work.sh feature/ten-viec
set -euo pipefail
cd "$(dirname "$0")/.."

[ $# -eq 1 ] || { echo "Dùng: ./scripts/start-work.sh feature/ten-viec"; exit 1; }
BRANCH=$1

if [ -n "$(git status --porcelain)" ]; then
  echo "Còn thay đổi chưa commit. Commit hoặc 'git stash' trước rồi chạy lại."
  exit 1
fi

git fetch origin
git checkout main
git pull --ff-only origin main
./scripts/migrate.sh || echo "(Bỏ qua migrate: Docker chưa chạy? Chạy 'docker compose up -d' rồi ./scripts/migrate.sh)"
git checkout -b "$BRANCH"
echo "Xong. Đang ở nhánh $BRANCH, code mới nhất của main."
