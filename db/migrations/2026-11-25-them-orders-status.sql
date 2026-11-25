-- Trạng thái đơn hàng (Phong làm trang đổi trạng thái ở admin/orders/)
-- new = mới đặt, shipping = đang giao, done = hoàn tất, cancelled = đã huỷ
ALTER TABLE orders
  ADD COLUMN status ENUM('new','shipping','done','cancelled') NOT NULL DEFAULT 'new' AFTER total;
