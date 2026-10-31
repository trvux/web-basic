#!/usr/bin/env python3
"""Tải ảnh chính của từng sản phẩm trong data/gearvn_laptops.json về src/uploads/products/.

Chạy:  python3 scripts/download_images.py
Sau khi chạy, mỗi sản phẩm trong JSON có thêm trường "local_image" (vd "/uploads/products/abc.jpg")
để lưu thẳng vào cột products.image. Ảnh đã tải thì bỏ qua, chạy lại không tải lại.
"""
import json
import sys
import time
import urllib.request
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DATA = ROOT / "data" / "gearvn_laptops.json"
DEST = ROOT / "src" / "uploads" / "products"
UA = "gearvn-student-crawler/1.0 (university project)"
DELAY = 0.5


def main():
    products = json.loads(DATA.read_text(encoding="utf-8"))
    DEST.mkdir(parents=True, exist_ok=True)
    ok = 0
    for i, p in enumerate(products, 1):
        url = p["images"][0]
        ext = url.split("?")[0].rsplit(".", 1)[-1].lower()
        if ext not in ("jpg", "jpeg", "png", "webp"):
            ext = "jpg"
        f = DEST / f"{p['slug'][:120]}.{ext}"
        if not f.exists():
            time.sleep(DELAY)
            try:
                req = urllib.request.Request(url, headers={"User-Agent": UA})
                with urllib.request.urlopen(req, timeout=30) as r:
                    f.write_bytes(r.read())
            except Exception as e:
                print(f"[{i}] LỖI {url}: {e}", file=sys.stderr)
                p["local_image"] = None
                continue
        p["local_image"] = f"/uploads/products/{f.name}"
        ok += 1
    DATA.write_text(json.dumps(products, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Xong: {ok}/{len(products)} ảnh trong {DEST}")


if __name__ == "__main__":
    main()
