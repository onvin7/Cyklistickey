<?php

require_once 'config/db.php';

try {
    $db = (new Database())->connect();
    
    // SQL pro vytvoření tabulky
    $sql = "
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
    
    CREATE INDEX IF NOT EXISTS idx_flash_news_active ON flash_news(is_active);
    CREATE INDEX IF NOT EXISTS idx_flash_news_type ON flash_news(type);
    CREATE INDEX IF NOT EXISTS idx_flash_news_sort ON flash_news(sort_order);
    ";
    
    $db->exec($sql);
    
    echo "✅ Tabulka flash_news byla úspěšně vytvořena!\n";
    
    // Vložení ukázkových dat
    $sampleData = [
        [
            'title' => 'Vítejte na našem cyklistickém magazínu!',
            'type' => 'custom',
            'is_active' => 1,
            'sort_order' => 1
        ],
        [
            'title' => 'Nejnovější cyklistické novinky a recenze',
            'type' => 'news',
            'is_active' => 1,
            'sort_order' => 2
        ],
        [
            'title' => 'Technické inovace v cyklistice',
            'type' => 'tech',
            'is_active' => 1,
            'sort_order' => 3
        ]
    ];
    
    $stmt = $db->prepare("INSERT INTO flash_news (title, type, is_active, sort_order) VALUES (?, ?, ?, ?)");
    
    foreach ($sampleData as $data) {
        $stmt->execute([$data['title'], $data['type'], $data['is_active'], $data['sort_order']]);
    }
    
    echo "✅ Ukázková data byla vložena!\n";
    echo "🎉 Flash News administrace je připravena k použití!\n";
    
} catch (Exception $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
}

