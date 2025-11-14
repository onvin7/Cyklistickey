-- Tabulka pro seriály závodů (musí být vytvořena první kvůli FOREIGN KEY)
CREATE TABLE IF NOT EXISTS `event_series` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT 'URL slug seriálu',
    `title` VARCHAR(255) NOT NULL COMMENT 'Název seriálu',
    `year` INT NOT NULL COMMENT 'Rok seriálu',
    `description` TEXT NULL COMMENT 'Popis seriálu',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_series_year_name` (`year`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabulka pro závody/events
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT 'URL slug (např. cyklistickey, bezeckey)',
    `title` VARCHAR(255) NOT NULL COMMENT 'Název závodu',
    `year` INT NOT NULL COMMENT 'Rok konání',
    `type` ENUM('individual', 'series') DEFAULT 'individual' COMMENT 'Typ závodu - jednotlivý nebo součást seriálu',
    `series_id` INT NULL COMMENT 'ID seriálu, pokud je součástí seriálu',
    `series_order` INT NULL COMMENT 'Pořadí v seriálu',
    `date` DATE NOT NULL COMMENT 'Datum konání',
    `location` VARCHAR(255) NOT NULL COMMENT 'Místo konání',
    `address` TEXT NULL COMMENT 'Přesná adresa',
    `description` TEXT NULL COMMENT 'Krátký popis',
    `content` LONGTEXT NULL COMMENT 'HTML obsah stránky závodu',
    `registration_url` VARCHAR(500) NULL COMMENT 'URL pro registraci',
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Zda je závod aktivní',
    `background_image` VARCHAR(255) NULL COMMENT 'Obrázek na pozadí',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`series_id`) REFERENCES `event_series`(`id`) ON DELETE SET NULL,
    UNIQUE KEY `unique_event_year_name` (`year`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexy pro lepší výkon
CREATE INDEX `idx_events_year` ON `events`(`year`);
CREATE INDEX `idx_events_type` ON `events`(`type`);
CREATE INDEX `idx_events_active` ON `events`(`is_active`);
CREATE INDEX `idx_events_series` ON `events`(`series_id`);
CREATE INDEX `idx_event_series_year` ON `event_series`(`year`);

