<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-eye me-2"></i>Statistiky zobrazení</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Zobrazení</li>
    </ol>
    
    <!-- Filtry -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-filter me-2"></i>Filtr časového období</h2>
        </div>
        <div class="p-3">
            <form id="period-filter" class="row g-3" method="GET">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-calendar me-1"></i></span>
                        <select class="form-select shadow-none border-start-0" id="period" name="period">
                            <option value="7" <?= isset($period) && $period == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                            <option value="30" <?= isset($period) && $period == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                            <option value="90" <?= isset($period) && $period == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                            <option value="365" <?= isset($period) && $period == 365 ? 'selected' : '' ?>>Poslední rok</option>
                            <option value="all" <?= isset($period) && $period == 'all' ? 'selected' : '' ?>>Všechna data</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </section>
    
    <!-- Souhrné statistiky -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--primary-color);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= isset($totalViews) ? number_format($totalViews) : '0' ?></div>
                        <div class="stat-label">Celkem zobrazení</div>
                    </div>
                    <div class="stat-icon" style="color: var(--primary-color);">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-2);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= isset($avgViewsPerDay) ? number_format($avgViewsPerDay, 1) : '0.0' ?></div>
                        <div class="stat-label">Průměr za den</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-2);">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-3);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= isset($mostViewedDay) && isset($mostViewedDay['date']) ? date('d.m.Y', strtotime($mostViewedDay['date'])) : '-' ?></div>
                        <div class="stat-label">Nejnavštěvovanější den</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-3);">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-4);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= isset($maxViewsInDay) ? number_format($maxViewsInDay) : '0' ?></div>
                        <div class="stat-label">Maximum zobrazení za den</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-4);">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Trend zobrazení v čase -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-line me-2"></i>Trend zobrazení v čase</h2>
        </div>
        <div class="p-0">
            <div id="viewsTrendChart" style="height: 350px;"></div>
        </div>
    </section>
    
    <!-- Grafy zobrazení podle dnů a času -->
    <div class="row">
        <div class="col-xl-12">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-calendar-week me-2"></i>Zobrazení podle dnů v týdnu</h2>
                </div>
                <div class="p-0">
                    <div id="viewsByDayOfWeekChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <!-- Tepelná mapa návštěvnosti za rok -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-calendar-alt me-2"></i>Roční přehled návštěvnosti</h2>
        </div>
        <div class="p-3">
            <div id="calendarHeatmap" style="height: 250px;"></div>
            <div class="d-flex justify-content-center align-items-center mt-2">
                <span class="text-muted me-2" style="font-size: 12px;">Méně</span>
                <div class="gradient-legend">
                    <div style="height: 15px; width: 150px; background: linear-gradient(to right, #ebedf0, #9be9a8, #40c463, #30a14e, #216e39);"></div>
                </div>
                <span class="text-muted ms-2" style="font-size: 12px;">Více</span>
            </div>
        </div>
    </section>

    <!-- Nejčtenější články -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-award me-2"></i>Top 20 článků podle zobrazení</h2>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="topArticlesTable" class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Název článku</th>
                            <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                            <th class="px-3 py-3 border-bottom text-center">% z celkem</th>
                            <th class="px-3 py-3 border-bottom text-center">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($topArticles) && is_array($topArticles) && count($topArticles) > 0): ?>
                            <?php foreach ($topArticles as $article): ?>
                                <?php $percentOfTotal = (isset($totalViews) && $totalViews > 0) ? ($article['total_views'] / $totalViews * 100) : 0; ?>
                                <tr class="align-middle">
                                    <td class="px-3 py-3 border-bottom">
                                        <?php if (isset($article['id']) && isset($article['nazev'])): ?>
                                            <a href="/admin/article/edit/<?= $article['id'] ?>" class="text-decoration-none fw-medium">
                                                <?= htmlspecialchars($article['nazev']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Bez názvu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-3 border-bottom text-center fw-bold">
                                        <?= isset($article['total_views']) ? number_format($article['total_views']) : '0' ?>
                                    </td>
                                    <td class="px-3 py-3 border-bottom text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress" style="width: 70px; height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= min(100, $percentOfTotal) ?>%; background-color: var(--primary-color);" aria-valuenow="<?= $percentOfTotal ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2"><?= number_format($percentOfTotal, 1) ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 border-bottom text-center">
                                        <?php if (isset($article['id'])): ?>
                                            <button type="button" class="btn btn-sm btn-primary view-detail" 
                                                    data-article-id="<?= $article['id'] ?>" 
                                                    data-article-title="<?= htmlspecialchars($article['nazev'] ?? 'Bez názvu') ?>">
                                                <i class="fas fa-chart-line"></i> Detail
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-chart-line"></i> Detail
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Nejsou k dispozici žádné údaje o zobrazení článků.
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

<!-- Modal pro detail článku -->
<div class="modal fade" id="articleDetailModal" tabindex="-1" aria-labelledby="articleDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="articleDetailModalLabel">Detail článku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-primary" id="detail-total-views">0</h3>
                                <p class="text-muted">Celkem zobrazení</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-success" id="detail-avg-views">0</h3>
                                <p class="text-muted">Průměr za den</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-info" id="detail-percent">0%</h3>
                                <p class="text-muted">% z celkových zobrazení</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="article-trend-chart" style="height: 300px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
        // Automatické odeslání formuláře při změně hodnoty filtru
        document.getElementById('period').addEventListener('change', function() {
            document.getElementById('period-filter').submit();
        });
        
        // DataTable pro nejčtenější články
        $('#topArticlesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/cs.json',
                lengthMenu: "Zobrazit _MENU_ záznamů",
                info: "Zobrazeno _START_ až _END_ z _TOTAL_ záznamů"
            },
            order: [[1, 'desc']], // Řazení podle počtu zobrazení
            paging: false,
            searching: false,
            info: false,
            columnDefs: [
                { orderable: false, targets: 3 } // Sloupec "Akce" není řaditelný
            ]
        });
        
        // Vytvoření ukázkových dat pro grafy, pokud žádná reálná data nejsou k dispozici
        function createSampleData(days = 7) {
            let data = [];
            let today = new Date();
            
            for (let i = 0; i < days; i++) {
                let date = new Date();
                date.setDate(today.getDate() - i);
                
                data.push({
                    date: date.toISOString().split('T')[0],
                    count: 0
                });
            }
            
            return data.reverse(); // Seřadíme od nejstaršího k nejnovějšímu
        }
        
        // Vytvoření vzorových dat pro dny v týdnu, pokud žádná reálná data nejsou k dispozici
        function createSampleDaysOfWeek() {
            return [
                { day_of_week: 0, count: 0 }, // Neděle
                { day_of_week: 1, count: 0 }, // Pondělí
                { day_of_week: 2, count: 0 }, // Úterý
                { day_of_week: 3, count: 0 }, // Středa
                { day_of_week: 4, count: 0 }, // Čtvrtek
                { day_of_week: 5, count: 0 }, // Pátek
                { day_of_week: 6, count: 0 }  // Sobota
            ];
        }
        
        // Získat data pro zobrazení nebo použít vzorovévá data
        const viewsTrendData = <?= !empty($viewsTrend) && is_array($viewsTrend) ? json_encode($viewsTrend) : 'createSampleData(7)' ?>;
        const viewsByDayOfWeekData = <?= !empty($viewsByDayOfWeek) && is_array($viewsByDayOfWeek) ? json_encode($viewsByDayOfWeek) : 'createSampleDaysOfWeek()' ?>;
        
        // Grafy
        const viewsTrendOptions = {
            chart: {
                type: 'area',
                height: 350,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            series: [{
                name: 'Zobrazení',
                data: viewsTrendData.map(item => item.count || 0)
            }],
            xaxis: {
                type: 'category',
                categories: viewsTrendData.map(item => {
                    if (item.date) {
                        const date = new Date(item.date);
                        return date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
                    }
                    return '-';
                }),
                labels: {
                    rotate: -45,
                    rotateAlways: false,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " zobrazení";
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            colors: ['var(--primary-color)']
        };

        const viewsTrendChart = new ApexCharts(document.querySelector("#viewsTrendChart"), viewsTrendOptions);
        viewsTrendChart.render();

        // Graf zobrazení podle dnů v týdnu
        const dayNames = ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'];
        
        const viewsByDayOfWeekOptions = {
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            series: [{
                name: 'Zobrazení',
                data: viewsByDayOfWeekData.map(item => item.count || 0)
            }],
            xaxis: {
                categories: viewsByDayOfWeekData.map(item => dayNames[item.day_of_week] || '-'),
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " zobrazení";
                    }
                }
            },
            colors: ['var(--chart-color-2)']
        };

        const viewsByDayOfWeekChart = new ApexCharts(document.querySelector("#viewsByDayOfWeekChart"), viewsByDayOfWeekOptions);
        viewsByDayOfWeekChart.render();

        // Tepelná mapa návštěvnosti
        function prepareCalendarData() {
            // Připravíme data pro tepelnou mapu z dat z controlleru nebo použijeme vzorová data
            <?php if (isset($calendarHeatmap) && isset($calendarHeatmap['calendar_data']) && is_array($calendarHeatmap['calendar_data'])): ?>
                // Použijeme reálná data
                const calendarData = <?= json_encode($calendarHeatmap['calendar_data']) ?>;
                const maxViews = <?= isset($calendarHeatmap['max_views']) ? $calendarHeatmap['max_views'] : 0 ?>;
                const avgViews = <?= isset($calendarHeatmap['avg_views']) ? $calendarHeatmap['avg_views'] : 0 ?>;
                
                return {
                    calendarData: calendarData,
                    maxViews: maxViews,
                    avgViews: avgViews
                };
            <?php else: ?>
                // Použijeme vzorová data za poslední rok, pokud nemáme reálná data
                const today = new Date();
                const oneYearAgo = new Date();
                oneYearAgo.setFullYear(today.getFullYear() - 1);
                oneYearAgo.setDate(today.getDate() + 1); // Začínáme od zítřka před rokem
                
                let calendarData = [];
                
                // Vytvoříme náhodná data pro poslední rok
                let currentDate = new Date(oneYearAgo);
                while (currentDate <= today) {
                    // Vygenerujeme náhodná data, ale dáme vyšší hodnoty pro aktuální týden
                    const isCurrentWeek = isDateInCurrentWeek(currentDate);
                    const count = isCurrentWeek 
                        ? Math.floor(Math.random() * 30) + 20 // 20-50 pro aktuální týden
                        : Math.floor(Math.random() * 50);     // 0-50 pro ostatní dny
                    
                    calendarData.push({
                        date: currentDate.toISOString().split('T')[0],
                        count: count,
                        month: currentDate.getMonth(),
                        day_of_week: currentDate.getDay(),
                        day_of_month: currentDate.getDate(),
                        is_current_week: isCurrentWeek
                    });
                    
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                return {
                    calendarData: calendarData,
                    maxViews: 50,
                    avgViews: 25
                };
            <?php endif; ?>
        }
        
        // Funkce pro kontrolu, zda datum spadá do aktuálního týdne
        function isDateInCurrentWeek(date) {
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay()); // Neděle je začátek týdne (0)
            startOfWeek.setHours(0, 0, 0, 0);
            
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6); // Sobota je konec týdne
            endOfWeek.setHours(23, 59, 59, 999);
            
            return date >= startOfWeek && date <= endOfWeek;
        }
        
        const calendarData = prepareCalendarData();
        
        // Měsíce v češtině pro popisky
        const monthsInCzech = ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 
            'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'];
            
        // Dny v týdnu v češtině pro popisky
        const daysInCzech = ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'];

        // Funkce pro získání dat posledního roku (ve stylu GitHub)
        function generateLastYearCalendarSeries() {
            const series = [];
            
            // Získáme data pro poslední rok
            const today = new Date();
            const oneYearAgo = new Date();
            oneYearAgo.setFullYear(today.getFullYear() - 1);
            oneYearAgo.setDate(today.getDate() + 1);
            
            // Pro každý den v týdnu (0 = neděle, 6 = sobota)
            for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                const dayData = [];
                
                // Spočítáme první datum pro tento den v týdnu
                let currentDate = new Date(oneYearAgo);
                
                // Posuneme na správný den v týdnu
                while (currentDate.getDay() !== dayOfWeek) {
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                // Nyní máme první datum pro tento den v týdnu, pokračujeme přes celý rok
                while (currentDate <= today) {
                    const dateStr = currentDate.toISOString().split('T')[0];
                    let value = 0;
                    
                    // Najdeme odpovídající data v našem datasetu
                    const dayDataItem = calendarData.calendarData.find(d => d.date === dateStr);
                    if (dayDataItem) {
                        value = dayDataItem.count;
                    }
                    
                    dayData.push({
                        x: dateStr,
                        y: value
                    });
                    
                    // Posuneme se na další týden (stejný den v týdnu)
                    currentDate.setDate(currentDate.getDate() + 7);
                }
                
                // Přidáme data do série
                series.push({
                    name: daysInCzech[dayOfWeek],
                    data: dayData
                });
            }
            
            return series;
        }
        
        // Funkce pro získání dat aktuálního týdne (pro anotace)
        function getCurrentWeekDates() {
            const today = new Date();
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay()); // Neděle je začátek týdne (0)
            startOfWeek.setHours(0, 0, 0, 0);
            
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6); // Sobota je konec týdne
            endOfWeek.setHours(23, 59, 59, 999);
            
            return {
                start: startOfWeek.toISOString().split('T')[0],
                end: endOfWeek.toISOString().split('T')[0]
            };
        }

        // Vytvoření heat mapy ve stylu GitHub contribution calendar
        const calendarHeatmapOptions = {
            chart: {
                height: 250,
                type: 'heatmap',
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: false
                }
            },
            tooltip: {
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const value = series[seriesIndex][dataPointIndex];
                    const date = w.globals.seriesX[seriesIndex][dataPointIndex];
                    const dateObj = new Date(date);
                    const formattedDate = `${dateObj.getDate()}. ${monthsInCzech[dateObj.getMonth()].substring(0, 3)} ${dateObj.getFullYear()}`;
                                        
                    // Zjistíme, zda je datum v aktuálním týdnu
                    const isCurrentWeek = isDateInCurrentWeek(dateObj);
                    const currentWeekBadge = isCurrentWeek ? 
                        '<span style="background-color: #0d6efd; color: white; font-size: 10px; padding: 2px 5px; border-radius: 3px; margin-left: 5px;">Aktuální týden</span>' : '';
                    
                    return '<div class="apexcharts-tooltip-title" style="font-family: Inter, system-ui; padding: 6px 8px; background-color: #1e1e1e; color: #ffffff; border-bottom: 1px solid #333;">' +
                           formattedDate + currentWeekBadge + '</div>' +
                           '<div class="apexcharts-tooltip-series-group" style="padding: 8px; display: flex; flex-direction: column; background: #121212; color: #eee;">' +
                           '<span style="margin-bottom: 5px;"><strong>' + value + ' zobrazení</strong></span>' +
                           '</div>';
                }
            },
            plotOptions: {
                heatmap: {
                    radius: 0,
                    enableShades: true,
                    useFillColorAsStroke: false,
                    distributed: false,
                    colorScale: {
                        ranges: [
                            {
                                from: 0,
                                to: 0,
                                color: '#ebedf0'  // Světle šedá barva pro nulové hodnoty
                            },
                            {
                                from: 1,
                                to: Math.max(1, Math.floor(calendarData.maxViews * 0.25)),
                                color: '#9be9a8'  // Světle zelená pro nejnižší nenulové hodnoty
                            },
                            {
                                from: Math.max(2, Math.floor(calendarData.maxViews * 0.25) + 1),
                                to: Math.floor(calendarData.maxViews * 0.5),
                                color: '#40c463'  // Zelená pro střední hodnoty
                            },
                            {
                                from: Math.floor(calendarData.maxViews * 0.5) + 1,
                                to: Math.floor(calendarData.maxViews * 0.75),
                                color: '#30a14e'  // Tmavší zelená pro vyšší hodnoty
                            },
                            {
                                from: Math.floor(calendarData.maxViews * 0.75) + 1,
                                to: calendarData.maxViews,
                                color: '#216e39'  // Tmavě zelená pro nejvyšší hodnoty
                            }
                        ],
                        min: 0,
                        max: calendarData.maxViews
                    }
                }
            },
            series: generateLastYearCalendarSeries(),
            yaxis: {
                labels: {
                    show: true,
                    formatter: function(val) {
                        return daysInCzech[val];
                    },
                    style: {
                        fontSize: '10px'
                    }
                },
                reversed: false,
                min: 0,
                max: 6,
                axisTicks: {
                    show: false
                },
                axisBorder: {
                    show: false
                }
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    show: true,
                    datetimeUTC: false,
                    format: 'd. MMM',
                    style: {
                        fontSize: '10px'
                    },
                    rotate: -45,
                    hideOverlappingLabels: true
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                tooltip: {
                    enabled: false
                }
            },
            grid: {
                show: false,
                padding: {
                    top: 10,
                    right: 10,
                    bottom: 20,
                    left: 35
                }
            },
            stroke: {
                width: 1,
                colors: ['#ffffff']
            },
            theme: {
                mode: 'light'
            },
            annotations: {
                xaxis: [{
                    // Zvýraznění aktuálního týdne
                    x: getCurrentWeekDates().start,
                    x2: getCurrentWeekDates().end,
                    borderColor: '#0d6efd',
                    strokeDashArray: 0,
                    borderWidth: 2,
                    opacity: 0.3,
                    fillColor: '#0d6efd'
                }]
            },
            states: {
                hover: {
                    filter: {
                        type: 'none'
                    }
                },
                active: {
                    filter: {
                        type: 'none'
                    }
                }
            }
        };

        const calendarHeatmapChart = new ApexCharts(document.querySelector("#calendarHeatmap"), calendarHeatmapOptions);
        calendarHeatmapChart.render();
        
        // Detail článku po kliknutí
        const articleDetailModal = new bootstrap.Modal(document.getElementById('articleDetailModal'));

        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const articleId = this.getAttribute('data-article-id');
                const articleTitle = this.getAttribute('data-article-title');
                
                document.getElementById('articleDetailModalLabel').textContent = 'Detail článku: ' + articleTitle;
                
                // Resetování hodnot před načtením nových dat
                document.getElementById('detail-total-views').textContent = '0';
                document.getElementById('detail-avg-views').textContent = '0.0';
                document.getElementById('detail-percent').textContent = '0%';
                document.querySelector("#article-trend-chart").innerHTML = '';
                
                articleDetailModal.show();
                
                // Načtení detailních dat přes AJAX
                fetch(`/admin/statistics/api/article/${articleId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Síťová odpověď není v pořádku');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Aktualizace dat v modálu
                        document.getElementById('detail-total-views').textContent = (data.total_views || 0).toLocaleString('cs-CZ');
                        document.getElementById('detail-avg-views').textContent = (data.avg_views_per_day || 0).toFixed(1);
                        document.getElementById('detail-percent').textContent = (data.percent_of_total || 0).toFixed(1) + '%';
                        
                        // Kontrola, zda máme data pro trend
                        const hasTrendData = data.trend && Array.isArray(data.trend) && data.trend.length > 0;
                        
                        // Vytvoření vzorových dat, pokud nemáme reálná data
                        const trendData = hasTrendData ? data.trend : createSampleData(7);
                        
                        // Vykreslení grafu trendu pro článek
                        const articleTrendOptions = {
                            chart: {
                                type: 'area',
                                height: 300,
                                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                                toolbar: {
                                    show: false
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            series: [{
                                name: 'Zobrazení',
                                data: trendData.map(item => item.count || 0)
                            }],
                            xaxis: {
                                type: 'category',
                                categories: trendData.map(item => {
                                    if (item.date) {
                                        const date = new Date(item.date);
                                        return date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
                                    }
                                    return '-';
                                })
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.2,
                                    stops: [0, 90, 100]
                                }
                            },
                            colors: ['var(--primary-color)']
                        };
                        
                        const articleTrendChart = new ApexCharts(document.querySelector("#article-trend-chart"), articleTrendOptions);
                        articleTrendChart.render();
                    })
                    .catch(error => {
                        console.error('Chyba při načítání dat:', error);
                        document.getElementById('detail-total-views').textContent = '0';
                        document.getElementById('detail-avg-views').textContent = '0.0';
                        document.getElementById('detail-percent').textContent = '0%';
                        document.querySelector("#article-trend-chart").innerHTML = 
                            '<div class="alert alert-danger">' +
                                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                                'Nepodařilo se načíst data. Zkuste to prosím později.' +
                            '</div>';
                    });
            });
        });
    });
</script> 