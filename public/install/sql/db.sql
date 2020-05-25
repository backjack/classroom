-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 08, 2020 at 12:45 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `traineasyprodselfhosted`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(10) UNSIGNED NOT NULL,
  `password` varchar(45) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(45) NOT NULL DEFAULT '',
  `last_name` varchar(45) NOT NULL DEFAULT '',
  `role_id` int(10) UNSIGNED DEFAULT NULL,
  `notify` tinyint(1) DEFAULT '1',
  `account_description` mediumtext,
  `picture` varchar(255) DEFAULT NULL,
  `account_status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int(11) NOT NULL,
  `article_name` varchar(100) NOT NULL,
  `article_content` mediumtext NOT NULL,
  `alias` varchar(250) NOT NULL,
  `top_nav` tinyint(1) DEFAULT '0',
  `bottom_nav` tinyint(1) DEFAULT '1',
  `sort_order` int(10) UNSIGNED DEFAULT NULL,
  `parent` int(10) UNSIGNED DEFAULT '0',
  `visibility` char(255) NOT NULL DEFAULT 'w'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `due_date` int(11) DEFAULT NULL,
  `created_on` int(11) NOT NULL,
  `assignment_type` char(255) NOT NULL,
  `instruction` text NOT NULL,
  `passmark` float DEFAULT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `allow_late` tinyint(1) DEFAULT '0',
  `opening_date` int(11) DEFAULT NULL,
  `schedule_type` char(255) NOT NULL DEFAULT 's',
  `lesson_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submission`
--

CREATE TABLE `assignment_submission` (
  `assignment_submission_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `content` text,
  `file_path` text,
  `grade` float DEFAULT NULL,
  `editable` tinyint(1) DEFAULT '0',
  `admin_comment` text,
  `created` int(11) NOT NULL,
  `modified` int(11) NOT NULL,
  `submitted` tinyint(1) DEFAULT '0',
  `student_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `attendance_date` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `bookmark_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `lecture_page_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `certificate`
--

CREATE TABLE `certificate` (
  `certificate_id` int(11) NOT NULL,
  `certificate_name` varchar(250) NOT NULL,
  `certificate_image` varchar(255) DEFAULT NULL,
  `created_on` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `orientation` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `html` text,
  `any_session` tinyint(1) NOT NULL DEFAULT '0',
  `account_id` int(11) DEFAULT NULL,
  `max_downloads` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_assignment`
--

