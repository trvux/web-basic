#!/usr/bin/env bash
# CI: mỗi bạn chỉ được sửa thư mục của mình, để 2 người không bao giờ sửa chung 1 file.
# Dùng trong GitHub Actions: AUTHOR=<login> BASE=origin/main ./scripts/check-ownership.sh
# Muốn đổi phân công: sửa bảng owned_paths() bên dưới (chỉ leader sửa).
set -euo pipefail

AUTHOR=${AUTHOR:?thiếu AUTHOR}
BASE=${BASE:-origin/main}

# Thư mục/file mỗi người được sửa. Tiền tố kết thúc bằng / là cả thư mục.
owned_paths() {
  case "$1" in
    trvux) echo "ALL" ;;   # leader
    nguyenminh-15th9na25) echo "src/products/ src/posts/" ;;
    ikinggamergod1-glitch) echo "src/admin/users/ src/admin/orders/ src/admin/index.php" ;;
    mphuctr) echo "src/admin/banners/ src/admin/posts/ src/account/ src/orders/ src/cart/" ;;
    *) echo "ALL" ;;      # bot (CodeRabbit...) hoặc người lạ: không chặn ở đây, vẫn cần leader duyệt
  esac
}
# Ai cũng được thêm file migration mới (tên có ngày + tên việc nên không trùng)
COMMON="db/migrations/"

OWNED=$(owned_paths "$AUTHOR")
[ "$OWNED" = "ALL" ] && { echo "OK: $AUTHOR được sửa mọi nơi."; exit 0; }

bad=0
while IFS= read -r f; do
  ok=0
  for p in $OWNED $COMMON; do
    case "$p" in
      */) [[ "$f" == "$p"* ]] && ok=1 ;;
      *)  [ "$f" = "$p" ] && ok=1 ;;
    esac
  done
  if [ $ok -eq 0 ]; then
    echo "::error file=$f::$AUTHOR không được sửa file này (không thuộc phần của mình). Nhắn leader nếu thật sự cần."
    bad=1
  fi
done < <(git diff --name-only "$BASE"...HEAD)

[ $bad -eq 0 ] && echo "OK: chỉ sửa trong phần của $AUTHOR."
exit $bad
