-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 02:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hiddeneats`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `food_id` int(11) NOT NULL,
  `foodName` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `food_category` varchar(255) NOT NULL DEFAULT 'General'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`food_id`, `foodName`, `price`, `image_url`, `food_category`) VALUES
(18, 'Spam Rice', 89, 'IMGS/spam_rice.png', 'All-day Breakfasts'),
(19, 'Sisig Rice', 95, 'IMGS/sisig_rice.png', 'Rice Meals'),
(20, 'Siomai', 60, 'IMGS/siomai.png', 'Add-ons'),
(21, 'Pork Lumpia', 65, 'IMGS/pork_lumpia.png', 'Add-ons'),
(22, 'New York Hotdog', 75, 'IMGS/newyork_hotdog.png', 'Specials'),
(23, 'Hungarian Rice', 99, 'IMGS/hungarian_rice.png', 'Rice Meals'),
(24, 'Grilled Teriyaki', 110, 'IMGS/grilled_teriyaki.png', 'Rice Meals'),
(25, 'Extra Rice', 20, 'IMGS/extra_rice.png', 'Add-ons'),
(26, 'Extra Egg', 15, 'IMGS/extra_egg.png', 'Add-ons'),
(27, 'Chorizo Rice', 85, 'IMGS/chorizo_rice.png', 'All-day Breakfasts'),
(28, 'Chicken Katsu Rice', 109, 'IMGS/chicken_katsu_rice.png', 'Rice Meals'),
(29, 'Cheese Burger Deluxe', 119, 'IMGS/cheese_burger_deluxe.png', 'Specials'),
(30, 'Burger Steak', 99, 'IMGS/burger_steak.png', 'Rice Meals'),
(31, 'Beef Shawarma Rice', 115, 'IMGS/beef_shawarma_rice.png', 'Rice Meals'),
(32, 'Beef Pares', 99, 'IMGS/beef_pares.png', 'Rice Meals');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `order_items` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_type` varchar(20) NOT NULL,
  `order_status` varchar(20) DEFAULT 'Pending',
  `payment_status` varchar(20) DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'Cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_name`, `order_items`, `total_price`, `order_type`, `order_status`, `payment_status`, `created_at`, `payment_method`) VALUES
