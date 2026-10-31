-- Nâng cấp DB ĐÃ CHẠY (có volume) lên schema laptop mới, giữ nguyên users/orders.
-- Chỉ dùng cho DB cũ: DB tạo mới đã có sẵn schema này từ db/init.sql.
-- Chạy:  mysql --default-character-set=utf8mb4 -uroot -p shop < db/migrations/001-laptop-schema.sql
SET NAMES utf8mb4;

ALTER TABLE products
  ADD COLUMN slug VARCHAR(255) NULL UNIQUE,
  ADD COLUMN sku VARCHAR(100) NULL UNIQUE,
  ADD COLUMN brand VARCHAR(50) NULL,
  ADD COLUMN old_price INT NULL,
  ADD COLUMN article MEDIUMTEXT NULL,
  ADD COLUMN source_url VARCHAR(500) NULL,
  ADD COLUMN cpu VARCHAR(255) NULL,
  ADD COLUMN cpu_brand VARCHAR(20) NULL,
  ADD COLUMN cpu_line VARCHAR(50) NULL,
  ADD COLUMN ram_gb SMALLINT NULL,
  ADD COLUMN ram_type VARCHAR(30) NULL,
  ADD COLUMN ssd_gb SMALLINT UNSIGNED NULL,
  ADD COLUMN gpu VARCHAR(255) NULL,
  ADD COLUMN gpu_brand VARCHAR(20) NULL,
  ADD COLUMN vram_gb SMALLINT NULL,
  ADD COLUMN screen_inch DECIMAL(3,1) NULL,
  ADD COLUMN refresh_hz SMALLINT NULL,
  ADD COLUMN resolution VARCHAR(100) NULL,
  ADD COLUMN os VARCHAR(100) NULL,
  ADD COLUMN weight_kg DECIMAL(4,2) NULL,
  ADD COLUMN battery_wh DECIMAL(5,1) NULL,
  ADD COLUMN warranty_months TINYINT NULL,
  ADD COLUMN use_case VARCHAR(255) NULL;

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
