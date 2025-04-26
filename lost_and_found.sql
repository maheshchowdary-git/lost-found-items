-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 26, 2025 at 06:41 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lost_and_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Electronics'),
(2, 'Clothing'),
(3, 'Documents');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`image_id`, `item_id`, `image_path`, `uploaded_at`) VALUES
(11, 35, 'uploads/1745395442_mac.jpeg', '2025-04-23 08:04:02'),
(12, 37, 'uploads/1745396461_earbuds.jpeg', '2025-04-23 08:21:01');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_found` date NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `found_by` varchar(255) NOT NULL,
  `status` enum('lost','found','returned','claimed') DEFAULT 'found',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_type` varchar(10) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `claimed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_name`, `description`, `date_found`, `location_id`, `category_id`, `found_by`, `status`, `created_at`, `item_type`, `contact_info`, `image_path`, `claimed_by`) VALUES
(35, 'laptop', 'macbook m2 air midnight color ', '2025-04-23', 1, 1, 'Ram', 'claimed', '2025-04-23 08:04:02', 'found', '9573409069', NULL, 1),
(36, 'Textbook ', 'DBMS Textbook by Dr.Hariram Chavan', '2025-04-22', 1, 3, 'John ', 'found', '2025-04-23 08:18:35', 'lost', '45634535', NULL, NULL),
(37, 'Earbuds', 'Beats Earbuds red color ', '2025-04-23', 1, 1, 'Nathan', 'found', '2025-04-23 08:21:01', 'found', '9837459345', NULL, NULL),
(38, 'Hoodie', 'Black hoodie size M ', '2025-04-15', 3, 2, 'reddy', 'claimed', '2025-04-23 09:14:05', 'found', '234234234', NULL, 1),
(39, 'earphones', 'black wired earphones ', '2025-04-23', 2, 1, 'dbmsadsadpf', 'found', '2025-04-23 10:03:13', 'lost', '23543534', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `name`, `details`) VALUES
(1, 'Library', NULL),
(2, 'Mess', NULL),
(3, 'Gym', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `log_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_logs`
--

INSERT INTO `status_logs` (`log_id`, `item_id`, `old_status`, `new_status`, `changed_at`) VALUES
(38, 35, 'found', 'claimed', '2025-04-23 08:04:36'),
(39, 38, 'found', 'claimed', '2025-04-24 05:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(1, 'mahesh', 'chirumamillamahesh2005@gmail.com', '$2y$10$gmRCZ1pWCIv9xtE02xZ20u31RZVRtTKfw1zHQRcP835koH.4.1cay', '2025-04-21 17:42:12', 'user'),
(3, 'admin', 'chirumamillamahesh2005@gmail.com', '$2y$10$XtfLSun7gxh.NjpB6F0sku8wsx5mX0VJi933nrPAc6yBfHP8LP4We', '2025-04-21 18:35:49', 'admin'),
(4, 'ram', 'dogears45@gmail.com', '$2y$10$aHz696eD68di33sQTqSxrOGsYXzqyKCLSwfma/XSV2.rMduS857My', '2025-04-23 08:03:15', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `item_id` (`item_id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD CONSTRAINT `status_logs_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
