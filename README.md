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
File `db/init.sql` chỉ chạy **lần đầu**. Nếu sửa cấu trúc bảng hoặc muốn DB sạch lại:

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
db/init.sql          tạo bảng + dữ liệu mẫu

Tài khoản test: `admin / admin123` (admin), `user / user123` (khách)
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
