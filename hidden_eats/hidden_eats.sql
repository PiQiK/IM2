-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 07:55 AM
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
-- Database: `hidden_eats`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
  `customer_name` varchar(100) NOT NULL,
  `order_items` text NOT NULL,
  `total_price` int(20) NOT NULL,
  `order_type` enum('Dine In','Take Out') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `payment_status` varchar(20) DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `order_items`, `total_price`, `order_type`, `created_at`, `order_status`, `payment_status`) VALUES
(1, 'John Doe', 'Burger x1, Fries x2', 199.99, 'Dine In', '2025-07-14 16:59:39', 'Completed', 'Paid'),
(2, 'jireh', 'chicken', 150.00, 'Dine In', '2025-07-14 17:05:05', 'Completed', 'Paid'),
(3, 'John', 'Fries', 100.00, 'Dine In', '2025-07-14 17:07:06', 'Completed', 'Paid'),
(4, 'Prince', 'Coke', 50.00, 'Take Out', '2025-07-14 17:09:16', 'Ready', 'Paid'),
(5, 'Carl', 'Chicken', 150.00, 'Dine In', '2025-07-14 17:18:05', 'Preparing', 'Paid'),
(6, 'Rykelle', 'Fries', 50.00, 'Dine In', '2025-07-14 17:31:59', 'Pending', 'Pending'),
(7, 'HE337705', '[{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1}]', 120.00, 'Dine In', '2025-07-14 11:46:17', 'Ready', 'Paid'),
(8, 'HE168923', '[{\"name\":\"Burger Steak Rice\",\"price\":120,\"qty\":1}]', 120.00, 'Dine In', '2025-07-14 11:51:01', 'Ready', 'Paid'),
(10, 'HE485218', '[{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1}]', 120.00, 'Dine In', '2025-07-14 13:27:09', 'Preparing', 'Paid'),
(11, 'HE726191', '[{\"name\":\"Burger Steak Rice\",\"price\":120,\"qty\":2},{\"name\":\"Beef Pares Rice\",\"price\":120,\"qty\":2},{\"name\":\"Chicken Katsu Rice\",\"price\":110,\"qty\":1},{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1},{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":2},{\"name\":\"Hungarian Rice\",\"price\":130,\"qty\":1},{\"name\":\"Sisig Rice\",\"price\":120,\"qty\":1}]', 1180.00, 'Dine In', '2025-07-14 13:27:45', 'Preparing', 'Paid'),
(12, 'HE108257', '[{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1},{\"name\":\"Chicken Katsu Rice\",\"price\":110,\"qty\":1},{\"name\":\"Beef Pares Rice\",\"price\":120,\"qty\":1},{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":1},{\"name\":\"Hungarian Rice\",\"price\":130,\"qty\":1},{\"name\":\"Burger Steak Rice\",\"price\":120,\"qty\":1}]', 710.00, 'Dine In', '2025-07-14 13:28:37', 'Preparing', 'Paid'),
(13, 'HE930719', '[{\"name\":\"Burger Steak Rice\",\"price\":120,\"qty\":1},{\"name\":\"Beef Pares Rice\",\"price\":120,\"qty\":1},{\"name\":\"Chicken Katsu Rice\",\"price\":110,\"qty\":1},{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1},{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":1},{\"name\":\"Hungarian Rice\",\"price\":130,\"qty\":1},{\"name\":\"Sisig Rice\",\"price\":120,\"qty\":1},{\"name\":\"Brewed Coffee\",\"price\":50,\"qty\":1}]', 880.00, 'Dine In', '2025-07-14 13:57:08', 'Preparing', 'Paid'),
(14, 'HE755814', '[{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":1}]', 110.00, 'Dine In', '2025-07-14 13:59:48', 'Preparing', 'Paid'),
(15, 'HE192263', '[{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1}]', 120.00, 'Dine In', '2025-07-14 14:00:02', 'Preparing', 'Paid'),
(16, 'HE809806', '[{\"name\":\"Burger Steak Rice\",\"price\":120,\"qty\":1},{\"name\":\"Beef Pares Rice\",\"price\":120,\"qty\":1},{\"name\":\"Chicken Katsu Rice\",\"price\":110,\"qty\":1},{\"name\":\"Hungarian Rice\",\"price\":130,\"qty\":1},{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":1},{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1},{\"name\":\"Sisig Rice\",\"price\":120,\"qty\":1}]', 830.00, 'Dine In', '2025-07-14 14:12:59', 'Preparing', 'Paid'),
(17, 'HE100178', '[{\"name\":\"Beef Shawarma Rice\",\"price\":110,\"qty\":1},{\"name\":\"Braised Pork Rice\",\"price\":120,\"qty\":1}]', 230.00, 'Dine In', '2025-07-14 23:52:40', 'Preparing', 'Cash');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
