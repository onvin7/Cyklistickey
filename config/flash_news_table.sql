-- Tabulka pro flash news
CREATE TABLE IF NOT EXISTS flash_news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    type ENUM('news', 'tech', 'custom') DEFAULT 'custom',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexy pro lepší výkon
CREATE INDEX idx_flash_news_active ON flash_news(is_active);
CREATE INDEX idx_flash_news_type ON flash_news(type);
CREATE INDEX idx_flash_news_sort ON flash_news(sort_order);

