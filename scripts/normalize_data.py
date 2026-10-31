#!/usr/bin/env python3
"""Chuẩn hóa data/gearvn_laptops.json -> data/laptops_clean.json (chưa đụng vào DB).

Chạy:  python3 scripts/normalize_data.py

Mỗi sản phẩm sau khi chuẩn hóa có:
  attrs  - thuộc tính dạng số/chuỗi gọn, dùng để LỌC & SẮP XẾP (ram_gb, ssd_gb, screen_inch...)
  specs  - danh sách {group, label, value} với nhãn thống nhất, dùng để HIỂN THỊ bảng thông số
  article_html - bài viết đã đổi thành HTML sạch (h2/h3/p/ul)
"""
import json
import re
from html import escape
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "data" / "gearvn_laptops.json"
OUT = ROOT / "data" / "laptops_clean.json"

BRANDS = {"ACER": "Acer", "ASUS": "ASUS", "MSI": "MSI", "LENOVO": "Lenovo",
          "GIGABYTE": "Gigabyte", "DELL": "Dell", "HP": "HP", "APPLE": "Apple"}

# Thứ tự hiển thị các nhóm trong bảng thông số
GROUPS = ["Tổng quan", "Bộ xử lý", "Bộ nhớ RAM", "Lưu trữ", "Đồ họa", "Màn hình",
          "Kết nối & cổng", "Pin", "Thiết kế"]

