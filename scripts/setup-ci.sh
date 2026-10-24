#!/usr/bin/env bash
# Chạy MỘT LẦN để bật CI/CD: GitHub Actions sẽ SSH vào VPS để deploy.
# Script này:
#   1. Tạo 1 key SSH riêng chỉ dành cho CI (không dùng chung với key cá nhân của mày)
#   2. Thêm public key đó vào ~/.ssh/authorized_keys của root trên VPS  <-- CẤP QUYỀN SSH, đọc kỹ
#   3. Lưu private key + host key vào GitHub Secrets của repo này (VPS_SSH_KEY, VPS_HOST, VPS_KNOWN_HOSTS)
#   4. Xoá private key khỏi máy (nó chỉ còn nằm trong GitHub Secrets)
# Muốn thu hồi sau này: xoá dòng có comment "github-actions@web-basic" trong authorized_keys trên VPS.
set -euo pipefail

VPS=phren-vps
REPO=trvux/web-basic
TMP=$(mktemp -d)
trap 'rm -rf "$TMP"' EXIT

ssh-keygen -t ed25519 -N "" -C "github-actions@web-basic" -f "$TMP/key" -q
echo "==> Đã tạo key CI"

# Nối public key vào cuối authorized_keys (đảm bảo file đã xuống dòng trước đó)
ssh "$VPS" 'f=~/.ssh/authorized_keys; [ -n "$(tail -c1 "$f")" ] && echo >> "$f"; cat >> "$f"' < "$TMP/key.pub"

HOST_IP=$(ssh -G "$VPS" | awk '/^hostname /{print $2}')
ssh -i "$TMP/key" -o IdentitiesOnly=yes -o BatchMode=yes -o StrictHostKeyChecking=accept-new \
    -o UserKnownHostsFile="$TMP/kh" "root@$HOST_IP" 'echo "==> Key CI đăng nhập VPS OK ($(hostname))"'

gh secret set VPS_SSH_KEY -R "$REPO" < "$TMP/key"
gh secret set VPS_HOST -R "$REPO" --body "$HOST_IP"
ssh-keyscan -t ed25519 "$HOST_IP" 2>/dev/null | gh secret set VPS_KNOWN_HOSTS -R "$REPO"
echo "==> Đã lưu 3 secret vào $REPO. Giờ push vào main là tự deploy."
