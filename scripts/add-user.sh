#!/usr/bin/env bash
# Thêm (hoặc đổi mật khẩu/role) một tài khoản web trên VPS.
# Dùng:  ./scripts/add-user.sh <username> <password> [user|admin]
# Ví dụ: ./scripts/add-user.sh sv001 matkhauchung admin
# Nếu username đã có thì cập nhật mật khẩu + role. Dữ liệu chỉ nằm trong DB trên VPS, không vào git.
set -euo pipefail

USERNAME=${1:?thiếu username}
PASSWORD=${2:?thiếu password}
ROLE=${3:-user}

[[ "$USERNAME" =~ ^[A-Za-z0-9._-]{1,50}$ ]] || { echo "username chỉ gồm chữ, số, . _ -"; exit 1; }
[[ "$ROLE" == "user" || "$ROLE" == "admin" ]] || { echo "role phải là user hoặc admin"; exit 1; }

ssh phren-vps bash -s -- "$USERNAME" "$PASSWORD" "$ROLE" <<'REMOTE'
set -euo pipefail
U=$1; P=$2; R=$3
cd /opt/web-phren
C="docker compose -f docker-compose.prod.yml"
# băm mật khẩu bằng chính PHP của web (password_hash), để login.php verify được
# </dev/null: không cho docker nuốt stdin, vì chính stdin này đang chứa phần script phía sau
HASH=$($C exec -T -e PW="$P" web php -r 'echo password_hash(getenv("PW"), PASSWORD_DEFAULT);' </dev/null)
printf "INSERT INTO users (username, password, role) VALUES ('%s', '%s', '%s')
  ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role);" "$U" "$HASH" "$R" \
  | $C exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" shop 2>/dev/null'
echo "OK: $U ($R)"
REMOTE