# Nhãn gốc -> (nhóm, nhãn chuẩn). Những nhãn cần GHÉP/BIẾN ĐỔI xử lý riêng trong build_specs().
SIMPLE = {
    "Dòng sản phẩm": ("Tổng quan", "Dòng sản phẩm"),
    "Màu sắc": ("Tổng quan", "Màu sắc"),
    "Phụ kiện đi kèm": ("Tổng quan", "Phụ kiện đi kèm"),
    "Tính năng đặc biệt": ("Tổng quan", "Tính năng đặc biệt"),
    "CPU": ("Bộ xử lý", "CPU"),
    "Dòng CPU": ("Bộ xử lý", "Dòng CPU"),
    "Hãng CPU": ("Bộ xử lý", "Hãng CPU"),
    "Thế hệ CPU": ("Bộ xử lý", "Thế hệ CPU"),
    "Loại RAM": ("Bộ nhớ RAM", "Loại RAM"),
    "Bus RAM": ("Bộ nhớ RAM", "Bus RAM"),
    "Số khe RAM": ("Bộ nhớ RAM", "Số khe RAM"),
    "RAM mỗi thanh": ("Bộ nhớ RAM", "RAM mỗi thanh"),
    "Số thanh RAM": ("Bộ nhớ RAM", "Số thanh RAM"),
    "RAM nâng cấp tối đa": ("Bộ nhớ RAM", "RAM nâng cấp tối đa"),
    "RAM onboard": ("Bộ nhớ RAM", "RAM onboard"),
    "Form factor SSD": ("Lưu trữ", "Form factor SSD"),
    "Số khe SSD": ("Lưu trữ", "Số khe SSD"),
    "SSD nâng cấp tối đa": ("Lưu trữ", "SSD nâng cấp tối đa"),
    "Card đồ họa": ("Đồ họa", "Card đồ họa"),
    "Hãng GPU": ("Đồ họa", "Hãng GPU"),
    "Dòng GPU": ("Đồ họa", "Dòng GPU"),
    "Dung lượng VRAM": ("Đồ họa", "Dung lượng VRAM"),
    "Loại VRAM": ("Đồ họa", "Loại VRAM"),
    "Kích thước màn hình": ("Màn hình", "Kích thước màn hình"),
    "Độ phân giải": ("Màn hình", "Độ phân giải"),
    "Tần số quét": ("Màn hình", "Tần số quét"),
    "Tấm nền": ("Màn hình", "Tấm nền"),
    "Công nghệ màn hình": ("Màn hình", "Công nghệ màn hình"),
    "Độ sáng màn hình": ("Màn hình", "Độ sáng màn hình"),
    "Chuẩn màu": ("Màn hình", "Chuẩn màu"),
    "Độ phủ sRGB": ("Màn hình", "Độ phủ sRGB"),
    "Độ phủ DCI-P3": ("Màn hình", "Độ phủ DCI-P3"),
    "Độ phủ NTSC": ("Màn hình", "Độ phủ NTSC"),
    "Độ phủ Adobe RGB": ("Màn hình", "Độ phủ Adobe RGB"),
    "Chứng nhận HDR": ("Màn hình", "Chứng nhận HDR"),
    "Chống chói": ("Màn hình", "Chống chói"),
    "Màn hình cảm ứng": ("Màn hình", "Màn hình cảm ứng"),
    "Laptop 2 trong 1": ("Màn hình", "Laptop 2 trong 1"),
    "Đồng bộ khung hình": ("Màn hình", "Đồng bộ khung hình"),
    "Tên màn hình riêng": ("Màn hình", "Tên màn hình riêng"),
    "Cổng kết nối": ("Kết nối & cổng", "Cổng kết nối"),
    "Chuẩn LAN": ("Kết nối & cổng", "Chuẩn LAN"),
    "Webcam": ("Kết nối & cổng", "Webcam"),
    "Công nghệ âm thanh": ("Kết nối & cổng", "Âm thanh"),
    "Bảo mật": ("Kết nối & cổng", "Bảo mật"),
    "Loại pin": ("Pin", "Loại pin"),
    "Số cell pin": ("Pin", "Số cell pin"),
    "Thời lượng pin": ("Pin", "Thời lượng pin"),
    "Kích thước máy": ("Thiết kế", "Kích thước máy"),
    "Chất liệu vỏ (mặt trên)": ("Thiết kế", "Chất liệu vỏ (mặt trên)"),
    "Chất liệu vỏ (mặt dưới)": ("Thiết kế", "Chất liệu vỏ (mặt dưới)"),
    "Chất liệu vỏ (màn hình)": ("Thiết kế", "Chất liệu vỏ (màn hình)"),
}
# Các nhãn gốc được ghép/biến đổi hoặc bỏ vì trùng thông tin (xem build_specs)
HANDLED = {"Hãng sản xuất", "Bảo hành", "Nhu cầu sử dụng", "Nhu cầu sử dụng laptop", "Hệ điều hành",
           "Dung lượng RAM", "RAM", "Dung lượng SSD", "SSD", "Giao thức SSD", "Chuẩn giao tiếp SSD",
           "Chuẩn SSD", "Chuẩn WIFI", "Chuẩn Wifi/Bluetooth", "Bluetooth", "Pin", "Dung lượng pin",
           "Trọng lượng", "Đèn bàn phím", "Bàn phím có đèn"}


def clean(v):
    v = re.sub(r"[®™]", "", v)
    return re.sub(r"\s+", " ", v).strip()


def num(text, pat=r"(\d+(?:[.,]\d+)?)"):
    m = re.search(pat, text or "", re.I)
    return float(m.group(1).replace(",", ".")) if m else None


def fmt_storage(gb):
    if gb is None:
        return None
    return f"{gb // 1024} TB" if gb >= 1024 and gb % 1024 == 0 else f"{gb} GB"


def fmt_num(x):
    return f"{x:g}"


def norm_os(v):
    if not v:
        return None
    low = v.lower()
    if "dos" in low or "không" in low:
        return "Không kèm hệ điều hành (DOS)"
    if "windows 11 home" in low:
        return "Windows 11 Home"
    if "windows 11" in low:
        return "Windows 11"
    return v


def norm_wifi(raw):
    m = re.search(r"wi-?fi\s*(\d{1,2}E?)(?!\d)", raw or "", re.I)
    return f"Wi-Fi {m.group(1).upper()}" if m else None


