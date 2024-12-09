-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS ecommerce;

-- Use the database
USE ecommerce;

-- Create Users table
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Categories table
CREATE TABLE IF NOT EXISTS Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Create Laptops table
CREATE TABLE IF NOT EXISTS Laptops (
    laptop_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    brand VARCHAR(50) NOT NULL,
    processor VARCHAR(50) NOT NULL,
    RAM INT NOT NULL,
    storage VARCHAR(50) NOT NULL,
    graphics_card VARCHAR(100),
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    image_path VARCHAR(255) DEFAULT 'default.jpg',
    features TEXT,
    warranty VARCHAR(50) DEFAULT '1 year',
    category_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);

-- Create Orders table
CREATE TABLE IF NOT EXISTS Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create OrderDetails table
CREATE TABLE IF NOT EXISTS OrderDetails (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    laptop_id INT NOT NULL,
    quantity INT NOT NULL,
    price_per_unit DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (laptop_id) REFERENCES Laptops(laptop_id) ON DELETE CASCADE
);

-- Create Cart table
CREATE TABLE IF NOT EXISTS Cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    laptop_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (laptop_id) REFERENCES Laptops(laptop_id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO Categories (name) VALUES ('Gaming'), ('Business'), ('Budget'), ('High-Performance');

-- Insert sample laptops
INSERT INTO Laptops (name, description, brand, processor, RAM, storage, graphics_card, price, stock, image_path, features, warranty, category_id)
VALUES 
('Dell XPS 15', 
 'High-performance laptop with 11th Gen Intel i7 processor, ideal for business and creativity. Features a 4K touch display.',
 'Dell', 
 'Intel i7', 
 16, 
 '512GB SSD', 
 'NVIDIA GeForce GTX 1650', 
 1500.00, 
 10, 
 'images/dell_xps15.jpg', 
 '4K Display, Backlit Keyboard, Thunderbolt 4', 
 '2 years', 
 4),
('HP Spectre x360', 
 'Premium convertible laptop with touchscreen display, suitable for professional and personal use.',
 'HP', 
 'Intel i5', 
 8, 
 '256GB SSD', 
 NULL, 
 1200.00, 
 15, 
 'images/hp_spectre_x360.jpg', 
 '360-degree hinge, Touchscreen, Pen Support', 
 '1 year', 
 2),
('Asus ROG Zephyrus', 
 'Top-notch gaming laptop with high refresh rate and RGB lighting. Packed with the latest AMD Ryzen 7 processor.',
 'Asus', 
 'Ryzen 7', 
 16, 
 '1TB SSD', 
 'NVIDIA GeForce RTX 3060', 
 2000.00, 
 8, 
 'images/asus_rog_zephyrus.jpg', 
 '144Hz Display, RGB Keyboard, Dual Cooling', 
 '2 years', 
 1),
('Lenovo ThinkPad', 
 'Durable business laptop designed for professionals with a focus on productivity and security.',
 'Lenovo', 
 'Intel i5', 
 8, 
 '512GB SSD', 
 NULL, 
 1000.00, 
 20, 
 'images/lenovo_thinkpad.jpg', 
 'TrackPoint, Fingerprint Reader, Long Battery Life', 
 '3 years', 
 2),
('Acer Aspire 5', 
 'Budget-friendly laptop suitable for students and light users. Offers decent performance for everyday tasks.',
 'Acer', 
 'Intel i3', 
 4, 
 '256GB SSD', 
 NULL, 
 500.00, 
 30, 
 'images/acer_aspire_5.jpg', 
 'Lightweight Design, Full HD Display', 
 '1 year', 
 3),    
('Apple MacBook Pro 14-inch', 
 'Powerful MacBook Pro with M1 Pro chip, ideal for creative professionals and high-performance tasks.',
 'Apple', 
 'M1 Pro', 
 16, 
 '512GB SSD', 
 'Integrated Apple GPU', 
 2400.00, 
 5, 
 'images/macbook_pro_14.jpg', 
 'Liquid Retina XDR, Magic Keyboard, Touch ID', 
 '1 year', 
 4),

('Microsoft Surface Laptop 4', 
 'Premium ultrabook with a sleek design, ideal for productivity and multitasking.',
 'Microsoft', 
 'Intel i7', 
 16, 
 '512GB SSD', 
 'Integrated Intel Iris Xe', 
 1500.00, 
 7, 
 'images/surface_laptop_4.jpg', 
 'PixelSense Touchscreen, Silent Cooling', 
 '2 years', 
 2),

('Alienware m15 R6', 
 'High-end gaming laptop with top-tier performance and AlienFX RGB lighting.',
 'Alienware', 
 'Intel i9', 
 32, 
 '1TB SSD', 
 'NVIDIA GeForce RTX 3070', 
 3000.00, 
 4, 
 'images/alienware_m15.jpg', 
 '240Hz Display, Advanced Cooling, AlienFX RGB', 
 '3 years', 
 1),

('Razer Blade 15', 
 'Premium gaming laptop with stunning 4K OLED display and powerful graphics.',
 'Razer', 
 'Intel i7', 
 16, 
 '1TB SSD', 
 'NVIDIA GeForce RTX 3080', 
 2800.00, 
 6, 
 'images/razer_blade_15.jpg', 
 '4K OLED Touchscreen, RGB Keyboard, Ultra-Slim Design', 
 '1 year', 
 1),

('HP Pavilion 15', 
 'Reliable and affordable laptop for students and light users.',
 'HP', 
 'AMD Ryzen 5', 
 8, 
 '512GB SSD', 
 NULL, 
 750.00, 
 20, 
 'images/hp_pavilion_15.jpg', 
 'Full HD Display, Bang & Olufsen Audio', 
 '1 year', 
 3),

('Acer Predator Helios 300', 
 'Gaming powerhouse with excellent cooling and high refresh rate display.',
 'Acer', 
 'Intel i7', 
 16, 
 '512GB SSD', 
 'NVIDIA GeForce RTX 3060', 
 1500.00, 
 10, 
 'images/acer_predator_helios_300.jpg', 
 '144Hz Display, AeroBlade 3D Cooling', 
 '2 years', 
 1),

('Lenovo Yoga 9i', 
 '2-in-1 laptop with a 360-degree hinge, suitable for work and entertainment.',
 'Lenovo', 
 'Intel i5', 
 8, 
 '256GB SSD', 
 'Integrated Intel Iris Xe', 
 1200.00, 
 15, 
 'images/lenovo_yoga_9i.jpg', 
 '360-degree Hinge, Touchscreen, Active Pen Support', 
 '1 year', 
 2),

('Asus VivoBook 15', 
 'Budget-friendly laptop with decent performance for daily tasks.',
 'Asus', 
 'Intel i3', 
 8, 
 '1TB HDD', 
 NULL, 
 600.00, 
 25, 
 'images/asus_vivobook_15.jpg', 
 'NanoEdge Display, Compact Design', 
 '1 year', 
 3),

('Dell Inspiron 15 3000', 
 'Affordable and reliable laptop for basic productivity and entertainment.',
 'Dell', 
 'AMD Ryzen 3', 
 4, 
 '256GB SSD', 
 NULL, 
 550.00, 
 30, 
 'images/dell_inspiron_15.jpg', 
 'Full HD Display, Long Battery Life', 
 '1 year', 
 3),

('Gigabyte AERO 15 OLED', 
 'High-performance laptop for creators with stunning OLED display.',
 'Gigabyte', 
 'Intel i9', 
 32, 
 '1TB SSD', 
 'NVIDIA GeForce RTX 3070', 
 2500.00, 
 3, 
 'images/gigabyte_aero_15.jpg', 
 '4K OLED, Studio-Ready Performance, RGB Fusion', 
 '2 years', 
 4);
