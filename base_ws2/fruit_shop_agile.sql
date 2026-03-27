
-- DATABASE: fruit_shop
CREATE DATABASE IF NOT EXISTS fruit_shop;
USE fruit_shop;

-- 1. Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, description) VALUES
('Táo', 'Các loại táo nhập khẩu'),
('Cam', 'Các loại cam tươi'),
('Nho', 'Nho Mỹ, Úc');

-- 2. Products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    sale_price INT DEFAULT NULL,
    image VARCHAR(255),
    quantity INT DEFAULT 0,
    description TEXT,
    category_id INT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO products (name, price, sale_price, image, quantity, description, category_id) VALUES
('Táo Mỹ', 50000, 45000, 'uploads/taomy.jpg', 100, 'Táo nhập khẩu từ Mỹ', 1),
('Cam Sành', 30000, NULL, 'uploads/camsanh.jpg', 150, 'Cam tươi ngon', 2),
('Nho Đen', 80000, 75000, 'uploads/nhoden.jpg', 80, 'Nho đen không hạt', 3);

-- 3. Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    role TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email, password, phone, address, role) VALUES
('Admin', 'admin@gmail.com', '123456', '0123456789', 'Hà Nội', 1),
('User A', 'user@gmail.com', '123456', '0987654321', 'Hà Nội', 0);

-- 4. Orders
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price INT,
    status VARCHAR(50) DEFAULT 'pending',
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO orders (user_id, total_price, status, note) VALUES
(2, 95000, 'pending', 'Giao buổi sáng');

-- 5. Order Items
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    price INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO order_items (order_id, product_id, price, quantity) VALUES
(1, 1, 45000, 1),
(1, 2, 30000, 1);
