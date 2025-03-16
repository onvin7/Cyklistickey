-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 15. bře 2025, 20:37
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `cyklistickey`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `admin_access`
--

CREATE TABLE `admin_access` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `role_1` tinyint(1) NOT NULL,
  `role_2` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `admin_access`
--

INSERT INTO `admin_access` (`id`, `page`, `role_1`, `role_2`) VALUES
(1, 'statistics', 1, 1),
(2, 'statistics/top', 0, 1),
(3, 'statistics/view', 0, 1),
(4, 'articles', 1, 1),
(5, 'articles/create', 1, 1),
(6, 'articles/store', 1, 1),
(7, 'articles/edit', 1, 1),
(8, 'articles/update', 1, 1),
(9, 'articles/delete', 0, 1),
(10, 'categories', 1, 1),
(11, 'categories/create', 0, 0),
(12, 'categories/store', 0, 0),
(13, 'categories/edit', 0, 0),
(14, 'categories/update', 0, 0),
(15, 'categories/delete', 0, 0),
(16, 'users', 0, 1),
(17, 'users/edit', 0, 0),
(18, 'users/update', 0, 0),
(19, 'users/delete', 0, 0),
(20, 'access-control', 0, 0),
(21, 'access-control/update', 0, 0),
(22, 'logout', 1, 1),
(23, 'promotions', 1, 1),
(24, 'promotions/create', 0, 1),
(25, 'promotions/store', 0, 1),
(26, 'promotions/upcoming', 0, 1),
(27, 'promotions/history', 0, 1),
(28, 'promotions/delete', 0, 1),
(29, 'settings', 1, 1),
(30, 'settings/update', 1, 1),
(31, 'social-sites', 0, 0),
(32, 'social-sites/save', 0, 0),
(33, 'social-sites/delete', 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `admin_access_logs`
--

CREATE TABLE `admin_access_logs` (
  `id` int(11) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `change_date` datetime NOT NULL,
  `page` varchar(255) NOT NULL,
  `role_1` tinyint(1) NOT NULL,
  `role_2` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `admin_access_logs`
--

INSERT INTO `admin_access_logs` (`id`, `changed_by`, `change_date`, `page`, `role_1`, `role_2`) VALUES
(49, 1, '2025-03-07 09:30:44', 'articles', 1, 1),
(50, 1, '2025-03-07 09:30:44', 'articles/create', 1, 1),
(51, 1, '2025-03-07 09:30:44', 'articles/delete', 0, 1),
(52, 1, '2025-03-07 09:30:44', 'articles/edit', 1, 1),
(53, 1, '2025-03-07 09:30:44', 'articles/store', 1, 1),
(54, 1, '2025-03-07 09:30:44', 'articles/update', 1, 1),
(55, 1, '2025-03-07 09:30:44', 'categories', 1, 1),
(56, 1, '2025-03-07 09:30:44', 'logout', 1, 1),
(57, 1, '2025-03-07 09:30:44', 'promotions', 1, 1),
(58, 1, '2025-03-07 09:30:44', 'promotions/create', 0, 1),
(59, 1, '2025-03-07 09:30:44', 'promotions/delete', 0, 1),
(60, 1, '2025-03-07 09:30:44', 'promotions/history', 0, 1),
(61, 1, '2025-03-07 09:30:44', 'promotions/store', 0, 1),
(62, 1, '2025-03-07 09:30:44', 'promotions/upcoming', 0, 1),
(63, 1, '2025-03-07 09:30:44', 'settings', 1, 1),
(64, 1, '2025-03-07 09:30:44', 'settings/update', 1, 1),
(65, 1, '2025-03-07 09:30:44', 'statistics', 1, 1),
(66, 1, '2025-03-07 09:30:44', 'statistics/top', 0, 1),
(67, 1, '2025-03-07 09:30:44', 'statistics/view', 0, 1),
(68, 1, '2025-03-07 09:30:44', 'users', 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky`
--

CREATE TABLE `clanky` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) NOT NULL,
  `datum` datetime NOT NULL,
  `viditelnost` tinyint(1) NOT NULL,
  `nahled_foto` varchar(255) DEFAULT NULL,
  `obsah` text NOT NULL,
  `user_id` int(10) NOT NULL,
  `autor` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `clanky`
--

INSERT INTO `clanky` (`id`, `nazev`, `datum`, `viditelnost`, `nahled_foto`, `obsah`, `user_id`, `autor`, `url`) VALUES
(1, 'Článek 1', '2025-01-01 09:00:00', 1, '', 'Obsah článku 1.', 2, 1, 'clanek-1'),
(2, 'Článek 2', '2025-01-02 10:15:00', 1, '', 'Obsah článku 2.', 3, 0, 'clanek-2'),
(3, 'Článek 3', '2025-01-03 11:30:00', 0, '', 'Obsah článku 3.', 4, 1, 'clanek-3'),
(4, 'Článek 4', '2025-01-04 08:45:00', 1, '', 'Obsah článku 4.', 5, 1, 'clanek-4'),
(5, 'Článek 5', '2025-01-05 14:20:00', 1, '', 'Obsah článku 5.', 6, 0, 'clanek-5'),
(6, 'Článek 6', '2025-01-06 09:15:00', 1, '', 'Obsah článku 6.', 7, 1, 'clanek-6'),
(7, 'Článek 7', '2025-01-07 10:30:00', 1, '', 'Obsah článku 7.', 8, 0, 'clanek-7'),
(8, 'Článek 8', '2025-01-08 11:45:00', 1, '', 'Obsah článku 8.', 9, 1, 'clanek-8'),
(9, 'Článek 9', '2025-01-09 12:00:00', 0, '', 'Obsah článku 9.', 10, 1, 'clanek-9'),
(10, 'Článek 10', '2025-01-10 08:30:00', 1, '', 'Obsah článku 10.', 1, 0, 'clanek-10'),
(11, 'Článek 11', '2025-01-11 09:45:00', 1, '', 'Obsah článku 11.', 2, 1, 'clanek-11'),
(12, 'Článek 12', '2025-01-12 10:00:00', 0, '', 'Obsah článku 12.', 3, 1, 'clanek-12'),
(13, 'Článek 13', '2025-01-13 11:15:00', 1, '', 'Obsah článku 13.', 4, 0, 'clanek-13'),
(14, 'Článek 14', '2025-01-14 12:30:00', 1, '', 'Obsah článku 14.', 5, 1, 'clanek-14'),
(15, 'Článek 15', '2025-01-15 08:45:00', 1, '', 'Obsah článku 15.', 6, 0, 'clanek-15'),
(16, 'Článek 16', '2025-01-16 09:00:00', 1, '', 'Obsah článku 16.', 7, 1, 'clanek-16'),
(17, 'Článek 17', '2025-01-17 10:15:00', 1, '', 'Obsah článku 17.', 8, 0, 'clanek-17'),
(18, 'Článek 18', '2025-01-18 11:30:00', 1, '', 'Obsah článku 18.', 9, 1, 'clanek-18'),
(19, 'Článek 19', '2025-01-19 12:45:00', 0, '', 'Obsah článku 19.', 10, 1, 'clanek-19'),
(20, 'Článek 20', '2025-01-20 08:00:00', 1, '', 'Obsah článku 20.', 1, 0, 'clanek-20'),
(21, 'Článek 21', '2025-01-21 09:15:00', 1, '', 'Obsah článku 21.', 2, 1, 'clanek-21'),
(22, 'Článek 22', '2025-01-22 10:30:00', 0, '', 'Obsah článku 22.', 3, 0, 'clanek-22'),
(23, 'Článek 23', '2025-01-23 11:45:00', 1, '', 'Obsah článku 23.', 4, 1, 'clanek-23'),
(24, 'Článek 24', '2025-01-24 12:00:00', 0, '', 'Obsah článku 24.', 5, 1, 'clanek-24'),
(25, 'Článek 25', '2025-01-25 08:30:00', 1, '', 'Obsah článku 25.', 6, 0, 'clanek-25'),
(26, 'Článek 26', '2025-01-26 09:45:00', 1, '', 'Obsah článku 26.', 7, 1, 'clanek-26'),
(27, 'Článek 27', '2025-01-27 10:00:00', 1, '', 'Obsah článku 27.', 8, 0, 'clanek-27'),
(28, 'Článek 28', '2025-01-28 11:15:00', 1, '', 'Obsah článku 28.', 9, 1, 'clanek-28'),
(29, 'Článek 29', '2025-01-29 12:30:00', 0, '', 'Obsah článku 29.', 10, 1, 'clanek-29'),
(30, 'Článek 30', '2025-01-30 08:45:00', 1, '', 'Obsah článku 30.', 1, 0, 'clanek-30'),
(31, 'Článek 31', '2025-01-31 09:00:00', 1, '', 'Obsah článku 31.', 2, 1, 'clanek-31'),
(32, 'Článek 32', '2025-01-31 09:15:00', 1, '', 'Obsah článku 32.', 3, 1, 'clanek-32'),
(33, 'Článek 33', '2025-01-31 09:30:00', 1, '', 'Obsah článku 33.', 4, 0, 'clanek-33'),
(34, 'Článek 34', '2025-01-31 09:45:00', 0, '', 'Obsah článku 34.', 5, 1, 'clanek-34'),
(35, 'Článek 35', '2025-01-31 10:00:00', 1, '', 'Obsah článku 35.', 6, 0, 'clanek-35'),
(36, 'Článek 36', '2025-01-31 10:15:00', 1, '', 'Obsah článku 36.', 7, 1, 'clanek-36'),
(37, 'Článek 37', '2025-01-31 10:30:00', 1, '', 'Obsah článku 37.', 8, 0, 'clanek-37'),
(38, 'Článek 38', '2025-01-31 10:45:00', 1, '', 'Obsah článku 38.', 9, 1, 'clanek-38'),
(39, 'Článek 39', '2025-01-31 11:00:00', 1, '', 'Obsah článku 39.', 10, 1, 'clanek-39'),
(40, 'Článek 40', '2025-01-31 11:15:00', 1, '', 'Obsah článku 40.', 1, 0, 'clanek-40'),
(41, 'Článek 41', '2025-01-31 11:30:00', 1, '', 'Obsah článku 41.', 2, 1, 'clanek-41'),
(42, 'Článek 42', '2025-01-31 11:45:00', 1, '', 'Obsah článku 42.', 3, 1, 'clanek-42'),
(43, 'Článek 43', '2025-01-31 12:00:00', 1, '', 'Obsah článku 43.', 4, 0, 'clanek-43'),
(44, 'Článek 44', '2025-01-31 12:15:00', 0, '', 'Obsah článku 44.', 5, 1, 'clanek-44'),
(45, 'Článek 45', '2025-01-31 12:30:00', 1, '', 'Obsah článku 45.', 6, 0, 'clanek-45'),
(46, 'Článek 46', '2025-01-31 12:45:00', 1, '', 'Obsah článku 46.', 7, 1, 'clanek-46'),
(47, 'Článek 47', '2025-01-31 13:00:00', 1, '', 'Obsah článku 47.', 8, 0, 'clanek-47'),
(48, 'Článek 48', '2025-01-31 13:15:00', 1, '', 'Obsah článku 48.', 9, 1, 'clanek-48'),
(49, 'Článek 49', '2025-01-31 13:30:00', 1, '', '', 10, 1, 'clanek-49'),
(50, 'Článek 50', '2025-02-15 18:24:28', 1, '', '<p>Obsah čl&aacute;nku 50.črfeubir&scaron;efvhgubi&scaron; gh&aacute;&iacute;&scaron;čzr&aacute;h</p>', 1, 1, 'Článek-50rew'),
(52, 'TEST', '2025-02-18 21:36:47', 1, '', '<p>rč&scaron;fed</p>', 1, 1, 'test'),
(53, 'rčfš', '2025-02-18 21:38:25', 1, NULL, '<p>rč&scaron;ferw</p>', 1, 1, 'rčfš'),
(54, 'tgvf', '2025-02-18 21:39:12', 1, NULL, '<p>fws</p>', 1, 1, 'tgvf'),
(55, 'gf', '2025-02-18 21:40:00', 1, NULL, '<p>efd</p>', 1, 1, 'gf'),
(57, 'testik', '2025-02-24 16:33:16', 1, 'DevislExtremeRace2021_24.jpg', '<h1><span style=\"text-decoration: underline;\"><em><strong>tests</strong></em></span></h1>\n<p><img src=\"/uploads/articles/67bc90a4e3823_DSCF7197.jpg\" alt=\"\" width=\"498\" height=\"332\"></p>\n<p>ourhfíuno r</p>\n<p>re</p>\n<p>&nbsp;</p>\n<p>&nbsp;ger</p>', 1, 1, 'testik'),
(58, 'td', '2025-02-24 16:34:46', 1, 'DSCF0353.jpg', '<h1><img src=\"../../uploads/articles/67bc918db5931_GOPR0401.JPG\" alt=\"\" width=\"500\" height=\"375\"></h1>', 1, 1, 'td'),
(59, '2+1 Kreatin monohydrát | 2+1 kg', '2025-03-11 20:58:12', 1, 'GOPR0510.JPG', '<h2><strong>V&yacute;hodn&yacute; set kreatinu monohydr&aacute;tu 2+1 kg v čistotě 99,9 %<br></strong></h2>\r\n<p><strong>Kreatin zvy&scaron;uje fyzickou v&yacute;konnost a oddaluje &uacute;navu a zlep&scaron;uje regeneraci. Kdy? Při opakovan&eacute; a&nbsp;vysoce intenzivn&iacute; kr&aacute;tkodob&eacute; z&aacute;těži. Kupte si mikronizovan&yacute; kreatin monohydr&aacute;t v&yacute;hodně v setu 2+1<br></strong></p>\r\n<p><strong><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"../../../uploads/articles/67bd7acb1f647_GOPR0513.JPG\" alt=\"krouzek\" width=\"716\" height=\"537\"></strong></p>\r\n<p><strong>Počet d&aacute;vek v balen&iacute;:</strong>&nbsp;3&times;525 (tj. na 4 cykly dle doporučen&iacute; n&iacute;že, jedna porce je 1,9 g)</p>\r\n<p><strong>Složen&iacute;:</strong>&nbsp;kreatin monohydr&aacute;t 99,9 % (test HPLC)&nbsp;</p>\r\n<p><strong>Rozpustnost:&nbsp;</strong>okamžit&aacute;</p>\r\n<p><strong>Chuť:&nbsp;</strong>neutr&aacute;ln&iacute;</p>\r\n<p><strong>Hmotnost v&yacute;robku: 3&times;</strong>1&nbsp;000 g</p>\r\n<p>Datum minim&aacute;ln&iacute; trvanlivosti v&yacute;robku je 12 měs&iacute;ců od data v&yacute;roby. Kreatin spotřebujte nejpozději do 4 měs&iacute;ců po otevřen&iacute;. U v&scaron;ech surovin laboratorně sledujeme ukazatele čistoty a zdravotn&iacute; nez&aacute;vadnosti.</p>\r\n<p><img src=\"../../../uploads/articles/67c03a86367b7_GOPR0509.JPG\" alt=\"\" width=\"200\" height=\"150\"></p>', 1, 1, '21-kreatin-monohydrat-21-kg'),
(60, 'hgvc', '2025-02-25 09:12:12', 1, 'DSCF8704.jpg', '<p>čr&scaron;fegtrwčhge</p>\n<h2><strong>V&yacute;hodn&yacute; set kreatinu monohydr&aacute;tu 2+1 kg v čistotě 99,9 %<br></strong></h2>\n<p><strong>Kreatin zvy&scaron;uje fyzickou v&yacute;konnost a oddaluje &uacute;navu a zlep&scaron;uje regeneraci. Kdy? Při opakovan&eacute; a&nbsp;vysoce intenzivn&iacute; kr&aacute;tkodob&eacute; z&aacute;těži. Kupte si mikronizovan&yacute; kreatin monohydr&aacute;t v&yacute;hodně v setu 2+1<br></strong></p>\n<p><strong>Počet d&aacute;vek v balen&iacute;:</strong>&nbsp;3&times;525 (tj. na 4 cykly dle doporučen&iacute; n&iacute;že, jedna porce je 1,9 g)</p>\n<p><strong>Složen&iacute;:</strong>&nbsp;kreatin monohydr&aacute;t 99,9 % (test HPLC)&nbsp;</p>\n<p><img src=\"../../uploads/articles/67bd7b44e940a_GOPR0208.JPG\" alt=\"hovnio\" width=\"200\" height=\"150\"></p>\n<p><strong>Rozpustnost:&nbsp;</strong>okamžit&aacute;</p>\n<p><strong>Chuť:&nbsp;</strong>neutr&aacute;ln&iacute;</p>\n<p><strong>Hmotnost v&yacute;robku: 3&times;</strong>1&nbsp;000 g</p>\n<p>Datum minim&aacute;ln&iacute; trvanlivosti v&yacute;robku je 12 měs&iacute;ců od data v&yacute;roby. Kreatin spotřebujte nejpozději do 4 měs&iacute;ců po otevřen&iacute;. U v&scaron;ech surovin laboratorně sledujeme ukazatele čistoty a zdravotn&iacute; nez&aacute;vadnosti.</p>', 1, 1, 'hgvc'),
(61, 'rfd', '2025-02-27 13:24:12', 1, 'kajak.png', '<p>rfefd</p>', 1, 1, 'rfd');

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky_kategorie`
--

CREATE TABLE `clanky_kategorie` (
  `id` int(11) NOT NULL,
  `id_clanku` int(11) NOT NULL,
  `id_kategorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `clanky_kategorie`
--

INSERT INTO `clanky_kategorie` (`id`, `id_clanku`, `id_kategorie`) VALUES
(1, 1, 3),
(2, 1, 5),
(3, 2, 1),
(4, 3, 4),
(5, 3, 2),
(6, 4, 6),
(7, 4, 1),
(8, 5, 3),
(9, 6, 5),
(10, 6, 2),
(11, 7, 4),
(12, 7, 1),
(13, 8, 3),
(14, 9, 5),
(15, 9, 6),
(16, 10, 2),
(17, 10, 4),
(18, 11, 1),
(19, 12, 3),
(20, 12, 5),
(21, 13, 2),
(22, 14, 4),
(23, 14, 6),
(24, 15, 1),
(25, 16, 3),
(26, 16, 5),
(27, 17, 2),
(28, 18, 4),
(29, 18, 6),
(30, 19, 1),
(31, 20, 3),
(32, 20, 5),
(33, 21, 2),
(34, 22, 4),
(35, 22, 6),
(36, 23, 1),
(37, 24, 3),
(38, 25, 5),
(39, 25, 2),
(40, 26, 4),
(41, 27, 6),
(42, 27, 1),
(43, 28, 3),
(44, 29, 5),
(45, 30, 2),
(46, 31, 4),
(47, 32, 6),
(48, 32, 1),
(49, 33, 3),
(50, 34, 5),
(51, 35, 2),
(52, 36, 4),
(53, 37, 6),
(54, 38, 1),
(55, 39, 3),
(56, 40, 5),
(57, 41, 2),
(58, 42, 4),
(59, 43, 6),
(60, 44, 1),
(61, 45, 3),
(62, 46, 5),
(63, 47, 2),
(64, 48, 4),
(65, 49, 6),
(66, 50, 1),
(79, 51, 3),
(80, 52, 1),
(81, 53, 6),
(82, 54, 2),
(83, 55, 4),
(84, 56, 1),
(85, 57, 5),
(86, 58, 3),
(87, 59, 2),
(88, 60, 6),
(89, 61, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `kategorie`
--

CREATE TABLE `kategorie` (
  `id` int(11) NOT NULL,
  `nazev_kategorie` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `kategorie`
--

INSERT INTO `kategorie` (`id`, `nazev_kategorie`, `url`) VALUES
(1, 'Aktuality', 'aktuality'),
(2, 'Technika', 'technika'),
(3, 'Závody', 'zavody'),
(4, 'Nevybráno', 'nevybrano'),
(5, 'test565', 'test565'),
(6, 'řrf', 'řrf');

-- --------------------------------------------------------

--
-- Struktura tabulky `pageviews`
--

CREATE TABLE `pageviews` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `view_date` date NOT NULL,
  `views` int(11) DEFAULT 0,
  `view_hour` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `token`, `expires_at`) VALUES
(5, 1, 'onvin@seznam.cz', '41af413fbcf26cf8bfd7fc651bbd18be06a4fc1125a1fbde8953312855f75786', '2025-02-21 23:24:31');

-- --------------------------------------------------------

--
-- Struktura tabulky `propagace`
--

CREATE TABLE `propagace` (
  `id` int(11) NOT NULL,
  `id_clanku` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `zacatek` datetime NOT NULL,
  `konec` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `propagace`
--

INSERT INTO `propagace` (`id`, `id_clanku`, `user_id`, `zacatek`, `konec`) VALUES
(1, 59, 1, '2025-02-27 13:20:00', '2025-02-28 13:20:00'),
(2, 52, 1, '2025-03-04 09:28:00', '2025-03-05 09:28:00'),
(3, 60, 1, '2025-03-05 09:28:00', '2025-03-06 09:28:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `socials`
--

CREATE TABLE `socials` (
  `id` int(11) NOT NULL,
  `fa_class` varchar(255) NOT NULL,
  `nazev` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `socials`
--

INSERT INTO `socials` (`id`, `fa_class`, `nazev`) VALUES
(1, 'fa-brands fa-instagram', 'Instagram\r\n'),
(2, 'fa-brands fa-facebook', 'Facebook'),
(3, 'fa-brands fa-strava', 'Strava'),
(4, 'fa-brands fa-x-twitter', 'Twitter'),
(6, 'fa-brands fa-youtube', 'Youtube'),
(7, 'fa-brands fa-tiktok', 'TikTok'),
(8, 'fa-regular fa-envelope', 'E-mail');

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `heslo` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `profil_foto` varchar(255) NOT NULL,
  `zahlavi_foto` varchar(255) NOT NULL,
  `popis` text NOT NULL,
  `datum` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `email`, `heslo`, `role`, `name`, `surname`, `profil_foto`, `zahlavi_foto`, `popis`, `datum`) VALUES
(1, 'onvin@seznam.cz', '$2y$10$lUvia0yvui/LTrjkI8DSe.DC.ScxyEbHDRzDIBMj/VOC3vTY0ZRnO', 3, 'Ondřej', 'Vincenc', '1741075315_DevislExtremeRace2021_24.jpg', '', 'Jmenuju se sice Vojta, ale v redakci mi nikdo neřekne jinak než Véna. Kolo mě provází vedle dalších sportů od útlého dětství, nicméně v posledních pár letech jsem tomu propadl úplně. Na Cyklistickým...', '2005-12-06'),
(2, 'user2@example.com', 'password2', 2, 'Adam', 'Fecko', '', 'header_2.jpg', 'Popis uživatele Adam Fecko.', '2025-01-02'),
(3, 'user3@example.com', 'password3', 1, 'Adam', 'Havlík', '', 'header_3.jpg', 'Popis uživatele Adam Havlík.', '2025-01-03'),
(4, 'user4@example.com', 'password4', 2, 'Richard', 'Horáček', '', 'header_4.jpg', 'Popis uživatele Richard Horáček.', '2025-01-04'),
(5, 'user5@example.com', 'password5', 1, 'Adam', 'Jozek', '', 'header_5.jpg', 'Popis uživatele Adam Jozek.', '2025-01-05'),
(6, 'user6@example.com', 'password6', 2, 'Jan', 'Karlík', '', 'header_6.jpg', 'Popis uživatele Jan Karlík.', '2025-01-06'),
(7, 'user7@example.com', 'password7', 1, 'Saša', 'Klíž', '', 'header_7.jpg', 'Popis uživatele Saša Klíž.', '2025-01-07'),
(8, 'user8@example.com', 'password8', 2, 'Filip', 'Kroužel', '', 'header_8.jpg', 'Popis uživatele Filip Kroužel.', '2025-01-08'),
(9, 'user9@example.com', 'password9', 1, 'Lukáš', 'Makarov', '', 'header_9.jpg', 'Popis uživatele Lukáš Makarov.', '2025-01-09'),
(10, 'user10@example.com', 'password10', 1, 'Stevered', 'Osykatšrefd', '', '', 'Popis uživatele Steve Osyka.', '2025-01-10'),
(11, 'steveosyka@gmail.com', '$2y$10$wxLbnwQlfxsFW9dM.WuJBetUix81sjwisR78kFV6jS6oe553hK1na', 1, 'Steve', 'Osyka', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `users_online`
--

CREATE TABLE `users_online` (
  `session` char(128) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `user_social`
--

CREATE TABLE `user_social` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `social_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `user_social`
--

INSERT INTO `user_social` (`id`, `user_id`, `social_id`, `link`) VALUES
(1, 1, 1, 'https://www.instagram.com/onvin_');

-- --------------------------------------------------------

--
-- Struktura tabulky `views_clanku`
--

CREATE TABLE `views_clanku` (
  `id` int(11) NOT NULL,
  `id_clanku` int(11) NOT NULL,
  `pocet` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `views_clanku`
--

INSERT INTO `views_clanku` (`id`, `id_clanku`, `pocet`, `datum`) VALUES
(1, 1, 120, '2025-01-01'),
(2, 1, 150, '2025-01-02'),
(3, 1, 180, '2025-01-03'),
(4, 2, 90, '2025-01-02'),
(5, 2, 140, '2025-01-03'),
(6, 2, 200, '2025-01-04'),
(7, 3, 100, '2025-01-03'),
(8, 3, 110, '2025-01-04'),
(9, 3, 150, '2025-01-05'),
(10, 4, 130, '2025-01-04'),
(11, 4, 160, '2025-01-05'),
(12, 4, 190, '2025-01-06'),
(13, 5, 140, '2025-01-05'),
(14, 5, 170, '2025-01-06'),
(15, 5, 200, '2025-01-07'),
(16, 6, 95, '2025-01-06'),
(17, 6, 115, '2025-01-07'),
(18, 6, 135, '2025-01-08'),
(19, 7, 145, '2025-01-07'),
(20, 7, 165, '2025-01-08'),
(21, 7, 185, '2025-01-09'),
(22, 8, 125, '2025-01-08'),
(23, 8, 145, '2025-01-09'),
(24, 8, 165, '2025-01-10'),
(25, 9, 105, '2025-01-09'),
(26, 9, 125, '2025-01-10'),
(27, 9, 145, '2025-01-11'),
(28, 10, 175, '2025-01-10'),
(29, 10, 195, '2025-01-11'),
(30, 10, 215, '2025-01-12'),
(31, 11, 135, '2025-01-11'),
(32, 11, 155, '2025-01-12'),
(33, 11, 175, '2025-01-13'),
(34, 12, 185, '2025-01-12'),
(35, 12, 205, '2025-01-13'),
(36, 12, 225, '2025-01-14'),
(37, 13, 115, '2025-01-13'),
(38, 13, 135, '2025-01-14'),
(39, 13, 155, '2025-01-15'),
(40, 14, 165, '2025-01-14'),
(41, 14, 185, '2025-01-15'),
(42, 14, 205, '2025-01-16'),
(43, 15, 145, '2025-01-15'),
(44, 15, 165, '2025-01-16'),
(45, 15, 185, '2025-01-17'),
(46, 16, 155, '2025-01-16'),
(47, 16, 175, '2025-01-17'),
(48, 16, 195, '2025-01-18'),
(49, 17, 205, '2025-01-17'),
(50, 17, 225, '2025-01-18'),
(51, 26, 140, '2025-01-01'),
(52, 26, 160, '2025-01-02'),
(53, 26, 180, '2025-01-03'),
(54, 27, 120, '2025-01-01'),
(55, 27, 130, '2025-01-02'),
(56, 27, 140, '2025-01-03'),
(57, 28, 110, '2025-01-01'),
(58, 28, 120, '2025-01-02'),
(59, 28, 130, '2025-01-03'),
(60, 29, 150, '2025-01-01'),
(61, 29, 170, '2025-01-02'),
(62, 29, 190, '2025-01-03'),
(63, 30, 200, '2025-01-01'),
(64, 30, 210, '2025-01-02'),
(65, 30, 220, '2025-01-03'),
(66, 31, 130, '2025-01-01'),
(67, 31, 140, '2025-01-02'),
(68, 31, 150, '2025-01-03'),
(69, 32, 160, '2025-01-01'),
(70, 32, 170, '2025-01-02'),
(71, 32, 180, '2025-01-03'),
(72, 33, 120, '2025-01-01'),
(73, 33, 130, '2025-01-02'),
(74, 33, 140, '2025-01-03'),
(75, 34, 110, '2025-01-01'),
(76, 34, 120, '2025-01-02'),
(77, 34, 130, '2025-01-03'),
(78, 35, 150, '2025-01-01'),
(79, 35, 170, '2025-01-02'),
(80, 35, 190, '2025-01-03'),
(81, 36, 130, '2025-01-01'),
(82, 36, 140, '2025-01-02'),
(83, 36, 150, '2025-01-03'),
(84, 37, 110, '2025-01-01'),
(85, 37, 120, '2025-01-02'),
(86, 37, 130, '2025-01-03'),
(87, 38, 140, '2025-01-01'),
(88, 38, 150, '2025-01-02'),
(89, 38, 160, '2025-01-03'),
(90, 39, 170, '2025-01-01'),
(91, 39, 180, '2025-01-02'),
(92, 39, 190, '2025-01-03'),
(93, 40, 200, '2025-01-01'),
(94, 40, 210, '2025-01-02'),
(95, 40, 220, '2025-01-03'),
(96, 41, 130, '2025-01-01'),
(97, 41, 140, '2025-01-02'),
(98, 41, 150, '2025-01-03'),
(99, 42, 160, '2025-01-01'),
(100, 42, 170, '2025-01-02'),
(101, 61, 1, '2025-03-11'),
(102, 61, 1, '2025-03-11'),
(103, 61, 1, '2025-03-11'),
(104, 61, 1, '2025-03-11'),
(105, 61, 1, '2025-03-11'),
(106, 61, 1, '2025-03-11'),
(107, 61, 1, '2025-03-11'),
(108, 61, 1, '2025-03-11'),
(109, 61, 1, '2025-03-11'),
(110, 61, 1, '2025-03-11'),
(111, 61, 1, '2025-03-11'),
(112, 61, 1, '2025-03-11'),
(113, 61, 1, '2025-03-11'),
(114, 59, 1, '2025-03-11'),
(115, 59, 1, '2025-03-11'),
(116, 59, 1, '2025-03-11'),
(117, 59, 1, '2025-03-11'),
(118, 59, 1, '2025-03-11'),
(119, 59, 1, '2025-03-11'),
(120, 59, 1, '2025-03-11'),
(121, 59, 1, '2025-03-11'),
(122, 59, 1, '2025-03-11'),
(123, 59, 1, '2025-03-11'),
(124, 59, 1, '2025-03-11'),
(125, 59, 1, '2025-03-11'),
(126, 59, 1, '2025-03-11'),
(127, 59, 1, '2025-03-11'),
(128, 59, 1, '2025-03-11'),
(129, 59, 1, '2025-03-11'),
(130, 59, 1, '2025-03-11'),
(131, 59, 1, '2025-03-11'),
(132, 59, 1, '2025-03-11'),
(133, 59, 1, '2025-03-11'),
(134, 59, 1, '2025-03-11'),
(135, 59, 1, '2025-03-11'),
(136, 59, 1, '2025-03-11'),
(137, 59, 1, '2025-03-11'),
(138, 59, 1, '2025-03-11'),
(139, 59, 1, '2025-03-11'),
(140, 13, 1, '2025-03-11'),
(141, 59, 1, '2025-03-11'),
(142, 13, 1, '2025-03-11'),
(143, 59, 1, '2025-03-11'),
(144, 59, 1, '2025-03-11'),
(145, 59, 1, '2025-03-11'),
(146, 59, 1, '2025-03-11'),
(147, 59, 1, '2025-03-11'),
(148, 59, 1, '2025-03-11'),
(149, 59, 1, '2025-03-11'),
(150, 59, 1, '2025-03-11'),
(151, 59, 1, '2025-03-11'),
(152, 59, 1, '2025-03-11'),
(153, 59, 1, '2025-03-11'),
(154, 59, 1, '2025-03-11'),
(155, 59, 1, '2025-03-11'),
(156, 35, 1, '2025-03-11'),
(157, 35, 1, '2025-03-11'),
(158, 35, 1, '2025-03-11'),
(159, 59, 1, '2025-03-11'),
(160, 59, 1, '2025-03-11'),
(161, 59, 1, '2025-03-11'),
(162, 59, 1, '2025-03-11'),
(163, 59, 1, '2025-03-11'),
(164, 59, 1, '2025-03-11'),
(165, 59, 1, '2025-03-11'),
(166, 59, 1, '2025-03-11'),
(167, 59, 1, '2025-03-11'),
(168, 59, 1, '2025-03-11'),
(169, 59, 1, '2025-03-11'),
(170, 17, 1, '2025-03-11'),
(171, 17, 1, '2025-03-11'),
(172, 59, 1, '2025-03-11'),
(173, 59, 1, '2025-03-11'),
(174, 59, 1, '2025-03-11'),
(175, 59, 1, '2025-03-11'),
(176, 59, 1, '2025-03-11'),
(177, 59, 1, '2025-03-11'),
(178, 59, 1, '2025-03-11'),
(179, 59, 1, '2025-03-11'),
(180, 59, 1, '2025-03-11'),
(181, 59, 1, '2025-03-11'),
(182, 59, 1, '2025-03-11'),
(183, 59, 1, '2025-03-11'),
(184, 59, 1, '2025-03-11'),
(185, 59, 1, '2025-03-11'),
(186, 59, 1, '2025-03-11'),
(187, 59, 1, '2025-03-11'),
(188, 59, 1, '2025-03-11'),
(189, 59, 1, '2025-03-11'),
(190, 59, 1, '2025-03-11'),
(191, 59, 1, '2025-03-11'),
(192, 59, 1, '2025-03-11'),
(193, 59, 1, '2025-03-11'),
(194, 59, 1, '2025-03-11'),
(195, 59, 1, '2025-03-11'),
(196, 59, 1, '2025-03-11'),
(197, 47, 1, '2025-03-11'),
(198, 47, 1, '2025-03-11'),
(199, 59, 1, '2025-03-11'),
(200, 59, 1, '2025-03-11'),
(201, 10, 1, '2025-03-11'),
(202, 59, 1, '2025-03-11'),
(203, 10, 1, '2025-03-11'),
(204, 59, 1, '2025-03-11'),
(205, 59, 1, '2025-03-11'),
(206, 59, 1, '2025-03-11'),
(207, 59, 1, '2025-03-11'),
(208, 59, 1, '2025-03-11'),
(209, 59, 1, '2025-03-11'),
(210, 59, 1, '2025-03-11'),
(211, 59, 1, '2025-03-11'),
(212, 59, 1, '2025-03-11'),
(213, 59, 1, '2025-03-11'),
(214, 35, 1, '2025-03-12'),
(215, 59, 1, '2025-03-12'),
(216, 35, 1, '2025-03-12'),
(217, 59, 1, '2025-03-12'),
(218, 59, 1, '2025-03-12'),
(219, 59, 1, '2025-03-12'),
(220, 59, 1, '2025-03-12'),
(221, 59, 1, '2025-03-12'),
(222, 59, 1, '2025-03-12'),
(223, 59, 1, '2025-03-12'),
(224, 59, 1, '2025-03-12'),
(225, 59, 1, '2025-03-12'),
(226, 59, 1, '2025-03-12'),
(227, 59, 1, '2025-03-12'),
(228, 59, 1, '2025-03-12'),
(229, 59, 1, '2025-03-12'),
(230, 59, 1, '2025-03-12'),
(231, 59, 1, '2025-03-12'),
(232, 59, 1, '2025-03-12'),
(233, 59, 1, '2025-03-12'),
(234, 59, 1, '2025-03-12'),
(235, 59, 1, '2025-03-12'),
(236, 57, 1, '2025-03-12'),
(237, 57, 1, '2025-03-12'),
(238, 57, 1, '2025-03-12'),
(239, 57, 1, '2025-03-12'),
(240, 58, 1, '2025-03-12'),
(241, 58, 1, '2025-03-12'),
(242, 60, 1, '2025-03-12'),
(243, 60, 1, '2025-03-12'),
(244, 57, 1, '2025-03-12'),
(245, 57, 1, '2025-03-12'),
(246, 57, 1, '2025-03-12'),
(247, 57, 1, '2025-03-12'),
(248, 57, 1, '2025-03-12'),
(249, 57, 1, '2025-03-12'),
(250, 57, 1, '2025-03-12'),
(251, 57, 1, '2025-03-12'),
(252, 57, 1, '2025-03-12'),
(253, 57, 1, '2025-03-12'),
(254, 57, 1, '2025-03-12'),
(255, 59, 1, '2025-03-12'),
(256, 59, 1, '2025-03-12'),
(257, 60, 1, '2025-03-12'),
(258, 60, 1, '2025-03-12'),
(259, 60, 1, '2025-03-13'),
(260, 59, 1, '2025-03-13'),
(261, 49, 1, '2025-03-13'),
(262, 49, 1, '2025-03-13'),
(263, 60, 1, '2025-03-13'),
(264, 49, 1, '2025-03-13'),
(265, 60, 1, '2025-03-13'),
(266, 55, 1, '2025-03-13'),
(267, 10, 1, '2025-03-13'),
(268, 52, 1, '2025-03-13'),
(269, 38, 1, '2025-03-13'),
(270, 60, 1, '2025-03-13'),
(271, 59, 1, '2025-03-13'),
(272, 40, 1, '2025-03-13'),
(273, 40, 1, '2025-03-13'),
(274, 59, 1, '2025-03-14'),
(275, 59, 1, '2025-03-14'),
(276, 60, 1, '2025-03-14'),
(277, 17, 1, '2025-03-15'),
(278, 54, 1, '2025-03-15'),
(279, 21, 1, '2025-03-15'),
(280, 47, 1, '2025-03-15'),
(281, 47, 1, '2025-03-15'),
(282, 47, 1, '2025-03-15'),
(283, 59, 1, '2025-03-15'),
(284, 59, 1, '2025-03-15'),
(285, 52, 1, '2025-03-15');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `admin_access`
--
ALTER TABLE `admin_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_admin_access_logs_to_users` (`changed_by`);

--
-- Indexy pro tabulku `clanky`
--
ALTER TABLE `clanky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id_clanky` (`user_id`);

--
-- Indexy pro tabulku `clanky_kategorie`
--
ALTER TABLE `clanky_kategorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_clanky_TO_clanky_kategorie` (`id_clanku`),
  ADD KEY `idx_id_kategorie_clanky_kategorie` (`id_kategorie`);

--
-- Indexy pro tabulku `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `pageviews`
--
ALTER TABLE `pageviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UQ_page` (`page`),
  ADD UNIQUE KEY `UQ_view_date` (`view_date`);

--
-- Indexy pro tabulku `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id_password_resets` (`user_id`);

--
-- Indexy pro tabulku `propagace`
--
ALTER TABLE `propagace`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_users_TO_propagace` (`user_id`),
  ADD KEY `idx_id_clanku_propagace` (`id_clanku`);

--
-- Indexy pro tabulku `socials`
--
ALTER TABLE `socials`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `users_online`
--
ALTER TABLE `users_online`
  ADD PRIMARY KEY (`session`);

--
-- Indexy pro tabulku `user_social`
--
ALTER TABLE `user_social`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_users_TO_user_social` (`social_id`);

--
-- Indexy pro tabulku `views_clanku`
--
ALTER TABLE `views_clanku`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fk_clanek_views_clanku` (`id_clanku`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `admin_access`
--
ALTER TABLE `admin_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pro tabulku `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pro tabulku `clanky`
--
ALTER TABLE `clanky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT pro tabulku `clanky_kategorie`
--
ALTER TABLE `clanky_kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT pro tabulku `kategorie`
--
ALTER TABLE `kategorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pro tabulku `pageviews`
--
ALTER TABLE `pageviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pro tabulku `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pro tabulku `propagace`
--
ALTER TABLE `propagace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pro tabulku `socials`
--
ALTER TABLE `socials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pro tabulku `user_social`
--
ALTER TABLE `user_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pro tabulku `views_clanku`
--
ALTER TABLE `views_clanku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `admin_access_logs`
--
ALTER TABLE `admin_access_logs`
  ADD CONSTRAINT `FK_admin_access_logs_to_users` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `clanky`
--
ALTER TABLE `clanky`
  ADD CONSTRAINT `FK_users_TO_clanky` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `clanky_kategorie`
--
ALTER TABLE `clanky_kategorie`
  ADD CONSTRAINT `FK_clanky_TO_clanky_kategorie` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`),
  ADD CONSTRAINT `FK_kategorie_TO_clanky_kategorie` FOREIGN KEY (`id_kategorie`) REFERENCES `kategorie` (`id`);

--
-- Omezení pro tabulku `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `FK_users_TO_password_resets` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `propagace`
--
ALTER TABLE `propagace`
  ADD CONSTRAINT `FK_clanky_TO_propagace` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`),
  ADD CONSTRAINT `FK_users_TO_propagace` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `user_social`
--
ALTER TABLE `user_social`
  ADD CONSTRAINT `FK_users_TO_user_social` FOREIGN KEY (`social_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `views_clanku`
--
ALTER TABLE `views_clanku`
  ADD CONSTRAINT `FK_clanky_TO_views_clanku` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
