<!-- BRAH PHP MY ADMIN IN THIS DAY AND AGE LMAO -->
<!-- WORKBENCH QUERIES -->

-- Create and use the database first
CREATE DATABASE IF NOT EXISTS `4747619_venuebook`;
USE `4747619_venuebook`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: fdb1033.awardspace.net
-- Generation Time: May 03, 2026 at 02:11 PM
-- Server version: 8.0.32
-- PHP Version: 8.1.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `4747619_venuebook`
--

-- --------------------------------------------------------

--
-- Table structure for table `Approvals`
--

CREATE TABLE `Approvals` (
  `Approval_id` int NOT NULL,
  `Booking_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Status` enum('approved','rejected') NOT NULL,
  `Reason` text,
  `Decided_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Approvals`
--

INSERT INTO `Approvals` (`Approval_id`, `Booking_id`, `User_id`, `Status`, `Reason`, `Decided_at`) VALUES
(2, 2, 14, 'approved', 'Availability confirmed.', '2026-04-07 04:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `AvailabilityBlocks`
--

CREATE TABLE `AvailabilityBlocks` (
  `Block_id` int NOT NULL,
  `Venue_id` int NOT NULL,
  `Blocked_date` date NOT NULL,
  `Reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `AvailabilityBlocks`
--

INSERT INTO `AvailabilityBlocks` (`Block_id`, `Venue_id`, `Blocked_date`, `Reason`) VALUES
(5, 12, '2026-05-01', 'Maintenance'),
(6, 13, '2026-12-25', 'Holiday');

-- --------------------------------------------------------

--
-- Table structure for table `Bookings`
--

CREATE TABLE `Bookings` (
  `Booking_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Venue_id` int NOT NULL,
  `Package_id` int DEFAULT NULL,
  `Event_date` date NOT NULL,
  `Guest_count` int NOT NULL,
  `Total_price` decimal(10,2) NOT NULL,
  `Payment_deadline` datetime DEFAULT NULL,
  `Booking_status` enum('pending','approved','confirmed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `Created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`Booking_id`, `User_id`, `Venue_id`, `Package_id`, `Event_date`, `Guest_count`, `Total_price`, `Payment_deadline`, `Booking_status`, `Created_at`) VALUES
(2, 13, 12, 1, '2026-06-15', 250, 90000.00, '2026-05-15 23:59:59', 'pending', '2026-04-07 04:23:31');

-- --------------------------------------------------------

--
-- Table structure for table `Contracts`
--

CREATE TABLE `Contracts` (
  `contractid` int NOT NULL,
  `userid` int NOT NULL,
  `bookingid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Contracts`
--

INSERT INTO `Contracts` (`contractid`, `userid`, `bookingid`) VALUES
(1, 15, 2);

-- --------------------------------------------------------

--
-- Table structure for table `Notifications`
--

CREATE TABLE `Notifications` (
  `Notification_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Booking_id` int DEFAULT NULL,
  `Type` enum('info','approval','payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Message` text NOT NULL,
  `Is_read` tinyint(1) DEFAULT '0',
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Notifications`
--

INSERT INTO `Notifications` (`Notification_id`, `User_id`, `Booking_id`, `Type`, `Message`, `Is_read`, `Created_at`) VALUES
(1, 15, 2, '', 'Your booking has been approved!', 0, '2026-04-07 04:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `Packages`
--

CREATE TABLE `Packages` (
  `Package_id` int NOT NULL,
  `Venue_id` int NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Inclusions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Status` enum('active','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Packages`
--

INSERT INTO `Packages` (`Package_id`, `Venue_id`, `Name`, `Inclusions`, `Price`, `Status`) VALUES
(1, 12, 'Premium Wedding', 'Catering & Decor', 55000.00, 'archived'),
(2, 12, 'Corporate Seminar', 'Projector & Coffee', 15000.00, 'active'),
(3, 12, 'Wedding Gold', 'Catering, Lights', 50000.00, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `Payments`
--

CREATE TABLE `Payments` (
  `Payment_id` int NOT NULL,
  `Booking_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `Paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Payments`
--

INSERT INTO `Payments` (`Payment_id`, `Booking_id`, `User_id`, `Amount`, `Type`, `Status`, `Paid_at`) VALUES
(1, 2, 15, 45000.00, 'Deposit', 'completed', '2026-04-07 04:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `User_id` int NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone_num` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` enum('client','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password_hash` varchar(255) NOT NULL,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`User_id`, `First_name`, `Last_name`, `Email`, `Phone_num`, `Role`, `Password_hash`, `Created_at`) VALUES
(13, 'Alice', 'Admin', 'alice@admin.com', '09111111111', 'admin', 'hash_admin', '2026-04-07 04:19:34'),
(14, 'Bob', 'Host', 'bob@host.com', '09222222222', '', 'hash_owner', '2026-04-07 04:19:34'),
(15, 'Charlie', 'Client', 'charlie@client.com', '09333333333', 'client', 'hash_client', '2026-04-07 04:19:34'),
(16, 'Johnny', 'Doe', 'john@example.com', '09998887777', 'client', 'new_hashed_pass', '2026-04-07 04:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `Venue`
--

CREATE TABLE `Venue` (
  `Venue_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text,
  `Location` varchar(255) NOT NULL,
  `Capacity` int NOT NULL,
  `Price_per_day` decimal(10,2) NOT NULL,
  `Status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `Venue`
--

INSERT INTO `Venue` (`Venue_id`, `User_id`, `Name`, `Description`, `Location`, `Capacity`, `Price_per_day`, `Status`) VALUES
(12, 13, 'Grand Ballroom', 'Elegant indoor hall.', 'Manila', 300, 50000.00, 'active'),
(13, 13, 'Garden Oasis', 'Outdoor paradise.', 'Tagaytay', 150, 25000.00, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Approvals`
--
ALTER TABLE `Approvals`
  ADD PRIMARY KEY (`Approval_id`),
  ADD KEY `Booking_id` (`Booking_id`),
  ADD KEY `User_id` (`User_id`);

--
-- Indexes for table `AvailabilityBlocks`
--
ALTER TABLE `AvailabilityBlocks`
  ADD PRIMARY KEY (`Block_id`),
  ADD KEY `Venue_id` (`Venue_id`);

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`Booking_id`),
  ADD KEY `User_id` (`User_id`),
  ADD KEY `Venue_id` (`Venue_id`),
  ADD KEY `Package_id` (`Package_id`);

--
-- Indexes for table `Contracts`
--
ALTER TABLE `Contracts`
  ADD PRIMARY KEY (`contractid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `bookingid` (`bookingid`);

--
-- Indexes for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD PRIMARY KEY (`Notification_id`),
  ADD KEY `User_id` (`User_id`),
  ADD KEY `Booking_id` (`Booking_id`);

--
-- Indexes for table `Packages`
--
ALTER TABLE `Packages`
  ADD PRIMARY KEY (`Package_id`),
  ADD KEY `Venue_id` (`Venue_id`);

--
-- Indexes for table `Payments`
--
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`Payment_id`),
  ADD KEY `Booking_id` (`Booking_id`),
  ADD KEY `User_id` (`User_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `Venue`
--
ALTER TABLE `Venue`
  ADD PRIMARY KEY (`Venue_id`),
  ADD KEY `User_id` (`User_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Approvals`
--
ALTER TABLE `Approvals`
  MODIFY `Approval_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `AvailabilityBlocks`
--
ALTER TABLE `AvailabilityBlocks`
  MODIFY `Block_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Bookings`
--
ALTER TABLE `Bookings`
  MODIFY `Booking_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Contracts`
--
ALTER TABLE `Contracts`
  MODIFY `contractid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Notifications`
--
ALTER TABLE `Notifications`
  MODIFY `Notification_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Packages`
--
ALTER TABLE `Packages`
  MODIFY `Package_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Payments`
--
ALTER TABLE `Payments`
  MODIFY `Payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `User_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Venue`
--
ALTER TABLE `Venue`
  MODIFY `Venue_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Approvals`
--
ALTER TABLE `Approvals`
  ADD CONSTRAINT `Approvals_ibfk_1` FOREIGN KEY (`Booking_id`) REFERENCES `Bookings` (`Booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Approvals_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`);

--
-- Constraints for table `AvailabilityBlocks`
--
ALTER TABLE `AvailabilityBlocks`
  ADD CONSTRAINT `AvailabilityBlocks_ibfk_1` FOREIGN KEY (`Venue_id`) REFERENCES `Venue` (`Venue_id`) ON DELETE CASCADE;

--
-- Constraints for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD CONSTRAINT `Bookings_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`),
  ADD CONSTRAINT `Bookings_ibfk_2` FOREIGN KEY (`Venue_id`) REFERENCES `Venue` (`Venue_id`),
  ADD CONSTRAINT `Bookings_ibfk_3` FOREIGN KEY (`Package_id`) REFERENCES `Packages` (`Package_id`) ON DELETE SET NULL;

--
-- Constraints for table `Contracts`
--
ALTER TABLE `Contracts`
  ADD CONSTRAINT `Contracts_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `Users` (`User_id`),
  ADD CONSTRAINT `Contracts_ibfk_2` FOREIGN KEY (`bookingid`) REFERENCES `Bookings` (`Booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `Notifications`
--
ALTER TABLE `Notifications`
  ADD CONSTRAINT `Notifications_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Notifications_ibfk_2` FOREIGN KEY (`Booking_id`) REFERENCES `Bookings` (`Booking_id`) ON DELETE SET NULL;

--
-- Constraints for table `Packages`
--
ALTER TABLE `Packages`
  ADD CONSTRAINT `Packages_ibfk_1` FOREIGN KEY (`Venue_id`) REFERENCES `Venue` (`Venue_id`) ON DELETE CASCADE;

--
-- Constraints for table `Payments`
--
ALTER TABLE `Payments`
  ADD CONSTRAINT `Payments_ibfk_1` FOREIGN KEY (`Booking_id`) REFERENCES `Bookings` (`Booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `Payments_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`);

--
-- Constraints for table `Venue`
--
ALTER TABLE `Venue`
  ADD CONSTRAINT `Venue_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;