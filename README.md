# Web bán đồ đơn giản (PHP + MySQL + Docker)

## 1. Chạy project

Cần cài sẵn **Docker Desktop**. Không cần cài PHP/MySQL/Apache gì thêm.

```bash
git clone <link-repo>
cd web-basic
unzip ~/Downloads/laptop-data-pack.zip   # gói dữ liệu nhóm trưởng gửi, giải nén ngay trong thư mục web-basic
docker compose up -d --build
./scripts/migrate.sh                    # áp các thay đổi DB mới nhất (an toàn khi chạy lại)
```

**Gói dữ liệu** (`laptop-data-pack.zip`) chứa 100 laptop + ảnh. Nó không nằm trong git vì repo này public (dữ liệu lấy từ gearvn.com). Giải nén **trước** lần chạy đầu tiên thì DB tự có sẵn sản phẩm. Lỡ chạy rồi mới giải nén thì reset DB một lần (mục "Reset database" bên dưới). Chưa có gói dữ liệu thì web vẫn chạy nhưng chưa có sản phẩm.

Tài khoản đăng nhập thử có sẵn: `admin / admin123` (vào trang quản trị), `user / user123` (khách).

| Cái gì | Địa chỉ |
|---|---|
| Website | http://localhost:8088 |
| phpMyAdmin (xem DB) | http://localhost:8081 (tự đăng nhập) |

Tắt: `docker compose down`

**Sửa code PHP xong chỉ cần F5 trình duyệt**, không cần build lại (thư mục `src/` được mount thẳng vào container).

### Reset database
Các file `.sql` trong `db/` chỉ chạy **lần đầu**, khi volume DB còn trống: `init.sql` (bảng, tài khoản, danh mục) rồi `seed-products.sql` (100 laptop, **không có trong git**, xem mục 9). Nếu sửa cấu trúc bảng hoặc muốn DB sạch lại:

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
db/seed-products.sql 100 laptop (TỰ SINH, không commit - xem mục 9)
data/                dữ liệu crawl từ gearvn.com (không commit)
scripts/             crawl_gearvn.py -> download_images.py -> normalize_data.py -> build_seed_sql.py
src/uploads/products ảnh sản phẩm (không commit)

