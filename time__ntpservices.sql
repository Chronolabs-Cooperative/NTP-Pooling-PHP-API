-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 27, 2019 at 06:13 PM
-- Server version: 5.7.25-1
-- PHP Version: 7.2.15-0ubuntu3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ntp-snails-email`
--

-- --------------------------------------------------------

--
-- Table structure for table `time__ntpservices`
--

DROP TABLE IF EXISTS `time__ntpservices`;
CREATE TABLE `time__ntpservices` (
  `id` bigint(32) UNSIGNED NOT NULL,
  `typal` enum('pool','server') DEFAULT 'pool',
  `state` enum('bucky','assigned') NOT NULL DEFAULT 'bucky',
  `hostname` varchar(250) NOT NULL DEFAULT '',
  `port` int(6) UNSIGNED NOT NULL DEFAULT '123',
  `name` varchar(128) NOT NULL DEFAULT '',
  `nameurl` varchar(250) NOT NULL DEFAULT '',
  `nameemail` varchar(196) NOT NULL DEFAULT '',
  `companyname` varchar(128) NOT NULL DEFAULT '',
  `companyurl` varchar(250) NOT NULL DEFAULT '',
  `companyemail` varchar(196) NOT NULL DEFAULT '',
  `companyrbn` varchar(64) NOT NULL DEFAULT '',
  `companyrbntype` varchar(13) NOT NULL DEFAULT '',
  `companytype` varchar(64) NOT NULL DEFAULT '',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pinging` decimal(32,26) UNSIGNED NOT NULL DEFAULT '0.00000000000000000000000000',
  `pinged` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `prevping` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `emailed` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `reportnext` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `reportlast` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `online` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `offline` int(12) UNSIGNED NOT NULL DEFAULT '0',
  `uptime` bigint(64) UNSIGNED NOT NULL DEFAULT '0',
  `downtime` bigint(64) UNSIGNED NOT NULL DEFAULT '0',
  `updated` int(12) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table for Storage and Reporting on NTP Services';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `time__ntpservices`
--
ALTER TABLE `time__ntpservices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `time__ntpservices`
--
ALTER TABLE `time__ntpservices`
  MODIFY `id` bigint(32) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
