-- Migrace: Přidání sloupce public_visible do tabulky users
-- Datum: 2025-01-XX
-- Popis: Přidává boolean pole pro kontrolu veřejné viditelnosti uživatelů v sekci redakce

ALTER TABLE `users` 
ADD COLUMN `public_visible` TINYINT(1) NOT NULL DEFAULT 1 
AFTER `role`;

-- Nastavení všech existujících uživatelů jako veřejně viditelných (pro zpětnou kompatibilitu)
UPDATE `users` SET `public_visible` = 1 WHERE `public_visible` IS NULL OR `public_visible` = 0;

