-- Aktualizace tabulky admin_access - přidání chybějících routů
-- Vygenerováno: <?= date('Y-m-d H:i:s') ?>

-- Vymazání stávajících dat
DELETE FROM `admin_access`;

-- Vložení kompletních dat
INSERT INTO `admin_access` (`page`, `role_1`, `role_2`) VALUES
-- Základní routes
('statistics', 1, 1),
('statistics/top', 0, 1),
('statistics/view', 0, 1),
('articles', 1, 1),
('articles/create', 1, 1),
('articles/store', 1, 1),
('articles/edit', 1, 1),
('articles/update', 1, 1),
('articles/delete', 0, 1),
('categories', 1, 1),
('categories/create', 0, 0),
('categories/store', 0, 0),
('categories/edit', 0, 0),
('categories/update', 0, 0),
('categories/delete', 0, 0),
('users', 0, 1),
('users/edit', 0, 0),
('users/update', 0, 0),
('users/delete', 0, 0),
('access-control', 0, 0),
('access-control/update', 0, 0),
('logout', 1, 1),
('promotions', 1, 1),
('promotions/create', 0, 1),
('promotions/store', 0, 1),
('promotions/upcoming', 0, 1),
('promotions/history', 0, 1),
('promotions/delete', 0, 1),
('settings', 1, 1),
('settings/update', 1, 1),
('social-sites', 0, 0),
('social-sites/save', 0, 0),
('social-sites/delete', 0, 0),

-- Chybějící statistics routes
('statistics/articles', 0, 1),
('statistics/categories', 0, 1),
('statistics/authors', 0, 1),
('statistics/performance', 0, 1),
('statistics/views', 0, 1),
('statistics/article-details', 0, 1),
('statistics/category-details', 0, 1),
('statistics/author-details', 0, 1),

-- Chybějící routes s parametry
('articles/edit', 1, 1),
('articles/update', 1, 1),
('articles/delete', 0, 1),
('upload-image', 1, 1),
('categories/edit', 0, 0),
('categories/update', 0, 0),
('categories/delete', 0, 0),
('users/edit', 0, 0),
('users/update', 0, 0),
('users/delete', 0, 0),
('promotions/delete', 0, 1),
('social-sites/delete', 0, 0);

COMMIT;
