<?php

namespace App\Controllers\Admin;

use App\Helpers\CsrfHelper;

class MigrationAdminController
{
    private $db;
    private $rootPath;
    private $stateFile;

    public function __construct($db)
    {
        $this->db = $db;
        $this->rootPath = dirname(dirname(dirname(__DIR__)));
        $this->stateFile = $this->rootPath . '/web/cache/migration/state.json';
    }

    public function index()
    {
        $state = $this->readState();
        $status = $this->buildStatus();
        $csrfToken = CsrfHelper::generate();

        $adminTitle = 'Migrace | Admin Panel - Cyklistickey magazín';
        $view = '../app/Views/Admin/migration/index.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function autopilot()
    {
        $state = $this->readState();
        $csrfToken = CsrfHelper::generate();

        $adminTitle = 'Migrace (Autopilot) | Admin Panel - Cyklistickey magazín';
        $view = '../app/Views/Admin/migration/autopilot.php';
        include '../app/Views/Admin/layout/base.php';
    }

    public function run($data)
    {
        if (!CsrfHelper::verify($data['csrf_token'] ?? null)) {
            http_response_code(403);
            die('Neplatný CSRF token');
        }

        $task = (string)($data['task'] ?? '');
        $result = $this->runTask($task, $data);
        $state = array_merge($this->readState(), $result);
        if ($task === 'migrate_db' && (int)($data['step'] ?? 0) === 3) {
            $out = (string)($result['last_output'] ?? '');
            if (preg_match('/MIGRATE_DB_NEXT_CURSOR_ID=(-?\d+)/', $out, $m)) {
                $state['article_import_cursor'] = (int)$m[1];
            }
            if (preg_match('/MIGRATE_DB_DONE=1/', $out)) {
                $state['article_import_cursor'] = null;
                $state['article_import_finished_at'] = date('c');
            }
        }
        if ($task === 'truncate_articles' && !empty($result['last_ok'])) {
            unset($state['article_import_cursor'], $state['article_import_finished_at']);
        }
        $this->writeState($state);

        header('Location: /admin/migration');
        exit;
    }

    public function autopilotStart($data = [])
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        if (!CsrfHelper::verify($data['csrf_token'] ?? null)) {
            http_response_code(403);
            die('Neplatný CSRF token');
        }

        $queue = [];
        // 1. Database migration (1-10)
        for ($i = 1; $i <= 10; $i++) {
            $queue[] = ['task' => 'migrate_db', 'step' => $i, 'limit' => 50];
        }

        $state = $this->readState();
        $state['autopilot'] = [
            'running' => true,
            'created_at' => date('c'),
            'queue' => $queue,
            'index' => 0,
            'results' => [],
        ];
        // Reset offsets for clean start
        unset($state['migrate_images']['next_offset']);
        unset($state['migrate_images']['next_offset_by_type']);
        unset($state['assets_sync']['cursor_thumbnails']);
        unset($state['assets_sync']['cursor_article_images']);
        
        $this->writeState($state);

        header('Location: /admin/migration/autopilot');
        exit;
    }

    /**
     * Autopilot: kroky 1–2, pak články po 10 od nejnovějšího (reset DB článků při prvním batchi),
     * pak kroky 4–10.
     */
    public function autopilotArticlesBatchStart($data = [])
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        if (!CsrfHelper::verify($data['csrf_token'] ?? null)) {
            http_response_code(403);
            die('Neplatný CSRF token');
        }

        $queue = [];
        for ($i = 1; $i <= 2; $i++) {
            $queue[] = ['task' => 'migrate_db', 'step' => $i, 'limit' => 50];
        }
        $queue[] = [
            'task' => 'migrate_db',
            'step' => 3,
            'limit' => 10,
            'direction' => 'desc',
            'cursor_id' => 0,
            'reset' => 1,
            '_batch_articles' => true,
        ];
        for ($i = 4; $i <= 10; $i++) {
            $queue[] = ['task' => 'migrate_db', 'step' => $i, 'limit' => 50];
        }

        $state = $this->readState();
        $state['autopilot'] = [
            'running' => true,
            'created_at' => date('c'),
            'queue' => $queue,
            'index' => 0,
            'results' => [],
            'mode' => 'articles_batch',
        ];
        unset($state['migrate_images']['next_offset']);
        unset($state['migrate_images']['next_offset_by_type']);
        unset($state['assets_sync']['cursor_thumbnails']);
        unset($state['assets_sync']['cursor_article_images']);

        $this->writeState($state);

