# Web bán đồ đơn giản (PHP + MySQL + Docker)

## 1. Chạy project

Cần cài sẵn **Docker Desktop**. Không cần cài PHP/MySQL/Apache gì thêm.

```bash
git clone <link-repo>
cd web-basic
docker compose up -d --build
```

| Cái gì | Địa chỉ |
|---|---|
| Website | http://localhost:8088 |
| phpMyAdmin (xem DB) | http://localhost:8081 (tự đăng nhập) |

Tắt: `docker compose down`

**Sửa code PHP xong chỉ cần F5 trình duyệt**, không cần build lại (thư mục `src/` được mount thẳng vào container).

### Reset database
Các file `.sql` trong `db/` chỉ chạy **lần đầu**, khi volume DB còn trống: `init.sql` (bảng, tài khoản, danh mục) rồi `seed-products.sql` (100 laptop, **không có trong git**, xem mục 5). Nếu sửa cấu trúc bảng hoặc muốn DB sạch lại:

```bash
docker compose down -v   # -v xoá luôn dữ liệu
docker compose up -d
```

> Đã `git pull` mà thấy bảng mới của người khác chưa có? Chạy lệnh reset ở trên.

## 2. Cấu trúc thư mục

```
src/
  config/db.php      kết nối DB ($pdo), hàm e() escape HTML
  includes/          header.php, footer.php (menu) - include vào mọi trang
  includes/head.php  Tailwind CDN + theme đỏ (brand color) -- đổi màu ở đây
  includes/ui.php    bộ class shadcn dùng chung: ui_btn(), UI_INPUT, UI_CARD...
  index.php          trang chủ (KHÁCH xem sản phẩm)
  admin/             trang ADMIN (menu đỏ, header riêng admin_header.php)
    index.php        dashboard
    products/        CRUD sản phẩm  <-- ĐÃ LÀM, dùng làm MẪU
    categories/      CRUD danh mục
    orders/          xem tất cả đơn hàng
  auth/              đăng ký / đăng nhập / đăng xuất
  cart/              giỏ hàng (session) + đặt hàng
  orders/            đơn hàng của tôi (khách)
db/init.sql          tạo bảng, tài khoản test, danh mục
db/seed-products.sql 100 laptop (TỰ SINH, không commit - xem mục 5)
data/                dữ liệu crawl từ gearvn.com (không commit)
scripts/             crawl_gearvn.py -> download_images.py -> normalize_data.py -> build_seed_sql.py
src/uploads/products ảnh sản phẩm (không commit)

Tài khoản test (**chỉ có trên máy dev**, bản chạy thật dùng mật khẩu khác): `admin / admin123` (admin), `user / user123` (khách)
```

## 3. Phân công

| Ai | Việc | Thư mục |
|---|---|---|
| A (leader) | Setup, CRUD sản phẩm, merge code, báo cáo | `admin/products/` |
| B | CRUD danh mục | `admin/categories/` |
| C | Đăng ký / đăng nhập / đăng xuất | `auth/` |
| D | Giỏ hàng (dùng session) | `cart/` + nút trong `index.php` |

Mỗi file stub đã có comment `TODO` ghi rõ các bước. **Cách học nhanh nhất: mở `src/admin/products/` ra đọc, copy rồi sửa.**

Mỗi người làm trong thư mục riêng nên gần như không bị conflict.

## 4. Quy trình Git (mỗi người làm)

```bash
git checkout main
git pull                          # luôn pull trước khi làm
git checkout -b feature/ten-viec  # vd: feature/categories

# ... code, test trên localhost:8088 ...

git add .
git commit -m "Thêm CRUD danh mục"
git push -u origin feature/ten-viec
```

Rồi lên GitHub tạo **Pull Request** vào `main`, nhờ leader review và merge. **Không push thẳng vào `main`.**

## 5. Luật code (để khỏi bị lỗi/bị trừ điểm)

1. Query SQL luôn dùng `prepare()` + `execute([...])`, **không** nối biến vào chuỗi SQL.
2. In dữ liệu ra HTML luôn bọc `e(...)`: `<?= e($row['name']) ?>`
3. Mật khẩu dùng `password_hash()` / `password_verify()`, không lưu thô.
4. Mỗi trang PHP bắt đầu bằng `require_once` header, kết thúc bằng footer (xem file mẫu).
5. Kết nối DB host là `db`, không phải `localhost`.

## 6. Tài khoản trên web thật (web.phren.dev)

Tài khoản được tạo thẳng trong DB trên VPS (không commit vào git). Leader thêm người mới bằng:

```bash
./scripts/add-user.sh <mã-số-sinh-viên> <mật-khẩu> [user|admin]
```

Đăng nhập tại https://web.phren.dev/auth/login.php. **Không commit** mật khẩu, `.env` hay mã số sinh viên vào repo (repo này public).

## 5. Dữ liệu sản phẩm (laptop từ gearvn.com)

Sản phẩm, ảnh, thông số, bài viết lấy từ [gearvn.com](https://gearvn.com/collections/laptop) làm dữ liệu mẫu cho đồ án (nguồn từng sản phẩm lưu ở cột `products.source_url`). **Repo này public nên dữ liệu và ảnh không nằm trong git**: chỉ có script. Để có dữ liệu ở máy mình, nhận gói dữ liệu từ nhóm trưởng (giải nén vào `data/`, `src/uploads/products/`, `db/seed-products.sql`) hoặc tự chạy theo thứ tự (chỉ cần Python 3, mất vài phút):

```bash
python3 scripts/crawl_gearvn.py 100     # tải dữ liệu  -> data/gearvn_laptops.json
python3 scripts/download_images.py      # tải ảnh      -> src/uploads/products/
python3 scripts/normalize_data.py       # chuẩn hóa    -> data/laptops_clean.json
python3 scripts/build_seed_sql.py       # sinh SQL     -> db/seed-products.sql
docker compose down -v && docker compose up -d   # nạp lại vào DB
```

Bảng: `products` (có các cột lọc như `ram_gb`, `ssd_gb`, `screen_inch`, `brand`...) và `product_specs` (bảng thông số đầy đủ, mỗi dòng một thông số).
