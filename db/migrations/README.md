# Migration: cách thay đổi cấu trúc DB

**Không sửa `db/init.sql`** (chỉ dùng để tạo DB mới lần đầu). Muốn thêm bảng/cột: tạo **file mới** ở đây,
đặt tên `YYYY-MM-DD-ten-viec.sql`, ví dụ `2026-11-03-them-bang-reviews.sql`:

```sql
CREATE TABLE reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  rating TINYINT NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

Áp dụng trên máy mình: `./scripts/migrate.sh` (chạy lại nhiều lần được, file đã áp thì bỏ qua).
Sau mỗi lần `git pull` thấy có file migration mới thì chạy lại lệnh đó.
Trên web thật (VPS), `deploy.sh` tự áp sau khi merge vào `main`.

Mỗi người một file khác tên nên không bao giờ conflict với nhau. Đã áp rồi thì **không sửa lại file cũ**, cần đổi thì tạo file mới.
