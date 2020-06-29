-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 09, 2019 at 07:36 PM
-- Server version: 5.7.27-0ubuntu0.18.04.1
-- PHP Version: 7.2.19-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ceek_panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_logs`
--

CREATE TABLE `customer_logs` (
  `id` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `mag_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `old_exp_date` int(11) DEFAULT NULL,
  `exp_date` int(11) DEFAULT NULL,
  `old_bouquet` text,
  `bouquet` text,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer_logs`
--

INSERT INTO `customer_logs` (`id`, `user`, `mag_id`, `user_id`, `old_exp_date`, `exp_date`, `old_bouquet`, `bouquet`, `created_at`) VALUES
(2, 2, 1, 2, 1598976666, 1598976666, '[9,11,13,14,15,16,17,30,31,34,35,36,40]', '[9,11,14,16,17,30,31,34,35,36,40', 1570643761);

-- --------------------------------------------------------

--
-- Table structure for table `dbs`
--

CREATE TABLE `dbs` (
  `id` int(11) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `db_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dbs`
--

INSERT INTO `dbs` (`id`, `host`, `port`, `username`, `password`, `db_name`) VALUES
(1, '139.99.208.195', 7999, 'bahri', '2334323', 'bahri_db');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_show` int(1) DEFAULT '1',
  `bar_title` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `is_default` int(11) DEFAULT '0',
  `permission` varchar(255) DEFAULT NULL,
  `db` int(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `title`, `is_show`, `bar_title`, `icon`, `sort`, `parent_id`, `is_default`, `permission`, `db`) VALUES
(1, 'home', 'Dashboard', 1, 'Dashboard', 'entypo-monitor', 1, 0, 1, '1', 1),
(2, 'info', 'Database Connection', 1, 'Database Connection', 'entypo-link', 2, 0, 0, '1', 0),
(3, 'add_mag', 'Add New MAG Device', 1, 'Add New MAG Device', 'entypo-plus', 3, 0, 0, '1,2', 1),
(4, 'manage_mag', 'Manage MAG Devices', 1, 'Manage MAG Devices', 'entypo-tools', 4, 0, 0, '1', 1),
(5, 'add_series', 'Add New Series', 1, 'Add New Series', 'entypo-docs', 5, 0, 0, '1', 1),
(6, 'tv_series', 'TV Series', 1, 'TV Series', 'entypo-window', 6, 0, 0, '1', 1),
(7, 'offline_streams', 'Offline streams', 1, 'Offline Streams', 'entypo-flow-branch', 7, 0, 0, '1', 1),
(8, 'manage_streams', 'Manage streams', 1, 'Manage Streams', 'entypo-magnet', 8, 0, 0, '1', 1),
(9, 'add_streams', 'Add New Stream', 1, 'Add New Stream', 'entypo-publish', 9, 0, 0, '1', 1),
(10, 'manage_vod', 'List VOD Videos', 1, 'List VOD Videos', 'entypo-cloud', 10, 0, 0, '1', 1),
(11, 'add_vod', 'Add New VOD', 1, 'Add New VOD', 'entypo-rocket', 11, 0, 0, '1', 1),
(12, 'show_logs', 'Show Logs', 1, 'Show Log History', 'entypo-doc-text-inv', 12, 0, 0, '1', 1),
(13, 'edit_mag', 'Show and Edit Mag Devices', 1, 'Show and Edit MAGs', 'entypo-vcard', 13, 0, 2, '2', 1),
(14, 'login', 'Log In', 0, '', NULL, 0, 0, 0, '1,2', 0),
(15, 'logout', NULL, 0, '', NULL, 0, 0, 0, '1,2', 0),
(16, 'dizi_system', NULL, 1, 'Dizi Sistemi', 'entypo-flow-tree', 14, 0, 0, '1', 0),
(17, 'show_atv', 'List All TV Series', 1, 'ATV', 'entypo-flow-line', 1, 16, 0, '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tv_series`
--

CREATE TABLE `tv_series` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `series` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `permission` int(11) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `permission`, `display_name`) VALUES
(1, 'administrator', '123456', 1, 'Administrator'),
(2, 'melike', '677228md', 2, 'Melike');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_logs`
--
ALTER TABLE `customer_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbs`
--
ALTER TABLE `dbs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tv_series`
--
ALTER TABLE `tv_series`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_logs`
--
ALTER TABLE `customer_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `dbs`
--
ALTER TABLE `dbs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `tv_series`
--
ALTER TABLE `tv_series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
