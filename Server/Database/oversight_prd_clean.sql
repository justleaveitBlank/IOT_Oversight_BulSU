-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2018 at 09:28 AM
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
  `appl_type` varchar(32) NOT NULL,
  `has_power` tinyint(1) NOT NULL,
  `has_power_limit` tinyint(1) DEFAULT NULL,
  `has_time_limit` tinyint(1) DEFAULT NULL,
  `current_date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_limit_value` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `power_limit_value` double NOT NULL,
  `current_power_usage` double NOT NULL,
  `avg_watthr` double NOT NULL,
  `estimated_cost` double NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_appliance`
--

INSERT INTO `t_appliance` (`uid`, `appl_name`, `appl_type`, `has_power`, `has_power_limit`, `has_time_limit`, `current_date_time`, `time_limit_value`, `power_limit_value`, `current_power_usage`, `avg_watthr`, `estimated_cost`, `description`) VALUES
('6f63b28', 'Panasonic', 'HAIR DRYER', 1, 1, 0, '2018-12-08 04:17:44', '2018-09-02 21:48:23', 5, 1554.45, 51.82, 17.49, NULL),
('f7ba179', 'Eureka', 'Electric Fan', 1, 1, 0, '2018-12-07 09:53:55', '2018-09-07 06:11:43', 10, 9.85, 0.33, 0.11, NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `t_notification`
--

DROP TABLE IF EXISTS `t_notification`;
CREATE TABLE `t_notification` (
  `notif_id` int(10) NOT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `status` varchar(30) CHARACTER SET utf8mb4 NOT NULL,
  `appliance_id` varchar(8) CHARACTER SET utf8mb4 NOT NULL,
  `date_pop` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_notification`
--

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
('true', 'true', 'false', 11.25, '1234');

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
  MODIFY `notif_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
