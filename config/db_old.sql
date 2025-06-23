-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Počítač: md396.wedos.net:3306
-- Vygenerováno: Pon 23. čen 2025, 16:22
-- Verze serveru: 10.4.34-MariaDB-log
-- Verze PHP: 5.4.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `d340619_clanky`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `audio`
--

CREATE TABLE IF NOT EXISTS `audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev_souboru` varchar(255) NOT NULL,
  `id_clanku` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_clanku` (`id_clanku`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=764 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky`
--

CREATE TABLE IF NOT EXISTS `clanky` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) NOT NULL,
  `datum` datetime NOT NULL,
  `viditelnost` tinyint(1) NOT NULL,
  `nahled_foto` varchar(255) DEFAULT NULL,
  `user_id` int(255) NOT NULL,
  `autor` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=925 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `clanky_blog`
--

CREATE TABLE IF NOT EXISTS `clanky_blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `kategorie_id` int(11) NOT NULL,
  `datum_cas` datetime NOT NULL,
  `obrazek` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kategorie_id` (`kategorie_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `fotky`
--

CREATE TABLE IF NOT EXISTS `fotky` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) DEFAULT NULL,
  `nazev` varchar(255) NOT NULL,
  `cislo_fotky` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_clanku` (`id_clanku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `kategorie`
--

CREATE TABLE IF NOT EXISTS `kategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev_kategorie` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `kategorie_clanku`
--

CREATE TABLE IF NOT EXISTS `kategorie_clanku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `id_kategorie` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_clanku` (`id_clanku`),
  KEY `id_kategorie` (`id_kategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=3204 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `pageviews`
--

CREATE TABLE IF NOT EXISTS `pageviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `view_date` date NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `view_hour` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_page_date` (`page`,`view_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=946385 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `podkategorie`
--

CREATE TABLE IF NOT EXISTS `podkategorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazev_podkategorie` varchar(255) NOT NULL,
  `id_kategorie` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kategorie` (`id_kategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `podkategorie_clanku`
--

CREATE TABLE IF NOT EXISTS `podkategorie_clanku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `id_podkategorie` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_clanku` (`id_clanku`),
  KEY `id_podkategorie` (`id_podkategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=8391 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `produkty`
--

CREATE TABLE IF NOT EXISTS `produkty` (
  `id` int(11) NOT NULL,
  `nazev` varchar(255) DEFAULT NULL,
  `cena` decimal(10,2) DEFAULT NULL,
  `old_cena` decimal(10,2) DEFAULT NULL,
  `kategorie` varchar(255) DEFAULT NULL,
  `popis` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `promoted` tinyint(1) DEFAULT 0,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `propagace`
--

CREATE TABLE IF NOT EXISTS `propagace` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_clanku` (`id_clanku`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `heslo` varchar(255) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `profil_foto` varchar(255) NOT NULL,
  `zahlavi_foto` varchar(255) NOT NULL,
  `popis` text NOT NULL,
  `datum` date DEFAULT NULL,
  `ig` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `strava` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=COMPACT AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `users_online`
--

CREATE TABLE IF NOT EXISTS `users_online` (
  `session` char(128) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `views_clanku`
--

CREATE TABLE IF NOT EXISTS `views_clanku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `pocet` int(11) NOT NULL DEFAULT 0,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_clanek` (`id_clanku`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=142143 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `views_user`
--

CREATE TABLE IF NOT EXISTS `views_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `pocet` int(11) NOT NULL,
  `datum` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=2 ;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `audio`
--
ALTER TABLE `audio`
  ADD CONSTRAINT `audio_ibfk_1` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`);

--
-- Omezení pro tabulku `clanky`
--
ALTER TABLE `clanky`
  ADD CONSTRAINT `clanky_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `clanky_blog`
--
ALTER TABLE `clanky_blog`
  ADD CONSTRAINT `clanky_blog_ibfk_1` FOREIGN KEY (`kategorie_id`) REFERENCES `kategorie` (`id`);

--
-- Omezení pro tabulku `fotky`
--
ALTER TABLE `fotky`
  ADD CONSTRAINT `fotky_ibfk_1` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`);

--
-- Omezení pro tabulku `kategorie_clanku`
--
ALTER TABLE `kategorie_clanku`
  ADD CONSTRAINT `kategorie_clanku_ibfk_1` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`),
  ADD CONSTRAINT `kategorie_clanku_ibfk_2` FOREIGN KEY (`id_kategorie`) REFERENCES `kategorie` (`id`);

--
-- Omezení pro tabulku `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Omezení pro tabulku `podkategorie`
--
ALTER TABLE `podkategorie`
  ADD CONSTRAINT `podkategorie_ibfk_1` FOREIGN KEY (`id_kategorie`) REFERENCES `kategorie` (`id`);

--
-- Omezení pro tabulku `podkategorie_clanku`
--
ALTER TABLE `podkategorie_clanku`
  ADD CONSTRAINT `podkategorie_clanku_ibfk_1` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`),
  ADD CONSTRAINT `podkategorie_clanku_ibfk_2` FOREIGN KEY (`id_podkategorie`) REFERENCES `podkategorie` (`id`);

--
-- Omezení pro tabulku `propagace`
--
ALTER TABLE `propagace`
  ADD CONSTRAINT `propagace_ibfk_1` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`),
  ADD CONSTRAINT `propagace_ibfk_2` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`);

--
-- Omezení pro tabulku `views_clanku`
--
ALTER TABLE `views_clanku`
  ADD CONSTRAINT `fk_clanek` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