def norm_bt(raw):
    raw = (raw or "").strip()
    if raw and re.search(r"wi-?fi", raw, re.I) and not re.search(r"bluetooth", raw, re.I):
        return None   # nguồn ghi nhầm thông tin Wi-Fi vào ô Bluetooth
    raw = re.sub(r"^v(?=\d)", "", raw, flags=re.I)
    raw = re.sub(r"\b(\d+)\.0\b", r"\1", raw) if re.fullmatch(r"(?:Bluetooth\s*)?[6-9]\.0", raw, re.I) else raw
    if re.fullmatch(r"\d+(\.\d+)?( ?/ ?\d+(\.\d+)?)*", raw):
        return f"Bluetooth {raw}"
    m = re.fullmatch(r"Bluetooth\s*(\d+(?:\.\d+)?)", raw, re.I)
    if m:
        v = m.group(1)
        return f"Bluetooth {v[:-2] if v.endswith('.0') and int(v[0]) >= 6 else v}"
    m = re.search(r"Bluetooth\s*(\d+(?:\.\d+)?)", raw, re.I)
    if m and "hoặc" not in raw:
        return f"Bluetooth {m.group(1)}"
    return raw or None


def norm_ssd_interface(*raws):
    text = " ".join(r for r in raws if r)
    if not text:
        return None
    nvme = bool(re.search(r"nvme", text, re.I))
    gen = re.search(r"(?:pcie\s*|gen\s*)(\d(?:\.\d)?)", text, re.I)
    parts = []
    if nvme:
        parts.append("NVMe")
    if gen:
        g = gen.group(1) if "." in gen.group(1) else gen.group(1) + ".0"
        parts.append(f"PCIe {g}")
    elif re.search(r"pcie", text, re.I):
        parts.append("PCIe")
    return " ".join(parts) or clean(text)


def norm_cpu_line(v):
    if not v:
        return None
    v = clean(v)
    m = re.fullmatch(r"(i[3579])", v, re.I)
    if m:
        return f"Core {m.group(1).lower()}"
    if re.fullmatch(r"Ultra \d", v, re.I):
        return "Core " + v
    return v


def guess_cpu_brand(cpu):
    low = (cpu or "").lower()
    return "Intel" if "intel" in low or "core" in low else "AMD" if ("amd" in low or "ryzen" in low) else None


def guess_gpu_brand(card):
    low = (card or "").lower()
    if "nvidia" in low or "geforce" in low or "rtx" in low or "gtx" in low:
        return "NVIDIA"
    if "radeon" in low or "amd" in low:
        return "AMD"
    if "intel" in low or "arc" in low or "uhd" in low or "iris" in low:
        return "Intel"
    return None


