<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-mouse-pointer"></i> Statistiky kliků: <?= htmlspecialchars($article['nazev']) ?></h2>
        <a href="/admin/link-clicks" class="btn btn-action">
            <i class="fa-solid fa-arrow-left"></i> Zpět na přehled
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informace o článku</h5>
                    <p><strong>Název:</strong> <?= htmlspecialchars($article['nazev']) ?></p>
                    <p><strong>ID:</strong> <?= htmlspecialchars($article['id']) ?></p>
                    <p><strong>Celkový počet kliků:</strong> 
                        <span class="badge bg-success" style="font-size: 1.1em;">
                            <?= number_format($totalClicks, 0, ',', ' ') ?>
                        </span>
                    </p>
                    <?php if (isset($uniqueIPs)): ?>
                    <p><strong>Unikátní IP adresy:</strong> 
                        <span class="badge bg-info" style="font-size: 1.1em;">
                            <?= number_format($uniqueIPs, 0, ',', ' ') ?>
                        </span>
                    </p>
                    <?php endif; ?>
                    <?php if (isset($timeOnPageStats) && $timeOnPageStats['total_clicks'] > 0): ?>
                    <p><strong>Průměrný čas na stránce:</strong> 
                        <span class="badge bg-warning text-dark" style="font-size: 1.1em;">
                            <?= number_format($timeOnPageStats['avg_time'] ?? 0, 1, ',', ' ') ?> s
                        </span>
                    </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Akce</h5>
                    <a href="/admin/articles/edit/<?= htmlspecialchars($article['id']) ?>" class="btn btn-primary" style="margin-right: 5px;">
                        <i class="fa-solid fa-pen"></i> Upravit článek
                    </a>
                    <a href="/admin/articles/preview/<?= htmlspecialchars($article['id']) ?>" class="btn btn-success text-white" target="_blank">
                        <i class="fa-solid fa-eye"></i> Náhled článku
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($clicks)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i> V tomto článku zatím nebyly zaznamenány žádné kliky na odkazy.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>URL odkazu</th>
                        <th>Text odkazu</th>
                        <th>Počet kliků</th>
                        <th>První klik</th>
                        <th>Poslední aktualizace</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clicks as $index => $click): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($click['url']) ?>" target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars(strlen($click['url']) > 60 ? substr($click['url'], 0, 60) . '...' : $click['url']) ?>
                                    <i class="fa-solid fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <?= htmlspecialchars($click['link_text'] ?: 'Bez textu') ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success" style="font-size: 1.1em;">
                                    <?= number_format($click['click_count'], 0, ',', ' ') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?= date('d.m.Y H:i', strtotime($click['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?= date('d.m.Y H:i', strtotime($click['updated_at'])) ?>
                            </td>
                            <td class="text-center">
                                <a href="/admin/link-clicks/url/<?= htmlspecialchars($click['id']) ?>" class="btn btn-sm btn-info text-white" title="Zobrazit detailní informace">
                                    <i class="fa-solid fa-info-circle"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php if (!empty($deviceStats) || !empty($browserStats) || !empty($countryStats) || !empty($hourlyStats)): ?>
    <div class="row mt-4">
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

    <?php if (!empty($recentEvents)): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fa-solid fa-list"></i> Poslední kliky (max 50)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Čas</th>
                            <th>URL</th>
                            <th>Zařízení</th>
                            <th>Prohlížeč</th>
                            <th>OS</th>
                            <th>Země</th>
                            <th>IP</th>
                            <th>Čas na stránce</th>
                            <th>Scroll</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentEvents as $event): ?>
                        <tr>
                            <td><?= date('d.m.Y H:i:s', strtotime($event['clicked_at'])) ?></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <a href="<?= htmlspecialchars($event['url']) ?>" target="_blank" title="<?= htmlspecialchars($event['url']) ?>">
                                    <?= htmlspecialchars(strlen($event['url']) > 40 ? substr($event['url'], 0, 40) . '...' : $event['url']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($event['device_type'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($event['browser'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($event['os'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($event['country'] ?? 'N/A') ?></td>
                            <td style="font-size: 0.85em;"><?= htmlspecialchars($event['ip_address'] ?? 'N/A') ?></td>
                            <td><?= $event['time_on_page'] ? number_format($event['time_on_page'], 0, ',', ' ') . 's' : 'N/A' ?></td>
                            <td><?= $event['scroll_depth'] !== null ? number_format($event['scroll_depth'], 0, ',', ' ') . '%' : 'N/A' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