(3, 'Carlos', '[{\"id\":1,\"name\":\"Spam Rice\",\"qty\":2,\"price\":89}]', 178.00, 'Dine In', 'Completed', 'Paid', '2025-07-15 15:05:18', 'Cash'),
(4, 'Anna', '[{\"id\":3,\"name\":\"Siomai\",\"qty\":3,\"price\":60}, {\"id\":8,\"name\":\"Extra Rice\",\"qty\":1,\"price\":20}]', 200.00, 'Take Out', 'Completed', 'Paid', '2025-07-14 15:05:18', 'GCash'),
(5, 'James', '[{\"id\":14,\"name\":\"Beef Shawarma Rice\",\"qty\":1,\"price\":115}]', 115.00, 'Dine In', 'Pending', 'Unpaid', '2025-07-11 15:05:18', 'Cash'),
(6, 'Sophia', '[{\"id\":13,\"name\":\"Burger Steak\",\"qty\":1,\"price\":99}, {\"id\":9,\"name\":\"Extra Egg\",\"qty\":2,\"price\":15}]', 129.00, 'Take Out', 'Completed', 'Paid', '2025-07-12 15:05:18', 'GCash'),
(7, 'Liam', '[{\"id\":11,\"name\":\"Chicken Katsu Rice\",\"qty\":2,\"price\":109}]', 218.00, 'Dine In', 'Completed', 'Paid', '2025-07-15 15:05:18', 'Cash'),
(8, 'Olivia', '[{\"id\":6,\"name\":\"Hungarian Rice\",\"qty\":1,\"price\":99}, {\"id\":2,\"name\":\"Sisig Rice\",\"qty\":1,\"price\":95}]', 194.00, 'Take Out', 'Completed', 'Paid', '2025-07-10 15:05:18', 'GCash'),
(9, 'Noah', '[{\"id\":15,\"name\":\"Beef Pares\",\"qty\":1,\"price\":99}]', 99.00, 'Dine In', 'Completed', 'Paid', '2025-07-13 15:05:18', 'Cash'),
(10, 'Emma', '[{\"id\":10,\"name\":\"Chorizo Rice\",\"qty\":1,\"price\":85}, {\"id\":4,\"name\":\"Pork Lumpia\",\"qty\":1,\"price\":65}]', 150.00, 'Take Out', 'Completed', 'Paid', '2025-07-14 15:05:18', 'GCash'),
(11, 'Lucas', '[{\"id\":5,\"name\":\"New York Hotdog\",\"qty\":2,\"price\":75}]', 150.00, 'Dine In', 'Completed', 'Paid', '2025-07-15 15:05:18', 'Cash'),
(12, 'Mia', '[{\"id\":12,\"name\":\"Cheese Burger Deluxe\",\"qty\":1,\"price\":119}, {\"id\":3,\"name\":\"Siomai\",\"qty\":1,\"price\":60}]', 179.00, 'Take Out', 'Pending', 'Unpaid', '2025-07-09 15:05:18', 'GCash'),
(13, 'Ethan', '[{\"id\":7,\"name\":\"Grilled Teriyaki\",\"qty\":1,\"price\":110}]', 110.00, 'Dine In', 'Completed', 'Paid', '2025-07-10 15:05:18', 'GCash'),
(14, 'Ava', '[{\"id\":1,\"name\":\"Spam Rice\",\"qty\":1,\"price\":89}, {\"id\":9,\"name\":\"Extra Egg\",\"qty\":1,\"price\":15}]', 104.00, 'Take Out', 'Completed', 'Paid', '2025-07-13 15:05:18', 'Cash'),
(15, 'William', '[{\"id\":2,\"name\":\"Sisig Rice\",\"qty\":2,\"price\":95}]', 190.00, 'Dine In', 'Completed', 'Paid', '2025-07-14 15:05:18', 'Cash'),
(16, 'Isabella', '[{\"id\":11,\"name\":\"Chicken Katsu Rice\",\"qty\":1,\"price\":109}, {\"id\":4,\"name\":\"Pork Lumpia\",\"qty\":2,\"price\":65}]', 239.00, 'Take Out', 'Pending', 'Paid', '2025-07-12 15:05:18', 'GCash'),
(17, 'Henry', '[{\"id\":13,\"name\":\"Burger Steak\",\"qty\":1,\"price\":99}]', 99.00, 'Dine In', 'Completed', 'Paid', '2025-07-11 15:05:18', 'Cash'),
(18, 'Amelia', '[{\"id\":6,\"name\":\"Hungarian Rice\",\"qty\":1,\"price\":99}, {\"id\":8,\"name\":\"Extra Rice\",\"qty\":2,\"price\":20}]', 139.00, 'Take Out', 'Completed', 'Paid', '2025-07-15 15:05:18', 'GCash'),
(19, 'Logan', '[{\"id\":14,\"name\":\"Beef Shawarma Rice\",\"qty\":2,\"price\":115}]', 230.00, 'Dine In', 'Pending', 'Unpaid', '2025-07-10 15:05:18', 'Cash'),
(20, 'Ella', '[{\"id\":12,\"name\":\"Cheese Burger Deluxe\",\"qty\":2,\"price\":119}]', 238.00, 'Take Out', 'Completed', 'Paid', '2025-07-15 15:05:18', 'GCash'),
(21, 'Benjamin', '[{\"id\":7,\"name\":\"Grilled Teriyaki\",\"qty\":1,\"price\":110}, {\"id\":3,\"name\":\"Siomai\",\"qty\":1,\"price\":60}]', 170.00, 'Dine In', 'Completed', 'Paid', '2025-07-13 15:05:18', 'Cash'),
(22, 'Chloe', '[{\"id\":15,\"name\":\"Beef Pares\",\"qty\":1,\"price\":99}, {\"id\":8,\"name\":\"Extra Rice\",\"qty\":1,\"price\":20}]', 119.00, 'Take Out', 'Completed', 'Paid', '2025-07-11 15:05:18', 'GCash'),
(23, 'User #19', '[{\"id\":24,\"name\":\"Grilled Teriyaki\",\"price\":110,\"qty\":1},{\"id\":23,\"name\":\"Hungarian Rice\",\"price\":99,\"qty\":1},{\"id\":19,\"name\":\"Sisig Rice\",\"price\":95,\"qty\":1},{\"id\":29,\"name\":\"Cheese Burger Deluxe\",\"price\":119,\"qty\":1}]', 423.00, 'Dine In', 'Completed', 'Paid', '2025-07-16 15:17:15', 'Cash'),
(24, 'User #19', '[{\"id\":18,\"name\":\"Spam Rice\",\"price\":89,\"qty\":1},{\"id\":32,\"name\":\"Beef Pares\",\"price\":99,\"qty\":1},{\"id\":31,\"name\":\"Beef Shawarma Rice\",\"price\":115,\"qty\":1},{\"id\":30,\"name\":\"Burger Steak\",\"price\":99,\"qty\":2},{\"id\":28,\"name\":\"Chicken Katsu Rice\",\"price\":109,\"qty\":1}]', 610.00, 'Take Out', 'Completed', 'Paid', '2025-07-16 15:17:56', 'GCash');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `is_admin`) VALUES
(18, 'user', '$2y$10$H.SlCIQiYhRJQyIWL/n24uXM8DQk6XhCByMoqSw9E0aanQ7FZ/Oqq', 0),
(19, 'admin', '$2y$10$zDpX04DCW5H3lrnuurOGbu/AzxpUHCF7IwpKdD5e2i2lK76y4XM6m', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`food_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
