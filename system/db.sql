-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1
-- Vytvořeno: Sob 15. bře 2025, 20:40
-- Verze serveru: 10.4.32-MariaDB
-- Verze PHP: 8.0.30

SET FOREIGN_KEY_CHECKS=0;
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
CREATE DATABASE IF NOT EXISTS `cyklistickey` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `cyklistickey`;

-- --------------------------------------------------------

--
-- Struktura tabulky `admin_access`
--

DROP TABLE IF EXISTS `admin_access`;
CREATE TABLE IF NOT EXISTS `admin_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `role_1` tinyint(1) NOT NULL,
  `role_2` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `admin_access_logs`
--

DROP TABLE IF EXISTS `admin_access_logs`;
CREATE TABLE IF NOT EXISTS `admin_access_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `changed_by` int(11) NOT NULL,
  `change_date` datetime NOT NULL,
  `page` varchar(255) NOT NULL,
  `role_1` tinyint(1) NOT NULL,
  `role_2` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_admin_access_logs_to_users` (`changed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky`
--

DROP TABLE IF EXISTS `clanky`;
CREATE TABLE IF NOT EXISTS `clanky` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) NOT NULL,
  `datum` datetime NOT NULL,
  `viditelnost` tinyint(1) NOT NULL,
  `nahled_foto` varchar(255) DEFAULT NULL,
  `obsah` text NOT NULL,
  `user_id` int(10) NOT NULL,
  `autor` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id_clanky` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky_kategorie`
--

DROP TABLE IF EXISTS `clanky_kategorie`;
CREATE TABLE IF NOT EXISTS `clanky_kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `id_kategorie` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_clanky_TO_clanky_kategorie` (`id_clanku`),
  KEY `idx_id_kategorie_clanky_kategorie` (`id_kategorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `kategorie`
--

DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE IF NOT EXISTS `kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev_kategorie` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `pageviews`
--

DROP TABLE IF EXISTS `pageviews`;
CREATE TABLE IF NOT EXISTS `pageviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `view_date` date NOT NULL,
  `views` int(11) DEFAULT 0,
  `view_hour` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_page` (`page`),
  UNIQUE KEY `UQ_view_date` (`view_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id_password_resets` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `propagace`
--

DROP TABLE IF EXISTS `propagace`;
CREATE TABLE IF NOT EXISTS `propagace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `zacatek` datetime NOT NULL,
  `konec` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_users_TO_propagace` (`user_id`),
  KEY `idx_id_clanku_propagace` (`id_clanku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `socials`
--

DROP TABLE IF EXISTS `socials`;
CREATE TABLE IF NOT EXISTS `socials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fa_class` varchar(255) NOT NULL,
  `nazev` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `heslo` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `profil_foto` varchar(255) NOT NULL,
  `zahlavi_foto` varchar(255) NOT NULL,
  `popis` text NOT NULL,
  `datum` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `users_online`
--

DROP TABLE IF EXISTS `users_online`;
CREATE TABLE IF NOT EXISTS `users_online` (
  `session` char(128) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `user_social`
--

DROP TABLE IF EXISTS `user_social`;
CREATE TABLE IF NOT EXISTS `user_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `social_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_users_TO_user_social` (`social_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `views_clanku`
--

DROP TABLE IF EXISTS `views_clanku`;
CREATE TABLE IF NOT EXISTS `views_clanku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `pocet` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_clanek_views_clanku` (`id_clanku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