CREATE TABLE `certificate_assignment` (
  `certificate_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_lesson`
--

CREATE TABLE `certificate_lesson` (
  `certificate_id` int(11) NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `certificate_test`
--

CREATE TABLE `certificate_test` (
  `certificate_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `country_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `iso_code_2` varchar(2) NOT NULL,
  `iso_code_3` varchar(3) NOT NULL,
  `address_format` mediumtext NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `currency_name` varchar(150) DEFAULT NULL,
  `currency_code` varchar(45) DEFAULT NULL,
  `symbol_left` varchar(45) DEFAULT NULL,
  `symbol_right` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `name`, `iso_code_2`, `iso_code_3`, `address_format`, `postcode_required`, `status`, `currency_name`, `currency_code`, `symbol_left`, `symbol_right`, `created_at`, `updated_at`) VALUES
(1, 'Afghanistan', 'AF', 'AFG', '', 0, 1, 'Afghani', 'AFN', '؋', NULL, '2020-04-08 09:48:18', '2017-01-10 11:18:14'),
(2, 'Albania', 'AL', 'ALB', '', 0, 1, 'Lek', 'ALL', 'L', NULL, '2018-11-01 12:51:37', '2017-01-10 11:18:14'),
(3, 'Algeria', 'DZ', 'DZA', '', 0, 1, 'Algerian Dinar', 'DZD', 'د.ج', NULL, '2020-04-08 09:48:18', '2017-01-10 11:18:15'),
(4, 'American Samoa', 'AS', 'ASM', '', 0, 1, 'Euros', 'EUR', '$', NULL, '2018-11-01 12:51:40', '2017-01-10 11:18:15'),
(5, 'Andorra', 'AD', 'AND', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:51:41', '2017-01-10 11:18:15'),
(6, 'Angola', 'AO', 'AGO', '', 0, 1, 'Angolan kwanza', 'AOA', 'Kz', NULL, '2018-11-01 12:51:42', '2017-01-10 11:18:15'),
(7, 'Anguilla', 'AI', 'AIA', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:51:43', '2017-01-10 11:18:15'),
(8, 'Antarctica', 'AQ', 'ATA', '', 0, 1, 'Antarctican dollar', 'AQD', '$', NULL, '2018-11-01 12:51:44', '2017-01-10 11:18:15'),
(9, 'Antigua and Barbuda', 'AG', 'ATG', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:51:45', '2017-01-10 11:18:15'),
(10, 'Argentina', 'AR', 'ARG', '', 0, 1, 'Peso', 'ARS', '$', NULL, '2018-11-01 12:51:46', '2017-01-10 11:18:15'),
(11, 'Armenia', 'AM', 'ARM', '', 0, 1, 'Dram', 'AMD', NULL, NULL, '2017-01-10 12:18:15', '2017-01-10 11:18:15'),
(12, 'Aruba', 'AW', 'ABW', '', 0, 1, 'Netherlands Antilles Guilder', 'ANG', 'ƒ', NULL, '2018-11-01 12:51:53', '2017-01-10 11:18:15'),
(13, 'Australia', 'AU', 'AUS', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:51:54', '2017-01-10 11:18:16'),
(14, 'Austria', 'AT', 'AUT', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:51:55', '2017-01-10 11:18:16'),
(15, 'Azerbaijan', 'AZ', 'AZE', '', 0, 1, 'Manat', 'AZN', NULL, NULL, '2017-01-10 12:18:16', '2017-01-10 11:18:16'),
(16, 'Bahamas', 'BS', 'BHS', '', 0, 1, 'Bahamian Dollar', 'BSD', '$', NULL, '2018-11-01 12:51:58', '2017-01-10 11:18:16'),
(17, 'Bahrain', 'BH', 'BHR', '', 0, 1, 'Bahraini Dinar', 'BHD', '.د.ب', NULL, '2020-04-08 09:48:19', '2017-01-10 11:18:16'),
(18, 'Bangladesh', 'BD', 'BGD', '', 0, 1, 'Taka', 'BDT', '৳', NULL, '2020-04-08 09:48:19', '2017-01-10 11:18:16'),
(19, 'Barbados', 'BB', 'BRB', '', 0, 1, 'Barbadian Dollar', 'BBD', '$', NULL, '2018-11-01 12:52:03', '2017-01-10 11:18:16'),
(20, 'Belarus', 'BY', 'BLR', '', 0, 1, 'Belarus Ruble', 'BYR', 'Br', NULL, '2018-11-01 12:52:04', '2017-01-10 11:18:16'),
(21, 'Belgium', 'BE', 'BEL', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:52:05', '2017-01-10 11:18:17'),
(22, 'Belize', 'BZ', 'BLZ', '', 0, 1, 'Belizean Dollar', 'BZD', '$', NULL, '2018-11-01 12:52:06', '2017-01-10 11:18:17'),
(23, 'Benin', 'BJ', 'BEN', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:52:07', '2017-01-10 11:18:17'),
(24, 'Bermuda', 'BM', 'BMU', '', 0, 1, 'Bermudian Dollar', 'BMD', '$', NULL, '2018-11-01 12:52:07', '2017-01-10 11:18:17'),
(25, 'Bhutan', 'BT', 'BTN', '', 0, 1, 'Indian Rupee', 'INR', '&#x20b9;', NULL, '2018-04-26 14:03:24', '2017-01-10 11:18:18'),
(26, 'Bolivia', 'BO', 'BOL', '', 0, 1, 'Boliviano', 'BOB', 'Bs.', NULL, '2018-11-01 12:52:08', '2017-01-10 11:18:18'),
(27, 'Bosnia and Herzegovina', 'BA', 'BIH', '', 0, 1, 'Bosnia and Herzegovina convertible mark', 'BAM', NULL, NULL, '2017-01-10 12:18:18', '2017-01-10 11:18:18'),
(28, 'Botswana', 'BW', 'BWA', '', 0, 1, 'Pula', 'BWP', 'P', NULL, '2018-11-01 12:52:11', '2017-01-10 11:18:18'),
(29, 'Bouvet Island', 'BV', 'BVT', '', 0, 1, 'Norwegian Krone', 'NOK', 'kr', NULL, '2018-11-01 12:52:12', '2017-01-10 11:18:18'),
(30, 'Brazil', 'BR', 'BRA', '', 0, 1, 'Brazil', 'BRL', 'R$', NULL, '2018-11-01 12:52:14', '2017-01-10 11:18:18'),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:18'),
(32, 'Brunei Darussalam', 'BN', 'BRN', '', 0, 1, 'Bruneian Dollar', 'BND', '$', NULL, '2018-11-01 12:52:14', '2017-01-10 11:18:18'),
(33, 'Bulgaria', 'BG', 'BGR', '', 0, 1, 'Lev', 'BGN', 'лв', NULL, '2020-04-08 09:48:20', '2017-01-10 11:18:18'),
(34, 'Burkina Faso', 'BF', 'BFA', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:52:17', '2017-01-10 11:18:18'),
(35, 'Burundi', 'BI', 'BDI', '', 0, 1, 'Burundi Franc', 'BIF', 'Fr', NULL, '2018-11-01 12:52:18', '2017-01-10 11:18:19'),
(36, 'Cambodia', 'KH', 'KHM', '', 0, 1, 'Riel', 'KHR', '៛', NULL, '2020-04-08 09:48:20', '2017-01-10 11:18:19'),
(37, 'Cameroon', 'CM', 'CMR', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:52:21', '2017-01-10 11:18:19'),
(38, 'Canada', 'CA', 'CAN', '', 0, 1, 'Canadian Dollar', 'CAD', '$', NULL, '2018-11-01 12:52:22', '2017-01-10 11:18:19'),
(39, 'Cape Verde', 'CV', 'CPV', '', 0, 1, 'Escudo', 'CVE', 'Esc', NULL, '2018-11-01 12:52:23', '2017-01-10 11:18:19'),
(40, 'Cayman Islands', 'KY', 'CYM', '', 0, 1, 'Caymanian Dollar', 'KYD', '$', NULL, '2018-11-01 12:52:24', '2017-01-10 11:18:19'),
(41, 'Central African Republic', 'CF', 'CAF', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:52:24', '2017-01-10 11:18:19'),
(42, 'Chad', 'TD', 'TCD', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:52:25', '2017-01-10 11:18:19'),
(43, 'Chile', 'CL', 'CHL', '', 0, 1, 'Chilean Peso', 'CLP', '$', NULL, '2018-11-01 12:52:26', '2017-01-10 11:18:19'),
(44, 'China', 'CN', 'CHN', '', 0, 1, 'Yuan Renminbi', 'CNY', '¥', NULL, '2018-11-01 12:52:27', '2017-01-10 11:18:19'),
(45, 'Christmas Island', 'CX', 'CXR', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:52:28', '2017-01-10 11:18:20'),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:52:29', '2017-01-10 11:18:20'),
(47, 'Colombia', 'CO', 'COL', '', 0, 1, 'Peso', 'COP', '$', NULL, '2018-11-01 12:52:30', '2017-01-10 11:18:20'),
(48, 'Comoros', 'KM', 'COM', '', 0, 1, 'Comoran Franc', 'KMF', 'Fr', NULL, '2018-11-01 12:52:31', '2017-01-10 11:18:20'),
(49, 'Congo', 'CG', 'COG', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:52:32', '2017-01-10 11:18:20'),
(50, 'Cook Islands', 'CK', 'COK', '', 0, 1, 'New Zealand Dollars', 'NZD', '$', NULL, '2018-11-01 12:52:33', '2017-01-10 11:18:20'),
(51, 'Costa Rica', 'CR', 'CRI', '', 0, 1, 'Costa Rican Colon', 'CRC', '₡', NULL, '2020-04-08 09:48:21', '2017-01-10 11:18:20'),
(52, 'Cote D\'Ivoire', 'CI', 'CIV', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:52:35', '2017-01-10 11:18:20'),
(53, 'Croatia', 'HR', 'HRV', '', 0, 1, 'Croatian Dinar', 'HRK', 'kn', NULL, '2018-11-01 12:52:36', '2017-01-10 11:18:20'),
(54, 'Cuba', 'CU', 'CUB', '', 0, 1, 'Cuban Peso', 'CUP', '$', NULL, '2018-11-01 12:52:36', '2017-01-10 11:18:20'),
(55, 'Cyprus', 'CY', 'CYP', '', 0, 1, 'Cypriot Pound', 'CYP', '€', NULL, '2018-11-01 12:52:39', '2017-01-10 11:18:20'),
(56, 'Czech Republic', 'CZ', 'CZE', '', 0, 1, 'Koruna', 'CZK', 'Kč', NULL, '2020-04-08 09:48:22', '2017-01-10 11:18:21'),
(57, 'Denmark', 'DK', 'DNK', '', 0, 1, 'Danish Krone', 'DKK', 'kr', NULL, '2018-11-01 12:52:42', '2017-01-10 11:18:21'),
(58, 'Djibouti', 'DJ', 'DJI', '', 0, 1, 'Djiboutian Franc', 'DJF', 'Fr', NULL, '2018-11-01 12:52:43', '2017-01-10 11:18:21'),
(59, 'Dominica', 'DM', 'DMA', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:52:44', '2017-01-10 11:18:21'),
(60, 'Dominican Republic', 'DO', 'DOM', '', 0, 1, 'Dominican Peso', 'DOP', '$', NULL, '2018-11-01 12:52:45', '2017-01-10 11:18:21'),
(61, 'East Timor', 'TL', 'TLS', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2017-01-24 09:58:19', '0000-00-00 00:00:00'),
(62, 'Ecuador', 'EC', 'ECU', '', 0, 1, 'Sucre', 'ECS', '$', NULL, '2018-11-01 12:52:47', '2017-01-10 11:18:21'),
(63, 'Egypt', 'EG', 'EGY', '', 0, 1, 'Egyptian Pound', 'EGP', '£', NULL, '2018-11-01 12:52:48', '2017-01-10 11:18:21'),
(64, 'El Salvador', 'SV', 'SLV', '', 0, 1, 'Salvadoran Colon', 'SVC', '$', NULL, '2018-11-01 12:52:50', '2017-01-10 11:18:21'),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:52:51', '2017-01-10 11:18:21'),
(66, 'Eritrea', 'ER', 'ERI', '', 0, 1, 'Ethiopian Birr', 'ETB', 'Nfk', NULL, '2018-11-01 12:52:52', '2017-01-10 11:18:21'),
(67, 'Estonia', 'EE', 'EST', '', 0, 1, 'Estonian Kroon', 'EEK', '€', NULL, '2018-11-01 12:52:52', '2017-01-10 11:18:21'),
(68, 'Ethiopia', 'ET', 'ETH', '', 0, 1, 'Ethiopian Birr', 'ETB', 'Br', NULL, '2018-11-01 12:52:53', '2017-01-10 11:18:21'),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', '', 0, 1, 'Falkland Pound', 'FKP', '£', NULL, '2018-11-01 12:52:54', '2017-01-10 11:18:22'),
(70, 'Faroe Islands', 'FO', 'FRO', '', 0, 1, 'Danish Krone', 'DKK', 'kr', NULL, '2018-11-01 12:52:55', '2017-01-10 11:18:22'),
(71, 'Fiji', 'FJ', 'FJI', '', 0, 1, 'Fijian Dollar', 'FJD', '$', NULL, '2018-11-01 12:53:06', '2017-01-10 11:18:22'),
(72, 'Finland', 'FI', 'FIN', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:09', '2017-01-10 11:18:22'),
(74, 'France, Metropolitan', 'FR', 'FRA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 1, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:10', '2017-01-10 11:18:22'),
(75, 'French Guiana', 'GF', 'GUF', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:11', '2017-01-10 11:18:22'),
(76, 'French Polynesia', 'PF', 'PYF', '', 0, 1, 'CFP Franc', 'XPF', 'Fr', NULL, '2018-11-01 12:53:12', '2017-01-10 11:18:22'),
(77, 'French Southern Territories', 'TF', 'ATF', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:13', '2017-01-10 11:18:22'),
(78, 'Gabon', 'GA', 'GAB', '', 0, 1, 'CFA Franc BEAC', 'XAF', 'Fr', NULL, '2018-11-01 12:53:15', '2017-01-10 11:18:22'),
(79, 'Gambia', 'GM', 'GMB', '', 0, 1, 'Dalasi', 'GMD', 'D', NULL, '2018-11-01 12:53:15', '2017-01-10 11:18:22'),
(80, 'Georgia', 'GE', 'GEO', '', 0, 1, 'Lari', 'GEL', 'ლ', NULL, '2020-04-08 09:48:23', '2017-01-10 11:18:22'),
(81, 'Germany', 'DE', 'DEU', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 1, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:19', '2017-01-10 11:18:22'),
(82, 'Ghana', 'GH', 'GHA', '', 0, 1, 'Ghana cedi', 'GHS', 'GH¢', NULL, '2019-03-29 15:28:42', '2017-01-10 11:18:22'),
(83, 'Gibraltar', 'GI', 'GIB', '', 0, 1, 'Gibraltar Pound', 'GIP', '£', NULL, '2018-11-01 12:53:21', '2017-01-10 11:18:22'),
(84, 'Greece', 'GR', 'GRC', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:22', '2017-01-10 11:18:22'),
(85, 'Greenland', 'GL', 'GRL', '', 0, 1, 'Danish Krone', 'DKK', 'kr', NULL, '2018-11-01 12:53:23', '2017-01-10 11:18:22'),
(86, 'Grenada', 'GD', 'GRD', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:53:25', '2017-01-10 11:18:22'),
(87, 'Guadeloupe', 'GP', 'GLP', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:26', '2017-01-10 11:18:22'),
(88, 'Guam', 'GU', 'GUM', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:22'),
(89, 'Guatemala', 'GT', 'GTM', '', 0, 1, 'Quetzal', 'GTQ', 'Q', NULL, '2018-11-01 12:53:27', '2017-01-10 11:18:22'),
(90, 'Guinea', 'GN', 'GIN', '', 0, 1, 'Guinean Franc', 'GNF', 'Fr', NULL, '2018-11-01 12:53:28', '2017-01-10 11:18:22'),
(91, 'Guinea-Bissau', 'GW', 'GNB', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:53:30', '2017-01-10 11:18:22'),
(92, 'Guyana', 'GY', 'GUY', '', 0, 1, 'Guyanaese Dollar', 'GYD', '$', NULL, '2018-11-01 12:53:30', '2017-01-10 11:18:22'),
(93, 'Haiti', 'HT', 'HTI', '', 0, 1, 'Gourde', 'HTG', 'G', NULL, '2018-11-01 12:53:32', '2017-01-10 11:18:23'),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:53:32', '2017-01-10 11:18:23'),
(95, 'Honduras', 'HN', 'HND', '', 0, 1, 'Lempira', 'HNL', 'L', NULL, '2018-11-01 12:53:34', '2017-01-10 11:18:23'),
(96, 'Hong Kong', 'HK', 'HKG', '', 0, 1, 'HKD', 'HKD', '$', NULL, '2018-11-01 12:53:35', '2017-01-10 11:18:23'),
(97, 'Hungary', 'HU', 'HUN', '', 0, 1, 'Forint', 'HUF', 'Ft', NULL, '2018-11-01 12:53:37', '2017-01-10 11:18:23'),
(98, 'Iceland', 'IS', 'ISL', '', 0, 1, 'Icelandic Krona', 'ISK', 'kr', NULL, '2018-11-01 12:53:39', '2017-01-10 11:18:23'),
(99, 'India', 'IN', 'IND', '', 0, 1, 'Indian Rupee', 'INR', '&#x20b9;', NULL, '2018-04-26 14:03:24', '2017-01-10 11:18:23'),
(100, 'Indonesia', 'ID', 'IDN', '', 0, 1, 'Indonesian Rupiah', 'IDR', 'Rp', NULL, '2018-11-01 12:53:42', '2017-01-10 11:18:23'),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', '', 0, 1, 'Iranian Rial', 'IRR', '﷼', NULL, '2020-04-08 09:48:23', '2017-01-10 11:18:23'),
(102, 'Iraq', 'IQ', 'IRQ', '', 0, 1, 'Iraqi Dinar', 'IQD', 'ع.د', NULL, '2020-04-08 09:48:24', '2017-01-10 11:18:23'),
(103, 'Ireland', 'IE', 'IRL', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:45', '2017-01-10 11:18:23'),
(104, 'Israel', 'IL', 'ISR', '', 0, 1, 'Shekel', 'ILS', '₪', NULL, '2020-04-08 09:48:25', '2017-01-10 11:18:23'),
(105, 'Italy', 'IT', 'ITA', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:53:50', '2017-01-10 11:18:23'),
(106, 'Jamaica', 'JM', 'JAM', '', 0, 1, 'Jamaican Dollar', 'JMD', '$', NULL, '2018-11-01 12:53:51', '2017-01-10 11:18:23'),
(107, 'Japan', 'JP', 'JPN', '', 0, 1, 'Japanese Yen', 'JPY', '¥', NULL, '2018-11-01 12:53:55', '2017-01-10 11:18:24'),
(108, 'Jordan', 'JO', 'JOR', '', 0, 1, 'Jordanian Dinar', 'JOD', 'د.ا', NULL, '2020-04-08 09:48:26', '2017-01-10 11:18:24'),
(109, 'Kazakhstan', 'KZ', 'KAZ', '', 0, 1, 'Tenge', 'KZT', NULL, NULL, '2017-01-10 12:18:24', '2017-01-10 11:18:24'),
(110, 'Kenya', 'KE', 'KEN', '', 0, 1, 'Kenyan Shilling', 'KES', 'Sh', NULL, '2018-11-01 12:54:01', '2017-01-10 11:18:24'),
(111, 'Kiribati', 'KI', 'KIR', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:54:01', '2017-01-10 11:18:24'),
(112, 'North Korea', 'KP', 'PRK', '', 0, 1, 'Won', 'KPW', '₩', NULL, '2020-04-08 09:48:26', '2017-01-10 11:18:24'),
(113, 'Korea, Republic of', 'KR', 'KOR', '', 0, 1, 'Won', 'KRW', '₩', NULL, '2020-04-08 09:48:27', '2017-01-10 11:18:25'),
(114, 'Kuwait', 'KW', 'KWT', '', 0, 1, 'Kuwaiti Dinar', 'KWD', 'د.ك', NULL, '2020-04-08 09:48:27', '2017-01-10 11:18:25'),
(115, 'Kyrgyzstan', 'KG', 'KGZ', '', 0, 1, 'Som', 'KGS', 'с', NULL, '2020-04-08 09:48:28', '2017-01-10 11:18:25'),
(116, 'Lao People\'s Democratic Republic', 'LA', 'LAO', '', 0, 1, 'Kip', 'LAK', '₭', NULL, '2020-04-08 09:48:28', '2017-01-10 11:18:25'),
(117, 'Latvia', 'LV', 'LVA', '', 0, 1, 'Lat', 'LVL', '€', NULL, '2018-11-01 12:54:12', '2017-01-10 11:18:25'),
(118, 'Lebanon', 'LB', 'LBN', '', 0, 1, 'Lebanese Pound', 'LBP', 'ل.ل', NULL, '2020-04-08 09:48:29', '2017-01-10 11:18:25'),
(119, 'Lesotho', 'LS', 'LSO', '', 0, 1, 'Loti', 'LSL', 'L', NULL, '2018-11-01 12:54:14', '2017-01-10 11:18:25'),
(120, 'Liberia', 'LR', 'LBR', '', 0, 1, 'Liberian Dollar', 'LRD', '$', NULL, '2018-11-01 12:54:16', '2017-01-10 11:18:25'),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', '', 0, 1, 'Libyan Dinar', 'LYD', 'ل.د', NULL, '2020-04-08 09:48:29', '2017-01-10 11:18:25'),
(122, 'Liechtenstein', 'LI', 'LIE', '', 0, 1, 'Swiss Franc', 'CHF', 'Fr', NULL, '2018-11-01 12:54:19', '2017-01-10 11:18:25'),
(123, 'Lithuania', 'LT', 'LTU', '', 0, 1, 'Lita', 'LTL', '€', NULL, '2018-11-01 12:54:19', '2017-01-10 11:18:25'),
(124, 'Luxembourg', 'LU', 'LUX', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:54:20', '2017-01-10 11:18:25'),
(125, 'Macau', 'MO', 'MAC', '', 0, 1, 'Pataca', 'MOP', 'P', NULL, '2018-11-01 12:54:21', '2017-01-10 11:18:25'),
(126, 'FYROM', 'MK', 'MKD', '', 0, 1, 'Denar', 'MKD', 'ден', NULL, '2020-04-08 09:48:30', '2017-01-10 11:18:25'),
(127, 'Madagascar', 'MG', 'MDG', '', 0, 1, 'Malagasy Franc', 'MGA', 'Ar', NULL, '2018-11-01 12:54:24', '2017-01-10 11:18:25'),
(128, 'Malawi', 'MW', 'MWI', '', 0, 1, 'Malawian Kwacha', 'MWK', 'MK', NULL, '2018-11-01 12:54:25', '2017-01-10 11:18:25'),
(129, 'Malaysia', 'MY', 'MYS', '', 0, 1, 'Ringgit', 'MYR', 'RM', NULL, '2018-11-01 12:54:25', '2017-01-10 11:18:25'),
(130, 'Maldives', 'MV', 'MDV', '', 0, 1, 'Rufiyaa', 'MVR', '.ރ', NULL, '2020-04-08 09:48:30', '2017-01-10 11:18:25'),
(131, 'Mali', 'ML', 'MLI', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:54:29', '2017-01-10 11:18:26'),
(132, 'Malta', 'MT', 'MLT', '', 0, 1, 'Maltese Lira', 'MTL', '€', NULL, '2018-11-01 12:54:30', '2017-01-10 11:18:26'),
(133, 'Marshall Islands', 'MH', 'MHL', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:26'),
(134, 'Martinique', 'MQ', 'MTQ', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:54:31', '2017-01-10 11:18:26'),
(135, 'Mauritania', 'MR', 'MRT', '', 0, 1, 'Ouguiya', 'MRO', 'UM', NULL, '2018-11-01 12:54:32', '2017-01-10 11:18:26'),
(136, 'Mauritius', 'MU', 'MUS', '', 0, 1, 'Mauritian Rupee', 'MUR', '₨', NULL, '2020-04-08 09:48:31', '2017-01-10 11:18:26'),
(137, 'Mayotte', 'YT', 'MYT', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:54:36', '2017-01-10 11:18:26'),
(138, 'Mexico', 'MX', 'MEX', '', 0, 1, 'Peso', 'MXN', '$', NULL, '2018-11-01 12:54:37', '2017-01-10 11:18:26'),
(139, 'Micronesia, Federated States of', 'FM', 'FSM', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:26'),
(140, 'Moldova, Republic of', 'MD', 'MDA', '', 0, 1, 'Leu', 'MDL', 'L', NULL, '2018-11-01 12:54:38', '2017-01-10 11:18:26'),
(141, 'Monaco', 'MC', 'MCO', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:54:39', '2017-01-10 11:18:27'),
(142, 'Mongolia', 'MN', 'MNG', '', 0, 1, 'Tugrik', 'MNT', '₮', NULL, '2020-04-08 09:48:31', '2017-01-10 11:18:27'),
(143, 'Montserrat', 'MS', 'MSR', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:54:42', '2017-01-10 11:18:27'),
(144, 'Morocco', 'MA', 'MAR', '', 0, 1, 'Dirham', 'MAD', 'د.م.', NULL, '2020-04-08 09:48:32', '2017-01-10 11:18:27'),
(145, 'Mozambique', 'MZ', 'MOZ', '', 0, 1, 'Metical', 'MZN', 'MT', NULL, '2018-11-01 12:54:50', '2017-01-10 11:18:27'),
(146, 'Myanmar', 'MM', 'MMR', '', 0, 1, 'Kyat', 'MMK', 'Ks', NULL, '2018-11-01 12:54:52', '2017-01-10 11:18:27'),
(147, 'Namibia', 'NA', 'NAM', '', 0, 1, 'Dollar', 'NAD', '$', NULL, '2018-11-01 12:54:53', '2017-01-10 11:18:27'),
(148, 'Nauru', 'NR', 'NRU', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:54:54', '2017-01-10 11:18:27'),
(149, 'Nepal', 'NP', 'NPL', '', 0, 1, 'Nepalese Rupee', 'NPR', '₨', NULL, '2020-04-08 09:48:33', '2017-01-10 11:18:27'),
(150, 'Netherlands', 'NL', 'NLD', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:54:57', '2017-01-10 11:18:27'),
(151, 'Netherlands Antilles', 'AN', 'ANT', '', 0, 1, 'Netherlands Antilles Guilder', 'ANG', NULL, NULL, '2017-01-10 12:18:27', '2017-01-10 11:18:27'),
(152, 'New Caledonia', 'NC', 'NCL', '', 0, 1, 'CFP Franc', 'XPF', 'Fr', NULL, '2018-11-01 12:55:00', '2017-01-10 11:18:27'),
(153, 'New Zealand', 'NZ', 'NZL', '', 0, 1, 'New Zealand Dollars', 'NZD', '$', NULL, '2018-11-01 12:55:01', '2017-01-10 11:18:27'),
(154, 'Nicaragua', 'NI', 'NIC', '', 0, 1, 'Cordoba Oro', 'NIO', 'C$', NULL, '2018-11-01 12:55:03', '2017-01-10 11:18:27'),
(155, 'Niger', 'NE', 'NER', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:55:05', '2017-01-10 11:18:27'),
(156, 'Nigeria', 'NG', 'NGA', '', 0, 1, 'Naira', 'NGN', '&#8358;', NULL, '2017-01-24 10:05:23', '2017-01-10 11:18:27'),
(157, 'Niue', 'NU', 'NIU', '', 0, 1, 'New Zealand Dollars', 'NZD', '$', NULL, '2018-11-01 12:55:06', '2017-01-10 11:18:28'),
(158, 'Norfolk Island', 'NF', 'NFK', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:55:08', '2017-01-10 11:18:28'),
(159, 'Northern Mariana Islands', 'MP', 'MNP', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:28'),
(160, 'Norway', 'NO', 'NOR', '', 0, 1, 'Norwegian Krone', 'NOK', 'kr', NULL, '2018-11-01 12:55:08', '2017-01-10 11:18:28'),
(161, 'Oman', 'OM', 'OMN', '', 0, 1, 'Sul Rial', 'OMR', 'ر.ع', NULL, '2019-10-25 15:29:26', '2017-01-10 11:18:28'),
(162, 'Pakistan', 'PK', 'PAK', '', 0, 1, 'Rupee', 'PKR', '₨', NULL, '2020-04-08 09:48:34', '2017-01-10 11:18:28'),
(163, 'Palau', 'PW', 'PLW', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:28'),
(164, 'Panama', 'PA', 'PAN', '', 0, 1, 'Balboa', 'PAB', 'B/.', NULL, '2018-11-01 12:55:11', '2017-01-10 11:18:28'),
(165, 'Papua New Guinea', 'PG', 'PNG', '', 0, 1, 'Kina', 'PGK', 'K', NULL, '2018-11-01 12:55:12', '2017-01-10 11:18:28'),
(166, 'Paraguay', 'PY', 'PRY', '', 0, 1, 'Guarani', 'PYG', '₲', NULL, '2020-04-08 09:48:35', '2017-01-10 11:18:28'),
(167, 'Peru', 'PE', 'PER', '', 0, 1, 'Nuevo Sol', 'PEN', 'S/.', NULL, '2018-11-01 12:55:16', '2017-01-10 11:18:28'),
(168, 'Philippines', 'PH', 'PHL', '', 0, 1, 'Peso', 'PHP', '₱', NULL, '2020-04-08 09:48:36', '2017-01-10 11:18:28'),
(169, 'Pitcairn', 'PN', 'PCN', '', 0, 1, 'New Zealand Dollars', 'NZD', '$', NULL, '2018-11-01 12:55:18', '2017-01-10 11:18:29'),
(170, 'Poland', 'PL', 'POL', '', 0, 1, 'Zloty', 'PLN', 'zł', NULL, '2020-04-08 09:48:36', '2017-01-10 11:18:29'),
(171, 'Portugal', 'PT', 'PRT', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:55:21', '2017-01-10 11:18:29'),
(172, 'Puerto Rico', 'PR', 'PRI', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:29'),
(173, 'Qatar', 'QA', 'QAT', '', 0, 1, 'Rial', 'QAR', 'ر.ق', NULL, '2020-04-08 09:48:37', '2017-01-10 11:18:29'),
(174, 'Reunion', 'RE', 'REU', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:55:24', '2017-01-10 11:18:29'),
(175, 'Romania', 'RO', 'ROM', '', 0, 1, 'Leu', 'RON', 'lei', NULL, '2018-11-01 12:55:25', '2017-01-10 11:18:29'),
(176, 'Russian Federation', 'RU', 'RUS', '', 0, 1, 'Ruble', 'RUB', '₽', NULL, '2020-04-08 09:48:38', '2017-01-10 11:18:29'),
(177, 'Rwanda', 'RW', 'RWA', '', 0, 1, 'Rwanda Franc', 'RWF', 'Fr', NULL, '2018-11-01 12:55:26', '2017-01-10 11:18:29'),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:55:27', '2017-01-10 11:18:29'),
(179, 'Saint Lucia', 'LC', 'LCA', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:55:29', '2017-01-10 11:18:29'),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', '', 0, 1, 'East Caribbean Dollar', 'XCD', '$', NULL, '2018-11-01 12:55:30', '2017-01-10 11:18:29'),
(181, 'Samoa', 'WS', 'WSM', '', 0, 1, 'Euros', 'EUR', 'T', NULL, '2018-11-01 12:55:31', '2017-01-10 11:18:29'),
(182, 'San Marino', 'SM', 'SMR', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:55:32', '2017-01-10 11:18:30'),
(183, 'Sao Tome and Principe', 'ST', 'STP', '', 0, 1, 'Dobra', 'STD', 'Db', NULL, '2018-11-01 12:55:33', '2017-01-10 11:18:30'),
(184, 'Saudi Arabia', 'SA', 'SAU', '', 0, 1, 'Riyal', 'SAR', 'ر.س', NULL, '2020-04-08 09:48:38', '2017-01-10 11:18:30'),
(185, 'Senegal', 'SN', 'SEN', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:55:36', '2017-01-10 11:18:30'),
(186, 'Seychelles', 'SC', 'SYC', '', 0, 1, 'Rupee', 'SCR', '₨', NULL, '2020-04-08 09:48:38', '2017-01-10 11:18:30'),
(187, 'Sierra Leone', 'SL', 'SLE', '', 0, 1, 'Leone', 'SLL', 'Le', NULL, '2018-11-01 12:55:38', '2017-01-10 11:18:30'),
(188, 'Singapore', 'SG', 'SGP', '', 0, 1, 'Dollar', 'SGD', '$', NULL, '2018-11-01 12:55:41', '2017-01-10 11:18:30'),
(189, 'Slovak Republic', 'SK', 'SVK', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{zone}\r\n{country}', 0, 1, 'Koruna', 'SKK', '€', NULL, '2018-11-01 12:55:42', '2017-01-10 11:18:30'),
(190, 'Slovenia', 'SI', 'SVN', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:55:44', '2017-01-10 11:18:30'),
(191, 'Solomon Islands', 'SB', 'SLB', '', 0, 1, 'Solomon Islands Dollar', 'SBD', '$', NULL, '2018-11-01 12:55:46', '2017-01-10 11:18:30'),
(192, 'Somalia', 'SO', 'SOM', '', 0, 1, 'Shilling', 'SOS', 'Sh', NULL, '2018-11-01 12:55:48', '2017-01-10 11:18:30'),
(193, 'South Africa', 'ZA', 'ZAF', '', 0, 1, 'Rand', 'ZAR', 'R', NULL, '2018-04-26 14:03:24', '2017-01-10 11:18:30'),
(194, 'South Georgia &amp; South Sandwich Islands', 'GS', 'SGS', '', 0, 1, 'Pound Sterling', 'GBP', '£', NULL, '2018-11-01 12:55:49', '2017-01-10 11:18:30'),
(195, 'Spain', 'ES', 'ESP', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:55:50', '2017-01-10 11:18:30'),
(196, 'Sri Lanka', 'LK', 'LKA', '', 0, 1, 'Rupee', 'LKR', 'Rs', NULL, '2018-11-01 12:55:53', '2017-01-10 11:18:30'),
(197, 'St. Helena', 'SH', 'SHN', '', 0, 1, 'Pound Sterling', 'GBP', '£', NULL, '2018-11-01 12:55:53', '2017-01-10 11:18:31'),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', '', 0, 1, 'Euro', 'EUR', '€', NULL, '2018-11-01 12:55:55', '2017-01-10 11:18:31'),
(199, 'Sudan', 'SD', 'SDN', '', 0, 1, 'Dinar', 'SDG', 'ج.س.', NULL, '2020-04-08 09:48:39', '2017-01-10 11:18:31'),
(200, 'Suriname', 'SR', 'SUR', '', 0, 1, 'Surinamese Guilder', 'SRD', '$', NULL, '2018-11-01 12:55:58', '2017-01-10 11:18:31'),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '', 0, 1, 'Norwegian Krone', 'NOK', 'kr', NULL, '2018-11-01 12:55:59', '2017-01-10 11:18:31'),
(202, 'Swaziland', 'SZ', 'SWZ', '', 0, 1, 'Lilangeni', 'SZL', 'L', NULL, '2018-11-01 12:56:00', '2017-01-10 11:18:31'),
(203, 'Sweden', 'SE', 'SWE', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 1, 1, 'Krona', 'SEK', 'kr', NULL, '2018-11-01 12:56:01', '2017-01-10 11:18:31'),
(204, 'Switzerland', 'CH', 'CHE', '', 0, 1, 'Swiss Franc', 'CHF', 'Fr', NULL, '2018-11-01 12:56:02', '2017-01-10 11:18:31'),
(205, 'Syrian Arab Republic', 'SY', 'SYR', '', 0, 1, 'Syrian Pound', 'SYP', '£', NULL, '2018-11-01 12:56:03', '2017-01-10 11:18:31'),
(206, 'Taiwan', 'TW', 'TWN', '', 0, 1, 'Dollar', 'TWD', '$', NULL, '2018-11-01 12:56:06', '2017-01-10 11:18:31'),
(207, 'Tajikistan', 'TJ', 'TJK', '', 0, 1, 'Tajikistan Ruble', 'TJS', 'ЅМ', NULL, '2020-04-08 09:48:39', '2017-01-10 11:18:31'),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', '', 0, 1, 'Shilling', 'TZS', 'Sh', NULL, '2018-11-01 12:56:08', '2017-01-10 11:18:31'),
(209, 'Thailand', 'TH', 'THA', '', 0, 1, 'Baht', 'THB', '฿', NULL, '2020-04-08 09:48:40', '2017-01-10 11:18:31'),
(210, 'Togo', 'TG', 'TGO', '', 0, 1, 'CFA Franc BCEAO', 'XOF', 'Fr', NULL, '2018-11-01 12:56:12', '2017-01-10 11:18:31'),
(211, 'Tokelau', 'TK', 'TKL', '', 0, 1, 'New Zealand Dollars', 'NZD', '$', NULL, '2018-11-01 12:56:12', '2017-01-10 11:18:31'),
(212, 'Tonga', 'TO', 'TON', '', 0, 1, 'PaÕanga', 'TOP', 'T$', NULL, '2018-11-01 12:56:13', '2017-01-10 11:18:31'),
(213, 'Trinidad and Tobago', 'TT', 'TTO', '', 0, 1, 'Trinidad and Tobago Dollar', 'TTD', '$', NULL, '2018-11-01 12:56:16', '2017-01-10 11:18:31'),
(214, 'Tunisia', 'TN', 'TUN', '', 0, 1, 'Tunisian Dinar', 'TND', 'د.ت', NULL, '2020-04-08 09:48:40', '2017-01-10 11:18:31'),
(215, 'Turkey', 'TR', 'TUR', '', 0, 1, 'Lira', 'TRY', NULL, NULL, '2017-01-10 12:18:31', '2017-01-10 11:18:31'),
(216, 'Turkmenistan', 'TM', 'TKM', '', 0, 1, 'Manat', 'TMT', 'm', NULL, '2018-11-01 12:56:19', '2017-01-10 11:18:32'),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:32'),
(218, 'Tuvalu', 'TV', 'TUV', '', 0, 1, 'Australian Dollars', 'AUD', '$', NULL, '2018-11-01 12:56:20', '2017-01-10 11:18:32'),
(219, 'Uganda', 'UG', 'UGA', '', 0, 1, 'Shilling', 'UGX', 'Sh', NULL, '2018-11-01 12:56:22', '2017-01-10 11:18:32'),
(220, 'Ukraine', 'UA', 'UKR', '', 0, 1, 'Hryvnia', 'UAH', '₴', NULL, '2020-04-08 09:48:40', '2017-01-10 11:18:32'),
(221, 'United Arab Emirates', 'AE', 'ARE', '', 0, 1, 'Dirham', 'AED', 'د.إ', NULL, '2020-04-08 09:48:41', '2017-01-10 11:18:32'),
(222, 'United Kingdom', 'GB', 'GBR', '', 1, 1, 'Pound Sterling', 'GBP', '£', NULL, '2018-11-01 12:56:25', '2017-01-10 11:18:32'),
(223, 'United States', 'US', 'USA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:32'),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:32'),
(225, 'Uruguay', 'UY', 'URY', '', 0, 1, 'Peso', 'UYU', '$', NULL, '2018-11-01 12:56:26', '2017-01-10 11:18:32'),
(226, 'Uzbekistan', 'UZ', 'UZB', '', 0, 1, 'Som', 'UZS', NULL, NULL, '2017-01-10 12:18:32', '2017-01-10 11:18:32'),
(227, 'Vanuatu', 'VU', 'VUT', '', 0, 1, 'Vatu', 'VUV', 'Vt', NULL, '2018-11-01 12:56:29', '2017-01-10 11:18:32'),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', '', 0, 1, 'Euros', 'EUR', '€', NULL, '2018-11-01 12:56:31', '2017-01-10 11:18:32'),
(229, 'Venezuela', 'VE', 'VEN', '', 0, 1, 'Bolivar', 'VEF', 'Bs F', NULL, '2018-11-01 12:56:32', '2017-01-10 11:18:32'),
(230, 'Viet Nam', 'VN', 'VNM', '', 0, 1, 'Dong', 'VND', '₫', NULL, '2020-04-08 09:48:42', '2017-01-10 11:18:32'),
(231, 'Virgin Islands (British)', 'VG', 'VGB', '', 0, 1, 'United States Dollar', 'USD', '$', '', '2018-10-15 13:01:12', '2017-01-10 11:18:32'),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2018-10-15 13:01:12', '2017-01-10 11:18:32'),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', '', 0, 1, 'CFP Franc', 'XPF', 'Fr', NULL, '2018-11-01 12:56:34', '2017-01-10 11:18:33'),
(234, 'Western Sahara', 'EH', 'ESH', '', 0, 1, 'Dirham', 'MAD', 'د.م.', NULL, '2020-04-08 09:48:42', '2017-01-10 11:18:33'),
(235, 'Yemen', 'YE', 'YEM', '', 0, 1, 'Rial', 'YER', '﷼', NULL, '2020-04-08 09:48:43', '2017-01-10 11:18:33'),
(237, 'Democratic Republic of Congo', 'CD', 'COD', '', 0, 1, 'Congolese Frank', 'CDF', 'Fr', NULL, '2018-11-01 12:56:38', '2017-01-10 11:18:33'),
(238, 'Zambia', 'ZM', 'ZMB', '', 0, 1, 'Kwacha', 'ZMK', 'ZK', NULL, '2018-11-01 12:56:38', '2017-01-10 11:18:33'),
(239, 'Zimbabwe', 'ZW', 'ZWE', '', 0, 1, 'Zimbabwe Dollar', 'ZWD', 'P', NULL, '2018-11-01 12:56:39', '2017-01-10 11:18:33'),
(240, 'Jersey', 'JE', 'JEY', '', 1, 1, 'Pound Sterling', 'GBP', '£', NULL, '2018-11-01 12:56:40', '2017-01-10 11:18:33'),
(241, 'Guernsey', 'GG', 'GGY', '', 1, 1, 'Guernsey pound', 'GGP', '£', NULL, '2018-11-01 12:56:41', '2017-01-10 11:18:33'),
(242, 'Montenegro', 'ME', 'MNE', '', 0, 1, 'Euro', 'EUR', '€', NULL, '2018-11-01 12:56:42', '2017-01-10 11:18:33'),
(243, 'Serbia', 'RS', 'SRB', '', 0, 1, 'Serbian dinar', 'RSD', 'дин.', NULL, '2020-04-08 09:48:44', '2017-01-10 11:18:33'),
(244, 'Aaland Islands', 'AX', 'ALA', '', 0, 1, 'Euro', 'EUR', '€', NULL, '2018-11-01 12:56:45', '2017-01-10 11:18:33'),
(245, 'Bonaire, Sint Eustatius and Saba', 'BQ', 'BES', '', 0, 1, 'United States Dollar', 'USD', '$', NULL, '2017-01-24 09:58:19', '0000-00-00 00:00:00'),
(246, 'Curacao', 'CW', 'CUW', '', 0, 1, 'Netherlands Antillean guilder', 'NAF', 'ƒ', NULL, '2018-11-01 12:56:46', '0000-00-00 00:00:00'),
(247, 'Palestinian Territory, Occupied', 'PS', 'PSE', '', 0, 1, 'Jordanian dinar', 'JOD', '₪', NULL, '2020-04-08 09:48:45', '2017-01-10 11:18:33'),
(248, 'South Sudan', 'SS', 'SSD', '', 0, 1, 'South Sudanese Pound', 'SSP', '£', NULL, '2018-11-01 12:56:47', '0000-00-00 00:00:00'),
(249, 'St. Barthelemy', 'BL', 'BLM', '', 0, 1, 'Euro', 'EUR', '€', NULL, '2018-11-01 12:56:48', '2017-01-10 11:18:33'),
(250, 'St. Martin (French part)', 'MF', 'MAF', '', 0, 1, 'Netherlands Antillean guilder', 'ANG', '€', NULL, '2018-11-01 12:56:49', '2017-01-10 11:18:33'),
(251, 'Canary Islands', 'IC', 'ICA', '', 0, 1, 'Euro', 'EUR', NULL, NULL, '2017-01-10 12:30:48', '0000-00-00 00:00:00'),
(253, 'Aaland Islands', 'AX', 'ALA', '', 0, 1, 'Euro', 'EUR', '€', NULL, '2018-11-01 12:56:51', '2017-01-10 11:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE `coupon` (
  `coupon_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `discount` int(11) NOT NULL,
  `expires` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` char(255) NOT NULL,
  `total` float DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `uses_total` int(11) DEFAULT NULL,
  `uses_customer` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_category`
--

CREATE TABLE `coupon_category` (
  `coupon_category_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `session_category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_invoice`
--

CREATE TABLE `coupon_invoice` (
  `coupon_invoice_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_session`
--

CREATE TABLE `coupon_session` (
  `coupon_session_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `exchange_rate` float UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`currency_id`, `country_id`, `exchange_rate`) VALUES
(1, 223, 1);

-- --------------------------------------------------------

--
-- Table structure for table `discussion`
--

CREATE TABLE `discussion` (
  `discussion_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `subject` varchar(250) NOT NULL,
  `question` text NOT NULL,
  `created_on` int(11) NOT NULL,
  `replied` tinyint(1) NOT NULL DEFAULT '0',
  `session_id` int(10) UNSIGNED DEFAULT NULL,
  `lecture_id` int(10) UNSIGNED DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_account`
--

CREATE TABLE `discussion_account` (
  `discussion_account_id` int(11) NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `discussion_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_reply`
--

CREATE TABLE `discussion_reply` (
  `discussion_reply_id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `reply` text NOT NULL,
  `replied_on` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `download`
--

CREATE TABLE `download` (
  `download_id` int(11) NOT NULL,
  `download_name` varchar(250) NOT NULL,
  `created_on` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `description` text,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `download_file`
--

CREATE TABLE `download_file` (
  `download_file_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  `path` text NOT NULL,
  `created_on` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `download_session`
--

CREATE TABLE `download_session` (
  `download_session_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `email_template_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `message` text NOT NULL,
  `default` text NOT NULL,
  `placeholders` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `default_subject` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`email_template_id`, `name`, `description`, `message`, `default`, `placeholders`, `subject`, `default_subject`) VALUES
(1, 'Upcoming class reminder (physical location)', 'This message is sent to students to remind them when a class is scheduled to hold.', '\r\n                Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> is scheduled to hold as follows: <br/>\r\n\r\n<div><strong>Date:</strong> [CLASS_DATE]</div>\r\n<div><strong>Session:</strong> [SESSION_NAME]</div>\r\n<div><strong>Venue:</strong> [CLASS_VENUE] </div>\r\n<div><strong>Starts:</strong> [CLASS_START_TIME]</div>\r\n<div><strong>Ends:</strong> [CLASS_END_TIME]</div>    \r\n                ', '\r\n   Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> is scheduled to hold as follows: <br/>\r\n\r\n<div><strong>Date:</strong> [CLASS_DATE]</div>\r\n<div><strong>Session:</strong> [SESSION_NAME]</div>\r\n<div><strong>Venue:</strong> [CLASS_VENUE] </div>\r\n<div><strong>Starts:</strong> [CLASS_START_TIME]</div>\r\n<div><strong>Ends:</strong> [CLASS_END_TIME]</div>             \r\n                ', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_DATE] : The class date</li>\r\n                <li>[SESSION_NAME] : The name of the session the class is attached to</li>\r\n                <li>[CLASS_VENUE] : The venue of the class</li>\r\n                <li>[CLASS_START_TIME] : The start time for the class</li>\r\n                <li>[CLASS_END_TIME] : The end time for the class</li>\r\n                <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                </ul>\r\n                ', 'Upcoming Class: [CLASS_NAME]', 'Upcoming Class: [CLASS_NAME]'),
(2, 'Upcoming class reminder (online class)', 'This message is sent to students to remind them when an online class is scheduled to open.', '\r\n                Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> is scheduled as follows: <br/>\r\n\r\n<div><strong>Course:</strong> [COURSE_NAME]</div>\r\n<div><strong>Starts:</strong> [CLASS_DATE]</div>     \r\n                ', '\r\n   Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> is scheduled as follows: <br/>\r\n\r\n<div><strong>Course:</strong> [COURSE_NAME]</div>\r\n<div><strong>Starts:</strong> [CLASS_DATE]</div>          \r\n                ', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_DATE] : The class date</li>\r\n                <li>[COURSE_NAME] : The name of the session the class is attached to</li> \r\n                <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                </ul>\r\n                ', 'Upcoming Class: [CLASS_NAME]', 'Upcoming Class: [CLASS_NAME]'),
(3, 'Upcoming Test reminder', 'This message is sent to users when there is an upcoming test in a session/course they are enrolled in', '\r\n                    Please be reminded that the test <strong>\'[TEST_NAME]\'</strong> is scheduled as follows: <br/>\r\n<div><strong>Session/Course:</strong> [SESSION_NAME] </div>\r\n<div><strong>Opens:</strong> [OPENING_DATE]</div>\r\n<div><strong>Closes:</strong> [CLOSING_DATE]</div>\r\n<div>Please ensure you take the test before the closing date.</div>\r\n                ', '\r\n                    Please be reminded that the test <strong>\'[TEST_NAME]\'</strong> is scheduled as follows: <br/>\r\n<div><strong>Session/Course:</strong> [SESSION_NAME] </div>\r\n<div><strong>Opens:</strong> [OPENING_DATE]</div>\r\n<div><strong>Closes:</strong> [CLOSING_DATE]</div>\r\n<div>Please ensure you take the test before the closing date.</div>\r\n                ', '\r\n                <ul>\r\n                <li>[TEST_NAME] : The name of the test</li>\r\n                <li>[TEST_DESCRIPTION] : The description of the test</li>\r\n                <li>[SESSION_NAME] : The name of the session or course the test is attached to</li>\r\n                <li>[OPENING_DATE] : The opening date of the test</li>\r\n                <li>[CLOSING_DATE] : The closing date of the test</li>\r\n                <li>[PASSMARK] : The test passmark e.g. 50%</li>\r\n                <li>[MINUTES_ALLOWED]: The number of minutes allowed for the test</li> \r\n                <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                </ul>\r\n                ', 'Upcoming Test: [TEST_NAME]', 'Upcoming Test: [TEST_NAME]'),
(4, 'Online Class start notification', 'This message is sent to students when a scheduled online class opens', '\r\n                Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> for the course \'[COURSE_NAME]\' has started. <br/>\r\nClick this link to take this class now: <a href=\"[CLASS_URL]\">[CLASS_URL]</a><br/>\r\n                ', '\r\n               Please be reminded that the class <strong>\'[CLASS_NAME]\'</strong> for the course \'[COURSE_NAME]\' has started. <br/>\r\nClick this link to take this class now: <a href=\"[CLASS_URL]\">[CLASS_URL]</a><br/>\r\n                ', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_URL] : The url of the class</li> \r\n                <li>[COURSE_NAME] : The name of the course the class belongs to</li>\r\n                <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                </ul>\r\n                ', 'Class [CLASS_NAME] is open', 'Class [CLASS_NAME] is open'),
(5, 'Homework reminder', 'This message is sent to students reminding them when a homework is due', '\r\n                Please be reminded that the homework <strong>\'[HOMEWORK_NAME]\'</strong> is due on [DUE_DATE]. <br/>\r\nPlease click this link to submit your homework now: <a href=\"[HOMEWORK_URL]\">[HOMEWORK_URL]</a>\r\n                ', '\r\n                Please be reminded that the homework <strong>\'[HOMEWORK_NAME]\'</strong> is due on [DUE_DATE]. <br/>\r\nPlease click this link to submit your homework now: <a href=\"[HOMEWORK_URL]\">[HOMEWORK_URL]</a>\r\n                ', '\r\n                <ul>\r\n                <li>[NUMBER_OF_DAYS] : The number of days remaining till the homework due date e.g. 1,2,3</li>\r\n                <li>[DAY_TEXT] : The \'day\' text. Defaults to \'day\' if [NUMBER_OF_DAYS] is 1 and \'days\' if greater than 1.</li>\r\n                <li>[HOMEWORK_NAME] : The name of the homework</li>\r\n                <li>[HOMEWORK_URL] : The homework url</li>\r\n                <li>[HOMEWORK_INSTRUCTION] : The instructions for the homework</li>\r\n                <li>[PASSMARK] : The passmark for the homework</li>\r\n                 <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                <li>[DUE_DATE] : The homework due date</li>\r\n                <li>[OPENING_DATE] : The homework opening date</li>\r\n                </ul>\r\n                ', 'Homework due in [NUMBER_OF_DAYS] [DAY_TEXT]', 'Homework due in [NUMBER_OF_DAYS] [DAY_TEXT]'),
(6, 'Course closing reminder', 'Warning email sent to enrolled students about a course that will close soon.', '\r\n                Please be reminded that the course <strong>\'[COURSE_NAME]\'</strong> closes on [CLOSING_DATE]. <br/>\r\nPlease click this link to complete the course now: <a href=\"[COURSE_URL]\">[COURSE_URL]</a>\r\n                ', '\r\n                Please be reminded that the course <strong>\'[COURSE_NAME]\'</strong> closes on [CLOSING_DATE]. <br/>\r\nPlease click this link to complete the course now: <a href=\"[COURSE_URL]\">[COURSE_URL]</a>\r\n                ', '\r\n                <ul>\r\n                <li>[COURSE_NAME] : The name of the course</li>\r\n                <li>[COURSE_URL] : The course URL</li>\r\n                <li>[CLOSING_DATE] : The closing date for the course</li> \r\n                 <li>[NUMBER_OF_DAYS] : The number of days remaining till the closing date e.g. 1,2,3</li>\r\n                <li>[DAY_TEXT] : The \'day\' text. Defaults to \'day\' if [NUMBER_OF_DAYS] is 1 and \'days\' if greater than 1.</li>\r\n               \r\n                <li>[RECIPIENT_FIRST_NAME] : The first name of the recipient </li>\r\n                <li>[RECIPIENT_LAST_NAME] : The last name of the recipient </li>\r\n                </ul>\r\n                ', 'Course ends in [NUMBER_OF_DAYS] [DAY_TEXT]', 'Course ends in [NUMBER_OF_DAYS] [DAY_TEXT]');

-- --------------------------------------------------------

--
-- Table structure for table `forum_participant`
--

CREATE TABLE `forum_participant` (
  `forum_participant_id` int(11) NOT NULL,
  `forum_topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` char(1) NOT NULL,
  `notify` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forum_post`
--

CREATE TABLE `forum_post` (
  `forum_post_id` int(11) NOT NULL,
  `forum_topic_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `post_created_on` int(11) NOT NULL,
  `post_owner` int(11) NOT NULL,
  `post_owner_type` char(1) NOT NULL,
  `post_reply_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topic`
--

CREATE TABLE `forum_topic` (
  `forum_topic_id` int(11) NOT NULL,
  `topic_title` varchar(255) NOT NULL,
  `created_on` int(11) NOT NULL,
  `topic_owner` int(11) NOT NULL,
  `topic_owner_type` char(1) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `lecture_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `homework_id` int(20) NOT NULL,
  `title` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `student_id` int(11) UNSIGNED NOT NULL,
  `currency_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `amount` float NOT NULL,
  `cart` text NOT NULL,
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `payment_method_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_transaction`
--

CREATE TABLE `invoice_transaction` (
  `invoice_transaction_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ip`
--

CREATE TABLE `ip` (
  `ip_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lecture`
--

CREATE TABLE `lecture` (
  `lecture_id` int(11) NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `lecture_title` varchar(250) NOT NULL,
  `sort_order` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lecture_file`
--

CREATE TABLE `lecture_file` (
  `lecture_file_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `path` text NOT NULL,
  `created_on` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lecture_note`
--

CREATE TABLE `lecture_note` (
  `lecture_note_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lecture_page`
--

CREATE TABLE `lecture_page` (
  `lecture_page_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` char(255) NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `audio_code` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE `lesson` (
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `lesson_name` varchar(250) NOT NULL,
  `picture` varchar(250) DEFAULT NULL,
  `content` mediumtext,
  `sort_order` int(10) UNSIGNED DEFAULT '0',
  `test_required` tinyint(1) NOT NULL DEFAULT '0',
  `test_id` int(10) UNSIGNED DEFAULT NULL,
  `lesson_type` char(255) NOT NULL DEFAULT 's',
  `introduction` mediumtext,
  `enforce_lecture_order` tinyint(1) DEFAULT '0',
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_file`
--

CREATE TABLE `lesson_file` (
  `lesson_file_id` int(11) NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `path` text NOT NULL,
  `created_on` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_group`
--

CREATE TABLE `lesson_group` (
  `lesson_group_id` int(11) NOT NULL,
  `group_name` varchar(250) NOT NULL,
  `description` text,
  `sort_order` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lesson_to_lesson_group`
--

CREATE TABLE `lesson_to_lesson_group` (
  `lesson_to_lesson_group_id` int(11) NOT NULL,
  `lesson_group_id` int(11) NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `newsflash`
--

CREATE TABLE `newsflash` (
  `newsflash_id` int(20) NOT NULL,
  `title` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `picture` mediumtext NOT NULL,
  `account_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `password_reset_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `added_on` int(11) NOT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(10) UNSIGNED NOT NULL,
  `payment_method` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int(10) UNSIGNED DEFAULT NULL,
  `code` varchar(200) NOT NULL,
  `currency` mediumtext NOT NULL,
  `method_label` varchar(255) DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `payment_method`, `status`, `sort_order`, `code`, `currency`, `method_label`, `is_global`) VALUES
(1, 'Bank Deposit/Transfer', 1, 2, 'bank', 'ANY', 'Bank Deposit/Transfer', 1),
(2, 'Paypal', 1, 0, 'paypal', 'AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY', 'Paypal', 1),
(3, '2Checkout', 1, 0, 'twocheckout', 'AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY', '2Checkout', 1),
(4, 'Paystack', 1, 1, 'paystack', 'NGN', 'Paystack', 1),
(5, 'Stripe', 0, NULL, 'stripe', 'AUD CAD EUR GBP JPY USD NZD CHF HKD SGD SEK DKK PLN NOK HUF CZK ILS MXN MYR BRL PHP TWD THB TRY', 'Stripe', 0),
(6, 'Payu.co.za', 0, 0, 'payu', 'ARS BRL CLP COP CZK HUF INR MXN NGN PAB PEN PLN RON RUB ZAR TRY', 'PayU: Online Payment', 0),
(7, 'Payfast.co.za', 0, 0, 'payfast', 'ZAR', 'Payfast: Online Payment', 0),
(8, 'PayU money', 0, 0, 'payumoney', 'INR', 'PayU money: Online Payment', 0),
(9, 'iPay', 0, 0, 'ipay', 'GHS', 'iPay: Online Payment', 0),
(10, 'Rave by Flutterwave', 0, 0, 'rave', 'NGN ZAR GHS KES USD GBP EUR', 'Online Payment (Rave)', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method_currency`
--

CREATE TABLE `payment_method_currency` (
  `payment_method_currency_id` int(11) NOT NULL,
  `payment_method_id` int(11) UNSIGNED NOT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method_field`
--

CREATE TABLE `payment_method_field` (
  `payment_method_field_id` int(10) UNSIGNED NOT NULL,
  `key` varchar(250) NOT NULL,
  `label` mediumtext NOT NULL,
  `placeholder` mediumtext,
  `value` mediumtext,
  `serialized` tinyint(1) DEFAULT '0',
  `type` varchar(45) NOT NULL,
  `options` mediumtext,
  `class` varchar(250) DEFAULT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `payment_method_field`
--

INSERT INTO `payment_method_field` (`payment_method_field_id`, `key`, `label`, `placeholder`, `value`, `serialized`, `type`, `options`, `class`, `payment_method_id`) VALUES
(1, 'bank_instructions', 'Payment Insructions/Bank Details', '', '', 0, 'textarea', NULL, NULL, 1),
(5, 'public_key', 'Public Key', '', '', 0, 'text', NULL, NULL, 4),
(6, 'secret_key', 'Secret Key', '', '', 0, 'text', NULL, NULL, 4),
(10, 'clientid', 'Paypal Client Id', NULL, '', 0, 'text', NULL, NULL, 2),
(11, 'secret', 'Paypal Secret', NULL, '', 0, 'text', NULL, NULL, 2),
(12, 'mode', 'Mode', NULL, '', 0, 'radio', '1=Live,0=Test', NULL, 2),
(24, 'accountNumber', 'Account Number', NULL, '', 0, 'text', NULL, NULL, 3),
(25, 'secretWord', 'Secret Word', NULL, '', 0, 'text', NULL, NULL, 3),
(26, 'testMode', 'Mode', NULL, '', 0, 'radio', '1=Live,0=Test', NULL, 3),
(27, 'pkey', 'Publishable Key', NULL, '', 0, 'text', NULL, NULL, 5),
(28, 'skey', 'Secret Key', NULL, '', 0, 'text', NULL, NULL, 5),
(29, 'payu_easyplus_safe_key', 'Safe key', '', '', 0, 'text', NULL, NULL, 6),
(30, 'payu_easyplus_api_username', 'API Username', '', '', 0, 'text', NULL, NULL, 6),
(31, 'payu_easyplus_api_password', 'API Password', '', '', 0, 'text', NULL, NULL, 6),
(32, 'payu_easyplus_transaction_mode', 'Transaction mode', '', '', 0, 'select', 'staging=Staging,production=Production', NULL, 6),
(33, 'payu_easyplus_transaction_type', 'Transaction type', '', '', 0, 'select', 'PAYMENT=PAYMENT,RESERVE=RESERVE', NULL, 6),
(34, 'payu_easyplus_payment_currency', 'Billing currency', '', '', 0, 'select', 'ARS=Argentina Peso (ARS),BRL=Brazil Real (BRL),CLP=Chile Peso (CLP),COP=Colombia Peso (COP),CZK=Czech Republic Koruna (CZK),HUF=Hungary Forint (HUF),INR=India Rupee (INR),MXN=Mexico Peso (MXN),NGN=Nigeria Naira (NGN),PAB=Panama Balboa (PAB),PEN=Peru Sol (PEN),PLN=Poland Zloty (PLN),RON=Romania Leu (RON),RUB=Russia Ruble (RUB),ZAR=South Africa Rand (ZAR),TRY=Turkey Lira (TRY)', NULL, 6),
(35, 'payu_easyplus_method_credit_card', 'Payment Method: Credit Card', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(36, 'payu_easyplus_method_discovery_miles', 'Payment Method: Discovery Miles', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(37, 'payu_easyplus_method_ebucks', 'Payment Method: Ebucks', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(38, 'payu_easyplus_method_eft', 'Payment Method: EFT', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(39, 'payu_easyplus_method_masterpass', 'Payment Method: Masterpass', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(40, 'payu_easyplus_method_rcs', 'Payment Method: RCS', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(41, 'payu_easyplus_method_eft_pro', 'Payment Method: EFT Pro', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(42, 'payu_easyplus_method_creditcard_vco', 'Payment Method: Credit Card VCO', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(43, 'payu_easyplus_method_mobicred', 'Payment Method: Mobicred', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(44, 'payu_easyplus_debug', 'Debug', NULL, '', 0, 'checkbox', NULL, NULL, 6),
(45, 'payfast_merchant_id', 'PayFast Merchant ID', '', '', 0, 'text', NULL, NULL, 7),
(46, 'payfast_merchant_key', 'PayFast Merchant Key', '', '', 0, 'text', NULL, NULL, 7),
(47, 'payfast_sandbox', 'Sandbox Mode', '', '', 0, 'select', '1=Yes,0=No', NULL, 7),
(48, 'payfast_debug', 'Debug', '', '', 0, 'select', '1=Yes,0=No', NULL, 7),
(49, 'payfast_passphrase', 'PayFast Secure Passphrase', '', '', 0, 'text', NULL, NULL, 7),
(50, 'payumoney_merchant_key', 'Merchant Key', '', '', 0, 'text', NULL, NULL, 8),
(51, 'payumoney_salt', 'Salt', '', '', 0, 'text', NULL, NULL, 8),
(52, 'payumoney_sandbox', 'Sandbox Mode', NULL, '', 0, 'select', '1=Yes,0=No', NULL, 8),
(53, 'ipay_merchant_key', 'Merchant Key', '', '', 0, 'text', NULL, NULL, 9),
(54, 'pkey', 'Public Key', NULL, '', 0, 'text', NULL, NULL, 10),
(55, 'skey', 'Secret Key', NULL, '', 0, 'text', NULL, NULL, 10),
(56, 'mode', 'Payment Mode', NULL, '', 0, 'radio', '1=Live,0=Test', NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE `permission` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `permission` varchar(250) NOT NULL,
  `path` varchar(250) NOT NULL,
  `permission_group_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`permission_id`, `permission`, `path`, `permission_group_id`) VALUES
(1, 'view_students', 'student/index', 1),
(2, 'add_student', 'student/add', 1),
(3, 'view_student', 'student/view', 1),
(4, 'edit_student', 'student/edit', 1),
(5, 'delete_student', 'student/delete', 1),
(6, 'bulk_enroll', 'student/massenroll', 1),
(7, 'enroll', 'student/enroll', 1),
(8, 'view_sessions', 'student/sessions', 2),
(9, 'add_session', 'student/addsession', 2),
(10, 'edit_session', 'student/editsession', 2),
(11, 'delete_session', 'student/deletesession', 2),
(12, 'export_student', 'student/export', 2),
(13, 'export_student_attendance', 'student/exportbulkattendance', 2),
(14, 'view_attendance_sheet', 'student/exportattendance', 2),
(15, 'set_attendance', 'student/attendance', 3),
(16, 'set_bulk_attendance', 'student/attendancebulk', 3),
(17, 'set_import_attendance', 'student/attendanceimport', 3),
(18, 'create_certificate_list', 'student/certificatelist', 3),
(19, 'set_attendance_dates', 'student/attendancedate', 3),
(20, 'view_classes', 'lessson/index', 4),
(21, 'add_class', 'lesson/add', 4),
(22, 'edit_class', 'lesson/edit', 4),
(23, 'delete_class', 'lesson/delete', 4),
(24, 'view_notes', 'homework/index', 5),
(25, 'add_note', 'homework/add', 5),
(26, 'edit_note', 'homework/edit', 5),
(27, 'delete_note', 'homework/delete', 5),
(28, 'view_blog', 'news/index', 6),
(29, 'add_blog', 'news/add', 6),
(30, 'edit_blog', 'news/edit', 6),
(31, 'delete_blog', 'news/delete', 6),
(32, 'manage_files', 'filemanager/index', 7),
(33, 'view_articles', 'articles/index', 8),
(34, 'add_article', 'articles/add', 8),
(35, 'edit_article', 'articles/edit', 8),
(36, 'delete_article', 'articles/delete', 8),
(37, 'view_widgets', 'widget/index', 9),
(38, 'create_widget', 'widget/create', 9),
(39, 'delete_widget', 'widget/delete', 9),
(40, 'view_registration_fields', 'setting/fields', 9),
(41, 'add_registration_field', 'setting/addfield', 9),
(42, 'delete_registration_field', 'setting/deletefield', 9),
(43, 'edit_registration_field', 'setting/editfield', 9),
(44, 'edit_site_settings', 'setting/index', 9),
(45, 'view_roles', 'setting/role', 9),
(46, 'add_role', 'setting/addrole', 9),
(47, 'edit_role', 'setting/editrole', 9),
(48, 'delete_role', 'setting/deleterole', 9),
(49, 'view_payment_methods', 'payment/index', 9),
(50, 'edit_payment_methods', 'payment/edit', 9),
(51, 'view_admins', 'setting/admins', 9),
(52, 'add_admin', 'setting/addadmin', 9),
(53, 'edit_admin', 'setting/editadmin', 9),
(55, 'view_transactions', 'student/transactions', 2),
(56, 'view_tests', 'test/index', 10),
(57, 'add_test', 'test/add', 10),
(58, 'add_options', 'test/addoptions', 10),
(59, 'add_question', 'test/addquestion', 10),
(60, 'delete_question', 'test/delete', 10),
(61, 'delete_option', 'test/deleteoption', 10),
(62, 'delete_question', 'test/deletequestion', 10),
(63, 'duplicate_question', 'test/duplicate', 10),
(64, 'edit_question', 'test/edit', 10),
(65, 'edit_option', 'test/editoption', 10),
(66, 'edit_question', 'test/editquestion', 10),
(67, 'export_result', 'test/exportresult', 10),
(68, 'manage_questions', 'test/questions', 10),
(69, 'view_results', 'test/results', 10),
(70, 'view_result', 'test/testresult', 10),
(71, 'view_payments', 'student/payments', 2),
(72, 'view_discussions', 'discuss/index', 11),
(73, 'view_discussion', 'discuss/viewdiscussion', 11),
(74, 'reply_discussion', 'discuss/addreply', 11),
(75, 'delete_discussion', 'discuss/delete', 11),
(76, 'view_instructors', 'student/instructors', 2),
(77, 'set_instructors', 'student/manageinstructors', 2),
(78, 'upgrade_database', 'setting/migrate', 9),
(79, 'view_certificates', 'certificate/index', 12),
(80, 'edit_certificate', 'certificate/edit', 12),
(81, 'add_certificate', 'certificate/add', 12),
(82, 'delete_certificate', 'certificate/delete', 12),
(83, 'view_downloads', 'download/index', 13),
(84, 'edit_download', 'download/edit', 13),
(85, 'add_download', 'download/add', 13),
(86, 'delete_download', 'download/delete', 13),
(87, 'global_resource_access', 'misc/global_access', 14),
(88, 'add_course', 'session/addcourse', 2),
(89, 'view_course_categories', 'session/groups', 2),
(90, 'add_course_category', 'session/addgroup', 2),
(91, 'edit_course_category', 'session/editgroup', 2),
(92, 'delete_course_category', 'session/deletegroup', 2),
(93, 'view_class_groups', 'lesson/groups', 4),
(94, 'add_class_group', 'lesson/addgroup', 4),
(95, 'edit_class_group', 'lesson/editgroup', 4),
(96, 'delete_class_group', 'lesson/deletegroup', 4),
(97, 'manage_class_downloads', 'lesson/files', 4),
(98, 'view_lectures', 'lecture/index', 4),
(99, 'add_lecture', 'lecture/add', 4),
(100, 'edit_lecture', 'lecture/edit', 4),
(101, 'delete_lecture', 'lecture/delete', 4),
(102, 'manage_lecture_downloads', 'lecture/files', 4),
(103, 'manage_lecture_content', 'lecture/content', 4),
(104, 'add_homework', 'assignment/add', 15),
(105, 'view_homework_list', 'assignment/index', 15),
(106, 'edit_homework', 'assignment/edit', 15),
(107, 'view_homework', 'assignment/view', 15),
(108, 'delete_homework', 'assignment/delete', 15),
(109, 'view_homework_submissions', 'assignment/submissions', 15),
(110, 'view_homework_submission', 'assignment/viewsubmission', 15),
(111, 'export_homework_results', 'assignment/exportresult', 15),
(112, 'view_themes', 'template/index', 9),
(113, 'customize_theme', 'template/customize', 9),
(114, 'install_theme', 'template/install', 9),
(115, 'configure_sms_gateways', 'smsgateway/index', 9),
(116, 'edit_sms_gateway', 'smsgateway/customize', 9),
(117, 'install_gateway', 'smsgateway/install', 9),
(118, 'uninstall_gateway', 'smsgateway/uninstall', 9),
(119, 'view_forum_topics', 'forum/index', 11),
(120, 'add_forum_topic', 'forum/addtopic', 11),
(121, 'view_forum_topic', 'forum/topic', 11),
(122, 'reply_forum_topic', 'forum/reply', 11),
(123, 'delete_forum_topic', 'forum/deletetopic', 11),
(124, 'view_test_grades', 'setting/testgrades', 9),
(125, 'add_test_grade', 'setting/addtestgrade', 9),
(126, 'edit_test_grade', 'setting/edittestgrade', 9),
(127, 'delete_test_grade', 'setting/deletetestgrade', 9),
(128, 'view_report_page', 'report/index', 16),
(129, 'view_class_report', 'report/classes', 16),
(130, 'view_students_report', 'report/students', 16),
(131, 'view_tests_report', 'report/tests', 16),
(132, 'view_homework_report', 'report/homework', 16),
(133, 'view_email_notifications', 'messages/emails', 9),
(134, 'edit_email_notification', 'messages/editemail', 9),
(135, 'view_sms_notifications', 'messages/sms', 9),
(136, 'edit_sms_notification', 'messages/editsms', 9),
(143, 'view_coupons', 'payment/coupons', 9),
(144, 'add_coupon', 'payment/addcoupon', 9),
(145, 'edit_coupon', 'payment/editcoupon', 9),
(146, 'delete_coupon', 'payment/deletecoupon', 9),
(147, 'manage_currencies', 'setting/currencies', 9),
(148, 'delete_currencies', 'setting/deletecurrency', 9),
(149, 'view_surveys', 'survey/index', 17),
(150, 'add_survey', 'survey/add', 17),
(151, 'add_options', 'survey/addoptions', 17),
(152, 'add_question', 'survey/addquestion', 17),
(153, 'delete_question', 'survey/delete', 17),
(154, 'delete_option', 'survey/deleteoption', 17),
(155, 'delete_question', 'survey/deletequestion', 17),
(156, 'duplicate_question', 'survey/duplicate', 17),
(157, 'edit_question', 'survey/edit', 17),
(158, 'edit_option', 'survey/editoption', 17),
(159, 'edit_question', 'survey/editquestion', 17),
(160, 'export_result', 'survey/exportresult', 17),
(161, 'manage_questions', 'survey/questions', 17),
(162, 'view_results', 'survey/results', 17),
(163, 'view_result', 'survey/surveyresult', 17);

-- --------------------------------------------------------

--
-- Table structure for table `permission_group`
--

CREATE TABLE `permission_group` (
  `permission_group_id` int(10) UNSIGNED NOT NULL,
  `group` varchar(250) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permission_group`
--

INSERT INTO `permission_group` (`permission_group_id`, `group`, `sort_order`) VALUES
(1, 'Student', 1),
(2, 'Session', 2),
(3, 'Attendance', 3),
(4, 'Classes', 4),
(5, 'Revision Notes', 5),
(6, 'Blog', 6),
(7, 'Files', 7),
(8, 'Articles', 8),
(9, 'Settings', 9),
(10, 'Tests', 10),
(11, 'Discussions', 11),
(12, 'Certificates', 12),
(13, 'Downloads', 13),
(14, 'Miscellaneous', 14),
(15, 'Homework', 15),
(16, 'Reports', 16),
(17, 'Survey', 17);

-- --------------------------------------------------------

--
-- Table structure for table `phinxlog`
--

CREATE TABLE `phinxlog` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `phinxlog`
--

INSERT INTO `phinxlog` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`) VALUES
(20170131101623, 'AddRolesMigration', '2017-01-31 10:49:13', '2017-01-31 10:49:13', 0),
(20170131122625, 'DropMaritalStatusTable', '2017-01-31 12:28:05', '2017-01-31 12:28:06', 0),
(20170131134802, 'AddPaymentMethodToTransactionTable', '2017-01-31 14:05:01', '2017-01-31 14:05:03', 0),
(20170131144715, 'AddTransactionPermission', '2017-01-31 15:00:48', '2017-01-31 15:00:48', 0),
(20170201092244, 'AddTestTables', '2017-02-01 10:33:07', '2017-02-01 10:33:08', 0),
(20170201103350, 'AddTestQuestionTable', '2017-02-01 10:38:00', '2017-02-01 10:38:01', 0),
(20170201103837, 'AddColumnsToTestTable', '2017-02-01 10:43:05', '2017-02-01 10:43:07', 0),
(20170201104343, 'AddTestOption', '2017-02-01 10:47:24', '2017-02-01 10:47:24', 0),
(20170201105322, 'AddStudentTestTables', '2017-02-01 11:03:12', '2017-02-01 11:03:12', 0),
(20170201110418, 'AddStudentTestOptionTable', '2017-02-01 12:28:45', '2017-02-01 12:28:45', 0),
(20170201135310, 'AddPassmark', '2017-02-01 13:54:23', '2017-02-01 13:54:24', 0),
(20170201161059, 'AddSortOrderToQuestions', '2017-02-01 16:13:08', '2017-02-01 16:13:09', 0),
(20170202125244, 'AddDisqusSetting', '2017-02-02 12:55:23', '2017-02-02 12:55:23', 0),
(20170203125313, 'AddSessionToTest', '2017-02-03 12:56:08', '2017-02-03 12:56:08', 0),
(20170203161450, 'AddTestPermissions', '2017-02-03 16:29:29', '2017-02-03 16:29:29', 0),
(20170203163157, 'AddTestPermissionToRole', '2017-02-03 16:34:49', '2017-02-03 16:34:49', 0),
(20170204110012, 'AddCurrencyColumn', '2017-02-04 11:20:06', '2017-02-04 11:20:08', 0),
(20170204123644, 'AddPayPalFields', '2017-02-04 12:59:13', '2017-02-04 12:59:13', 0),
(20170204130020, 'ChangePaypalName', '2017-02-04 13:01:19', '2017-02-04 13:01:19', 0),
(20170204170150, 'AddPaypalExpress', '2017-02-04 17:43:20', '2017-02-04 17:43:21', 0),
(20170204174517, 'AddCurrencyToPaypalex', '2017-02-04 17:45:55', '2017-02-04 17:45:55', 0),
(20170204181017, 'Add2CheckoutFields', '2017-02-04 18:15:54', '2017-02-04 18:15:54', 0),
(20170204181705, 'Edict2checkoutcode', '2017-02-04 18:18:32', '2017-02-04 18:18:32', 0),
(20170206095505, 'AddStripeMethod', '2017-02-06 10:03:18', '2017-02-06 10:03:18', 0),
(20170206133615, 'DropFullTextKey', '2017-02-06 13:37:01', '2017-02-06 13:37:02', 0),
(20170206161443, 'DeletePaypalTokenField', '2017-02-06 16:17:26', '2017-02-06 16:17:26', 0),
(20170206190557, 'RenamePaypal', '2017-02-06 19:09:34', '2017-02-06 19:09:34', 0),
(20170207134259, 'AddPaymentTable', '2017-02-07 13:51:03', '2017-02-07 13:51:04', 0),
(20170208130110, 'AddPaymentsPermission', '2017-02-08 13:02:49', '2017-02-08 13:02:49', 0),
(20170208133714, 'AddQuestionTables', '2017-02-08 13:45:43', '2017-02-08 13:45:44', 0),
(20170208135418, 'AddRepliedFlagToDiscussion', '2017-02-08 13:57:24', '2017-02-08 13:57:26', 0),
(20170208142711, 'AddLabelSettings', '2017-02-08 14:42:04', '2017-02-08 14:42:04', 0),
(20170208160939, 'AddQuestionInstructions', '2017-02-08 16:13:32', '2017-02-08 16:13:32', 0),
(20170208171211, 'RenameIdColumn', '2017-02-08 17:13:23', '2017-02-08 17:13:26', 0),
(20170209102727, 'AddDiscussPermission', '2017-02-09 10:33:05', '2017-02-09 10:33:05', 0),
(20170209103340, 'AddMailSettings', '2017-02-09 10:53:38', '2017-02-09 10:53:38', 0),
(20170209125353, 'AddBlogWidget', '2017-02-09 13:01:57', '2017-02-09 13:01:57', 0),
(20170210101001, 'DropAgeRange', '2017-02-10 10:30:22', '2017-02-10 10:30:23', 0),
(20170210102349, 'DropSettingsTable', '2017-02-10 10:30:23', '2017-02-10 10:30:24', 0),
(20170210105905, 'DropUselesAccountsFileds', '2017-02-10 11:05:48', '2017-02-10 11:05:51', 0),
(20170213092127, 'AddSignupFormWidget', '2017-02-13 10:54:13', '2017-02-13 10:54:14', 0),
(20170302154312, 'AddTimeFieldsToClass', '2017-03-07 11:43:20', '2017-03-07 11:43:21', 0),
(20170302161947, 'ModifySessionTimes', '2017-03-07 11:43:21', '2017-03-07 11:43:22', 0),
(20170303113345, 'AddLessonAdminTable', '2017-03-07 11:43:22', '2017-03-07 11:43:23', 0),
(20170303113432, 'AddNewIntructorRole', '2017-03-07 11:43:23', '2017-03-07 11:43:23', 0),
(20170303121724, 'AlterSessionLessonAccount', '2017-03-07 11:43:23', '2017-03-07 11:43:24', 0),
(20170303131927, 'AddSessionColumnToSessionLesson', '2017-03-07 11:43:24', '2017-03-07 11:43:27', 0),
(20170303151408, 'ManageInstructors', '2017-03-07 11:43:27', '2017-03-07 11:43:27', 0),
(20170306094141, 'AddSiteUrlField', '2017-03-07 11:43:27', '2017-03-07 11:43:27', 0),
(20170306100646, 'SendClassReminder', '2017-03-07 11:43:27', '2017-03-07 11:43:28', 0),
(20170306105913, 'AddTimeZone', '2017-03-07 11:43:28', '2017-03-07 11:43:28', 0),
(20170307105648, 'ChangeStudentStatus', '2017-03-07 11:43:28', '2017-03-07 11:43:29', 0),
(20170307120525, 'AddReminderHourSetting', '2017-03-07 12:50:32', '2017-03-07 12:50:32', 0),
(20170310144241, 'AddUpgradeDbPermission', '2017-03-10 15:58:11', '2017-03-10 15:58:12', 0),
(20170403150004, 'AddPasswordResetTable', '2017-04-07 13:24:08', '2017-04-07 13:24:09', 0),
(20170403150659, 'AddPasswordResetDateate', '2017-04-07 13:24:09', '2017-04-07 13:24:10', 0),
(20170410132851, 'AddCertifcateTable', '2017-06-22 11:40:43', '2017-06-22 11:40:44', 0),
(20170410141458, 'AddCertificateFieldTables', '2017-06-22 11:40:44', '2017-06-22 11:40:45', 0),
(20170410150942, 'MoidfyCertificateImage', '2017-06-22 11:40:45', '2017-06-22 11:40:46', 0),
(20170608124814, 'AddPermissionGroup', '2017-06-22 11:40:46', '2017-06-22 11:40:47', 0),
(20170608125530, 'AddCertificateOrientation', '2017-06-22 11:40:47', '2017-06-22 11:40:47', 0),
(20170608133150, 'AddDescriptionToCertificate', '2017-06-22 11:40:47', '2017-06-22 11:40:48', 0),
(20170608140543, 'AddSessionToCertificate', '2017-06-22 11:40:48', '2017-06-22 11:40:49', 0),
(20170609103215, 'AddHtmlField', '2017-06-22 11:40:49', '2017-06-22 11:40:51', 0),
(20170609104739, 'AddCertificateScope', '2017-06-22 11:40:51', '2017-06-22 11:40:52', 0),
(20170613121802, 'AddCertificateTestTable', '2017-06-22 11:40:52', '2017-06-22 11:40:52', 0),
(20170616150601, 'AddCertifcateLabel', '2017-06-22 11:40:53', '2017-06-22 11:40:53', 0),
(20170616151810, 'AddCertificateMenuOption', '2017-06-22 11:40:53', '2017-06-22 11:40:53', 0),
(20170727111835, 'AddDownloadTables', '2017-08-24 21:08:46', '2017-08-24 21:08:47', 0),
(20170727122754, 'ModifyDownloadDescription', '2017-08-24 21:08:48', '2017-08-24 21:08:48', 0),
(20170818144804, 'AddDownloadPermissions', '2017-08-24 21:08:48', '2017-08-24 21:08:48', 0),
(20170822112404, 'AddDownloadMenuOption', '2017-08-24 21:08:48', '2017-08-24 21:08:49', 0),
(20170822112607, 'AddDownloadLabel', '2017-08-24 21:08:49', '2017-08-24 21:08:49', 0),
(20170906191358, 'AddOnlineCoursesTables', '2017-12-04 12:31:06', '2017-12-04 12:31:18', 0),
(20170907131038, 'AddLessonTypeField', '2017-12-04 12:31:18', '2017-12-04 12:31:20', 0),
(20170920101541, 'AddLectureNoteTable', '2017-12-04 12:31:20', '2017-12-04 12:31:20', 0),
(20170920112003, 'AddSortOrderToGroups', '2017-12-04 12:31:21', '2017-12-04 12:31:22', 0),
(20170926131523, 'ChangeSessionFields', '2017-12-04 12:31:22', '2017-12-04 12:31:23', 0),
(20170926134418, 'ChangeLessonDate', '2017-12-04 12:31:24', '2017-12-04 12:31:25', 0),
(20170928115620, 'AddDescriptionToSessionCategory', '2017-12-04 12:31:26', '2017-12-04 12:31:26', 0),
(20171002191223, 'AddSortOrderToSessionLesson', '2017-12-04 12:31:27', '2017-12-04 12:31:28', 0),
(20171003081248, 'AddDiscussionOptionToCourse', '2017-12-04 12:31:28', '2017-12-04 12:31:30', 0),
(20171003092024, 'AddAdditonalCourseFields', '2017-12-04 12:31:30', '2017-12-04 12:31:32', 0),
(20171003114433, 'AddFullTextIndexes', '2017-12-04 12:31:32', '2017-12-04 12:31:45', 0),
(20171004151351, 'AddNewTables', '2017-12-04 12:31:45', '2017-12-04 12:32:18', 0),
(20171005091102, 'AddRelatedCourseTable', '2017-12-04 12:32:18', '2017-12-04 12:32:19', 0),
(20171005092545, 'AddSessionInstructor', '2017-12-04 12:32:19', '2017-12-04 12:32:21', 0),
(20171005093240, 'AddBookmarkTable', '2017-12-04 12:32:21', '2017-12-04 12:32:24', 0),
(20171005095856, 'AddHomeworkTable', '2017-12-04 12:32:24', '2017-12-04 12:32:25', 0),
(20171005100336, 'AddHomeworkSubmissionTable', '2017-12-04 12:32:25', '2017-12-04 12:32:26', 0),
(20171005155940, 'AddAdminAbout', '2017-12-04 12:32:26', '2017-12-04 12:32:28', 0),
(20171011134529, 'AddAssignmentTitle', '2017-12-04 12:32:28', '2017-12-04 12:32:30', 0),
(20171016125515, 'AddCommentToAssignmentSubmission', '2017-12-04 12:32:30', '2017-12-04 12:32:35', 0),
(20171018110744, 'AddStatusToAssignmentSubmission', '2017-12-04 12:32:35', '2017-12-04 12:32:38', 0),
(20171018121056, 'AddAllowLatSubmission', '2017-12-04 12:32:38', '2017-12-04 12:32:39', 0),
(20171023145315, 'AddAdminPhoto', '2017-12-04 12:32:40', '2017-12-04 12:32:42', 0),
(20171024105449, 'AddDiscussionAccountTable', '2017-12-04 12:32:42', '2017-12-04 12:32:43', 0),
(20171024112318, 'AddAccountStatus', '2017-12-04 12:32:43', '2017-12-04 12:32:44', 0),
(20171024130042, 'AddAdminFlagToDiscussion', '2017-12-04 12:32:44', '2017-12-04 12:32:45', 0),
(20171028115103, 'AddSessionLogTable', '2017-12-04 12:32:46', '2017-12-04 12:32:47', 0),
(20171030135256, 'AddClassIntroduction', '2017-12-04 12:32:47', '2017-12-04 12:32:53', 0),
(20171031160452, 'RemoveLessonIdFromLog', '2017-12-04 12:32:53', '2017-12-04 12:32:57', 0),
(20171115130241, 'AddLectureForceOrder', '2017-12-04 12:32:57', '2017-12-04 12:33:12', 0),
(20171121185231, 'AddCompletedFlagToStudentSession', '2017-12-04 12:33:12', '2017-12-04 12:33:15', 0),
(20171123110427, 'AddLiveChatSetting', '2017-12-04 12:33:15', '2017-12-04 12:33:15', 0),
(20171123112429, 'AddNewPermission', '2017-12-04 12:33:15', '2017-12-04 12:33:16', 0),
(20171123132446, 'AddAccountIdToResources', '2017-12-04 12:33:16', '2017-12-04 12:33:37', 0),
(20171125135729, 'AddNewPermissions', '2017-12-04 12:33:38', '2017-12-04 12:33:39', 0),
(20171125142014, 'AddAssignmentPermissions', '2017-12-04 12:33:39', '2017-12-04 12:33:39', 0),
(20171127105043, 'ChangeWidgetType', '2017-12-04 12:33:39', '2017-12-04 12:33:39', 0),
(20171127112900, 'ChangeSettings', '2017-12-04 12:33:39', '2017-12-04 12:33:39', 0),
(20171127115221, 'ModifyLabels', '2017-12-04 12:33:40', '2017-12-04 12:33:40', 0),
(20171127121309, 'ChangeEnrollLabel', '2017-12-04 12:33:40', '2017-12-04 12:33:40', 0),
(20171127123842, 'AddFeaturedLabel', '2017-12-04 12:33:40', '2017-12-04 12:33:40', 0),
(20171129104405, 'AddBookmarkDate', '2017-12-04 12:33:40', '2017-12-04 12:33:42', 0),
(20171129112956, 'AddStudentPendingTable', '2017-12-04 12:33:42', '2017-12-04 12:33:42', 0),
(20180122105745, 'AddSessionTests', '2018-02-11 19:54:49', '2018-02-11 19:54:56', 0),
(20180130161818, 'RenamePrimaryKey', '2018-02-11 19:54:56', '2018-02-11 19:54:56', 0),
(20180221141745, 'AddTemplateTable', '2018-02-26 14:42:29', '2018-02-26 14:42:30', 0),
(20180221142418, 'AddTemplateOptionTable', '2018-02-26 14:42:30', '2018-02-26 14:42:31', 0),
(20180221154434, 'AddDefaultTemplateOptions', '2018-02-26 14:42:31', '2018-02-26 14:42:31', 0),
(20180222203903, 'AddTemplatePermission', '2018-02-26 14:42:32', '2018-02-26 14:42:32', 0),
(20180223105147, 'AddInfoSettings', '2018-02-26 14:42:32', '2018-02-26 14:42:32', 0),
(20180223142423, 'AddTemplateTwo', '2018-02-26 14:42:32', '2018-02-26 14:42:32', 0),
(20180224224441, 'AddCreditsBgColor', '2018-02-26 14:42:32', '2018-02-26 14:42:32', 0),
(20180226141952, 'MigrateFromTemplate', '2018-02-26 14:42:32', '2018-02-26 14:42:33', 0),
(20180227141122, 'AddNewSettings', '2018-03-09 17:03:25', '2018-03-09 17:03:25', 0),
(20180307123612, 'AddNewField', '2018-03-09 17:03:25', '2018-03-09 17:03:28', 0),
(20180313141134, 'AddSocialLoginSettings', '2018-03-14 15:06:48', '2018-03-14 15:06:48', 0),
(20180313161651, 'RemoveInvalideSocialSettings', '2018-03-14 15:06:49', '2018-03-14 15:06:49', 0),
(20180313162755, 'AddPictureFieldToUser', '2018-03-14 15:06:49', '2018-03-14 15:06:51', 0),
(20180315161300, 'CreateSmsGatewayTable', '2018-03-29 16:57:44', '2018-03-29 16:57:44', 0),
(20180315161946, 'AddSmsGatewayFieldsTable', '2018-03-29 16:57:45', '2018-03-29 16:57:45', 0),
(20180315222257, 'AddTestShowResultField', '2018-03-29 16:57:45', '2018-03-29 16:57:47', 0),
(20180317150621, 'AddSessionTable', '2018-03-29 16:57:47', '2018-03-29 16:57:48', 0),
(20180321233633, 'ChangeSessionId', '2018-03-29 16:57:48', '2018-03-29 16:57:49', 0),
(20180322171520, 'AddDefaultGateways', '2018-03-29 16:57:49', '2018-03-29 16:57:50', 0),
(20180322190445, 'AddSmsGatewayPermissions', '2018-03-29 16:57:50', '2018-03-29 16:57:50', 0),
(20180323092022, 'AddClickatellGateway', '2018-03-29 16:57:50', '2018-03-29 16:57:50', 0),
(20180407153321, 'AddForumTables', '2018-04-24 17:54:56', '2018-04-24 17:54:57', 0),
(20180407154355, 'AddForumPostTable', '2018-04-24 17:54:57', '2018-04-24 17:54:57', 0),
(20180407155425, 'AddLectureIdToForumTopic', '2018-04-24 17:54:57', '2018-04-24 17:54:58', 0),
(20180409135349, 'AddDiscussionOption', '2018-04-24 17:54:58', '2018-04-24 17:55:02', 0),
(20180424155401, 'AddAudioField', '2018-04-24 17:55:02', '2018-04-24 17:55:03', 0),
(20180425165038, 'AddPayGateway', '2018-04-26 15:03:24', '2018-04-26 15:03:24', 0),
(20180425213423, 'AddSouthAfricanCode', '2018-04-26 15:03:24', '2018-04-26 15:03:24', 0),
(20180426131403, 'AddGatewayLabel', '2018-04-26 15:03:25', '2018-04-26 15:03:26', 0),
(20180501123525, 'AddPayFast', '2018-05-01 16:29:21', '2018-05-01 16:29:21', 0),
(20180502154248, 'AddPayuMoneyGateway', '2018-05-02 18:21:53', '2018-05-02 18:21:53', 0),
(20180505131812, 'AddPostSubscribersTable', '2018-05-07 16:33:55', '2018-05-07 16:33:57', 0),
(20180521134753, 'AddForumPermissions', '2018-05-23 14:55:28', '2018-05-23 14:55:28', 0),
(20180525104846, 'AddEnrollmentDate', '2018-05-28 11:32:43', '2018-05-28 11:32:44', 0),
(20180525153825, 'AddTestGradesTable', '2018-05-28 11:32:44', '2018-05-28 11:32:44', 0),
(20180525163604, 'AddTestGradePermission', '2018-05-28 11:32:44', '2018-05-28 11:32:45', 0),
(20180526184500, 'AddReportPermissionGroup', '2018-05-28 11:32:45', '2018-05-28 11:32:45', 0),
(20180526184700, 'AddReportPermissions', '2018-05-28 11:32:45', '2018-05-28 11:32:45', 0),
(20180531104150, 'AddGhanianPaymentGateway', '2018-05-31 13:30:55', '2018-05-31 13:30:55', 0),
(20180609145147, 'AddCertificateAssignmentTable', '2018-07-12 10:14:30', '2018-07-12 10:14:30', 0),
(20180611145015, 'ChangeAssignmentDate', '2018-07-12 10:14:30', '2018-07-12 10:14:30', 0),
(20180611145440, 'ChangeAssignmentDate2', '2018-07-12 10:14:30', '2018-07-12 10:14:31', 0),
(20180622095946, 'AddTemplateTables', '2018-07-12 10:14:31', '2018-07-12 10:14:32', 0),
(20180622101541, 'AddSubjectToTemplate', '2018-07-12 10:14:32', '2018-07-12 10:14:34', 0),
(20180622103131, 'AddMessageTemplates', '2018-07-12 10:14:34', '2018-07-12 10:14:34', 0),
(20180622134322, 'AddSmsTemplates', '2018-07-12 10:14:34', '2018-07-12 10:14:34', 0),
(20180623182812, 'AddNotificationPermissions', '2018-07-12 10:14:34', '2018-07-12 10:14:35', 0),
(20180625094546, 'AddRaveGateway', '2018-07-12 10:14:35', '2018-07-12 10:14:35', 0),
(20180625100512, 'AddRaveFields', '2018-07-12 10:14:35', '2018-07-12 10:14:35', 0),
(20180710124829, 'AddSessionCoursesLabel', '2018-07-12 10:14:36', '2018-07-12 10:14:36', 0),
(20180710130123, 'AddSessionCourseLabel', '2018-07-12 10:14:36', '2018-07-12 10:14:36', 0),
(20180712105022, 'AddTemplateThree', '2018-07-16 16:29:11', '2018-07-16 16:29:12', 0),
(20180712122527, 'FixTthreeSortOrder', '2018-07-16 16:29:12', '2018-07-16 16:29:12', 0),
(20180712123301, 'FixHomePageParalax', '2018-07-16 16:29:12', '2018-07-16 16:29:12', 0),
(20180712123841, 'FixReviewSortOrder', '2018-07-16 16:29:12', '2018-07-16 16:29:12', 0),
(20180716121908, 'FixParalaxKey', '2018-07-16 16:29:12', '2018-07-16 16:29:12', 0),
(20180716130404, 'AddNewsletterShowOption', '2018-07-16 16:29:13', '2018-07-16 16:29:13', 0),
(20180803154734, 'AddStudentCertificateTable', '2018-08-16 14:12:36', '2018-08-16 14:12:37', 0),
(20180808132040, 'AddUniqueIndexToStudentCertificate', '2018-08-16 14:12:38', '2018-08-16 14:12:38', 0),
(20180808140239, 'AddStudentFullTextIndexes', '2018-08-16 14:12:38', '2018-08-16 14:12:42', 0),
(20180822103953, 'AddLastSeenToStudent', '2018-09-07 14:53:14', '2018-09-07 14:53:18', 0),
(20180915212957, 'AddVideoTable', '2018-09-20 13:54:09', '2018-09-20 13:54:11', 0),
(20180915220733, 'AddVideoIndexes', '2018-09-20 13:54:11', '2018-09-20 13:54:21', 0),
(20181006145052, 'AddCurrencyTable', '2018-10-15 14:01:04', '2018-10-15 14:01:05', 0),
(20181006145748, 'AddInvoiceTable', '2018-10-15 14:01:05', '2018-10-15 14:01:06', 0),
(20181006154335, 'AddInvoiceTransactionTable', '2018-10-15 14:01:06', '2018-10-15 14:01:06', 0),
(20181006154930, 'AddColumnToPaymentMethod', '2018-10-15 14:01:06', '2018-10-15 14:01:07', 0),
(20181006161623, 'AddDefaultCurrency', '2018-10-15 14:01:08', '2018-10-15 14:01:08', 0),
(20181008120328, 'ModifyPaymentMethod', '2018-10-15 14:01:08', '2018-10-15 14:01:11', 0),
(20181008121112, 'AddPaymentMethodCurrency', '2018-10-15 14:01:11', '2018-10-15 14:01:12', 0),
(20181008143940, 'UpdateCurrency', '2018-10-15 14:01:12', '2018-10-15 14:01:12', 0),
(20181009120452, 'SetGlobalPaymentMethods', '2018-10-15 14:01:12', '2018-10-15 14:01:12', 0),
(20181009132149, 'AddCouponTable', '2018-10-15 14:01:12', '2018-10-15 14:01:13', 0),
(20181009140251, 'AddIpTable', '2018-10-15 14:01:13', '2018-10-15 14:01:14', 0),
(20181011155106, 'AddPaymentMethodToInvoice', '2018-10-15 14:01:14', '2018-10-15 14:01:17', 0),
(20181011163124, 'SetTransactionTableId', '2018-10-15 14:01:17', '2018-10-15 14:01:17', 0),
(20181015123245, 'AddVideoLibraryPermissions', '2018-10-15 14:01:17', '2018-10-15 14:01:18', 0),
(20181015124123, 'AddCurrencyPermissions', '2018-10-15 14:01:18', '2018-10-15 14:01:18', 0),
(20181027080758, 'ModifyInvoiceTransaction', '2018-10-27 10:06:53', '2018-10-27 10:06:54', 0),
(20181101111126, 'AddMissingSymbols', '2018-11-01 12:51:33', '2018-11-01 12:56:51', 0),
(20181113171156, 'AddApiKey', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20181120142126, 'AddVisibilityToWidget', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20181207124716, 'AddArticleVisibility', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20181212115323, 'AddFullTextSearchNewsflash', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20190109121833, 'AddExpiresToToken', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20190119103911, 'AddStudentVideoTable', '2019-02-07 11:58:07', '2019-02-07 11:58:07', 0),
(20190225102113, 'AddBannerSettings', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190320133554, 'AddCouponUpgrades', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190320135713, 'AddCouponSessionTable', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190320135837, 'AddCouponCategoryTable', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190320141702, 'AddCouponInvoiceTable', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190320150110, 'AddDefaultCouponType', '2019-03-21 14:14:11', '2019-03-21 14:14:11', 0),
(20190327150012, 'UpdateCedisCurrency', '2019-03-29 15:28:42', '2019-03-29 15:28:42', 0),
(20190329142455, 'AddCaptchaSettings', '2019-03-29 15:28:42', '2019-03-29 15:28:42', 0),
(20190329144039, 'ChangeCaptchaOption', '2019-03-29 15:28:42', '2019-03-29 15:28:42', 0),
(20190527125048, 'AddMaxDownlodToCertificate', '2019-07-03 13:21:09', '2019-07-03 13:21:09', 0),
(20190527133651, 'AddStudentCertificateDownloadsTable', '2019-07-03 13:21:09', '2019-07-03 13:21:09', 0),
(20190626131826, 'AddSurveyTable', '2019-07-03 13:21:09', '2019-07-03 13:21:09', 0),
(20190626132354, 'AddSurveyQuestionTable', '2019-07-03 13:21:09', '2019-07-03 13:21:09', 0),
(20190626134311, 'AddSurveyOptionTable', '2019-07-03 13:21:09', '2019-07-03 13:21:09', 0),
(20190626134319, 'AddSurveyResponseTable', '2019-07-03 13:21:09', '2019-07-03 13:21:10', 0),
(20190626134332, 'AddSurveyResponseOptionTable', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190627110136, 'AddAccountToSurvey', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190702104851, 'ChangedGooglePlus', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190702121742, 'ModifyTemplateThree', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190702123839, 'FixtTemplateThree', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190702143653, 'AddTopBarToTemplatThree', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190703112101, 'ChangeParalaxGroup', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190703113502, 'RemoveHomepageOptions', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190703113650, 'RenameThemes', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20190703115237, 'RemoveVideoPermissions', '2019-07-03 13:21:10', '2019-07-03 13:21:10', 0),
(20191004141440, 'AddLanguageConfig', '2019-10-04 23:48:28', '2019-10-04 23:48:28', 0),
(20191018115214, 'AddStudentIdToSurvey', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20191018122518, 'AddStudentFlagToSurvey', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20191018124455, 'AddSessionSurveyTable', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20191018125145, 'AddSurveryPermissions', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20191021073333, 'FixOmaniCurrency', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20191022114518, 'FixSessionSurveyId', '2019-10-25 16:29:26', '2019-10-25 16:29:26', 0),
(20200305131713, 'TrimPermissions', '2020-04-08 01:52:43', '2020-04-08 01:52:43', 0),
(20200408004345, 'ChangeToUtf8', '2020-04-08 01:52:43', '2020-04-08 01:52:46', 0),
(20200408091615, 'FixLessonCollation', '2020-04-08 10:17:50', '2020-04-08 10:17:50', 0),
(20200408094352, 'FixCurrencyErrors', '2020-04-08 10:48:17', '2020-04-08 10:48:45', 0);

-- --------------------------------------------------------

--
-- Table structure for table `registration_field`
--

CREATE TABLE `registration_field` (
  `registration_field_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  `options` mediumtext,
  `required` tinyint(1) DEFAULT '0',
  `placeholder` mediumtext,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `related_course`
--

CREATE TABLE `related_course` (
  `related_course_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `related_session_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role`) VALUES
(1, 'Super Administrator'),
(2, 'Administrator'),
(3, 'Instructor');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `role_permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`role_permission_id`, `role_id`, `permission_id`) VALUES
(341, 1, 1),
(342, 1, 2),
(343, 1, 3),
(344, 1, 4),
(345, 1, 5),
(346, 1, 6),
(347, 1, 7),
(348, 1, 8),
(349, 1, 9),
(350, 1, 10),
(351, 1, 11),
(352, 1, 12),
(353, 1, 13),
(354, 1, 14),
(355, 1, 55),
(356, 1, 71),
(357, 1, 15),
(358, 1, 16),
(359, 1, 17),
(360, 1, 18),
(361, 1, 19),
(362, 1, 20),
(363, 1, 21),
(364, 1, 22),
(365, 1, 23),
(366, 1, 24),
(367, 1, 25),
(368, 1, 26),
(369, 1, 27),
(370, 1, 28),
(371, 1, 29),
(372, 1, 30),
(373, 1, 31),
(374, 1, 32),
(375, 1, 33),
(376, 1, 34),
(377, 1, 35),
(378, 1, 36),
(379, 1, 37),
(380, 1, 38),
(381, 1, 39),
(382, 1, 40),
(383, 1, 41),
(384, 1, 42),
(385, 1, 43),
(386, 1, 44),
(387, 1, 45),
(388, 1, 46),
(389, 1, 47),
(390, 1, 48),
(391, 1, 49),
(392, 1, 50),
(393, 1, 51),
(394, 1, 52),
(395, 1, 53),
(396, 1, 56),
(397, 1, 57),
(398, 1, 58),
(399, 1, 59),
(400, 1, 60),
(401, 1, 61),
(402, 1, 62),
(403, 1, 63),
(404, 1, 64),
(405, 1, 65),
(406, 1, 66),
(407, 1, 67),
(408, 1, 68),
(409, 1, 69),
(410, 1, 70),
(411, 1, 72),
(413, 1, 73),
(415, 1, 74),
(417, 1, 75),
(431, 1, 77),
(433, 1, 76),
(435, 1, 78),
(438, 1, 79),
(441, 1, 80),
(444, 1, 81),
(447, 1, 82),
(450, 1, 83),
(453, 1, 84),
(456, 1, 85),
(459, 1, 86),
(462, 1, 87),
(463, 1, 8),
(466, 1, 9),
(469, 1, 10),
(472, 1, 11),
(475, 1, 12),
(478, 1, 13),
(481, 1, 14),
(484, 1, 20),
(487, 1, 21),
(490, 1, 22),
(493, 1, 23),
(496, 1, 55),
(499, 1, 71),
(502, 1, 76),
(505, 1, 77),
(508, 1, 88),
(511, 1, 89),
(514, 1, 90),
(517, 1, 91),
(520, 1, 92),
(523, 1, 93),
(526, 1, 94),
(529, 1, 95),
(532, 1, 96),
(535, 1, 97),
(538, 1, 98),
(541, 1, 99),
(544, 1, 100),
(547, 1, 101),
(550, 1, 102),
(553, 1, 103),
(556, 1, 104),
(557, 1, 105),
(558, 1, 106),
(559, 1, 107),
(560, 1, 108),
(561, 1, 109),
(562, 1, 110),
(563, 1, 111),
(564, 2, 1),
(565, 2, 2),
(566, 2, 3),
(567, 2, 4),
(568, 2, 5),
(569, 2, 6),
(570, 2, 7),
(571, 2, 8),
(572, 2, 9),
(573, 2, 10),
(574, 2, 11),
(575, 2, 12),
(576, 2, 13),
(577, 2, 14),
(578, 2, 55),
(579, 2, 71),
(580, 2, 76),
(581, 2, 77),
(582, 2, 88),
(583, 2, 89),
(584, 2, 90),
(585, 2, 91),
(586, 2, 92),
(587, 2, 15),
(588, 2, 16),
(589, 2, 17),
(590, 2, 18),
(591, 2, 19),
(592, 2, 20),
(593, 2, 21),
(594, 2, 22),
(595, 2, 23),
(596, 2, 93),
(597, 2, 94),
(598, 2, 95),
(599, 2, 96),
(600, 2, 97),
(601, 2, 98),
(602, 2, 99),
(603, 2, 100),
(604, 2, 101),
(605, 2, 102),
(606, 2, 103),
(607, 2, 24),
(608, 2, 25),
(609, 2, 26),
(610, 2, 27),
(611, 2, 28),
(612, 2, 29),
(613, 2, 30),
(614, 2, 31),
(615, 2, 32),
(616, 2, 33),
(617, 2, 34),
(618, 2, 35),
(619, 2, 36),
(620, 2, 78),
(621, 2, 56),
(622, 2, 57),
(623, 2, 58),
(624, 2, 59),
(625, 2, 60),
(626, 2, 61),
(627, 2, 62),
(628, 2, 63),
(629, 2, 64),
(630, 2, 65),
(631, 2, 66),
(632, 2, 67),
(633, 2, 68),
(634, 2, 69),
(635, 2, 70),
(636, 2, 72),
(637, 2, 73),
(638, 2, 74),
(639, 2, 75),
(640, 2, 79),
(641, 2, 80),
(642, 2, 81),
(643, 2, 82),
(644, 2, 83),
(645, 2, 84),
(646, 2, 85),
(647, 2, 86),
(648, 2, 87),
(649, 2, 104),
(650, 2, 105),
(651, 2, 106),
(652, 2, 107),
(653, 2, 108),
(654, 2, 109),
(655, 2, 110),
(656, 2, 111),
(657, 3, 1),
(658, 3, 3),
(659, 3, 8),
(660, 3, 9),
(661, 3, 10),
(662, 3, 11),
(663, 3, 12),
(664, 3, 13),
(665, 3, 14),
(666, 3, 55),
(667, 3, 71),
(668, 3, 76),
(669, 3, 77),
(670, 3, 88),
(671, 3, 89),
(672, 3, 90),
(673, 3, 91),
(674, 3, 92),
(675, 3, 15),
(676, 3, 16),
(677, 3, 17),
(678, 3, 20),
(679, 3, 21),
(680, 3, 22),
(681, 3, 23),
(682, 3, 93),
(683, 3, 94),
(684, 3, 95),
(685, 3, 96),
(686, 3, 97),
(687, 3, 98),
(688, 3, 99),
(689, 3, 100),
(690, 3, 101),
(691, 3, 102),
(692, 3, 103),
(693, 3, 24),
(694, 3, 25),
(695, 3, 26),
(696, 3, 27),
(697, 3, 32),
(698, 3, 56),
(699, 3, 57),
(700, 3, 58),
(701, 3, 59),
(702, 3, 60),
(703, 3, 61),
(704, 3, 62),
(705, 3, 63),
(706, 3, 64),
(707, 3, 65),
(708, 3, 66),
(709, 3, 67),
(710, 3, 68),
(711, 3, 69),
(712, 3, 70),
(713, 3, 72),
(714, 3, 73),
(715, 3, 74),
(716, 3, 75),
(717, 3, 79),
(718, 3, 80),
(719, 3, 81),
(720, 3, 82),
(721, 3, 83),
(722, 3, 84),
(723, 3, 85),
(724, 3, 86),
(725, 3, 104),
(726, 3, 105),
(727, 3, 106),
(728, 3, 107),
(729, 3, 108),
(730, 3, 109),
(731, 3, 110),
(732, 3, 111),
(733, 1, 112),
(734, 1, 113),
(735, 1, 114),
(736, 1, 115),
(737, 1, 116),
(738, 1, 117),
(739, 1, 118),
(740, 1, 119),
(741, 2, 119),
(742, 3, 119),
(743, 1, 120),
(744, 2, 120),
(745, 3, 120),
(746, 1, 121),
(747, 2, 121),
(748, 3, 121),
(749, 1, 122),
(750, 2, 122),
(751, 3, 122),
(752, 1, 123),
(753, 2, 123),
(754, 3, 123),
(755, 1, 124),
(756, 1, 125),
(757, 1, 126),
(758, 1, 127),
(759, 1, 128),
(760, 2, 128),
(761, 1, 129),
(762, 2, 129),
(763, 1, 130),
(764, 2, 130),
(765, 1, 131),
(766, 2, 131),
(767, 1, 132),
(768, 2, 132),
(769, 1, 133),
(770, 1, 134),
(771, 1, 135),
(772, 1, 136),
(791, 1, 143),
(792, 1, 144),
(793, 1, 145),
(794, 1, 146),
(795, 1, 147),
(796, 1, 148),
(797, 1, 149),
(798, 2, 149),
(799, 1, 150),
(800, 2, 150),
(801, 1, 151),
(802, 2, 151),
(803, 1, 152),
(804, 2, 152),
(805, 1, 153),
(806, 2, 153),
(807, 1, 154),
(808, 2, 154),
(809, 1, 155),
(810, 2, 155),
(811, 1, 156),
(812, 2, 156),
(813, 1, 157),
(814, 2, 157),
(815, 1, 158),
(816, 2, 158),
(817, 1, 159),
(818, 2, 159),
(819, 1, 160),
(820, 2, 160),
(821, 1, 161),
(822, 2, 161),
(823, 1, 162),
(824, 2, 162),
(825, 1, 163),
(826, 2, 163);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `session_name` varchar(250) NOT NULL,
  `session_date` int(10) UNSIGNED DEFAULT NULL,
  `session_status` tinyint(1) NOT NULL DEFAULT '1',
  `session_end_date` int(10) UNSIGNED DEFAULT NULL,
  `payment_required` tinyint(1) NOT NULL DEFAULT '0',
  `amount` float DEFAULT NULL,
  `enrollment_closes` int(11) DEFAULT NULL,
  `description` mediumtext,
  `venue` mediumtext,
  `session_type` char(255) NOT NULL DEFAULT 's',
  `picture` varchar(250) DEFAULT NULL,
  `enable_discussion` tinyint(1) DEFAULT '0',
  `enable_chat` tinyint(1) DEFAULT '0',
  `enforce_order` tinyint(1) DEFAULT '0',
  `effort` varchar(255) DEFAULT NULL,
  `length` varchar(255) DEFAULT NULL,
  `short_description` mediumtext,
  `introduction` mediumtext,
  `account_id` int(11) DEFAULT NULL,
  `enable_forum` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `session_category`
--

CREATE TABLE `session_category` (
  `session_category_id` int(11) NOT NULL,
  `category_name` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(11) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_instructor`
--

CREATE TABLE `session_instructor` (
  `session_instructor_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_lesson`
--

CREATE TABLE `session_lesson` (
  `session_lesson_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `lesson_date` int(10) UNSIGNED DEFAULT NULL,
  `lesson_venue` mediumtext,
  `lesson_start` varchar(250) DEFAULT NULL,
  `lesson_end` varchar(250) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_lesson_account`
--

CREATE TABLE `session_lesson_account` (
  `session_lesson_account_id` int(11) NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_storage`
--

CREATE TABLE `session_storage` (
  `id` varchar(250) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session_storage`
--

INSERT INTO `session_storage` (`id`, `name`, `modified`, `lifetime`, `data`) VALUES
('028610b4b47dedf704b25216d23040af', 'traineasy', 1562156605, 2592000, '__ZF|a:5:{s:20:\"_REQUEST_ACCESS_TIME\";d:1562156605.907833099365234375;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:32:\"b90ea7aabfa3384bb45f874a3b1cf631\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1562156895;}s:50:\"Zend_Form_Captcha_09cc2788e917573851cf585739f8f701\";a:2:{s:11:\"EXPIRE_HOPS\";a:2:{s:4:\"hops\";i:1;s:2:\"ts\";d:1562156594.9053609371185302734375;}s:6:\"EXPIRE\";i:1562156895;}s:50:\"Zend_Form_Captcha_fbdc10302e81f4e75d1c38a837210f77\";a:2:{s:11:\"EXPIRE_HOPS\";a:2:{s:4:\"hops\";i:1;s:2:\"ts\";d:1562156595.54129695892333984375;}s:6:\"EXPIRE\";i:1562156895;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":359:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:76:\"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:67.0) Gecko/20100101 Firefox/67.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}currency|C:23:\"Zend\\Stdlib\\ArrayObject\":228:{a:4:{s:7:\"storage\";a:1:{s:11:\"currency_id\";i:1;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":297:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:74:\"http://localhost/Projects/TrainEasySelfHosted/app/public/admin/student/add\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}cart|C:23:\"Zend\\Stdlib\\ArrayObject\":634:{a:4:{s:7:\"storage\";a:1:{s:4:\"cart\";s:409:\"O:24:\"Application\\Library\\Cart\":8:{s:34:\"\0Application\\Library\\Cart\0sessions\";a:0:{}s:36:\"\0Application\\Library\\Cart\0isDiscount\";b:0;s:34:\"\0Application\\Library\\Cart\0couponId\";N;s:41:\"\0Application\\Library\\Cart\0discountApplied\";N;s:41:\"\0Application\\Library\\Cart\0paymentMethodId\";N;s:31:\"\0Application\\Library\\Cart\0total\";N;s:35:\"\0Application\\Library\\Cart\0invoiceId\";N;s:35:\"\0Application\\Library\\Cart\0studentId\";N;}\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":551:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:3:{s:32:\"717253d299cd1c82331216e82b5b6a54\";s:32:\"1dfec2ce8dc1ada494efb20d165b4b12\";s:32:\"a38a30a8747e022c7f8b2df32721a7e0\";s:32:\"5fea0348452b8ee5938f41744cf69ffd\";s:32:\"2277d645d7f8838b4512a1f04a946b2d\";s:32:\"a93ab5386ffb24021ad994d07e17e3e4\";}s:4:\"hash\";s:65:\"a93ab5386ffb24021ad994d07e17e3e4-2277d645d7f8838b4512a1f04a946b2d\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}client|C:23:\"Zend\\Stdlib\\ArrayObject\":230:{a:4:{s:7:\"storage\";a:1:{s:4:\"type\";s:7:\"desktop\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Form_Captcha_09cc2788e917573851cf585739f8f701|C:23:\"Zend\\Stdlib\\ArrayObject\":231:{a:4:{s:7:\"storage\";a:1:{s:4:\"word\";s:8:\"dinou5vu\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Form_Captcha_fbdc10302e81f4e75d1c38a837210f77|C:23:\"Zend\\Stdlib\\ArrayObject\":231:{a:4:{s:7:\"storage\";a:1:{s:4:\"word\";s:8:\"c9xoxyxy\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('032j53tu5thg864sdfpp8c0qth', 'traineasy', 1524589261, 2592000, '__ZF|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1524589260.975916;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:26:\"vsh0ih8277tt1ilkt5dd0rcnna\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1524589561;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":355:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":275:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:52:\"http://localhost/Projects/TrainEasyApp/public/admin/\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":711:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:5:{s:32:\"ff57d918bfa04bbfb0508c3622598269\";s:32:\"4c0c2ee21c3a4b181cb29ecb9c2a4c3a\";s:32:\"2b0f263740dfde502f60dd646d48ad93\";s:32:\"0fd70fe67a5fed1c87bc2af9ce1a18b4\";s:32:\"cf13b8e49a3fb081ccc6ba175c8e3457\";s:32:\"835b14c7cb64f52fafdb42d03b541f5f\";s:32:\"8adf962f87ec1f17978359c5ac9ab99b\";s:32:\"59d1ecc15d36509d0cdda313b7791cf5\";s:32:\"004a929e66deb5155773815664ebd5d0\";s:32:\"8db8119b4f1ca17ae2ab92a6428a6405\";}s:4:\"hash\";s:65:\"8db8119b4f1ca17ae2ab92a6428a6405-004a929e66deb5155773815664ebd5d0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('8ihiponrdmfikrm8ftoa1t7njo', 'traineasy', 1537788036, 2592000, '__ZF|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1537788036.068508;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:26:\"33dosca1u5anl0rod2qhjs05m6\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1537788336;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":355:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":275:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:52:\"http://localhost/Projects/TrainEasyApp/public/admin/\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":631:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:4:{s:32:\"0a1988b772ac69b1424d4cd086ad7161\";s:32:\"ac8a5235aa603ede901b8b6f050cabc3\";s:32:\"0a9714ab8da84e55457b8c27811d3a6a\";s:32:\"4cb050925a2b75b8dba4c225637d2654\";s:32:\"821bdc81483298ed4470b04ae83cf85f\";s:32:\"256bbac74359d822a200be33df617df9\";s:32:\"da2b894e5a95f90588f127d40711719e\";s:32:\"fc3b919753ffb0f3a7b20d8cbef760ea\";}s:4:\"hash\";s:65:\"fc3b919753ffb0f3a7b20d8cbef760ea-da2b894e5a95f90588f127d40711719e\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('8ua7pa4d45qonm0l94mboeh6mk', 'traineasy', 1540717850, 2592000, '__ZF|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1540717850.242806;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:26:\"24hhto5qeij4h9990sk95iiqct\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1540718150;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":355:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}currency|C:23:\"Zend\\Stdlib\\ArrayObject\":228:{a:4:{s:7:\"storage\";a:1:{s:11:\"currency_id\";i:1;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":275:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:52:\"http://localhost/Projects/TrainEasyApp/public/admin/\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}cart|C:23:\"Zend\\Stdlib\\ArrayObject\":589:{a:4:{s:7:\"storage\";a:1:{s:4:\"cart\";s:364:\"O:24:\"Application\\Library\\Cart\":7:{s:34:\"\0Application\\Library\\Cart\0sessions\";a:0:{}s:36:\"\0Application\\Library\\Cart\0isDiscount\";b:0;s:34:\"\0Application\\Library\\Cart\0couponId\";N;s:41:\"\0Application\\Library\\Cart\0discountApplied\";N;s:41:\"\0Application\\Library\\Cart\0paymentMethodId\";N;s:31:\"\0Application\\Library\\Cart\0total\";N;s:35:\"\0Application\\Library\\Cart\0invoiceId\";N;}\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":1112:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:10:{s:32:\"289aa012e8597bbb85a2c172e424a037\";s:32:\"a7f560e33a408973d2d59f37e6bdad1d\";s:32:\"053f7914fa51a745b50b24dd9c0b16bd\";s:32:\"bafd7e4518396c65dcb1c32f25774029\";s:32:\"2af1ccad099b80a2c84bbec3ad4d9955\";s:32:\"10112cd80761b6260bc1b62a36636f65\";s:32:\"ae0354a961a5cd30831400fbbad1053b\";s:32:\"1c17ae3e090c4a612f7ed8c959300f48\";s:32:\"75355064d549c097797195ea765acd70\";s:32:\"f80f184d9cb5dc45aed08ecf52183f17\";s:32:\"d55723225a0d8a14c1a2bf27ee04f201\";s:32:\"54340b961aabeed0e043d4d02225accd\";s:32:\"527b1c6f2772cbc89edbe322604df351\";s:32:\"6e5df84fe3939d1e8c281c0725fa5250\";s:32:\"e6f3d4b7b8e8c0de06bcc1619b8396b0\";s:32:\"7061fb4e8bdd16f5786a4384330e50d3\";s:32:\"be6fbeca811fed0e26380a439c4c0f86\";s:32:\"c254c302729f5a6ddccdd502e8796e70\";s:32:\"f20037d2f018322e241a9205e8af3d6c\";s:32:\"47fcf1dc1ba02bfb0579f10f1b68485e\";}s:4:\"hash\";s:65:\"47fcf1dc1ba02bfb0579f10f1b68485e-f20037d2f018322e241a9205e8af3d6c\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('af17ff5a2ba554ce37684892c3989c45', 'traineasy', 1549542552, 2592000, '__ZF|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1549542552.6209900379180908203125;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:32:\"6b5818dd906759e39d487b32eb87829c\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1549542852;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":359:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:9:\"127.0.0.1\";s:13:\"httpUserAgent\";s:76:\"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:64.0) Gecko/20100101 Firefox/64.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}currency|C:23:\"Zend\\Stdlib\\ArrayObject\":228:{a:4:{s:7:\"storage\";a:1:{s:11:\"currency_id\";i:1;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":275:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:52:\"http://localhost/Projects/TrainEasyApp/public/admin/\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}cart|C:23:\"Zend\\Stdlib\\ArrayObject\":589:{a:4:{s:7:\"storage\";a:1:{s:4:\"cart\";s:364:\"O:24:\"Application\\Library\\Cart\":7:{s:34:\"\0Application\\Library\\Cart\0sessions\";a:0:{}s:36:\"\0Application\\Library\\Cart\0isDiscount\";b:0;s:34:\"\0Application\\Library\\Cart\0couponId\";N;s:41:\"\0Application\\Library\\Cart\0discountApplied\";N;s:41:\"\0Application\\Library\\Cart\0paymentMethodId\";N;s:31:\"\0Application\\Library\\Cart\0total\";N;s:35:\"\0Application\\Library\\Cart\0invoiceId\";N;}\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":791:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:6:{s:32:\"68665fe1dc9af9729075c6cf3bfcc2f5\";s:32:\"07dacd832b2907056b0e295d77c26c06\";s:32:\"ebe680117e484ca7bccfdc92f34f3464\";s:32:\"fbaf28274ad3a663188074355b8f1f43\";s:32:\"3e8bd9d1368377196793df3729e328ec\";s:32:\"62b6ba54ea150e95d46d799d29937e5c\";s:32:\"a03b4aa4f8ce38320ef7b58ecad46e83\";s:32:\"51d7630162af5fcb65d0dc290c4b7595\";s:32:\"a4f6a38352f484e74c5cd9e2e77ded98\";s:32:\"55f62cbc56574f9c075dd2de89028d7f\";s:32:\"6f60b9a9578c1ceff4535b12ee5b80d2\";s:32:\"db7894b7c94ab8511db93236df49ebfd\";}s:4:\"hash\";s:65:\"db7894b7c94ab8511db93236df49ebfd-6f60b9a9578c1ceff4535b12ee5b80d2\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('t112hu7o4re4gm7ik1lpg4cc83', 'traineasy', 1540631631, 2592000, '__ZF|a:3:{s:20:\"_REQUEST_ACCESS_TIME\";d:1540631631.495476;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:26:\"55gdavfjlbchg034stdhg29lff\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1540631931;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":355:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:63.0) Gecko/20100101 Firefox/63.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}currency|C:23:\"Zend\\Stdlib\\ArrayObject\":228:{a:4:{s:7:\"storage\";a:1:{s:11:\"currency_id\";i:1;}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}admin_login|C:23:\"Zend\\Stdlib\\ArrayObject\":289:{a:4:{s:7:\"storage\";a:1:{s:3:\"url\";s:66:\"http://localhost/Projects/TrainEasyApp/public/admin/payment/edit/2\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}cart|C:23:\"Zend\\Stdlib\\ArrayObject\":589:{a:4:{s:7:\"storage\";a:1:{s:4:\"cart\";s:364:\"O:24:\"Application\\Library\\Cart\":7:{s:34:\"\0Application\\Library\\Cart\0sessions\";a:0:{}s:36:\"\0Application\\Library\\Cart\0isDiscount\";b:0;s:34:\"\0Application\\Library\\Cart\0couponId\";N;s:41:\"\0Application\\Library\\Cart\0discountApplied\";N;s:41:\"\0Application\\Library\\Cart\0paymentMethodId\";N;s:31:\"\0Application\\Library\\Cart\0total\";N;s:35:\"\0Application\\Library\\Cart\0invoiceId\";N;}\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":391:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:1:{s:32:\"2788aec0b7766e22080e7f1e696fe30b\";s:32:\"bc834369052d09a8df3dfb2262e7080a\";}s:4:\"hash\";s:65:\"bc834369052d09a8df3dfb2262e7080a-2788aec0b7766e22080e7f1e696fe30b\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}'),
('vg8uhomoskbuc7akp9u0p5puaq', 'traineasy', 1537787990, 2592000, '__ZF|a:4:{s:20:\"_REQUEST_ACCESS_TIME\";d:1537787990.558507;s:6:\"_VALID\";a:1:{s:25:\"Zend\\Session\\Validator\\Id\";s:26:\"rvv433fdbfccrjevc941ccu5et\";}s:33:\"Zend_Validator_Csrf_salt_security\";a:1:{s:6:\"EXPIRE\";i:1537788245;}s:50:\"Zend_Form_Captcha_88e2bdfa4c602424447c349d8a2f7432\";a:2:{s:11:\"EXPIRE_HOPS\";a:2:{s:4:\"hops\";i:1;s:2:\"ts\";d:1537787935.583313;}s:6:\"EXPIRE\";i:1537788245;}}initialized|C:23:\"Zend\\Stdlib\\ArrayObject\":355:{a:4:{s:7:\"storage\";a:3:{s:4:\"init\";i:1;s:10:\"remoteAddr\";s:3:\"::1\";s:13:\"httpUserAgent\";s:78:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:62.0) Gecko/20100101 Firefox/62.0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}FlashMessenger|C:23:\"Zend\\Stdlib\\ArrayObject\":205:{a:4:{s:7:\"storage\";a:0:{}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Validator_Csrf_salt_security|C:23:\"Zend\\Stdlib\\ArrayObject\":391:{a:4:{s:7:\"storage\";a:2:{s:9:\"tokenList\";a:1:{s:32:\"45be08adb19052e4e5c23f4ca0892cc0\";s:32:\"0f2f691b66e52bd8c802e1d2a5bb6d02\";}s:4:\"hash\";s:65:\"0f2f691b66e52bd8c802e1d2a5bb6d02-45be08adb19052e4e5c23f4ca0892cc0\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}Zend_Form_Captcha_88e2bdfa4c602424447c349d8a2f7432|C:23:\"Zend\\Stdlib\\ArrayObject\":231:{a:4:{s:7:\"storage\";a:1:{s:4:\"word\";s:8:\"2uxepydy\";}s:4:\"flag\";i:2;s:13:\"iteratorClass\";s:13:\"ArrayIterator\";s:19:\"protectedProperties\";a:4:{i:0;s:7:\"storage\";i:1;s:4:\"flag\";i:2;s:13:\"iteratorClass\";i:3;s:19:\"protectedProperties\";}}}');

-- --------------------------------------------------------

--
-- Table structure for table `session_survey`
--

CREATE TABLE `session_survey` (
  `session_survey_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `survey_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_test`
--

CREATE TABLE `session_test` (
  `session_test_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(11) NOT NULL,
  `opening_date` int(11) DEFAULT NULL,
  `closing_date` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `session_to_session_category`
--

CREATE TABLE `session_to_session_category` (
  `session_to_session_category_id` int(11) NOT NULL,
  `session_category_id` int(11) NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `setting_id` int(10) UNSIGNED NOT NULL,
  `key` varchar(250) NOT NULL,
  `label` mediumtext NOT NULL,
  `placeholder` mediumtext,
  `value` mediumtext,
  `serialized` tinyint(1) DEFAULT '0',
  `type` varchar(45) NOT NULL,
  `options` mediumtext,
  `class` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`setting_id`, `key`, `label`, `placeholder`, `value`, `serialized`, `type`, `options`, `class`) VALUES
(1, 'general_site_name', 'Site Name', '', '', 0, 'text', NULL, NULL),
(2, 'regis_enable_registration', 'Enable registration?', '', '1', 0, 'radio', '1=Yes,0=No', NULL),
(3, 'general_homepage_title', 'Homepage title', '', '', 0, 'text', NULL, NULL),
(4, 'general_homepage_meta_desc', 'Homepage Meta Description', '', '', 0, 'textarea', NULL, NULL),
(5, 'general_admin_email', 'Admin Email', '', '', 0, 'text', NULL, NULL),
(6, 'color_navbar', 'Navigation Bar', '', '', 0, 'text', NULL, NULL),
(7, 'color_primary_color', 'Site Primary Color', '', '', 0, 'text', NULL, NULL),
(8, 'color_navtext', 'Navigation text color', '', '', 0, 'text', NULL, NULL),
(10, 'color_footer', 'Footer background color', '', '', 0, 'text', NULL, NULL),
(11, 'color_footertext', 'Footer text color', '', '', 0, 'text', NULL, NULL),
(12, 'image_logo', 'Site Logo', '', '', 0, 'hidden', NULL, NULL),
(15, 'regis_registration_instructions', 'Registration Instructions', '', '', 0, 'textarea', NULL, 'rte'),
(16, 'footer_about', 'About Us', '', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>\r\n', 0, 'textarea', NULL, 'rte'),
(17, 'footer_address', 'Address', '', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ', 0, 'textarea', NULL, NULL),
(18, 'footer_email', 'Email', '', 'info@company.net', 0, 'text', NULL, NULL),
(19, 'footer_tel', 'Telephone', '', '+4495869585', 0, 'text', NULL, NULL),
(25, 'image_icon', 'Site Icon', '', '', 0, 'hidden', NULL, NULL),
(26, 'country_id', 'Country/Currency', '', '223', 0, 'select', '', NULL),
(27, 'general_auto_enroll', 'Enable Automatic Enrollment', '', '0', 0, 'radio', '1=Yes,0=No', NULL),
(28, 'general_ssl', 'Use SSL', NULL, '0', 0, 'radio', '1=Yes,0=No', NULL),
(29, 'footer_newsletter_code', 'Newsletter Form Code', '', '', 0, 'textarea', NULL, NULL),
(30, 'footer_credits', 'Site Credits', NULL, '', 0, 'text', NULL, NULL),
(31, 'general_header_scripts', 'Header Scripts (advanced)', 'Content for the \"Head\" section of all site pages', '', 0, 'textarea', NULL, NULL),
(32, 'general_foot_scripts', 'Footer Scripts (advanced)', 'Content to be placed before the closing </body> tag', '', 0, 'textarea', '', NULL),
(34, 'menu_show_courses', 'Show Online Courses', '', '1', 0, 'radio', '1=Yes,0=No', NULL),
(35, 'menu_show_sessions', 'Show Upcoming Sessions', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(36, 'menu_show_blog', 'Show Blog', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(37, 'menu_show_contact', 'Show Contact Us', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(38, 'menu_show_articles', 'Show Articles', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(39, 'footer_show_sicons', 'Show Social Icons', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(40, 'footer_show_newsletter', 'Show Newsletter Form', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(41, 'footer_show_about', 'Show About Us', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(42, 'footer_show_contact', 'Show Contact Us', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(43, 'regis_email_message', 'Successful Registration Email', 'The email that a student recieves after registering successfully', '', 0, 'textarea', NULL, 'rte'),
(44, 'color_page_title', 'Page title background color', '', '', 0, 'text', NULL, NULL),
(45, 'color_page_title_text', 'Page title text color', '', '', 0, 'text', NULL, NULL),
(46, 'regis_enrollment_alert', 'Send alert for enrollments', '', '1', 0, 'radio', '1=Yes,0=No', NULL),
(47, 'regis_signup_alert', 'Send alert for registrations', '', '1', 0, 'radio', '1=Yes,0=No', NULL),
(48, 'general_disqus', 'Disqus Shortname', 'Your Disqus Shortname', '', 0, 'text', NULL, NULL),
(51, 'label_enroll', 'Enroll', NULL, '', 0, 'text', NULL, NULL),
(52, 'label_discussion', 'Discuss', NULL, '', 0, 'text', NULL, NULL),
(53, 'label_classes_attended', 'Classes Attended', NULL, '', 0, 'text', NULL, NULL),
(54, 'label_revision_notes', 'Revision Notes', NULL, '', 0, 'text', NULL, NULL),
(55, 'label_take_test', 'Take A Test', NULL, '', 0, 'text', NULL, NULL),
(56, 'label_classes', 'Classes', NULL, '', 0, 'text', NULL, NULL),
(57, 'label_sessions', 'Upcoming Sessions', NULL, '', 0, 'text', NULL, NULL),
(58, 'label_blog', 'Blog', NULL, '', 0, 'text', NULL, NULL),
(59, 'label_contact_us', 'Contact Us', NULL, '', 0, 'text', NULL, NULL),
(60, 'label_about_us', 'About Us', NULL, '', 0, 'text', NULL, NULL),
(61, 'label_follow_us', 'Follow Us', NULL, '', 0, 'text', NULL, NULL),
(62, 'general_discussion_instructions', 'Discussion Instructions', NULL, '', 0, 'textarea', NULL, 'rte'),
(63, 'mail_protocol', 'Mail Protocol', NULL, 'mail', 0, 'select', 'mail=Mail,smtp=SMTP', NULL),
(64, 'mail_smtp_host', 'SMTP Host', NULL, '', 0, 'text', NULL, NULL),
(65, 'mail_smtp_username', 'SMTP Username', NULL, '', 0, 'text', NULL, NULL),
(66, 'mail_smtp_password', 'SMTP Password', NULL, '', 0, 'text', NULL, NULL),
(67, 'mail_smtp_port', 'SMTP Port', NULL, '', 0, 'text', NULL, NULL),
(68, 'mail_smtp_timeout', 'SMTP Timeout', NULL, '', 0, 'text', NULL, NULL),
(69, 'general_show_fee', 'Show Session Fees', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(70, 'menu_show_discussions', 'Show Discussions', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(71, 'menu_show_tests', 'Show Tests', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(72, 'menu_show_notes', 'Show Revision Notes', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(73, 'menu_show_attended', 'Show Classes Attended', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(74, 'general_site_ip', 'Site IP Address', 'The IP address of your site (for cron security)', '', 0, 'text', NULL, NULL),
(75, 'general_send_reminder', 'Send Class Reminders', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(76, 'general_reminder_days', 'Reminder Day', 'How many days to a class should a reminder be sent', '1', 0, 'text', NULL, 'number'),
(77, 'general_timezone', 'Site Timezone', NULL, 'UTC', 0, 'select', 'Australia/Adelaide,Australia/Broken_Hill,Australia/Darwin,Australia/North,Australia/South,Australia/Yancowinna,America/Porto_Acre,Australia/Adelaide,America/Eirunepe,America/Rio_Branco,Brazil/Acre,Asia/Jayapura,Australia/Broken_Hill,Australia/Darwin,Australia/North,Australia/South,Australia/Yancowinna,America/Porto_Acre,America/Eirunepe,America/Rio_Branco,Brazil/Acre,Australia/Eucla,Australia/Eucla,America/Goose_Bay,America/Pangnirtung,America/Halifax,America/Barbados,America/Blanc-Sablon,America/Glace_Bay,America/Goose_Bay,America/Martinique,America/Moncton,America/Pangnirtung,America/Thule,Atlantic/Bermuda,Canada/Atlantic,Asia/Baghdad,Australia/Melbourne,Antarctica/Macquarie,Australia/ACT,Australia/Brisbane,Australia/Canberra,Australia/Currie,Australia/Hobart,Australia/Lindeman,Australia/NSW,Australia/Queensland,Australia/Sydney,Australia/Tasmania,Australia/Victoria,Australia/Melbourne,Antarctica/Macquarie,Australia/ACT,Australia/Brisbane,Australia/Canberra,Australia/Currie,Australia/Hobart,Australia/LHI,Australia/Lindeman,Australia/Lord_Howe,Australia/NSW,Australia/Queensland,Australia/Sydney,Australia/Tasmania,Australia/Victoria,Asia/Kabul,Asia/Kabul,America/Anchorage,America/Anchorage,America/Adak,America/Atka,America/Anchorage,America/Juneau,America/Nome,America/Sitka,America/Yakutat,America/Anchorage,America/Juneau,America/Nome,America/Sitka,America/Yakutat,Asia/Aqtobe,Asia/Aqtobe,Asia/Aqtobe,Asia/Aqtobe,Asia/Almaty,Asia/Almaty,Asia/Almaty,Asia/Yerevan,Asia/Yerevan,America/Boa_Vista,America/Campo_Grande,America/Cuiaba,America/Manaus,America/Porto_Velho,America/Santarem,Brazil/West,Asia/Yerevan,Asia/Yerevan,America/Asuncion,America/Boa_Vista,America/Campo_Grande,America/Cuiaba,America/Eirunepe,America/Manaus,America/Porto_Acre,America/Porto_Velho,America/Rio_Branco,America/Santarem,Brazil/Acre,Brazil/West,Europe/Amsterdam,Europe/Athens,Asia/Anadyr,Asia/Anadyr,Asia/Anadyr,Asia/Anadyr,Asia/Anadyr,Asia/Anadyr,America/Curacao,America/Aruba,America/Kralendijk,America/Lower_Princes,America/Halifax,America/Blanc-Sablon,America/Glace_Bay,America/Moncton,America/Pangnirtung,America/Puerto_Rico,Canada/Atlantic,Asia/Aqtau,Asia/Aqtau,Asia/Aqtobe,Asia/Aqtau,Asia/Aqtau,Asia/Aqtobe,America/Buenos_Aires,America/Buenos_Aires,America/Argentina/Buenos_Aires,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Catamarca,America/Cordoba,America/Jujuy,America/Mendoza,America/Rosario,Antarctica/Palmer,America/Argentina/Buenos_Aires,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Catamarca,America/Cordoba,America/Jujuy,America/Mendoza,America/Rosario,Antarctica/Palmer,America/Buenos_Aires,America/Buenos_Aires,America/Argentina/Buenos_Aires,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Catamarca,America/Cordoba,America/Jujuy,America/Mendoza,America/Rosario,Antarctica/Palmer,America/Argentina/Buenos_Aires,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Catamarca,America/Cordoba,America/Jujuy,America/Mendoza,America/Rosario,Antarctica/Palmer,Asia/Ashkhabad,Asia/Ashkhabad,Asia/Ashgabat,Asia/Ashgabat,Asia/Ashkhabad,Asia/Ashkhabad,Asia/Ashgabat,Asia/Ashgabat,Asia/Riyadh,America/Anguilla,America/Antigua,America/Aruba,America/Barbados,America/Blanc-Sablon,America/Curacao,America/Dominica,America/Glace_Bay,America/Goose_Bay,America/Grand_Turk,America/Grenada,America/Guadeloupe,America/Halifax,America/Kralendijk,America/Lower_Princes,America/Marigot,America/Martinique,America/Miquelon,America/Moncton,America/Montserrat,America/Pangnirtung,America/Port_of_Spain,America/Puerto_Rico,America/Santo_Domingo,America/St_Barthelemy,America/St_Kitts,America/St_Lucia,America/St_Thomas,America/St_Vincent,America/Thule,America/Tortola,America/Virgin,Atlantic/Bermuda,Canada/Atlantic,Asia/Aden,Asia/Baghdad,Asia/Bahrain,Asia/Kuwait,Asia/Qatar,Australia/Perth,Australia/West,Australia/Perth,Antarctica/Casey,Australia/West,America/Halifax,America/Blanc-Sablon,America/Glace_Bay,America/Moncton,America/Pangnirtung,America/Puerto_Rico,Canada/Atlantic,Atlantic/Azores,Atlantic/Azores,Atlantic/Azores,Atlantic/Azores,Atlantic/Azores,Asia/Baku,Asia/Baku,Asia/Baku,Asia/Baku,Asia/Baku,Asia/Baku,Asia/Baku,Asia/Baku,Europe/London,Asia/Dacca,Asia/Dhaka,Europe/Belfast,Europe/Gibraltar,Europe/Guernsey,Europe/Isle_of_Man,Europe/Jersey,GB,America/Adak,Asia/Dacca,America/Atka,America/Nome,Asia/Dhaka,Africa/Mogadishu,Africa/Addis_Ababa,Africa/Asmara,Africa/Asmera,Africa/Dar_es_Salaam,Africa/Djibouti,Africa/Kampala,Africa/Nairobi,Indian/Antananarivo,Indian/Comoro,Indian/Mayotte,Africa/Nairobi,Africa/Addis_Ababa,Africa/Asmara,Africa/Asmera,Africa/Dar_es_Salaam,Africa/Djibouti,Africa/Kampala,Africa/Mogadishu,Indian/Antananarivo,Indian/Comoro,Indian/Mayotte,America/Barbados,Europe/Tiraspol,America/Bogota,Asia/Baghdad,Asia/Bangkok,Asia/Phnom_Penh,Asia/Vientiane,Asia/Jakarta,Europe/Bucharest,Europe/Chisinau,Asia/Brunei,Asia/Brunei,Asia/Kuching,Asia/Kuching,Asia/Kuching,America/La_Paz,America/La_Paz,America/Sao_Paulo,America/Araguaina,America/Bahia,America/Belem,America/Fortaleza,America/Maceio,America/Recife,Brazil/East,America/Sao_Paulo,America/Araguaina,America/Bahia,America/Belem,America/Fortaleza,America/Maceio,America/Recife,America/Santarem,Brazil/East,Europe/London,Europe/London,America/Adak,America/Atka,America/Nome,Pacific/Midway,Pacific/Pago_Pago,Pacific/Samoa,Europe/Belfast,Europe/Guernsey,Europe/Isle_of_Man,Europe/Jersey,GB,Europe/Belfast,Europe/Dublin,Europe/Gibraltar,Europe/Guernsey,Europe/Isle_of_Man,Europe/Jersey,GB,Pacific/Bougainville,Asia/Thimbu,Asia/Thimphu,Asia/Kolkata,Asia/Calcutta,Asia/Dacca,Asia/Dhaka,Asia/Rangoon,Atlantic/Canary,America/Anchorage,Australia/Adelaide,Africa/Juba,Africa/Khartoum,Antarctica/Casey,America/Anchorage,Africa/Khartoum,Africa/Blantyre,Africa/Bujumbura,Africa/Gaborone,Africa/Harare,Africa/Juba,Africa/Kigali,Africa/Lubumbashi,Africa/Lusaka,Africa/Maputo,Africa/Windhoek,America/Anchorage,Indian/Cocos,America/Rankin_Inlet,America/Resolute,America/Chicago,Asia/Shanghai,America/Havana,America/Atikokan,America/Bahia_Banderas,America/Belize,America/Cambridge_Bay,America/Cancun,America/Chihuahua,America/Coral_Harbour,America/Costa_Rica,America/El_Salvador,America/Fort_Wayne,America/Guatemala,America/Indiana/Indianapolis,America/Indiana/Knox,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Iqaluit,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Knox_IN,America/Louisville,America/Managua,America/Matamoros,America/Menominee,America/Merida,America/Mexico_City,America/Monterrey,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Ojinaga,America/Pangnirtung,America/Rainy_River,America/Rankin_Inlet,America/Resolute,America/Tegucigalpa,America/Winnipeg,Canada/Central,Mexico/General,Asia/Chongqing,Asia/Chungking,Asia/Harbin,Asia/Taipei,PRC,ROC,Europe/Berlin,Europe/Berlin,Europe/Kaliningrad,Africa/Algiers,Africa/Ceuta,Africa/Tripoli,Africa/Tunis,Antarctica/Troll,Arctic/Longyearbyen,Atlantic/Jan_Mayen,Europe/Amsterdam,Europe/Andorra,Europe/Athens,Europe/Belgrade,Europe/Bratislava,Europe/Brussels,Europe/Budapest,Europe/Busingen,Europe/Chisinau,Europe/Copenhagen,Europe/Gibraltar,Europe/Kaliningrad,Europe/Kiev,Europe/Lisbon,Europe/Ljubljana,Europe/Luxembourg,Europe/Madrid,Europe/Malta,Europe/Minsk,Europe/Monaco,Europe/Oslo,Europe/Paris,Europe/Podgorica,Europe/Prague,Europe/Riga,Europe/Rome,Europe/San_Marino,Europe/Sarajevo,Europe/Simferopol,Europe/Skopje,Europe/Sofia,Europe/Stockholm,Europe/Tallinn,Europe/Tirane,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vaduz,Europe/Vatican,Europe/Vienna,Europe/Vilnius,Europe/Warsaw,Europe/Zagreb,Europe/Zaporozhye,Europe/Zurich,Europe/Berlin,Europe/Kaliningrad,Africa/Algiers,Africa/Casablanca,Africa/Ceuta,Africa/Tripoli,Africa/Tunis,Arctic/Longyearbyen,Atlantic/Jan_Mayen,Europe/Amsterdam,Europe/Andorra,Europe/Athens,Europe/Belgrade,Europe/Bratislava,Europe/Brussels,Europe/Budapest,Europe/Busingen,Europe/Chisinau,Europe/Copenhagen,Europe/Gibraltar,Europe/Kaliningrad,Europe/Kiev,Europe/Lisbon,Europe/Ljubljana,Europe/Luxembourg,Europe/Madrid,Europe/Malta,Europe/Minsk,Europe/Monaco,Europe/Oslo,Europe/Paris,Europe/Podgorica,Europe/Prague,Europe/Riga,Europe/Rome,Europe/San_Marino,Europe/Sarajevo,Europe/Simferopol,Europe/Skopje,Europe/Sofia,Europe/Stockholm,Europe/Tallinn,Europe/Tirane,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vaduz,Europe/Vatican,Europe/Vienna,Europe/Vilnius,Europe/Warsaw,Europe/Zagreb,Europe/Zaporozhye,Europe/Zurich,America/Scoresbysund,America/Scoresbysund,Pacific/Chatham,Pacific/Chatham,Pacific/Chatham,America/Belize,Asia/Choibalsan,Asia/Choibalsan,Asia/Choibalsan,Asia/Choibalsan,Pacific/Chuuk,Pacific/Truk,Pacific/Yap,Pacific/Rarotonga,Pacific/Rarotonga,Pacific/Rarotonga,America/Santiago,America/Santiago,Antarctica/Palmer,Chile/Continental,Chile/Continental,America/Santiago,America/Santiago,America/Santiago,Antarctica/Palmer,Chile/Continental,Antarctica/Palmer,Chile/Continental,Chile/Continental,America/Argentina/Buenos_Aires,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Buenos_Aires,America/Catamarca,America/Cordoba,America/Jujuy,America/Mendoza,America/Rosario,America/Caracas,America/La_Paz,America/Cayman,America/Panama,Europe/Chisinau,Europe/Tiraspol,America/Bogota,America/Bogota,America/Chicago,America/Atikokan,America/Coral_Harbour,America/Fort_Wayne,America/Indiana/Indianapolis,America/Indiana/Knox,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Knox_IN,America/Louisville,America/Menominee,America/Rainy_River,America/Winnipeg,Canada/Central,America/Chicago,America/Havana,America/Atikokan,America/Bahia_Banderas,America/Belize,America/Cambridge_Bay,America/Cancun,America/Chihuahua,America/Coral_Harbour,America/Costa_Rica,America/Detroit,America/El_Salvador,America/Fort_Wayne,America/Guatemala,America/Hermosillo,America/Indiana/Indianapolis,America/Indiana/Knox,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Iqaluit,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Knox_IN,America/Louisville,America/Managua,America/Matamoros,America/Mazatlan,America/Menominee,America/Merida,America/Mexico_City,America/Monterrey,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Ojinaga,America/Pangnirtung,America/Rainy_River,America/Rankin_Inlet,America/Regina,America/Resolute,America/Swift_Current,America/Tegucigalpa,America/Thunder_Bay,America/Winnipeg,Canada/Central,Canada/East-Saskatchewan,Canada/Saskatchewan,Mexico/BajaSur,Mexico/General,Asia/Chongqing,Asia/Chungking,Asia/Harbin,Asia/Macao,Asia/Macau,Asia/Shanghai,Asia/Taipei,PRC,ROC,Europe/Zaporozhye,Atlantic/Cape_Verde,Atlantic/Cape_Verde,Atlantic/Cape_Verde,America/Chicago,America/Atikokan,America/Coral_Harbour,America/Fort_Wayne,America/Indiana/Indianapolis,America/Indiana/Knox,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Knox_IN,America/Louisville,America/Menominee,America/Mexico_City,America/Rainy_River,America/Winnipeg,Canada/Central,Mexico/General,Indian/Christmas,Pacific/Guam,Pacific/Saipan,Asia/Dacca,Asia/Dhaka,Antarctica/Davis,Antarctica/Davis,Antarctica/DumontDUrville,Europe/Dublin,Asia/Dushanbe,Asia/Dushanbe,Asia/Dushanbe,Asia/Dushanbe,Chile/EasterIsland,Chile/EasterIsland,Pacific/Easter,Pacific/Easter,Chile/EasterIsland,Chile/EasterIsland,Chile/EasterIsland,Pacific/Easter,Pacific/Easter,Pacific/Easter,Africa/Khartoum,Africa/Addis_Ababa,Africa/Asmara,Africa/Asmera,Africa/Dar_es_Salaam,Africa/Djibouti,Africa/Juba,Africa/Kampala,Africa/Mogadishu,Africa/Nairobi,Indian/Antananarivo,Indian/Comoro,Indian/Mayotte,America/Guayaquil,Pacific/Galapagos,America/Iqaluit,America/New_York,America/Cancun,America/Detroit,America/Fort_Wayne,America/Grand_Turk,America/Indiana/Indianapolis,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Iqaluit,America/Jamaica,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Louisville,America/Montreal,America/Nassau,America/Nipigon,America/Pangnirtung,America/Port-au-Prince,America/Santo_Domingo,America/Thunder_Bay,America/Toronto,Canada/Eastern,Europe/Helsinki,Africa/Cairo,Asia/Amman,Asia/Beirut,Asia/Damascus,Asia/Gaza,Asia/Hebron,Asia/Istanbul,Asia/Nicosia,Europe/Athens,Europe/Bucharest,Europe/Chisinau,Europe/Istanbul,Europe/Kaliningrad,Europe/Kiev,Europe/Mariehamn,Europe/Minsk,Europe/Moscow,Europe/Nicosia,Europe/Riga,Europe/Samara,Europe/Simferopol,Europe/Sofia,Europe/Tallinn,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vilnius,Europe/Warsaw,Europe/Zaporozhye,Europe/Helsinki,Asia/Gaza,Asia/Hebron,Africa/Cairo,Africa/Tripoli,Asia/Amman,Asia/Beirut,Asia/Damascus,Asia/Gaza,Asia/Hebron,Asia/Istanbul,Asia/Nicosia,Europe/Athens,Europe/Bucharest,Europe/Chisinau,Europe/Istanbul,Europe/Kaliningrad,Europe/Kiev,Europe/Mariehamn,Europe/Minsk,Europe/Moscow,Europe/Nicosia,Europe/Riga,Europe/Simferopol,Europe/Sofia,Europe/Tallinn,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vilnius,Europe/Warsaw,Europe/Zaporozhye,America/Scoresbysund,America/Scoresbysund,America/Santo_Domingo,Chile/EasterIsland,Pacific/Easter,America/New_York,America/Detroit,America/Iqaluit,America/Montreal,America/Nipigon,America/Thunder_Bay,America/Toronto,Canada/Eastern,America/New_York,America/Atikokan,America/Cambridge_Bay,America/Cancun,America/Cayman,America/Chicago,America/Coral_Harbour,America/Detroit,America/Fort_Wayne,America/Grand_Turk,America/Indiana/Indianapolis,America/Indiana/Knox,America/Indiana/Marengo,America/Indiana/Petersburg,America/Indiana/Tell_City,America/Indiana/Vevay,America/Indiana/Vincennes,America/Indiana/Winamac,America/Indianapolis,America/Iqaluit,America/Jamaica,America/Kentucky/Louisville,America/Kentucky/Monticello,America/Knox_IN,America/Louisville,America/Managua,America/Menominee,America/Merida,America/Moncton,America/Montreal,America/Nassau,America/Nipigon,America/Panama,America/Pangnirtung,America/Port-au-Prince,America/Rankin_Inlet,America/Resolute,America/Santo_Domingo,America/Thunder_Bay,America/Toronto,Canada/Eastern,America/New_York,America/Detroit,America/Iqaluit,America/Montreal,America/Nipigon,America/Thunder_Bay,America/Toronto,Canada/Eastern,Europe/Kaliningrad,Europe/Minsk,America/Martinique,Pacific/Fiji,Pacific/Fiji,Atlantic/Stanley,Atlantic/Stanley,Atlantic/Stanley,Atlantic/Stanley,Atlantic/Stanley,Atlantic/Madeira,America/Noronha,Brazil/DeNoronha,America/Noronha,Brazil/DeNoronha,Asia/Aqtau,Asia/Aqtau,Asia/Bishkek,Asia/Bishkek,Asia/Bishkek,Asia/Bishkek,Pacific/Galapagos,Pacific/Gambier,America/Guyana,Asia/Tbilisi,Asia/Tbilisi,Asia/Tbilisi,Asia/Tbilisi,America/Cayenne,America/Cayenne,Africa/Accra,Pacific/Tarawa,Africa/Abidjan,Africa/Accra,Africa/Bamako,Africa/Banjul,Africa/Bissau,Africa/Conakry,Africa/Dakar,Africa/Freetown,Africa/Lome,Africa/Monrovia,Africa/Nouakchott,Africa/Ouagadougou,Africa/Sao_Tome,Africa/Timbuktu,America/Danmarkshavn,Atlantic/Reykjavik,Atlantic/St_Helena,Etc/GMT,Etc/Greenwich,Europe/Belfast,Europe/Dublin,Europe/Gibraltar,Europe/Guernsey,Europe/Isle_of_Man,Europe/Jersey,Europe/London,GB,Asia/Dubai,Atlantic/South_Georgia,Asia/Bahrain,Asia/Muscat,Asia/Qatar,Pacific/Guam,Pacific/Saipan,America/Guyana,America/Guyana,America/Guyana,America/Adak,America/Atka,America/Adak,America/Atka,Pacific/Honolulu,Pacific/Johnston,Asia/Hong_Kong,Asia/Hong_Kong,America/Havana,Atlantic/Azores,Asia/Calcutta,Asia/Dacca,Asia/Dhaka,Asia/Kolkata,Europe/Helsinki,Europe/Mariehamn,Asia/Hovd,Asia/Hovd,Asia/Hovd,Pacific/Honolulu,Pacific/Honolulu,Pacific/Johnston,Pacific/Johnston,Asia/Bangkok,Asia/Ho_Chi_Minh,Asia/Phnom_Penh,Asia/Saigon,Asia/Vientiane,Asia/Jerusalem,Asia/Tel_Aviv,Asia/Jerusalem,Asia/Gaza,Asia/Hebron,Asia/Tel_Aviv,Asia/Ho_Chi_Minh,Asia/Saigon,Asia/Colombo,Asia/Irkutsk,Asia/Istanbul,Europe/Istanbul,Indian/Chagos,Indian/Chagos,Asia/Tehran,Asia/Tehran,Asia/Irkutsk,Asia/Irkutsk,Asia/Irkutsk,Asia/Irkutsk,Asia/Irkutsk,Asia/Chita,Asia/Tehran,Asia/Tehran,Atlantic/Reykjavik,Asia/Jerusalem,Atlantic/Reykjavik,Asia/Calcutta,Asia/Colombo,Asia/Dacca,Asia/Dhaka,Asia/Karachi,Asia/Kathmandu,Asia/Katmandu,Asia/Kolkata,Asia/Thimbu,Asia/Thimphu,Europe/Dublin,Asia/Calcutta,Asia/Colombo,Asia/Karachi,Asia/Kolkata,Europe/Dublin,Europe/Dublin,Asia/Gaza,Asia/Hebron,Asia/Tel_Aviv,Asia/Jakarta,Asia/Pyongyang,Asia/Sakhalin,Asia/Seoul,Asia/Tokyo,ROK,Asia/Tokyo,Asia/Jerusalem,Asia/Tel_Aviv,Asia/Tokyo,Asia/Dili,Asia/Ho_Chi_Minh,Asia/Hong_Kong,Asia/Jakarta,Asia/Kuala_Lumpur,Asia/Kuching,Asia/Makassar,Asia/Manila,Asia/Pontianak,Asia/Pyongyang,Asia/Rangoon,Asia/Saigon,Asia/Sakhalin,Asia/Seoul,Asia/Singapore,Asia/Taipei,Asia/Ujung_Pandang,Pacific/Bougainville,Pacific/Nauru,ROC,ROK,Asia/Taipei,ROC,Asia/Karachi,Asia/Seoul,Asia/Seoul,ROK,ROK,Asia/Bishkek,Asia/Bishkek,Asia/Bishkek,Asia/Qyzylorda,Asia/Qyzylorda,Asia/Qyzylorda,Asia/Qyzylorda,Europe/Vilnius,America/Grand_Turk,America/Jamaica,Europe/Kiev,Pacific/Kosrae,Pacific/Kosrae,Asia/Krasnoyarsk,Asia/Krasnoyarsk,Asia/Novokuznetsk,Asia/Novokuznetsk,Asia/Krasnoyarsk,Asia/Krasnoyarsk,Asia/Krasnoyarsk,Asia/Novokuznetsk,Asia/Novokuznetsk,Asia/Seoul,Asia/Pyongyang,Asia/Seoul,Asia/Pyongyang,ROK,ROK,Europe/Samara,Europe/Samara,Europe/Samara,Pacific/Kwajalein,Australia/LHI,Australia/Lord_Howe,Australia/LHI,Australia/Lord_Howe,Australia/Lord_Howe,Australia/LHI,Pacific/Kiritimati,Pacific/Kiritimati,Pacific/Kiritimati,Asia/Colombo,Asia/Colombo,Africa/Monrovia,Europe/Riga,Atlantic/Madeira,Atlantic/Madeira,Atlantic/Madeira,Asia/Magadan,Asia/Magadan,Asia/Srednekolymsk,Asia/Ust-Nera,Asia/Srednekolymsk,Asia/Ust-Nera,Asia/Magadan,Asia/Magadan,Asia/Magadan,Asia/Srednekolymsk,Asia/Ust-Nera,Asia/Srednekolymsk,Asia/Ust-Nera,Asia/Srednekolymsk,Asia/Ust-Nera,Asia/Singapore,Asia/Kuala_Lumpur,Asia/Singapore,Asia/Singapore,Asia/Singapore,Asia/Kuala_Lumpur,Asia/Kuala_Lumpur,Asia/Kuala_Lumpur,Pacific/Marquesas,Antarctica/Mawson,Antarctica/Mawson,America/Cambridge_Bay,America/Yellowknife,Europe/Moscow,America/Denver,America/Bahia_Banderas,America/Boise,America/Cambridge_Bay,America/Chihuahua,America/Edmonton,America/Hermosillo,America/Inuvik,America/Mazatlan,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Ojinaga,America/Phoenix,America/Regina,America/Shiprock,America/Swift_Current,America/Yellowknife,Canada/East-Saskatchewan,Canada/Mountain,Canada/Saskatchewan,Mexico/BajaSur,Pacific/Kwajalein,Pacific/Kwajalein,Pacific/Majuro,Pacific/Majuro,Antarctica/Macquarie,Europe/Moscow,Europe/Moscow,America/Montevideo,America/Managua,Africa/Monrovia,Indian/Maldives,Asia/Colombo,Asia/Rangoon,Asia/Makassar,Asia/Ujung_Pandang,Europe/Minsk,Asia/Macao,Asia/Macau,Asia/Macao,Asia/Macau,America/Denver,America/Boise,America/Cambridge_Bay,America/Edmonton,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Regina,America/Shiprock,America/Swift_Current,America/Yellowknife,Canada/East-Saskatchewan,Canada/Mountain,Canada/Saskatchewan,Europe/Moscow,Europe/Chisinau,Europe/Kaliningrad,Europe/Kiev,Europe/Minsk,Europe/Riga,Europe/Samara,Europe/Simferopol,Europe/Tallinn,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vilnius,Europe/Volgograd,Europe/Zaporozhye,Europe/Moscow,Europe/Moscow,Europe/Chisinau,Europe/Kaliningrad,Europe/Kiev,Europe/Minsk,Europe/Riga,Europe/Samara,Europe/Simferopol,Europe/Tallinn,Europe/Tiraspol,Europe/Uzhgorod,Europe/Vilnius,Europe/Volgograd,Europe/Zaporozhye,Europe/Simferopol,Europe/Volgograd,Europe/Moscow,America/Denver,America/Bahia_Banderas,America/Boise,America/Cambridge_Bay,America/Chihuahua,America/Creston,America/Dawson_Creek,America/Edmonton,America/Ensenada,America/Hermosillo,America/Inuvik,America/Mazatlan,America/Mexico_City,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Ojinaga,America/Phoenix,America/Regina,America/Santa_Isabel,America/Shiprock,America/Swift_Current,America/Tijuana,America/Yellowknife,Canada/East-Saskatchewan,Canada/Mountain,Canada/Saskatchewan,Mexico/BajaNorte,Mexico/BajaSur,Mexico/General,Europe/Moscow,Indian/Mauritius,Indian/Mauritius,Indian/Maldives,America/Denver,America/Boise,America/Cambridge_Bay,America/Edmonton,America/North_Dakota/Beulah,America/North_Dakota/Center,America/North_Dakota/New_Salem,America/Phoenix,America/Regina,America/Shiprock,America/Swift_Current,America/Yellowknife,Canada/East-Saskatchewan,Canada/Mountain,Canada/Saskatchewan,Asia/Kuala_Lumpur,Asia/Kuching,Pacific/Noumea,Pacific/Noumea,America/St_Johns,Canada/Newfoundland,America/St_Johns,America/St_Johns,America/Goose_Bay,Canada/Newfoundland,America/Goose_Bay,Canada/Newfoundland,America/Paramaribo,Europe/Amsterdam,Europe/Amsterdam,Pacific/Norfolk,Pacific/Norfolk,Asia/Novosibirsk,Asia/Novosibirsk,Asia/Novokuznetsk,Asia/Novosibirsk,Asia/Novosibirsk,Asia/Novokuznetsk,Asia/Novokuznetsk,America/St_Johns,Asia/Katmandu,America/Adak,America/Atka,America/Nome,America/Goose_Bay,Canada/Newfoundland,Asia/Kathmandu,Pacific/Nauru,Pacific/Nauru,America/St_Johns,America/St_Johns,Europe/Amsterdam,America/Goose_Bay,Canada/Newfoundland,America/Goose_Bay,Canada/Newfoundland,America/Adak,America/Atka,America/Nome,Pacific/Midway,Pacific/Pago_Pago,Pacific/Samoa,Pacific/Niue,Pacific/Niue,Pacific/Niue,America/St_Johns,America/Adak,America/Atka,America/Nome,America/Goose_Bay,Canada/Newfoundland,Pacific/Auckland,Antarctica/McMurdo,Antarctica/South_Pole,NZ,Pacific/Auckland,Antarctica/McMurdo,Antarctica/South_Pole,NZ,Pacific/Auckland,Pacific/Auckland,Pacific/Auckland,Antarctica/McMurdo,Antarctica/South_Pole,NZ,Antarctica/McMurdo,Antarctica/South_Pole,NZ,Antarctica/McMurdo,Antarctica/South_Pole,NZ,Asia/Omsk,Asia/Omsk,Asia/Omsk,Asia/Omsk,Asia/Omsk,Asia/Oral,Asia/Oral,Asia/Oral,America/Inuvik,America/Los_Angeles,America/Boise,America/Dawson,America/Dawson_Creek,America/Ensenada,America/Juneau,America/Metlakatla,America/Santa_Isabel,America/Sitka,America/Tijuana,America/Vancouver,America/Whitehorse,Canada/Pacific,Canada/Yukon,Mexico/BajaNorte,America/Lima,Asia/Kamchatka,Asia/Kamchatka,Asia/Kamchatka,Asia/Kamchatka,America/Lima,Pacific/Bougainville,Pacific/Port_Moresby,Pacific/Enderbury,Pacific/Enderbury,Pacific/Enderbury,Asia/Manila,Asia/Manila,Asia/Karachi,Asia/Karachi,Asia/Ho_Chi_Minh,Asia/Saigon,America/Miquelon,America/Miquelon,America/Paramaribo,America/Paramaribo,Antarctica/DumontDUrville,Asia/Yekaterinburg,Asia/Pontianak,Africa/Algiers,Africa/Tunis,Europe/Monaco,Europe/Paris,Pacific/Pitcairn,Pacific/Pohnpei,Pacific/Ponape,America/Port-au-Prince,America/Los_Angeles,America/Dawson_Creek,America/Ensenada,America/Juneau,America/Metlakatla,America/Santa_Isabel,America/Sitka,America/Tijuana,America/Vancouver,Canada/Pacific,Mexico/BajaNorte,America/Los_Angeles,America/Bahia_Banderas,America/Boise,America/Creston,America/Dawson,America/Dawson_Creek,America/Ensenada,America/Hermosillo,America/Inuvik,America/Juneau,America/Mazatlan,America/Metlakatla,America/Santa_Isabel,America/Sitka,America/Tijuana,America/Vancouver,America/Whitehorse,Canada/Pacific,Canada/Yukon,Mexico/BajaNorte,Mexico/BajaSur,Pacific/Pitcairn,America/Los_Angeles,America/Dawson_Creek,America/Ensenada,America/Juneau,America/Metlakatla,America/Santa_Isabel,America/Sitka,America/Tijuana,America/Vancouver,Canada/Pacific,Mexico/BajaNorte,Pacific/Palau,America/Asuncion,America/Asuncion,America/Asuncion,America/Guayaquil,Asia/Qyzylorda,Asia/Qyzylorda,Asia/Qyzylorda,Indian/Reunion,Europe/Riga,Asia/Rangoon,Antarctica/Rothera,Asia/Sakhalin,Asia/Sakhalin,Asia/Sakhalin,Asia/Sakhalin,Asia/Samarkand,Europe/Samara,Europe/Samara,Asia/Samarkand,Asia/Samarkand,Europe/Samara,Europe/Samara,Africa/Johannesburg,Africa/Johannesburg,Africa/Johannesburg,Africa/Maseru,Africa/Mbabane,Africa/Windhoek,Africa/Maseru,Africa/Mbabane,Africa/Maseru,Africa/Mbabane,Africa/Windhoek,Pacific/Guadalcanal,Indian/Mahe,America/Santo_Domingo,Pacific/Apia,Asia/Singapore,Asia/Singapore,Asia/Aqtau,Asia/Aqtau,Asia/Aqtau,America/Costa_Rica,Atlantic/Stanley,America/Santiago,Chile/Continental,Asia/Kuala_Lumpur,Asia/Singapore,Europe/Simferopol,Asia/Srednekolymsk,America/Paramaribo,America/Paramaribo,Pacific/Samoa,Pacific/Apia,Pacific/Midway,Pacific/Pago_Pago,Europe/Volgograd,Europe/Volgograd,Asia/Yekaterinburg,Asia/Yekaterinburg,Asia/Yekaterinburg,Asia/Yekaterinburg,Africa/Windhoek,Antarctica/Syowa,Pacific/Tahiti,Asia/Samarkand,Asia/Tashkent,Asia/Tashkent,Asia/Samarkand,Asia/Tashkent,Asia/Tashkent,Asia/Tbilisi,Asia/Tbilisi,Asia/Tbilisi,Asia/Tbilisi,Asia/Tbilisi,Indian/Kerguelen,Asia/Dushanbe,Pacific/Fakaofo,Pacific/Fakaofo,Asia/Dili,Asia/Dili,Asia/Tehran,Europe/Tallinn,Asia/Ashgabat,Asia/Ashkhabad,Asia/Ashgabat,Asia/Ashkhabad,Pacific/Tongatapu,Pacific/Tongatapu,Pacific/Tongatapu,Europe/Istanbul,Asia/Istanbul,Europe/Istanbul,Asia/Istanbul,Europe/Volgograd,Pacific/Funafuti,Etc/UCT,Asia/Ulaanbaatar,Asia/Ulan_Bator,Asia/Ulaanbaatar,Asia/Ulaanbaatar,Asia/Choibalsan,Asia/Ulan_Bator,Asia/Choibalsan,Asia/Ulan_Bator,Asia/Oral,Asia/Oral,Asia/Oral,Asia/Oral,Asia/Oral,Antarctica/Troll,Etc/Universal,Etc/UTC,Etc/Zulu,UTC,UTC,America/Montevideo,America/Montevideo,America/Montevideo,America/Montevideo,America/Montevideo,Asia/Samarkand,Asia/Tashkent,Asia/Samarkand,Asia/Tashkent,America/Caracas,America/Caracas,Asia/Vladivostok,Asia/Vladivostok,Asia/Khandyga,Asia/Vladivostok,Asia/Vladivostok,Asia/Vladivostok,Asia/Khandyga,Asia/Ust-Nera,Asia/Khandyga,Asia/Ust-Nera,Europe/Volgograd,Europe/Volgograd,Europe/Volgograd,Europe/Volgograd,Antarctica/Vostok,Pacific/Efate,Pacific/Efate,Pacific/Wake,America/Mendoza,America/Argentina/Jujuy,America/Argentina/Mendoza,America/Argentina/San_Luis,America/Jujuy,America/Mendoza,America/Argentina/Catamarca,America/Argentina/ComodRivadavia,America/Argentina/Cordoba,America/Argentina/Jujuy,America/Argentina/La_Rioja,America/Argentina/Mendoza,America/Argentina/Rio_Gallegos,America/Argentina/Salta,America/Argentina/San_Juan,America/Argentina/San_Luis,America/Argentina/Tucuman,America/Argentina/Ushuaia,America/Catamarca,America/Cordoba,America/Jujuy,America/Rosario,Africa/Windhoek,Africa/Ndjamena,Africa/Brazzaville,Africa/Bissau,Africa/El_Aaiun,Africa/Bangui,Africa/Douala,Africa/Kinshasa,Africa/Lagos,Africa/Libreville,Africa/Luanda,Africa/Malabo,Africa/Ndjamena,Africa/Niamey,Africa/Porto-Novo,Africa/Windhoek,Europe/Lisbon,Europe/Madrid,Europe/Monaco,Europe/Paris,Europe/Paris,Europe/Luxembourg,Africa/Algiers,Africa/Casablanca,Africa/Ceuta,Africa/El_Aaiun,Atlantic/Canary,Atlantic/Faeroe,Atlantic/Faroe,Atlantic/Madeira,Europe/Brussels,Europe/Lisbon,Europe/Luxembourg,Europe/Madrid,Europe/Monaco,Europe/Paris,Europe/Luxembourg,Africa/Algiers,Africa/Casablanca,Africa/Ceuta,Africa/El_Aaiun,Atlantic/Azores,Atlantic/Canary,Atlantic/Faeroe,Atlantic/Faroe,Atlantic/Madeira,Europe/Andorra,Europe/Brussels,Europe/Lisbon,Europe/Luxembourg,Europe/Madrid,Europe/Monaco,Pacific/Wallis,America/Godthab,America/Danmarkshavn,America/Godthab,America/Danmarkshavn,Asia/Jakarta,Asia/Pontianak,Asia/Jakarta,Asia/Pontianak,Asia/Jakarta,Asia/Pontianak,Asia/Dili,Asia/Makassar,Asia/Pontianak,Asia/Ujung_Pandang,Asia/Jayapura,Europe/Vilnius,Europe/Warsaw,Pacific/Apia,Pacific/Apia,Pacific/Apia,Asia/Kashgar,Asia/Urumqi,Asia/Yakutsk,Asia/Yakutsk,Asia/Chita,Asia/Khandyga,Asia/Chita,Asia/Khandyga,Asia/Yakutsk,Asia/Yakutsk,Asia/Yakutsk,Asia/Chita,Asia/Khandyga,Asia/Ust-Nera,Asia/Chita,Asia/Khandyga,Asia/Ust-Nera,Asia/Chita,Asia/Khandyga,America/Dawson,America/Whitehorse,Canada/Yukon,America/Dawson,America/Juneau,America/Whitehorse,America/Yakutat,Canada/Yukon,Asia/Yekaterinburg,Asia/Yekaterinburg,Asia/Yekaterinburg,Asia/Yerevan,Asia/Yerevan,Asia/Yerevan,Asia/Yerevan,America/Dawson,America/Whitehorse,America/Yakutat,Canada/Yukon,America/Anchorage,America/Dawson,America/Juneau,America/Nome,America/Sitka,America/Whitehorse,America/Yakutat,Canada/Yukon,America/Dawson,America/Whitehorse,America/Yakutat,Canada/Yukon,Antarctica/Davis,America/Cambridge_Bay,America/Inuvik,America/Iqaluit,America/Pangnirtung,America/Rankin_Inlet,America/Resolute,America/Yellowknife,Antarctica/Casey,Antarctica/DumontDUrville,Antarctica/Macquarie,Antarctica/Mawson,Antarctica/Palmer,Antarctica/Rothera,Antarctica/Syowa,Antarctica/Troll,Antarctica/Vostok,Indian/Kerguelen', NULL),
(78, 'general_reminder_hour', 'Reminder Hour', NULL, '12', 0, 'select', '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23', NULL),
(79, 'label_certificates', 'Certificates', NULL, NULL, 0, 'text', NULL, NULL),
(80, 'menu_show_certificates', 'Show Certificates', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(81, 'menu_show_downloads', 'Show Downloads', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(82, 'label_downloads', 'Downloads', NULL, NULL, 0, 'text', NULL, NULL),
(83, 'general_chat_code', 'Live Chat Code', 'Enter in the code from your live chat provider here', NULL, 0, 'textarea', NULL, NULL),
(84, 'menu_show_homework', 'Show Homework', NULL, '1', 0, 'radio', '1=Yes,0=No', NULL),
(85, 'label_courses', 'Online Courses', NULL, NULL, 0, 'text', NULL, NULL),
(86, 'label_my_sessions', 'My Sessions & Courses', NULL, NULL, 0, 'text', NULL, NULL),
(87, 'label_homework', 'Homework', NULL, NULL, 0, 'text', NULL, NULL),
(88, 'regis_confirm_email', 'Confirm Student Emails', NULL, '0', 0, 'radio', '1=Yes,0=No', NULL),
(89, 'label_featured', 'Featured', NULL, NULL, 0, 'text', NULL, NULL),
(90, 'label_calendar', 'Calendar', NULL, NULL, 0, 'text', NULL, NULL),
(91, 'label_blog_posts', 'Blog Posts', NULL, NULL, 0, 'text', NULL, NULL),
(92, 'label_register', 'Register', NULL, NULL, 0, 'text', NULL, NULL),
(93, 'info_terms', 'Terms & Conditions', NULL, NULL, 0, 'textarea', NULL, 'rte'),
(94, 'info_privacy', 'Privacy Policy', NULL, NULL, 0, 'textarea', NULL, 'rte'),
(95, 'general_address', 'Contact Address', NULL, 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ', 0, 'textarea', NULL, 'rte'),
(96, 'general_tel', 'Contact Telephone No', NULL, '+4495869585', 0, 'text', NULL, NULL),
(97, 'general_contact_email', 'Contact Email', NULL, 'info@company.net', 0, 'text', NULL, NULL),
(98, 'social_enable_facebook', 'Enable Facebook Login', NULL, '0', 0, 'radio', '1=Yes,0=No', NULL),
(99, 'social_facebook_secret', 'Facebook App Secret', NULL, NULL, 0, 'text', NULL, NULL),
(100, 'social_facebook_app_id', 'Facebook App ID', NULL, NULL, 0, 'text', NULL, NULL),
(101, 'social_enable_google', 'Enable Google Login', NULL, '0', 0, 'radio', '1=Yes,0=No', NULL),
(102, 'social_google_secret', 'Google App Secret', NULL, NULL, 0, 'text', NULL, NULL),
(103, 'social_google_id', 'Google ID', NULL, NULL, 0, 'text', NULL, NULL),
(104, 'sms_enabled', 'Enable SMS?', NULL, '0', 0, 'checkbox', NULL, NULL),
(105, 'sms_sender_name', 'Sender name', NULL, '', 0, 'text', NULL, NULL),
(106, 'label_sessions_courses', 'Sessions & Courses', NULL, NULL, 0, 'text', NULL, NULL),
(107, 'label_session_course', 'Session/Course', NULL, NULL, 0, 'text', NULL, NULL),
(108, 'banner_status', 'Enable Banner', NULL, '0', 0, 'radio', '1=Yes,0=No', NULL),
(109, 'banner_app_name', 'App Name', NULL, NULL, 0, 'text', NULL, NULL),
(110, 'banner_android_id', 'Android ID', NULL, NULL, 0, 'text', NULL, NULL),
(111, 'banner_ios_id', 'iOS ID', NULL, NULL, 0, 'text', NULL, NULL),
(112, 'banner_icon_url', 'Icon URL', NULL, NULL, 0, 'text', NULL, NULL),
(113, 'regis_captcha_type', 'Captcha Type', NULL, 'image', 0, 'select', 'image=Image,google=Google reCAPTCHA v3', NULL),
(114, 'regis_recaptcha_key', 'Recaptcha Site Key', NULL, NULL, 0, 'text', NULL, NULL),
(115, 'regis_recaptcha_secret', 'Recaptcha Secret Key', NULL, NULL, 0, 'text', NULL, NULL),
(116, 'config_language', 'Language', NULL, 'en', 0, 'text', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sms_gateway`
--

CREATE TABLE `sms_gateway` (
  `sms_gateway_id` int(11) NOT NULL,
  `gateway_name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `about` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sms_gateway`
--

INSERT INTO `sms_gateway` (`sms_gateway_id`, `gateway_name`, `url`, `country`, `code`, `active`, `about`) VALUES
(1, 'Smart SMS Solutions', 'http://smartsmssolutions.com', 'Nigeria', 'smartsms', 0, 'Smart SMS Solutions is one of the most affordable sms gateways in the Nigerian market. However they only support sending texts within Nigeria'),
(2, 'Cheap global sms', 'https://cheapglobalsms.com', 'Nigeria', 'cheapglobal', 0, 'Cheap global sms has affordable rates. They also support sending SMS internationally'),
(3, 'Clickatell', 'https://www.clickatell.com', 'South Africa, United States', 'clickatell', 0, 'SA born Clickatell offers global sms solutions');

-- --------------------------------------------------------

--
-- Table structure for table `sms_gateway_field`
--

CREATE TABLE `sms_gateway_field` (
  `sms_gateway_field_id` int(11) NOT NULL,
  `sms_gateway_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  `options` text,
  `class` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sms_gateway_field`
--

INSERT INTO `sms_gateway_field` (`sms_gateway_field_id`, `sms_gateway_id`, `label`, `key`, `type`, `value`, `options`, `class`, `placeholder`, `sort_order`) VALUES
(1, 1, 'Username', 'username', 'text', NULL, NULL, NULL, 'Your login username', 1),
(2, 1, 'Password', 'password', 'text', NULL, NULL, NULL, NULL, 2),
(3, 2, 'Sub Account', 'sub_account', 'text', NULL, NULL, NULL, 'Your sub account', 1),
(4, 2, 'Sub Account Password', 'sub_account_pass', 'text', NULL, NULL, NULL, 'Your sub account password', 2),
(5, 3, 'Api Key', 'apikey', 'text', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sms_template`
--

CREATE TABLE `sms_template` (
  `sms_template_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `message` text NOT NULL,
  `default` text NOT NULL,
  `placeholders` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sms_template`
--

INSERT INTO `sms_template` (`sms_template_id`, `name`, `description`, `message`, `default`, `placeholders`) VALUES
(1, 'Upcoming class reminder (physical location)', 'This message is sent to students to remind them when a class is scheduled to hold.', 'Reminder! The [SESSION_NAME] class \'[SESSION_NAME]\' holds on [CLASS_DATE]. Venue: [CLASS_VENUE] . Starts: [CLASS_START_TIME] . Ends: [CLASS_END_TIME]', 'Reminder! The [SESSION_NAME] class \'[SESSION_NAME]\' holds on [CLASS_DATE]. Venue: [CLASS_VENUE] . Starts: [CLASS_START_TIME] . Ends: [CLASS_END_TIME]', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_DATE] : The class date</li>\r\n                <li>[SESSION_NAME] : The name of the session the class is attached to</li>\r\n                <li>[CLASS_VENUE] : The venue of the class</li>\r\n                <li>[CLASS_START_TIME] : The start time for the class</li>\r\n                <li>[CLASS_END_TIME] : The end time for the class</li> \r\n                </ul>\r\n                '),
(2, 'Upcoming class reminder (online class)', 'This message is sent to students to remind them when an online class is scheduled to open.', 'Reminder! The [COURSE_NAME] class \'[CLASS_NAME]\' starts on  [CLASS_DATE]', 'Reminder! The [COURSE_NAME] class \'[CLASS_NAME]\' starts on  [CLASS_DATE]', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_DATE] : The class date</li>\r\n                <li>[COURSE_NAME] : The name of the session the class is attached to</li>  \r\n                </ul>\r\n                '),
(3, 'Upcoming Test reminder', 'This message is sent to users when there is an upcoming test in a session/course they are enrolled in', 'Reminder: The \'[SESSION_NAME]\' test \'[TEST_NAME]\' opens on [OPENING_DATE] and closes on [CLOSING_DATE]', 'Reminder: The \'[SESSION_NAME]\' test \'[TEST_NAME]\' opens on [OPENING_DATE] and closes on [CLOSING_DATE]', '\r\n                <ul>\r\n                <li>[TEST_NAME] : The name of the test</li>\r\n                <li>[TEST_DESCRIPTION] : The description of the test</li>\r\n                <li>[SESSION_NAME] : The name of the session or course the test is attached to</li>\r\n                <li>[OPENING_DATE] : The opening date of the test</li>\r\n                <li>[CLOSING_DATE] : The closing date of the test</li>\r\n                <li>[PASSMARK] : The test passmark e.g. 50%</li>\r\n                <li>[MINUTES_ALLOWED]: The number of minutes allowed for the test</li>  \r\n                </ul>\r\n                '),
(4, 'Online Class start notification', 'This message is sent to students when a scheduled online class opens', 'Please be reminded that the class \'[CLASS_NAME]\' for the course \'[COURSE_NAME]\' has started. <br/>\r\nVisit this link to take this class now: [CLASS_URL]', 'Please be reminded that the class \'[CLASS_NAME]\' for the course \'[COURSE_NAME]\' has started. <br/>\r\nVisit this link to take this class now: [CLASS_URL]', '\r\n                <ul>\r\n                <li>[CLASS_NAME] : The name of the class</li>\r\n                <li>[CLASS_URL] : The url of the class</li> \r\n                <li>[COURSE_NAME] : The name of the course the class belongs to</li> \r\n                </ul>\r\n                '),
(5, 'Homework reminder', 'This message is sent to students reminding them when a homework is due', 'Please be reminded that the homework \'[HOMEWORK_NAME]\' is due on [DUE_DATE]. \r\nPlease click this link to submit your homework now: [HOMEWORK_URL]', 'Please be reminded that the homework \'[HOMEWORK_NAME]\' is due on [DUE_DATE]. \r\nPlease click this link to submit your homework now: [HOMEWORK_URL]', '\r\n                <ul>\r\n                <li>[NUMBER_OF_DAYS] : The number of days remaining till the homework due date e.g. 1,2,3</li>\r\n                <li>[DAY_TEXT] : The \'day\' text. Defaults to \'day\' if [NUMBER_OF_DAYS] is 1 and \'days\' if greater than 1.</li>\r\n                <li>[HOMEWORK_NAME] : The name of the homework</li>\r\n                <li>[HOMEWORK_URL] : The homework url</li>\r\n                <li>[HOMEWORK_INSTRUCTION] : The instructions for the homework</li>\r\n                <li>[PASSMARK] : The passmark for the homework</li>\r\n                 <li>[DUE_DATE] : The homework due date</li>\r\n                <li>[OPENING_DATE] : The homework opening date</li>\r\n                 \r\n                </ul>\r\n                '),
(6, 'Course closing reminder', 'Warning email sent to enrolled students about a course that will close soon.', 'Please be reminded that the course \'[COURSE_NAME]\' closes on [CLOSING_DATE]. \r\nPlease click this link to complete the course now: [COURSE_URL]', 'Please be reminded that the course \'[COURSE_NAME]\' closes on [CLOSING_DATE]. \r\nPlease click this link to complete the course now: [COURSE_URL]', '\r\n                <ul>\r\n                <li>[COURSE_NAME] : The name of the course</li>\r\n                <li>[COURSE_URL] : The course URL</li>\r\n                <li>[CLOSING_DATE] : The closing date for the course</li> \r\n                 <li>[NUMBER_OF_DAYS] : The number of days remaining till the closing date e.g. 1,2,3</li>\r\n                <li>[DAY_TEXT] : The \'day\' text. Defaults to \'day\' if [NUMBER_OF_DAYS] is 1 and \'days\' if greater than 1.</li>\r\n                \r\n                </ul>\r\n                ');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(70) NOT NULL DEFAULT '',
  `last_name` varchar(70) NOT NULL DEFAULT '',
  `mobile_number` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `password` varchar(100) NOT NULL,
  `student_created` int(11) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `social_login` tinyint(1) NOT NULL DEFAULT '0',
  `registration_complete` tinyint(1) NOT NULL DEFAULT '1',
  `last_seen` int(11) DEFAULT '0',
  `api_token` varchar(255) DEFAULT NULL,
  `token_expires` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `student_certificate`
--

CREATE TABLE `student_certificate` (
  `student_certificate_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `certificate_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `tracking_number` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_certificate_download`
--

CREATE TABLE `student_certificate_download` (
  `student_certificate_download_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `certificate_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_field`
--

CREATE TABLE `student_field` (
  `student_field_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `registration_field_id` int(10) UNSIGNED NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_lecture`
--

CREATE TABLE `student_lecture` (
  `student_lecture_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `date` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_registration`
--

CREATE TABLE `student_registration` (
  `student_registration_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `data` text NOT NULL,
  `code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_session`
--

CREATE TABLE `student_session` (
  `student_session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `reg_code` varchar(45) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `enrolled_on` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `student_session_log`
--

CREATE TABLE `student_session_log` (
  `id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `log_date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_test`
--

CREATE TABLE `student_test` (
  `student_test_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `score` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_test_option`
--

CREATE TABLE `student_test_option` (
  `student_test_option_id` int(11) NOT NULL,
  `student_test_id` int(11) NOT NULL,
  `test_option_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_video`
--

CREATE TABLE `student_video` (
  `student_video_id` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `video_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `survey_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_on` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `survey_option`
--

CREATE TABLE `survey_option` (
  `survey_option_id` int(11) NOT NULL,
  `survey_question_id` int(11) NOT NULL,
  `option` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `survey_question`
--

CREATE TABLE `survey_question` (
  `survey_question_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `survey_response`
--

CREATE TABLE `survey_response` (
  `survey_response_id` int(11) NOT NULL,
  `survey_id` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `survey_response_option`
--

CREATE TABLE `survey_response_option` (
  `survey_response_option_id` int(11) NOT NULL,
  `survey_response_id` int(11) NOT NULL,
  `survey_option_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE `template` (
  `template_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `template`
--

INSERT INTO `template` (`template_id`, `name`, `sort_order`, `active`) VALUES
(1, 'Classic', 1, 0),
(2, 'Corporate', 2, 0),
(3, 'Varsity', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `template_option`
--

CREATE TABLE `template_option` (
  `template_option_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` text,
  `group` varchar(255) NOT NULL,
  `options` text,
  `class` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `template_option`
--

INSERT INTO `template_option` (`template_option_id`, `template_id`, `label`, `key`, `type`, `value`, `group`, `options`, `class`, `placeholder`, `sort_order`) VALUES
(1, 1, 'Navigation Bar', 'color_navbar', 'color', '', 'color', NULL, NULL, NULL, 1),
(2, 1, 'Primary Color', 'color_primary_color', 'color', '', 'color', NULL, NULL, NULL, 2),
(3, 1, 'Navigation text color', 'color_navtext', 'color', '', 'color', NULL, NULL, NULL, 3),
(4, 1, 'Footer background color', 'color_footer', 'color', '', 'color', NULL, NULL, NULL, 4),
(5, 1, 'Footer text color', 'color_footertext', 'color', '', 'color', NULL, NULL, NULL, 5),
(6, 1, 'Page title background color', 'color_page_title', 'color', '', 'color', NULL, NULL, NULL, 6),
(7, 1, 'Page title text color', 'color_page_title_text', 'color', '', 'color', NULL, NULL, NULL, 7),
(8, 1, 'Facebook', 'social_facebook', 'text', '#', 'social', NULL, NULL, NULL, 1),
(9, 1, 'Twitter', 'social_twitter', 'text', '#', 'social', NULL, NULL, NULL, 2),
(10, 1, 'Instagram', 'social_instagram', 'text', '#', 'social', NULL, NULL, NULL, 3),
(11, 1, 'Google', 'social_google', 'text', '#', 'social', NULL, NULL, NULL, 4),
(12, 1, 'Youtube', 'social_youtube', 'text', '#', 'social', NULL, NULL, NULL, 5),
(13, 1, 'About Us', 'footer_about', 'textarea', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>\r\n', 'footer', NULL, 'rte', NULL, 1),
(14, 1, 'Address', 'footer_address', 'textarea', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ', 'footer', NULL, 'rte', NULL, 2),
(15, 1, 'Email', 'footer_email', 'text', 'info@company.net', 'footer', NULL, NULL, NULL, 3),
(16, 1, 'Telephone', 'footer_tel', 'text', '+4495869585', 'footer', NULL, NULL, NULL, 4),
(17, 1, 'Newsletter Form Code', 'footer_newsletter_code', 'textarea', '', 'footer', NULL, NULL, NULL, 5),
(18, 1, 'Site Credits', 'footer_credits', 'text', '', 'footer', NULL, NULL, NULL, 6),
(19, 1, 'Show Social Icons', 'footer_show_sicons', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 7),
(20, 1, 'Show Newsletter Form', 'footer_show_newsletter', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 8),
(21, 1, 'Show About Us', 'footer_show_about', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 9),
(22, 1, 'Show Contact Us', 'footer_show_contact', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 10),
(23, 2, 'Top Bar Background Color', 'topbar_bgcolor', 'color', '', 'top_bar', NULL, NULL, NULL, 1),
(24, 2, 'Top Bar Font Color', 'topbar_fontcolor', 'color', NULL, 'top_bar', NULL, NULL, NULL, 2),
(25, 2, 'Top Bar Slogan', 'topbar_slogan', 'text', 'Enroll for your online course or training session today', 'top_bar', NULL, NULL, 'Slogan text on top bar', 3),
(26, 2, 'Top Bar Icon', 'topbar_icon', 'text', 'graduation-cap', 'top_bar', NULL, NULL, 'Font awesome icon', 4),
(27, 2, 'Facebook Url', 'topbar_facebook', 'text', '#', 'top_bar', NULL, NULL, 'Full facebook url', 4),
(28, 2, 'Twitter Url', 'topbar_twitter', 'text', '#', 'top_bar', NULL, NULL, 'Full twitter url', 5),
(29, 2, 'Google+ Url', 'topbar_google', 'text', '#', 'top_bar', NULL, NULL, 'Full Google+ url', 6),
(30, 2, 'Linkedin Url', 'topbar_linkedin', 'text', '#', 'top_bar', NULL, NULL, 'Full linkedin url', 7),
(31, 2, 'Instagram Url', 'topbar_instagram', 'text', '#', 'top_bar', NULL, NULL, 'Full instagram url', 8),
(32, 2, 'Navigation Bar Background Color', 'navbar_bgcolor', 'color', '', 'navigation_bar', NULL, NULL, NULL, 1),
(33, 2, 'Navigation Bar Font Color', 'navbar_fontcolor', 'color', '', 'navigation_bar', NULL, NULL, NULL, 2),
(34, 2, 'Primary Color', 'color_primary_color', 'color', '', 'colors', NULL, NULL, NULL, 1),
(35, 2, 'Primary Button Color', 'color_primary_btn_color', 'color', NULL, 'colors', NULL, NULL, NULL, 2),
(36, 2, 'Secondary Button Color', 'color_secondary_btn_color', 'color', NULL, 'colors', NULL, NULL, NULL, 3),
(37, 2, 'Page title background color', 'color_page_title', 'color', '', 'colors', NULL, NULL, NULL, 4),
(38, 2, 'Page title text color', 'color_page_title_text', 'color', '', 'colors', NULL, NULL, NULL, 5),
(39, 2, 'Footer background color', 'footer_bgcolor', 'color', '', 'footer', NULL, NULL, NULL, 1),
(40, 2, 'Footer text color', 'footer_textcolor', 'color', '', 'footer', NULL, NULL, NULL, 2),
(41, 2, 'About Us', 'footer_about', 'textarea', '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>\r\n', 'footer', NULL, 'rte', NULL, 3),
(42, 2, 'Website', 'footer_website', 'text', '', 'footer', NULL, NULL, NULL, 4),
(43, 2, 'Address', 'footer_address', 'text', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. ', 'footer', NULL, NULL, NULL, 5),
(44, 2, 'Email', 'footer_email', 'text', 'info@company.net', 'footer', NULL, NULL, NULL, 6),
(45, 2, 'Telephone', 'footer_tel', 'text', '+4495869585', 'footer', NULL, NULL, NULL, 7),
(46, 2, 'Newsletter Form Code', 'footer_newsletter_code', 'textarea', '', 'footer', NULL, NULL, NULL, 8),
(47, 2, 'Site Credits', 'footer_credits', 'text', NULL, 'footer', NULL, NULL, NULL, 9),
(48, 2, 'Credits background color', 'footer_credits_bgcolor', 'color', '', 'footer', NULL, NULL, NULL, 10),
(49, 2, 'Credits text color', 'footer_credits_textcolor', 'color', '', 'footer', NULL, NULL, NULL, 11),
(54, 3, 'Show homepage paralax photo', 'show_paralax_photo', 'radio', '1', 'home_page_reviews', '1=Yes,0=No', NULL, NULL, 17),
(55, 3, 'Homepage paralax photo', 'paralax_photo', 'image', NULL, 'home_page_reviews', NULL, NULL, '1664px x 1202px', 18),
(58, 3, 'Show reviews', 'show_reviews', 'radio', '1', 'home_page_reviews', '1=Yes,0=No', NULL, NULL, 1),
(59, 3, 'Review 1 text', 'review_1_text', 'textarea', NULL, 'home_page_reviews', NULL, NULL, NULL, 2),
(60, 3, 'Review 1 name', 'review_1_name', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 3),
(61, 3, 'Review 1 company', 'review_1_company', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 4),
(62, 3, 'Review 1 stars', 'review_1_stars', 'select', NULL, 'home_page_reviews', '1=1,2=2,3=3,4=4,5=5', NULL, NULL, 5),
(63, 3, 'Review 1 photo', 'review_1_photo', 'image', NULL, 'home_page_reviews', NULL, NULL, '50px x 50px', 6),
(64, 3, 'Review 2 text', 'review_2_text', 'textarea', NULL, 'home_page_reviews', NULL, NULL, NULL, 7),
(65, 3, 'Review 2 name', 'review_2_name', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 8),
(66, 3, 'Review 2 company', 'review_2_company', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 9),
(67, 3, 'Review 2 stars', 'review_2_stars', 'select', NULL, 'home_page_reviews', '1=1,2=2,3=3,4=4,5=5', NULL, NULL, 10),
(68, 3, 'Review 2 photo', 'review_2_photo', 'image', NULL, 'home_page_reviews', NULL, NULL, '50px x 50px', 11),
(69, 3, 'Review 3 text', 'review_3_text', 'textarea', NULL, 'home_page_reviews', NULL, NULL, NULL, 12),
(70, 3, 'Review 3 name', 'review_3_name', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 13),
(71, 3, 'Review 3 company', 'review_3_company', 'text', NULL, 'home_page_reviews', NULL, NULL, NULL, 14),
(72, 3, 'Review 3 stars', 'review_3_stars', 'select', NULL, 'home_page_reviews', '1=1,2=2,3=3,4=4,5=5', NULL, NULL, 15),
(73, 3, 'Review 3 photo', 'review_3_photo', 'image', NULL, 'home_page_reviews', NULL, NULL, '50px x 50px', 16),
(74, 3, 'Navigation Bar Background Color', 'navbar_bgcolor', 'color', NULL, 'navigation_bar', NULL, NULL, NULL, 1),
(75, 3, 'Navigation Bar Font Color', 'navbar_fontcolor', 'color', NULL, 'navigation_bar', NULL, NULL, NULL, 2),
(76, 3, 'Primary Color', 'color_primary_color', 'color', NULL, 'colors', NULL, NULL, NULL, 1),
(77, 3, 'Primary Button Color', 'color_primary_btn_color', 'color', NULL, 'colors', NULL, NULL, NULL, 2),
(78, 3, 'Page title background color', 'color_page_title', 'color', NULL, 'colors', NULL, NULL, NULL, 3),
(79, 3, 'Page title text color', 'color_page_title_text', 'color', NULL, 'colors', NULL, NULL, NULL, 5),
(80, 3, 'Footer background color', 'footer_bgcolor', 'color', NULL, 'footer', NULL, NULL, NULL, 1),
(81, 3, 'Footer text color', 'footer_textcolor', 'color', NULL, 'footer', NULL, NULL, NULL, 2),
(82, 3, 'Newsletter Form Code', 'footer_newsletter_code', 'textarea', NULL, 'footer', NULL, NULL, NULL, 3),
(83, 3, 'Site Credits', 'footer_credits', 'text', NULL, 'footer', NULL, NULL, NULL, 5),
(84, 3, 'Facebook Url', 'footer_facebook', 'text', '#', 'social', NULL, NULL, 'Full facebook url', 5),
(85, 3, 'Twitter Url', 'footer_twitter', 'text', '#', 'social', NULL, NULL, 'Full twitter url', 6),
(86, 3, 'Youtube URL', 'footer_youtube', 'text', '#', 'social', NULL, NULL, 'Full Youtube url', 7),
(87, 3, 'Linkedin Url', 'footer_linkedin', 'text', '#', 'social', NULL, NULL, 'Full linkedin url', 8),
(88, 3, 'Instagram Url', 'footer_instagram', 'text', '#', 'social', NULL, NULL, 'Full instagram url', 9),
(89, 3, 'Show Newsletter Signup Form', 'show_newsletter', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 4),
(90, 3, 'Show About Us in footer', 'show_footer_about', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 6),
(91, 3, 'Footer About Us', 'footer_about_us', 'textarea', NULL, 'footer', NULL, NULL, NULL, 7),
(92, 3, 'Show Contact Us in footer', 'show_footer_contact', 'radio', '1', 'footer', '1=Yes,0=No', NULL, NULL, 8),
(93, 3, 'Address', 'footer_address', 'text', NULL, 'footer', NULL, NULL, NULL, 9),
(94, 3, 'Email', 'footer_email', 'text', NULL, 'footer', NULL, NULL, NULL, 10),
(95, 3, 'Telephone', 'footer_tel', 'text', NULL, 'footer', NULL, NULL, NULL, 11),
(96, 3, 'Top Bar Background Color', 'topbar_bgcolor', 'color', NULL, 'navigation_bar', NULL, NULL, NULL, 3),
(97, 3, 'Top Bar Text Color', 'topbar_textcolor', 'color', NULL, 'navigation_bar', NULL, NULL, NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `test_id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `created_on` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `minutes` int(11) NOT NULL,
  `allow_multiple` tinyint(1) NOT NULL,
  `passmark` float NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `show_result` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `test_grade`
--

CREATE TABLE `test_grade` (
  `test_grade_id` int(11) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `test_grade`
--

INSERT INTO `test_grade` (`test_grade_id`, `grade`, `min`, `max`) VALUES
(1, 'A', 70, 100),
(2, 'B', 60, 69),
(3, 'C', 50, 59),
(4, 'D', 45, 49),
(5, 'E', 40, 44),
(6, 'F', 0, 39);

-- --------------------------------------------------------

--
-- Table structure for table `test_option`
--

CREATE TABLE `test_option` (
  `test_option_id` int(11) NOT NULL,
  `test_question_id` int(11) NOT NULL,
  `option` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `test_question`
--

CREATE TABLE `test_question` (
  `test_question_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `amount` float NOT NULL,
  `status` varchar(45) DEFAULT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `payment_method_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `video_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` int(11) NOT NULL,
  `account_id` int(10) UNSIGNED NOT NULL,
  `ready` tinyint(1) NOT NULL,
  `length` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `widget`
--

CREATE TABLE `widget` (
  `widget_id` int(10) UNSIGNED NOT NULL,
  `widget_name` varchar(250) NOT NULL,
  `widget_code` varchar(45) NOT NULL,
  `form` mediumtext NOT NULL,
  `widget_description` mediumtext NOT NULL,
  `repeat` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `widget`
--

INSERT INTO `widget` (`widget_id`, `widget_name`, `widget_code`, `form`, `widget_description`, `repeat`) VALUES
(1, 'Slideshow', 'slideshow', '\n\n<div class=\"box\" style=\" border-bottom:solid 1px #999999\">\n    <div class=\"box-head\">\n        <header><h4 class=\"text-light\">Slide <strong>[num]</strong>  </h4></header>\n    </div>\n    <div class=\"box-body \" class=\"slideroptions\">\n\n\n        <div class=\"form-group\" style=\"margin-bottom:10px\">\n\n            <label for=\"slideshow_image[num]\" class=\"control-label\">Image</label><br />\n\n\n            <div class=\"image\"><img data-name=\"slideshow_image[num]\" src=\"[no_image]\" alt=\"\" id=\"slideshow_thumb[num]\" /><br />\n                <input class=\"form-control\" type=\"hidden\" name=\"slideshow_image[num]\" value=\"\" id=\"slideshow_image[num]\" />\n                <a class=\"pointer\" onclick=\"image_upload(\'slideshow_image[num]\', \'slideshow_thumb[num]\');\">Browse</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class=\"pointer\" onclick=\"$(\'#slideshow_thumb[num]\').attr(\'src\', \'[no_image]\'); $(\'#slideshow_image[num]\').attr(\'value\', \'\');\">Clear</a></div>\n\n        </div>\n\n\n\n\n        <div class=\"row\" style=\"margin-bottom: 20px\">\n            <div class=\"col-md-12\">\n                <div class=\"form-group form-group-sm form-group-md\">\n                    <div class=\"col-md-2 col-sm-3\">\n                        <label class=\"control-label\">URL:</label>\n                    </div>\n\n                    <div class=\"col-md-5 col-sm-5\">\n                        <input name=\"url[num]\"   class=\"form-control\" placeholder=\"Full URL e.g. http://...\"  type=\"text\">\n\n\n\n                    </div>\n                </div>\n            </div>\n\n        </div>\n\n\n \n\n\n\n\n\n\n    </div>\n\n\n\n\n\n</div><!--end .box -->\n			 \n\n\n\n\n\n\n ', 'An image slideshow', 10),
(2, 'Text & Button', 'textbtn', '<div class=\"form-group\"  >\n\n    <label for=\"message\" class=\"control-label\">Content</label>\n\n    <textarea name=\"message\"  class=\"rte\" rows=\"2\" ></textarea> \n\n</div>\n\n<div class=\"form-group\">\n    <label for=\"buttontext\">Button Text (Optional)</label>\n    <input class=\"form-control\" type=\"text\" name=\"buttontext\"/>\n</div>\n\n<div class=\"form-group\">\n    <label for=\"Button Link\">Button Link (Optional)</label>\n    <input class=\"form-control\" type=\"text\" name=\"buttonlink\"/>\n</div>', 'Enter formatted text content. Images are allowed.', 0),
(3, 'Featured', 'sessions', '<div class=\"form-group\">\n    <label for=\"\">Select Session/Course</label>\n    [sessionselect]\n</div>', 'Select sessions/courses to feature on the homepage', 10),
(4, 'Session Calendar', 'calendar', '<div></div>', 'Show the session calendar on the homepage', 0),
(5, 'Plain Text', 'text', '<div class=\"form-group\"  >\n\n    <label for=\"message\" class=\"control-label\">Content</label>\n\n    <textarea name=\"message\"  class=\"rte\" rows=\"2\" ></textarea> \n\n</div>\n\n ', 'Enter plain text', 0),
(6, 'Blog Posts', 'blog', '<div class=\"form-group\"  >\n\n    <label for=\"limit\" class=\"control-label\">Number of Posts to Display</label>\n\n    <input type=\"text\" name=\"limit\" class=\"form-control number\" value=\"4\" />\n\n</div>', 'Add recent blog posts to site', 0),
(7, 'Signup Form', 'signup', '<div></div>', 'Add the signup form to the homepage', 0);

-- --------------------------------------------------------

--
-- Table structure for table `widget_value`
--

CREATE TABLE `widget_value` (
  `widget_value_id` int(10) UNSIGNED NOT NULL,
  `widget_id` int(10) UNSIGNED NOT NULL,
  `value` mediumtext,
  `enabled` tinyint(1) DEFAULT '0',
  `sort_order` int(10) UNSIGNED DEFAULT '1',
  `visibility` char(255) NOT NULL DEFAULT 'w'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `widget_value`
--

INSERT INTO `widget_value` (`widget_value_id`, `widget_id`, `value`, `enabled`, `sort_order`, `visibility`) VALUES
(1, 2, 'a:5:{s:7:\"enabled\";s:1:\"1\";s:10:\"sort_order\";s:1:\"1\";s:7:\"message\";s:497:\"<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.</p>\r\n\";s:10:\"buttontext\";s:10:\"Learn More\";s:10:\"buttonlink\";s:1:\"#\";}', 1, 1, 'w'),
(3, 4, NULL, 1, 3, 'w');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `Unique` (`email`) USING BTREE,
  ADD KEY `FK_accounts_1` (`role_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`),
  ADD UNIQUE KEY `Index_2` (`alias`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `assignment_submission`
--
ALTER TABLE `assignment_submission`
  ADD PRIMARY KEY (`assignment_submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `FK_attendance_2` (`student_id`),
  ADD KEY `FK_attendance_3` (`session_id`);

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `lecture_page_id` (`lecture_page_id`);

--
-- Indexes for table `certificate`
--
ALTER TABLE `certificate`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `certificate_assignment`
--
ALTER TABLE `certificate_assignment`
  ADD PRIMARY KEY (`certificate_id`,`assignment_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `certificate_lesson`
--
ALTER TABLE `certificate_lesson`
  ADD PRIMARY KEY (`certificate_id`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `certificate_test`
--
ALTER TABLE `certificate_test`
  ADD PRIMARY KEY (`certificate_id`,`test_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`) USING BTREE;

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`coupon_id`);

--
-- Indexes for table `coupon_category`
--
ALTER TABLE `coupon_category`
  ADD PRIMARY KEY (`coupon_category_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `session_category_id` (`session_category_id`);

--
-- Indexes for table `coupon_invoice`
--
ALTER TABLE `coupon_invoice`
  ADD PRIMARY KEY (`coupon_invoice_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `coupon_session`
--
ALTER TABLE `coupon_session`
  ADD PRIMARY KEY (`coupon_session_id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`currency_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `discussion`
--
ALTER TABLE `discussion`
  ADD PRIMARY KEY (`discussion_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `discussion_account`
--
ALTER TABLE `discussion_account`
  ADD PRIMARY KEY (`discussion_account_id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `discussion_reply`
--
ALTER TABLE `discussion_reply`
  ADD PRIMARY KEY (`discussion_reply_id`) USING BTREE,
  ADD KEY `discussion_id` (`discussion_id`);

--
-- Indexes for table `download`
--
ALTER TABLE `download`
  ADD PRIMARY KEY (`download_id`);

--
-- Indexes for table `download_file`
--
ALTER TABLE `download_file`
  ADD PRIMARY KEY (`download_file_id`),
  ADD KEY `download_id` (`download_id`);

--
-- Indexes for table `download_session`
--
ALTER TABLE `download_session`
  ADD PRIMARY KEY (`download_session_id`),
  ADD KEY `download_id` (`download_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`email_template_id`);

--
-- Indexes for table `forum_participant`
--
ALTER TABLE `forum_participant`
  ADD PRIMARY KEY (`forum_participant_id`),
  ADD KEY `forum_topic_id` (`forum_topic_id`);

--
-- Indexes for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`forum_post_id`),
  ADD KEY `forum_topic_id` (`forum_topic_id`);

--
-- Indexes for table `forum_topic`
--
ALTER TABLE `forum_topic`
  ADD PRIMARY KEY (`forum_topic_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`homework_id`),
  ADD KEY `FK_homework_1` (`lesson_id`),
  ADD KEY `FK_homework_2` (`session_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indexes for table `invoice_transaction`
--
ALTER TABLE `invoice_transaction`
  ADD PRIMARY KEY (`invoice_transaction_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `ip`
--
ALTER TABLE `ip`
  ADD PRIMARY KEY (`ip_id`),
  ADD UNIQUE KEY `idx_ip_ip` (`ip`);

--
-- Indexes for table `lecture`
--
ALTER TABLE `lecture`
  ADD PRIMARY KEY (`lecture_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `lecture_file`
--
ALTER TABLE `lecture_file`
  ADD PRIMARY KEY (`lecture_file_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `lecture_note`
--
ALTER TABLE `lecture_note`
  ADD PRIMARY KEY (`lecture_note_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `lecture_page`
--
ALTER TABLE `lecture_page`
  ADD PRIMARY KEY (`lecture_page_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`lesson_id`) USING BTREE;
ALTER TABLE `lesson` ADD FULLTEXT KEY `lesson_name` (`lesson_name`,`content`);

--
-- Indexes for table `lesson_file`
--
ALTER TABLE `lesson_file`
  ADD PRIMARY KEY (`lesson_file_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `lesson_group`
--
ALTER TABLE `lesson_group`
  ADD PRIMARY KEY (`lesson_group_id`);

--
-- Indexes for table `lesson_to_lesson_group`
--
ALTER TABLE `lesson_to_lesson_group`
  ADD PRIMARY KEY (`lesson_to_lesson_group_id`),
  ADD KEY `lesson_group_id` (`lesson_group_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `newsflash`
--
ALTER TABLE `newsflash`
  ADD PRIMARY KEY (`newsflash_id`);
ALTER TABLE `newsflash` ADD FULLTEXT KEY `title` (`title`,`content`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`password_reset_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `payment_method_currency`
--
ALTER TABLE `payment_method_currency`
  ADD PRIMARY KEY (`payment_method_currency_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `payment_method_field`
--
ALTER TABLE `payment_method_field`
  ADD PRIMARY KEY (`payment_method_field_id`),
  ADD KEY `FK_payment_method_field_1` (`payment_method_id`);

--
-- Indexes for table `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `FK_permission_1` (`permission_group_id`);

--
-- Indexes for table `permission_group`
--
ALTER TABLE `permission_group`
  ADD PRIMARY KEY (`permission_group_id`);

--
-- Indexes for table `phinxlog`
--
ALTER TABLE `phinxlog`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `registration_field`
--
ALTER TABLE `registration_field`
  ADD PRIMARY KEY (`registration_field_id`);

--
-- Indexes for table `related_course`
--
ALTER TABLE `related_course`
  ADD PRIMARY KEY (`related_course_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `related_session_id` (`related_session_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_permission_id`),
  ADD KEY `FK_role_permission_1` (`role_id`),
  ADD KEY `FK_role_permission_2` (`permission_id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`);
ALTER TABLE `session` ADD FULLTEXT KEY `session_name` (`session_name`,`description`,`venue`);

--
-- Indexes for table `session_category`
--
ALTER TABLE `session_category`
  ADD PRIMARY KEY (`session_category_id`);

--
-- Indexes for table `session_instructor`
--
ALTER TABLE `session_instructor`
  ADD PRIMARY KEY (`session_instructor_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `session_lesson`
--
ALTER TABLE `session_lesson`
  ADD PRIMARY KEY (`session_lesson_id`),
  ADD KEY `FK_session_lesson_1` (`session_id`),
  ADD KEY `FK_session_lesson_2` (`lesson_id`);

--
-- Indexes for table `session_lesson_account`
--
ALTER TABLE `session_lesson_account`
  ADD PRIMARY KEY (`session_lesson_account_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `FK_session_lesson_account_2` (`lesson_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `session_storage`
--
ALTER TABLE `session_storage`
  ADD PRIMARY KEY (`id`,`name`);

--
-- Indexes for table `session_survey`
--
ALTER TABLE `session_survey`
  ADD PRIMARY KEY (`session_survey_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `session_test`
--
ALTER TABLE `session_test`
  ADD PRIMARY KEY (`session_test_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `session_to_session_category`
--
ALTER TABLE `session_to_session_category`
  ADD PRIMARY KEY (`session_to_session_category_id`),
  ADD KEY `session_category_id` (`session_category_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `sms_gateway`
--
ALTER TABLE `sms_gateway`
  ADD PRIMARY KEY (`sms_gateway_id`);

--
-- Indexes for table `sms_gateway_field`
--
ALTER TABLE `sms_gateway_field`
  ADD PRIMARY KEY (`sms_gateway_field_id`),
  ADD KEY `sms_gateway_id` (`sms_gateway_id`);

--
-- Indexes for table `sms_template`
--
ALTER TABLE `sms_template`
  ADD PRIMARY KEY (`sms_template_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `UNIQUE` (`email`) USING BTREE;
ALTER TABLE `student` ADD FULLTEXT KEY `first_name` (`first_name`,`last_name`,`email`);

--
-- Indexes for table `student_certificate`
--
ALTER TABLE `student_certificate`
  ADD PRIMARY KEY (`student_certificate_id`),
  ADD UNIQUE KEY `idx_tracking_number` (`tracking_number`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `certificate_id` (`certificate_id`);
ALTER TABLE `student_certificate` ADD FULLTEXT KEY `tracking_number` (`tracking_number`);

--
-- Indexes for table `student_certificate_download`
--
ALTER TABLE `student_certificate_download`
  ADD PRIMARY KEY (`student_certificate_download_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `certificate_id` (`certificate_id`);

--
-- Indexes for table `student_field`
--
ALTER TABLE `student_field`
  ADD PRIMARY KEY (`student_field_id`),
  ADD KEY `FK_student_field_1` (`registration_field_id`),
  ADD KEY `FK_student_field_2` (`student_id`);

--
-- Indexes for table `student_lecture`
--
ALTER TABLE `student_lecture`
  ADD PRIMARY KEY (`student_lecture_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `student_registration`
--
ALTER TABLE `student_registration`
  ADD PRIMARY KEY (`student_registration_id`);

--
-- Indexes for table `student_session`
--
ALTER TABLE `student_session`
  ADD PRIMARY KEY (`student_session_id`),
  ADD KEY `FK_student_session_1` (`student_id`),
  ADD KEY `FK_student_session_2` (`session_id`);

--
-- Indexes for table `student_session_log`
--
ALTER TABLE `student_session_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `student_test`
--
ALTER TABLE `student_test`
  ADD PRIMARY KEY (`student_test_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `student_test_option`
--
ALTER TABLE `student_test_option`
  ADD PRIMARY KEY (`student_test_option_id`),
  ADD KEY `student_test_id` (`student_test_id`),
  ADD KEY `test_option_id` (`test_option_id`);

--
-- Indexes for table `student_video`
--
ALTER TABLE `student_video`
  ADD PRIMARY KEY (`student_video_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `survey`
--
ALTER TABLE `survey`
  ADD PRIMARY KEY (`survey_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `survey_option`
--
ALTER TABLE `survey_option`
  ADD PRIMARY KEY (`survey_option_id`),
  ADD KEY `survey_question_id` (`survey_question_id`);

--
-- Indexes for table `survey_question`
--
ALTER TABLE `survey_question`
  ADD PRIMARY KEY (`survey_question_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `survey_response`
--
ALTER TABLE `survey_response`
  ADD PRIMARY KEY (`survey_response_id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `survey_response_option`
--
ALTER TABLE `survey_response_option`
  ADD PRIMARY KEY (`survey_response_option_id`),
  ADD KEY `survey_response_id` (`survey_response_id`),
  ADD KEY `survey_option_id` (`survey_option_id`);

--
-- Indexes for table `template`
--
ALTER TABLE `template`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `template_option`
--
ALTER TABLE `template_option`
  ADD PRIMARY KEY (`template_option_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `test_grade`
--
ALTER TABLE `test_grade`
  ADD PRIMARY KEY (`test_grade_id`);

--
-- Indexes for table `test_option`
--
ALTER TABLE `test_option`
  ADD PRIMARY KEY (`test_option_id`),
  ADD KEY `test_question_id` (`test_question_id`);

--
-- Indexes for table `test_question`
--
ALTER TABLE `test_question`
  ADD PRIMARY KEY (`test_question_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `FK_transaction_1` (`student_id`),
  ADD KEY `FK_transaction_2` (`payment_method_id`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `account_id` (`account_id`);
ALTER TABLE `video` ADD FULLTEXT KEY `name` (`name`,`description`);

--
-- Indexes for table `widget`
--
ALTER TABLE `widget`
  ADD PRIMARY KEY (`widget_id`);

--
-- Indexes for table `widget_value`
--
ALTER TABLE `widget_value`
  ADD PRIMARY KEY (`widget_value_id`),
  ADD KEY `FK_widget_value_1` (`widget_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment_submission`
--
ALTER TABLE `assignment_submission`
  MODIFY `assignment_submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificate`
--
ALTER TABLE `certificate`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_category`
--
ALTER TABLE `coupon_category`
  MODIFY `coupon_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_invoice`
--
ALTER TABLE `coupon_invoice`
  MODIFY `coupon_invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_session`
--
ALTER TABLE `coupon_session`
  MODIFY `coupon_session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discussion`
--
ALTER TABLE `discussion`
  MODIFY `discussion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_account`
--
ALTER TABLE `discussion_account`
  MODIFY `discussion_account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_reply`
--
ALTER TABLE `discussion_reply`
  MODIFY `discussion_reply_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download`
--
ALTER TABLE `download`
  MODIFY `download_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download_file`
--
ALTER TABLE `download_file`
  MODIFY `download_file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download_session`
--
ALTER TABLE `download_session`
  MODIFY `download_session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `email_template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `forum_participant`
--
ALTER TABLE `forum_participant`
  MODIFY `forum_participant_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_post`
--
ALTER TABLE `forum_post`
  MODIFY `forum_post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_topic`
--
ALTER TABLE `forum_topic`
  MODIFY `forum_topic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `homework_id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_transaction`
--
ALTER TABLE `invoice_transaction`
  MODIFY `invoice_transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ip`
--
ALTER TABLE `ip`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecture`
--
ALTER TABLE `lecture`
  MODIFY `lecture_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecture_file`
--
ALTER TABLE `lecture_file`
  MODIFY `lecture_file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecture_note`
--
ALTER TABLE `lecture_note`
  MODIFY `lecture_note_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lecture_page`
--
ALTER TABLE `lecture_page`
  MODIFY `lecture_page_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `lesson_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_file`
--
ALTER TABLE `lesson_file`
  MODIFY `lesson_file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_group`
--
ALTER TABLE `lesson_group`
  MODIFY `lesson_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lesson_to_lesson_group`
--
ALTER TABLE `lesson_to_lesson_group`
  MODIFY `lesson_to_lesson_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsflash`
--
ALTER TABLE `newsflash`
  MODIFY `newsflash_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `password_reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_method_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment_method_currency`
--
ALTER TABLE `payment_method_currency`
  MODIFY `payment_method_currency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method_field`
--
ALTER TABLE `payment_method_field`
  MODIFY `payment_method_field_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `permission`
--
ALTER TABLE `permission`
  MODIFY `permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `permission_group`
--
ALTER TABLE `permission_group`
  MODIFY `permission_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `registration_field`
--
ALTER TABLE `registration_field`
  MODIFY `registration_field_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `related_course`
--
ALTER TABLE `related_course`
  MODIFY `related_course_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `role_permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=827;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_category`
--
ALTER TABLE `session_category`
  MODIFY `session_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_instructor`
--
ALTER TABLE `session_instructor`
  MODIFY `session_instructor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_lesson`
--
ALTER TABLE `session_lesson`
  MODIFY `session_lesson_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_lesson_account`
--
ALTER TABLE `session_lesson_account`
  MODIFY `session_lesson_account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_survey`
--
ALTER TABLE `session_survey`
  MODIFY `session_survey_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_test`
--
ALTER TABLE `session_test`
  MODIFY `session_test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session_to_session_category`
--
ALTER TABLE `session_to_session_category`
  MODIFY `session_to_session_category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `sms_gateway`
--
ALTER TABLE `sms_gateway`
  MODIFY `sms_gateway_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sms_gateway_field`
--
ALTER TABLE `sms_gateway_field`
  MODIFY `sms_gateway_field_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sms_template`
--
ALTER TABLE `sms_template`
  MODIFY `sms_template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_certificate`
--
ALTER TABLE `student_certificate`
  MODIFY `student_certificate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_certificate_download`
--
ALTER TABLE `student_certificate_download`
  MODIFY `student_certificate_download_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_field`
--
ALTER TABLE `student_field`
  MODIFY `student_field_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_lecture`
--
ALTER TABLE `student_lecture`
  MODIFY `student_lecture_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_registration`
--
ALTER TABLE `student_registration`
  MODIFY `student_registration_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_session`
--
ALTER TABLE `student_session`
  MODIFY `student_session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_session_log`
--
ALTER TABLE `student_session_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_test`
--
ALTER TABLE `student_test`
  MODIFY `student_test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_test_option`
--
ALTER TABLE `student_test_option`
  MODIFY `student_test_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_video`
--
ALTER TABLE `student_video`
  MODIFY `student_video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey`
--
ALTER TABLE `survey`
  MODIFY `survey_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_option`
--
ALTER TABLE `survey_option`
  MODIFY `survey_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_question`
--
ALTER TABLE `survey_question`
  MODIFY `survey_question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_response`
--
ALTER TABLE `survey_response`
  MODIFY `survey_response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_response_option`
--
ALTER TABLE `survey_response_option`
  MODIFY `survey_response_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template`
--
ALTER TABLE `template`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `template_option`
--
ALTER TABLE `template_option`
  MODIFY `template_option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_grade`
--
ALTER TABLE `test_grade`
  MODIFY `test_grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `test_option`
--
ALTER TABLE `test_option`
  MODIFY `test_option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_question`
--
ALTER TABLE `test_question`
  MODIFY `test_question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `widget`
--
ALTER TABLE `widget`
  MODIFY `widget_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `widget_value`
--
ALTER TABLE `widget_value`
  MODIFY `widget_value_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `FK_accounts_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Constraints for table `assignment`
--
ALTER TABLE `assignment`
  ADD CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `assignment_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `assignment_submission`
--
ALTER TABLE `assignment_submission`
  ADD CONSTRAINT `assignment_submission_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `assignment_submission_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `FK_attendance_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `FK_attendance_3` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON UPDATE CASCADE;

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `bookmark_ibfk_3` FOREIGN KEY (`lecture_page_id`) REFERENCES `lecture_page` (`lecture_page_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `certificate`
--
ALTER TABLE `certificate`
  ADD CONSTRAINT `certificate_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `certificate_assignment`
--
ALTER TABLE `certificate_assignment`
  ADD CONSTRAINT `certificate_assignment_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`certificate_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `certificate_assignment_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `certificate_lesson`
--
ALTER TABLE `certificate_lesson`
  ADD CONSTRAINT `certificate_lesson_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`certificate_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `certificate_lesson_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `certificate_test`
--
ALTER TABLE `certificate_test`
  ADD CONSTRAINT `certificate_test_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`certificate_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `certificate_test_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `coupon_category`
--
ALTER TABLE `coupon_category`
  ADD CONSTRAINT `coupon_category_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `coupon_category_ibfk_2` FOREIGN KEY (`session_category_id`) REFERENCES `session_category` (`session_category_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `coupon_invoice`
--
ALTER TABLE `coupon_invoice`
  ADD CONSTRAINT `coupon_invoice_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `coupon_invoice_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `coupon_session`
--
ALTER TABLE `coupon_session`
  ADD CONSTRAINT `coupon_session_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `coupon_session_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `currency`
--
ALTER TABLE `currency`
  ADD CONSTRAINT `currency_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `discussion`
--
ALTER TABLE `discussion`
  ADD CONSTRAINT `discussion_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `discussion_account`
--
ALTER TABLE `discussion_account`
  ADD CONSTRAINT `discussion_account_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussion` (`discussion_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `discussion_account_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `discussion_reply`
--
ALTER TABLE `discussion_reply`
  ADD CONSTRAINT `discussion_reply_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussion` (`discussion_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `download_file`
--
ALTER TABLE `download_file`
  ADD CONSTRAINT `download_file_ibfk_1` FOREIGN KEY (`download_id`) REFERENCES `download` (`download_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `download_session`
--
ALTER TABLE `download_session`
  ADD CONSTRAINT `download_session_ibfk_1` FOREIGN KEY (`download_id`) REFERENCES `download` (`download_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `download_session_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `forum_participant`
--
ALTER TABLE `forum_participant`
  ADD CONSTRAINT `forum_participant_ibfk_1` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topic` (`forum_topic_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `forum_post`
--
ALTER TABLE `forum_post`
  ADD CONSTRAINT `forum_post_ibfk_1` FOREIGN KEY (`forum_topic_id`) REFERENCES `forum_topic` (`forum_topic_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `forum_topic`
--
ALTER TABLE `forum_topic`
  ADD CONSTRAINT `forum_topic_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `homework`
--
ALTER TABLE `homework`
  ADD CONSTRAINT `FK_homework_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`),
  ADD CONSTRAINT `FK_homework_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`);

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_transaction`
--
ALTER TABLE `invoice_transaction`
  ADD CONSTRAINT `invoice_transaction_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lecture`
--
ALTER TABLE `lecture`
  ADD CONSTRAINT `lecture_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lecture_file`
--
ALTER TABLE `lecture_file`
  ADD CONSTRAINT `lecture_file_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lecture_note`
--
ALTER TABLE `lecture_note`
  ADD CONSTRAINT `lecture_note_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `lecture_note_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lecture_page`
--
ALTER TABLE `lecture_page`
  ADD CONSTRAINT `lecture_page_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lesson_file`
--
ALTER TABLE `lesson_file`
  ADD CONSTRAINT `lesson_file_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `lesson_to_lesson_group`
--
ALTER TABLE `lesson_to_lesson_group`
  ADD CONSTRAINT `lesson_to_lesson_group_ibfk_1` FOREIGN KEY (`lesson_group_id`) REFERENCES `lesson_group` (`lesson_group_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `lesson_to_lesson_group_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `payment_method_currency`
--
ALTER TABLE `payment_method_currency`
  ADD CONSTRAINT `payment_method_currency_ibfk_1` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `payment_method_currency_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `payment_method_field`
--
ALTER TABLE `payment_method_field`
  ADD CONSTRAINT `FK_payment_method_field_1` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permission`
--
ALTER TABLE `permission`
  ADD CONSTRAINT `FK_permission_1` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`permission_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `related_course`
--
ALTER TABLE `related_course`
  ADD CONSTRAINT `related_course_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `related_course_ibfk_2` FOREIGN KEY (`related_session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `FK_role_permission_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_role_permission_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_instructor`
--
ALTER TABLE `session_instructor`
  ADD CONSTRAINT `session_instructor_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `session_instructor_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `session_lesson`
--
ALTER TABLE `session_lesson`
  ADD CONSTRAINT `FK_session_lesson_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_session_lesson_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_lesson_account`
--
ALTER TABLE `session_lesson_account`
  ADD CONSTRAINT `FK_session_lesson_account_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_lesson_account_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `session_lesson_account_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `session_survey`
--
ALTER TABLE `session_survey`
  ADD CONSTRAINT `session_survey_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `session_survey_ibfk_2` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `session_test`
--
ALTER TABLE `session_test`
  ADD CONSTRAINT `session_test_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `session_test_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `session_to_session_category`
--
ALTER TABLE `session_to_session_category`
  ADD CONSTRAINT `session_to_session_category_ibfk_1` FOREIGN KEY (`session_category_id`) REFERENCES `session_category` (`session_category_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `session_to_session_category_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `sms_gateway_field`
--
ALTER TABLE `sms_gateway_field`
  ADD CONSTRAINT `sms_gateway_field_ibfk_1` FOREIGN KEY (`sms_gateway_id`) REFERENCES `sms_gateway` (`sms_gateway_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_certificate`
--
ALTER TABLE `student_certificate`
  ADD CONSTRAINT `student_certificate_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_certificate_ibfk_2` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`certificate_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_certificate_download`
--
ALTER TABLE `student_certificate_download`
  ADD CONSTRAINT `student_certificate_download_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_certificate_download_ibfk_2` FOREIGN KEY (`certificate_id`) REFERENCES `certificate` (`certificate_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_field`
--
ALTER TABLE `student_field`
  ADD CONSTRAINT `FK_student_field_1` FOREIGN KEY (`registration_field_id`) REFERENCES `registration_field` (`registration_field_id`),
  ADD CONSTRAINT `FK_student_field_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_lecture`
--
ALTER TABLE `student_lecture`
  ADD CONSTRAINT `student_lecture_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_lecture_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_lecture_ibfk_3` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_session`
--
ALTER TABLE `student_session`
  ADD CONSTRAINT `FK_student_session_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_student_session_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_session_log`
--
ALTER TABLE `student_session_log`
  ADD CONSTRAINT `student_session_log_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_session_log_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_session_log_ibfk_3` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_test`
--
ALTER TABLE `student_test`
  ADD CONSTRAINT `student_test_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_test_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_test_option`
--
ALTER TABLE `student_test_option`
  ADD CONSTRAINT `student_test_option_ibfk_1` FOREIGN KEY (`student_test_id`) REFERENCES `student_test` (`student_test_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_test_option_ibfk_2` FOREIGN KEY (`test_option_id`) REFERENCES `test_option` (`test_option_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student_video`
--
ALTER TABLE `student_video`
  ADD CONSTRAINT `student_video_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_video_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `video` (`video_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `survey`
--
ALTER TABLE `survey`
  ADD CONSTRAINT `survey_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `survey_option`
--
ALTER TABLE `survey_option`
  ADD CONSTRAINT `survey_option_ibfk_1` FOREIGN KEY (`survey_question_id`) REFERENCES `survey_question` (`survey_question_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `survey_question`
--
ALTER TABLE `survey_question`
  ADD CONSTRAINT `survey_question_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `survey_response`
--
ALTER TABLE `survey_response`
  ADD CONSTRAINT `survey_response_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `survey_response_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `survey_response_option`
--
ALTER TABLE `survey_response_option`
  ADD CONSTRAINT `survey_response_option_ibfk_1` FOREIGN KEY (`survey_response_id`) REFERENCES `survey_response` (`survey_response_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `survey_response_option_ibfk_2` FOREIGN KEY (`survey_option_id`) REFERENCES `survey_option` (`survey_option_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `template_option`
--
ALTER TABLE `template_option`
  ADD CONSTRAINT `template_option_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `template` (`template_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `test_option`
--
ALTER TABLE `test_option`
  ADD CONSTRAINT `test_option_ibfk_1` FOREIGN KEY (`test_question_id`) REFERENCES `test_question` (`test_question_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `test_question`
--
ALTER TABLE `test_question`
  ADD CONSTRAINT `test_question_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `FK_transaction_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_transaction_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `video_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE NO ACTION;

--
-- Constraints for table `widget_value`
--
ALTER TABLE `widget_value`
  ADD CONSTRAINT `FK_widget_value_1` FOREIGN KEY (`widget_id`) REFERENCES `widget` (`widget_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
