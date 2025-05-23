-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 02:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pentastic_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inks`
--

CREATE TABLE `inks` (
  `id` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `volume_ml` decimal(5,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by_username` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inks`
--

INSERT INTO `inks` (`id`, `brand`, `color`, `volume_ml`, `price`, `quantity`, `image`, `created_at`, `added_by_username`) VALUES
(1, 'Parker Quink', 'Blue', 57.00, 250.00, 15, 'uploads/Parker Quink.jpg', '2025-05-20 10:33:15', 'boss'),
(2, 'LAMY T52', 'Black', 50.00, 350.00, 10, 'uploads/LAMY T52.jpg', '2025-05-20 10:33:35', 'boss'),
(3, 'Pilot Ink Bottle', 'Black', 30.00, 150.00, 25, 'uploads/Pilot Ink Bottle.jpg', '2025-05-20 10:33:56', 'boss'),
(4, 'Pentel EnerGel Refill', 'Black - Blue', 1.00, 65.00, 40, 'uploads/Pentel EnerGel Refill.jpg', '2025-05-20 10:34:16', 'boss'),
(5, 'Cross Ink Bottle', 'Black', 62.00, 500.00, 10, 'uploads/Cross Ink Bottle.jpg', '2025-05-20 10:35:13', 'boss');

-- --------------------------------------------------------

--
-- Table structure for table `pens`
--

CREATE TABLE `pens` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `added_by_username` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pens`
--

INSERT INTO `pens` (`id`, `name`, `description`, `price`, `quantity`, `created_at`, `image`, `added_by_username`) VALUES
(1, 'Parker Jotter Ballpoint Pen', 'A timeless stainless steel pen with a sleek design, ideal for everyday use.', 600.00, 20, '2025-05-20 10:30:52', 'uploads/1747737052_Parker_Jotter_Ballpoint_Pen.jpeg', 'boss'),
(2, 'LAMY Safari Fountain Pen', 'Lightweight, stylish fountain pen with ergonomic grip and a fine nib. Great for smooth writing.', 1650.00, 9, '2025-05-20 10:31:24', 'uploads/1747737084_LAMY_Safari_Fountain_Pen.jpeg', 'boss'),
(3, 'Pilot G2 Gel Pen', 'Smooth gel ink, retractable tip, and comfortable grip for long writing sessions.', 75.00, 30, '2025-05-20 10:31:56', 'uploads/1747737116_Pilot_G2_Gel_Pen.jpg', 'boss'),
(4, 'Pentel EnerGel RTX', 'Liquid gel ink dries quickly, perfect for left-handed users. Sleek and professional look.', 95.00, 29, '2025-05-20 10:32:21', 'uploads/1747737141_Pentel_EnerGel_RTX.jpg', 'boss'),
(5, 'Cross Century II Rollerball Pen', 'Luxury rollerball pen with elegant chrome finish and superior ink flow.', 2350.00, 9, '2025-05-20 10:32:43', 'uploads/1747737163_Cross_Century_II_Rollerball_Pen.jpg', 'boss');

-- --------------------------------------------------------

--
-- Table structure for table `pen_ink_compatibility`
--

