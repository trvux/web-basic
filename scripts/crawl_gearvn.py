#!/usr/bin/env python3
"""Lấy dữ liệu laptop từ gearvn.com/collections/laptop để làm dữ liệu mẫu cho đồ án.

Chạy:  python3 scripts/crawl_gearvn.py [số_sản_phẩm]      (mặc định 100)
Ra:    data/gearvn_laptops.json

Chỉ dùng thư viện chuẩn của Python. Lịch sự với site: 1 request/giây, khai báo
đúng User-Agent, tôn trọng robots.txt (chỉ đọc /collections/ và /products/).
HTML đã tải được cache trong data/.cache/ nên chạy lại không tải lại từ đầu.
"""
import hashlib
import json
import re
import sys
import time
import urllib.error
import urllib.request
from html.parser import HTMLParser
from pathlib import Path

BASE = "https://gearvn.com"
COLLECTION = BASE + "/collections/laptop"
UA = "gearvn-student-crawler/1.0 (university project)"
DELAY = 1.0  # giây giữa 2 request

ROOT = Path(__file__).resolve().parent.parent
CACHE = ROOT / "data" / ".cache"
OUT = ROOT / "data" / "gearvn_laptops.json"


def fetch(url):
    """Tải HTML (có cache trên đĩa). Trả về None nếu lỗi."""
    CACHE.mkdir(parents=True, exist_ok=True)
    f = CACHE / (hashlib.md5(url.encode()).hexdigest() + ".html")
    if f.exists():
        return f.read_text(encoding="utf-8")
    time.sleep(DELAY)
    req = urllib.request.Request(url, headers={"User-Agent": UA, "Accept-Language": "vi"})
    for attempt in range(3):
        try:
            with urllib.request.urlopen(req, timeout=30) as r:
                html = r.read().decode("utf-8", "replace")
            f.write_text(html, encoding="utf-8")
            return html
        except (urllib.error.URLError, TimeoutError) as e:
            print(f"   ! lỗi tải {url} (lần {attempt + 1}): {e}", file=sys.stderr)
            time.sleep(3 * (attempt + 1))
    return None


def list_product_urls(want):
    urls = []
    page = 1
    while len(urls) < want and page <= 15:
        html = fetch(f"{COLLECTION}?page={page}")
        if not html:
            break
        found = re.findall(r'href="(/products/[^"?#]+)"', html)
        new = [BASE + u for u in dict.fromkeys(found) if BASE + u not in urls]
        print(f"trang {page}: {len(new)} sản phẩm mới")
        if not new:
            break
        urls += new
        page += 1
    return urls[:want]


def flight_text(html):
    """Ghép các mảnh self.__next_f.push([1,"..."]) lại thành một chuỗi."""
    parts = []
    for m in re.finditer(r'self\.__next_f\.push\(\[1,("(?:[^"\\]|\\.)*")\]\)', html):
        parts.append(json.loads(m.group(1)))
    return "".join(parts)


def extract_json_after(text, key):
    """Tìm  "key":<json>  trong text và parse đúng giá trị JSON đó."""
    i = text.find(f'"{key}":')
    if i < 0:
        return None
    try:
        val, _ = json.JSONDecoder().raw_decode(text[i + len(key) + 3:])
        return val
    except ValueError:
        return None


class Article(HTMLParser):
    """Lấy bài viết trong #product-description thành các khối: h2/h3/p/li + ảnh."""

    BLOCKS = {"h2", "h3", "h4", "p", "li"}
    SKIP = {"button", "svg", "script", "style", "nav"}

    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.depth = 0          # độ sâu so với thẻ #product-description (0 = chưa vào)
        self.skip = 0
        self.cur = None         # [tag, [text...]]
        self.blocks = []

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if self.depth == 0:
            if a.get("id") == "product-description":
                self.depth = 1
            return
        if tag not in ("br", "img", "hr", "meta", "input", "link", "source", "path"):
            self.depth += 1
        if tag in self.SKIP:
            self.skip += 1
        if self.skip:
            return
        if tag == "img":
            src = a.get("src") or a.get("data-src")
            if src and src.startswith("http"):
                self.blocks.append({"type": "img", "src": src, "alt": a.get("alt", "")})
        elif tag in self.BLOCKS:
            self.flush()
            self.cur = [tag, []]
        elif tag == "br" and self.cur:
            self.cur[1].append(" ")

    def handle_endtag(self, tag):
        if self.depth == 0:
            return
        if tag in self.BLOCKS:
            self.flush()
        if tag in self.SKIP and self.skip:
            self.skip -= 1
        if tag not in ("br", "img", "hr", "meta", "input", "link", "source", "path"):
            self.depth -= 1

    def handle_data(self, data):
        if self.depth and not self.skip and self.cur:
            self.cur[1].append(data)

    def flush(self):
        if self.cur:
            text = re.sub(r"\s+", " ", "".join(self.cur[1])).strip()
            if text:
                self.blocks.append({"type": self.cur[0], "text": text})
        self.cur = None


def parse_product(url, html):
    ld = None
    for raw in re.findall(r'<script type="application/ld\+json"[^>]*>(.*?)</script>', html, re.S):
        try:
            d = json.loads(raw)
        except ValueError:
            continue
        if isinstance(d, dict) and d.get("@type") == "Product":
            ld = d
            break
    if not ld:
        return None

    offer = ld.get("offers") or {}
    old = (offer.get("priceSpecification") or {}).get("price")
    price = offer.get("price")

    flight = flight_text(html)
    groups = extract_json_after(flight, "specs_table") or []
    specs = []   # [{group, label, value}]
    for g in groups:
        for r in g.get("rows", []):
            if r.get("label") and r.get("value"):
                specs.append({"group": g.get("group_name") or g.get("title"),
                              "label": r["label"], "value": r["value"]})

    art = Article()
    art.feed(html)
    art.flush()
    # bỏ khối "Mục lục", chỉ giữ từ tiêu đề bài viết đầu tiên
    blocks = [b for b in art.blocks if not (b["type"] == "h2" and b["text"] == "Thông tin sản phẩm")]

    return {
        "source_url": url,
        "slug": url.rsplit("/", 1)[-1],
        "sku": ld.get("sku"),
        "name": ld.get("name"),
        "brand": (ld.get("brand") or {}).get("name"),
        "price": int(price) if price else None,
        "old_price": int(old) if old and price and old > price else None,
        "in_stock": "InStock" in (offer.get("availability") or ""),
        "short_description": ld.get("description"),
        "images": ld.get("image") if isinstance(ld.get("image"), list) else [ld.get("image")],
        "specs": specs,
        "article": blocks,
    }


def main():
    want = int(sys.argv[1]) if len(sys.argv) > 1 else 100
    # lấy dư để bù những sản phẩm lỗi/thiếu dữ liệu
    urls = list_product_urls(want + 25)
    print(f"Có {len(urls)} link, bắt đầu tải chi tiết...")
    products = []
    for n, url in enumerate(urls, 1):
        if len(products) >= want:
            break
        html = fetch(url)
        p = parse_product(url, html) if html else None
        if not p or not p["price"] or not p["specs"]:
            print(f"[{n}] bỏ qua (thiếu dữ liệu): {url}")
            continue
        products.append(p)
        print(f"[{n}] {len(products):>3} {p['name'][:60]}  specs={len(p['specs'])} bài={len(p['article'])}")
    OUT.parent.mkdir(exist_ok=True)
    OUT.write_text(json.dumps(products, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Xong: {len(products)} sản phẩm -> {OUT}")


if __name__ == "__main__":
    main()
