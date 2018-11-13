-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2018 at 02:17 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

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
CREATE DATABASE IF NOT EXISTS `oversight_prd` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `oversight_prd`;

-- --------------------------------------------------------

--
-- Table structure for table `t_appliance`
--

DROP TABLE IF EXISTS `t_appliance`;
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
  `avg_watthr` double DEFAULT NULL,
  `estimated_cost` double DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_appliance`
--

INSERT INTO `t_appliance` (`uid`, `appl_name`, `has_power`, `has_power_limit`, `has_time_limit`, `current_date_time`, `time_limit_value`, `power_limit_value`, `current_power_usage`, `avg_watthr`, `estimated_cost`, `description`) VALUES
('6f63b28', 'Appliance_01', 0, 1, 0, '2018-11-08 09:33:27', '2018-09-03 13:48:23', 0.0012, 18.62, 0, 0, NULL),
('f7ba179', 'Appliance_02', 0, 0, 0, '2018-10-15 12:32:16', '2018-09-07 14:11:43', 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_history`
--

DROP TABLE IF EXISTS `t_history`;
CREATE TABLE `t_history` (
  `uid` varchar(8) NOT NULL,
  `consumed` float DEFAULT NULL,
  `effective_date` datetime NOT NULL,
  `lst_updt_dte` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_history`
--

INSERT INTO `t_history` (`uid`, `consumed`, `effective_date`, `lst_updt_dte`) VALUES
('6f63b28', 50, '2018-10-23 15:05:33', '2018-10-23 15:06:34'),
('6f63b28', 21.36, '2018-10-24 15:09:06', '2018-10-24 17:50:06'),
('6f63b28', 30, '2018-10-25 02:21:00', '2018-10-25 03:33:19'),
('6f63b28', 50.76, '2018-10-27 07:44:17', '2018-10-27 07:52:00'),
('6f63b28', 57.84, '2018-10-30 12:03:57', '2018-10-30 12:15:29'),
('6f63b28', 18.62, '2018-11-08 15:52:59', '2018-11-08 17:33:23'),
('ae113a20', 0, '2018-11-08 16:02:19', '2018-11-08 16:02:19'),
('NO_UID', 37.42, '2018-10-25 12:16:59', '2018-10-25 12:21:55'),
('NO_UID', 14.24, '2018-10-26 04:08:37', '2018-10-26 04:27:02'),
('NO_UID', 0.01, '2018-10-27 09:00:30', '2018-10-27 09:02:24'),
('NO_UID', 7.45, '2018-10-28 04:14:41', '2018-10-28 04:30:51'),
('NO_UID', 279.05, '2018-10-30 10:05:15', '2018-10-30 13:58:10'),
('NO_UID', 0.08, '2018-11-08 16:01:18', '2018-11-08 16:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `t_notification`
--

DROP TABLE IF EXISTS `t_notification`;
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
(1, 'newanoapp', 'ignored', 'NO_UID'),
(2, 'newapp', 'ignored', 'ae113a20'),
(3, 'newapp', 'ignored', 'currentU'),
(4, 'newapp', 'ignored', 'currentU'),
(5, 'newapp', 'ignored', 'currentU'),
(6, 'newapp', 'ignored', 'currentU'),
(7, 'consumption', 'unresolved', '6f63b28');

-- --------------------------------------------------------

--
-- Table structure for table `t_rate`
--

DROP TABLE IF EXISTS `t_rate`;
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

DROP TABLE IF EXISTS `t_settings`;
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
('true', 'true', 'true', 1.5, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

DROP TABLE IF EXISTS `t_users`;
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
('user01', 'pass', 'user01', 'user01', '09999999999', 'user01@gmail.com', '11111', '1111');

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
  ADD PRIMARY KEY (`uid`,`effective_date`);

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
  MODIFY `notif_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