def build_product(p, unmapped):
    raw = {}
    for s in p["specs"]:
        raw.setdefault(s["label"], clean(s["value"]))

    def get(*labels):
        for l in labels:
            if raw.get(l):
                return raw[l]
        return None

    for label in raw:
        if label not in SIMPLE and label not in HANDLED:
            unmapped.add(label)

    # ---- các giá trị đã ghép/chuẩn hóa ----
    ram_gb = num(get("Dung lượng RAM", "RAM"))
    ram_gb = int(ram_gb) if ram_gb else None
    ssd_gb = num(get("Dung lượng SSD", "SSD"))
    ssd_gb = int(ssd_gb) if ssd_gb else None
    if ssd_gb and ssd_gb >= 1000 and ssd_gb % 1024:
        ssd_gb = round(ssd_gb / 1024) * 1024   # 1000 GB -> 1024 GB (1 TB)
    screen_inch = num(get("Kích thước màn hình"))
    refresh_hz = num(get("Tần số quét"))
    vram_gb = num(get("Dung lượng VRAM"))
    wt_raw = get("Trọng lượng") or ""
    weight_kg = num(wt_raw, r"(\d+(?:[.,]\d+)?)\s*kg")
    if weight_kg is None and (g := num(wt_raw, r"(\d+(?:[.,]\d+)?)\s*g\b")):
        weight_kg = g / 1000
    batt_wh = num(get("Dung lượng pin"), r"(\d+(?:\.\d+)?)\s*wh") or num(get("Pin"), r"(\d+(?:\.\d+)?)\s*-?\s*wh")
    warranty = num(get("Bảo hành"), r"(\d+)\s*tháng")
    usage_raw = get("Nhu cầu sử dụng laptop", "Nhu cầu sử dụng") or ""
    usage = [u.strip() for u in usage_raw.split(",") if u.strip()]
    os_name = norm_os(get("Hệ điều hành"))
    wifi = norm_wifi(get("Chuẩn WIFI", "Chuẩn Wifi/Bluetooth"))
    bt = norm_bt(get("Bluetooth")) or norm_bt_from_combo(get("Chuẩn Wifi/Bluetooth"))
    ssd_if = norm_ssd_interface(get("Giao thức SSD"), get("Chuẩn giao tiếp SSD"), get("Chuẩn SSD"))
    kb_light = get("Đèn bàn phím", "Bàn phím có đèn")
    cpu = get("CPU")
    gpu = get("Card đồ họa")
    brand = BRANDS.get((p["brand"] or "").upper(), (p["brand"] or "").title())

    attrs = {
        "brand": brand,
        "cpu": cpu,
        "cpu_brand": get("Hãng CPU") or guess_cpu_brand(cpu),
        "cpu_line": norm_cpu_line(get("Dòng CPU")),
        "ram_gb": ram_gb,
        "ram_type": get("Loại RAM"),
        "ssd_gb": ssd_gb,
        "gpu": gpu,
        "gpu_brand": get("Hãng GPU") or guess_gpu_brand(gpu),
        "vram_gb": int(vram_gb) if vram_gb else None,
        "screen_inch": screen_inch,
        "refresh_hz": int(refresh_hz) if refresh_hz else None,
        "resolution": get("Độ phân giải"),
        "os": os_name,
        "weight_kg": round(weight_kg, 2) if weight_kg else None,
        "battery_wh": batt_wh,
        "warranty_months": int(warranty) if warranty else None,
        "usage": usage,
    }

    # ---- bảng thông số hiển thị (nhãn thống nhất, nhóm cố định) ----
    rows = []

    def add(group, label, value):
        if value not in (None, "", []):
            rows.append({"group": group, "label": label, "value": value})

    add("Tổng quan", "Hãng sản xuất", brand)
    add("Tổng quan", "Bảo hành", f"{int(warranty)} tháng" if warranty else None)
    add("Tổng quan", "Nhu cầu sử dụng", ", ".join(usage))
    add("Tổng quan", "Hệ điều hành", os_name)
    add("Bộ nhớ RAM", "Dung lượng RAM", f"{ram_gb} GB" if ram_gb else None)
    add("Lưu trữ", "Dung lượng SSD", fmt_storage(ssd_gb))
    add("Lưu trữ", "Chuẩn SSD", ssd_if)
    add("Kết nối & cổng", "Wi-Fi", wifi or get("Chuẩn WIFI", "Chuẩn Wifi/Bluetooth"))
    add("Kết nối & cổng", "Bluetooth", bt)
    add("Pin", "Dung lượng pin", f"{fmt_num(batt_wh)} Wh" if batt_wh else None)
    pin = get("Pin")
    cells = re.fullmatch(r"(\d+)(?:\s*cells?)?", pin or "", re.I)
    if cells and not raw.get("Số cell pin"):
        add("Pin", "Số cell pin", f"{cells.group(1)} cell")
    elif pin and not cells and not re.fullmatch(r"\d+(?:\.\d+)?\s*wh", pin, re.I):
        add("Pin", "Chi tiết pin", pin)
    add("Thiết kế", "Trọng lượng", f"{fmt_num(round(weight_kg, 2))} kg" if weight_kg else None)
    if kb_light:
        if kb_light in ("Có", "Không"):
            add("Thiết kế", "Đèn bàn phím", kb_light)
        else:
            add("Thiết kế", "Đèn bàn phím", "Có")
            add("Thiết kế", "Loại đèn bàn phím", kb_light)
    for label, (group, canon) in SIMPLE.items():
        value = raw.get(label)
        if label == "Số cell pin" and value:
            value = re.sub(r"^(\d+)\s*cells?$", r"\1", value, flags=re.I)
            value = f"{value} cell" if value.isdigit() else value
        add(group, canon, value)

    order = {g: i for i, g in enumerate(GROUPS)}
    # sort ổn định: giữ thứ tự thêm vào trong cùng một nhóm
    rows.sort(key=lambda r: order[r["group"]])
    return attrs, rows


