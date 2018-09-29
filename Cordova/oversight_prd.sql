-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2018 at 04:55 AM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oversight_prd`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_appliance`
--

CREATE TABLE `t_appliance` (
  `uid` varchar(8) NOT NULL,
  `appl_name` varchar(32) NOT NULL,
  `has_power` tinyint(1) NOT NULL,
  `has_power_limit` tinyint(1) DEFAULT NULL,
  `has_time_limit` tinyint(1) DEFAULT NULL,
  `current_date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_limit_value` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `power_limit_value` double DEFAULT NULL,
  `current_power_usage` double DEFAULT NULL,
  `appl_image` blob,
  `avg_watthr` double DEFAULT NULL,
  `estimated_cost` double DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_appliance`
--

INSERT INTO `t_appliance` (`uid`, `appl_name`, `has_power`, `has_power_limit`, `has_time_limit`, `current_date_time`, `time_limit_value`, `power_limit_value`, `current_power_usage`, `appl_image`, `avg_watthr`, `estimated_cost`, `description`) VALUES
('6f63b28', 'Appliance_01', 0, 0, NULL, '2018-09-28 02:53:42', '2018-09-03 13:48:23', 0, 0, NULL, NULL, NULL, NULL),
('f7ba179', 'Appliance_02', 0, NULL, NULL, '2018-09-27 01:39:21', '2018-09-07 14:11:43', 25, 0, NULL, NULL, NULL, NULL),
('NO_UID', 'NO_UID_Appliance', 0, 0, NULL, '2018-09-28 02:49:18', '2018-09-03 09:41:25', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_history`
--

CREATE TABLE `t_history` (
  `uid` varchar(8) NOT NULL,
  `consumed` float DEFAULT NULL,
  `effective_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lst_updt_dte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_history`
--

INSERT INTO `t_history` (`uid`, `consumed`, `effective_date`, `lst_updt_dte`) VALUES
('6f63b28', 1, '2018-09-28 08:54:39', '2018-09-28 08:54:39'),
('6f63b28', 2, '2018-09-28 08:56:39', '2018-09-28 08:56:39'),
('6f63b28', 3, '2018-09-28 10:05:43', '2018-09-28 10:05:43');

-- --------------------------------------------------------

--
-- Table structure for table `t_notification`
--

CREATE TABLE `t_notification` (
  `notif_id` int(10) NOT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `appliance_id` varchar(8) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_notification`
--

INSERT INTO `t_notification` (`notif_id`, `type`, `status`, `appliance_id`) VALUES
(282, 'newapp', 'registered', 'ae113a20'),
(283, 'newapp', 'registered', '1111111'),
(284, 'newapp', 'registered', '1111111'),
(285, 'newapp', 'ignored', 'ae113a20'),
(286, 'newapp', 'ignored', '1111111');

-- --------------------------------------------------------

--
-- Table structure for table `t_rate`
--

CREATE TABLE `t_rate` (
  `total_consumed` float DEFAULT NULL,
  `power_rate` float DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `effective_date` float DEFAULT NULL,
  `lst_updt_dte` date DEFAULT NULL,
  `lst_updt_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `t_settings`
--

CREATE TABLE `t_settings` (
  `socket` varchar(5) NOT NULL DEFAULT 'true',
  `limitation` varchar(5) NOT NULL DEFAULT 'true',
  `authentication` varchar(5) NOT NULL DEFAULT 'true',
  `price` double NOT NULL DEFAULT '0',
  `admin` varchar(11) NOT NULL DEFAULT '1234'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_settings`
--

INSERT INTO `t_settings` (`socket`, `limitation`, `authentication`, `price`, `admin`) VALUES
('true', 'false', 'true', 2.5, '1234');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

CREATE TABLE `t_users` (
  `username` varchar(20) NOT NULL,
  `password` varchar(20) DEFAULT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `contact` varchar(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `confirm_code` varchar(32) DEFAULT NULL,
  `admin` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_users`
--

INSERT INTO `t_users` (`username`, `password`, `firstname`, `lastname`, `contact`, `email`, `confirm_code`, `admin`) VALUES
('admin', 'admin', 'john', 'francisco', '09345678910', 'johnbenedictjb@gmail.com', NULL, '1234'),
('user01', 'pass', 'user01', 'user01', '09999999999', 'user01@gmail.com', NULL, '1111');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_appliance`
--
ALTER TABLE `t_appliance`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `t_history`
--
ALTER TABLE `t_history`
  ADD PRIMARY KEY (`uid`,`effective_date`,`lst_updt_dte`);

--
-- Indexes for table `t_notification`
--
ALTER TABLE `t_notification`
  ADD PRIMARY KEY (`notif_id`),
  ADD KEY `notif_app_fk` (`appliance_id`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_notification`
--
ALTER TABLE `t_notification`
  MODIFY `notif_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
