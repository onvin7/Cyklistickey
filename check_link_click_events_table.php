<?php
/**
 * Skript pro kontrolu existence tabulky link_click_events
 * Spusť tento soubor v prohlížeči nebo z příkazové řádky
 */

require __DIR__ . '/config/db.php';

try {
    $db = (new Database())->connect();
    
    // Kontrola existence tabulky
    $query = "SHOW TABLES LIKE 'link_click_events'";
    $stmt = $db->query($query);
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ Tabulka 'link_click_events' existuje.\n\n";
        
        // Zobrazení struktury tabulky
        $query = "DESCRIBE link_click_events";
        $stmt = $db->query($query);
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Struktura tabulky:\n";
        echo str_repeat("-", 80) . "\n";
        printf("%-20s %-15s %-10s %-10s %-10s\n", "Field", "Type", "Null", "Key", "Default");
        echo str_repeat("-", 80) . "\n";
        foreach ($columns as $column) {
            printf("%-20s %-15s %-10s %-10s %-10s\n", 
                $column['Field'], 
                $column['Type'], 
                $column['Null'], 
                $column['Key'], 
                $column['Default'] ?? 'NULL'
            );
        }
        
        // Počet záznamů
        $query = "SELECT COUNT(*) as count FROM link_click_events";
        $stmt = $db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\n📊 Počet záznamů: " . $result['count'] . "\n";
        
        // Posledních 5 záznamů
        if ($result['count'] > 0) {
            $query = "SELECT * FROM link_click_events ORDER BY clicked_at DESC LIMIT 5";
            $stmt = $db->query($query);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nPosledních 5 záznamů:\n";
            echo str_repeat("-", 80) . "\n";
            foreach ($events as $event) {
                echo "ID: {$event['id']}, Článek: {$event['id_clanku']}, Čas: {$event['clicked_at']}, IP: {$event['ip_address']}\n";
            }
        }
        
    } else {
        echo "❌ Tabulka 'link_click_events' NEEXISTUJE!\n\n";
        echo "Musíš spustit SQL migraci:\n";
        echo "config/link_click_events_table.sql\n\n";
        echo "Obsah migrace:\n";
        echo file_get_contents(__DIR__ . '/config/link_click_events_table.sql');
    }
    
} catch (Exception $e) {
    echo "❌ Chyba: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

