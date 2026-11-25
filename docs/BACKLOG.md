# Product Backlog: LaptopShop (đồ án Lập trình ứng dụng web, 71ITSE30503)

Nguồn: BM-006 (đề bài) + BM-007 (rubric). Nhóm 4 người: Huy (leader), Minh, Phong, Phúc.

**Hạn chốt: 28/11/2026, mọi thứ phải xong và chạy được.** Hôm nay 25/11. Huy đã làm xong nền, 3 bạn còn lại bắt đầu từ số 0, nên backlog này **cắt gọn chỉ còn việc ăn điểm rubric**.

## 1. Rubric → việc phải làm

| Tiêu chí | Điểm | Tối đa khi... | Hiện trạng | Việc |
|---|---|---|---|---|
| Giao diện đẹp, thân thiện | 3 | đạt cả 3: **đầy đủ, đẹp, tiện dụng** | Có trang chủ, giỏ, đăng nhập. Thiếu chi tiết SP, tìm kiếm/lọc, phân trang | S1, S2 |
| Cấu hình được web server | 1 | **chạy được XAMPP + Apache + MySQL** | Chỉ chạy bằng Docker | **X1, X2** (nguy cơ mất điểm) |
| Chức năng quản trị đầy đủ | 3 | ≥3/4 thao tác đọc/thêm/xoá/sửa **và quản trị được nội dung website** | CRUD SP + DM xong, đơn chỉ xem | A1, A2, A4 (+A3, A5) |
| Có website local + free host | 1 | local **và** free host | Local + VPS riêng, chưa có free host | D1 |
| Làm việc nhóm hiệu quả | 1 | nhóm chấm hoàn thành 80–100% | Có PR flow | Mỗi người ≥1 PR/ngày |
| Nộp đúng hạn | 1 | đúng hạn + đúng quy cách file | Chưa | P1, P2 |

Rubric ghi cụ thể **XAMPP**, nên phải có hướng dẫn chạy XAMPP dù nhóm dùng Docker.

## 2. Phân công

| Người | Làm | Thư mục sở hữu |
|---|---|---|
| **Huy** (leader) | H0, X1–X3, D1, A6, P1, P2, review + merge | file chung (`includes/`, `config/`, `db/`, `src/index.php`) |
| **Minh** | S1, S2, S4 | `src/products/` |
| **Phong** | A1, A2, A3 | `src/admin/users/`, `src/admin/orders/`, `src/admin/index.php` |
| **Phúc** | A4, A5, U3 | `src/admin/banners/`, `src/admin/posts/`, `src/account/`, `src/orders/` |

CI tự báo đỏ nếu PR sửa file ngoài phần của mình. Cần đổi DB thì thêm file trong `db/migrations/` (không sửa `init.sql`). **Cách học nhanh: đọc `src/admin/products/`, copy rồi sửa.**

## 3. Backlog

Ưu tiên: **M** = Must (ăn điểm), **S** = Should (làm nếu kịp). Cỡ: S ≈ 1–2 giờ, M ≈ nửa ngày, L ≈ 1 ngày.

### Z0. Làm quen, **tối 25/11** (Minh, Phong, Phúc)

| Việc | Xong khi |
|---|---|
| Cài Docker Desktop, `git clone`, giải nén gói dữ liệu Huy gửi, `docker compose up -d --build`, `./scripts/migrate.sh` | Mở được http://localhost:8088, đăng nhập `admin / admin123` |
| Đọc `README.md` mục 3–6 và `src/admin/products/` | Giải thích được form → PHP → MySQL → HTML |
| Tạo nhánh bằng `./scripts/start-work.sh feature/ten-viec` | Có nhánh riêng |

### H0. Huy chuẩn bị sẵn để không ai bị chặn (**tối 25/11**)

| ID | Việc | Ưu | Cỡ |
|---|---|---|---|
| H0 | Merge PR luật làm việc; viết sẵn 3 file migration riêng (tên có ngày) cho `banners`, `posts`, cột `orders.status`; thêm vào `ui.php` các component còn thiếu (select, phân trang, alert thành công, textarea); gắn menu header cho `products/`, `posts/`, admin `users/`, `banners/`, `posts/` | M | M |

### Huy (leader)

