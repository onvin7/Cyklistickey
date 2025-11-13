<?php

namespace App\Controllers\Admin;

use App\Helpers\CSRFHelper;
use App\Helpers\TrackingHelper;

class TrackingAdminController
{
    /**
     * Zobraz├ş str├ínku pro spr├ívu tracking k├│d┼»
     */
    public function index()
    {
        $seoConfigFile = __DIR__ . '/../../../web/config/seo_config.json';
        $seoConfig = json_decode(file_get_contents($seoConfigFile), true);
        
        $trackingConfig = $seoConfig['tracking'] ?? [
            'meta_pixel_id' => 'YOUR_META_PIXEL_ID',
            'google_analytics_id' => 'YOUR_GA_ID',
            'enabled' => false
        ];

        include __DIR__ . '/../../Views/Admin/tracking/index.php';
    }

    /**
     * Ulo┼ż├ş tracking konfiguraci
     */
    public function update()
    {
        if (!CSRFHelper::validateToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Neplatn├Ż CSRF token';
            header('Location: /admin/tracking');
            exit;
        }

        try {
            $seoConfigFile = __DIR__ . '/../../../web/config/seo_config.json';
            $seoConfig = json_decode(file_get_contents($seoConfigFile), true);
            
            // Aktualizuj tracking konfiguraci
            $seoConfig['tracking'] = [
                'meta_pixel_id' => trim($_POST['meta_pixel_id'] ?? ''),
                'google_analytics_id' => trim($_POST['google_analytics_id'] ?? ''),
                'enabled' => isset($_POST['enabled'])
            ];

            // Ulo┼ż zp─Ťt do souboru
            $jsonContent = json_encode($seoConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($seoConfigFile, $jsonContent)) {
                $_SESSION['success'] = 'Tracking konfigurace byla ├║sp─Ť┼ín─Ť ulo┼żena';
            } else {
                $_SESSION['error'] = 'Chyba p┼Öi ukl├íd├ín├ş konfigurace';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Chyba: ' . $e->getMessage();
        }

        header('Location: /admin/tracking');
        exit;
    }

    /**
     * Testuje tracking k├│dy
     */
    public function test()
    {
        $metaPixelId = TrackingHelper::getMetaPixelId();
        $gaId = TrackingHelper::getGoogleAnalyticsId();
        $isEnabled = TrackingHelper::isTrackingEnabled();

        $results = [
            'meta_pixel' => [
                'id' => $metaPixelId,
                'valid' => !empty($metaPixelId) && $metaPixelId !== 'YOUR_META_PIXEL_ID',
                'enabled' => $isEnabled
            ],
            'google_analytics' => [
                'id' => $gaId,
                'valid' => !empty($gaId) && $gaId !== 'YOUR_GA_ID',
                'enabled' => $isEnabled
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
}
