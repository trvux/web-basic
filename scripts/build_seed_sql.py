#!/usr/bin/env python3
"""Sinh db/seed-products.sql từ data/laptops_clean.json.

Chạy:  python3 scripts/build_seed_sql.py
Sau đó MySQL tự nạp file này khi khởi tạo DB lần đầu (xem docker-compose.yml).
Đừng sửa tay db/seed-products.sql: sửa data rồi chạy lại script này.
"""
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "data" / "laptops_clean.json"
OUT = ROOT / "db" / "seed-products.sql"


def q(v):
    """Giá trị Python -> literal SQL."""
    if v is None:
        return "NULL"
    if isinstance(v, bool):
        return "1" if v else "0"
    if isinstance(v, (int, float)):
        return repr(v)
    s = str(v).replace("\\", "\\\\").replace("'", "''").replace("\0", "")
    return f"'{s}'"


def summary(a):
    """Mô tả ngắn cho thẻ sản phẩm, vd: 'Core 5, 16GB RAM, 512GB SSD, RTX 4050, 15.6"'."""
    parts = []
    if a["cpu_line"]:
        parts.append(a["cpu_line"])
    if a["ram_gb"]:
        parts.append(f"{a['ram_gb']}GB RAM")
    if a["ssd_gb"]:
        parts.append(f"{a['ssd_gb'] // 1024}TB SSD" if a["ssd_gb"] >= 1024 else f"{a['ssd_gb']}GB SSD")
    if a["gpu"]:
        parts.append(a["gpu"].split("(")[0].strip())
    if a["screen_inch"]:
        parts.append(f"{a['screen_inch']:g}\"")
    return ", ".join(parts)


COLS = ["name", "price", "stock", "description", "image", "category_id", "slug", "sku", "brand",
        "old_price", "article", "source_url", "cpu", "cpu_brand", "cpu_line", "ram_gb", "ram_type",
        "ssd_gb", "gpu", "gpu_brand", "vram_gb", "screen_inch", "refresh_hz", "resolution", "os",
        "weight_kg", "battery_wh", "warranty_months", "use_case"]


def main():
    data = json.loads(SRC.read_text(encoding="utf-8"))
    out = [
        "-- FILE TỰ SINH bởi scripts/build_seed_sql.py - đừng sửa tay.",
        f"-- {len(data)} laptop lấy từ gearvn.com (xem cột source_url). Chạy sau init.sql.",
        "SET NAMES utf8mb4;",
        "",
    ]
    for p in data:
        a = p["attrs"]
        vals = {
            "name": p["name"], "price": p["price"], "stock": p["stock"], "description": summary(a),
            "image": p["image"],
            "category_id": f"(SELECT id FROM categories WHERE name = {q(p['category'])})",
            "slug": p["slug"], "sku": p["sku"], "brand": a["brand"], "old_price": p["old_price"],
            "article": p["article_html"], "source_url": p["source_url"],
            "cpu": a["cpu"], "cpu_brand": a["cpu_brand"], "cpu_line": a["cpu_line"],
            "ram_gb": a["ram_gb"], "ram_type": a["ram_type"], "ssd_gb": a["ssd_gb"],
            "gpu": a["gpu"], "gpu_brand": a["gpu_brand"], "vram_gb": a["vram_gb"],
            "screen_inch": a["screen_inch"], "refresh_hz": a["refresh_hz"],
            "resolution": a["resolution"], "os": a["os"], "weight_kg": a["weight_kg"],
            "battery_wh": a["battery_wh"], "warranty_months": a["warranty_months"],
            "use_case": ", ".join(a["usage"]) or None,
        }
        sql_vals = [vals[c] if c == "category_id" else q(vals[c]) for c in COLS]
        out.append(f"-- {p['name']}")
        out.append(f"INSERT INTO products ({', '.join(COLS)}) VALUES\n({', '.join(sql_vals)});")
        out.append("SET @pid = LAST_INSERT_ID();")
        rows = [f"(@pid, {q(s['group'])}, {q(s['label'])}, {q(s['value'])}, {i})" for i, s in enumerate(p["specs"])]
        out.append("INSERT INTO product_specs (product_id, group_name, label, value, sort_order) VALUES\n"
                   + ",\n".join(rows) + ";")
        out.append("")
    OUT.write_text("\n".join(out), encoding="utf-8")
    print(f"Đã ghi {OUT} ({OUT.stat().st_size / 1e6:.2f} MB, {len(data)} sản phẩm)")


if __name__ == "__main__":
    main()
