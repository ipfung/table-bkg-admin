-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 17, 2022 at 10:06 AM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voyagerdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `room_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `service_id` int(10) UNSIGNED DEFAULT NULL,
  `package_id` int(10) UNSIGNED DEFAULT NULL,
  `internal_remark` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `start_time`, `end_time`, `room_id`, `user_id`, `service_id`, `package_id`, `internal_remark`, `status`, `created_at`, `updated_at`) VALUES
(1, '2022-08-06 14:00:00', '2022-08-06 15:30:00', 1, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-05 02:48:21', '2022-08-12 09:03:01'),
(2, '2022-08-07 14:00:00', '2022-08-07 16:00:00', 1, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-05 02:50:19', '2022-08-12 09:03:07'),
(3, '2022-08-05 13:00:00', '2022-08-05 14:00:00', 1, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-05 02:59:50', '2022-08-12 09:03:12'),
(4, '2022-08-07 17:30:00', '2022-08-07 19:00:00', 2, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-05 03:59:30', '2022-08-12 09:03:17'),
(5, '2022-08-06 11:30:00', '2022-08-06 13:00:00', 2, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-06 14:53:28', '2022-08-12 09:03:23'),
(6, '2022-08-06 10:00:00', '2022-08-06 11:30:00', 1, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-06 14:54:57', '2022-08-12 09:03:28'),
(7, '2022-08-17 10:30:00', '2022-08-17 12:00:00', 2, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-08 01:26:59', '2022-08-12 09:03:34'),
(8, '2022-08-09 09:30:00', '2022-08-09 12:00:00', 2, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-08 08:57:52', '2022-08-12 09:03:39'),
(9, '2022-08-09 09:30:00', '2022-08-09 12:00:00', 1, 3, 1, NULL, 'Appointment rejected due to no payment has been made in 96 hour', 'rejected', '2022-08-08 09:00:11', '2022-08-12 09:03:44'),
(10, '2022-08-09 15:00:00', '2022-08-09 16:00:00', 1, 3, 1, NULL, '', 'pending', '2022-08-08 09:05:14', '2022-08-12 08:11:23'),
(11, '2022-08-16 14:30:00', '2022-08-16 16:00:00', 1, 3, 1, NULL, '', 'pending', '2022-08-08 09:10:05', '2022-08-12 08:11:28'),
(12, '2022-08-15 19:00:00', '2022-08-15 20:30:00', 1, 3, 1, NULL, '', 'pending', '2022-08-08 12:44:14', '2022-08-12 08:11:33'),
(13, '2022-08-15 23:30:00', '2022-08-16 01:00:00', 1, 5, 1, NULL, '', 'pending', '2022-08-09 01:27:12', '2022-08-12 08:11:38'),
(14, '2022-08-16 01:00:00', '2022-08-16 02:00:00', 2, 5, 1, NULL, '', 'pending', '2022-08-09 03:06:26', '2022-08-12 08:11:44'),
(15, '2022-08-16 01:00:00', '2022-08-16 02:00:00', 1, 5, 1, NULL, '', 'pending', '2022-08-09 19:42:27', '2022-08-12 08:11:50'),
(16, '2022-08-18 11:00:00', '2022-08-18 12:30:00', 1, 5, 1, NULL, '', 'pending', '2022-08-10 16:05:32', '2022-08-12 08:11:55'),
(17, '2022-08-18 11:00:00', '2022-08-18 12:30:00', 2, 5, 1, NULL, '', 'pending', '2022-08-10 18:01:17', '2022-08-12 08:12:00'),
(18, '2022-08-20 13:00:00', '2022-08-20 14:30:00', 2, 5, 1, NULL, '', 'pending', '2022-08-10 18:07:09', '2022-08-12 08:12:06'),
(19, '2022-08-20 13:00:00', '2022-08-20 14:30:00', 1, 5, 1, NULL, '', 'pending', '2022-08-10 18:09:25', '2022-08-12 08:12:12'),
(20, '2022-08-19 13:30:00', '2022-08-19 15:00:00', 2, 5, 1, NULL, '', 'pending', '2022-08-10 18:09:47', '2022-08-12 08:12:18'),
(21, '2022-08-20 15:00:00', '2022-08-20 16:00:00', 1, 5, 1, NULL, '', 'pending', '2022-08-10 18:14:01', '2022-08-12 08:12:23'),
(22, '2022-08-17 13:30:00', '2022-08-17 15:00:00', 3, 3, 1, NULL, NULL, 'pending', '2022-08-16 19:10:02', '2022-08-16 19:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `booking_reminders`
--

CREATE TABLE `booking_reminders` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_reminders`
--

