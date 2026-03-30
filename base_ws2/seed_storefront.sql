USE shop_db_final;
START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1) BỔ SUNG CỘT CHO CATEGORIES
-- =====================================================
SET @db := DATABASE();

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'slug') = 0,
  'ALTER TABLE categories ADD COLUMN slug VARCHAR(120) NULL AFTER name',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'is_active') = 0,
  'ALTER TABLE categories ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER description',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'sort_order') = 0,
  'ALTER TABLE categories ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER is_active',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT COUNT(*) FROM information_schema.COLUMNS
   WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'created_at') = 0,
  'ALTER TABLE categories ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER sort_order',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 2) BỔ SUNG CỘT CHO PRODUCTS
-- =====================================================
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'sku') = 0,
  'ALTER TABLE products ADD COLUMN sku VARCHAR(50) NULL AFTER product_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'slug') = 0,
  'ALTER TABLE products ADD COLUMN slug VARCHAR(170) NULL AFTER name', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'sale_price') = 0,
  'ALTER TABLE products ADD COLUMN sale_price DECIMAL(10,2) NULL AFTER price', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'origin') = 0,
  'ALTER TABLE products ADD COLUMN origin VARCHAR(120) NULL AFTER unit', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'weight_gram') = 0,
  'ALTER TABLE products ADD COLUMN weight_gram INT NULL AFTER origin', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'is_featured') = 0,
  'ALTER TABLE products ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER weight_gram', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'is_new') = 0,
  'ALTER TABLE products ADD COLUMN is_new TINYINT(1) NOT NULL DEFAULT 0 AFTER is_featured', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'status') = 0,
  'ALTER TABLE products ADD COLUMN status ENUM(''active'',''inactive'',''out_of_stock'') NOT NULL DEFAULT ''active'' AFTER is_new', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'created_at') = 0,
  'ALTER TABLE products ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER status', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'products' AND COLUMN_NAME = 'updated_at') = 0,
  'ALTER TABLE products ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 3) BỔ SUNG CỘT CHO ORDERS / PAYMENTS
-- =====================================================
SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_fullname') = 0,
  'ALTER TABLE orders ADD COLUMN shipping_fullname VARCHAR(120) NULL AFTER customer_id', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_phone') = 0,
  'ALTER TABLE orders ADD COLUMN shipping_phone VARCHAR(20) NULL AFTER shipping_fullname', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_address') = 0,
  'ALTER TABLE orders ADD COLUMN shipping_address TEXT NULL AFTER shipping_phone', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'note') = 0,
  'ALTER TABLE orders ADD COLUMN note TEXT NULL AFTER shipping_address', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method') = 0,
  'ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) NULL AFTER note', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipping_fee') = 0,
  'ALTER TABLE orders ADD COLUMN shipping_fee DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER payment_method', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'transaction_code') = 0,
  'ALTER TABLE payments ADD COLUMN transaction_code VARCHAR(100) NULL AFTER method', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'payments' AND COLUMN_NAME = 'amount') = 0,
  'ALTER TABLE payments ADD COLUMN amount DECIMAL(10,2) NULL AFTER transaction_code', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- =====================================================
-- 4) XÓA DỮ LIỆU KHÔNG LIÊN QUAN (ĐIỆN THOẠI/LAPTOP)
-- =====================================================
DELETE od
FROM order_details od
JOIN products p ON p.product_id = od.product_id
JOIN categories c ON c.category_id = p.category_id
WHERE c.name IN ('Điện thoại', 'Laptop');

DELETE pm
FROM payments pm
LEFT JOIN order_details od ON od.order_id = pm.order_id
WHERE od.order_id IS NULL;

DELETE o
FROM orders o
LEFT JOIN order_details od ON od.order_id = o.order_id
WHERE od.order_id IS NULL;

DELETE p
FROM products p
JOIN categories c ON c.category_id = p.category_id
WHERE c.name IN ('Điện thoại', 'Laptop');

