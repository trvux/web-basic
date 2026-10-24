#!/usr/bin/env bash
# Thêm (hoặc đổi mật khẩu/role) một tài khoản web trên VPS.
# Dùng:  ./scripts/add-user.sh <username> <password> [user|admin] [họ tên]
# Ví dụ: ./scripts/add-user.sh sv001 matkhauchung admin "Nguyễn Văn A"
# Nếu username đã có thì cập nhật mật khẩu + role. Dữ liệu chỉ nằm trong DB trên VPS, không vào git.
set -euo pipefail

USERNAME=${1:?thiếu username}
PASSWORD=${2:?thiếu password}
ROLE=${3:-user}
FULLNAME=${4:-}

[[ "$USERNAME" =~ ^[A-Za-z0-9._-]{1,50}$ ]] || { echo "username chỉ gồm chữ, số, . _ -"; exit 1; }
[[ "$FULLNAME" != *"'"* && "$FULLNAME" != *'\'* && ${#FULLNAME} -le 100 ]] || { echo "họ tên không được chứa ' hoặc \\ và tối đa 100 ký tự"; exit 1; }
[[ "$ROLE" == "user" || "$ROLE" == "admin" ]] || { echo "role phải là user hoặc admin"; exit 1; }

# ssh nối các đối số thành 1 chuỗi rồi shell bên VPS tách lại theo dấu cách,
# nên phải quote từng đối số (%q) để "Bảo Huy" không bị tách thành 2 từ.
ARGS=$(printf '%q ' "$USERNAME" "$PASSWORD" "$ROLE" "$FULLNAME")
ssh phren-vps "bash -s -- $ARGS" <<'REMOTE'
set -euo pipefail
U=$1; P=$2; R=$3; N=$4
cd /opt/web-phren
C="docker compose -f docker-compose.prod.yml"
# băm mật khẩu bằng chính PHP của web (password_hash), để login.php verify được
# </dev/null: không cho docker nuốt stdin, vì chính stdin này đang chứa phần script phía sau
HASH=$($C exec -T -e PW="$P" web php -r 'echo password_hash(getenv("PW"), PASSWORD_DEFAULT);' </dev/null)
# --default-character-set=utf8mb4: bắt buộc, thiếu là tiếng Việt bị lỗi font trong DB
printf "INSERT INTO users (username, full_name, password, role) VALUES ('%s', NULLIF('%s',''), '%s', '%s')
  ON DUPLICATE KEY UPDATE full_name = COALESCE(VALUES(full_name), full_name), password = VALUES(password), role = VALUES(role);" "$U" "$N" "$HASH" "$R" \
  | $C exec -T db sh -c 'mysql --default-character-set=utf8mb4 -uroot -p"$MYSQL_ROOT_PASSWORD" shop 2>/dev/null'
echo "OK: $U ($R)"
REMOTE
