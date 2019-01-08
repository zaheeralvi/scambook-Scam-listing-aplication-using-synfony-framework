-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2019 at 07:21 AM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scambook`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_detail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `post_id`, `comment_detail`, `date`) VALUES
(1, 1, 1, 'hahahaha ', '2018-11-13 09:12:01'),
(2, 6, 1, 'sdfsdfsdf', '2018-11-13 16:04:49'),
(3, 6, 5, 'sdfdsf sdfrsdf', '2018-11-13 16:05:52'),
(4, 6, 1, ' sdfsdf sdfsdf', '2018-11-13 16:06:33'),
(5, 6, 1, 'sdf', '2018-11-13 16:07:55'),
(6, 6, 1, 'sdf', '2018-11-13 16:08:14'),
(7, 6, 1, 'dfgdf g', '2018-11-13 16:09:28'),
(8, 1, 5, 'this is for testing', '2018-11-13 16:20:35'),
(9, 1, 27, 'yeah you are right', '2018-11-13 16:46:58'),
(10, 1, 19, 'Reported Damage : 85', '2018-11-13 16:57:49'),
(11, 1, 27, 'i thing done', '2018-11-13 16:58:40'),
(12, 1, 5, 'test test etst test test ets test test\r\n\r\n	\r\nuser Posted: November 13, 2018 11:03 Comment', '2018-11-13 17:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `name`, `phone`, `email`, `description`, `created_on`) VALUES
(1, 'ccjk', '0303030303', 'ccjk@ccjk.com', 'i am working in ccjk', '2018-11-13 09:11:16'),
(2, 'mangosis', '00865252652', 'mangosis.com', 'we work on software and intelligent systems', '2018-11-13 15:25:22'),
(3, 'google', '0596505', 'info@google.com', 'google is google', '2018-11-13 16:45:34');

-- --------------------------------------------------------

--
-- Table structure for table `fos_user`
--

CREATE TABLE `fos_user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salesman_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` decimal(10,0) DEFAULT NULL,
  `address_1` text COLLATE utf8_unicode_ci,
  `address_2` text COLLATE utf8_unicode_ci,
  `cur_address` text COLLATE utf8_unicode_ci,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` text COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `user_status` tinyint(1) DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `googleplus_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `others` text COLLATE utf8_unicode_ci,
  `longitude` decimal(18,15) DEFAULT NULL,
  `latitude` decimal(18,15) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `confirmation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `fos_user`
--

INSERT INTO `fos_user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`, `first_name`, `last_name`, `salesman_name`, `dob`, `age`, `address_1`, `address_2`, `cur_address`, `city`, `state`, `country`, `description`, `user_status`, `facebook_id`, `twitter_id`, `googleplus_id`, `postal_code`, `others`, `longitude`, `latitude`, `created_date`, `modified_date`, `updated_at`, `confirmation_code`, `language`, `contact_no`) VALUES
(1, 'adminuser', 'adminuser', 'zaheermalik284@gmail.com', 'zaheermalik284@gmail.com', 1, NULL, '$2y$13$4toS0WmLI5b6c9KHFlegceqmRrstJvmGL4Fx0J4Mfq/odK.pmGHyq', '2018-11-13 16:12:47', NULL, NULL, 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'zaheeralvi', 'zaheeralvi', 'zaheer@ccjk.com', 'zaheer@ccjk.com', 1, NULL, '$2y$13$N0XtjmMCLqJRjTBIeTE0aONDbvwH8E1PvX9St5v2.cMZzPOTNyUua', '2018-11-13 14:18:48', NULL, NULL, 'a:0:{}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'zaheeralvi1', 'zaheeralvi1', 'zaheer1@ccjk.com', 'zaheer1@ccjk.com', 1, NULL, '$2y$13$v1VJBd.42tXUtke8OPDcQ.9MqQUu.BeaYHzlBbmb6voZCCX/SIELu', '2018-11-13 14:22:55', NULL, NULL, 'a:0:{}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'faizy102@11.com', 'faizy102@11.com', 'faizy102@11.com', 'faizy102@11.com', 1, NULL, '$2y$13$ydCxUTtzSSNR6Q9wiiUmy.fskd2wsb4LotX.5kpQRj/trcAk3VKfG', '2018-11-13 15:06:31', NULL, NULL, 'a:0:{}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'tr', 'tr', 'tert@ew.kjkjh', 'tert@ew.kjkjh', 1, NULL, '$2y$13$V7s9feweTfIkYUvlepeq1usCa3LJtwn9CO9lG9tRR0V6wX/HAWOVG', '2018-11-13 15:49:09', NULL, NULL, 'a:0:{}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'ter', 'ter', 'tert@ew.kjrekjh', 'tert@ew.kjrekjh', 1, NULL, '$2y$13$E0w8KMJDY8880.3Xy5./jOzkWyjkRBDxPHAWezamZwUWeyqdCyEXa', '2018-11-13 15:55:43', NULL, NULL, 'a:0:{}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scam_details`
--

CREATE TABLE `scam_details` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `damage_price` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `investigation` tinyint(1) NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date_occurance` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abbreviation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `scam_details`
--

INSERT INTO `scam_details` (`id`, `company_id`, `damage_price`, `status`, `investigation`, `description`, `date_occurance`, `user_id`, `website`, `abbreviation`) VALUES
(1, 1, '500', 'new', 0, 'some personal', '2018-11-13 09:11:46', NULL, NULL, NULL),
(5, 1, '500', 'investigation requested', 1, 'test test etst test test ets test test', '2018-11-13 11:03:09', 1, NULL, NULL),
(6, 1, '150', 'resolved', 1, 'testing testing', '2018-11-13 12:08:13', 1, NULL, NULL),
(17, 1, '34', 'new', 0, 'testing', '2018-11-13 13:30:58', 1, NULL, NULL),
(18, 1, '85', 'new', 0, 'testing ho rahi ha', '2018-11-13 13:31:22', 1, NULL, NULL),
(19, 1, '85', 'new', 0, 'testing ho rahi ha', '2018-11-13 13:31:43', 1, NULL, NULL),
(20, 1, '87', 'new', 0, '7827892', '2018-11-13 13:31:57', 1, NULL, NULL),
(25, 2, '450', 'new', 0, 'they are not providing best services to thair clients', '2018-11-13 15:27:25', 1, NULL, NULL),
(26, 2, '50', 'new', 0, 'test test test ets t ets tetejy', '2018-11-13 15:29:51', 1, NULL, NULL),
(27, 3, '450', 'new', 0, 'bad search experience', '2018-11-13 16:46:27', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`),
  ADD KEY `IDX_9474526C4B89032C` (`post_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fos_user`
--
ALTER TABLE `fos_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_957A6479C05FB297` (`confirmation_token`);

--
-- Indexes for table `scam_details`
--
ALTER TABLE `scam_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_11725971979B1AD6` (`company_id`),
  ADD KEY `IDX_11725971A76ED395` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `fos_user`
--
ALTER TABLE `fos_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `scam_details`
--
ALTER TABLE `scam_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `scam_details` (`id`),
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

--
-- Constraints for table `scam_details`
--
ALTER TABLE `scam_details`
  ADD CONSTRAINT `FK_11725971979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`),
  ADD CONSTRAINT `FK_11725971A76ED395` FOREIGN KEY (`user_id`) REFERENCES `fos_user` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
