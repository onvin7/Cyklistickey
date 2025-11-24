-- Přidání sloupce public_visible do tabulky users
-- Tento sloupec určuje, zda má být uživatel zobrazen v sekci redakce
-- Výchozí hodnota 1 = viditelný (zajišťuje zpětnou kompatibilitu)

ALTER TABLE `users` 
ADD COLUMN `public_visible` TINYINT(1) NOT NULL DEFAULT 1 
AFTER `role`;


