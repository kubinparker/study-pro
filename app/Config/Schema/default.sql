-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 18, 2020 at 10:57 AM
-- Server version: 5.7.26
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms_cake4`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `created`, `updated`, `username`, `password`, `role`) VALUES
(1, '2017-07-14 00:00:00', '2017-07-14 00:00:00', 'caters_admin', 'catersadmin', 0),
(2, '2019-08-20 10:35:43', '2019-08-20 10:35:43', 'kokutai_admin', 'V3g5m9Yx', 0),
(3, '2019-08-20 10:35:43', '2019-08-20 10:35:43', 'utsunomiya_city', 't2XcpWn4', 1),
(4, '2019-08-20 10:35:43', '2019-08-20 10:35:43', 'irapcover', '1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('publish','draft') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'publish',
  `position` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `link` text NOT NULL,
  `title` varchar(30) CHARACTER SET utf8mb4 DEFAULT NULL,
  `image1` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `size` tinyint(3) NOT NULL DEFAULT '1',
  `file1` varchar(255) DEFAULT NULL,
  `file1_name` varchar(255) DEFAULT NULL,
  `file1_size` int(11) UNSIGNED DEFAULT NULL,
  `file2` varchar(255) DEFAULT NULL,
  `file2_name` varchar(255) DEFAULT NULL,
  `file2_size` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `created`, `updated`, `date`, `status`, `position`, `link`, `title`, `image1`, `image2`, `image3`, `size`, `file1`, `file1_name`, `file1_size`, `file2`, `file2_name`, `file2_size`) VALUES
(3, '2020-02-10 15:02:47', '2020-02-10 15:05:13', '2020-02-10', 'publish', 6, 'https://www.google.com/', 'バナー1', '', 'img_3_5e40f242-5148-41b5-89dc-40a385824a64.jpeg', 'img_3_5e40f26e-b4b4-407f-b013-412285824a64.jpeg', 2, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '2020-02-10 15:06:45', '2020-02-10 15:06:45', '2020-02-10', 'publish', 5, 'http://backlog.caters.jp/', 'バナー1234', '', 'img_4_5e40f2f5-89e4-4219-9057-3c5685824a64.jpg', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '2020-02-10 15:07:45', '2020-02-10 15:07:45', '2020-02-10', 'publish', 4, 'https://stackoverflow.com/questions/2058578/best-way-to-check-if-a-url-is-valid', 'バナー3', NULL, NULL, 'img_5_5e40f331-5c8c-415e-a695-40a385824a64.jpg', 3, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '2020-02-10 15:11:09', '2020-02-10 16:01:26', '2020-02-10', 'publish', 3, 'https://www.google.com/', 'バナー4', NULL, 'img_6_5e40ffc6-0edc-46ef-aa70-402e85824a64.jpg', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '2020-02-10 15:12:01', '2020-02-10 16:09:04', '2020-02-10', 'publish', 2, 'https://www.google.com/', 'バナー5', 'img_7_5e410190-7748-4ef1-bdee-3f4385824a64.jpg', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '2020-02-10 15:18:31', '2020-02-10 16:01:05', '2020-02-10', 'publish', 1, 'https://www.google.com/', 'バナー8', 'img_8_9747d28d-20b8-4a31-b99b-a049beb650af.jpg', 'img_8_f8f93193-67de-4156-a8c5-dccb552c4ac3.jpg', 'img_8_fdead3f4-78a6-4721-a303-07b4a9c699ca.jpg', 1, 'file_8_ed69d6cf-fbcb-4aba-9280-121924fabdac.doc', '01gouyousiki.doc', 34816, 'file_8_040b2bc2-2dc4-4398-ae62-daa787635326.pdf', 'nouzei18.12.04.pdf', 132664),
(12, '2020-02-13 16:44:12', '2020-02-13 16:44:12', '2020-02-13', 'publish', 7, 'https://www.google.com/', 'バナー12', 'img_12_5e44fe4c-8818-41f9-9923-1cc185824a64.png', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
