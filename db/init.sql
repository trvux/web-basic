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
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
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

INSERT INTO categories (name) VALUES ('Laptop văn phòng'), ('Laptop gaming'), ('Phụ kiện');

INSERT INTO products (name, price, stock, description, image, category_id) VALUES
('Dell Inspiron 15', 15990000, 10, 'i5, 16GB RAM, 512GB SSD', 'https://placehold.co/300x300?text=Dell+Inspiron', 1),
('HP Pavilion 14', 14500000, 8, 'i5, 8GB RAM, 512GB SSD', 'https://placehold.co/300x300?text=HP+Pavilion', 1),
('Lenovo ThinkPad E14', 19900000, 6, 'i7, 16GB RAM, 512GB SSD', 'https://placehold.co/300x300?text=ThinkPad', 1),
('MacBook Air M2', 24990000, 4, 'M2, 8GB RAM, 256GB SSD', 'https://placehold.co/300x300?text=MacBook+Air', 1),
('ASUS TUF Gaming F15', 22990000, 5, 'i7, RTX 4050, 16GB RAM', 'https://placehold.co/300x300?text=ASUS+TUF', 2),
('Acer Nitro 5', 20990000, 7, 'i5, RTX 3050, 16GB RAM', 'https://placehold.co/300x300?text=Acer+Nitro', 2),
('MSI Katana 15', 27990000, 3, 'i7, RTX 4060, 16GB RAM', 'https://placehold.co/300x300?text=MSI+Katana', 2),
('Chuột Logitech M331', 250000, 40, 'Chuột không dây im lặng', 'https://placehold.co/300x300?text=Chuot', 3),
('Bàn phím cơ Keychron K2', 1890000, 15, 'Bluetooth, switch brown', 'https://placehold.co/300x300?text=Ban+phim', 3),
('Balo laptop 15.6 inch', 350000, 25, 'Chống nước', 'https://placehold.co/300x300?text=Balo', 3);

-- Tài khoản test: admin / admin123  và  user / user123
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$12$PFFb3ERjHWHJLbsp50g7wOio.tV/qCS/xiOIX53nN8Las78Z1ggGO', 'admin'),
('user',  '$2y$12$vHIVYeIugGJJXaRFr3vkPOcFYhmDnozIXysUJDcTe1YqKYDkzoph2', 'user');
