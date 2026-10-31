-- Báo cho MySQL biết file này là UTF-8 (thiếu dòng này tiếng Việt sẽ bị lỗi font trong DB)
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  description TEXT,
  image VARCHAR(500),
  category_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  -- Phần mở rộng (nullable để form admin cũ vẫn thêm/sửa được sản phẩm chỉ với các cột trên)
  slug VARCHAR(255) NULL UNIQUE,
  sku VARCHAR(100) NULL UNIQUE,
  brand VARCHAR(50) NULL,
  old_price INT NULL,              -- giá gốc, có thì hiện giá gạch ngang
  article MEDIUMTEXT NULL,         -- bài viết giới thiệu (HTML đã làm sạch)
  source_url VARCHAR(500) NULL,    -- nguồn dữ liệu (ghi nguồn khi hiển thị)

  -- Các thuộc tính dạng số/gọn để LỌC và SẮP XẾP (bảng đầy đủ nằm ở product_specs)
  cpu VARCHAR(255) NULL,
  cpu_brand VARCHAR(20) NULL,
  cpu_line VARCHAR(50) NULL,
  ram_gb SMALLINT NULL,
  ram_type VARCHAR(30) NULL,
  ssd_gb SMALLINT UNSIGNED NULL,
  gpu VARCHAR(255) NULL,
  gpu_brand VARCHAR(20) NULL,
  vram_gb SMALLINT NULL,
  screen_inch DECIMAL(3,1) NULL,
  refresh_hz SMALLINT NULL,
  resolution VARCHAR(100) NULL,
  os VARCHAR(100) NULL,
  weight_kg DECIMAL(4,2) NULL,
  battery_wh DECIMAL(5,1) NULL,
  warranty_months TINYINT NULL,
  use_case VARCHAR(255) NULL,      -- nhu cầu: "Gaming, Đồ họa..." (không đặt tên usage vì là từ khoá MySQL)

  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Bảng thông số kỹ thuật đầy đủ: mỗi dòng 1 thông số, nhóm theo group_name
CREATE TABLE IF NOT EXISTS product_specs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  group_name VARCHAR(50) NOT NULL,
  label VARCHAR(100) NOT NULL,
  value TEXT NOT NULL,
  sort_order SMALLINT NOT NULL DEFAULT 0,
  INDEX (product_id, sort_order),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(100) NULL, -- họ tên hiển thị
  password VARCHAR(255) NOT NULL, -- lưu bằng password_hash()
  role ENUM('user','admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  total INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- name/price lưu lại giá trị lúc mua nên không dùng khoá ngoại tới products
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  qty INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO categories (name) VALUES ('Laptop văn phòng'), ('Laptop gaming');

-- Sản phẩm thật nằm trong db/seed-products.sql (do scripts/build_seed_sql.py sinh ra)

-- Tài khoản test: admin / admin123  và  user / user123
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$12$PFFb3ERjHWHJLbsp50g7wOio.tV/qCS/xiOIX53nN8Las78Z1ggGO', 'admin'),
('user',  '$2y$12$vHIVYeIugGJJXaRFr3vkPOcFYhmDnozIXysUJDcTe1YqKYDkzoph2', 'user');
