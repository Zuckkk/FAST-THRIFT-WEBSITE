CREATE DATABASE IF NOT EXISTS fast_thrift;
USE fast_thrift;

-- Drop existing tables in correct order (child tables first)
DROP TABLE IF EXISTS user_promo_usage;
DROP TABLE IF EXISTS user_promos;
DROP TABLE IF EXISTS orders;    
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS promo_codes;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS users;

-- Create users table first (parent table)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE products (
    id VARCHAR(64) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create promo_codes table
CREATE TABLE promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    type ENUM('discount', 'shipping') NOT NULL,
    value DECIMAL(5,2),
    max_uses INT DEFAULT 1,
    active BOOLEAN DEFAULT TRUE,
    valid_until DATE,
    min_purchase DECIMAL(10,2) DEFAULT 0
);

-- Create orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    order_data TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    shipping_fee DECIMAL(10,2) DEFAULT 100,
    address VARCHAR(500) NOT NULL,
    promo_code VARCHAR(50) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create user_promos table (for tracking promo usage)
CREATE TABLE user_promos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    promo_code_id INT NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id)
);

-- Create user_promo_usage table (for simpler promo tracking)
CREATE TABLE user_promo_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255),
    promo_code VARCHAR(50),
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_promo (user_email, promo_code)
);

-- Insert sample users
INSERT INTO users (email, password, first_name, last_name, is_admin) VALUES
('admin@gmail.com', '12345', 'Admin', 'User', 1),
('zach@gmail.com', '123', 'Zach', 'Smith', 0);

-- Insert sample admin
INSERT INTO admins (email, password) VALUES
('admin@gmail.com', '321');

-- Insert sample promo codes
INSERT INTO promo_codes (code, type, value, valid_until, min_purchase, active) VALUES
('WELCOME10', 'discount', 10.00, '2024-12-31', 0.00, true),
('WELCOME20', 'discount', 20.00, '2024-12-31', 0.00, true),
('WELCOME30', 'discount', 30.00, '2024-12-31', 0.00, true),
('WELCOME40', 'discount', 40.00, '2024-12-31', 0.00, true),
('WELCOME50', 'discount', 50.00, '2024-12-31', 0.00, true),
('SUMMER25', 'discount', 25.00, '2024-12-31', 0.00, true),
('FREESHIP', 'shipping', 0.00, '2024-12-31', 0.00, true),
('THRIFTY15', 'discount', 15.00, '2024-12-31', 0.00, true),
('SAVE35', 'discount', 35.00, '2024-12-31', 0.00, true),
('KAEYLEPOGI', 'discount', 100.00, '2024-12-31', 0.00, true)
ON DUPLICATE KEY UPDATE
active = VALUES(active),
valid_until = VALUES(valid_until);

-- Insert sample products
INSERT INTO products (id, title, price, image, quantity) VALUES
('diesel-jeans', 'Diesel Jeans - 30W UK 6 Blue Cotton', 3300.00, 'PRODUCTS/DieselJeans.jpg', 5),
('harley-davidson-sweatshirt', 'Harley Davidson Sweatshirt - Large Black Cotton Blend', 2900.00, 'PRODUCTS/HarleyDavidsonSweatshirt.jpg', 3),
('riley-true-religion', 'Riley True Religion Denim Shorts - 28W UK 8 Blue Cotton', 2700.00, 'PRODUCTS/RileyTrueReligionDenim.jpg', 2),
('joey-big-t-denim-shorts', 'Made in USA Joey Big T True Religion Denim Shorts - 32W UK 10 Blue Cotton', 2700.00, 'PRODUCTS/MadeinUSAJoeyBig1.jpg', 1),
('3-suisses-top', '3 Suisses Long Sleeve Top - XS Pink Viscose Blend', 500.00, 'PRODUCTS/3SuissesLongSleeveTop1.jpg', 4),
('levis-denim-shorts', '311 Levis Denim Shorts - 31W UK 12 Blue Cotton', 900.00, 'PRODUCTS/311LevisDenimShorts1.jpg', 2),
('501-levis-jeans', '501 Levis Jeans - 28W UK 8 Blue Cotton', 900.00, 'PRODUCTS/501LevisJeans.jpg', 2),
('bedford-harley-davidson', 'Bedford Heights Ohio Harley Davidson Top - Medium Pink Cotton', 600.00, 'PRODUCTS/Bedford.jpg', 1),
('gastonia-harley-davidson', 'Gastonia NC Harley Davidson Top - XL White Cotton', 600.00, 'PRODUCTS/Gastonia.jpg', 1),
('divided-cropped-top', 'Divided Cropped Top - Small Pink Polyester Blend', 200.00, 'PRODUCTS/Divided1.jpg', 3),
('supreme-graphic-hoodie', 'Supreme Graphic Hoodie - XL Black Cotton', 33100.00, 'PRODUCTS/supreme.png', 1),
('true-religion-shorts', 'True Religion Contrast Stitch Shorts - 34W 13L Acid Wash Cotton', 3500.00, 'PRODUCTS/TrueReligionContrast1.jpg', 1),
('washington-nike-windbreaker', 'Washington Nike Windbreaker', 1070.00, 'PRODUCTS/washington1.jpg', 1),
('nike-sweatshirt-white', 'Nike Sweatshirt - 2XL White Cotton Blend', 3700.00, 'PRODUCTS/nikesweat1.jpg', 1),
('nike-sweatshirt-brown', 'Made In Usa Nike Sweatshirt - Small Brown Cotton', 3700.00, 'PRODUCTS/SweatshirtSmallBrownCotton1.jpg', 1),
('carhartt-jacket', 'Carhartt Jacket - 2XL Beige Cotton', 12600.00, 'PRODUCTS/CarharttJacket1.jpg', 1),
('supreme-2018-hoodie', 'Supreme 2018 Supreme Hoodie - Medium Red Cotton', 10700.00, 'PRODUCTS/Supreme2018Supreme.jpg', 1),
('puma-track-jacket', 'Puma Track Jacket - 2XL Blue Polyester', 2700.00, 'PRODUCTS/PumaTrackJacket1.jpg', 1),
('versace-blazer', 'Versace Classic Blazer - Large Brown Wool Blend', 9600.00, 'PRODUCTS/VersaceClassicBlazer1.jpg', 1),
('carhartt-shorts', 'Carhartt Carpenter Shorts - 33W 8L Brown Cotton', 3300.00, 'PRODUCTS/CarharttCarpenterShorts1.jpg', 1);