CREATE TABLE `pen_ink_compatibility` (
  `id` int(11) NOT NULL,
  `pen_id` int(11) DEFAULT NULL,
  `ink_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pen_ink_compatibility`
--

INSERT INTO `pen_ink_compatibility` (`id`, `pen_id`, `ink_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pen_id` int(11) DEFAULT NULL,
  `ink_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `purchased_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `pen_id`, `ink_id`, `quantity`, `total_price`, `purchased_at`) VALUES
(1, 2, 5, NULL, 1, 2350.00, '2025-05-22 13:34:02'),
(2, 2, 5, NULL, 1, 2350.00, '2025-05-22 13:37:19'),
(3, 2, 5, NULL, 1, 2350.00, '2025-05-22 13:37:29'),
(4, 2, 5, NULL, 1, 2350.00, '2025-05-22 13:40:55'),
(5, 2, 5, NULL, 1, 2350.00, '2025-05-22 13:55:38'),
(6, 2, 2, NULL, 1, 1650.00, '2025-05-22 13:56:02'),
(7, 2, 2, NULL, 1, 1650.00, '2025-05-22 13:59:52'),
(8, 4, NULL, 5, 1, 500.00, '2025-05-23 03:11:59'),
(9, 4, 5, NULL, 5, 11750.00, '2025-05-23 03:19:45'),
(10, 4, NULL, 5, 7, 3500.00, '2025-05-23 03:20:11'),
(11, 4, 5, NULL, 5, 11750.00, '2025-05-23 03:26:22'),
(12, 4, NULL, 5, 5, 2500.00, '2025-05-23 03:27:07'),
(13, 4, 5, NULL, 100, 235000.00, '2025-05-23 05:02:29'),
(14, 4, NULL, 5, 5, 2500.00, '2025-05-23 05:16:39'),
(15, 4, 5, NULL, 5, 11750.00, '2025-05-23 06:50:25'),
(16, 4, NULL, 5, 5, 2500.00, '2025-05-23 06:50:40'),
(17, 2, 2, NULL, 1, 1650.00, '2025-05-23 07:10:06'),
(18, 2, 4, NULL, 1, 95.00, '2025-05-23 07:10:27'),
(19, 2, 5, NULL, 1, 2350.00, '2025-05-23 07:13:47');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `created_at`) VALUES
(1, 'boss', '$2y$10$dCwl/2k0KpjUzcW0YK7AeeBqxNc9.KXDxs8M6zF97ndTzE9WYU2n.', 1, '2025-05-19 15:58:21'),
(2, 'yna', '$2y$10$9F3j63Z2jNLODcrzc9YDWeIdT..ZngsoLfyaUNaHiguBr378kCnna', 2, '2025-05-20 01:33:52'),
(3, 'ano?', '$2y$10$R5AQLwxPdtETIllo7T0HjOHMVya45juZ6LYBcGCsE10bFMO2QyD9u', 2, '2025-05-20 05:07:01'),
(4, 'sumakses', '$2y$10$el2bqHiFzRDG1GqCyvW./u3Y4KhoYUatZzn0oOyH30UrOloFHFRr2', 2, '2025-05-22 17:18:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inks`
--
ALTER TABLE `inks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inks_user` (`added_by_username`);

--
-- Indexes for table `pens`
--
ALTER TABLE `pens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pens_user` (`added_by_username`);

--
-- Indexes for table `pen_ink_compatibility`
--
ALTER TABLE `pen_ink_compatibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pen_id` (`pen_id`),
  ADD KEY `ink_id` (`ink_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pen_id` (`pen_id`),
  ADD KEY `ink_id` (`ink_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inks`
--
ALTER TABLE `inks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pens`
--
ALTER TABLE `pens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pen_ink_compatibility`
--
ALTER TABLE `pen_ink_compatibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inks`
--
ALTER TABLE `inks`
  ADD CONSTRAINT `fk_inks_user` FOREIGN KEY (`added_by_username`) REFERENCES `users` (`username`);

--
-- Constraints for table `pens`
--
ALTER TABLE `pens`
  ADD CONSTRAINT `fk_pens_user` FOREIGN KEY (`added_by_username`) REFERENCES `users` (`username`);

--
-- Constraints for table `pen_ink_compatibility`
--
ALTER TABLE `pen_ink_compatibility`
  ADD CONSTRAINT `pen_ink_compatibility_ibfk_1` FOREIGN KEY (`pen_id`) REFERENCES `pens` (`id`),
  ADD CONSTRAINT `pen_ink_compatibility_ibfk_2` FOREIGN KEY (`ink_id`) REFERENCES `inks` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`pen_id`) REFERENCES `pens` (`id`),
  ADD CONSTRAINT `purchases_ibfk_3` FOREIGN KEY (`ink_id`) REFERENCES `inks` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
