-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 29, 2022 at 03:04 AM
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
(1, 1, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(2, 1, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(3, 1, 'email', 'text', 'Email', 1, 1, 1, 1, 1, 1, NULL, 3),
(4, 1, 'password', 'password', 'Password', 1, 0, 0, 1, 1, 0, NULL, 4),
(5, 1, 'remember_token', 'text', 'Remember Token', 0, 0, 0, 0, 0, 0, NULL, 5),
(6, 1, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 0, 0, 0, NULL, 6),
(7, 1, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 7),
(8, 1, 'avatar', 'image', 'Avatar', 0, 1, 1, 1, 1, 1, NULL, 8),
(9, 1, 'user_belongsto_role_relationship', 'relationship', 'Role', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsTo\",\"column\":\"role_id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"roles\",\"pivot\":0}', 10),
(10, 1, 'user_belongstomany_role_relationship', 'relationship', 'voyager::seeders.data_rows.roles', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"user_roles\",\"pivot\":\"1\",\"taggable\":\"0\"}', 11),
(11, 1, 'settings', 'hidden', 'Settings', 0, 0, 0, 0, 0, 0, NULL, 12),
(12, 2, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(13, 2, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(14, 2, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3),
(15, 2, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4),
(16, 3, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, '{}', 1),
(17, 3, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(18, 3, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 3),
(19, 3, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 4),
(20, 3, 'display_name', 'text', 'Display Name', 1, 1, 1, 1, 1, 1, '{}', 5),
(21, 1, 'role_id', 'text', 'Role', 1, 1, 1, 1, 1, 1, NULL, 9),
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
(92, 13, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, '{}', 2),
(93, 13, 'description', 'text', 'Description', 0, 1, 1, 1, 1, 1, '{}', 3),
(94, 13, 'color', 'color', 'Color', 0, 1, 1, 1, 1, 1, '{}', 4),
(95, 13, 'price', 'number', 'Price', 0, 1, 1, 1, 1, 1, '{}', 5),
(96, 13, 'service_type', 'select_dropdown', 'Service Type', 1, 1, 1, 1, 1, 1, '{}', 6),
(97, 13, 'min_capacity', 'number', 'Min Capacity', 0, 1, 1, 1, 1, 1, '{}', 7),
(98, 13, 'max_capacity', 'number', 'Max Capacity', 0, 1, 1, 1, 1, 1, '{}', 8),
(99, 13, 'pic_full_path', 'image', 'Pic Full Path', 0, 1, 1, 1, 1, 1, '{}', 9),
(100, 13, 'status', 'radio_btn', 'Status', 0, 1, 1, 1, 1, 1, '{}', 10),
(101, 13, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 1, 0, 1, '{}', 11),
(102, 13, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 12),
(103, 13, 'translations', 'text', 'Translations', 0, 1, 1, 1, 1, 1, '{}', 13),
(104, 13, 'category_id', 'text', 'Category Id', 0, 1, 1, 1, 1, 1, '{}', 14),
(105, 13, 'service_belongsto_category_relationship', 'relationship', 'categories', 0, 1, 1, 1, 1, 1, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Category\",\"table\":\"categories\",\"type\":\"belongsTo\",\"column\":\"category_id\",\"key\":\"id\",\"label\":\"name\",\"pivot_table\":\"categories\",\"pivot\":\"0\",\"taggable\":\"0\"}', 15);

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
(1, 'users', 'users', 'User', 'Users', 'voyager-person', 'TCG\\Voyager\\Models\\User', 'TCG\\Voyager\\Policies\\UserPolicy', 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(2, 'menus', 'menus', 'Menu', 'Menus', 'voyager-list', 'TCG\\Voyager\\Models\\Menu', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(3, 'roles', 'roles', 'Role', 'Roles', 'voyager-lock', 'TCG\\Voyager\\Models\\Role', NULL, 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController', NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"desc\",\"default_search_key\":null,\"scope\":null}', '2022-07-19 06:56:31', '2022-07-25 09:31:00'),
(4, 'categories', 'categories', 'Category', 'Categories', 'voyager-categories', 'TCG\\Voyager\\Models\\Category', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(5, 'posts', 'posts', 'Post', 'Posts', 'voyager-news', 'TCG\\Voyager\\Models\\Post', 'TCG\\Voyager\\Policies\\PostPolicy', '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(6, 'pages', 'pages', 'Page', 'Pages', 'voyager-file-text', 'TCG\\Voyager\\Models\\Page', NULL, '', '', 1, 0, NULL, '2022-07-19 06:56:31', '2022-07-19 06:56:31'),
(10, 'locations', 'locations', 'Location', 'Locations', 'voyager-location', 'App\\Models\\Location', NULL, NULL, NULL, 1, 0, '{\"order_column\":\"name\",\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":\"name\",\"scope\":null}', '2022-07-19 08:16:53', '2022-07-27 03:09:57'),
(11, 'companies', 'companies', 'Company', 'Companies', 'voyager-company', 'App\\Models\\Company', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2022-07-19 08:43:07', '2022-07-19 08:43:07'),
(12, 'timeslots', 'timeslots', 'Timeslot', 'Timeslots', 'voyager-alarm-clock', 'App\\Models\\Timeslot', NULL, NULL, NULL, 1, 0, '{\"order_column\":\"day_idx\",\"order_display_column\":\"day_idx\",\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-21 07:37:54', '2022-07-27 08:44:11'),
(13, 'services', 'services', 'Service', 'Services', NULL, 'App\\Models\\Service', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2022-07-27 07:48:19', '2022-07-28 03:15:22');

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
(5, 1, 'Tools', '', '_self', 'voyager-tools', NULL, NULL, 9, '2022-07-19 06:56:31', '2022-07-19 06:56:31', NULL, NULL),
(6, 1, 'Menu Builder', '', '_self', 'voyager-list', NULL, 5, 1, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.menus.index', NULL),
(7, 1, 'Database', '', '_self', 'voyager-data', NULL, 5, 2, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.database.index', NULL),
(8, 1, 'Compass', '', '_self', 'voyager-compass', NULL, 5, 3, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.compass.index', NULL),
(9, 1, 'BREAD', '', '_self', 'voyager-bread', NULL, 5, 4, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.bread.index', NULL),
(10, 1, 'Settings', '', '_self', 'voyager-settings', NULL, NULL, 10, '2022-07-19 06:56:31', '2022-07-19 08:44:12', 'voyager.settings.index', NULL),
(11, 1, 'Categories', '', '_self', 'voyager-categories', NULL, NULL, 8, '2022-07-19 06:56:31', '2022-07-19 06:56:31', 'voyager.categories.index', NULL),
(12, 1, 'Locations', '', '_self', 'voyager-location', '#000000', NULL, 6, '2022-07-19 06:56:31', '2022-07-19 08:52:11', 'voyager.locations.index', 'null'),
(13, 1, 'Pages', '', '_self', 'voyager-file-text', NULL, NULL, 7, '2022-07-19 06:56:31', '2022-07-19 06:56:31', 'voyager.pages.index', NULL),
(16, 1, 'Company', '', '_self', 'voyager-company', '#000000', NULL, 5, '2022-07-19 08:44:07', '2022-07-19 08:44:12', 'voyager.companies.index', NULL),
(17, 2, 'Locations', '', '_self', 'voyager-location', '#000000', NULL, 11, '2022-07-19 14:05:48', '2022-07-19 14:05:48', 'voyager.locations.index', NULL),
(18, 1, 'Timeslots', '', '_self', 'voyager-alarm-clock', NULL, NULL, 12, '2022-07-21 07:37:54', '2022-07-21 07:37:54', 'voyager.timeslots.index', NULL),
(19, 1, 'Services', '', '_self', NULL, NULL, NULL, 13, '2022-07-27 07:48:19', '2022-07-27 07:48:19', 'voyager.services.index', NULL);

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
(28, '2017_04_11_000000_alter_post_nullable_fields_table', 2);

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
(60, 'delete_services', 'services', '2022-07-27 07:48:19', '2022-07-27 07:48:19');

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
(60, 1);

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
(6, 'App\\Models\\User', 2, 'MyApp', '89a16758d15da9a6fb18a9b0339e145c916e7548e450659d65c0bb1f225280b2', '[\"*\"]', '2022-07-28 11:30:56', '2022-07-28 09:14:10', '2022-07-28 11:30:56');

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
  `book_days_in_adv` smallint(6) DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`, `book_days_in_adv`) VALUES
(1, 'admin', 'Administrator', '2022-07-19 06:56:31', '2022-07-25 09:37:41', 30),
(2, 'user', 'Normal User', '2022-07-19 06:56:31', '2022-07-19 06:56:31', 10),
(3, 'manager', 'Manager', '2022-07-19 14:07:33', '2022-07-25 09:35:57', 30),
(4, 'Centre Coach', 'Centre Coach', '2022-07-25 09:31:46', '2022-07-25 09:31:46', 20),
(5, 'Friendly Coach', 'Friendly Coach', '2022-07-25 09:36:19', '2022-07-25 09:36:19', 15),
(6, 'member', 'Member', '2022-07-25 09:37:28', '2022-07-25 09:37:28', 15);

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
  `service_type` tinyint(4) NOT NULL DEFAULT '1',
  `min_capacity` smallint(6) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL,
  `pic_full_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` smallint(6) DEFAULT '1001',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `translations` text COLLATE utf8mb4_unicode_ci,
  `category_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 1, 1, '09:00:00', '18:00:00', '2022-07-25 09:19:22', '2022-07-25 09:19:22'),
(2, 1, 2, '09:00:00', '18:00:00', '2022-07-25 09:22:11', '2022-07-25 09:22:11'),
(3, 1, 3, '09:00:00', '18:00:00', '2022-07-25 09:22:29', '2022-07-25 09:22:29'),
(4, 1, 4, '09:00:00', '18:00:00', '2022-07-25 09:23:04', '2022-07-25 09:23:04'),
(5, 1, 5, '09:00:00', '18:00:00', '2022-07-25 09:23:20', '2022-07-25 09:23:20'),
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `avatar`, `email_verified_at`, `password`, `remember_token`, `settings`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin', 'admin@admin.com', 'users/July2022/qMTpJP0bytpHzptTBs4p.webp', NULL, '$2y$10$BJv4EUHyOVpqJpWxYFthb.CdWAV3KQdHzJkDHjTz2ZZn0o8G/S0y2', 'ETy4uQKm6cUqBsJwDh2lGFiZg7MvocfIjqDgyeUahL7zkFN3VgODurhAQQtF', '{\"locale\":\"zh_TW\"}', '2022-07-19 06:56:31', '2022-07-25 09:38:37'),
(2, 3, 'manager', 'manager@manager.com', 'users/default.png', NULL, '$2y$10$yUzvtbt58ollO.R/mEdINuVbGVOS8R2V05ZmlFV/zVsXxl28oFz3u', NULL, '{\"locale\":\"en\"}', '2022-07-19 14:06:31', '2022-07-19 14:08:07'),
(3, 2, 'User', 'user@user.com', 'users/default.png', NULL, '$2y$10$T7r7VqgGGFKYu4W4mkHPAeLVnJIr5EkcC5QgEoWhFbZ4u8nMFvWFe', NULL, '{\"locale\":\"zh_TW\"}', '2022-07-26 02:31:34', '2022-07-26 02:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_roles_user_id_index` (`user_id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

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
-- AUTO_INCREMENT for table `data_rows`
--
ALTER TABLE `data_rows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `data_types`
--
ALTER TABLE `data_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