| ID | Việc | Tiêu chí hoàn thành | Ưu | Cỡ |
|---|---|---|---|---|
| X1 | `config/db.php` đọc host/user/pass từ biến môi trường, mặc định `localhost`/`root`/rỗng | Chạy cả Docker lẫn XAMPP, không sửa code | M | S |
| X2 | `docs/XAMPP.md` + `db/xampp-import.sql` (init + migrations + seed gộp 1 file) | Máy chỉ có XAMPP: copy `src/` vào `htdocs`, import 1 file SQL, chạy đủ chức năng. Chụp ảnh Apache + MySQL đang chạy cho slide | M | M |
| X3 | Test thật trên 1 máy có XAMPP (đường dẫn, quyền `uploads/`) | Đăng nhập, giỏ, admin chạy được | M | S |
| D1 | Free host (InfinityFree hoặc tương đương) | Có URL công khai chạy được. Ghi vào slide | M | M |
| A6 | Rà `admin/`: mọi file có `require_admin()`, xoá dùng POST, validate đầu vào | Checklist trong PR | M | S |
| P1 | Slide `BaoCao_LaptopShop.pptx`: demo theo từng câu rubric, đóng góp từng người | Mỗi người trình bày phần mình | M | M |
| P2 | Đóng gói zip theo mẫu đề: `BaoCao_LaptopShop.pptx` + thư mục `DoAn_LaptopShop/` (mã nguồn + SQL + ảnh), không có mật khẩu/`.env` thật, tên file đủ MSSV 4 người | Nộp CTE | M | S |

### Minh: giao diện khách (`src/products/`)

| ID | Việc | Tiêu chí hoàn thành | Ưu | Cỡ |
|---|---|---|---|---|
| S1 | `products/index.php`: danh sách có **tìm kiếm theo tên**, **lọc** hãng/khoảng giá/RAM/SSD, **sắp xếp** giá, **phân trang** | Lọc kết hợp được, URL giữ bộ lọc, dùng `prepare()`, có thông báo khi không có kết quả | M | L |
| S2 | `products/show.php?id=`: chi tiết (ảnh, giá, bảng thông số từ `product_specs`, nút thêm giỏ) | ID sai → trang báo không tìm thấy | M | L |
| S4 | Responsive mobile cho 2 trang trên | Không vỡ layout ở 375px | S | S |

### Phong: quản trị người dùng và đơn hàng

| ID | Việc | Tiêu chí hoàn thành | Ưu | Cỡ |
|---|---|---|---|---|
| A1 | `admin/users/`: danh sách, thêm, sửa vai trò, xoá | `password_hash`, không tự xoá/hạ quyền chính mình, có `require_admin()` | M | L |
| A2 | `admin/orders/`: xem chi tiết đơn + **đổi trạng thái** (mới → đang giao → hoàn tất → huỷ) | Dùng migration `orders.status` của Huy | M | M |
| A3 | `admin/index.php`: dashboard (doanh thu, số đơn theo trạng thái, số SP/DM/user) | Số liệu lấy từ DB | S | M |

### Phúc: quản trị nội dung website

| ID | Việc | Tiêu chí hoàn thành | Ưu | Cỡ |
|---|---|---|---|---|
| A4 | `admin/banners/`: CRUD banner trang chủ (ảnh, link, thứ tự, bật/tắt) | Đây là phần **"quản trị được nội dung website"** của rubric. Validate loại + dung lượng ảnh | M | L |
| A5 | `admin/posts/`: CRUD bài viết (tiêu đề, nội dung, ảnh) | Khách đọc được ở trang `posts/` (Minh làm trang đọc nếu Phúc kịp CRUD) | S | L |
| U3 | Validate giỏ/thanh toán: số lượng ≥1, form địa chỉ + SĐT | Không đặt được số lượng âm/0 | S | S |

**Đã cắt khỏi phạm vi** (làm sau nếu dư thời gian): trang tài khoản/đổi mật khẩu, huỷ đơn phía khách.

## 4. Lịch 3 ngày

| Khi nào | Mục tiêu | Xong khi |
|---|---|---|
| **Tối 25/11** | Z0 (3 bạn), H0 (Huy) | Mọi người chạy được web ở máy mình, có nhánh riêng |
| **26/11** | Sáng–trưa: S1, A1, A4 (phần CRUD cơ bản), X1, X2. **Chiều tối: PR đầu tiên của mỗi người phải mở** | Huy review + merge trong ngày. Sau mỗi lần merge, mọi người chạy `./scripts/sync-branch.sh` |
| **27/11** | S2, A2, A5, D1, X3, U3, A3, S4. Tối: **đóng băng tính năng** | Luồng chính chạy: đăng ký → xem/lọc → chi tiết → giỏ → đặt → admin đổi trạng thái; admin sửa banner |
| **28/11** | **Chốt.** Chỉ sửa lỗi. Test XAMPP + free host lần cuối, slide xong, đóng gói zip P2 | Mọi thứ done và nộp |

Việc nào không kịp thì giảm phạm vi, không kéo sang ngày 28. Ưu tiên theo cột **M**.

## 5. Definition of Done

- Chạy được trên localhost:8088, bấm thử đủ chức năng, không có PHP warning.
- SQL dùng `prepare()`, in HTML dùng `e()`, mật khẩu `password_hash`, trang admin có `require_admin()`.
- **Bắt buộc dùng bộ giao diện shadcn trong `ui.php`** (xem README mục 5), không màu Tailwind viết cứng. Copy từ `src/admin/products/` và `src/index.php`. CI kiểm tra.
- Trước khi mở PR đã chạy `./scripts/sync-branch.sh`; chỉ sửa file thuộc phần của mình; CI xanh; Huy duyệt rồi merge.
