-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2023 at 11:24 PM
-- Server version: 8.0.33
-- PHP Version: 8.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartend10`
--

-- --------------------------------------------------------

--
-- Table structure for table `smartend_analytics_pages`
--

CREATE TABLE `smartend_analytics_pages` (
  `id` bigint UNSIGNED NOT NULL,
  `visitor_id` int NOT NULL,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `query` text COLLATE utf8mb4_unicode_ci,
  `load_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_analytics_pages`
--

INSERT INTO `smartend_analytics_pages` (`id`, `visitor_id`, `ip`, `title`, `name`, `query`, `load_time`, `date`, `time`, `created_at`, `updated_at`) VALUES
(1, 1, '127.0.0.1', 'Dashboard &raquo; Site Title', 'unknown', 'http://smartend10.test/admin', '0.10358286', '2023-06-30', '23:24:39', '2023-06-30 20:24:39', '2023-06-30 20:24:39');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_analytics_visitors`
--

CREATE TABLE `smartend_analytics_visitors` (
  `id` bigint UNSIGNED NOT NULL,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_cor1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_cor2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolution` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referrer` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hostname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_analytics_visitors`
--

INSERT INTO `smartend_analytics_visitors` (`id`, `ip`, `city`, `country_code`, `country`, `region`, `full_address`, `location_cor1`, `location_cor2`, `os`, `browser`, `resolution`, `referrer`, `hostname`, `org`, `date`, `time`, `created_at`, `updated_at`) VALUES
(1, '127.0.0.1', 'unknown', 'US', 'unknown', 'Connecticut', NULL, '41.31', '-72.92', 'Mac OS X', 'Chrome', 'unknown', 'http://smartend10.test/admin', 'NA', 'America/New_York', '2023-06-30', '23:24:39', '2023-06-30 20:24:39', '2023-06-30 20:24:39');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_attach_files`
--

CREATE TABLE `smartend_attach_files` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_banners`
--

CREATE TABLE `smartend_banners` (
  `id` bigint UNSIGNED NOT NULL,
  `section_id` int NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details_ar` text COLLATE utf8mb4_unicode_ci,
  `details_en` text COLLATE utf8mb4_unicode_ci,
  `details_ch` text COLLATE utf8mb4_unicode_ci,
  `details_hi` text COLLATE utf8mb4_unicode_ci,
  `details_es` text COLLATE utf8mb4_unicode_ci,
  `details_ru` text COLLATE utf8mb4_unicode_ci,
  `details_pt` text COLLATE utf8mb4_unicode_ci,
  `details_fr` text COLLATE utf8mb4_unicode_ci,
  `details_de` text COLLATE utf8mb4_unicode_ci,
  `details_th` text COLLATE utf8mb4_unicode_ci,
  `details_br` text COLLATE utf8mb4_unicode_ci,
  `code` text COLLATE utf8mb4_unicode_ci,
  `file_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_type` tinyint DEFAULT NULL,
  `youtube_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `visits` int NOT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_banners`
--

INSERT INTO `smartend_banners` (`id`, `section_id`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `details_ar`, `details_en`, `details_ch`, `details_hi`, `details_es`, `details_ru`, `details_pt`, `details_fr`, `details_de`, `details_th`, `details_br`, `code`, `file_ar`, `file_en`, `file_ch`, `file_hi`, `file_es`, `file_ru`, `file_pt`, `file_fr`, `file_de`, `file_th`, `file_br`, `video_type`, `youtube_link`, `link_url`, `icon`, `status`, `visits`, `row_no`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'بنر رقم ١', 'Banner #1', '横幅 #1', 'बैनर #1', 'Bandera #1', 'Баннер #1', 'Bandeira #1', 'Bannière #1', 'Banner #1', 'แบนเนอร์ #1', 'Estandarte nº 1', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', NULL, NULL, '#', NULL, 1, 0, 1, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(2, 1, 'بنر رقم ٢', 'Banner #2', '横幅 #2', 'बैनर #2', 'Bandera #2', 'Баннер #2', 'Bandeira #2', 'Bannière #2', 'Banner #2', 'แบนเนอร์ #2', 'Estandarte #2', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', 'noimg.png', NULL, NULL, '#', NULL, 1, 0, 2, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(3, 2, 'تصميم ريسبونسف', 'Responsive Design', '响应式设计', 'प्रभावी डिजाइन', 'Diseño de respuesta', 'Адаптивный дизайн', 'Design Responsivo', 'onception réactive', 'Reagerend ontwerp', 'การออกแบบที่ตอบสนอง', 'Responsive Design', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '#', 'fa-object-group', 1, 0, 1, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(4, 2, ' احدث التقنيات', 'HTML5 & CSS3', 'HTML5 和 CSS3', 'HTML5 और CSS3', 'HTML5 y CSS3', 'HTML5 и CSS3', 'HTML5 & CSS3', 'HTML5 et CSS3', 'HTML5 & CSS3', 'HTML5 & CSS3', 'HTML5 e CSS3', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '#', 'fa-html5', 1, 0, 2, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(5, 2, 'باستخدام بوتستراب', 'Bootstrap Used', '使用的引导程序', 'बूटस्ट्रैप प्रयुक्त', 'Bootstrap utilizado', 'Bootstrap', 'Bootstrap usado', 'Bootstrap utilisé', 'Bootstrap gebruikt', 'Bootstrap', 'Bootstrap', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '#', 'fa-code', 1, 0, 3, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(6, 2, 'تصميم كلاسيكي', 'Classic Design', '经典设计', 'क्लासिक डिजाइन', 'Diseño clásico', 'Классический', 'Design Clássico', 'Conception classique', 'Klassiek ontwerp', 'ดีไซน์คลาสสิก', 'Classic Design', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '#', 'fa-laptop', 1, 0, 4, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_comments`
--

CREATE TABLE `smartend_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_contacts`
--

CREATE TABLE `smartend_contacts` (
  `id` bigint UNSIGNED NOT NULL,
  `group_id` int DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_contacts_groups`
--

CREATE TABLE `smartend_contacts_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_contacts_groups`
--

INSERT INTO `smartend_contacts_groups` (`id`, `name`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Newsletter Emails', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_countries`
--

CREATE TABLE `smartend_countries` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tel` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_countries`
--

INSERT INTO `smartend_countries` (`id`, `code`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `tel`, `created_at`, `updated_at`) VALUES
(1, 'AL', 'ألبانيا', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', 'Albania', '355', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(2, 'DZ', 'الجزائر', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', 'Algeria', '213', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(3, 'AS', 'ساموا الأمريكية', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', 'American Samoa', '1-684', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(4, 'AD', 'أندورا', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', 'Andorra', '376', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(5, 'AO', 'أنغولا', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', 'Angola', '244', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(6, 'AI', 'أنغيلا', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', 'Anguilla', '1-264', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(7, 'AR', 'الأرجنتين', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', 'Argentina', '54', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(8, 'AM', 'أرمينيا', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', 'Armenia', '374', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(9, 'AW', 'أروبا', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', 'Aruba', '297', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(10, 'AU', 'أستراليا', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', 'Australia', '61', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(11, 'AT', 'النمسا', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', 'Austria', '43', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(12, 'AZ', 'أذربيجان', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', 'Azerbaijan', '994', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(13, 'BS', 'جزر البهاما', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', 'Bahamas', '1-242', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(14, 'BH', 'البحرين', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', 'Bahrain', '973', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(15, 'BD', 'بنغلاديش', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', 'Bangladesh', '880', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(16, 'BB', 'بربادوس', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', 'Barbados', '1-246', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(17, 'BY', 'روسيا البيضاء', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', 'Belarus', '375', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(18, 'BE', 'بلجيكا', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', 'Belgium', '32', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(19, 'BZ', 'بليز', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', 'Belize', '501', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(20, 'BJ', 'بنين', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', 'Benin', '229', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(21, 'BM', 'برمودا', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', 'Bermuda', '1-441', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(22, 'BT', 'بوتان', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', 'Bhutan', '975', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(23, 'BO', 'بوليفيا', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', 'Bolivia', '591', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(24, 'BA', 'البوسنة والهرسك', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', '387', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(25, 'BW', 'بوتسوانا', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', 'Botswana', '267', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(26, 'BR', 'البرازيل', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', 'Brazil', '55', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(27, 'VG', 'جزر فيرجن البريطانية', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', 'British Virgin Islands', '1-284', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(28, 'IO', 'إقليم المحيط الهندي البريطاني', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'British Indian Ocean Territory', '246', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(29, 'BN', 'بروناي دار السلام', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', 'Brunei Darussalam', '673', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(30, 'BG', 'بلغاريا', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', 'Bulgaria', '359', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(31, 'BF', 'بوركينا فاسو', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', 'Burkina Faso', '226', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(32, 'BI', 'بوروندي', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', 'Burundi', '257', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(33, 'KH', 'كمبوديا', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', 'Cambodia', '855', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(34, 'CM', 'الكاميرون', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', 'Cameroon', '237', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(35, 'CA', 'كندا', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', 'Canada', '1', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(36, 'CV', 'الرأس الأخضر', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', 'Cape Verde', '238', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(37, 'KY', 'جزر كايمان', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', 'Cayman Islands', '1-345', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(38, 'CF', 'افريقيا الوسطى', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', 'Central African Republic', '236', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(39, 'TD', 'تشاد', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', 'Chad', '235', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(40, 'CL', 'تشيلي', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', 'Chile', '56', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(41, 'CN', 'الصين', 'China', 'China', 'China', 'China', 'China', 'China', 'China', 'China', 'China', 'China', '86', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(42, 'HK', 'هونغ كونغ', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', 'Hong Kong', '852', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(43, 'MO', 'ماكاو', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', 'Macao', '853', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(44, 'CX', 'جزيرة الكريسماس', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', 'Christmas Island', '61', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(45, 'CC', 'جزر كوكوس (كيلينغ)', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', '61', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(46, 'CO', 'كولومبيا', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', 'Colombia', '57', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(47, 'KM', 'جزر القمر', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', 'Comoros', '269', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(48, 'CK', 'جزر كوك', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', 'Cook Islands', '682', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(49, 'CR', 'كوستا ريكا', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', 'Costa Rica', '506', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(50, 'HR', 'كرواتيا', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', 'Croatia', '385', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(51, 'CU', 'كوبا', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', 'Cuba', '53', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(52, 'CY', 'قبرص', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', 'Cyprus', '357', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(53, 'CZ', 'الجمهورية التشيكية', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', 'Czech Republic', '420', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(54, 'DK', 'الدنمارك', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', 'Denmark', '45', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(55, 'DJ', 'جيبوتي', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', 'Djibouti', '253', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(56, 'DM', 'دومينيكا', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', 'Dominica', '1-767', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(57, 'DO', 'جمهورية الدومينيكان', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', 'Dominican Republic', '1-809', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(58, 'EC', 'الاكوادور', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', 'Ecuador', '593', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(59, 'EG', 'مصر', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', 'Egypt', '20', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(60, 'SV', 'السلفادور', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', 'El Salvador', '503', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(61, 'GQ', 'غينيا الاستوائية', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', 'Equatorial Guinea', '240', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(62, 'ER', 'إريتريا', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', 'Eritrea', '291', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(63, 'EE', 'استونيا', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', 'Estonia', '372', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(64, 'ET', 'أثيوبيا', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', 'Ethiopia', '251', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(65, 'FO', 'جزر فارو', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', 'Faroe Islands', '298', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(66, 'FJ', 'فيجي', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', 'Fiji', '679', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(67, 'FI', 'فنلندا', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', 'Finland', '358', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(68, 'FR', 'فرنسا', 'France', 'France', 'France', 'France', 'France', 'France', 'France', 'France', 'France', 'France', '33', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(69, 'GF', 'جيانا الفرنسية', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', 'French Guiana', '689', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(70, 'GA', 'الغابون', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', 'Gabon', '241', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(71, 'GM', 'غامبيا', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', 'Gambia', '220', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(72, 'GE', 'جورجيا', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', 'Georgia', '995', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(73, 'DE', 'ألمانيا', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', 'Germany', '49', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(74, 'GH', 'غانا', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', 'Ghana', '233', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(75, 'GI', 'جبل طارق', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', 'Gibraltar', '350', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(76, 'GR', 'يونان', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', 'Greece', '30', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(77, 'GL', 'غرينلاند', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', 'Greenland', '299', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(78, 'GD', 'غرينادا', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', 'Grenada', '1-473', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(79, 'GU', 'غوام', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', 'Guam', '1-671', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(80, 'GT', 'غواتيمالا', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', 'Guatemala', '502', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(81, 'GN', 'غينيا', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', 'Guinea', '224', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(82, 'GW', 'غينيا-بيساو', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', 'Guinea-Bissau', '245', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(83, 'GY', 'غيانا', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', 'Guyana', '592', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(84, 'HT', 'هايتي', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', 'Haiti', '509', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(85, 'HN', 'هندوراس', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', 'Honduras', '504', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(86, 'HU', 'هنغاريا', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', 'Hungary', '36', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(87, 'IS', 'أيسلندا', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', 'Iceland', '354', '2023-06-30 20:24:34', '2023-06-30 20:24:35'),
(88, 'IN', 'الهند', 'India', 'India', 'India', 'India', 'India', 'India', 'India', 'India', 'India', 'India', '91', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(89, 'ID', 'أندونيسيا', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', 'Indonesia', '62', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(90, 'IR', 'جمهورية إيران الإسلامية', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', 'Iran, Islamic Republic of', '98', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(91, 'IQ', 'العراق', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', 'Iraq', '964', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(92, 'IE', 'أيرلندا', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', 'Ireland', '353', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(93, 'IM', 'جزيرة مان', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', 'Isle of Man', '44-1624', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(94, 'IL', 'إسرائيل', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', 'Israel', '972', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(95, 'IT', 'إيطاليا', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', 'Italy', '39', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(96, 'JM', 'جامايكا', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', 'Jamaica', '1-876', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(97, 'JP', 'اليابان', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', 'Japan', '81', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(98, 'JE', 'جيرسي', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', 'Jersey', '44-1534', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(99, 'JO', 'الأردن', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', 'Jordan', '962', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(100, 'KZ', 'كازاخستان', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', 'Kazakhstan', '7', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(101, 'KE', 'كينيا', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', 'Kenya', '254', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(102, 'KI', 'كيريباس', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', 'Kiribati', '686', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(103, 'KW', 'الكويت', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', 'Kuwait', '965', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(104, 'KG', 'قيرغيزستان', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', 'Kyrgyzstan', '996', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(105, 'LV', 'لاتفيا', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', 'Latvia', '371', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(106, 'LB', 'لبنان', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', 'Lebanon', '961', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(107, 'LS', 'ليسوتو', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', 'Lesotho', '266', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(108, 'LR', 'ليبيريا', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', 'Liberia', '231', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(109, 'LY', 'ليبيا', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', 'Libya', '218', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(110, 'LI', 'ليشتنشتاين', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', 'Liechtenstein', '423', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(111, 'LT', 'ليتوانيا', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', 'Lithuania', '370', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(112, 'LU', 'لوكسمبورغ', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', 'Luxembourg', '352', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(113, 'MK', 'مقدونيا، جمهورية', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', 'Macedonia', '389', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(114, 'MG', 'مدغشقر', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', 'Madagascar', '261', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(115, 'MW', 'ملاوي', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', 'Malawi', '265', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(116, 'MY', 'ماليزيا', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', 'Malaysia', '60', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(117, 'MV', 'جزر المالديف', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', 'Maldives', '960', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(118, 'ML', 'مالي', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', 'Mali', '223', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(119, 'MT', 'مالطا', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', 'Malta', '356', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(120, 'MH', 'جزر مارشال', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', 'Marshall Islands', '692', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(121, 'MR', 'موريتانيا', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', 'Mauritania', '222', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(122, 'MU', 'موريشيوس', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', 'Mauritius', '230', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(123, 'YT', 'مايوت', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', 'Mayotte', '262', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(124, 'MX', 'المكسيك', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', 'Mexico', '52', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(125, 'FM', 'ولايات ميكرونيزيا الموحدة', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', 'Micronesia', '691', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(126, 'MD', 'مولدوفا', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', 'Moldova', '373', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(127, 'MC', 'موناكو', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', 'Monaco', '377', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(128, 'MN', 'منغوليا', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', 'Mongolia', '976', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(129, 'ME', 'الجبل الأسود', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', 'Montenegro', '382', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(130, 'MS', 'مونتسيرات', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', 'Montserrat', '1-664', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(131, 'MA', 'المغرب', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', 'Morocco', '212', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(132, 'MZ', 'موزمبيق', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', 'Mozambique', '258', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(133, 'MM', 'ميانمار', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', 'Myanmar', '95', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(134, 'NA', 'ناميبيا', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', 'Namibia', '264', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(135, 'NR', 'ناورو', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', 'Nauru', '674', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(136, 'NP', 'نيبال', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', 'Nepal', '977', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(137, 'NL', 'هولندا', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', 'Netherlands', '31', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(138, 'AN', 'جزر الأنتيل الهولندية', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', 'Netherlands Antilles', '599', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(139, 'NC', 'كاليدونيا الجديدة', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', 'New Caledonia', '687', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(140, 'NZ', 'نيوزيلندا', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', 'New Zealand', '64', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(141, 'NI', 'نيكاراغوا', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', 'Nicaragua', '505', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(142, 'NE', 'النيجر', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', 'Niger', '227', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(143, 'NG', 'نيجيريا', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', 'Nigeria', '234', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(144, 'NU', 'نيوي', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', 'Niue', '683', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(145, 'NO', 'النرويج', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', 'Norway', '47', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(146, 'OM', 'عمان', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', 'Oman', '968', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(147, 'PK', 'باكستان', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', 'Pakistan', '92', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(148, 'PW', 'بالاو', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', 'Palau', '680', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(149, 'PS', 'فلسطين', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', 'Palestinian', '972', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(150, 'PA', 'بناما', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', 'Panama', '507', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(151, 'PY', 'باراغواي', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', 'Paraguay', '595', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(152, 'PE', 'بيرو', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', 'Peru', '51', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(153, 'PH', 'الفلبين', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', 'Philippines', '63', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(154, 'PN', 'بيتكيرن', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', 'Pitcairn', '870', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(155, 'PL', 'بولندا', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', 'Poland', '48', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(156, 'PT', 'البرتغال', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', 'Portugal', '351', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(157, 'PR', 'بويرتو ريكو', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', 'Puerto Rico', '1-787', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(158, 'QA', 'قطر', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', 'Qatar', '974', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(159, 'RO', 'رومانيا', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', 'Romania', '40', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(160, 'RU', 'الفيدرالية الروسية', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', 'Russian Federation', '7', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(161, 'RW', 'رواندا', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', 'Rwanda', '250', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(162, 'SH', 'سانت هيلينا', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', 'Saint Helena', '290', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(163, 'KN', 'سانت كيتس ونيفيس', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', 'Saint Kitts and Nevis', '1-869', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(164, 'LC', 'سانت لوسيا', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', 'Saint Lucia', '1-758', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(165, 'PM', 'سان بيار وميكلون', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', '508', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(166, 'VC', 'سانت فنسنت وجزر غرينادين', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', 'Saint Vincent and Grenadines', '1-784', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(167, 'WS', 'ساموا', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', 'Samoa', '685', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(168, 'SM', 'سان مارينو', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', 'San Marino', '378', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(169, 'ST', 'ساو تومي وبرينسيبي', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', 'Sao Tome and Principe', '239', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(170, 'SA', 'المملكة العربية السعودية', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', 'Saudi Arabia', '966', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(171, 'SN', 'السنغال', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', 'Senegal', '221', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(172, 'RS', 'صربيا', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', 'Serbia', '381', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(173, 'SC', 'سيشيل', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', 'Seychelles', '248', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(174, 'SL', 'سيرا ليون', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', 'Sierra Leone', '232', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(175, 'SG', 'سنغافورة', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', 'Singapore', '65', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(176, 'SK', 'سلوفاكيا', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', 'Slovakia', '421', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(177, 'SI', 'سلوفينيا', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', 'Slovenia', '386', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(178, 'SB', 'جزر سليمان', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', 'Solomon Islands', '677', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(179, 'SO', 'الصومال', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', 'Somalia', '252', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(180, 'ZA', 'جنوب أفريقيا', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', 'South Africa', '27', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(181, 'ES', 'إسبانيا', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', 'Spain', '34', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(182, 'LK', 'سيريلانكا', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', 'Sri Lanka', '94', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(183, 'SD', 'السودان', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', 'Sudan', '249', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(184, 'SR', 'سورينام', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', 'Suriname', '597', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(185, 'SJ', 'جزر سفالبارد وجان ماين', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', 'Svalbard and Jan Mayen Islands', '47', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(186, 'SZ', 'سوازيلاند', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', 'Swaziland', '268', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(187, 'SE', 'السويد', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', 'Sweden', '46', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(188, 'CH', 'سويسرا', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', 'Switzerland', '41', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(189, 'SY', 'سوريا', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', 'Syrian Arab Republic', '963', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(190, 'TW', 'تايوان، جمهورية الصين', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', 'Taiwan, Republic of China', '886', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(191, 'TJ', 'طاجيكستان', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', 'Tajikistan', '992', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(192, 'TZ', 'تنزانيا', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', 'Tanzania', '255', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(193, 'TH', 'تايلاند', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', 'Thailand', '66', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(194, 'TG', 'توغو', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', 'Togo', '228', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(195, 'TK', 'توكيلاو', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', 'Tokelau', '690', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(196, 'TO', 'تونغا', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', 'Tonga', '676', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(197, 'TT', 'ترينداد وتوباغو', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', 'Trinidad and Tobago', '1-868', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(198, 'TN', 'تونس', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', 'Tunisia', '216', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(199, 'TR', 'تركيا', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', 'Turkey', '90', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(200, 'TM', 'تركمانستان', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', 'Turkmenistan', '993', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(201, 'TC', 'جزر تركس وكايكوس', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'Turks and Caicos Islands', '1-649', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(202, 'TV', 'توفالو', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', 'Tuvalu', '688', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(203, 'UG', 'أوغندا', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', 'Uganda', '256', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(204, 'UA', 'أوكرانيا', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', 'Ukraine', '380', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(205, 'AE', 'الإمارات العربية المتحدة', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', 'United Arab Emirates', '971', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(206, 'GB', 'المملكة المتحدة', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', 'United Kingdom', '44', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(207, 'US', 'الولايات المتحدة الأمريكية', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', 'United States of America', '1', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(208, 'UY', 'أوروغواي', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', 'Uruguay', '598', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(209, 'UZ', 'أوزبكستان', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', 'Uzbekistan', '998', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(210, 'VU', 'فانواتو', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', 'Vanuatu', '678', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(211, 'VE', 'فنزويلا', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', 'Venezuela', '58', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(212, 'VN', 'فيتنام', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', 'Viet Nam', '84', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(213, 'WF', 'واليس وفوتونا', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', 'Wallis and Futuna Islands', '681', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(214, 'YE', 'اليمن', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', 'Yemen', '967', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(215, 'ZM', 'زامبيا', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', 'Zambia', '260', '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(216, 'ZW', 'زيمبابوي', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', 'Zimbabwe', '263', '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_events`
--

CREATE TABLE `smartend_events` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL,
  `type` tinyint NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_failed_jobs`
--

CREATE TABLE `smartend_failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_languages`
--

CREATE TABLE `smartend_languages` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `left` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `right` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `box_status` tinyint DEFAULT '1',
  `status` tinyint DEFAULT '1',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_languages`
--

INSERT INTO `smartend_languages` (`id`, `title`, `code`, `direction`, `left`, `right`, `icon`, `box_status`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 'ltr', 'left', 'right', 'us', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(2, 'العربية', 'ar', 'rtl', 'right', 'left', 'sa', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(3, '中文語言', 'ch', 'ltr', 'right', 'left', 'cn', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(4, 'हिंदी भाषा', 'hi', 'ltr', 'right', 'left', 'in', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(5, 'हespañol', 'es', 'ltr', 'right', 'left', 'es', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(6, 'русский', 'ru', 'ltr', 'right', 'left', 'ru', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(7, 'Português', 'pt', 'ltr', 'right', 'left', 'pt', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(8, 'Le français', 'fr', 'ltr', 'right', 'left', 'fr', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(9, 'Deutsch', 'de', 'ltr', 'right', 'left', 'de', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(10, 'ภาษาไทย', 'th', 'ltr', 'right', 'left', 'th', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(11, 'Português', 'br', 'ltr', 'right', 'left', 'br', 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_maps`
--

CREATE TABLE `smartend_maps` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details_ar` text COLLATE utf8mb4_unicode_ci,
  `details_en` text COLLATE utf8mb4_unicode_ci,
  `details_ch` text COLLATE utf8mb4_unicode_ci,
  `details_hi` text COLLATE utf8mb4_unicode_ci,
  `details_es` text COLLATE utf8mb4_unicode_ci,
  `details_ru` text COLLATE utf8mb4_unicode_ci,
  `details_pt` text COLLATE utf8mb4_unicode_ci,
  `details_fr` text COLLATE utf8mb4_unicode_ci,
  `details_de` text COLLATE utf8mb4_unicode_ci,
  `details_th` text COLLATE utf8mb4_unicode_ci,
  `details_br` text COLLATE utf8mb4_unicode_ci,
  `icon` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_menus`
--

CREATE TABLE `smartend_menus` (
  `id` bigint UNSIGNED NOT NULL,
  `row_no` int NOT NULL,
  `father_id` int NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `cat_id` int DEFAULT NULL,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_menus`
--

INSERT INTO `smartend_menus` (`id`, `row_no`, `father_id`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `status`, `type`, `cat_id`, `link`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'القائمة الرئيسية', 'Main Menu', '主菜单', 'मुख्य मेन्यू', 'Menú principal', 'Главное меню', 'Menu principal', 'Menu principal', 'Hauptmenü', 'เมนูหลัก', 'Menu principal', 1, 0, 0, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(2, 2, 0, 'روابط سريعة', 'Quick Links', '快速链接', 'त्वरित सम्पक', 'enlaces rápidos', 'Быстрые ссылки', 'Links Rápidos', 'Liens rapides', 'Quicklinks', 'ลิงค์ด่วน', 'Links Rápidos', 1, 0, 0, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(3, 1, 1, 'الرئيسية', 'Home', '家', 'घर', 'Casa', 'Дом', 'Lar', 'Domicile', 'Home', 'บ้าน', 'Principal', 1, 1, 0, 'home', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(4, 2, 1, 'من نحن', 'About', '关于', 'के बारे में', 'Acerca de', 'О', 'Cerca de', 'À propos', 'Über uns', 'เกี่ยวกับ', 'Sobre', 1, 1, 0, 'topic/about', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(5, 3, 1, 'خدماتنا', 'Services', '服务', 'सेवाएं', 'Servicios', 'Услуги', 'Serviços', 'services', 'Services', 'Services', 'Serviços', 1, 3, 2, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(6, 4, 1, 'أخبارنا', 'News', '新闻', 'समाचार', 'Noticias', 'Новости', 'Notícia', 'Nouvelles', 'News', 'ข่าว', 'Notícias', 1, 2, 3, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(7, 5, 1, 'الصور', 'Photos', '照片', 'तस्वीरें', 'Fotos', 'Фото', 'Fotos', 'Photos', 'Fotos', '照片', 'Fotos', 1, 2, 4, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(8, 6, 1, 'الفيديو', 'Videos', '视频', 'वीडियो', 'Videos', 'Видео', 'Vídeos', 'Vidéos', 'Videos', 'วิดีโอ', 'Vídeos', 1, 3, 5, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(9, 7, 1, 'الصوتيات', 'Audio', '声音的', 'ऑडियो', 'Audio', 'Аудио', 'Áudio', 'l\'audio', 'Audio', 'เครื่องเสียง', 'áudio', 1, 3, 6, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(10, 8, 1, 'المنتجات', 'Products', '产品', 'उत्पादों', 'Productos', 'Товары', 'Produtos', 'Produits', 'Produkte', 'สินค้า', 'Produtos', 1, 3, 8, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(11, 9, 1, 'المدونة', 'Blog', '博客', 'ब्लॉग', 'Blog', 'Блог', 'Blog', 'Blog', 'Blog', 'บล็อก', 'blog', 1, 2, 7, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(12, 10, 1, 'اتصل بنا', 'Contact', '接触', 'संपर्क करें', 'Contacto', 'Контакт', 'Contato', 'Contact', 'Kontakt', 'ติดต่อ', 'Contato', 1, 1, 0, 'contact', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(13, 1, 2, 'الرئيسية', 'Home', '家', 'घर', 'Casa', 'Дом', 'Lar', 'Domicile', 'Homer', 'บ้าน', 'Pagina inicial', 1, 1, 0, 'home', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(14, 2, 2, 'من نحن', 'About', '关于', 'के बारे में', 'Acerca de', 'О', 'Cerca de', 'À propos', 'Über uns', 'เกี่ยวกับ', 'Sobre nós', 1, 1, 0, 'topic/about', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(15, 3, 2, 'المدونة', 'Blog', '博客', 'ब्लॉग', 'Blog', 'Блог', 'Blog', 'Blog', 'Blog', 'บล็อก', 'blog', 1, 2, 7, '', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(16, 4, 2, 'الخصوصية', 'Privacy', '隐私', 'गोपनीयता', 'Intimidad', 'Конфиденциальность', 'Privacidade', 'Intimité', 'Datenschutz', 'ความเป็นส่วนตัว', 'Privacidade', 1, 1, 0, 'topic/privacy', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(17, 5, 2, 'الشروط والأحكام', 'Terms & Conditions', '条款和条件', 'नियम एवं शर्तें', 'Términos y condiciones', 'Условия и положения', 'termos e Condições', 'termes et conditions', 'AGB', 'ข้อตกลงและเงื่อนไข', 'termos e Condições', 1, 1, 0, 'topic/terms', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(18, 6, 2, 'اتصل بنا', 'Contact', '接触', 'संपर्क करें', 'Contacto', 'Контакт', 'Contato', 'Contact', 'Kontakt', 'ติดต่อ', 'Contato', 1, 1, 0, 'contact', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_migrations`
--

CREATE TABLE `smartend_migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_migrations`
--

INSERT INTO `smartend_migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2020_09_11_160850_create_sessions_table', 1),
(7, '2020_09_11_190632_create_webmaster_settings_table', 1),
(8, '2020_09_11_190633_create_webmaster_sections_table', 1),
(9, '2020_09_11_190635_create_webmaster_banners_table', 1),
(10, '2020_09_11_190637_create_webmails_groups_table', 1),
(11, '2020_09_11_190638_create_webmails_files_table', 1),
(12, '2020_09_11_190640_create_webmails_table', 1),
(13, '2020_09_11_190641_create_topics_table', 1),
(14, '2020_09_11_190643_create_settings_table', 1),
(15, '2020_09_11_190645_create_sections_table', 1),
(16, '2020_09_11_190647_create_photos_table', 1),
(17, '2020_09_11_190648_create_permissions_table', 1),
(18, '2020_09_11_190650_create_menus_table', 1),
(19, '2020_09_11_190652_create_maps_table', 1),
(20, '2020_09_11_190654_create_events_table', 1),
(21, '2020_09_11_190656_create_countries_table', 1),
(22, '2020_09_11_190657_create_contacts_groups_table', 1),
(23, '2020_09_11_190659_create_contacts_table', 1),
(24, '2020_09_11_190701_create_comments_table', 1),
(25, '2020_09_11_190703_create_banners_table', 1),
(26, '2020_09_11_190704_create_attach_files_table', 1),
(27, '2020_09_11_190706_create_analytics_visitors_table', 1),
(28, '2020_09_11_190708_create_analytics_pages_table', 1),
(29, '2020_09_11_190912_create_related_topics_table', 1),
(30, '2020_09_11_190914_create_topic_categories_table', 1),
(31, '2020_09_11_190916_create_topic_fields_table', 1),
(32, '2020_09_11_190917_create_webmaster_section_fields_table', 1),
(33, '2020_09_11_201046_create_languages_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `smartend_password_resets`
--

CREATE TABLE `smartend_password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_permissions`
--

CREATE TABLE `smartend_permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `view_status` tinyint NOT NULL DEFAULT '0',
  `add_status` tinyint NOT NULL DEFAULT '0',
  `edit_status` tinyint NOT NULL DEFAULT '0',
  `delete_status` tinyint NOT NULL DEFAULT '0',
  `active_status` tinyint NOT NULL DEFAULT '0',
  `analytics_status` tinyint NOT NULL DEFAULT '0',
  `inbox_status` tinyint NOT NULL DEFAULT '0',
  `newsletter_status` tinyint NOT NULL DEFAULT '0',
  `calendar_status` tinyint NOT NULL DEFAULT '0',
  `banners_status` tinyint NOT NULL DEFAULT '0',
  `settings_status` tinyint NOT NULL DEFAULT '0',
  `webmaster_status` tinyint NOT NULL DEFAULT '0',
  `data_sections` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_status` tinyint NOT NULL DEFAULT '0',
  `home_links` text COLLATE utf8mb4_unicode_ci,
  `home_details_ar` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_en` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_ch` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_hi` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_es` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_ru` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_pt` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_fr` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_de` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_th` longtext COLLATE utf8mb4_unicode_ci,
  `home_details_br` longtext COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_permissions`
--

INSERT INTO `smartend_permissions` (`id`, `name`, `view_status`, `add_status`, `edit_status`, `delete_status`, `active_status`, `analytics_status`, `inbox_status`, `newsletter_status`, `calendar_status`, `banners_status`, `settings_status`, `webmaster_status`, `data_sections`, `home_status`, `home_links`, `home_details_ar`, `home_details_en`, `home_details_ch`, `home_details_hi`, `home_details_es`, `home_details_ru`, `home_details_pt`, `home_details_fr`, `home_details_de`, `home_details_th`, `home_details_br`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Webmaster', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '1,2,3,4,5,6,7,8,9', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(2, 'Website Manager', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '1,2,3,4,5,6,7,8,9', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(3, 'Limited User', 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 0, 0, '1,2,3,4,5,6,7,8,9', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_personal_access_tokens`
--

CREATE TABLE `smartend_personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_photos`
--

CREATE TABLE `smartend_photos` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_related_topics`
--

CREATE TABLE `smartend_related_topics` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `topic2_id` int NOT NULL,
  `row_no` int NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_sections`
--

CREATE TABLE `smartend_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `visits` int NOT NULL,
  `webmaster_id` int NOT NULL,
  `father_id` int NOT NULL,
  `row_no` int NOT NULL,
  `seo_title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_sessions`
--

CREATE TABLE `smartend_sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_settings`
--

CREATE TABLE `smartend_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `site_title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_desc_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_keywords_ar` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_en` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_ch` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_hi` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_es` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_ru` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_pt` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_fr` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_de` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_th` text COLLATE utf8mb4_unicode_ci,
  `site_keywords_br` text COLLATE utf8mb4_unicode_ci,
  `site_webmails` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notify_messages_status` tinyint DEFAULT NULL,
  `notify_comments_status` tinyint DEFAULT NULL,
  `notify_orders_status` tinyint DEFAULT NULL,
  `notify_table_status` tinyint DEFAULT NULL,
  `notify_private_status` tinyint DEFAULT NULL,
  `site_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_status` tinyint NOT NULL,
  `close_msg` text COLLATE utf8mb4_unicode_ci,
  `social_link1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link3` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link4` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link5` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link6` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link7` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link8` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link9` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_link10` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t1_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t3` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t4` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t5` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t6` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_t7_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_logo_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_fav` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_apple` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_color1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_color2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_type` tinyint DEFAULT NULL,
  `style_bg_type` tinyint DEFAULT NULL,
  `style_bg_pattern` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_bg_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_bg_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_subscribe` tinyint DEFAULT NULL,
  `style_footer` tinyint DEFAULT NULL,
  `style_header` tinyint DEFAULT NULL,
  `style_footer_bg` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_preload` tinyint DEFAULT NULL,
  `css` longtext COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_settings`
--

INSERT INTO `smartend_settings` (`id`, `site_title_ar`, `site_title_en`, `site_title_ch`, `site_title_hi`, `site_title_es`, `site_title_ru`, `site_title_pt`, `site_title_fr`, `site_title_de`, `site_title_th`, `site_title_br`, `site_desc_ar`, `site_desc_en`, `site_desc_ch`, `site_desc_hi`, `site_desc_es`, `site_desc_ru`, `site_desc_pt`, `site_desc_fr`, `site_desc_de`, `site_desc_th`, `site_desc_br`, `site_keywords_ar`, `site_keywords_en`, `site_keywords_ch`, `site_keywords_hi`, `site_keywords_es`, `site_keywords_ru`, `site_keywords_pt`, `site_keywords_fr`, `site_keywords_de`, `site_keywords_th`, `site_keywords_br`, `site_webmails`, `notify_messages_status`, `notify_comments_status`, `notify_orders_status`, `notify_table_status`, `notify_private_status`, `site_url`, `site_status`, `close_msg`, `social_link1`, `social_link2`, `social_link3`, `social_link4`, `social_link5`, `social_link6`, `social_link7`, `social_link8`, `social_link9`, `social_link10`, `contact_t1_ar`, `contact_t1_en`, `contact_t1_ch`, `contact_t1_hi`, `contact_t1_es`, `contact_t1_ru`, `contact_t1_pt`, `contact_t1_fr`, `contact_t1_de`, `contact_t1_th`, `contact_t1_br`, `contact_t3`, `contact_t4`, `contact_t5`, `contact_t6`, `contact_t7_ar`, `contact_t7_en`, `contact_t7_ch`, `contact_t7_hi`, `contact_t7_es`, `contact_t7_ru`, `contact_t7_pt`, `contact_t7_fr`, `contact_t7_de`, `contact_t7_th`, `contact_t7_br`, `style_logo_ar`, `style_logo_en`, `style_logo_ch`, `style_logo_hi`, `style_logo_es`, `style_logo_ru`, `style_logo_pt`, `style_logo_fr`, `style_logo_de`, `style_logo_th`, `style_logo_br`, `style_fav`, `style_apple`, `style_color1`, `style_color2`, `style_type`, `style_bg_type`, `style_bg_pattern`, `style_bg_color`, `style_bg_image`, `style_subscribe`, `style_footer`, `style_header`, `style_footer_bg`, `style_preload`, `css`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'اسم الموقع', 'Site Title', '网站标题', 'घटनास्थल शीर्षक', 'Título del sitio', 'Заголовок сайта', 'titulo do site', 'Titre du site', 'Pagina Titel', 'ชื่อเว็บไซต์', 'Descrição do site e algumas poucas informações sobre ele', 'وصف الموقع الإلكتروني ونبذة قصيره عنه', 'Website description and some little information about it', '网站描述和一些关于它的小信息', 'वेबसाइट विवरण और इसके बारे में कुछ छोटी जानकारी', 'Descripción del sitio web y poca información al respecto.', 'Описание веб-сайта и небольшая информация о нем', 'Descrição do site e algumas poucas informações sobre ele', 'Description du site et quelques informations à son sujet', 'Beschrijving van de website en wat informatie erover', 'คำอธิบายเว็บไซต์และข้อมูลเล็กน้อยเกี่ยวกับมัน', NULL, 'كلمات، دلالية، موقع، موقع إلكتروني', 'key, words, website, web', '关键，词，网站，网络', 'कुंजी, शब्द, वेबसाइट, वेब', 'clave, palabras, sitio web, web', 'ключ, слова, веб-сайт, веб', 'chave, palavras, site, web', 'clé, mots, site web, web', 'sleutel, woorden, website, web', 'คีย์ คำ เว็บไซต์ เว็บ', 'chave, palavras, site, web', 'info@sitename.com', 1, 1, 1, NULL, NULL, 'http://www.sitename.com/', 1, 'Website under maintenance \n<h1>Comming SOON</h1>', '#', '#', '#', '#', '#', '#', '#', '#', '#', '#', 'المبني - اسم الشارع - المدينة - الدولة', 'Building, Street name, City, Country', '建筑物、街道名称、城市、国家', 'भवन, सड़क का नाम, शहर, देश', 'Edificio, Nombre de la calle, Ciudad, País', 'Здание, Название улицы, Город, Страна', 'Edifício, nome da rua, cidade, país', 'Bâtiment, Nom de rue, Ville, Pays', 'Gebouw, straatnaam, plaats, land', 'อาคาร ชื่อถนน เมือง ประเทศ', 'Domingo a Quinta das 08:00 às 17:00', '+(xxx) 0xxxxxxx', '+(xxx) 0xxxxxxx', '+(xxx) 0xxxxxxx', 'info@sitename.com', 'من الأحد إلى الخميس 08:00 ص - 05:00 م', 'Sunday to Thursday 08:00 AM to 05:00 PM', '周日至周四 08:00 AM 至 05:00 PM', 'रविवार से गुरुवार सुबह 08:00 बजे से शाम 05:00 बजे तक', 'Domingo a jueves 08:00 AM a 05:00 PM', 'С воскресенья по четверг с 08:00 до 17:00.', 'Domingo a quinta-feira, das 8h às 17h', 'Du dimanche au jeudi de 08h00 à 17h00', 'zondag t/m donderdag 08:00 uur tot 17:00 uur', 'อาทิตย์-พฤหัสบดี 08.00-17.00 น.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '#0cbaa4', '#2e3e4e', 0, 0, NULL, NULL, NULL, 1, 1, 0, NULL, 0, NULL, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_topics`
--

CREATE TABLE `smartend_topics` (
  `id` bigint UNSIGNED NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details_ar` longtext COLLATE utf8mb4_unicode_ci,
  `details_en` longtext COLLATE utf8mb4_unicode_ci,
  `details_ch` longtext COLLATE utf8mb4_unicode_ci,
  `details_hi` longtext COLLATE utf8mb4_unicode_ci,
  `details_es` longtext COLLATE utf8mb4_unicode_ci,
  `details_ru` longtext COLLATE utf8mb4_unicode_ci,
  `details_pt` longtext COLLATE utf8mb4_unicode_ci,
  `details_fr` longtext COLLATE utf8mb4_unicode_ci,
  `details_de` longtext COLLATE utf8mb4_unicode_ci,
  `details_th` longtext COLLATE utf8mb4_unicode_ci,
  `details_br` longtext COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `video_type` tinyint DEFAULT NULL,
  `photo_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attach_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_file` text COLLATE utf8mb4_unicode_ci,
  `audio_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL,
  `visits` int NOT NULL,
  `webmaster_id` int NOT NULL,
  `section_id` int NOT NULL,
  `row_no` int NOT NULL,
  `form_id` int DEFAULT NULL,
  `topic_id` int DEFAULT NULL,
  `seo_title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_topics`
--

INSERT INTO `smartend_topics` (`id`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `details_ar`, `details_en`, `details_ch`, `details_hi`, `details_es`, `details_ru`, `details_pt`, `details_fr`, `details_de`, `details_th`, `details_br`, `date`, `expire_date`, `video_type`, `photo_file`, `attach_file`, `video_file`, `audio_file`, `icon`, `status`, `visits`, `webmaster_id`, `section_id`, `row_no`, `form_id`, `topic_id`, `seo_title_ar`, `seo_title_en`, `seo_title_ch`, `seo_title_hi`, `seo_title_es`, `seo_title_ru`, `seo_title_pt`, `seo_title_fr`, `seo_title_de`, `seo_title_th`, `seo_title_br`, `seo_description_ar`, `seo_description_en`, `seo_description_ch`, `seo_description_hi`, `seo_description_es`, `seo_description_ru`, `seo_description_pt`, `seo_description_fr`, `seo_description_de`, `seo_description_th`, `seo_description_br`, `seo_keywords_ar`, `seo_keywords_en`, `seo_keywords_ch`, `seo_keywords_hi`, `seo_keywords_es`, `seo_keywords_ru`, `seo_keywords_pt`, `seo_keywords_fr`, `seo_keywords_de`, `seo_keywords_th`, `seo_keywords_br`, `seo_url_slug_ar`, `seo_url_slug_en`, `seo_url_slug_ch`, `seo_url_slug_hi`, `seo_url_slug_es`, `seo_url_slug_ru`, `seo_url_slug_pt`, `seo_url_slug_fr`, `seo_url_slug_de`, `seo_url_slug_th`, `seo_url_slug_br`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'من نحن', 'About Us', '关于', 'के बारे में', 'Acerca de', 'О', 'Cerca de', 'À propos', 'Over', 'เกี่ยวกับ', 'Sobre nós', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', '2023-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(2, 'اتصل بنا', 'Contact Us', '接触', 'संपर्क करें', 'Contacto', 'Контакт', 'Contato', 'Contact', 'Contact', 'ติดต่อ', 'Contate-nos', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', '2023-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(3, 'الخصوصية', 'Privacy', '隐私', 'गोपनीयता', 'Intimidad', 'Конфиденциальность', 'Privacidade', 'Intimité', 'Privacy', 'ความเป็นส่วนตัว', 'Privacidade', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', '2023-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(4, 'الشروط والأحكام', 'Terms & Conditions', '条款和条件', 'नियम एवं शर्तें', 'Términos y condiciones', 'Условия и положения', 'termos e Condições', 'termes et conditions', 'algemene voorwaarden', 'ข้อตกลงและเงื่อนไข', 'termos e Condições', 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.', 'It is a long established fact that a reader will be distracted by the readable content of a page.', '一个长期存在的事实是，读者会被页面的可读内容分散注意力。', 'यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक किसी पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।', 'Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.', 'Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы.', 'É um fato estabelecido há muito tempo que um leitor se distrairá com o conteúdo legível de uma página.', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.', 'Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.', 'เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า', 'É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.', '2023-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(5, 'الصفحة الرئيسية', 'Home Welcome', '家', 'घर', 'Casa', 'Дом', 'Lar', 'Domicile', 'Thuis', 'บ้าน', 'Home Welcome', '<div style=\'text-align: center\'><h2>مرحبا بكم في موقعنا</h2>\nهناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص.</div>', '<div style=\'text-align: center\'><h2>Welcome to our website</h2>It is a long established fact that a reader will be distracted by the readable content of a page.It is a long established fact that a reader will be distracted by the readable content of a page.It is a long established fact that a reader will be distracted by the readable content of a page.It is a long established fact that a reader will be distracted by the readable content of a page.It is a long established fact that a reader will be distracted by the readable content of a page.</div>', '<div style=\'text-align: center\'><h2>欢迎来到我们的网站</h2>485 / 5000\nTranslation results\n读者会被页面的可读内容分心是一个长期确立的事实 被页面的可读内容分心。长期以来，读者会被页面的可读内容分心，这是一个长期确立的事实。长期以来，读者会被页面的可读内容分心。 </div>', '<div style=\'text-align: center\'><h2>हमारी वैबसाइट पर आपका स्वागत है</h2>यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक एक पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा। यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक एक पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा। यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक होगा एक पृष्ठ की पठनीय सामग्री से विचलित हो। यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक एक पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा। यह एक लंबे समय से स्थापित तथ्य है कि एक पाठक एक पृष्ठ की पठनीय सामग्री से विचलित हो जाएगा।.</div>', '<div style=\'text-align: center\'><h2>Bienvenido a nuestro sitio web</h2>Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página. Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página. distraerse con el contenido legible de una página. Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página. Es un hecho establecido desde hace mucho tiempo que un lector se distraerá con el contenido legible de una página.</div>', '<div style=\'text-align: center\'><h2>Добро пожаловать на наш сайт</h2>То, что читатель будет отвлекаться на удобочитаемое содержание страницы, - давно установленный факт. То, что читатель будет отвлекаться на читаемое содержание страницы, - давно установленный факт. отвлекаться на читабельное содержание страницы. Давно установлено, что читатель будет отвлекаться на читабельное содержание страницы. Давно установившийся факт, что читатель будет отвлекаться на читаемое содержание страницы.</div>', '<div style=\'text-align: center\'><h2>Bem-vindo ao nosso site</h2>É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página. É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página. É um fato estabelecido há muito tempo que um leitor irá ser distraído pelo conteúdo legível de uma página. É um fato estabelecido que um leitor será distraído pelo conteúdo legível de uma página. É um fato estabelecido que um leitor será distraído pelo conteúdo legível de uma página.</div>', '<div style=\'text-align: center\'><h2>Bienvenue sur notre site</h2>C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.C\'est un fait établi de longue date qu\'un lecteur être distrait par le contenu lisible d\'une page. C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page. C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page.</div>', '<div style=\'text-align: center\'><h2>Welkom op onze website</h2>Het staat al lang vast dat een lezer wordt afgeleid door de leesbare inhoud van een pagina. Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina. worden afgeleid door de leesbare inhoud van een pagina. Het staat al lang vast dat een lezer wordt afgeleid door de leesbare inhoud van een pagina. Het is een vaststaand feit dat een lezer wordt afgeleid door de leesbare inhoud van een pagina.</div>', '<div style=\'text-align: center\'><h2>ยินดีต้อนรับสู่เว็บไซต์ของเรา</h2>เป็นข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า ข้อเท็จจริงที่เป็นที่ยอมรับมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า เป็นความจริงที่เป็นที่ยอมรับมานานแล้วว่าผู้อ่านจะ ฟุ้งซ่านโดยเนื้อหาที่อ่านได้ของหน้า เป็นความจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า ข้อเท็จจริงที่มีมาช้านานว่าผู้อ่านจะถูกรบกวนโดยเนื้อหาที่อ่านได้ของหน้า</div>', '<div style=\'text-align: center\'><h2>Bem-vindo ao nosso site</h2>É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página. É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página. ser distraído pelo conteúdo legível de uma página. É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página. É um fato há muito estabelecido que um leitor será distraído pelo conteúdo legível de uma página.</div>', '2023-06-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, 0, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_topic_categories`
--

CREATE TABLE `smartend_topic_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `section_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_topic_fields`
--

CREATE TABLE `smartend_topic_fields` (
  `id` bigint UNSIGNED NOT NULL,
  `topic_id` int NOT NULL,
  `field_id` int NOT NULL,
  `field_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_users`
--

CREATE TABLE `smartend_users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permissions_id` int DEFAULT NULL,
  `status` tinyint NOT NULL,
  `connect_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connect_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_users`
--

INSERT INTO `smartend_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `photo`, `permissions_id`, `status`, `connect_email`, `connect_password`, `provider`, `provider_id`, `access_token`, `created_by`, `updated_by`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@site.com', NULL, '$2y$10$lxcJjiHbHge3PXNlbWArFukWE6C2A0XHFuVwxgCHBsMleuGFWn1x2', NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmails`
--

CREATE TABLE `smartend_webmails` (
  `id` bigint UNSIGNED NOT NULL,
  `cat_id` int NOT NULL DEFAULT '0',
  `group_id` int DEFAULT NULL,
  `contact_id` int DEFAULT NULL,
  `father_id` int DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` longtext COLLATE utf8mb4_unicode_ci,
  `date` datetime NOT NULL,
  `from_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_cc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_bcc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `flag` tinyint NOT NULL DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmails_files`
--

CREATE TABLE `smartend_webmails_files` (
  `id` bigint UNSIGNED NOT NULL,
  `webmail_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmails_groups`
--

CREATE TABLE `smartend_webmails_groups` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_webmails_groups`
--

INSERT INTO `smartend_webmails_groups` (`id`, `name`, `color`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Support', '#00bcd4', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(2, 'Orders', '#f44336', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35'),
(3, 'Queries', '#8bc34a', 1, NULL, '2023-06-30 20:24:35', '2023-06-30 20:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmaster_banners`
--

CREATE TABLE `smartend_webmaster_banners` (
  `id` bigint UNSIGNED NOT NULL,
  `row_no` int NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width` int NOT NULL,
  `height` int NOT NULL,
  `desc_status` tinyint NOT NULL,
  `link_status` tinyint NOT NULL,
  `type` tinyint NOT NULL,
  `icon_status` tinyint NOT NULL,
  `status` tinyint NOT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_webmaster_banners`
--

INSERT INTO `smartend_webmaster_banners` (`id`, `row_no`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `width`, `height`, `desc_status`, `link_status`, `type`, `icon_status`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'بنرات الرئيسية', 'Home Banners', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1600, 500, 1, 1, 1, 0, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(2, 2, 'بنرات نصية', 'Text Banners', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 330, 330, 1, 1, 0, 1, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(3, 3, 'بنرات جانبية', 'Side Banners', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 330, 330, 0, 1, 1, 0, 1, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmaster_sections`
--

CREATE TABLE `smartend_webmaster_sections` (
  `id` bigint UNSIGNED NOT NULL,
  `row_no` int NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint NOT NULL DEFAULT '0',
  `title_status` tinyint NOT NULL DEFAULT '1',
  `photo_status` tinyint NOT NULL DEFAULT '1',
  `case_status` tinyint NOT NULL DEFAULT '1',
  `visits_status` tinyint NOT NULL DEFAULT '1',
  `sections_status` tinyint NOT NULL DEFAULT '0',
  `comments_status` tinyint NOT NULL DEFAULT '0',
  `date_status` tinyint NOT NULL DEFAULT '0',
  `expire_date_status` tinyint NOT NULL DEFAULT '0',
  `longtext_status` tinyint NOT NULL DEFAULT '0',
  `editor_status` tinyint NOT NULL DEFAULT '0',
  `attach_file_status` tinyint NOT NULL DEFAULT '0',
  `extra_attach_file_status` tinyint NOT NULL DEFAULT '0',
  `multi_images_status` tinyint NOT NULL DEFAULT '0',
  `section_icon_status` tinyint NOT NULL DEFAULT '0',
  `icon_status` tinyint NOT NULL DEFAULT '0',
  `maps_status` tinyint NOT NULL DEFAULT '0',
  `order_status` tinyint NOT NULL DEFAULT '0',
  `related_status` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `seo_title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_description_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_keywords_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seo_url_slug_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_webmaster_sections`
--

INSERT INTO `smartend_webmaster_sections` (`id`, `row_no`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `type`, `title_status`, `photo_status`, `case_status`, `visits_status`, `sections_status`, `comments_status`, `date_status`, `expire_date_status`, `longtext_status`, `editor_status`, `attach_file_status`, `extra_attach_file_status`, `multi_images_status`, `section_icon_status`, `icon_status`, `maps_status`, `order_status`, `related_status`, `status`, `seo_title_ar`, `seo_title_en`, `seo_title_ch`, `seo_title_hi`, `seo_title_es`, `seo_title_ru`, `seo_title_pt`, `seo_title_fr`, `seo_title_de`, `seo_title_th`, `seo_title_br`, `seo_description_ar`, `seo_description_en`, `seo_description_ch`, `seo_description_hi`, `seo_description_es`, `seo_description_ru`, `seo_description_pt`, `seo_description_fr`, `seo_description_de`, `seo_description_th`, `seo_description_br`, `seo_keywords_ar`, `seo_keywords_en`, `seo_keywords_ch`, `seo_keywords_hi`, `seo_keywords_es`, `seo_keywords_ru`, `seo_keywords_pt`, `seo_keywords_fr`, `seo_keywords_de`, `seo_keywords_th`, `seo_keywords_br`, `seo_url_slug_ar`, `seo_url_slug_en`, `seo_url_slug_ch`, `seo_url_slug_hi`, `seo_url_slug_es`, `seo_url_slug_ru`, `seo_url_slug_pt`, `seo_url_slug_fr`, `seo_url_slug_de`, `seo_url_slug_th`, `seo_url_slug_br`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'صفحات الموقع', 'Site pages', '网站页面', 'साइट पेज', 'Sitio Páginas', 'Страницы сайта', 'Site Páginas', 'Site Pages', 'Seiten', 'หน้าเว็บไซต์', 'páginas do site', 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 1, 'صفحات الموقع', 'Site pages', '网站页面', 'साइट पेज', 'Sitio Páginas', 'Страницы сайта', 'Site Páginas', 'Site Pages', 'Seiten', 'หน้าเว็บไซต์', 'páginas do site', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 'sitepages', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(2, 2, 'الخدمات', 'Services', '服务', 'सेवाएं', 'Servicios', 'Услуги', 'Serviços', 'services', 'Dienstleistungen', 'บริการ', 'Serviços', 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 'الخدمات', 'Services', '服务', 'सेवाएं', 'Servicios', 'Услуги', 'Serviços', 'services', 'Dienstleistungen', 'บริการ', 'Serviços', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'services', 'services', 'services', 'services', 'services', 'services', 'services', 'services', 'services', 'services', 'services', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(3, 3, 'الأخبار', 'News', '新闻', 'समाचार', 'Noticias', 'Новости', 'Notícia', 'Nouvelles', 'Nieuws', 'ข่าว', 'Notícias', 0, 1, 1, 1, 1, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 1, 1, 'الأخبار', 'News', '新闻', 'समाचार', 'Noticias', 'Новости', 'Notícia', 'Nouvelles', 'Nieuws', 'ข่าว', 'Notícias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'news', 'news', 'news', 'news', 'news', 'news', 'news', 'news', 'news', 'news', 'news', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(4, 4, 'الصور', 'Photos', '照片', 'तस्वीरें', 'Fotos', 'Фото', 'Fotos', 'Photos', 'Fotos', '照片', 'Fotos', 1, 1, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 1, 'الصور', 'Photos', '照片', 'तस्वीरें', 'Fotos', 'Фото', 'Fotos', 'Photos', 'Fotos', '照片', 'Fotos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 'photos', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(5, 5, 'الفيديو', 'Videos', '视频', 'वीडियो', 'Videos', 'Видео', 'Vídeos', 'Vidéos', 'Videos', 'วิดีโอ', 'Vídeos', 2, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 'الفيديو', 'Videos', '视频', 'वीडियो', 'Videos', 'Видео', 'Vídeos', 'Vidéos', 'Videos', 'วิดีโอ', 'Vídeos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 'videos', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(6, 6, 'الصوتيات', 'Audio', '声音的', 'ऑडियो', 'Audio', 'Аудио', 'Áudio', 'l\'audio', 'Audio', 'เครื่องเสียง', 'áudio', 3, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 'الصوتيات', 'Audio', 'Audio', 'ऑडियो', 'Audio', 'Аудио', 'Áudio', 'l\'audio', 'Audio', 'เครื่องเสียง', 'áudio', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 'audio', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(7, 7, 'المدونة', 'Blog', '博客', 'ब्लॉग', 'Blog', 'Блог', 'Blog', 'Blog', 'Blog', 'บล็อก', 'blog', 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 1, 1, 'المدونة', 'Blog', '博客', 'ब्लॉग', 'Blog', 'Блог', 'Blog', 'Blog', 'Blog', 'บล็อก', 'blog', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 'blog', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(8, 8, 'المنتجات', 'Products', '产品', 'उत्पादों', 'Productos', 'Товары', 'Produtos', 'Produits', 'Produkte', 'สินค้า', 'Produtos', 0, 1, 1, 1, 1, 2, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 1, 'المنتجات', 'Products', '产品', 'उत्पादों', 'Productos', 'Товары', 'Produtos', 'Produits', 'Produkte', 'สินค้า', 'Produtos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'products', 'products', 'products', 'products', 'products', 'products', 'products', 'products', 'products', 'products', 'products', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34'),
(9, 9, 'العملاء', 'Partners', '伙伴', 'भागीदारों', 'Socias', 'Партнеры', 'Sócias', 'Les partenaires', 'Partners', 'พันธมิตร', 'Parceiras', 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 'العملاء', 'Partners', '伙伴', 'भागीदारों', 'Socias', 'Партнеры', 'Sócias', 'Les partenaires', 'Partners', 'พันธมิตร', 'Parceiras', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 'partners', 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmaster_section_fields`
--

CREATE TABLE `smartend_webmaster_section_fields` (
  `id` bigint UNSIGNED NOT NULL,
  `webmaster_id` int NOT NULL,
  `type` int NOT NULL,
  `title_ar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_hi` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_es` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ru` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_pt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_fr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_de` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_th` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_br` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details_ar` text COLLATE utf8mb4_unicode_ci,
  `details_en` text COLLATE utf8mb4_unicode_ci,
  `details_ch` text COLLATE utf8mb4_unicode_ci,
  `details_hi` text COLLATE utf8mb4_unicode_ci,
  `details_es` text COLLATE utf8mb4_unicode_ci,
  `details_ru` text COLLATE utf8mb4_unicode_ci,
  `details_pt` text COLLATE utf8mb4_unicode_ci,
  `details_fr` text COLLATE utf8mb4_unicode_ci,
  `details_de` text COLLATE utf8mb4_unicode_ci,
  `details_th` text COLLATE utf8mb4_unicode_ci,
  `details_br` text COLLATE utf8mb4_unicode_ci,
  `row_no` int NOT NULL,
  `status` tinyint NOT NULL,
  `required` tinyint NOT NULL,
  `in_table` tinyint NOT NULL DEFAULT '0',
  `in_search` tinyint NOT NULL DEFAULT '0',
  `in_listing` tinyint NOT NULL DEFAULT '0',
  `in_page` tinyint NOT NULL DEFAULT '0',
  `in_statics` tinyint NOT NULL DEFAULT '0',
  `lang_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `view_permission_groups` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_permission_groups` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edit_permission_groups` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_webmaster_section_fields`
--

INSERT INTO `smartend_webmaster_section_fields` (`id`, `webmaster_id`, `type`, `title_ar`, `title_en`, `title_ch`, `title_hi`, `title_es`, `title_ru`, `title_pt`, `title_fr`, `title_de`, `title_th`, `title_br`, `default_value`, `details_ar`, `details_en`, `details_ch`, `details_hi`, `details_es`, `details_ru`, `details_pt`, `details_fr`, `details_de`, `details_th`, `details_br`, `row_no`, `status`, `required`, `in_table`, `in_search`, `in_listing`, `in_page`, `in_statics`, `lang_code`, `css_class`, `view_permission_groups`, `add_permission_groups`, `edit_permission_groups`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 9, 0, 'رابط', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', 'URL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 1, 0, 0, 1, 0, 'all', NULL, '0', '0', '0', 1, 1, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `smartend_webmaster_settings`
--

CREATE TABLE `smartend_webmaster_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `seo_status` tinyint NOT NULL,
  `analytics_status` tinyint NOT NULL,
  `banners_status` tinyint NOT NULL,
  `inbox_status` tinyint NOT NULL,
  `calendar_status` tinyint NOT NULL,
  `settings_status` tinyint NOT NULL,
  `newsletter_status` tinyint NOT NULL,
  `members_status` tinyint NOT NULL,
  `orders_status` tinyint NOT NULL,
  `shop_status` tinyint NOT NULL,
  `shop_settings_status` tinyint NOT NULL,
  `default_currency_id` int NOT NULL,
  `languages_by_default` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latest_news_section_id` int NOT NULL,
  `header_menu_id` int NOT NULL,
  `footer_menu_id` int NOT NULL,
  `home_banners_section_id` int NOT NULL,
  `home_text_banners_section_id` int NOT NULL,
  `side_banners_section_id` int NOT NULL,
  `contact_page_id` int NOT NULL,
  `newsletter_contacts_group` int NOT NULL,
  `new_comments_status` tinyint NOT NULL,
  `links_status` tinyint NOT NULL,
  `register_status` tinyint NOT NULL,
  `permission_group` int NOT NULL,
  `api_status` tinyint NOT NULL,
  `api_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `home_content1_section_id` int NOT NULL,
  `home_content2_section_id` int NOT NULL,
  `home_content3_section_id` int NOT NULL,
  `home_contents_per_page` int NOT NULL,
  `mail_driver` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_host` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_port` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_encryption` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_no_replay` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_template` longtext COLLATE utf8mb4_unicode_ci,
  `nocaptcha_status` tinyint NOT NULL,
  `nocaptcha_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nocaptcha_sitekey` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_tags_status` tinyint NOT NULL,
  `google_tags_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_analytics_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_facebook_status` tinyint NOT NULL,
  `login_facebook_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_facebook_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_twitter_status` tinyint NOT NULL,
  `login_twitter_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_twitter_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_google_status` tinyint NOT NULL,
  `login_google_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_google_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_linkedin_status` tinyint NOT NULL,
  `login_linkedin_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_linkedin_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_github_status` tinyint NOT NULL,
  `login_github_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_github_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_bitbucket_status` tinyint NOT NULL,
  `login_bitbucket_client_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_bitbucket_client_secret` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dashboard_link_status` tinyint NOT NULL,
  `text_editor` tinyint NOT NULL DEFAULT '0',
  `tiny_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` tinyint NOT NULL DEFAULT '0',
  `purchase_code` text COLLATE utf8mb4_unicode_ci,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smartend_webmaster_settings`
--

INSERT INTO `smartend_webmaster_settings` (`id`, `seo_status`, `analytics_status`, `banners_status`, `inbox_status`, `calendar_status`, `settings_status`, `newsletter_status`, `members_status`, `orders_status`, `shop_status`, `shop_settings_status`, `default_currency_id`, `languages_by_default`, `latest_news_section_id`, `header_menu_id`, `footer_menu_id`, `home_banners_section_id`, `home_text_banners_section_id`, `side_banners_section_id`, `contact_page_id`, `newsletter_contacts_group`, `new_comments_status`, `links_status`, `register_status`, `permission_group`, `api_status`, `api_key`, `home_content1_section_id`, `home_content2_section_id`, `home_content3_section_id`, `home_contents_per_page`, `mail_driver`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_encryption`, `mail_no_replay`, `mail_title`, `mail_template`, `nocaptcha_status`, `nocaptcha_secret`, `nocaptcha_sitekey`, `google_tags_status`, `google_tags_id`, `google_analytics_code`, `login_facebook_status`, `login_facebook_client_id`, `login_facebook_client_secret`, `login_twitter_status`, `login_twitter_client_id`, `login_twitter_client_secret`, `login_google_status`, `login_google_client_id`, `login_google_client_secret`, `login_linkedin_status`, `login_linkedin_client_id`, `login_linkedin_client_secret`, `login_github_status`, `login_github_client_id`, `login_github_client_secret`, `login_bitbucket_status`, `login_bitbucket_client_id`, `login_bitbucket_client_secret`, `dashboard_link_status`, `text_editor`, `tiny_key`, `timezone`, `version`, `license`, `purchase_code`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 5, 'en', 3, 1, 2, 1, 2, 3, 2, 1, 1, 0, 0, 3, 0, '402784613679330', 7, 4, 9, 20, 'smtp', '', '', '', '', '', 'noreply@site.com', '{title}', '{details}', 0, '', '', 0, '', '', 0, '', '', 0, '', '', 0, '', '', 0, '', '', 0, '', '', 0, '', '', 1, 0, NULL, 'UTC', '10.0.0', 0, NULL, 1, NULL, '2023-06-30 20:24:34', '2023-06-30 20:24:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `smartend_analytics_pages`
--
ALTER TABLE `smartend_analytics_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_analytics_visitors`
--
ALTER TABLE `smartend_analytics_visitors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_attach_files`
--
ALTER TABLE `smartend_attach_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_banners`
--
ALTER TABLE `smartend_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_comments`
--
ALTER TABLE `smartend_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_contacts`
--
ALTER TABLE `smartend_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_contacts_groups`
--
ALTER TABLE `smartend_contacts_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_countries`
--
ALTER TABLE `smartend_countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_events`
--
ALTER TABLE `smartend_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_failed_jobs`
--
ALTER TABLE `smartend_failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `smartend_failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `smartend_languages`
--
ALTER TABLE `smartend_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_maps`
--
ALTER TABLE `smartend_maps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_menus`
--
ALTER TABLE `smartend_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_migrations`
--
ALTER TABLE `smartend_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_password_resets`
--
ALTER TABLE `smartend_password_resets`
  ADD KEY `smartend_password_resets_email_index` (`email`);

--
-- Indexes for table `smartend_permissions`
--
ALTER TABLE `smartend_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_personal_access_tokens`
--
ALTER TABLE `smartend_personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `smartend_personal_access_tokens_token_unique` (`token`);

--
-- Indexes for table `smartend_photos`
--
ALTER TABLE `smartend_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_related_topics`
--
ALTER TABLE `smartend_related_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_sections`
--
ALTER TABLE `smartend_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_sessions`
--
ALTER TABLE `smartend_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `smartend_sessions_user_id_index` (`user_id`),
  ADD KEY `smartend_sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `smartend_settings`
--
ALTER TABLE `smartend_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_topics`
--
ALTER TABLE `smartend_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_topic_categories`
--
ALTER TABLE `smartend_topic_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_topic_fields`
--
ALTER TABLE `smartend_topic_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_users`
--
ALTER TABLE `smartend_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `smartend_users_email_unique` (`email`);

--
-- Indexes for table `smartend_webmails`
--
ALTER TABLE `smartend_webmails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmails_files`
--
ALTER TABLE `smartend_webmails_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmails_groups`
--
ALTER TABLE `smartend_webmails_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmaster_banners`
--
ALTER TABLE `smartend_webmaster_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmaster_sections`
--
ALTER TABLE `smartend_webmaster_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmaster_section_fields`
--
ALTER TABLE `smartend_webmaster_section_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smartend_webmaster_settings`
--
ALTER TABLE `smartend_webmaster_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `smartend_analytics_pages`
--
ALTER TABLE `smartend_analytics_pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_analytics_visitors`
--
ALTER TABLE `smartend_analytics_visitors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_attach_files`
--
ALTER TABLE `smartend_attach_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_banners`
--
ALTER TABLE `smartend_banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `smartend_comments`
--
ALTER TABLE `smartend_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_contacts`
--
ALTER TABLE `smartend_contacts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_contacts_groups`
--
ALTER TABLE `smartend_contacts_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_countries`
--
ALTER TABLE `smartend_countries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `smartend_events`
--
ALTER TABLE `smartend_events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_failed_jobs`
--
ALTER TABLE `smartend_failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_languages`
--
ALTER TABLE `smartend_languages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `smartend_maps`
--
ALTER TABLE `smartend_maps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_menus`
--
ALTER TABLE `smartend_menus`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `smartend_migrations`
--
ALTER TABLE `smartend_migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `smartend_permissions`
--
ALTER TABLE `smartend_permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `smartend_personal_access_tokens`
--
ALTER TABLE `smartend_personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_photos`
--
ALTER TABLE `smartend_photos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_related_topics`
--
ALTER TABLE `smartend_related_topics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_sections`
--
ALTER TABLE `smartend_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_settings`
--
ALTER TABLE `smartend_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_topics`
--
ALTER TABLE `smartend_topics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `smartend_topic_categories`
--
ALTER TABLE `smartend_topic_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_topic_fields`
--
ALTER TABLE `smartend_topic_fields`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_users`
--
ALTER TABLE `smartend_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_webmails`
--
ALTER TABLE `smartend_webmails`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_webmails_files`
--
ALTER TABLE `smartend_webmails_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smartend_webmails_groups`
--
ALTER TABLE `smartend_webmails_groups`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `smartend_webmaster_banners`
--
ALTER TABLE `smartend_webmaster_banners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `smartend_webmaster_sections`
--
ALTER TABLE `smartend_webmaster_sections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `smartend_webmaster_section_fields`
--
ALTER TABLE `smartend_webmaster_section_fields`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `smartend_webmaster_settings`
--
ALTER TABLE `smartend_webmaster_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
