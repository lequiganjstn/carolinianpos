CREATE DATABASE IF NOT EXISTS carolinianpos
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE carolinianpos;

DROP TABLE IF EXISTS stock_adjustments;
DROP TABLE IF EXISTS transaction_items;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  role ENUM('Cashier','Manager','Admin') NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  sku VARCHAR(80) NOT NULL UNIQUE,
  category VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock_qty INT NOT NULL DEFAULT 0,
  low_stock_threshold INT NOT NULL DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cashier_id INT UNSIGNED NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  amount_paid DECIMAL(10,2) NOT NULL,
  change_amount DECIMAL(10,2) NOT NULL,
  status ENUM('completed','voided') NOT NULL DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cashier_id) REFERENCES users(id)
);

CREATE TABLE transaction_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transaction_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE stock_adjustments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  adjusted_by INT UNSIGNED NOT NULL,
  quantity_change INT NOT NULL,
  reason VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (adjusted_by) REFERENCES users(id)
);

INSERT INTO users (username,password_hash,full_name,role) VALUES
('admin', '$2y$10$5OTuIwzgqUxoZGnI5T4AgOGiJP5CR7QqCIMgmqY/c/C5z37OtbHDK', 'System Administrator', 'Admin'),
('manager', '$2y$10$5OTuIwzgqUxoZGnI5T4AgOGiJP5CR7QqCIMgmqY/c/C5z37OtbHDK', 'Store Manager', 'Manager'),
('cashier', '$2y$10$5OTuIwzgqUxoZGnI5T4AgOGiJP5CR7QqCIMgmqY/c/C5z37OtbHDK', 'Campus Cashier', 'Cashier');

INSERT INTO products (name,sku,category,price,stock_qty,low_stock_threshold) VALUES
('Carolinian T-Shirt','CUSC-TS-001','Merchandise',450.00,30,5),
('University Hoodie','CUSC-HD-001','Merchandise',950.00,12,5),
('USC Notebook','CUSC-NB-001','School Supplies',85.00,45,10),
('Blue Ballpen','CUSC-BP-001','School Supplies',25.00,100,20),
('Campus ID Lace','CUSC-LC-001','Accessories',75.00,8,5),
('Water Bottle','CUSC-WB-001','Accessories',250.00,18,5);
