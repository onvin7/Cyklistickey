<?php

namespace App\Models;

class FlashNewsJSON
{
    private $jsonFile;
    private $data;

    public function __construct()
    {
        $this->jsonFile = __DIR__ . '/../../web/flash.json';
        $this->loadData();
    }

    /**
     * Na─Źte data z JSON souboru
     */
    private function loadData()
    {
        try {
            if (file_exists($this->jsonFile)) {
                $jsonContent = file_get_contents($this->jsonFile);
                $this->data = json_decode($jsonContent, true);
            } else {
                $this->data = [
                    'news' => ['titles' => []],
                    'tech' => ['titles' => []],
                    'custom' => ['titles' => []]
                ];
            }
        } catch (Exception $e) {
            error_log('FlashNewsJSON loadData error: ' . $e->getMessage());
            $this->data = [
                'news' => ['titles' => []],
                'tech' => ['titles' => []],
                'custom' => ['titles' => []]
            ];
        }
    }

    /**
     * Ulo┼ż├ş data do JSON souboru
     */
    private function saveData()
    {
        try {
            $jsonContent = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($this->jsonFile, $jsonContent) !== false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON saveData error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Z├şsk├í v┼íechny flash news
     */
    public function getAll()
    {
        $allNews = [];
        $id = 1;

        // News
        foreach ($this->data['news']['titles'] ?? [] as $item) {
            $allNews[] = [
                'id' => $id++,
                'title' => $item['title'],
                'type' => 'news',
                'is_active' => $item['is_active'] ?? 1,
                'sort_order' => $item['sort_order'] ?? 0,
                'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $item['updated_at'] ?? date('Y-m-d H:i:s'),
                'created_by_name' => $item['created_by_name'] ?? 'API'
            ];
        }

        // Tech
        foreach ($this->data['tech']['titles'] ?? [] as $item) {
            $allNews[] = [
                'id' => $id++,
                'title' => $item['title'],
                'type' => 'tech',
                'is_active' => $item['is_active'] ?? 1,
                'sort_order' => $item['sort_order'] ?? 0,
                'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $item['updated_at'] ?? date('Y-m-d H:i:s'),
                'created_by_name' => $item['created_by_name'] ?? 'API'
            ];
        }

        // Custom
        foreach ($this->data['custom']['titles'] ?? [] as $item) {
            $allNews[] = [
                'id' => $id++,
                'title' => $item['title'],
                'type' => 'custom',
                'is_active' => $item['is_active'] ?? 1,
                'sort_order' => $item['sort_order'] ?? 0,
                'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $item['updated_at'] ?? date('Y-m-d H:i:s'),
                'created_by_name' => $item['created_by_name'] ?? 'Admin'
            ];
        }

        // Se┼Öadit podle sort_order a created_at
        usort($allNews, function($a, $b) {
            if ($a['sort_order'] == $b['sort_order']) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            }
            return $a['sort_order'] - $b['sort_order'];
        });

        return $allNews;
    }

    /**
     * Z├şsk├í flash news podle ID
     */
    public function getById($id)
    {
        $allNews = $this->getAll();
        foreach ($allNews as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Vytvo┼Ö├ş novou flash news
     */
    public function create($data)
    {
        try {
            $newItem = [
                'title' => $data['title'],
                'is_active' => $data['is_active'] ?? 1,
                'sort_order' => $data['sort_order'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by_name' => $data['created_by_name'] ?? 'Admin'
            ];

            $type = $data['type'] ?? 'custom';
            if (!isset($this->data[$type]['titles'])) {
                $this->data[$type]['titles'] = [];
            }

            $this->data[$type]['titles'][] = $newItem;
            return $this->saveData();
        } catch (Exception $e) {
            error_log('FlashNewsJSON create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aktualizuje flash news
     */
    public function update($id, $data)
    {
        try {
            $allNews = $this->getAll();
            $found = false;

            foreach ($allNews as $index => $item) {
                if ($item['id'] == $id) {
                    $type = $item['type'];
                    $arrayIndex = $this->findArrayIndex($type, $index);
                    
                    if ($arrayIndex !== false) {
                        $this->data[$type]['titles'][$arrayIndex]['title'] = $data['title'];
                        $this->data[$type]['titles'][$arrayIndex]['is_active'] = $data['is_active'] ?? 1;
                        $this->data[$type]['titles'][$arrayIndex]['sort_order'] = $data['sort_order'] ?? 0;
                        $this->data[$type]['titles'][$arrayIndex]['updated_at'] = date('Y-m-d H:i:s');
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                return $this->saveData();
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sma┼że flash news
     */
    public function delete($id)
    {
        try {
            $allNews = $this->getAll();
            $found = false;

            foreach ($allNews as $index => $item) {
                if ($item['id'] == $id) {
                    $type = $item['type'];
                    $arrayIndex = $this->findArrayIndex($type, $index);
                    
                    if ($arrayIndex !== false) {
                        unset($this->data[$type]['titles'][$arrayIndex]);
                        $this->data[$type]['titles'] = array_values($this->data[$type]['titles']);
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                return $this->saveData();
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * P┼Öepne aktivn├ş stav flash news
     */
    public function toggleActive($id)
    {
        try {
            $allNews = $this->getAll();
            $found = false;

            foreach ($allNews as $index => $item) {
                if ($item['id'] == $id) {
                    $type = $item['type'];
                    $arrayIndex = $this->findArrayIndex($type, $index);
                    
                    if ($arrayIndex !== false) {
                        $this->data[$type]['titles'][$arrayIndex]['is_active'] = 
                            $this->data[$type]['titles'][$arrayIndex]['is_active'] ? 0 : 1;
                        $this->data[$type]['titles'][$arrayIndex]['updated_at'] = date('Y-m-d H:i:s');
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                return $this->saveData();
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON toggleActive error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aktualizuje po┼Öad├ş flash news
     */
    public function updateSortOrder($id, $sortOrder)
    {
        try {
            $allNews = $this->getAll();
            $found = false;

            foreach ($allNews as $index => $item) {
                if ($item['id'] == $id) {
                    $type = $item['type'];
                    $arrayIndex = $this->findArrayIndex($type, $index);
                    
                    if ($arrayIndex !== false) {
                        $this->data[$type]['titles'][$arrayIndex]['sort_order'] = $sortOrder;
                        $this->data[$type]['titles'][$arrayIndex]['updated_at'] = date('Y-m-d H:i:s');
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                return $this->saveData();
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON updateSortOrder error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Z├şsk├í flash news pro zobrazen├ş na webu
     */
    public function getForDisplay()
    {
        $displayNews = [];
        
        // News
        foreach ($this->data['news']['titles'] ?? [] as $item) {
            if (($item['is_active'] ?? 1) == 1) {
                $displayNews[] = [
                    'title' => $item['title'],
                    'type' => 'news'
                ];
            }
        }

        // Tech
        foreach ($this->data['tech']['titles'] ?? [] as $item) {
            if (($item['is_active'] ?? 1) == 1) {
                $displayNews[] = [
                    'title' => $item['title'],
                    'type' => 'tech'
                ];
            }
        }

        // Custom
        foreach ($this->data['custom']['titles'] ?? [] as $item) {
            if (($item['is_active'] ?? 1) == 1) {
                $displayNews[] = [
                    'title' => $item['title'],
                    'type' => 'custom'
                ];
            }
        }

        return $displayNews;
    }

    /**
     * Z├şsk├í statistiky flash news
     */
    public function getStats()
    {
        $allNews = $this->getAll();
        $stats = [
            'total' => count($allNews),
            'active' => 0,
            'inactive' => 0,
            'news_count' => 0,
            'tech_count' => 0,
            'custom_count' => 0
        ];

        foreach ($allNews as $item) {
            if ($item['is_active']) {
                $stats['active']++;
            } else {
                $stats['inactive']++;
            }

            if ($item['type'] === 'news') {
                $stats['news_count']++;
            } elseif ($item['type'] === 'tech') {
                $stats['tech_count']++;
            } else {
                $stats['custom_count']++;
            }
        }

        return $stats;
    }

    /**
     * Najde index v poli podle po┼Öad├ş v getAll()
     */
    private function findArrayIndex($type, $globalIndex)
    {
        $currentIndex = 0;
        $types = ['news', 'tech', 'custom'];
        
        foreach ($types as $t) {
            if ($t === $type) {
                $count = count($this->data[$t]['titles'] ?? []);
                if ($globalIndex < $currentIndex + $count) {
                    return $globalIndex - $currentIndex;
                }
            }
            $currentIndex += count($this->data[$t]['titles'] ?? []);
        }
        
        return false;
    }

    /**
     * Aktualizuje data z API
     */
    public function refreshFromAPI()
    {
        try {
            // Zavolej flash_config.php pro aktualizaci
            $configFile = __DIR__ . '/../../web/flash_config.php';
            if (file_exists($configFile)) {
                ob_start();
                include $configFile;
                ob_end_clean();
                $this->loadData();
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSON refreshFromAPI error: ' . $e->getMessage());
            return false;
        }
    }
}


