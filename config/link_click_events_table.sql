-- Tabulka pro detailní sledování jednotlivých kliků
-- Umožňuje analýzu: kdy, odkud, kdo, jaké zařízení, atd.

CREATE TABLE IF NOT EXISTS `link_click_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_click_id` int(11) NOT NULL COMMENT 'Odkaz na link_clicks.id',
  `id_clanku` int(11) NOT NULL COMMENT 'ID článku (pro rychlejší dotazy)',
  `url` text NOT NULL COMMENT 'URL odkazu (pro rychlejší dotazy)',
  `clicked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Přesný čas kliku',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP adresa (IPv4 nebo IPv6)',
  `user_agent` text DEFAULT NULL COMMENT 'User Agent prohlížeče',
  `referrer` text DEFAULT NULL COMMENT 'Odkud přišel uživatel (HTTP_REFERER)',
  `session_id` varchar(255) DEFAULT NULL COMMENT 'Session ID pro tracking unikátních uživatelů',
  `device_type` enum('desktop','mobile','tablet','bot','unknown') DEFAULT 'unknown' COMMENT 'Typ zařízení',
  `browser` varchar(100) DEFAULT NULL COMMENT 'Název prohlížeče',
  `os` varchar(100) DEFAULT NULL COMMENT 'Operační systém',
  `country` varchar(2) DEFAULT NULL COMMENT 'ISO kód země (např. CZ, US)',
  `city` varchar(100) DEFAULT NULL COMMENT 'Město (pokud je dostupné)',
  `time_on_page` int(11) DEFAULT NULL COMMENT 'Čas strávený na stránce před kliknutím (v sekundách)',
  `link_position` varchar(50) DEFAULT NULL COMMENT 'Pozice odkazu v článku (first, middle, last, top, bottom)',
  `scroll_depth` int(11) DEFAULT NULL COMMENT 'Scroll depth v procentech (0-100)',
  `link_type` varchar(50) DEFAULT NULL COMMENT 'Typ odkazu (external, social, shop, internal, etc.)',
  `viewport_width` int(11) DEFAULT NULL COMMENT 'Šířka viewportu',
  `viewport_height` int(11) DEFAULT NULL COMMENT 'Výška viewportu',
  PRIMARY KEY (`id`),
  KEY `idx_link_click_id` (`link_click_id`),
  KEY `idx_id_clanku_events` (`id_clanku`),
  KEY `idx_clicked_at` (`clicked_at`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_device_type` (`device_type`),
  KEY `idx_link_type` (`link_type`),
  CONSTRAINT `FK_link_clicks_TO_link_click_events` FOREIGN KEY (`link_click_id`) REFERENCES `link_clicks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