        header('Location: /admin/migration/autopilot');
        exit;
    }

    public function autopilotStop($data = [])
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            die('Method Not Allowed');
        }

        if (!CsrfHelper::verify($data['csrf_token'] ?? null)) {
            http_response_code(403);
            die('Neplatný CSRF token');
        }

        $state = $this->readState();
        $state['autopilot']['running'] = false;
        $state['autopilot']['stopped_at'] = date('c');
        $this->writeState($state);

        header('Location: /admin/migration');
        exit;
    }

    public function autopilotTick($data = [])
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['running' => false, 'error' => 'Method not allowed']);
            exit;
        }

        if (!CsrfHelper::verify($data['csrf_token'] ?? null)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['running' => false, 'error' => 'Neplatný CSRF token']);
            exit;
        }

        $state = $this->readState();
        $autopilot = $state['autopilot'] ?? null;

        if (!$autopilot || empty($autopilot['running']) || empty($autopilot['queue'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['running' => false, 'error' => 'Autopilot neběží']);
            exit;
        }

        $index = (int)($autopilot['index'] ?? 0);
        $queue = $autopilot['queue'];

        if ($index >= count($queue)) {
            $state['autopilot']['running'] = false;
            $state['autopilot']['finished_at'] = date('c');
            $this->writeState($state);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['running' => false, 'done' => true]);
            exit;
        }

        $item = $queue[$index];
        $task = (string)($item['task'] ?? '');
        
        // Merge state into data to allow resuming (offsets, etc.)
        $runData = array_merge($data, $item);
        if ($task === 'migrate_images') {
            $imageType = (string)($item['type'] ?? 'all');
            if (isset($state['migrate_images']['next_offset_by_type'][$imageType])) {
                $runData['offset'] = (int)$state['migrate_images']['next_offset_by_type'][$imageType];
            } elseif (isset($state['migrate_images']['next_offset'])) {
                // Backward compatibility with previous state format.
                $runData['offset'] = (int)$state['migrate_images']['next_offset'];
            }
        }
        if ($task === 'migrate_db' && (int)($item['step'] ?? 0) === 3) {
            foreach (['direction', 'cursor_id', 'reset'] as $k) {
                if (array_key_exists($k, $item)) {
                    $runData[$k] = $item[$k];
                }
            }
        }
        if ($task === 'sync_assets') {
            // syncAssets uses state internally, but we can pass batch
        }

        $result = $this->runTask($task, $runData);

        // Update state with results
        $newState = $this->readState();
        $newState['autopilot']['results'][] = [
            'at' => date('c'),
            'task' => $task,
            'meta' => $item,
            'ok' => (bool)($result['last_ok'] ?? false),
        ];

        // Logic to determine if we should move to next task or repeat
        $shouldMoveToNext = true;
        
        if ($task === 'migrate_images') {
            // Check if migrate_images output says it's NOT done
            if (preg_match('/MIGRATE_IMAGES_DONE=0/', $result['last_output'] ?? '')) {
                $shouldMoveToNext = false;
                // Parse next offset from output if needed, or rely on state
                if (preg_match('/MIGRATE_IMAGES_NEXT_OFFSET=(\d+)/', $result['last_output'], $matches)) {
                    $imageType = (string)($item['type'] ?? 'all');
                    if (preg_match('/MIGRATE_IMAGES_TYPE=([a-z_]+)/', $result['last_output'], $typeMatches)) {
                        $imageType = (string)$typeMatches[1];
                    }
                    $newState['migrate_images']['next_offset_by_type'][$imageType] = (int)$matches[1];
                }
            }
        } elseif ($task === 'sync_assets') {
            if (isset($result['sync_done']) && !$result['sync_done']) {
                $shouldMoveToNext = false;
            }
        } elseif ($task === 'migrate_db') {
            // Repeat only when explicit migration markers request continuation.
            $out = $result['last_output'] ?? '';
            if (preg_match('/MIGRATE_DB_DONE=0/', $out)) {
                if (preg_match('/MIGRATE_DB_NEXT_CURSOR_ID=(-?\d+)/', $out, $matches)) {
                    $shouldMoveToNext = false;
                    $newState['autopilot']['queue'][$index]['cursor_id'] = (int)$matches[1];
                    $newState['autopilot']['queue'][$index]['reset'] = 0;
                } elseif (preg_match('/MIGRATE_DB_NEXT_START_ID=(\d+)/', $out, $matches)) {
                    $shouldMoveToNext = false;
                    $newState['autopilot']['queue'][$index]['start_id'] = (int)$matches[1];
                }
            }
        }

        if ($shouldMoveToNext) {
            $newState['autopilot']['index'] = $index + 1;
        }

        $newState = array_merge($newState, $result);
        $this->writeState($newState);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'running' => (bool)($newState['autopilot']['running'] ?? false),
            'done' => false,
            'index' => (int)($newState['autopilot']['index'] ?? 0),
            'total' => count($queue),
            'last' => [
                'task' => $task,
                'ok' => (bool)($result['last_ok'] ?? false),
                'log' => (string)($result['last_output'] ?? ''),
            ],
            'next_action' => $shouldMoveToNext ? 'next' : 'repeat'
        ]);
        exit;
    }

    private function runTask(string $task, array $data): array
    {
        $startedAt = microtime(true);
        $output = '';
        $ok = false;
        $error = null;
        $extra = [];

        try {
            if ($task === 'migrate_db') {
                $step = (int)($data['step'] ?? 0);
                if ($step < 1 || $step > 10) {
                    throw new \RuntimeException('Neplatný krok migrace');
                }

                $minId = isset($data['min_id']) ? (int)$data['min_id'] : 0;
                $maxId = isset($data['max_id']) ? (int)$data['max_id'] : 0;
                $startId = isset($data['start_id']) ? (int)$data['start_id'] : 0;
                $limit = isset($data['limit']) ? (int)$data['limit'] : 50;
                $direction = isset($data['direction']) ? (string)$data['direction'] : 'asc';
                $cursorId = isset($data['cursor_id']) ? (int)$data['cursor_id'] : 0;
                $reset = isset($data['reset']) ? (int)$data['reset'] : 0;

                $getParams = [
                    'step' => (string)$step,
                    'min_id' => (string)$minId,
                    'max_id' => (string)$maxId,
                    'start_id' => (string)$startId,
                    'limit' => (string)$limit,
                    'direction' => $direction,
                    'cursor_id' => (string)$cursorId,
                    'reset' => (string)$reset,
                ];

                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_db.php', $getParams);
                $ok = true;
            } elseif ($task === 'check_missing_images') {
                $output = $this->buildMissingImagesReport();
                $ok = true;
            } elseif ($task === 'truncate_articles') {
                $tr = $this->truncateArticlesTables();
                $output = $tr['log'];
                $ok = $tr['ok'];
            } elseif ($task === 'migrate_images') {
                $type = (string)($data['type'] ?? 'all');
                $limit = isset($data['limit']) ? (int)$data['limit'] : 50;
                $offset = isset($data['offset']) ? (int)$data['offset'] : 0;
                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_images.php', [
                    'type' => $type,
                    'limit' => (string)$limit,
                    'offset' => (string)$offset,
                ]);
                $ok = true;
            } elseif ($task === 'migrate_users_complete') {
                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_users_complete.php', []);
                $ok = true;
            } elseif ($task === 'migrate_audio_from_db') {
                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_audio_from_db.php', []);
                $ok = true;
            } elseif ($task === 'migrate_audio_table_to_clanky_audio') {
                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_audio_table_to_clanky_audio.php', []);
                $ok = true;
            } elseif ($task === 'migrate_audio_rename') {
                $output = $this->includeScriptWithGet($this->rootPath . '/web/migrate_audio_rename.php', []);
                $ok = true;
            } elseif ($task === 'sync_assets') {
                $mode = (string)($data['mode'] ?? 'all');
                $batch = isset($data['batch']) ? (int)$data['batch'] : 25;
                $res = $this->syncAssets($mode, $batch);
                $output = $res['log'];
                $extra['sync_done'] = $res['done'];
                $ok = true;
            } else {
                throw new \RuntimeException('Neznámý task');
            }
        } catch (\Throwable $e) {
            $ok = false;
            $error = $e->getMessage();
        }

        $durationMs = (int)round((microtime(true) - $startedAt) * 1000);
        $maxLen = $task === 'check_missing_images' ? 500000 : 20000;
        if (strlen($output) > $maxLen) {
            $output = substr($output, 0, $maxLen) . "\n\n... (zkráceno)";
        }

        return array_merge([
            'last_task' => $task,
            'last_ok' => $ok,
            'last_error' => $error,
            'last_output' => $output,
            'last_duration_ms' => $durationMs,
            'last_run_at' => date('c'),
        ], $extra);
    }

    private function includeScriptWithGet(string $scriptPath, array $getParams): string
    {
        if (!file_exists($scriptPath)) {
            throw new \RuntimeException('Skript nenalezen: ' . basename($scriptPath));
        }

        $oldGet = $_GET;
        $oldServer = $_SERVER;

        $_GET = array_merge($_GET, $getParams);
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if (!defined('MIGRATION_INTERNAL_RUN')) {
            define('MIGRATION_INTERNAL_RUN', true);
        }

        $baseLevel = ob_get_level();
        // Use nested buffers so script-level ob_end_clean() does not leak output to HTTP response.
        ob_start();
        ob_start();

        try {
            include $scriptPath;
            $content = (string)ob_get_clean();
            $content = (string)ob_get_clean() . $content;
        } finally {
            while (ob_get_level() > $baseLevel) {
                @ob_end_clean();
            }
        }

        $_GET = $oldGet;
        $_SERVER = $oldServer;

        return (string)$content;
    }

    private function syncAssets(string $mode, int $batch): array
    {
        $batch = max(1, min(200, $batch));

        $credentialsFile = $this->rootPath . '/config/db_credentials.php';
        if (file_exists($credentialsFile)) {
            require_once $credentialsFile;
        }

        $baseUrl = defined('OLD_PUBLIC_BASE_URL') ? rtrim((string)OLD_PUBLIC_BASE_URL, '/') : 'https://www.magazin.cyklistickey.cz';

        $uploadsBase = $this->rootPath . '/web/uploads';
        $paths = [
            'articles' => $uploadsBase . '/articles',
            'thumb_velke' => $uploadsBase . '/thumbnails/velke',
            'thumb_male' => $uploadsBase . '/thumbnails/male',
        ];

        foreach ($paths as $p) {
            if (!is_dir($p)) {
                @mkdir($p, 0755, true);
            }
        }

        $state = $this->readState();
        $cursorThumbnails = (int)($state['assets_sync']['cursor_thumbnails'] ?? 0);
        $cursorArticleImages = (int)($state['assets_sync']['cursor_article_images'] ?? 0);
        
        // Get total counts for progress
        $totalThumbnails = (int)$this->db->query('SELECT COUNT(*) FROM clanky WHERE nahled_foto IS NOT NULL AND nahled_foto != ""')->fetchColumn();
        $totalArticleImages = (int)$this->db->query('SELECT COUNT(*) FROM clanky WHERE obsah IS NOT NULL AND obsah != ""')->fetchColumn();

        $processedInBatch = 0;
        $skipped = 0;
        $failed = 0;
        $downloaded = 0;

        $log = [];
        $log[] = 'SYNC ASSETS: mode=' . $mode . ', batch=' . $batch;
        $log[] = "Progress: Thumbnails $cursorThumbnails/$totalThumbnails, Articles $cursorArticleImages/$totalArticleImages";

        $isDone = true;

        if ($mode === 'all' || $mode === 'thumbnails') {
            if ($cursorThumbnails < $totalThumbnails) {
                $isDone = false;
                $stmt = $this->db->prepare('SELECT id, nahled_foto FROM clanky WHERE nahled_foto IS NOT NULL AND nahled_foto != "" ORDER BY id ASC LIMIT :limit OFFSET :offset');
                $stmt->bindValue(':limit', $batch, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $cursorThumbnails, \PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $file = basename((string)$row['nahled_foto']);
                    if ($file === '') {
                        $skipped++;
                        continue;
                    }

                    $largeOk = $this->downloadIfMissing($baseUrl . '/assets/img/upload/clanek_nahled/' . rawurlencode($file), $paths['thumb_velke'] . '/' . $file, $log);
                    $smallOk = $this->downloadIfMissing($baseUrl . '/assets/img/upload/clanek_nahled/nahled/' . rawurlencode($file), $paths['thumb_male'] . '/' . $file, $log);

                    if ($largeOk) $downloaded++;
                    if ($smallOk) $downloaded++;
                    $processedInBatch++;
                }
                $cursorThumbnails += count($rows);
            }
        }

        if ($mode === 'all' || $mode === 'article_images') {
            if ($cursorArticleImages < $totalArticleImages) {
                $isDone = false;
                $stmt = $this->db->prepare('SELECT id, obsah FROM clanky WHERE obsah IS NOT NULL AND obsah != "" ORDER BY id ASC LIMIT :limit OFFSET :offset');
                $stmt->bindValue(':limit', $batch, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', $cursorArticleImages, \PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $stmtUpdate = $this->db->prepare('UPDATE clanky SET obsah = :obsah WHERE id = :id');

                foreach ($rows as $row) {
                    $id = (int)($row['id'] ?? 0);
                    $origHtml = (string)$row['obsah'];
                    $html = $origHtml;

                    $html = str_replace(
                        [
                            $baseUrl . '/assets/img/upload/clanek_obsah/',
                            'https://www.magazin.cyklistickey.cz/assets/img/upload/clanek_obsah/',
                            '/assets/img/upload/clanek_obsah/',
                            'assets/img/upload/clanek_obsah/',
                        ],
                        '/uploads/articles/',
                        $html
                    );
                    $html = str_replace('/web/uploads/articles/', '/uploads/articles/', $html);

                    $matches = [];
                    preg_match_all('#/uploads/articles/([^"\'\s>\?]+)#', $html, $matches);
                    $files = array_unique(array_map('basename', $matches[1] ?? []));
                    foreach ($files as $file) {
                        if ($file === '') continue;
                        if (file_exists($paths['articles'] . '/' . $file)) {
                            $skipped++;
                            continue;
                        }

                        $ok = $this->downloadIfMissing($baseUrl . '/assets/img/upload/clanek_obsah/' . rawurlencode($file), $paths['articles'] . '/' . $file, $log);
                        if ($ok) $downloaded++; else $failed++;
                    }

                    if ($id > 0 && $html !== $origHtml) {
                        $stmtUpdate->execute([':obsah' => $html, ':id' => $id]);
                        $log[] = 'OK rewrite obsah id=' . $id;
                    }
                    $processedInBatch++;
                }
                $cursorArticleImages += count($rows);
            }
        }

        $newState = $this->readState();
        $newState['assets_sync'] = [
            'cursor_thumbnails' => $cursorThumbnails,
            'cursor_article_images' => $cursorArticleImages,
            'total_thumbnails' => $totalThumbnails,
            'total_article_images' => $totalArticleImages,
            'updated_at' => date('c'),
            'last' => [
                'downloaded' => $downloaded,
                'failed' => $failed,
                'skipped' => $skipped,
                'done_rows' => $processedInBatch,
            ],
        ];
        $this->writeState($newState);

        $log[] = 'Batch finished: downloaded=' . $downloaded . ', failed=' . $failed . ', skipped=' . $skipped;
        if ($isDone) {
            $log[] = '✅ SYNC COMPLETED';
        }

        return [
            'log' => implode("\n", $log),
            'done' => $isDone,
            'counts' => $newState['assets_sync']
        ];
    }

    private function downloadIfMissing(string $url, string $targetPath, array &$log): bool
    {
        if (file_exists($targetPath)) {
            return false;
        }

        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $ctx = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true, 'header' => "User-Agent: MigrationBot\r\n"]]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data === false || strlen($data) === 0) {
            $log[] = 'FAIL ' . $url;
            return false;
        }

        $written = @file_put_contents($targetPath, $data);
        if ($written === false) {
            $log[] = 'FAIL write ' . $targetPath;
            return false;
        }

        $log[] = 'OK ' . $url . ' -> ' . basename($targetPath);
        return true;
    }

    private function buildStatus(): array
    {
        $credentialsFile = $this->rootPath . '/config/db_credentials.php';
        if (file_exists($credentialsFile)) {
            require_once $credentialsFile;
        }

        $oldOk = defined('OLD_DB_HOST') && defined('OLD_DB_NAME') && defined('OLD_DB_USER') && defined('OLD_DB_PASS');
        $newOk = defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS');

        $status = [
            'db' => [
                'old_configured' => $oldOk,
                'new_configured' => $newOk,
            ],
            'counts' => [],
            'uploads' => [],
        ];

        if ($oldOk) {
            $pdoOld = $this->connectExternalDb([
                'host' => (string)OLD_DB_HOST,
                'database' => (string)OLD_DB_NAME,
                'username' => (string)OLD_DB_USER,
                'password' => (string)OLD_DB_PASS,
            ]);
            $status['counts']['old'] = $this->readCounts($pdoOld, [
                'kategorie',
                'users',
                'clanky',
                'kategorie_clanku',
                'propagace',
                'views_clanku',
            ]);

            $stmt = $pdoOld->query('SELECT COUNT(*) FROM password_resets WHERE expires_at >= NOW()');
            $status['counts']['old']['password_resets_valid'] = (int)$stmt->fetchColumn();
        }

        if ($newOk) {
            $pdoNew = $this->connectExternalDb([
                'host' => (string)DB_HOST,
                'database' => (string)DB_NAME,
                'username' => (string)DB_USER,
                'password' => (string)DB_PASS,
            ]);
            $status['counts']['new'] = $this->readCounts($pdoNew, [
                'kategorie',
                'users',
                'clanky',
                'clanky_kategorie',
                'propagace',
                'views_clanku',
                'password_resets',
            ]);

            try {
                $stmt = $pdoNew->query('SELECT COUNT(*) FROM clanky WHERE obsah IS NULL OR obsah = ""');
                $status['counts']['new']['clanky_bez_obsahu'] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $status['counts']['new']['clanky_bez_obsahu'] = null;
            }

            try {
                $stmt = $pdoNew->query('SELECT COUNT(*) FROM clanky WHERE obsah LIKE "%assets/img/upload/clanek_obsah/%" OR obsah LIKE "%/assets/img/upload/clanek_obsah/%"');
                $status['counts']['new']['clanky_obsah_stare_cesty'] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $status['counts']['new']['clanky_obsah_stare_cesty'] = null;
            }

            try {
                $stmt = $pdoNew->query('SELECT COUNT(*) FROM clanky WHERE obsah LIKE "%/web/uploads/articles/%"');
                $status['counts']['new']['clanky_obsah_web_uploads'] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $status['counts']['new']['clanky_obsah_web_uploads'] = null;
            }

            try {
                $stmt = $pdoNew->query('SELECT COUNT(*) FROM clanky WHERE audio IS NOT NULL AND audio != "" AND audio NOT LIKE "/uploads/audio/%"');
                $status['counts']['new']['clanky_audio_neurl'] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $status['counts']['new']['clanky_audio_neurl'] = null;
            }

            try {
                $stmt = $pdoNew->query('SELECT COUNT(*) FROM clanky WHERE (audio IS NULL OR audio = "") AND audio_file IS NOT NULL AND audio_file != ""');
                $status['counts']['new']['clanky_audiofile_bez_url'] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $status['counts']['new']['clanky_audiofile_bez_url'] = null;
            }
        }

        $uploadsBase = $this->rootPath . '/web/uploads';
        $status['uploads'] = [
            'articles' => $this->countFiles($uploadsBase . '/articles'),
            'audio' => $this->countFiles($uploadsBase . '/audio'),
            'thumb_velke' => $this->countFiles($uploadsBase . '/thumbnails/velke'),
            'thumb_male' => $this->countFiles($uploadsBase . '/thumbnails/male'),
            'users' => $this->countFiles($uploadsBase . '/users/thumbnails'),
        ];

        $state = $this->readState();
        $autopilot = $state['autopilot'] ?? [];
        $autopilotTotal = is_array($autopilot['queue'] ?? null) ? count($autopilot['queue']) : 0;
        $autopilotIndex = (int)($autopilot['index'] ?? 0);
        if ($autopilotTotal > 0 && $autopilotIndex > $autopilotTotal) {
            $autopilotIndex = $autopilotTotal;
        }

        $checks = [];
        $countPairs = [
            ['label' => 'Kategorie', 'old' => 'kategorie', 'new' => 'kategorie'],
            ['label' => 'Uživatelé', 'old' => 'users', 'new' => 'users'],
            ['label' => 'Články', 'old' => 'clanky', 'new' => 'clanky'],
            ['label' => 'Vazby články-kategorie', 'old' => 'kategorie_clanku', 'new' => 'clanky_kategorie'],
            ['label' => 'Propagace', 'old' => 'propagace', 'new' => 'propagace'],
            ['label' => 'Views článků', 'old' => 'views_clanku', 'new' => 'views_clanku'],
            ['label' => 'Password resets (valid)', 'old' => 'password_resets_valid', 'new' => 'password_resets'],
        ];

        $okCount = 0;
        $pendingCount = 0;
        $failCount = 0;

        foreach ($countPairs as $pair) {
            $oldVal = $status['counts']['old'][$pair['old']] ?? null;
            $newVal = $status['counts']['new'][$pair['new']] ?? null;

            $stateLabel = 'n/a';
            $stateCode = 'na';
            if ($oldVal !== null && $newVal !== null) {
                if ((int)$oldVal === (int)$newVal) {
                    $stateLabel = 'OK';
                    $stateCode = 'ok';
                    $okCount++;
                } elseif ((int)$newVal < (int)$oldVal) {
                    $stateLabel = 'Chybí';
                    $stateCode = 'pending';
                    $pendingCount++;
                } else {
                    $stateLabel = 'Navíc';
                    $stateCode = 'warn';
                    $failCount++;
                }
            }

            $checks[] = [
                'label' => $pair['label'],
                'old' => $oldVal,
                'new' => $newVal,
                'delta' => ($oldVal !== null && $newVal !== null) ? ((int)$newVal - (int)$oldVal) : null,
                'state' => $stateLabel,
                'state_code' => $stateCode,
            ];
        }

        $contentEmpty = $status['counts']['new']['clanky_bez_obsahu'] ?? null;
        $contentOldPath = $status['counts']['new']['clanky_obsah_stare_cesty'] ?? null;
        $contentWebUploads = $status['counts']['new']['clanky_obsah_web_uploads'] ?? null;

        if ($contentEmpty !== null) {
            $checks[] = [
                'label' => 'Články bez obsahu',
                'old' => null,
                'new' => $contentEmpty,
                'delta' => null,
                'state' => ((int)$contentEmpty === 0) ? 'OK' : 'Nutné opravit',
                'state_code' => ((int)$contentEmpty === 0) ? 'ok' : 'pending',
            ];
            if ((int)$contentEmpty === 0) $okCount++; else $pendingCount++;
        }

        if ($contentOldPath !== null) {
            $checks[] = [
                'label' => 'Staré cesty v obsahu článků',
                'old' => null,
                'new' => $contentOldPath,
                'delta' => null,
                'state' => ((int)$contentOldPath === 0) ? 'OK' : 'Nutné přepsat',
                'state_code' => ((int)$contentOldPath === 0) ? 'ok' : 'pending',
            ];
            if ((int)$contentOldPath === 0) $okCount++; else $pendingCount++;
        }

        if ($contentWebUploads !== null) {
            $checks[] = [
                'label' => 'Cesty začínající /web/uploads v obsahu',
                'old' => null,
                'new' => $contentWebUploads,
                'delta' => null,
                'state' => ((int)$contentWebUploads === 0) ? 'OK' : 'Nutné přepsat',
                'state_code' => ((int)$contentWebUploads === 0) ? 'ok' : 'pending',
            ];
            if ((int)$contentWebUploads === 0) $okCount++; else $pendingCount++;
        }

        $totalChecks = max(1, $okCount + $pendingCount + $failCount);
        $progressPercent = (int)floor(($okCount / $totalChecks) * 100);
        if ($progressPercent < 0) $progressPercent = 0;
        if ($progressPercent > 100) $progressPercent = 100;

        $status['overview'] = [
            'checks' => $checks,
            'summary' => [
                'ok' => $okCount,
                'pending' => $pendingCount,
                'failed' => $failCount,
                'total' => $totalChecks,
                'progress_percent' => $progressPercent,
            ],
            'autopilot' => [
                'running' => (bool)($autopilot['running'] ?? false),
                'index' => $autopilotIndex,
                'total' => $autopilotTotal,
                'created_at' => $autopilot['created_at'] ?? null,
                'finished_at' => $autopilot['finished_at'] ?? null,
                'stopped_at' => $autopilot['stopped_at'] ?? null,
            ],
        ];

        return $status;
    }

    private function countFiles(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        $items = scandir($path);
        $count = 0;
        foreach ($items as $i) {
            if ($i === '.' || $i === '..') {
                continue;
            }
            $full = $path . '/' . $i;
            if (is_file($full)) {
                $count++;
            }
        }
        return $count;
    }

    private function readCounts(\PDO $pdo, array $tables): array
    {
        $out = [];
        foreach ($tables as $t) {
            try {
                $stmt = $pdo->query('SELECT COUNT(*) FROM ' . $t);
                $out[$t] = (int)$stmt->fetchColumn();
            } catch (\Throwable $e) {
                $out[$t] = null;
            }
        }
        return $out;
    }

    private function connectExternalDb(array $config): \PDO
    {
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8mb4';
        $pdo = new \PDO($dsn, $config['username'], $config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 10,
        ]);
        $pdo->exec("SET NAMES 'utf8mb4'");
        return $pdo;
    }

    /**
     * Vyprázdní tabulky clanky_kategorie a clanky (bez mazání kategorií samotných).
     * Vypne kontrolu cizích klíčů na dobu TRUNCATE.
     */
    private function truncateArticlesTables(): array
    {
        $lines = [];
        $lines[] = '=== TRUNCATE článků (FOREIGN_KEY_CHECKS=0) ===';
        $ok = true;

        try {
            $this->db->exec('SET FOREIGN_KEY_CHECKS=0');
        } catch (\Throwable $e) {
            $lines[] = '✗ Nelze vypnout FOREIGN_KEY_CHECKS: ' . $e->getMessage();
            return ['ok' => false, 'log' => implode("\n", $lines)];
        }

        try {
            foreach (['clanky_kategorie', 'clanky'] as $table) {
                try {
                    $this->db->exec('TRUNCATE TABLE `' . str_replace('`', '``', $table) . '`');
                    $lines[] = '✓ TRUNCATE `' . $table . '`';
                } catch (\Throwable $e) {
                    $ok = false;
                    $lines[] = '✗ `' . $table . '`: ' . $e->getMessage();
                }
            }
            if ($ok) {
                $lines[] = 'Hotovo.';
            }
        } finally {
            try {
                $this->db->exec('SET FOREIGN_KEY_CHECKS=1');
                $lines[] = '✓ FOREIGN_KEY_CHECKS=1 (znovu zapnuto)';
            } catch (\Throwable $e) {
                $ok = false;
                $lines[] = '✗ FOREIGN_KEY_CHECKS=1 selhalo: ' . $e->getMessage();
            }
        }

        return ['ok' => $ok, 'log' => implode("\n", $lines)];
    }

    /**
     * Zkontroluje, zda soubory odkazované z obsahu článků (a náhledy) existují na disku.
     */
    private function buildMissingImagesReport(): string
    {
        $articlesDir = $this->rootPath . '/web/uploads/articles';
        $thumbVelke = $this->rootPath . '/web/uploads/thumbnails/velke';
        $thumbMale = $this->rootPath . '/web/uploads/thumbnails/male';

        $lines = [];
        $lines[] = '=== Kontrola obrázků (obsah článků + náhledy) ===';
        $lines[] = 'Složka článků: ' . $articlesDir;
        $lines[] = '';

        try {
            $stmt = $this->db->query('SELECT id, nazev, obsah, nahled_foto FROM clanky ORDER BY id DESC');
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return "Chyba čtení z DB: " . $e->getMessage();
        }

        $totalArticles = count($rows);
        $withMissing = 0;
        $missingFilesTotal = 0;

        foreach ($rows as $row) {
            $id = (int)$row['id'];
            $nazev = (string)($row['nazev'] ?? '');
            $html = (string)($row['obsah'] ?? '');
            $missing = [];

            if ($html !== '') {
                $found = [];
                if (preg_match_all('#/uploads/articles/([^"\'\s<>\?]+)#i', $html, $m)) {
                    foreach ($m[1] as $rawPath) {
                        $file = urldecode(basename($rawPath));
                        if ($file !== '' && $file !== '.' && $file !== '..') {
                            $found[$file] = true;
                        }
                    }
                }
                if (preg_match_all('#/assets/img/upload/clanek_obsah/([^"\'\s<>\?]+)#i', $html, $m2)) {
                    foreach ($m2[1] as $rawPath) {
                        $file = urldecode(basename($rawPath));
                        if ($file !== '' && $file !== '.' && $file !== '..') {
                            $found[$file] = true;
                        }
                    }
                }
                foreach (array_keys($found) as $file) {
                    $path = $articlesDir . DIRECTORY_SEPARATOR . $file;
                    if (!is_file($path)) {
                        $missing[] = $file;
                        $missingFilesTotal++;
                    }
                }
            }

            $nahled = isset($row['nahled_foto']) ? trim((string)$row['nahled_foto']) : '';
            if ($nahled !== '') {
                $fn = basename($nahled);
                $v = $thumbVelke . DIRECTORY_SEPARATOR . $fn;
                $m = $thumbMale . DIRECTORY_SEPARATOR . $fn;
                $thumbMissing = [];
                if (!is_file($v)) {
                    $thumbMissing[] = 'thumbnails/velke/' . $fn;
                }
                if (!is_file($m)) {
                    $thumbMissing[] = 'thumbnails/male/' . $fn;
                }
                if (!empty($thumbMissing)) {
                    $missing[] = 'náhled: ' . implode(', ', $thumbMissing);
                }
            }

            $missing = array_values(array_unique($missing));
            if (!empty($missing)) {
                $withMissing++;
                $lines[] = "Článek ID {$id} — " . $nazev;
                foreach ($missing as $m) {
                    $lines[] = '  • chybí: ' . $m;
                }
                $lines[] = '';
            }
        }

        $lines[] = '---';
        $lines[] = "Článků v DB: {$totalArticles}, článků s nečím chybějícím: {$withMissing}, celkem chybějících položek (soubory + náhledy): {$missingFilesTotal}";

        if ($withMissing === 0) {
            $lines[] = 'OK: u nenalezených problémů s obrázky v obsahu / náhledy (pro nahrané soubory).';
        }

        return implode("\n", $lines);
    }

    private function readState(): array
    {
        $dir = dirname($this->stateFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (!file_exists($this->stateFile)) {
            return [];
        }

        $raw = @file_get_contents($this->stateFile);
        if (!$raw) {
            return [];
        }

        $json = json_decode($raw, true);
        return is_array($json) ? $json : [];
    }

    private function writeState(array $state): void
    {
        $dir = dirname($this->stateFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $tmp = $this->stateFile . '.tmp';
        @file_put_contents($tmp, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        @rename($tmp, $this->stateFile);
    }
}
