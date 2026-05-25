-- SQL Script for Mie Ayam Wengi 57 Database Setup
-- Compatible with MySQL and phpMyAdmin

CREATE DATABASE IF NOT EXISTS `mie_ayam_wengi_57`;
USE `mie_ayam_wengi_57`;

-- 1. Table: admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `category` VARCHAR(50) NOT NULL, -- 'Mie Ayam', 'Sampingan', 'Minuman'
  `image_path` VARCHAR(255) NOT NULL,
  `is_available` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `table_number` VARCHAR(20) NOT NULL,
  `phone_number` VARCHAR(20) NOT NULL,
  `notes` TEXT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Pending', 'Cooking', 'Completed', 'Cancelled') DEFAULT 'Pending',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Tunai',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `menu_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Default Admin: username 'admin', password 'wengi57'
-- Generated using password_hash('wengi57', PASSWORD_BCRYPT)
INSERT INTO `admins` (`id`, `username`, `password`, `full_name`) 
VALUES (1, 'admin', '$2y$10$FcWm2kVM2U/mdvvE.9OdYeDSFuJ2FUvR6SPBHOf5EHEeIJdDZS9ny', 'Owner Mie Ayam Wengi 57')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Seed Menus
INSERT INTO `menus` (`id`, `name`, `description`, `price`, `category`, `image_path`, `is_available`) VALUES
(1, 'Mie Ayam Biasa Wengi', 'Mie kenyal gurih disajikan dengan potongan ayam kecap melimpah, caisim segar, kuah kaldu spesial, dan taburan daun bawang serta bawang goreng.', 13000.00, 'Mie Ayam', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop&q=80', 1),
(2, 'Mie Ayam Bakso Spesial', 'Mie Ayam legendaris Wengi ditambah dengan 3 butir bakso sapi urat jumbo yang kenyal dan berdaging, lengkap dengan kuah kaldu panas terpisah.', 18000.00, 'Mie Ayam', 'https://images.unsplash.com/photo-1552611052-33e04de081de?w=500&auto=format&fit=crop&q=80', 1),
(3, 'Mie Ayam Pangsit Basah', 'Kombinasi klasik mie ayam dengan 3 buah pangsit basah homemade berkuah kaldu, berisi olahan ayam cincang berbumbu gurih.', 17000.00, 'Mie Ayam', 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=500&auto=format&fit=crop&q=80', 1),
(4, 'Mie Ayam Komplit Wengi', 'Sensasi kuliner malam terlengkap! Mie ayam porsi mantap disajikan dengan 2 butir bakso sapi dan 2 buah pangsit basah hangat.', 22000.00, 'Mie Ayam', 'https://images.unsplash.com/photo-1585032226651-759b368d7246?w=500&auto=format&fit=crop&q=80', 1),
(5, 'Pangsit Goreng Crispy (5 Pcs)', 'Pangsit goreng renyah isi daging ayam cincang gurih, disajikan hangat dengan saus asam manis pedas yang segar.', 10000.00, 'Sampingan', 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=500&auto=format&fit=crop&q=80', 1),
(6, 'Bakso Goreng Mekar (3 Pcs)', 'Bakso goreng mekar bertekstur garing di luar namun sangat lembut dan kenyal di dalam, bercita rasa gurih gurih asin.', 12000.00, 'Sampingan', 'https://images.unsplash.com/photo-1541532713592-79a0317b6b77?w=500&auto=format&fit=crop&q=80', 1),
(7, 'Es Teh Manis Wengi', 'Es teh manis segar pelepas dahaga malam hari, diseduh dari daun teh pilihan beraroma melati khas Jawa Tengah.', 4000.00, 'Minuman', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&auto=format&fit=crop&q=80', 1),
(8, 'Es Jeruk Peras Segar', 'Es jeruk peras asli dari jeruk peras pilihan yang kaya akan vitamin C, memberikan perpaduan rasa asam manis yang seimbang.', 6000.00, 'Minuman', 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500&auto=format&fit=crop&q=80', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;
