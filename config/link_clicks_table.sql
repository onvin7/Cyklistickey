-- Tabulka pro sledování kliků na odkazy v článcích
CREATE TABLE IF NOT EXISTS `link_clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_clanku` int(11) NOT NULL,
  `url` text NOT NULL,
  `link_text` varchar(500) DEFAULT NULL,
  `click_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_id_clanku_link_clicks` (`id_clanku`),
  KEY `idx_url_link_clicks` (`url`(255)),
  CONSTRAINT `FK_clanky_TO_link_clicks` FOREIGN KEY (`id_clanku`) REFERENCES `clanky` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

