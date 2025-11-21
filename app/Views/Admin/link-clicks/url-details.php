<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-link"></i> Detail odkazu</h2>
        <a href="/admin/link-clicks/article/<?= htmlspecialchars($linkClick['id_clanku']) ?>" class="btn btn-action">
            <i class="fa-solid fa-arrow-left"></i> Zpět na článek
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informace o odkazu</h5>
                    <p><strong>URL:</strong> 
                        <a href="<?= htmlspecialchars($linkClick['url']) ?>" target="_blank" rel="noopener noreferrer">
                            <?= htmlspecialchars($linkClick['url']) ?>
                            <i class="fa-solid fa-external-link-alt ms-1"></i>
                        </a>
                    </p>
                    <p><strong>Text odkazu:</strong> <?= htmlspecialchars($linkClick['link_text'] ?: 'Bez textu') ?></p>
                    <p><strong>Článek:</strong> 
                        <a href="/admin/link-clicks/article/<?= htmlspecialchars($linkClick['id_clanku']) ?>">
                            <?= htmlspecialchars($linkClick['nazev_clanku'] ?? 'Neznámý článek') ?>
                        </a>
                    </p>
                    <p><strong>Celkový počet kliků:</strong> 
                        <span class="badge bg-success" style="font-size: 1.1em;">
                            <?= number_format($linkClick['click_count'], 0, ',', ' ') ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5>Časové údaje</h5>
                    <p><strong>První klik:</strong> <?= date('d.m.Y H:i:s', strtotime($linkClick['created_at'])) ?></p>
                    <p><strong>Poslední aktualizace:</strong> <?= date('d.m.Y H:i:s', strtotime($linkClick['updated_at'])) ?></p>
                    <?php if ($uniqueIPs > 0): ?>
                    <p><strong>Unikátní IP adresy:</strong> 
                        <span class="badge bg-info" style="font-size: 1.1em;">
                            <?= number_format($uniqueIPs, 0, ',', ' ') ?>
                        </span>
                    </p>
                    <?php endif; ?>
                    <?php if ($timeOnPageStats && $timeOnPageStats['total_clicks'] > 0): ?>
                    <p><strong>Průměrný čas na stránce:</strong> 
                        <span class="badge bg-warning text-dark" style="font-size: 1.1em;">
                            <?= number_format($timeOnPageStats['avg_time'] ?? 0, 1, ',', ' ') ?> s
                        </span>
                        (min: <?= number_format($timeOnPageStats['min_time'], 0, ',', ' ') ?>s, 
                         max: <?= number_format($timeOnPageStats['max_time'], 0, ',', ' ') ?>s)
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($deviceStats) || !empty($browserStats) || !empty($countryStats) || !empty($hourlyStats)): ?>
    <div class="row mb-4">
        <?php if (!empty($deviceStats)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-mobile-screen-button"></i> Zařízení</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Typ</th>
                                <th class="text-end">Počet</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $deviceTotal = array_sum(array_column($deviceStats, 'count'));
                            foreach ($deviceStats as $stat): 
                                $percentage = $deviceTotal > 0 ? ($stat['count'] / $deviceTotal) * 100 : 0;
                                $deviceLabels = [
                                    'desktop' => 'Desktop',
                                    'mobile' => 'Mobil',
                                    'tablet' => 'Tablet',
                                    'bot' => 'Bot',
                                    'unknown' => 'Neznámé'
                                ];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($deviceLabels[$stat['device_type']] ?? $stat['device_type']) ?></td>
                                <td class="text-end"><?= number_format($stat['count'], 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($percentage, 1, ',', ' ') ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($browserStats)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-globe"></i> Prohlížeče</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Prohlížeč</th>
                                <th class="text-end">Počet</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $browserTotal = array_sum(array_column($browserStats, 'count'));
                            foreach ($browserStats as $stat): 
                                $percentage = $browserTotal > 0 ? ($stat['count'] / $browserTotal) * 100 : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['browser']) ?></td>
                                <td class="text-end"><?= number_format($stat['count'], 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($percentage, 1, ',', ' ') ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($countryStats)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-earth-europe"></i> Země</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Země</th>
                                <th class="text-end">Počet</th>
                                <th class="text-end">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $countryTotal = array_sum(array_column($countryStats, 'count'));
                            foreach ($countryStats as $stat): 
                                $percentage = $countryTotal > 0 ? ($stat['count'] / $countryTotal) * 100 : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['country']) ?></td>
                                <td class="text-end"><?= number_format($stat['count'], 0, ',', ' ') ?></td>
                                <td class="text-end"><?= number_format($percentage, 1, ',', ' ') ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($hourlyStats)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa-solid fa-clock"></i> Časové rozložení (po hodinách)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Hodina</th>
                                <th class="text-end">Počet kliků</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hourlyStats as $stat): ?>
                            <tr>
                                <td><?= str_pad($stat['hour'], 2, '0', STR_PAD_LEFT) ?>:00</td>
                                <td class="text-end"><?= number_format($stat['count'], 0, ',', ' ') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($events)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i> Pro tento odkaz zatím nebyly zaznamenány žádné detailní kliky.
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h5><i class="fa-solid fa-list"></i> Všechny kliky (<?= count($events) ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Čas</th>
                                <th>IP adresa</th>
                                <th>Zařízení</th>
                                <th>Prohlížeč</th>
                                <th>OS</th>
                                <th>Země</th>
                                <th>Město</th>
                                <th>Referrer</th>
                                <th>Čas na stránce</th>
                                <th>Scroll</th>
                                <th>Pozice</th>
                                <th>Typ</th>
                                <th>Viewport</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td style="white-space: nowrap;">
                                    <?= date('d.m.Y H:i:s', strtotime($event['clicked_at'])) ?>
                                </td>
                                <td style="font-size: 0.85em;">
                                    <?= htmlspecialchars($event['ip_address'] ?? 'N/A') ?>
                                </td>
                                <td>
                                    <?php
                                    $deviceLabels = [
                                        'desktop' => 'Desktop',
                                        'mobile' => 'Mobil',
                                        'tablet' => 'Tablet',
                                        'bot' => 'Bot',
                                        'unknown' => 'Neznámé'
                                    ];
                                    echo htmlspecialchars($deviceLabels[$event['device_type']] ?? $event['device_type'] ?? 'N/A');
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($event['browser'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($event['os'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($event['country'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($event['city'] ?? 'N/A') ?></td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($event['referrer'] ?? '') ?>">
                                    <?= htmlspecialchars($event['referrer'] ? (strlen($event['referrer']) > 40 ? substr($event['referrer'], 0, 40) . '...' : $event['referrer']) : 'N/A') ?>
                                </td>
                                <td class="text-center">
                                    <?= $event['time_on_page'] !== null ? number_format($event['time_on_page'], 0, ',', ' ') . 's' : 'N/A' ?>
                                </td>
                                <td class="text-center">
                                    <?= $event['scroll_depth'] !== null ? number_format($event['scroll_depth'], 0, ',', ' ') . '%' : 'N/A' ?>
                                </td>
                                <td>
                                    <?php
                                    $positionLabels = [
                                        'first' => 'První',
                                        'middle' => 'Střed',
                                        'last' => 'Poslední',
                                        'top' => 'Nahoře',
                                        'bottom' => 'Dole',
                                        'only' => 'Jediný'
                                    ];
                                    echo htmlspecialchars($positionLabels[$event['link_position']] ?? $event['link_position'] ?? 'N/A');
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $typeLabels = [
                                        'external' => 'Externí',
                                        'social' => 'Sociální',
                                        'shop' => 'E-shop'
                                    ];
                                    echo htmlspecialchars($typeLabels[$event['link_type']] ?? $event['link_type'] ?? 'N/A');
                                    ?>
                                </td>
                                <td style="font-size: 0.85em;">
                                    <?php if ($event['viewport_width'] && $event['viewport_height']): ?>
                                        <?= $event['viewport_width'] ?>×<?= $event['viewport_height'] ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