Tài khoản test (**chỉ có trên máy dev**, bản chạy thật dùng mật khẩu khác): `admin / admin123` (admin), `user / user123` (khách)
```

## 3. Phân công (hạn chốt: **28/11**)

Nền đã xong (đăng ký/đăng nhập, giỏ hàng, đặt hàng, CRUD sản phẩm + danh mục, xem đơn). Giờ mỗi người làm **tính năng mới trong thư mục riêng của mình**. Chi tiết từng việc, tiêu chí xong và lịch 3 ngày: [`docs/BACKLOG.md`](docs/BACKLOG.md).

| Ai | Việc chính | Được sửa |
|---|---|---|
| **Huy** (leader) | XAMPP, free host, rà bảo mật, slide, merge | mọi nơi (file chung: `includes/`, `config/`, `db/`, `src/index.php`) |
| **Minh** | Danh sách SP (tìm kiếm, lọc, sắp xếp, phân trang), chi tiết SP | `src/products/` |
| **Phong** | Quản lý người dùng, đổi trạng thái đơn, dashboard | `src/admin/users/`, `src/admin/orders/`, `src/admin/index.php` |
| **Phúc** | Quản lý banner, bài viết, validate giỏ hàng | `src/admin/banners/`, `src/admin/posts/`, `src/account/`, `src/orders/` |

Ai cũng được thêm file mới trong `db/migrations/`. **CI tự báo đỏ nếu PR sửa file ngoài phần của mình**, nên hai người không thể sửa chung một file. Cần sửa file chung thì nhắn Huy, Huy sửa giúp.

**Cách học nhanh nhất: mở `src/admin/products/` ra đọc, copy rồi sửa.**

## 4. Quy trình Git (mỗi người làm)

**Không ai push thẳng vào `main`** (GitHub chặn). `main` tự deploy lên web.phren.dev, code lỗi là lên thẳng web thật. Mọi thứ đi qua Pull Request, **Huy review rồi mới merge**. PR phải: CI xanh + được Huy duyệt + nhánh đã cập nhật theo `main` mới nhất.

### Luật sống còn: luôn làm trên code MỚI NHẤT

Lỗi hay gặp: A làm xong và được merge, B vẫn làm tiếp trên bản cũ ở máy mình rồi mở PR, kết quả là conflict hoặc ghi đè code của A. Cách tránh:

1. **Bắt đầu việc mới = dùng script**, nó tự kéo `main` mới nhất rồi mới tách nhánh:
   ```bash
   ./scripts/start-work.sh feature/ten-viec     # vd: feature/product-filter
   ```
2. **Mỗi khi có PR của ai đó được merge** (Huy sẽ nhắn trong nhóm), và **trước khi mở PR**, cập nhật nhánh của mình:
   ```bash
   ./scripts/sync-branch.sh
   ```
3. GitHub sẽ **không cho merge** nếu nhánh của mình cũ hơn `main`. Thấy nút "Update branch" hoặc "This branch is out-of-date" thì chạy lại lệnh ở bước 2 rồi `git push`.

Sau khi PR được merge: nhánh đó bỏ, việc tiếp theo tạo nhánh mới bằng `start-work.sh` (đừng dùng lại nhánh cũ).

### Các bước mỗi việc

```bash
./scripts/start-work.sh feature/ten-viec   # 1. kéo main mới + tách nhánh
# 2. code, test trên localhost:8088, commit thường xuyên
git add . && git commit -m "Thêm lọc sản phẩm theo hãng"
./scripts/sync-branch.sh                   # 3. kéo main mới vào nhánh trước khi đẩy
git push -u origin feature/ten-viec        # 4. đẩy nhánh, rồi mở Pull Request vào main trên GitHub
```

**PR nhỏ, merge sớm.** Một PR = một việc nhỏ, làm xong trong vài giờ. Nhánh để càng lâu thì càng dễ conflict. Chỉ có 3 ngày, nên mỗi người mở PR **ít nhất 1 lần mỗi ngày**.

**Chỉ sửa file trong phần của mình (mục 3).** CI kiểm tra tự động.

### Khi GitHub báo "This branch has conflicts"

Conflict nghĩa là có người merge vào `main` trước, và hai bên sửa cùng một chỗ. Gỡ ngay trên nhánh của mình:

```bash
git checkout feature/ten-viec
git pull origin main          # kéo main mới vào nhánh mình
```

Nếu Git báo `CONFLICT` thì mở file đó, tìm khối này và giữ lại đúng code (thường là cả hai bên), xóa 3 dòng đánh dấu:

```
<<<<<<< HEAD
code của mình
=======
code của người khác
>>>>>>> origin/main
```

```bash
git add <file-vừa-sửa>
git commit                    # để nguyên message Git đưa sẵn
git push
```

Không biết gỡ thì **nhắn leader, đừng xóa đại code của người khác.** Không bao giờ dùng `git push --force`.

## 5. Giao diện: BẮT BUỘC dùng bộ shadcn có sẵn

Giao diện đã được chuyển từ shadcn/ui sang PHP + Tailwind, nằm sẵn trong **`src/includes/ui.php`** và theme đỏ trong `src/includes/head.php`. **Mọi trang mới phải dùng, không tự viết class riêng.** Làm sai thì giao diện mỗi trang một kiểu, mất điểm tiêu chí "giao diện đẹp" (3đ).

| Cần | Dùng |
|---|---|
| Nút | `class="<?= ui_btn() ?>"`, biến thể `ui_btn('outline')`, `'destructive'`, `'secondary'`, `'ghost'`; cỡ `ui_btn('default', 'sm')` |
| Ô nhập, nhãn, nhóm trường | `UI_INPUT`, `UI_TEXTAREA`, `UI_LABEL`, `UI_FIELD` |
| Thẻ (card) | `UI_CARD`, `UI_CARD_HEADER`, `UI_CARD_TITLE`, `UI_CARD_CONTENT` |
| Bảng | `UI_TABLE_WRAP`, `UI_TABLE`, `UI_TR`, `UI_TH`, `UI_TD` |
| Nhãn nhỏ | `ui_badge()` |
| Thông báo lỗi, tiêu đề, khung trang | `UI_ALERT_ERROR`, `UI_H1`, `UI_PAGE` |

**Cấm:** màu Tailwind viết cứng như `bg-red-600`, `text-gray-800`, `border-gray-300`. Cần màu thì dùng token của theme: `bg-primary`, `text-muted-foreground`, `bg-muted`, `border-border`, `text-destructive`.

**Mẫu để copy:** `src/admin/products/` (trang admin: bảng, form) và `src/index.php` (trang khách).

Thiếu component (ví dụ select, phân trang, alert thành công)? **Nhắn Huy thêm vào `ui.php`**, đừng tự viết trong trang của mình. CI tự báo đỏ nếu PR có màu viết cứng.

## 6. Luật code (để khỏi bị lỗi/bị trừ điểm)

1. Query SQL luôn dùng `prepare()` + `execute([...])`, **không** nối biến vào chuỗi SQL.
2. In dữ liệu ra HTML luôn bọc `e(...)`: `<?= e($row['name']) ?>`
3. Mật khẩu dùng `password_hash()` / `password_verify()`, không lưu thô.
4. Mỗi trang PHP bắt đầu bằng `require_once` header, kết thúc bằng footer (xem file mẫu).
5. Kết nối DB host là `db`, không phải `localhost`.

## 7. Tài khoản trên web thật (web.phren.dev)

Tài khoản được tạo thẳng trong DB trên VPS (không commit vào git). Leader thêm người mới bằng:

```bash
./scripts/add-user.sh <mã-số-sinh-viên> <mật-khẩu> [user|admin]
```

Đăng nhập tại https://web.phren.dev/auth/login.php. **Không commit** mật khẩu, `.env` hay mã số sinh viên vào repo (repo này public).

Caddy của project này cũng phục vụ các subdomain khác của `phren.dev` (ví dụ `reperi.phren.dev`): mỗi sản phẩm tự thả một file `<tên>.caddy` vào `/opt/phren-hub/sites` trên VPS, nên **không cần sửa `Caddyfile` ở đây** khi có sản phẩm mới.

## 8. Thay đổi cấu trúc DB (migration)

**Không sửa `db/init.sql`** (nó chỉ tạo DB mới lần đầu, và là file dễ conflict nhất). Cần thêm bảng/cột thì tạo file mới `db/migrations/YYYY-MM-DD-ten-viec.sql`, rồi chạy `./scripts/migrate.sh` (an toàn khi chạy nhiều lần). Sau mỗi lần `git pull` cũng chạy lại lệnh này. Chi tiết và ví dụ: `db/migrations/README.md`. Trên web thật, `deploy.sh` tự áp sau khi merge vào `main`.

## 9. Dữ liệu sản phẩm (laptop từ gearvn.com)

Sản phẩm, ảnh, thông số, bài viết lấy từ [gearvn.com](https://gearvn.com/collections/laptop) làm dữ liệu mẫu cho đồ án (nguồn từng sản phẩm lưu ở cột `products.source_url`). **Repo này public nên dữ liệu và ảnh không nằm trong git**: chỉ có script. Để có dữ liệu ở máy mình, nhận gói dữ liệu từ nhóm trưởng (giải nén vào `data/`, `src/uploads/products/`, `db/seed-products.sql`) hoặc tự chạy theo thứ tự (chỉ cần Python 3, mất vài phút):

```bash
python3 scripts/crawl_gearvn.py 100     # tải dữ liệu  -> data/gearvn_laptops.json
python3 scripts/download_images.py      # tải ảnh      -> src/uploads/products/
python3 scripts/normalize_data.py       # chuẩn hóa    -> data/laptops_clean.json
python3 scripts/build_seed_sql.py       # sinh SQL     -> db/seed-products.sql
docker compose down -v && docker compose up -d   # nạp lại vào DB
```

Bảng: `products` (có các cột lọc như `ram_gb`, `ssd_gb`, `screen_inch`, `brand`...) và `product_specs` (bảng thông số đầy đủ, mỗi dòng một thông số).