DELETE FROM categories WHERE name IN ('Điện thoại', 'Laptop');

-- =====================================================
-- 5) CHUẨN HÓA DANH MỤC HOA QUẢ
-- =====================================================
INSERT INTO categories (name, slug, description, is_active, sort_order)
VALUES
('Trái cây nội địa', 'trai-cay-noi-dia', 'Hoa quả Việt Nam theo mùa', 1, 1),
('Trái cây nhập khẩu', 'trai-cay-nhap-khau', 'Hoa quả cao cấp nhập khẩu', 1, 2),
('Nước ép & combo', 'nuoc-ep-combo', 'Combo quà tặng và nước ép', 1, 3)
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  slug = VALUES(slug),
  is_active = 1;

-- =====================================================
-- 6) CHUẨN HÓA SẢN PHẨM HOA QUẢ MẪU
-- =====================================================
SET @cat_noidia = (SELECT category_id FROM categories WHERE slug = 'trai-cay-noi-dia' LIMIT 1);
SET @cat_nhapkhau = (SELECT category_id FROM categories WHERE slug = 'trai-cay-nhap-khau' LIMIT 1);
SET @cat_combo = (SELECT category_id FROM categories WHERE slug = 'nuoc-ep-combo' LIMIT 1);

INSERT INTO products
(sku, name, slug, price, sale_price, stock, unit, origin, weight_gram, description, image, category_id, is_featured, is_new, status)
VALUES
('FRUIT-XOAI-001', 'Xoài Cát Hòa Lộc', 'xoai-cat-hoa-loc', 85000, 79000, 120, 'kg', 'Tiền Giang, Việt Nam', 1000, 'Ngọt đậm, thơm, thịt dày', 'xoai-cat.jpg', @cat_noidia, 1, 1, 'active'),
('FRUIT-BUOI-001', 'Bưởi Da Xanh', 'buoi-da-xanh', 65000, NULL, 80, 'quả', 'Bến Tre, Việt Nam', 1200, 'Múi mọng, vị thanh', 'buoi-da-xanh.jpg', @cat_noidia, 1, 0, 'active'),
('FRUIT-NHO-001', 'Nho đen không hạt Mỹ', 'nho-den-khong-hat-my', 220000, 199000, 40, 'kg', 'Mỹ', 500, 'Giòn ngọt, không hạt', 'nho-den-my.jpg', @cat_nhapkhau, 1, 0, 'active'),
('FRUIT-CHERRY-001', 'Cherry New Zealand', 'cherry-new-zealand', 480000, NULL, 17, 'kg', 'New Zealand', 500, 'Quả to, ngọt giòn', 'cherry-nz.jpg', @cat_nhapkhau, 1, 1, 'active'),
('FRUIT-COMBO-001', 'Combo trái cây 5 món', 'combo-trai-cay-5-mon', 399000, 369000, 25, 'hộp', 'Việt Nam', NULL, 'Set quà tặng mix 5 loại', 'combo-5-mon.jpg', @cat_combo, 1, 0, 'active')
ON DUPLICATE KEY UPDATE
  price = VALUES(price),
  sale_price = VALUES(sale_price),
  stock = VALUES(stock),
  description = VALUES(description),
  image = VALUES(image),
  status = VALUES(status);

-- =====================================================
-- 7) SỬA PASSWORD ADMIN (ĐỂ LOGIN ĐƯỢC)
-- =====================================================
UPDATE users
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.ogxj9h1p6u0QW7C.'
WHERE email = 'admin@gmail.com' AND (password = '1' OR password IS NULL OR password = '');

-- =====================================================
-- 8) CHUẨN HÓA STATUS CÓ DẤU
-- =====================================================
UPDATE orders
SET status = 'Đang xử lý'
WHERE status IN ('Dang xu ly', 'Đang xử lý');

UPDATE payments
SET status = 'Chưa thanh toán'
WHERE status IN ('Chua thanh toan', 'Chưa thanh toán');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
