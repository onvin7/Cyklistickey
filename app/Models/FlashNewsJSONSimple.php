<?php

namespace App\Models;

use Exception;

class FlashNewsJSONSimple
{
    private $jsonFile;
    private $data;

    public function __construct()
    {
        $this->jsonFile = __DIR__ . '/../../web/flash.json';
        $this->loadData();
    }

    private function loadData(): void
    {
        try {
            if (file_exists($this->jsonFile)) {
                $jsonContent = file_get_contents($this->jsonFile);
                $this->data = json_decode($jsonContent, true);
            } else {
                $this->data = [];
            }
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple loadData error: ' . $e->getMessage());
            $this->data = [];
        }

        $this->ensureStructure();
    }

    private function ensureStructure(): void
    {
        foreach (['news', 'tech', 'custom'] as $type) {
            if (!isset($this->data[$type]) || !is_array($this->data[$type])) {
                $this->data[$type] = ['titles' => []];
            }

            if (!isset($this->data[$type]['titles']) || !is_array($this->data[$type]['titles'])) {
                $this->data[$type]['titles'] = [];
        }
    }
    }

    private function saveData(): bool
    {
        try {
            $jsonContent = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($this->jsonFile, $jsonContent) !== false;
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple saveData error: ' . $e->getMessage());
            return false;
        }
    }

    private function getMaxSortOrder(): int
    {
        $max = 0;
        foreach (['news', 'tech', 'custom'] as $type) {
            foreach ($this->data[$type]['titles'] as $item) {
                if (isset($item['sort_order']) && is_numeric($item['sort_order'])) {
                    $value = (int)$item['sort_order'];
                    if ($value > $max) {
                        $max = $value;
                    }
                }
            }
        }

        return $max;
    }

    public function getNextSortOrder(): int
    {
        return $this->getMaxSortOrder() + 1;
    }

    private function ensureSortOrderInitialized(): void
    {
        $changed = false;
        $max = $this->getMaxSortOrder();

        foreach (['news', 'tech', 'custom'] as $type) {
            foreach ($this->data[$type]['titles'] as &$item) {
                if (!isset($item['sort_order']) || !is_numeric($item['sort_order'])) {
                    $max++;
                    $item['sort_order'] = $max;
                    $changed = true;
                }
                if (!isset($item['is_active'])) {
                    $item['is_active'] = 1;
                    $changed = true;
                }
                if (!isset($item['created_at'])) {
                    $item['created_at'] = date('Y-m-d H:i:s');
                    $changed = true;
                }
                if (!isset($item['updated_at'])) {
                    $item['updated_at'] = date('Y-m-d H:i:s');
                    $changed = true;
                }
                if (!isset($item['created_by_name'])) {
                    $item['created_by_name'] = 'API';
                    $changed = true;
        }
            }
        }
        unset($item);

        if ($changed) {
            $this->saveData();
        }
    }

