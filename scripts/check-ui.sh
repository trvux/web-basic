#!/usr/bin/env bash
# CI: bắt buộc dùng bộ giao diện shadcn trong src/includes/ui.php.
# Cấm màu Tailwind viết cứng kiểu bg-red-600, text-gray-800, border-gray-300 trong trang PHP
# (dùng token của theme: bg-primary, text-muted-foreground, border-border... hoặc ui_btn(), UI_INPUT, UI_CARD...).
cd "$(dirname "$0")/.."

PATTERN='\b(bg|text|border|ring|from|via|to|divide|fill|stroke|outline|shadow|placeholder|accent|caret|decoration)-(red|gray|grey|blue|green|slate|zinc|neutral|stone|yellow|orange|amber|lime|emerald|teal|cyan|sky|indigo|violet|purple|fuchsia|pink|rose)-[0-9]{2,3}\b'

hits=$(grep -rnE "$PATTERN" src --include='*.php' --exclude-dir=includes || true)
if [ -n "$hits" ]; then
  echo "$hits" | while IFS= read -r l; do
    f=${l%%:*}; rest=${l#*:}; n=${rest%%:*}
    echo "::error file=$f,line=$n::Không dùng màu Tailwind viết cứng. Dùng ui_btn()/UI_INPUT/UI_CARD... hoặc token theme (bg-primary, text-muted-foreground). Xem README mục 'Giao diện'."
  done
  exit 1
fi
echo "OK: không có màu viết cứng."
