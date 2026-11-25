#!/usr/bin/env bash
# Cập nhật nhánh đang làm với main mới nhất. Chạy MỖI KHI: trước khi mở PR, sau khi PR của người khác vừa được merge,
# hoặc khi GitHub báo "branch is out-of-date".
set -euo pipefail
cd "$(dirname "$0")/.."

BRANCH=$(git rev-parse --abbrev-ref HEAD)
[ "$BRANCH" != "main" ] || { echo "Đang ở main. Dùng ./scripts/start-work.sh để tách nhánh mới."; exit 1; }
[ -z "$(git status --porcelain)" ] || { echo "Còn thay đổi chưa commit. Commit trước rồi chạy lại."; exit 1; }

git fetch origin
git merge origin/main || { echo; echo "Có CONFLICT. Mở file bị báo, giữ code đúng, xoá dấu <<<<<<< ======= >>>>>>>, rồi: git add <file> && git commit. Không biết gỡ thì nhắn leader."; exit 1; }
./scripts/migrate.sh || true
echo "Nhánh $BRANCH đã cập nhật theo main. Giờ git push."
