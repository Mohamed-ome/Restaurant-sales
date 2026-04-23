-- Database Schema for Restaurant Management System (Al-Mantiqa)
-- Optimized for XAMPP (MySQL/MariaDB)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('ADMIN','MANAGER') NOT NULL DEFAULT 'MANAGER',
  `pin` varchar(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `pin` (`pin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `name`, `role`, `pin`) VALUES
('admin', 'مدير النظام', 'ADMIN', '1234'),
('manager', 'مشرف الصالة', 'MANAGER', '2222');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('FOOD','JUICE','OTHER') NOT NULL DEFAULT 'FOOD',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`name`, `type`) VALUES
('المأكولات', 'FOOD'),
('العصائر', 'JUICE');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_ar` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `ingredients` text DEFAULT NULL,
  `in_stock` int(11) NOT NULL DEFAULT 0,
  `min_threshold` int(11) NOT NULL DEFAULT 10,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`category_id`, `name`, `name_ar`, `price`, `ingredients`, `in_stock`, `min_threshold`, `image`) VALUES
(1, 'Margherita Pizza', 'بيتزا مارجريتا', 120.00, 'طماطم، موتزاريلا، ريحان', 45, 10, 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?q=80&w=1000&auto=format&fit=crop'),
(1, 'Chicken Burger', 'برجر دجاج', 85.00, 'دجاج، خس، صوص سري', 30, 5, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1000&auto=format&fit=crop'),
(1, 'Grilled Fish', 'سمك مشوي', 150.00, 'سمك طازج مشوي على الفحم بتتبيلة مميزة', 20, 5, 'https://images.unsplash.com/photo-1594002494803-510702672728?q=80&w=1000&auto=format&fit=crop'),
(1, 'Fried Fish', 'سمك مقلي', 140.00, 'سمك مقلي مقرمش مع أرز وصوص', 25, 5, 'https://images.unsplash.com/photo-1534604973900-c41ab4c5e636?q=80&w=1000&auto=format&fit=crop'),
(1, 'French Fries', 'فرايد', 40.00, 'أصابع بطاطس مقرمشة مملحة', 100, 20, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?q=80&w=1000&auto=format&fit=crop'),
(1, 'Mixed Grill', 'مشويات مشكلة', 200.00, 'تشكيلة من كباب، شيش طاووق، وريش مشوية', 15, 5, 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?q=80&w=1000&auto=format&fit=crop'),
(2, 'Mango Juice', 'عصير مانجو', 45.00, 'مانجو طازج، سكر', 12, 15, 'https://images.unsplash.com/photo-1553531384-cc64ac80f931?q=80&w=1000&auto=format&fit=crop'),
(2, 'Lemon Mint', 'ليمون نعناع', 35.00, 'ليمون، نعناع، ثلج', 80, 10, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?q=80&w=1000&auto=format&fit=crop');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('CASH','BANKAK') NOT NULL DEFAULT 'CASH',
  `dining_option` enum('TAKEAWAY','DINEIN') NOT NULL DEFAULT 'DINEIN',
  `notes` text DEFAULT NULL,
  `status` enum('COMPLETED','CANCELLED') NOT NULL DEFAULT 'COMPLETED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_time` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