    public function getAll(): array
    {
        $this->ensureSortOrderInitialized();

        $allNews = [];
        $globalIndex = 0;

        // Nejdřív načti všechny položky se stabilním ID
        foreach (['news', 'tech', 'custom'] as $type) {
            foreach ($this->data[$type]['titles'] as $index => $item) {
            $allNews[] = [
                    'id' => ++$globalIndex,
                    'title' => $item['title'] ?? '',
                    'type' => $type,
                    'is_active' => (int)($item['is_active'] ?? 1),
                    'sort_order' => (int)($item['sort_order'] ?? 0),
                'created_at' => $item['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at' => $item['updated_at'] ?? date('Y-m-d H:i:s'),
                    'created_by_name' => $item['created_by_name'] ?? ($type === 'custom' ? 'Admin' : 'API'),
                    'internal_type' => $type,
                    'internal_index' => $index,
                    'stable_id' => $type . '_' . $index, // Stabilní ID pro reorder
            ];
            }
        }

        // Seřaď podle sort_order
        usort($allNews, function ($a, $b) {
            if ($a['sort_order'] === $b['sort_order']) {
                return strtotime($b['created_at']) <=> strtotime($a['created_at']);
            }

            return $a['sort_order'] <=> $b['sort_order'];
        });

        return $allNews;
    }

    private function getAllIndexedById(): array
    {
        $indexed = [];
        $globalIndex = 0;
        
        // Vytvoř index podle stabilního ID (type_index)
        foreach (['news', 'tech', 'custom'] as $type) {
            foreach ($this->data[$type]['titles'] as $index => $item) {
                $stableId = $type . '_' . $index;
                $indexed[++$globalIndex] = [
                    'id' => $globalIndex,
                    'internal_type' => $type,
                    'internal_index' => $index,
                    'stable_id' => $stableId,
                ];
            }
        }
        
        return $indexed;
    }

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

    public function create($data)
    {
        try {
            $type = in_array($data['type'] ?? 'custom', ['news', 'tech', 'custom'], true)
                ? $data['type']
                : 'custom';

            $sortOrder = $data['sort_order'] ?? null;
            if (!$sortOrder || $sortOrder <= 0) {
                $sortOrder = $this->getNextSortOrder();
            }

            $newItem = [
                'title' => $data['title'],
                'is_active' => $data['is_active'] ?? 1,
                'sort_order' => (int)$sortOrder,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by_name' => $data['created_by_name'] ?? 'Admin'
            ];

            $this->data[$type]['titles'][] = $newItem;

            return $this->normalizeSortOrders();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple create error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $indexed = $this->getAllIndexedById();

            if (!isset($indexed[$id])) {
                return false;
            }

            $itemInfo = $indexed[$id];
            $currentType = $itemInfo['internal_type'];
            $currentIndex = $itemInfo['internal_index'];
                    
            $entry =& $this->data[$currentType]['titles'][$currentIndex];
            $entry['title'] = $data['title'];
            $entry['is_active'] = $data['is_active'] ?? 1;
            if (!empty($data['sort_order']) && $data['sort_order'] > 0) {
                $entry['sort_order'] = (int)$data['sort_order'];
            }
            $entry['updated_at'] = date('Y-m-d H:i:s');

            $targetType = $data['type'] ?? $currentType;
            if (!in_array($targetType, ['news', 'tech', 'custom'], true)) {
                $targetType = $currentType;
            }

            if ($targetType !== $currentType) {
                $movedEntry = $entry;
                unset($this->data[$currentType]['titles'][$currentIndex]);
                $this->data[$currentType]['titles'] = array_values($this->data[$currentType]['titles']);
                $this->data[$targetType]['titles'][] = $movedEntry;
            }

            unset($entry);

            return $this->normalizeSortOrders();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple update error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $indexed = $this->getAllIndexedById();

            if (!isset($indexed[$id])) {
                return false;
            }

            $itemInfo = $indexed[$id];
            $type = $itemInfo['internal_type'];
            $index = $itemInfo['internal_index'];
                    
            unset($this->data[$type]['titles'][$index]);
                        $this->data[$type]['titles'] = array_values($this->data[$type]['titles']);

            return $this->normalizeSortOrders();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple delete error: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleActive($id)
    {
        try {
            $indexed = $this->getAllIndexedById();

            if (!isset($indexed[$id])) {
                return false;
            }

            $itemInfo = $indexed[$id];
            $type = $itemInfo['internal_type'];
            $index = $itemInfo['internal_index'];
                    
            $current = $this->data[$type]['titles'][$index]['is_active'] ?? 1;
            $this->data[$type]['titles'][$index]['is_active'] = $current ? 0 : 1;
            $this->data[$type]['titles'][$index]['updated_at'] = date('Y-m-d H:i:s');

                return $this->saveData();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple toggleActive error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateSortOrder($id, $sortOrder)
    {
        if ($sortOrder <= 0) {
            return false;
        }

        try {
            $indexed = $this->getAllIndexedById();

            if (!isset($indexed[$id])) {
                return false;
            }

            $itemInfo = $indexed[$id];
            $type = $itemInfo['internal_type'];
            $index = $itemInfo['internal_index'];
                    
            $this->data[$type]['titles'][$index]['sort_order'] = (int)$sortOrder;
            $this->data[$type]['titles'][$index]['updated_at'] = date('Y-m-d H:i:s');

            return $this->normalizeSortOrders();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple updateSortOrder error: ' . $e->getMessage());
            return false;
        }
    }

    public function reorder(array $orderedIds)
    {
        try {
            // Nejdřív načti všechna data seřazená podle aktuálního pořadí
            $allNews = [];
            $globalIndex = 0;
            
            foreach (['news', 'tech', 'custom'] as $type) {
                foreach ($this->data[$type]['titles'] as $index => $item) {
                    $allNews[++$globalIndex] = [
                        'type' => $type,
                        'index' => $index,
                    ];
                }
            }
            
            // Aktualizuj sort_order podle nového pořadí
            $position = 1;
            foreach ($orderedIds as $id) {
                if (!isset($allNews[$id])) {
                    continue;
                }
                $itemInfo = $allNews[$id];
                $type = $itemInfo['type'];
                $index = $itemInfo['index'];
                $this->data[$type]['titles'][$index]['sort_order'] = $position++;
                $this->data[$type]['titles'][$index]['updated_at'] = date('Y-m-d H:i:s');
            }

            // Položky, které nebyly v orderedIds, dostanou pořadí na konec
            foreach ($allNews as $id => $itemInfo) {
                if (in_array($id, $orderedIds, true)) {
                    continue;
                }
                $type = $itemInfo['type'];
                $index = $itemInfo['index'];
                $this->data[$type]['titles'][$index]['sort_order'] = $position++;
                $this->data[$type]['titles'][$index]['updated_at'] = date('Y-m-d H:i:s');
            }

            return $this->normalizeSortOrders();
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple reorder error: ' . $e->getMessage());
            return false;
            }
        }

    private function normalizeSortOrders()
    {
        $this->ensureSortOrderInitialized();

        $allNews = $this->getAll();
        $position = 1;
        foreach ($allNews as $item) {
            $type = $item['internal_type'];
            $index = $item['internal_index'];
            $this->data[$type]['titles'][$index]['sort_order'] = $position++;
        }

        return $this->saveData();
    }

    public function getForDisplay()
    {
        $visible = [];
        foreach ($this->getAll() as $item) {
            if ($item['is_active']) {
                $visible[] = [
                    'id' => $item['id'],
                    'title' => $item['title'],
                    'type' => $item['type'],
                    'sort_order' => $item['sort_order']
                ];
            }
        }
        return $visible;
    }

    public function getStats()
    {
        $stats = [
            'total' => 0,
            'active' => 0,
            'inactive' => 0,
            'news_count' => 0,
            'tech_count' => 0,
            'custom_count' => 0
        ];

        foreach ($this->getAll() as $item) {
            $stats['total']++;
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

    public function refreshFromAPI()
    {
        try {
            $configFile = __DIR__ . '/../../web/flash_config.php';
            if (file_exists($configFile)) {
                ob_start();
                include $configFile;
                ob_end_clean();
                $this->loadData();
                $this->ensureSortOrderInitialized();
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log('FlashNewsJSONSimple refreshFromAPI error: ' . $e->getMessage());
            return false;
        }
    }
}
