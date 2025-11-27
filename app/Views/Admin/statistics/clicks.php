<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-mouse-pointer me-2"></i>Statistiky kliků</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Kliky</li>
    </ol>
    
    <!-- Filtry -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-filter me-2"></i>Filtr časového období</h2>
        </div>
        <div class="p-3">
            <form id="period-filter" class="row g-3" method="GET">
                <div class="col-md-4">
                    <select class="form-select form-select-sm shadow-none border" id="period" name="period">
                        <option value="7" <?= $days == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                        <option value="30" <?= $days == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                        <option value="90" <?= $days == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                        <option value="365" <?= $days == 365 ? 'selected' : '' ?>>Poslední rok</option>
                        <option value="custom" <?= $customPeriod ? 'selected' : '' ?>>Vlastní období</option>
                    </select>
                </div>
                <div class="col-md-4 custom-period <?= $customPeriod ? '' : 'd-none' ?>">
                    <input type="date" class="form-control form-control-sm shadow-none border" id="start-date" name="start-date" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4 custom-period <?= $customPeriod ? '' : 'd-none' ?>">
                    <input type="date" class="form-control form-control-sm shadow-none border" id="end-date" name="end-date" value="<?= $endDate ?>">
                </div>
            </form>
        </div>
    </section>
    
    <!-- Souhrnné karty -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #9b59b6;"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($totalClicks) ?></div>
                        <div class="stat-label">Celkový počet kliků</div>
                    </div>
                    <div class="stat-icon" style="color: #9b59b6;">
                        <i class="fa-solid fa-mouse-pointer"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #8e44ad;"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($avgClicksPerArticle, 1) ?></div>
                        <div class="stat-label">Průměr kliků na článek</div>
                    </div>
                    <div class="stat-icon" style="color: #8e44ad;">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #7d3c98;"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= $topLink && isset($topLink['click_count']) ? number_format($topLink['click_count']) : '0' ?></div>
                        <div class="stat-label">Top odkaz</div>
                        <?php if ($topLink && isset($topLink['url'])): ?>
                        <div class="small text-muted mt-1" style="font-size: 0.75rem; word-break: break-all;">
                            <?= htmlspecialchars(mb_strlen($topLink['url']) > 40 ? mb_substr($topLink['url'], 0, 40) . '...' : $topLink['url']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="stat-icon" style="color: #7d3c98;">
                        <i class="fa-solid fa-link"></i>
                    </div>
                </div>
                <?php if ($topLink && isset($topLink['id'])): ?>
                <div class="pt-2 mt-2 border-top px-3 pb-3">
                    <a href="/admin/link-clicks/url/<?= $topLink['id'] ?>" class="btn btn-sm btn-primary w-100">Zobrazit detail <i class="fas fa-angle-right ms-1"></i></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #6c3483;"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($articlesWithClicks) ?></div>
                        <div class="stat-label">Články s kliky</div>
                    </div>
                    <div class="stat-icon" style="color: #6c3483;">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend kliků v čase -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-line me-2"></i>Trend kliků v čase</h2>
        </div>
        <div class="p-0">
            <div id="clicksTrendChart" style="height: 350px;"></div>
        </div>
    </section>

    <!-- Grafy -->
    <div class="row">
        <div class="col-xl-8">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-trophy me-2"></i>Top 10 článků podle kliků</h2>
                </div>
                <div class="p-0">
                    <div id="topArticlesByClicksChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-clock me-2"></i>Kliky podle hodin</h2>
                </div>
                <div class="p-0">
                    <div id="clicksByHourChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-tags me-2"></i>Kliky podle kategorií</h2>
                </div>
                <div class="p-0">
                    <div id="clicksByCategoryChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-week me-2"></i>Kliky podle dnů v týdnu</h2>
                </div>
                <div class="p-0">
                    <div id="clicksByDayOfWeekChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <!-- Tabulky -->
    <div class="row">
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-newspaper me-2"></i>Top články podle kliků</h2>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th class="px-3 py-3 border-bottom">Název článku</th>
                                    <th class="px-3 py-3 border-bottom">Autor</th>
                                    <th class="px-3 py-3 border-bottom text-center">Kliky</th>
                                    <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                                    <th class="px-3 py-3 border-bottom text-center">CTR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topArticlesByClicks)): ?>
                                    <?php foreach ($topArticlesByClicks as $article): ?>
                                        <?php 
                                        $ctr = ($article['total_views'] > 0) ? ($article['total_clicks'] / $article['total_views']) * 100 : 0;
                                        ?>
                                        <tr class="align-middle">
                                            <td class="px-3 py-3 border-bottom">
                                                <a href="/admin/article/edit/<?= $article['id'] ?>" class="text-decoration-none fw-medium">
                                                    <?= htmlspecialchars($article['nazev']) ?>
                                                </a>
                                            </td>
                                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['autor'] ?? '-') ?></td>
                                            <td class="px-3 py-3 border-bottom text-center fw-bold" style="color: #9b59b6;">
                                                <?= number_format($article['total_clicks'] ?? 0) ?>
                                            </td>
                                            <td class="px-3 py-3 border-bottom text-center">
                                                <?= number_format($article['total_views'] ?? 0) ?>
                                            </td>
                                            <td class="px-3 py-3 border-bottom text-center">
                                                <span class="badge rounded-pill bg-info px-2 py-1"><?= number_format($ctr, 2) ?>%</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i> Žádné kliky k zobrazení.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-link me-2"></i>Top odkazy</h2>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-light">
                                    <th class="px-3 py-3 border-bottom">URL</th>
                                    <th class="px-3 py-3 border-bottom">Článek</th>
                                    <th class="px-3 py-3 border-bottom text-center">Kliky</th>
                                    <th class="px-3 py-3 border-bottom text-center">Akce</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($topLinks)): ?>
                                    <?php foreach ($topLinks as $link): ?>
                                        <tr class="align-middle">
                                            <td class="px-3 py-3 border-bottom">
                                                <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" rel="noopener noreferrer" class="text-decoration-none" title="<?= htmlspecialchars($link['url']) ?>">
                                                    <?= htmlspecialchars(mb_strlen($link['url']) > 50 ? mb_substr($link['url'], 0, 50) . '...' : $link['url']) ?>
                                                </a>
                                            </td>
                                            <td class="px-3 py-3 border-bottom">
                                                <?php if ($link['article_id']): ?>
                                                    <a href="/admin/article/edit/<?= $link['article_id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($link['article_name'] ?? 'Neznámý článek') ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-3 py-3 border-bottom text-center fw-bold" style="color: #9b59b6;">
                                                <?= number_format($link['click_count']) ?>
                                            </td>
                                            <td class="px-3 py-3 border-bottom text-center">
                                                <a href="/admin/link-clicks/url/<?= $link['id'] ?>" class="btn btn-sm btn-info text-white">
                                                    <i class="fas fa-info-circle"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i> Žádné odkazy k zobrazení.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Kliky podle kategorií - tabulka -->
    <?php if (!empty($clicksByCategory)): ?>
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-tags me-2"></i>Kliky podle kategorií</h2>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Kategorie</th>
                            <th class="px-3 py-3 border-bottom text-center">Kliky</th>
                            <th class="px-3 py-3 border-bottom text-center">Počet článků</th>
                            <th class="px-3 py-3 border-bottom text-center">Průměr kliků/článek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clicksByCategory as $category): ?>
                            <tr class="align-middle">
                                <td class="px-3 py-3 border-bottom">
                                    <a href="/admin/statistics/categories" class="text-decoration-none fw-medium">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                </td>
                                <td class="px-3 py-3 border-bottom text-center fw-bold" style="color: #9b59b6;">
                                    <?= number_format($category['clicks']) ?>
                                </td>
                                <td class="px-3 py-3 border-bottom text-center">
                                    <?= number_format($category['articles_count']) ?>
                                </td>
                                <td class="px-3 py-3 border-bottom text-center">
                                    <?php 
                                    $avgClicks = $category['articles_count'] > 0 ? $category['clicks'] / $category['articles_count'] : 0;
                                    echo number_format($avgClicks, 1);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nastavení filtru pro vlastní období
        const periodFilter = document.getElementById('period-filter');
        const periodSelect = document.getElementById('period');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const customPeriodElements = document.querySelectorAll('.custom-period');
        
        function submitForm() {
            periodFilter.submit();
        }
        
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customPeriodElements.forEach(el => el.classList.remove('d-none'));
            } else {
                customPeriodElements.forEach(el => el.classList.add('d-none'));
                submitForm();
            }
        });
        
        let dateChangeTimer;
        const dateChangeHandler = function() {
            clearTimeout(dateChangeTimer);
            dateChangeTimer = setTimeout(submitForm, 500);
        };
        
        startDateInput.addEventListener('change', dateChangeHandler);
        endDateInput.addEventListener('change', dateChangeHandler);

        // Kontrola ApexCharts
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts není načten!');
            document.querySelectorAll('[id$="Chart"]').forEach(el => {
                el.innerHTML = '<div class="alert alert-danger">Nepodařilo se načíst grafy. Obnovte stránku.</div>';
            });
            return;
        }

        // GRAF TRENDU KLIKŮ
        const clicksTrendOptions = {
            chart: {
                type: 'line',
                height: 350,
                zoom: { enabled: true },
                toolbar: { show: false }
            },
            stroke: {
                curve: 'straight',
                width: 2
            },
            series: [{
                name: 'Kliky',
                data: <?= json_encode($clicksTrendValues) ?>
            }],
            xaxis: {
                categories: <?= json_encode($clicksTrendLabels) ?>,
                labels: {
                    rotate: -45,
                    style: { fontSize: '10px' }
                }
            },
            yaxis: {
                title: { text: 'Počet kliků' },
                min: 0
            },
            colors: ['#9b59b6'],
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' kliků';
                    }
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            }
        };
        
        const clicksTrendChart = new ApexCharts(document.querySelector("#clicksTrendChart"), clicksTrendOptions);
        clicksTrendChart.render();

        // GRAF TOP 10 ČLÁNKŮ PODLE KLIKŮ
        <?php if (!empty($topArticlesByClicks)): ?>
        const topArticlesByClicksSeries = <?= json_encode(array_column($topArticlesByClicks, 'total_clicks')) ?>;
        const topArticlesByClicksLabels = <?= json_encode(array_map(function($article) { 
            return mb_strlen($article['nazev']) > 30 ? mb_substr($article['nazev'], 0, 30) . '...' : $article['nazev']; 
        }, $topArticlesByClicks)) ?>;
        
        const topArticlesByClicksOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            series: [{
                name: 'Počet kliků',
                data: topArticlesByClicksSeries
            }],
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: { position: 'top' },
                    borderRadius: 4,
                    distributed: true
                }
            },
            colors: ['#9b59b6', '#8e44ad', '#7d3c98', '#6c3483', '#5b2c6f', '#4a235a', '#391a45', '#2d1430', '#1f0e1b', '#0a0508'],
            xaxis: {
                categories: topArticlesByClicksLabels,
                title: { text: 'Počet kliků' }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) { return val; },
                offsetX: 10,
                style: { fontSize: '12px', colors: ['#333'] }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' kliků';
                    }
                }
            }
        };
        
        const topArticlesByClicksChart = new ApexCharts(document.querySelector("#topArticlesByClicksChart"), topArticlesByClicksOptions);
        topArticlesByClicksChart.render();
        <?php endif; ?>

        // GRAF KLIKŮ PODLE HODIN
        const hourlyData = <?= json_encode(array_values($hourlyData)) ?>;
        const clicksByHourOptions = {
            chart: {
                type: 'line',
                height: 350,
                toolbar: { show: false }
            },
            series: [{
                name: 'Kliky',
                data: hourlyData
            }],
            xaxis: {
                categories: Array.from({length: 24}, (_, i) => i + ':00'),
                title: { text: 'Hodina' }
            },
            yaxis: {
                title: { text: 'Počet kliků' },
                min: 0
            },
            colors: ['#9b59b6'],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            markers: {
                size: 4,
                hover: { size: 6 }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' kliků';
                    }
                }
            }
        };
        
        const clicksByHourChart = new ApexCharts(document.querySelector("#clicksByHourChart"), clicksByHourOptions);
        clicksByHourChart.render();

        // GRAF KLIKŮ PODLE KATEGORIÍ
        <?php if (!empty($clicksByCategory)): ?>
        const categoryClicksData = <?= json_encode(array_column($clicksByCategory, 'clicks')) ?>;
        const categoryClicksLabels = <?= json_encode(array_column($clicksByCategory, 'name')) ?>;
        
        const clicksByCategoryOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            series: [{
                name: 'Počet kliků',
                data: categoryClicksData
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            xaxis: {
                categories: categoryClicksLabels,
                labels: {
                    rotate: -45,
                    style: { fontSize: '10px' }
                }
            },
            yaxis: {
                title: { text: 'Počet kliků' },
                min: 0
            },
            colors: ['#9b59b6'],
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', colors: ['#333'] }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' kliků';
                    }
                }
            }
        };
        
        const clicksByCategoryChart = new ApexCharts(document.querySelector("#clicksByCategoryChart"), clicksByCategoryOptions);
        clicksByCategoryChart.render();
        <?php endif; ?>

        // GRAF KLIKŮ PODLE DNŮ V TÝDNU
        const dayOfWeekData = <?= json_encode(array_values($dayOfWeekData)) ?>;
        const dayOfWeekLabels = <?= json_encode($dayOfWeekLabels) ?>;
        
        const clicksByDayOfWeekOptions = {
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false }
            },
            series: [{
                name: 'Počet kliků',
                data: dayOfWeekData
            }],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            xaxis: {
                categories: dayOfWeekLabels
            },
            yaxis: {
                title: { text: 'Počet kliků' },
                min: 0
            },
            colors: ['#9b59b6'],
            dataLabels: {
                enabled: true,
                style: { fontSize: '10px', colors: ['#333'] }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' kliků';
                    }
                }
            }
        };
        
        const clicksByDayOfWeekChart = new ApexCharts(document.querySelector("#clicksByDayOfWeekChart"), clicksByDayOfWeekOptions);
        clicksByDayOfWeekChart.render();
    });
</script>