INSERT INTO `booking_reminders` (`id`, `booking_id`, `email`, `created_at`, `updated_at`) VALUES
(1, 14, 'ipfung@gmail.com', '2022-08-11 03:06:59', '2022-08-11 03:06:59'),
(2, 15, 'ipfung@gmail.com', '2022-08-11 03:07:04', '2022-08-11 03:07:04'),
(3, 14, 'ipfung@gmail.com', '2022-08-11 03:12:32', '2022-08-11 03:12:32'),
(4, 15, 'ipfung@gmail.com', '2022-08-11 03:12:37', '2022-08-11 03:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `order`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'Category 1', 'category-1', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, NULL, 1, 'Category 2', 'category-2', '2022-07-19 06:56:31', '2022-07-19 06:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_phone_ios` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `address`, `website`, `country_phone_ios`, `phone`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Victory Ping Pong', NULL, NULL, NULL, NULL, NULL, '2022-07-21 07:28:30', '2022-07-21 07:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `customer_bookings`
--

CREATE TABLE `customer_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `price` double NOT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `info` int(11) DEFAULT NULL,
  `checkin` datetime DEFAULT NULL,
  `checkout` datetime DEFAULT NULL,
  `revised_appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `revision_counter` smallint(6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_bookings`
--

INSERT INTO `customer_bookings` (`id`, `appointment_id`, `customer_id`, `price`, `coupon_id`, `token`, `info`, `checkin`, `checkout`, `revised_appointment_id`, `revision_counter`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 75, NULL, NULL, NULL, NULL, NULL, 1, 0, '2022-08-05 02:48:21', '2022-08-05 02:48:21'),
(2, 2, 3, 100, NULL, NULL, NULL, NULL, NULL, 2, 0, '2022-08-05 02:50:19', '2022-08-05 02:50:19'),
(3, 3, 3, 75, NULL, NULL, NULL, '2022-08-05 12:43:46', NULL, 3, 0, '2022-08-05 02:59:50', '2022-08-05 11:43:46'),
(4, 4, 3, 75, NULL, NULL, NULL, NULL, NULL, 4, 0, '2022-08-05 03:59:30', '2022-08-05 03:59:30'),
(5, 5, 3, 90, NULL, NULL, NULL, NULL, NULL, 5, 0, '2022-08-06 14:53:28', '2022-08-06 14:53:28'),
(6, 6, 3, 90, NULL, NULL, NULL, '2022-08-06 10:17:49', NULL, 6, 0, '2022-08-06 14:54:57', '2022-08-06 08:17:49'),
(7, 7, 3, 90, NULL, NULL, NULL, NULL, NULL, 7, 1, '2022-08-08 01:26:59', '2022-08-08 12:56:34'),
(8, 8, 3, 150, NULL, NULL, NULL, NULL, NULL, 8, 0, '2022-08-08 08:57:52', '2022-08-08 08:57:52'),
(9, 9, 3, 150, NULL, NULL, NULL, NULL, NULL, 9, 0, '2022-08-08 09:00:11', '2022-08-08 09:00:11'),
(10, 10, 3, 60, NULL, NULL, NULL, NULL, NULL, 10, 0, '2022-08-08 09:05:14', '2022-08-08 09:05:14'),
(11, 11, 3, 90, NULL, NULL, NULL, NULL, NULL, 11, 0, '2022-08-08 09:10:05', '2022-08-12 06:52:44'),
(12, 12, 3, 90, NULL, NULL, NULL, NULL, NULL, 12, 1, '2022-08-08 12:44:14', '2022-08-12 07:01:26'),
(13, 13, 5, 90, NULL, NULL, NULL, NULL, NULL, 13, 0, '2022-08-09 01:27:12', '2022-08-09 01:27:12'),
(14, 14, 5, 60, NULL, NULL, NULL, NULL, NULL, 14, 0, '2022-08-09 03:06:26', '2022-08-09 03:06:26'),
(15, 15, 5, 60, NULL, NULL, NULL, NULL, NULL, 15, 0, '2022-08-09 19:42:27', '2022-08-09 19:42:27'),
(16, 16, 5, 90, NULL, NULL, NULL, NULL, NULL, 16, 0, '2022-08-10 16:05:32', '2022-08-10 16:05:32'),
(17, 17, 5, 90, NULL, NULL, NULL, NULL, NULL, 17, 0, '2022-08-10 18:01:17', '2022-08-10 18:01:17'),
(18, 18, 5, 90, NULL, NULL, NULL, NULL, NULL, 18, 0, '2022-08-10 18:07:09', '2022-08-10 18:07:09'),
(19, 19, 5, 90, NULL, NULL, NULL, NULL, NULL, 19, 0, '2022-08-10 18:09:25', '2022-08-10 18:09:25'),
(20, 20, 5, 90, NULL, NULL, NULL, NULL, NULL, 20, 0, '2022-08-10 18:09:47', '2022-08-10 18:09:47'),
(21, 21, 5, 60, NULL, NULL, NULL, NULL, NULL, 21, 0, '2022-08-10 18:14:01', '2022-08-10 18:14:01'),
(22, 22, 3, 90, NULL, NULL, NULL, NULL, NULL, 22, 0, '2022-08-16 19:10:02', '2022-08-16 19:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `data_rows`
--

CREATE TABLE `data_rows` (
  `id` int(10) UNSIGNED NOT NULL,
  `data_type_id` int(10) UNSIGNED NOT NULL,
  `field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `browse` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `edit` tinyint(1) NOT NULL DEFAULT '1',
  `add` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `details` text COLLATE utf8mb4_unicode_ci,
  `order` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_rows`
--

INSERT INTO `data_rows` (`id`, `data_type_id`, `field`, `type`, `display_name`, `required`, `browse`, `read`, `edit`, `add`, `delete`, `details`, `order`) VALUES
(1, 1, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, '{}', 1),
(2, 1, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(3, 1, 'email', 'text', 'Email', 1, 1, 1, 1, 1, 1, '{}', 3),
(4, 1, 'password', 'password', 'Password', 1, 0, 0, 1, 1, 0, '{}', 4),
(5, 1, 'remember_token', 'text', 'Remember Token', 0, 0, 0, 0, 0, 0, '{}', 5),
(6, 1, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 0, 0, 0, '{}', 6),
(7, 1, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7),
(8, 1, 'avatar', 'image', 'Avatar', 0, 1, 1, 1, 1, 1, '{}', 8),
(9, 1, 'user_belongsto_role_relationship', 'relationship', 'Role', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsTo\",\"column\":\"role_id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"roles\",\"pivot\":\"0\",\"taggable\":\"0\"}', 10),
(10, 1, 'user_belongstomany_role_relationship', 'relationship', 'voyager::seeders.data_rows.roles', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"user_roles\",\"pivot\":\"1\",\"taggable\":\"0\"}', 11),
(11, 1, 'settings', 'hidden', 'Settings', 0, 0, 0, 0, 0, 0, '{}', 12),
(12, 2, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(13, 2, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(14, 2, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3),
(15, 2, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4),
(16, 3, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, '{}', 1),
(17, 3, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(18, 3, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 3),
(19, 3, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 4),
(20, 3, 'display_name', 'text', 'Display Name', 1, 1, 1, 1, 1, 1, '{}', 5),
(21, 1, 'role_id', 'text', 'Role', 0, 1, 1, 1, 1, 1, '{}', 9),
(22, 4, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(23, 4, 'parent_id', 'select_dropdown', 'Parent', 0, 0, 1, 1, 1, 1, '{\"default\":\"\",\"null\":\"\",\"options\":{\"\":\"-- None --\"},\"relationship\":{\"key\":\"id\",\"label\":\"name\"}}', 2),
(24, 4, 'order', 'text', 'Order', 1, 1, 1, 1, 1, 1, '{\"default\":1}', 3),
(25, 4, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 4),
(26, 4, 'slug', 'text', 'Slug', 1, 1, 1, 1, 1, 1, '{\"slugify\":{\"origin\":\"name\"}}', 5),
(27, 4, 'created_at', 'timestamp', 'Created At', 0, 0, 1, 0, 0, 0, NULL, 6),
(28, 4, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 7),
(29, 5, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(30, 5, 'author_id', 'text', 'Author', 1, 0, 1, 1, 0, 1, NULL, 2),
(31, 5, 'category_id', 'text', 'Category', 1, 0, 1, 1, 1, 0, NULL, 3),
(32, 5, 'title', 'text', 'Title', 1, 1, 1, 1, 1, 1, NULL, 4),
(33, 5, 'excerpt', 'text_area', 'Excerpt', 1, 0, 1, 1, 1, 1, NULL, 5),
(34, 5, 'body', 'rich_text_box', 'Body', 1, 0, 1, 1, 1, 1, NULL, 6),
(35, 5, 'image', 'image', 'Post Image', 0, 1, 1, 1, 1, 1, '{\"resize\":{\"width\":\"1000\",\"height\":\"null\"},\"quality\":\"70%\",\"upsize\":true,\"thumbnails\":[{\"name\":\"medium\",\"scale\":\"50%\"},{\"name\":\"small\",\"scale\":\"25%\"},{\"name\":\"cropped\",\"crop\":{\"width\":\"300\",\"height\":\"250\"}}]}', 7),
(36, 5, 'slug', 'text', 'Slug', 1, 0, 1, 1, 1, 1, '{\"slugify\":{\"origin\":\"title\",\"forceUpdate\":true},\"validation\":{\"rule\":\"unique:posts,slug\"}}', 8),
(37, 5, 'meta_description', 'text_area', 'Meta Description', 1, 0, 1, 1, 1, 1, NULL, 9),
(38, 5, 'meta_keywords', 'text_area', 'Meta Keywords', 1, 0, 1, 1, 1, 1, NULL, 10),
(39, 5, 'status', 'select_dropdown', 'Status', 1, 1, 1, 1, 1, 1, '{\"default\":\"DRAFT\",\"options\":{\"PUBLISHED\":\"published\",\"DRAFT\":\"draft\",\"PENDING\":\"pending\"}}', 11),
(40, 5, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 0, 0, 0, NULL, 12),
(41, 5, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 13),
(42, 5, 'seo_title', 'text', 'SEO Title', 0, 1, 1, 1, 1, 1, NULL, 14),
(43, 5, 'featured', 'checkbox', 'Featured', 1, 1, 1, 1, 1, 1, NULL, 15),
(44, 6, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(45, 6, 'author_id', 'text', 'Author', 1, 0, 0, 0, 0, 0, NULL, 2),
(46, 6, 'title', 'text', 'Title', 1, 1, 1, 1, 1, 1, NULL, 3),
(47, 6, 'excerpt', 'text_area', 'Excerpt', 1, 0, 1, 1, 1, 1, NULL, 4),
(48, 6, 'body', 'rich_text_box', 'Body', 1, 0, 1, 1, 1, 1, NULL, 5),
(49, 6, 'slug', 'text', 'Slug', 1, 0, 1, 1, 1, 1, '{\"slugify\":{\"origin\":\"title\"},\"validation\":{\"rule\":\"unique:pages,slug\"}}', 6),
(50, 6, 'meta_description', 'text', 'Meta Description', 1, 0, 1, 1, 1, 1, NULL, 7),
(51, 6, 'meta_keywords', 'text', 'Meta Keywords', 1, 0, 1, 1, 1, 1, NULL, 8),
(52, 6, 'status', 'select_dropdown', 'Status', 1, 1, 1, 1, 1, 1, '{\"default\":\"INACTIVE\",\"options\":{\"INACTIVE\":\"INACTIVE\",\"ACTIVE\":\"ACTIVE\"}}', 9),
(53, 6, 'created_at', 'timestamp', 'Created At', 1, 1, 1, 0, 0, 0, NULL, 10),
(54, 6, 'updated_at', 'timestamp', 'Updated At', 1, 0, 0, 0, 0, 0, NULL, 11),
(55, 6, 'image', 'image', 'Page Image', 0, 1, 1, 1, 1, 1, NULL, 12),
(56, 10, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(57, 10, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(58, 10, 'description', 'text_area', 'Description', 0, 1, 1, 1, 1, 1, '{}', 3),
(59, 10, 'address', 'text_area', 'Address', 0, 1, 1, 1, 1, 1, '{}', 4),
(60, 10, 'phone', 'text', 'Phone', 0, 1, 1, 1, 1, 1, '{}', 5),
(61, 10, 'longitude', 'text', 'Longitude', 0, 1, 1, 1, 1, 1, '{}', 6),
(62, 10, 'latitude', 'text', 'Latitude', 0, 1, 1, 1, 1, 1, '{}', 7),
(63, 10, 'pic_thumbnail_path', 'file', 'Pic Thumbnail Path', 0, 1, 1, 1, 1, 1, '{}', 8),
(64, 10, 'pic_full_path', 'image', 'Pic Full Path', 0, 1, 1, 1, 1, 1, '{}', 9),
(65, 10, 'pin', 'text', 'Pin', 0, 1, 1, 1, 1, 1, '{}', 10),
(66, 10, 'translations', 'text', 'Translations', 0, 1, 1, 1, 1, 1, '{}', 11),
(67, 10, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{}', 12),
(68, 10, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 13),
(69, 10, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 14),
(70, 11, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(71, 11, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(72, 11, 'address', 'text_area', 'Address', 0, 1, 1, 1, 1, 1, '{}', 3),
(73, 11, 'website', 'text', 'Website', 0, 1, 1, 1, 1, 1, '{}', 4),
(74, 11, 'country_phone_ios', 'select_dropdown', 'Country Phone Ios', 0, 1, 1, 1, 1, 1, '{}', 5),
(75, 11, 'phone', 'text', 'Phone', 0, 1, 1, 1, 1, 1, '{}', 6),
(76, 11, 'email', 'text', 'Email', 0, 1, 1, 1, 1, 1, '{}', 7),
(77, 11, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 8),
(78, 11, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 9),
(79, 10, 'company_id', 'text', 'Company Id', 0, 1, 1, 1, 1, 1, '{}', 15),
(80, 10, 'location_belongsto_company_relationship', 'relationship', 'Company', 1, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Models\\\\Company\",\"table\":\"companies\",\"type\":\"belongsTo\",\"column\":\"company_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"categories\",\"pivot\":\"0\",\"taggable\":\"0\"}', 16),
(81, 12, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(82, 12, 'location_id', 'text', 'Location Id', 1, 1, 1, 1, 1, 1, '{}', 2),
(83, 12, 'day_idx', 'select_dropdown', 'Day', 1, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"Sunday\",\"1\":\"Monday\",\"2\":\"Tuesday\",\"3\":\"Wednesday\",\"4\":\"Thursday\",\"5\":\"Friday\",\"6\":\"Saturday\"}}', 3),
(84, 12, 'from_time', 'time', 'From Time', 1, 1, 1, 1, 1, 1, '{}', 4),
(85, 12, 'to_time', 'time', 'To Time', 1, 1, 1, 1, 1, 1, '{}', 5),
(86, 12, 'timeslot_belongsto_location_relationship', 'relationship', 'Location', 0, 1, 1, 1, 1, 1, '{\"model\":\"\\\\App\\\\Models\\\\Location\",\"table\":\"locations\",\"type\":\"belongsTo\",\"column\":\"location_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"categories\",\"pivot\":\"0\",\"taggable\":\"0\"}', 6),
(87, 12, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 6),
(88, 12, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7),
(89, 3, 'book_days_in_adv', 'number', 'Book Days In Adv', 0, 1, 1, 1, 1, 1, '{}', 6),
(90, 10, 'time_settings', 'text', 'Time Settings', 0, 1, 1, 1, 1, 1, '{}', 16),
(91, 13, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(92, 13, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 3),
(93, 13, 'description', 'text', 'Description', 0, 1, 1, 1, 1, 1, '{}', 4),
(94, 13, 'color', 'color', 'Color', 0, 1, 1, 1, 1, 1, '{}', 5),
(95, 13, 'price', 'number', 'Price', 0, 1, 1, 1, 1, 1, '{}', 6),
(97, 13, 'min_capacity', 'number', 'Min Capacity', 0, 1, 1, 1, 1, 1, '{}', 7),
(98, 13, 'max_capacity', 'number', 'Max Capacity', 0, 1, 1, 1, 1, 1, '{}', 8),
(99, 13, 'pic_full_path', 'image', 'Pic Full Path', 0, 1, 1, 1, 1, 1, '{}', 9),
(100, 13, 'status', 'radio_btn', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1001\",\"options\":{\"1001\":\"Active\",\"1002\":\"Suspend\"}}', 10),
(101, 13, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 11),
(102, 13, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 12),
(103, 13, 'translations', 'text', 'Translations', 0, 1, 1, 1, 1, 1, '{}', 13),
(104, 13, 'category_id', 'text', 'Category Id', 1, 1, 1, 1, 1, 1, '{}', 14),
(105, 13, 'service_belongsto_category_relationship', 'relationship', 'Category', 0, 1, 1, 1, 1, 1, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Category\",\"table\":\"categories\",\"type\":\"belongsTo\",\"column\":\"category_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"categories\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2),
(106, 14, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(107, 14, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(108, 14, 'location_id', 'text', 'Location', 0, 1, 1, 1, 1, 1, '{}', 3),
(109, 14, 'translations', 'text', 'Translations', 0, 1, 1, 1, 1, 1, '{}', 4),
(110, 14, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1001\",\"options\":{\"1001\":\"Active\",\"1002\":\"Suspend\"}}', 6),
(111, 14, 'room_belongsto_location_relationship', 'relationship', 'locations', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Models\\\\Location\",\"table\":\"locations\",\"type\":\"belongsTo\",\"column\":\"location_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"categories\",\"pivot\":\"0\",\"taggable\":\"0\"}', 7),
(112, 15, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(113, 15, 'start_time', 'text', 'Start Time', 1, 1, 1, 1, 1, 1, '{}', 2),
(114, 15, 'end_time', 'text', 'End Time', 1, 1, 1, 1, 1, 1, '{}', 3),
(115, 15, 'room_id', 'text', 'Room', 0, 1, 1, 1, 1, 1, '{}', 4),
(117, 15, 'service_id', 'text', 'Service', 0, 1, 1, 1, 1, 1, '{}', 7),
(118, 15, 'package_id', 'text', 'Package', 0, 1, 1, 1, 1, 1, '{}', 9),
(119, 15, 'internal_remark', 'text_area', 'Internal Remark', 0, 1, 1, 1, 1, 1, '{}', 11),
(120, 15, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"pending\",\"options\":{\"approved\":\"Approved\",\"pending\":\"Pending\",\"rejected\":\"Rejected\",\"canceled\":\"Canceled\"}}', 12),
(121, 15, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 13),
(122, 15, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 14),
(123, 15, 'appointment_belongsto_service_relationship', 'relationship', 'Service', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Models\\\\Service\",\"table\":\"services\",\"type\":\"belongsTo\",\"column\":\"service_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"appointments\",\"pivot\":\"0\",\"taggable\":\"0\"}', 8),
(124, 15, 'appointment_belongsto_room_relationship', 'relationship', 'Room', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Models\\\\Room\",\"table\":\"rooms\",\"type\":\"belongsTo\",\"column\":\"room_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"appointments\",\"pivot\":\"0\",\"taggable\":\"0\"}', 10),
(125, 14, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 8),
(126, 14, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 9),
(127, 15, 'user_id', 'text', 'User Id', 0, 1, 1, 1, 1, 1, '{}', 5),
(129, 15, 'appointment_belongsto_user_relationship', 'relationship', 'Customer', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Models\\\\User\",\"table\":\"users\",\"type\":\"belongsTo\",\"column\":\"user_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"appointments\",\"pivot\":\"0\",\"taggable\":\"0\"}', 6),
(130, 3, 'default_price', 'number', 'Default Price', 0, 1, 1, 1, 1, 1, '{}', 7),
(131, 1, 'email_verified_at', 'timestamp', 'Email Verified At', 0, 1, 1, 1, 1, 1, '{}', 6),
(132, 1, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1001\",\"options\":{\"1001\":\"Active\",\"1002\":\"Suspend\"}}', 12),
(133, 14, 'color', 'color', 'Color', 0, 1, 1, 1, 1, 1, '{}', 5);

-- --------------------------------------------------------

--
-- Table structure for table `data_types`
--

CREATE TABLE `data_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_singular` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name_plural` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT '0',
  `server_side` tinyint(4) NOT NULL DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_types`
--

INSERT INTO `data_types` (`id`, `name`, `slug`, `display_name_singular`, `display_name_plural`, `icon`, `model_name`, `policy_name`, `controller`, `description`, `generate_permissions`, `server_side`, `details`, `created_at`, `updated_at`) VALUES
(1, 'users', 'users', 'User', 'Users', 'voyager-person', 'TCG\\Voyager\\Models\\User', 'TCG\\Voyager\\Policies\\UserPolicy', 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController', NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"desc\",\"default_search_key\":null,\"scope\":null}', '2022-07-19 06:56:31', '2022-08-11 06:09:46'),
(2, 'menus', 'menus', 'Menu', 'Menus', 'voyager-list', 'TCG\\Voyager\\Models\\Menu', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(3, 'roles', 'roles', 'Role', 'Roles', 'voyager-lock', 'TCG\\Voyager\\Models\\Role', NULL, 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController', NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"desc\",\"default_search_key\":null,\"scope\":null}', '2022-07-19 06:56:31', '2022-08-05 05:19:07'),
(4, 'categories', 'categories', 'Category', 'Categories', 'voyager-categories', 'TCG\\Voyager\\Models\\Category', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(5, 'posts', 'posts', 'Post', 'Posts', 'voyager-news', 'TCG\\Voyager\\Models\\Post', 'TCG\\Voyager\\Policies\\PostPolicy', '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(6, 'pages', 'pages', 'Page', 'Pages', 'voyager-file-text', 'TCG\\Voyager\\Models\\Page', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(10, 'locations', 'locations', 'Location', 'Locations', 'voyager-location', 'App\\Models\\Location', NULL, NULL, NULL, 1, 0, '{\"order_column\":\"name\",\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2022-07-19 08:16:53', '2022-07-27 03:09:57'),
(11, 'companies', 'companies', 'Company', 'Companies', 'voyager-company', 'App\\Models\\Company', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(12, 'timeslots', 'timeslots', 'Timeslot', 'Timeslots', 'voyager-alarm-clock', 'App\\Models\\Timeslot', NULL, NULL, NULL, 1, 0, '{\"order_column\":\"day_idx\",\"order_display_column\":\"day_idx\",\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-21 07:37:54', '2022-07-27 08:44:11'),
(13, 'services', 'services', 'Service', 'Services', NULL, 'App\\Models\\Service', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-27 07:48:19', '2022-08-05 02:08:38'),
(14, 'rooms', 'rooms', 'Room', 'Rooms', NULL, 'App\\Models\\Room', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-29 10:00:57', '2022-08-17 03:22:20'),
(15, 'appointments', 'appointments', 'Appointment', 'Appointments', NULL, 'App\\Models\\Appointment', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-29 10:14:29', '2022-08-05 02:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` decimal(10,0) DEFAULT NULL,
  `latitude` decimal(10,0) DEFAULT NULL,
  `pic_thumbnail_path` varchar(999) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_full_path` varchar(999) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin` mediumtext COLLATE utf8mb4_unicode_ci,
  `translations` text COLLATE utf8mb4_unicode_ci,
  `status` int(11) DEFAULT '1001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `time_settings` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `description`, `address`, `phone`, `longitude`, `latitude`, `pic_thumbnail_path`, `pic_full_path`, `pin`, `translations`, `status`, `created_at`, `updated_at`, `company_id`, `time_settings`) VALUES
(1, 'San Po Kong', NULL, NULL, NULL, NULL, NULL, '[{\"download_link\":\"locations\\/July2022\\/sRgfslUNF4Pzg2S90fx8.png\",\"original_name\":\"Original-on-Transparent_80.png\"}]', 'locations/July2022/zkGT3Qy64PdOSnXZx1yG.jpg', NULL, NULL, NULL, '2022-07-19 08:24:00', '2022-07-27 03:21:25', 1, '{time_slot_steps: 30, use_duration_for_time_slot: false}');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, 'manager', '2022-07-19 07:01:13', '2022-07-19 07:01:13');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '_self',
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parameters` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `menu_id`, `title`, `url`, `target`, `icon_class`, `color`, `parent_id`, `order`, `created_at`, `updated_at`, `route`, `parameters`) VALUES
(1, 1, 'Dashboard', '', '_self', 'voyager-boat', NULL, NULL, 1, '2022-07-19 06:56:31', '2022-07-19 06:56:31', 'voyager.dashboard', NULL),
(2, 1, 'Media', '', '_self', 'voyager-images', NULL, NULL, 4, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.media.index', NULL),
(3, 1, 'Users', '', '_self', 'voyager-person', NULL, NULL, 3, '2022-07-19 06:56:31', '2022-07-19 06:56:31', 'voyager.users.index', NULL),
(4, 1, 'Roles', '', '_self', 'voyager-lock', NULL, NULL, 2, '2022-07-19 06:56:31', '2022-07-19 06:56:31', 'voyager.roles.index', NULL),
(5, 1, 'Tools', '', '_self', 'voyager-tools', NULL, NULL, 12, '2022-07-19 06:56:31', '2022-08-05 02:11:06', NULL, NULL),
(6, 1, 'Menu Builder', '', '_self', 'voyager-list', NULL, 5, 1, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.menus.index', NULL),
(7, 1, 'Database', '', '_self', 'voyager-data', NULL, 5, 2, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.database.index', NULL),
(8, 1, 'Compass', '', '_self', 'voyager-compass', NULL, 5, 3, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.compass.index', NULL),
(9, 1, 'BREAD', '', '_self', 'voyager-bread', NULL, 5, 4, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.bread.index', NULL),
(10, 1, 'Settings', '', '_self', 'voyager-settings', NULL, NULL, 13, '2022-07-19 06:56:31', '2022-08-05 02:11:06', 'voyager.settings.index', NULL),
(11, 1, 'Categories', '', '_self', 'voyager-categories', NULL, NULL, 6, '2022-07-19 06:56:31', '2022-08-05 02:11:06', 'voyager.categories.index', NULL),
(12, 1, 'Locations', '', '_self', 'voyager-location', '#000000', NULL, 9, '2022-07-19 06:56:31', '2022-08-05 02:11:06', 'voyager.locations.index', 'null'),
(16, 1, 'Company', '', '_self', 'voyager-company', '#000000', NULL, 8, '2022-07-19 08:44:07', '2022-08-05 02:11:06', 'voyager.companies.index', NULL),
(17, 2, 'Appointments', '', '_self', 'voyager-location', '#000000', NULL, 1, '2022-07-19 14:05:48', '2022-08-05 02:15:30', 'voyager.appointments.index', 'null'),
(18, 1, 'Timeslots', '', '_self', 'voyager-alarm-clock', NULL, NULL, 11, '2022-07-21 07:37:54', '2022-08-05 02:11:06', 'voyager.timeslots.index', NULL),
(19, 1, 'Services', '', '_self', 'voyager-gift', '#000000', NULL, 7, '2022-07-27 07:48:19', '2022-08-05 02:20:19', 'voyager.services.index', 'null'),
(20, 1, 'Rooms', '', '_self', 'voyager-shop', '#000000', NULL, 10, '2022-07-29 10:00:57', '2022-08-05 02:18:41', 'voyager.rooms.index', 'null'),
(21, 1, 'Appointments', '', '_self', 'voyager-calendar', '#000000', NULL, 5, '2022-07-29 10:14:29', '2022-08-05 02:18:17', 'voyager.appointments.index', 'null'),
(22, 2, 'Ping Pong Table', '', '_self', 'voyager-shop', '#000000', NULL, 14, '2022-08-05 02:14:16', '2022-08-05 02:14:16', 'voyager.rooms.index', NULL),
(23, 2, 'Timeslots', '', '_self', 'voyager-calendar', '#000000', NULL, 15, '2022-08-05 02:15:07', '2022-08-05 02:15:07', 'voyager.timeslots.index', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_01_000000_add_voyager_user_fields', 1),
(4, '2016_01_01_000000_create_data_types_table', 1),
(5, '2016_05_19_173453_create_menu_table', 1),
(6, '2016_10_21_190000_create_roles_table', 1),
(7, '2016_10_21_190000_create_settings_table', 1),
(8, '2016_11_30_135954_create_permission_table', 1),
(9, '2016_11_30_141208_create_permission_role_table', 1),
(10, '2016_12_26_201236_data_types__add__server_side', 1),
(11, '2017_01_13_000000_add_route_to_menu_items_table', 1),
(12, '2017_01_14_005015_create_translations_table', 1),
(13, '2017_01_15_000000_make_table_name_nullable_in_permissions_table', 1),
(14, '2017_03_06_000000_add_controller_to_data_types_table', 1),
(15, '2017_04_21_000000_add_order_to_data_rows_table', 1),
(16, '2017_07_05_210000_add_policyname_to_data_types_table', 1),
(17, '2017_08_05_000000_add_group_to_settings_table', 1),
(18, '2017_11_26_013050_add_user_role_relationship', 1),
(19, '2017_11_26_015000_create_user_roles_table', 1),
(20, '2018_03_11_000000_add_user_settings', 1),
(21, '2018_03_14_000000_add_details_to_data_types_table', 1),
(22, '2018_03_16_000000_make_settings_value_nullable', 1),
(23, '2019_08_19_000000_create_failed_jobs_table', 1),
(24, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(25, '2016_01_01_000000_create_pages_table', 2),
(26, '2016_01_01_000000_create_posts_table', 2),
(27, '2016_02_15_204651_create_categories_table', 2),
(28, '2017_04_11_000000_alter_post_nullable_fields_table', 2),
(29, '2022_07_27_023159_create_testing_table', 3),
(30, '2022_08_04_040850_add_unique_constraint_to_appointments_table', 3),
(31, '2022_08_11_020658_create_jobs_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `order_total` double NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `order_status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_status` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discount` double DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `order_date`, `order_total`, `customer_id`, `order_status`, `payment_status`, `user_id`, `created_at`, `updated_at`, `discount`) VALUES
(1, '62ec9305414d5', '2022-08-05', 75, 3, 'confirmed', 'paid', 3, '2022-08-05 02:48:21', '2022-08-05 02:48:21', 0),
(2, '62ec937bce083', '2022-08-05', 100, 3, 'confirmed', 'pending', 3, '2022-08-05 02:50:19', '2022-08-05 02:50:19', 0),
(3, '62ec95b6d6c06', '2022-08-05', 75, 3, 'confirmed', 'pending', 3, '2022-08-05 02:59:50', '2022-08-05 02:59:50', 0),
(4, '62eca3b29f1eb', '2022-08-05', 75, 3, 'confirmed', 'pending', 3, '2022-08-05 03:59:30', '2022-08-05 03:59:30', 0),
(5, '62ee1df818358', '2022-08-06', 90, 3, 'confirmed', 'pending', 3, '2022-08-06 14:53:28', '2022-08-06 14:53:28', 0),
(6, '62ee1e514c8c9', '2022-08-06', 90, 3, 'confirmed', 'pending', 3, '2022-08-06 14:54:57', '2022-08-06 14:54:57', 0),
(7, '62f074730cb39', '2022-08-08', 90, 3, 'confirmed', 'pending', 3, '2022-08-08 01:26:59', '2022-08-08 01:26:59', 0),
(8, '62f0de2002566', '2022-08-08', 150, 3, 'confirmed', 'paid', 3, '2022-08-08 08:57:52', '2022-08-08 08:57:52', 0),
(9, '62f0deab1ab4e', '2022-08-08', 150, 3, 'confirmed', 'pending', 3, '2022-08-08 09:00:11', '2022-08-08 09:00:11', 0),
(10, '62f0dfda5008a', '2022-08-08', 60, 3, 'confirmed', 'pending', 3, '2022-08-08 09:05:14', '2022-08-08 09:05:14', 0),
(11, '62f0e0fd637f4', '2022-08-08', 90, 3, 'confirmed', 'pending', 3, '2022-08-08 09:10:05', '2022-08-08 09:10:05', 0),
(12, '62f1132e18d06', '2022-08-08', 90, 3, 'confirmed', 'pending', 3, '2022-08-08 12:44:14', '2022-08-08 12:44:14', 0),
(13, '62f1c6006c241', '2022-08-09', 90, 5, 'pending', 'pending', 5, '2022-08-09 01:27:12', '2022-08-09 01:27:12', 0),
(14, '62f1dd42879fc', '2022-08-09', 60, 5, 'pending', 'pending', 5, '2022-08-09 03:06:26', '2022-08-09 03:06:26', 0),
(15, '62f2c6b34fb45', '2022-08-09', 60, 5, 'pending', 'paid', 5, '2022-08-09 19:42:27', '2022-08-09 19:42:27', 0),
(16, '62f3e55c37e8a', '2022-08-10', 90, 5, 'pending', 'pending', 5, '2022-08-10 16:05:32', '2022-08-10 16:05:32', 0),
(17, '62f4007d2b258', '2022-08-10', 90, 5, 'pending', 'pending', 5, '2022-08-10 18:01:17', '2022-08-10 18:01:17', 0),
(18, '62f401dd62876', '2022-08-10', 90, 5, 'pending', 'pending', 5, '2022-08-10 18:07:09', '2022-08-10 18:07:09', 0),
(19, '62f40265b0a0a', '2022-08-10', 90, 5, 'pending', 'pending', 5, '2022-08-10 18:09:25', '2022-08-10 18:09:25', 0),
(20, '62f4027b2ca00', '2022-08-10', 90, 5, 'pending', 'pending', 5, '2022-08-10 18:09:47', '2022-08-10 18:09:47', 0),
(21, '62f40379a6cea', '2022-08-10', 60, 5, 'pending', 'pending', 5, '2022-08-10 18:14:01', '2022-08-10 18:14:01', 0),
(22, '62fbf99a063d8', '2022-08-16', 90, 3, 'pending', 'pending', 3, '2022-08-16 19:10:02', '2022-08-16 19:10:02', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `order_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_price` double NOT NULL,
  `discounted_price` double DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `booking_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `order_type`, `order_description`, `original_price`, `discounted_price`, `coupon_id`, `created_at`, `updated_at`, `booking_id`) VALUES
(1, 1, 'booking', '{\"start_time\":\"2022-08-06 14:00:00\",\"end_time\":\"2022-08-06 15:30:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-05T03:48:21.000000Z\",\"created_at\":\"2022-08-05T03:48:21.000000Z\",\"id\":1}', 75, 75, NULL, '2022-08-05 02:48:21', '2022-08-05 02:48:21', 1),
(2, 2, 'booking', '{\"start_time\":\"2022-08-07 14:00:00\",\"end_time\":\"2022-08-07 16:00:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-05T03:50:19.000000Z\",\"created_at\":\"2022-08-05T03:50:19.000000Z\",\"id\":2}', 100, 100, NULL, '2022-08-05 02:50:19', '2022-08-05 02:50:19', 2),
(3, 3, 'booking', '{\"start_time\":\"2022-08-07 17:30:00\",\"end_time\":\"2022-08-07 19:00:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-05T03:59:50.000000Z\",\"created_at\":\"2022-08-05T03:59:50.000000Z\",\"id\":3}', 75, 75, NULL, '2022-08-05 02:59:50', '2022-08-05 02:59:50', 3),
(4, 4, 'booking', '{\"start_time\":\"2022-08-07 17:30:00\",\"end_time\":\"2022-08-07 19:00:00\",\"room_id\":2,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-05T04:59:30.000000Z\",\"created_at\":\"2022-08-05T04:59:30.000000Z\",\"id\":4}', 75, 75, NULL, '2022-08-05 03:59:30', '2022-08-05 03:59:30', 4),
(5, 5, 'booking', '{\"start_time\":\"2022-08-06 11:30:00\",\"end_time\":\"2022-08-06 13:00:00\",\"room_id\":2,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-06T07:53:28.000000Z\",\"created_at\":\"2022-08-06T07:53:28.000000Z\",\"id\":5}', 90, 90, NULL, '2022-08-06 14:53:28', '2022-08-06 14:53:28', 5),
(6, 6, 'booking', '{\"start_time\":\"2022-08-06 11:30:00\",\"end_time\":\"2022-08-06 13:00:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-06T07:54:57.000000Z\",\"created_at\":\"2022-08-06T07:54:57.000000Z\",\"id\":6}', 90, 90, NULL, '2022-08-06 14:54:57', '2022-08-06 14:54:57', 6),
(7, 7, 'booking', '{\"start_time\":\"2022-08-11 14:00:00\",\"end_time\":\"2022-08-11 15:30:00\",\"room_id\":2,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T02:26:59.000000Z\",\"created_at\":\"2022-08-08T02:26:59.000000Z\",\"id\":7}', 90, 90, NULL, '2022-08-08 01:26:59', '2022-08-08 01:26:59', 7),
(8, 8, 'booking', '{\"start_time\":\"2022-08-09 09:30:00\",\"end_time\":\"2022-08-09 12:00:00\",\"room_id\":2,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T09:57:52.000000Z\",\"created_at\":\"2022-08-08T09:57:52.000000Z\",\"id\":8}', 150, 150, NULL, '2022-08-08 08:57:52', '2022-08-08 08:57:52', 8),
(9, 9, 'booking', '{\"start_time\":\"2022-08-09 09:30:00\",\"end_time\":\"2022-08-09 12:00:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T10:00:11.000000Z\",\"created_at\":\"2022-08-08T10:00:11.000000Z\",\"id\":9}', 150, 150, NULL, '2022-08-08 09:00:11', '2022-08-08 09:00:11', 9),
(10, 10, 'booking', '{\"start_time\":\"2022-08-09 15:00:00\",\"end_time\":\"2022-08-09 16:00:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T10:05:14.000000Z\",\"created_at\":\"2022-08-08T10:05:14.000000Z\",\"id\":10}', 60, 60, NULL, '2022-08-08 09:05:14', '2022-08-08 09:05:14', 10),
(11, 11, 'booking', '{\"start_time\":\"2022-08-12 11:30:00\",\"end_time\":\"2022-08-12 13:00:00\",\"room_id\":2,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T10:10:05.000000Z\",\"created_at\":\"2022-08-08T10:10:05.000000Z\",\"id\":11}', 90, 90, NULL, '2022-08-08 09:10:05', '2022-08-08 09:10:05', 11),
(12, 12, 'booking', '{\"start_time\":\"2022-08-15 15:00:00\",\"end_time\":\"2022-08-15 16:30:00\",\"room_id\":1,\"user_id\":3,\"service_id\":1,\"status\":\"approved\",\"updated_at\":\"2022-08-08T13:44:14.000000Z\",\"created_at\":\"2022-08-08T13:44:14.000000Z\",\"id\":12}', 90, 90, NULL, '2022-08-08 12:44:14', '2022-08-08 12:44:14', 12),
(13, 13, 'booking', '{\"start_time\":\"2022-08-15 23:30:00\",\"end_time\":\"2022-08-16 01:00:00\",\"room_id\":1,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-09T02:27:12.000000Z\",\"created_at\":\"2022-08-09T02:27:12.000000Z\",\"id\":13}', 90, 90, NULL, '2022-08-09 01:27:12', '2022-08-09 01:27:12', 13),
(14, 14, 'booking', '{\"start_time\":\"2022-08-16 01:00:00\",\"end_time\":\"2022-08-16 02:00:00\",\"room_id\":2,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-09T04:06:26.000000Z\",\"created_at\":\"2022-08-09T04:06:26.000000Z\",\"id\":14}', 60, 60, NULL, '2022-08-09 03:06:26', '2022-08-09 03:06:26', 14),
(15, 15, 'booking', '{\"start_time\":\"2022-08-16 01:00:00\",\"end_time\":\"2022-08-16 02:00:00\",\"room_id\":1,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-09T20:42:27.000000Z\",\"created_at\":\"2022-08-09T20:42:27.000000Z\",\"id\":15}', 60, 60, NULL, '2022-08-09 19:42:27', '2022-08-09 19:42:27', 15),
(16, 16, 'booking', '{\"start_time\":\"2022-08-18 11:00:00\",\"end_time\":\"2022-08-18 12:30:00\",\"room_id\":1,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T17:05:32.000000Z\",\"created_at\":\"2022-08-10T17:05:32.000000Z\",\"id\":16}', 90, 90, NULL, '2022-08-10 16:05:32', '2022-08-10 16:05:32', 16),
(17, 17, 'booking', '{\"start_time\":\"2022-08-18 11:00:00\",\"end_time\":\"2022-08-18 12:30:00\",\"room_id\":2,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T19:01:17.000000Z\",\"created_at\":\"2022-08-10T19:01:17.000000Z\",\"id\":17}', 90, 90, NULL, '2022-08-10 18:01:17', '2022-08-10 18:01:17', 17),
(18, 18, 'booking', '{\"start_time\":\"2022-08-20 13:00:00\",\"end_time\":\"2022-08-20 14:30:00\",\"room_id\":2,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T19:07:09.000000Z\",\"created_at\":\"2022-08-10T19:07:09.000000Z\",\"id\":18}', 90, 90, NULL, '2022-08-10 18:07:09', '2022-08-10 18:07:09', 18),
(19, 19, 'booking', '{\"start_time\":\"2022-08-20 13:00:00\",\"end_time\":\"2022-08-20 14:30:00\",\"room_id\":1,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T19:09:25.000000Z\",\"created_at\":\"2022-08-10T19:09:25.000000Z\",\"id\":19}', 90, 90, NULL, '2022-08-10 18:09:25', '2022-08-10 18:09:25', 19),
(20, 20, 'booking', '{\"start_time\":\"2022-08-19 13:30:00\",\"end_time\":\"2022-08-19 15:00:00\",\"room_id\":2,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T19:09:47.000000Z\",\"created_at\":\"2022-08-10T19:09:47.000000Z\",\"id\":20}', 90, 90, NULL, '2022-08-10 18:09:47', '2022-08-10 18:09:47', 20),
(21, 21, 'booking', '{\"start_time\":\"2022-08-20 15:00:00\",\"end_time\":\"2022-08-20 16:00:00\",\"room_id\":1,\"user_id\":5,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-10T19:14:01.000000Z\",\"created_at\":\"2022-08-10T19:14:01.000000Z\",\"id\":21}', 60, 60, NULL, '2022-08-10 18:14:01', '2022-08-10 18:14:01', 21),
(22, 22, 'booking', '{\"start_time\":\"2022-08-17 13:30:00\",\"end_time\":\"2022-08-17 15:00:00\",\"room_id\":3,\"user_id\":3,\"service_id\":1,\"status\":\"pending\",\"updated_at\":\"2022-08-16T20:10:02.000000Z\",\"created_at\":\"2022-08-16T20:10:02.000000Z\",\"id\":22}', 90, 90, NULL, '2022-08-16 19:10:02', '2022-08-16 19:10:02', 22);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `body` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `status` enum('ACTIVE','INACTIVE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INACTIVE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `author_id`, `title`, `excerpt`, `body`, `image`, `slug`, `meta_description`, `meta_keywords`, `status`, `created_at`, `updated_at`) VALUES
(1, 0, 'Hello World', 'Hang the jib grog grog blossom grapple dance the hempen jig gangway pressgang bilge rat to go on account lugger. Nelsons folly gabion line draught scallywag fire ship gaff fluke fathom case shot. Sea Legs bilge rat sloop matey gabion long clothes run a shot across the bow Gold Road cog league.', '<p>Hello World. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>\n<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>', 'pages/page1.jpg', 'hello-world', 'Yar Meta Description', 'Keyword1, Keyword2', 'ACTIVE', '2022-07-19 06:56:31', '2022-07-19 06:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `payment_date_time` datetime NOT NULL,
  `status` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `entity` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `payment_date_time`, `status`, `payment_method`, `gateway`, `parent_id`, `entity`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 75, '2022-08-05 03:48:21', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-05 02:48:21', '2022-08-05 02:48:21'),
(2, 2, 100, '2022-08-05 03:50:19', 'pending', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-05 02:50:19', '2022-08-05 02:50:19'),
(3, 3, 75, '2022-08-05 03:59:50', 'pending', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-05 02:59:50', '2022-08-05 02:59:50'),
(4, 4, 75, '2022-08-05 04:59:30', 'pending', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-05 03:59:30', '2022-08-05 03:59:30'),
(5, 5, 90, '2022-08-06 15:53:28', 'pending', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-06 14:53:28', '2022-08-06 14:53:28'),
(6, 6, 90, '2022-08-06 15:54:57', 'pending', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-06 14:54:57', '2022-08-06 14:54:57'),
(7, 7, 90, '2022-08-08 02:26:59', 'pending', 'electronic', 'wechatpayHK', NULL, 'appointment', NULL, '2022-08-08 01:26:59', '2022-08-08 01:26:59'),
(8, 8, 150, '2022-08-08 09:57:52', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-08 08:57:52', '2022-08-08 08:57:52'),
(9, 9, 150, '2022-08-08 10:00:11', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-08 09:00:11', '2022-08-08 09:00:11'),
(10, 10, 60, '2022-08-08 10:05:14', 'paid', 'electronic', 'octopus', NULL, 'appointment', NULL, '2022-08-08 09:05:14', '2022-08-08 09:05:14'),
(11, 11, 90, '2022-08-08 10:10:05', 'pending', 'electronic', 'alipayHK', NULL, 'appointment', NULL, '2022-08-08 09:10:05', '2022-08-08 09:10:05'),
(12, 12, 90, '2022-08-08 13:44:14', 'partially', 'electronic', 'alipayHK', NULL, 'appointment', NULL, '2022-08-08 12:44:14', '2022-08-08 12:44:14'),
(13, 13, 90, '2022-08-09 02:27:12', 'pending', 'electronic', 'wechatpayHK', NULL, 'appointment', NULL, '2022-08-09 01:27:12', '2022-08-09 01:27:12'),
(14, 14, 60, '2022-08-09 04:06:26', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-09 03:06:26', '2022-08-09 03:06:26'),
(15, 15, 60, '2022-08-09 20:42:27', 'pending', 'electronic', 'alipayHK', NULL, 'appointment', NULL, '2022-08-09 19:42:27', '2022-08-09 19:42:27'),
(16, 16, 90, '2022-08-10 17:05:32', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-10 16:05:32', '2022-08-10 16:05:32'),
(17, 17, 90, '2022-08-10 19:01:17', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-10 18:01:17', '2022-08-10 18:01:17'),
(18, 18, 90, '2022-08-10 19:07:09', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-10 18:07:09', '2022-08-10 18:07:09'),
(19, 19, 90, '2022-08-10 19:09:25', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-10 18:09:25', '2022-08-10 18:09:25'),
(20, 20, 90, '2022-08-10 19:09:47', 'pending', 'electronic', 'payme', NULL, 'appointment', NULL, '2022-08-10 18:09:47', '2022-08-10 18:09:47'),
(21, 21, 60, '2022-08-10 19:14:01', 'pending', 'electronic', 'pps', NULL, 'appointment', NULL, '2022-08-10 18:14:01', '2022-08-10 18:14:01'),
(22, 22, 90, '2022-08-16 20:10:02', 'pending', 'electronic', 'fps', NULL, 'appointment', NULL, '2022-08-16 19:10:02', '2022-08-16 19:10:02');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `key`, `table_name`, `created_at`, `updated_at`) VALUES
(1, 'browse_admin', NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, 'browse_bread', NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(3, 'browse_database', NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(4, 'browse_media', NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(5, 'browse_compass', NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(6, 'browse_menus', 'menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(7, 'read_menus', 'menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(8, 'edit_menus', 'menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(9, 'add_menus', 'menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(10, 'delete_menus', 'menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(11, 'browse_roles', 'roles', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(12, 'read_roles', 'roles', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(13, 'edit_roles', 'roles', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(14, 'add_roles', 'roles', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(15, 'delete_roles', 'roles', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(16, 'browse_users', 'users', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(17, 'read_users', 'users', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(18, 'edit_users', 'users', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(19, 'add_users', 'users', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(20, 'delete_users', 'users', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(21, 'browse_settings', 'settings', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(22, 'read_settings', 'settings', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(23, 'edit_settings', 'settings', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(24, 'add_settings', 'settings', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(25, 'delete_settings', 'settings', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(26, 'browse_categories', 'categories', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(27, 'read_categories', 'categories', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(28, 'edit_categories', 'categories', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(29, 'add_categories', 'categories', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(30, 'delete_categories', 'categories', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(31, 'browse_posts', 'posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(32, 'read_posts', 'posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(33, 'edit_posts', 'posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(34, 'add_posts', 'posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(35, 'delete_posts', 'posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(36, 'browse_pages', 'pages', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(37, 'read_pages', 'pages', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(38, 'edit_pages', 'pages', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(39, 'add_pages', 'pages', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(40, 'delete_pages', 'pages', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(41, 'browse_locations', 'locations', '2022-07-19 08:16:54', '2022-07-19 08:16:54'),
(42, 'read_locations', 'locations', '2022-07-19 08:16:54', '2022-07-19 08:16:54'),
(43, 'edit_locations', 'locations', '2022-07-19 08:16:54', '2022-07-19 08:16:54'),
(44, 'add_locations', 'locations', '2022-07-19 08:16:54', '2022-07-19 08:16:54'),
(45, 'delete_locations', 'locations', '2022-07-19 08:16:54', '2022-07-19 08:16:54'),
(46, 'browse_companies', 'companies', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(47, 'read_companies', 'companies', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(48, 'edit_companies', 'companies', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(49, 'add_companies', 'companies', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(50, 'delete_companies', 'companies', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(51, 'browse_timeslots', 'timeslots', '2022-07-21 07:37:54', '2022-07-21 07:37:54'),
(52, 'read_timeslots', 'timeslots', '2022-07-21 07:37:54', '2022-07-21 07:37:54'),
(53, 'edit_timeslots', 'timeslots', '2022-07-21 07:37:54', '2022-07-21 07:37:54'),
(54, 'add_timeslots', 'timeslots', '2022-07-21 07:37:54', '2022-07-21 07:37:54'),
(55, 'delete_timeslots', 'timeslots', '2022-07-21 07:37:54', '2022-07-21 07:37:54'),
(56, 'browse_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19'),
(57, 'read_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19'),
(58, 'edit_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19'),
(59, 'add_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19'),
(60, 'delete_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19'),
(61, 'browse_rooms', 'rooms', '2022-07-29 10:00:57', '2022-07-29 10:00:57'),
(62, 'read_rooms', 'rooms', '2022-07-29 10:00:57', '2022-07-29 10:00:57'),
(63, 'edit_rooms', 'rooms', '2022-07-29 10:00:57', '2022-07-29 10:00:57'),
(64, 'add_rooms', 'rooms', '2022-07-29 10:00:57', '2022-07-29 10:00:57'),
(65, 'delete_rooms', 'rooms', '2022-07-29 10:00:57', '2022-07-29 10:00:57'),
(66, 'browse_appointments', 'appointments', '2022-07-29 10:14:29', '2022-07-29 10:14:29'),
(67, 'read_appointments', 'appointments', '2022-07-29 10:14:29', '2022-07-29 10:14:29'),
(68, 'edit_appointments', 'appointments', '2022-07-29 10:14:29', '2022-07-29 10:14:29'),
(69, 'add_appointments', 'appointments', '2022-07-29 10:14:29', '2022-07-29 10:14:29'),
(70, 'delete_appointments', 'appointments', '2022-07-29 10:14:29', '2022-07-29 10:14:29');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 3),
(1, 4),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(26, 3),
(27, 1),
(27, 3),
(28, 1),
(28, 3),
(29, 1),
(29, 3),
(30, 1),
(30, 3),
(31, 1),
(31, 3),
(32, 1),
(32, 3),
(33, 1),
(33, 3),
(34, 1),
(34, 3),
(35, 1),
(35, 3),
(36, 1),
(36, 3),
(37, 1),
(37, 3),
(38, 1),
(38, 3),
(39, 1),
(39, 3),
(40, 1),
(40, 3),
(41, 1),
(41, 3),
(42, 1),
(42, 3),
(43, 1),
(43, 3),
(44, 1),
(44, 3),
(45, 1),
(45, 3),
(46, 1),
(46, 3),
(47, 1),
(47, 3),
(48, 1),
(48, 3),
(49, 1),
(49, 3),
(50, 1),
(50, 3),
(51, 1),
(51, 3),
(52, 1),
(52, 3),
(53, 1),
(53, 3),
(54, 1),
(54, 3),
(55, 1),
(55, 3),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(61, 3),
(62, 1),
(62, 3),
(63, 1),
(63, 3),
(64, 1),
(64, 3),
(65, 1),
(65, 3),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'MyApp', '1a5a87f8c1629f931b8335a0f36ab0e6dda8c4761c38a155a9c6aaa42a40ea58', '[\"*\"]', NULL, '2022-07-26 16:43:03', '2022-07-26 16:43:03'),
(2, 'App\\Models\\User', 1, 'MyApp', 'c1fd57b307125227d68d870c086c24f97a4626239e7a3e2c1ede374dbf1d57f1', '[\"*\"]', NULL, '2022-07-27 01:03:09', '2022-07-27 01:03:09'),
(3, 'App\\Models\\User', 1, 'MyApp', '812a1495b419d363d9b0a1b54a09585d8d2ebd4727f8ae180c5dcab9fcf2fe16', '[\"*\"]', NULL, '2022-07-27 01:18:34', '2022-07-27 01:18:34'),
(4, 'App\\Models\\User', 1, 'MyApp', 'b1660e433bbb07f0cf1d2b00a616f09cedf7f13e6366e79a0bfeea1e6688b88c', '[\"*\"]', NULL, '2022-07-27 01:25:39', '2022-07-27 01:25:39'),
(5, 'App\\Models\\User', 1, 'MyApp', 'e2069fc9390322a1a5da755747cd4431f456d4dad3491e06149876f555659b95', '[\"*\"]', NULL, '2022-07-28 09:08:28', '2022-07-28 09:08:28'),
(6, 'App\\Models\\User', 2, 'MyApp', '89a16758d15da9a6fb18a9b0339e145c916e7548e450659d65c0bb1f225280b2', '[\"*\"]', '2022-07-28 11:30:56', '2022-07-28 09:14:10', '2022-07-28 11:30:56'),
(7, 'App\\Models\\User', 3, 'MyApp', '72c84fb28c7065f1708ce717cbba2f35737d5e827cd15b1fd243c5d96b214298', '[\"*\"]', '2022-08-02 01:28:22', '2022-07-29 03:22:55', '2022-08-02 01:28:22'),
(8, 'App\\Models\\User', 3, 'MyApp', '8cb4226b6a9688ad41498e246f50e7d14a71035de737e976317a5e569a84db17', '[\"*\"]', '2022-08-02 04:38:52', '2022-08-02 04:22:42', '2022-08-02 04:38:52'),
(9, 'App\\Models\\User', 3, 'MyApp', '58c1a80b901f68c6af1bcd70c9138fd9c6be067ef39e75861283930ca35702a0', '[\"*\"]', '2022-08-02 05:17:32', '2022-08-02 05:17:21', '2022-08-02 05:17:32'),
(10, 'App\\Models\\User', 3, 'MyApp', '175da41429f8bc1b58ef519b49fc28e294896cc3bed20819733230aabf99d76e', '[\"*\"]', '2022-08-03 12:53:11', '2022-08-02 05:28:58', '2022-08-03 12:53:11'),
(11, 'App\\Models\\User', 3, 'MyApp', '540d1dd7dc7679e5d9ae222f813ebe90afc8f86420003e5ac09c3b56893d412b', '[\"*\"]', NULL, '2022-08-03 16:42:37', '2022-08-03 16:42:37'),
(12, 'App\\Models\\User', 3, 'MyApp', '1b8039b37ec99578b928a7043c424df3137074495de174f3a1eed4ade01cf7c1', '[\"*\"]', '2022-08-03 16:49:28', '2022-08-03 16:45:44', '2022-08-03 16:49:28'),
(13, 'App\\Models\\User', 3, 'MyApp', '6b75fc72bbb393f113cbee4afb9bcf51f5446d53628efc819760b6aebd9195f7', '[\"*\"]', NULL, '2022-08-03 16:50:44', '2022-08-03 16:50:44'),
(14, 'App\\Models\\User', 3, 'MyApp', '5013a9b3e97040c958926acfae8545f2f8bf81dbf784ddd7c83ddbe4ddfb0e48', '[\"*\"]', NULL, '2022-08-03 16:51:55', '2022-08-03 16:51:55'),
(15, 'App\\Models\\User', 3, 'MyApp', '10f97ec225fdf5f87ede7338aeea0e353b3687f4a6c07d5acc71d1f3c707d9c0', '[\"*\"]', NULL, '2022-08-03 16:52:37', '2022-08-03 16:52:37'),
(16, 'App\\Models\\User', 3, 'MyApp', '72670c192735ed82d5d42ffdf568de78d7559cc76d24f9629a0e61c1c861ed92', '[\"*\"]', '2022-08-04 08:16:58', '2022-08-03 16:53:06', '2022-08-04 08:16:58'),
(17, 'App\\Models\\User', 3, 'MyApp', '55e949c6ce036f696447a81c6a1655a15862e9ba86cf704a56a30d9983fd1e56', '[\"*\"]', '2022-08-05 05:21:54', '2022-08-04 08:30:38', '2022-08-05 05:21:54'),
(18, 'App\\Models\\User', 4, 'MyApp', '09d1045a35650d4229c57efb1a7462e48b4255c17a604bdb8a9ede81e7610131', '[\"*\"]', '2022-08-05 08:29:38', '2022-08-05 08:29:11', '2022-08-05 08:29:38'),
(19, 'App\\Models\\User', 3, 'MyApp', '41c6ada9fe12101fc2630a8287caa9d2c1f6c51a2c6018e507d0b118f85842ce', '[\"*\"]', '2022-08-05 10:40:34', '2022-08-05 08:30:19', '2022-08-05 10:40:34'),
(20, 'App\\Models\\User', 3, 'MyApp', 'dd8eaeaaaf19681c0c9f49f84375ccf541514898b4bbf9f6af6f99c81344a26a', '[\"*\"]', '2022-08-07 14:45:01', '2022-08-05 11:20:48', '2022-08-07 14:45:01'),
(21, 'App\\Models\\User', 3, 'MyApp', '6e0e055971fa930de8b2900b32b94c0ed524cd01c4668ecd20abab99a95f7ed6', '[\"*\"]', '2022-08-07 14:59:22', '2022-08-07 14:58:34', '2022-08-07 14:59:22'),
(22, 'App\\Models\\User', 3, 'MyApp', 'a55fe51fcdf9afad276a1c6006d3644dff76dba6ae131bc04caf8c176947fe0f', '[\"*\"]', '2022-08-08 01:05:37', '2022-08-07 14:59:51', '2022-08-08 01:05:37'),
(23, 'App\\Models\\User', 3, 'MyApp', '2fd188a08aa9204a35db06aea7bcb2f6740a3a30d0d9dcab58603c26d4776aa1', '[\"*\"]', '2022-08-08 01:23:59', '2022-08-08 01:10:18', '2022-08-08 01:23:59'),
(24, 'App\\Models\\User', 3, 'MyApp', 'c8774c3dba39fdb2bbe13caca8ea4e929fd84550613a13d27fb1d985939938a1', '[\"*\"]', '2022-08-08 12:56:38', '2022-08-08 01:25:07', '2022-08-08 12:56:38'),
(25, 'App\\Models\\User', 3, 'MyApp', '99347ad2bf9cd150efe63a915b8c573642fae106c0a9209817b3b0a8a4dca341', '[\"*\"]', NULL, '2022-08-08 14:47:07', '2022-08-08 14:47:07'),
(26, 'App\\Models\\User', 5, 'MyApp', 'f1bcdf8fed215b0611bf13843d2968bf71afce55fc19db176e2bd7e2785e71a1', '[\"*\"]', '2022-08-11 07:05:29', '2022-08-08 14:56:08', '2022-08-11 07:05:29'),
(27, 'App\\Models\\User', 3, 'MyApp', '273d257e099230a14658408a2b5461e37e5cf22a433a99fbe85d22e4e3022cf6', '[\"*\"]', '2022-08-11 13:17:14', '2022-08-11 07:10:34', '2022-08-11 13:17:14'),
(28, 'App\\Models\\User', 2, 'MyApp', 'e1a5b4ab4cabb5c4549f066eac7d1fbc97ea0ae53346ab3593540479c0ac736e', '[\"*\"]', '2022-08-11 15:31:54', '2022-08-11 15:31:44', '2022-08-11 15:31:54'),
(29, 'App\\Models\\User', 2, 'MyApp', '666acd9e4ed424b384c927c6844662bf97a25992f7866db070e0b1d11a7d259d', '[\"*\"]', '2022-08-11 16:54:33', '2022-08-11 16:14:32', '2022-08-11 16:54:33'),
(30, 'App\\Models\\User', 3, 'MyApp', 'b2fe1d71a924d79208b0c66b7105a38be877deecb44ec261e130f8d0a2d72506', '[\"*\"]', '2022-08-11 16:54:52', '2022-08-11 16:54:45', '2022-08-11 16:54:52'),
(31, 'App\\Models\\User', 3, 'MyApp', 'f442d0a0619c7833d514c7367abd0996734cda1b598250a7b93bfd271d248bb8', '[\"*\"]', '2022-08-11 16:57:18', '2022-08-11 16:55:21', '2022-08-11 16:57:18'),
(32, 'App\\Models\\User', 2, 'MyApp', '7c082e50b4b85ffc1d6ec2428e6f9c66973a8c9cb7143c025e55f4dec2c0c283', '[\"*\"]', '2022-08-11 16:58:02', '2022-08-11 16:57:54', '2022-08-11 16:58:02'),
(33, 'App\\Models\\User', 3, 'MyApp', '86bd355282a78afe9f3117a3d11638a3ff7ad46b50a3da13de7dcbd31a947a75', '[\"*\"]', '2022-08-12 10:33:50', '2022-08-11 18:10:20', '2022-08-12 10:33:50'),
(34, 'App\\Models\\User', 2, 'MyApp', 'b82a2ef76f09f94680f537059f539fdc4b5fc0a168370c7eece20ede5c64791e', '[\"*\"]', '2022-08-15 09:47:23', '2022-08-12 10:34:05', '2022-08-15 09:47:23'),
(35, 'App\\Models\\User', 3, 'MyApp', 'c7c94a05c017c7d55bf71ac223b80cae1ad5131bcab246955716b97a563f0327', '[\"*\"]', '2022-08-16 09:23:15', '2022-08-15 09:47:53', '2022-08-16 09:23:15'),
(36, 'App\\Models\\User', 3, 'MyApp', '6a19e7cca3452ad6acbeff7ed94684075bef6fdaadf11867bbe18422caf728ee', '[\"*\"]', '2022-08-16 09:56:37', '2022-08-16 09:53:41', '2022-08-16 09:56:37'),
(37, 'App\\Models\\User', 3, 'MyApp', '45a0948f9143ee9dfe1fa9ee9d53936f0cb3c852a6ae0fd26c4eafb670868aa2', '[\"*\"]', NULL, '2022-08-16 09:56:51', '2022-08-16 09:56:51'),
(38, 'App\\Models\\User', 1, 'MyApp', 'ec70274b4fccead0423af1f32ae6f46eba838c65b20b510be22216d21fd3f88b', '[\"*\"]', NULL, '2022-08-16 10:04:52', '2022-08-16 10:04:52'),
(39, 'App\\Models\\User', 1, 'MyApp', '7673aec4ef8d8f2ee718f49ea893f8d79fa4b23ed56cbe3fbc33911265382c8a', '[\"*\"]', '2022-08-16 10:33:58', '2022-08-16 10:33:42', '2022-08-16 10:33:58'),
(40, 'App\\Models\\User', 1, 'MyApp', '3729df5225e5b7d56c02969c9809af7aa0644d738733a6958b94b8f96fc59e7f', '[\"*\"]', '2022-08-16 10:38:14', '2022-08-16 10:35:00', '2022-08-16 10:38:14'),
(41, 'App\\Models\\User', 3, 'MyApp', '0c042553348d395bc354bc55b573171d1c8b7eb012c92efc31ca9ce365f60aa3', '[\"*\"]', '2022-08-17 03:04:07', '2022-08-16 11:15:54', '2022-08-17 03:04:07'),
(42, 'App\\Models\\User', 1, 'MyApp', '80f1d632ecad3f4651b3953811c79de5bc0c16a43a181bb57a9ea3f69dabab35', '[\"*\"]', '2022-08-16 14:51:40', '2022-08-16 14:50:18', '2022-08-16 14:51:40'),
(43, 'App\\Models\\User', 1, 'MyApp', '43b3d1205ab68aa79e177cfc2d6d6c1b3a95b3a659acac0d202bb9c109f3a008', '[\"*\"]', NULL, '2022-08-16 15:01:22', '2022-08-16 15:01:22'),
(44, 'App\\Models\\User', 1, 'MyApp', '46cf883387a046598b93cde5261b5735b9f32fca79d429b82a815f37990f7203', '[\"*\"]', NULL, '2022-08-16 15:29:18', '2022-08-16 15:29:18'),
(45, 'App\\Models\\User', 1, 'MyApp', '4be721d720398887a0d2937a38e9bb0669778f49cf2b753b4f3ac8864925bbb5', '[\"*\"]', NULL, '2022-08-16 15:29:49', '2022-08-16 15:29:49'),
(46, 'App\\Models\\User', 1, 'MyApp', 'f37344481534ad07d5a980a3756168d1987bbd0762140a35d50a4e14a9dc2905', '[\"*\"]', NULL, '2022-08-16 15:30:12', '2022-08-16 15:30:12'),
(47, 'App\\Models\\User', 1, 'MyApp', '17870b268afbb2dcdf2b8326914aba980fdb8105c05b4d94a909a6ca7dd629ff', '[\"*\"]', NULL, '2022-08-16 15:30:57', '2022-08-16 15:30:57'),
(48, 'App\\Models\\User', 1, 'MyApp', '53429df22e172d3291a9e724705955c7ca534603ee67bc49b1df4bf723c0cc1f', '[\"*\"]', NULL, '2022-08-16 15:33:46', '2022-08-16 15:33:46'),
(49, 'App\\Models\\User', 1, 'MyApp', '6632dc79f6b9dad27d5f5e6d68ae8f0dbb76d397c494c8a68deecf9822b2b0de', '[\"*\"]', NULL, '2022-08-16 15:39:06', '2022-08-16 15:39:06'),
(50, 'App\\Models\\User', 1, 'MyApp', '6b0fd2ed224616c29b22cf1b0d2e508c26635840f3b04ee5510869c8429c5d42', '[\"*\"]', NULL, '2022-08-16 15:41:38', '2022-08-16 15:41:38'),
(51, 'App\\Models\\User', 1, 'MyApp', 'b6e64c2839f256beb94afb0e7cf8f8650b919b1e8bcb5051f4bb9d87a9ace6e3', '[\"*\"]', NULL, '2022-08-16 15:43:28', '2022-08-16 15:43:28'),
(52, 'App\\Models\\User', 1, 'MyApp', '821b84d6b21c053ae054b8b4260194ddf0a340a83b6c3a082c95d6c74a71bbaf', '[\"*\"]', NULL, '2022-08-16 15:47:21', '2022-08-16 15:47:21'),
(53, 'App\\Models\\User', 1, 'MyApp', 'ad99f2320fff12a2e07f56ee115ff3299b6dd6be4035542fc033ec13bef4ba29', '[\"*\"]', '2022-08-16 16:01:08', '2022-08-16 16:00:43', '2022-08-16 16:01:08'),
(54, 'App\\Models\\User', 1, 'MyApp', '23108054b74685f1620795c1759e92718b23626a0268bf94b8ea0060b4705844', '[\"*\"]', '2022-08-17 06:24:05', '2022-08-17 03:04:43', '2022-08-17 06:24:05'),
(55, 'App\\Models\\User', 1, 'MyApp', '0c96addc827dceb8c88e8b7456239fa3826011e95f2d0a56045dea1ae54cf613', '[\"*\"]', '2022-08-17 06:37:02', '2022-08-17 06:24:13', '2022-08-17 06:37:02'),
(56, 'App\\Models\\User', 3, 'MyApp', 'c749d4cbe422990eb1633cb5c8a1540811ee8fa0b6e107f704d73289e592859d', '[\"*\"]', '2022-08-17 07:36:11', '2022-08-17 06:38:26', '2022-08-17 07:36:11'),
(57, 'App\\Models\\User', 1, 'MyApp', 'fdb22b0715a8657e477c8a38b06d80efbde431a9ddf72dfadcb2541db9b053cc', '[\"*\"]', '2022-08-17 08:08:44', '2022-08-17 07:36:19', '2022-08-17 08:08:44'),
(58, 'App\\Models\\User', 2, 'MyApp', 'e9ed21090df866ed8b1498c0e082624fd4833921efd4b2876c75d05006ba10f5', '[\"*\"]', '2022-08-17 09:05:56', '2022-08-17 08:09:44', '2022-08-17 09:05:56');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `status` enum('PUBLISHED','DRAFT','PENDING') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `author_id`, `category_id`, `title`, `seo_title`, `excerpt`, `body`, `image`, `slug`, `meta_description`, `meta_keywords`, `status`, `featured`, `created_at`, `updated_at`) VALUES
(1, 0, NULL, 'Lorem Ipsum Post', NULL, 'This is the excerpt for the Lorem Ipsum Post', '<p>This is the body of the lorem ipsum post</p>', 'posts/post1.jpg', 'lorem-ipsum-post', 'This is the meta description', 'keyword1, keyword2, keyword3', 'PUBLISHED', 0, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, 0, NULL, 'My Sample Post', NULL, 'This is the excerpt for the sample Post', '<p>This is the body for the sample post, which includes the body.</p>\n                <h2>We can use all kinds of format!</h2>\n                <p>And include a bunch of other stuff.</p>', 'posts/post2.jpg', 'my-sample-post', 'Meta Description for sample post', 'keyword1, keyword2, keyword3', 'PUBLISHED', 0, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(3, 0, NULL, 'Latest Post', NULL, 'This is the excerpt for the latest post', '<p>This is the body for the latest post</p>', 'posts/post3.jpg', 'latest-post', 'This is the meta description', 'keyword1, keyword2, keyword3', 'PUBLISHED', 0, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(4, 0, NULL, 'Yarr Post', NULL, 'Reef sails nipperkin bring a spring upon her cable coffer jury mast spike marooned Pieces of Eight poop deck pillage. Clipper driver coxswain galleon hempen halter come about pressgang gangplank boatswain swing the lead. Nipperkin yard skysail swab lanyard Blimey bilge water ho quarter Buccaneer.', '<p>Swab deadlights Buccaneer fire ship square-rigged dance the hempen jig weigh anchor cackle fruit grog furl. Crack Jennys tea cup chase guns pressgang hearties spirits hogshead Gold Road six pounders fathom measured fer yer chains. Main sheet provost come about trysail barkadeer crimp scuttle mizzenmast brig plunder.</p>\n<p>Mizzen league keelhaul galleon tender cog chase Barbary Coast doubloon crack Jennys tea cup. Blow the man down lugsail fire ship pinnace cackle fruit line warp Admiral of the Black strike colors doubloon. Tackle Jack Ketch come about crimp rum draft scuppers run a shot across the bow haul wind maroon.</p>\n<p>Interloper heave down list driver pressgang holystone scuppers tackle scallywag bilged on her anchor. Jack Tar interloper draught grapple mizzenmast hulk knave cable transom hogshead. Gaff pillage to go on account grog aft chase guns piracy yardarm knave clap of thunder.</p>', 'posts/post4.jpg', 'yarr-post', 'this be a meta descript', 'keyword1, keyword2, keyword3', 'PUBLISHED', 0, '2022-07-19 06:56:31', '2022-07-19 06:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `book_days_in_adv` smallint(6) DEFAULT '10',
  `default_price` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`, `book_days_in_adv`, `default_price`) VALUES
(1, 'admin', 'Administrator', '2022-07-19 06:56:31', '2022-08-05 05:19:42', 30, 100),
(2, 'user', 'Normal User', '2022-07-19 06:56:31', '2022-08-05 05:21:33', 10, 60),
(3, 'manager', 'Manager', '2022-07-19 14:07:33', '2022-08-05 05:19:54', 30, 50),
(4, 'internal_coach', 'Centre Coach', '2022-07-25 09:31:46', '2022-08-05 05:20:01', 20, 30),
(5, 'external_coach', 'Friendly Coach', '2022-07-25 09:36:19', '2022-08-05 05:20:08', 15, 30),
(6, 'member', 'Student', '2022-07-25 09:37:28', '2022-08-11 05:58:16', 15, 30);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `translations` text COLLATE utf8mb4_unicode_ci,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `color` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `location_id`, `translations`, `status`, `created_at`, `updated_at`, `color`) VALUES
(1, 'Table 1', 1, NULL, 1001, '2022-07-29 10:02:00', '2022-08-17 03:27:48', '#1091f4'),
(2, 'Table 2', 1, NULL, 1001, '2022-08-04 17:10:00', '2022-08-17 03:28:00', '#23e60a'),
(3, 'Table 3', 1, NULL, 1001, '2022-08-15 06:41:00', '2022-08-17 03:23:12', '#3d0de7'),
(4, 'Table 5', 1, NULL, 1001, '2022-08-15 06:42:00', '2022-08-17 03:23:01', '#ec8d09'),
(5, 'Table 4', 1, NULL, 1001, '2022-08-15 06:44:00', '2022-08-17 03:22:47', '#e01515'),
(6, 'Table 6', 1, NULL, 1002, '2022-08-15 06:50:00', '2022-08-17 03:28:13', '#9e6605');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` double DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `min_capacity` smallint(6) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL,
  `pic_full_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(6) DEFAULT '1001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `translations` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `color`, `price`, `category_id`, `min_capacity`, `max_capacity`, `pic_full_path`, `status`, `created_at`, `updated_at`, `translations`) VALUES
(1, 'Ping pong table', 'Booking for ping pong table', '#fc2eff', 50, 1, NULL, NULL, NULL, 1001, '2022-08-05 02:09:00', '2022-08-05 02:43:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `details` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `display_name`, `value`, `details`, `type`, `order`, `group`) VALUES
(1, 'site.title', 'Site Title', 'Victory Table Tennis', '', 'text', 1, 'Site'),
(2, 'site.description', 'Site Description', 'Table Tennis Booking System', '', 'text', 2, 'Site'),
(3, 'site.logo', 'Site Logo', 'settings/July2022/YjUVH537iv1zU1zVadbC.png', '', 'image', 3, 'Site'),
(4, 'site.google_analytics_tracking_id', 'Google Analytics Tracking ID', NULL, '', 'text', 4, 'Site'),
(5, 'admin.bg_image', 'Admin Background Image', 'settings/July2022/PJ1Wn88TDqaa5D1mFkAl.jpg', '', 'image', 5, 'Admin'),
(6, 'admin.title', 'Admin Title', 'Lemonade', '', 'text', 1, 'Admin'),
(7, 'admin.description', 'Admin Description', 'Welcome to Lemonade', '', 'text', 2, 'Admin'),
(8, 'admin.loader', 'Admin Loader', 'settings/July2022/gbidw3jtZ8nhrH2FUtPM.png', '', 'image', 3, 'Admin'),
(9, 'admin.icon_image', 'Admin Icon Image', 'settings/July2022/33dBSiOd2Ph2ZKhtuA1M.png', '', 'image', 4, 'Admin'),
(10, 'admin.google_analytics_client_id', 'Google Analytics Client ID (used for admin dashboard)', NULL, '', 'text', 1, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `testtbl`
--

CREATE TABLE `testtbl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timeslots`
--

CREATE TABLE `timeslots` (
  `id` int(10) UNSIGNED NOT NULL,
  `location_id` int(11) NOT NULL,
  `day_idx` tinyint(4) NOT NULL,
  `from_time` time NOT NULL,
  `to_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timeslots`
--

INSERT INTO `timeslots` (`id`, `location_id`, `day_idx`, `from_time`, `to_time`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '09:00:00', '02:00:00', '2022-07-25 09:19:22', '2022-07-25 09:19:22'),
(2, 1, 2, '09:00:00', '00:00:00', '2022-07-25 09:22:11', '2022-07-25 09:22:11'),
(3, 1, 3, '09:00:00', '19:00:00', '2022-07-25 09:22:29', '2022-08-15 09:42:33'),
(4, 1, 4, '09:00:00', '18:00:00', '2022-07-25 09:23:04', '2022-07-25 09:23:04'),
(5, 1, 5, '09:00:00', '23:00:00', '2022-07-25 09:23:20', '2022-07-25 09:23:20'),
(6, 1, 6, '09:00:00', '23:00:00', '2022-07-25 09:23:00', '2022-07-27 15:33:51'),
(7, 1, 0, '09:00:00', '20:30:00', '2022-07-25 09:23:00', '2022-07-27 15:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foreign_key` int(10) UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `table_name`, `column_name`, `foreign_key`, `locale`, `value`, `created_at`, `updated_at`) VALUES
(1, 'data_types', 'display_name_singular', 5, 'pt', 'Post', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, 'data_types', 'display_name_singular', 6, 'pt', 'Página', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(3, 'data_types', 'display_name_singular', 1, 'pt', 'Utilizador', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(4, 'data_types', 'display_name_singular', 4, 'pt', 'Categoria', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(5, 'data_types', 'display_name_singular', 2, 'pt', 'Menu', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(6, 'data_types', 'display_name_singular', 3, 'pt', 'Função', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(7, 'data_types', 'display_name_plural', 5, 'pt', 'Posts', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(8, 'data_types', 'display_name_plural', 6, 'pt', 'Páginas', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(9, 'data_types', 'display_name_plural', 1, 'pt', 'Utilizadores', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(10, 'data_types', 'display_name_plural', 4, 'pt', 'Categorias', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(11, 'data_types', 'display_name_plural', 2, 'pt', 'Menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(12, 'data_types', 'display_name_plural', 3, 'pt', 'Funções', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(13, 'categories', 'slug', 1, 'pt', 'categoria-1', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(14, 'categories', 'name', 1, 'pt', 'Categoria 1', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(15, 'categories', 'slug', 2, 'pt', 'categoria-2', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(16, 'categories', 'name', 2, 'pt', 'Categoria 2', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(17, 'pages', 'title', 1, 'pt', 'Olá Mundo', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(18, 'pages', 'slug', 1, 'pt', 'ola-mundo', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(19, 'pages', 'body', 1, 'pt', '<p>Olá Mundo. Scallywag grog swab Cat o\'nine tails scuttle rigging hardtack cable nipper Yellow Jack. Handsomely spirits knave lad killick landlubber or just lubber deadlights chantey pinnace crack Jennys tea cup. Provost long clothes black spot Yellow Jack bilged on her anchor league lateen sail case shot lee tackle.</p>\r\n<p>Ballast spirits fluke topmast me quarterdeck schooner landlubber or just lubber gabion belaying pin. Pinnace stern galleon starboard warp carouser to go on account dance the hempen jig jolly boat measured fer yer chains. Man-of-war fire in the hole nipperkin handsomely doubloon barkadeer Brethren of the Coast gibbet driver squiffy.</p>', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(20, 'menu_items', 'title', 1, 'pt', 'Painel de Controle', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(21, 'menu_items', 'title', 2, 'pt', 'Media', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(22, 'menu_items', 'title', 12, 'pt', 'Publicações', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(23, 'menu_items', 'title', 3, 'pt', 'Utilizadores', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(24, 'menu_items', 'title', 11, 'pt', 'Categorias', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(25, 'menu_items', 'title', 13, 'pt', 'Páginas', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(26, 'menu_items', 'title', 4, 'pt', 'Funções', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(27, 'menu_items', 'title', 5, 'pt', 'Ferramentas', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(28, 'menu_items', 'title', 6, 'pt', 'Menus', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(29, 'menu_items', 'title', 7, 'pt', 'Base de dados', '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(30, 'menu_items', 'title', 10, 'pt', 'Configurações', '2022-07-19 06:56:31', '2022-07-19 06:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'users/default.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `avatar`, `email_verified_at`, `password`, `remember_token`, `settings`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, 'Admin', 'admin@admin.com', 'users/July2022/qMTpJP0bytpHzptTBs4p.webp', NULL, '$2y$10$BJv4EUHyOVpqJpWxYFthb.CdWAV3KQdHzJkDHjTz2ZZn0o8G/S0y2', 'UutMqQ0CaKLygsKmncIV8Wz8LvbSCnW3lVpnH7yxQyDcmaXuiTQpvrrTLCoS', '{\"locale\":\"zh_TW\"}', '2022-07-19 06:56:31', '2022-07-25 09:38:37', 'active'),
(2, 3, 'manager', 'manager@manager.com', 'users/default.png', NULL, '$2y$10$yUzvtbt58ollO.R/mEdINuVbGVOS8R2V05ZmlFV/zVsXxl28oFz3u', NULL, '{\"locale\":\"en\"}', '2022-07-19 14:06:31', '2022-07-19 14:08:07', 'active'),
(3, 2, 'User', 'user@user.com', 'users/default.png', NULL, '$2y$10$T7r7VqgGGFKYu4W4mkHPAeLVnJIr5EkcC5QgEoWhFbZ4u8nMFvWFe', NULL, '{\"locale\":\"zh_TW\"}', '2022-07-26 02:31:34', '2022-07-26 02:31:34', 'active'),
(4, 5, 'Coach ABC', 'coach@coach.com', 'users/default.png', NULL, '$2y$10$OV4sMVPBXUhIHap6v57g1e2Jz4hiPm9OZ5IcidITY9sMuMALAJcju', NULL, '{\"locale\":\"zh_TW\"}', '2022-08-05 08:28:49', '2022-08-11 16:48:45', 'active'),
(5, 2, 'User1', 'user1@user1.com', 'users/default.png', NULL, '$2y$10$L32p1Ai2VAEb114DdHgC8OSsCOZn.UnpeUne6FeTVTr3xxqP8NZJq', NULL, '{\"locale\":\"zh_TW\"}', '2022-08-08 14:55:53', '2022-08-11 16:54:33', 'banned');

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uuid` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_teammates`
--

CREATE TABLE `user_teammates` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `teammate_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_teammates`
--

INSERT INTO `user_teammates` (`user_id`, `teammate_id`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 3, 2, NULL, NULL),
(4, 5, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_room_time` (`start_time`,`end_time`,`room_id`);

--
-- Indexes for table `booking_reminders`
--
ALTER TABLE `booking_reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_bookings`
--
ALTER TABLE `customer_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_rows`
--
ALTER TABLE `data_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_rows_data_type_id_foreign` (`data_type_id`);

--
-- Indexes for table `data_types`
--
ALTER TABLE `data_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_types_name_unique` (`name`),
  ADD UNIQUE KEY `data_types_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_name_unique` (`name`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_number_unique` (`order_number`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_key_index` (`key`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_permission_id_index` (`permission_id`),
  ADD KEY `permission_role_role_id_index` (`role_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `posts_slug_unique` (`slug`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `testtbl`
--
ALTER TABLE `testtbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timeslots`
--
ALTER TABLE `timeslots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `translations_table_name_column_name_foreign_key_locale_unique` (`table_name`,`column_name`,`foreign_key`,`locale`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_roles_user_id_index` (`user_id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- Indexes for table `user_teammates`
--
ALTER TABLE `user_teammates`
  ADD PRIMARY KEY (`user_id`,`teammate_id`),
  ADD KEY `user_teammates_teammate_id_` (`teammate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `booking_reminders`
--
ALTER TABLE `booking_reminders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customer_bookings`
--
ALTER TABLE `customer_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `data_rows`
--
ALTER TABLE `data_rows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `data_types`
--
ALTER TABLE `data_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `testtbl`
--
ALTER TABLE `testtbl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslots`
--
ALTER TABLE `timeslots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `data_rows`
--
ALTER TABLE `data_rows`
  ADD CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