def norm_bt_from_combo(raw):
    m = re.search(r"Bluetooth\s*(\d+(?:\.\d+)?)", raw or "", re.I)
    return f"Bluetooth {m.group(1)}" if m and "hoặc" not in raw else None


def article_to_html(blocks):
    out, in_list = [], False
    for b in blocks:
        t = b["type"]
        if t == "li":
            if not in_list:
                out.append("<ul>")
                in_list = True
            out.append(f"<li>{escape(b['text'])}</li>")
            continue
        if in_list:
            out.append("</ul>")
            in_list = False
        if t in ("h2", "h3", "h4"):
            out.append(f"<{t}>{escape(b['text'])}</{t}>")
        elif t == "p":
            out.append(f"<p>{escape(b['text'])}</p>")
    if in_list:
        out.append("</ul>")
    return "\n".join(out)


def category_of(attrs, name):
    gaming = "Gaming" in attrs["usage"] or "gaming" in name.lower()
    if not attrs["usage"] and re.search(r"rtx|gtx", attrs["gpu"] or "", re.I):
        gaming = True
    return "Laptop gaming" if gaming else "Laptop văn phòng"


def main():
    data = json.loads(SRC.read_text(encoding="utf-8"))
    unmapped = set()
    result = []
    for p in data:
        attrs, specs = build_product(p, unmapped)
        result.append({
            "sku": p["sku"], "slug": p["slug"], "name": clean(p["name"]),
            "category": category_of(attrs, p["name"]),
            "price": p["price"], "old_price": p["old_price"],
            "stock": 10 if p["in_stock"] else 0,
            "short_description": clean(p["short_description"] or ""),
            "image": p.get("local_image"), "gallery": p["images"],
            "source_url": p["source_url"],
            "attrs": attrs, "specs": specs,
            "article_html": article_to_html(p["article"]),
        })
    OUT.write_text(json.dumps(result, ensure_ascii=False, indent=2), encoding="utf-8")

    print(f"Đã chuẩn hóa {len(result)} sản phẩm -> {OUT}")
    print("Nhãn chưa có trong bảng ánh xạ:", sorted(unmapped) or "không có")
    print("\nThiếu giá trị ở thuộc tính (trên 100 sản phẩm):")
    for k in result[0]["attrs"]:
        miss = sum(1 for r in result if r["attrs"][k] in (None, "", []))
        if miss:
            print(f"  {k:<16} thiếu {miss}")
    print("\nGiá trị sau chuẩn hóa:")
    for k in ("brand", "cpu_brand", "cpu_line", "gpu_brand", "os", "ram_gb", "ssd_gb", "screen_inch", "refresh_hz"):
        from collections import Counter
        c = Counter(r["attrs"][k] for r in result)
        print(f"  {k:<12}", dict(c.most_common(12)))
    from collections import Counter
    print("  category    ", dict(Counter(r["category"] for r in result)))
    print("  nhãn thông số chuẩn:", len({s['label'] for r in result for s in r['specs']}), "(trước đó: 72)")


if __name__ == "__main__":
    main()
