<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-chart-line me-2"></i>Statistiky</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Statistiky</li>
    </ol>

    <!-- Souhrnné karty -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #4d5aea;"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($totalViews) ?></div>
                        <div class="stat-label">Celkový počet zobrazení</div>
                        </div>
                    <div class="stat-icon" style="color: #4d5aea;">
                            <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top px-3 pb-3">
                    <a href="/admin/statistics/views" class="btn btn-sm btn-primary w-100">Zobrazit detail <i class="fas fa-angle-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #f25c78;"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($totalArticles) ?></div>
                        <div class="stat-label">Celkový počet článků</div>
                        </div>
                    <div class="stat-icon" style="color: #f25c78;">
                            <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top px-3 pb-3">
                    <a href="/admin/statistics/articles" class="btn btn-sm btn-primary w-100">Zobrazit detail <i class="fas fa-angle-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #1bd4cd;"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($totalCategories) ?></div>
                        <div class="stat-label">Celkový počet kategorií</div>
                        </div>
                    <div class="stat-icon" style="color: #1bd4cd;">
                            <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top px-3 pb-3">
                    <a href="/admin/statistics/categories" class="btn btn-sm btn-primary w-100">Zobrazit detail <i class="fas fa-angle-right ms-1"></i></a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #ffbb44;"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($avgViewsPerArticle, 1) ?></div>
                        <div class="stat-label">Průměr zobrazení na článek</div>
                        </div>
                    <div class="stat-icon" style="color: #ffbb44;">
                            <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
                <div class="pt-2 mt-2 border-top px-3 pb-3">
                    <a href="/admin/statistics/performance" class="btn btn-sm btn-primary w-100">Zobrazit detail <i class="fas fa-angle-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtry -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-filter me-2"></i>Filtr časového období</h2>
        </div>
        <div class="p-3">
            <form id="period-filter" class="row g-3">
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

    <!-- Top grafy -->
    <div class="row">
        <div class="col-xl-8">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line me-2"></i>Trend zobrazení v čase</h2>
                </div>
                <div>
                    <div id="viewsTrendChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-trophy me-2"></i>Top 5 článků</h2>
                </div>
                <div>
                    <div id="topArticlesChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <!-- Kategorie -->
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-tags me-2"></i>Zobrazení podle kategorií</h2>
                </div>
                <div>
                    <div id="categoriesChart" style="height: 350px;"></div>
                </div>
                <div class="card-footer text-center pt-3 mt-3 border-top">
                    <a href="/admin/statistics/categories" class="btn btn-action">Více podrobností</a>
                </div>
            </section>
        </div>
        <!-- Autoři -->
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-users me-2"></i>Aktivita autorů</h2>
                </div>
                <div>
                    <div id="authorsChart" style="height: 350px;"></div>
                </div>
                <div class="card-footer text-center pt-3 mt-3 border-top">
                    <a href="/admin/statistics/authors" class="btn btn-action">Více podrobností</a>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded');
        
        // Nastavení filtru pro vlastní období
        const periodFilter = document.getElementById('period-filter');
        const periodSelect = document.getElementById('period');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const customPeriodElements = document.querySelectorAll('.custom-period');
        
        // Funkce pro automatické odeslání formuláře
        function submitForm() {
            periodFilter.submit();
        }
        
        // Event listenery pro automatické odeslání při změně hodnot
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customPeriodElements.forEach(el => el.classList.remove('d-none'));
                // Neodešleme formulář hned, počkáme až uživatel zadá datumy
            } else {
                customPeriodElements.forEach(el => el.classList.add('d-none'));
                submitForm(); // Automaticky odešleme formulář při výběru předvolené hodnoty
            }
        });
        
        // Odeslání formuláře při změně datumů (s debouncingem)
        let dateChangeTimer;
        const dateChangeHandler = function() {
            clearTimeout(dateChangeTimer);
            // Odešleme formulář až po 500ms nečinnosti, aby měl uživatel čas vybrat datum
            dateChangeTimer = setTimeout(submitForm, 500);
        };
        
        startDateInput.addEventListener('change', dateChangeHandler);
        endDateInput.addEventListener('change', dateChangeHandler);

        // Kontrola, zda je definován objekt ApexCharts
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts není načten! Zkontrolujte, zda je knihovna správně importována.');
            document.querySelectorAll('[id$="Chart"]').forEach(el => {
                el.innerHTML = '<div class="alert alert-danger">Nepodařilo se načíst grafy. Obnovte stránku nebo kontaktujte správce.</div>';
            });
            return;
        }

        // Kontrolní výpisy dat pro ladění
        console.log('View Trend Data:', <?= json_encode($viewsTrendData) ?>);
        console.log('View Trend Labels:', <?= json_encode($viewsTrendLabels) ?>);
        console.log('Top Articles:', <?= json_encode($topArticles) ?>);
        console.log('Category Stats:', <?= json_encode($categoryStats) ?>);
        console.log('Author Stats:', <?= json_encode($authorStats) ?>);
        
        // Funkce pro bezpečné zobrazení grafu
        function renderChart(elementId, options, chartType) {
            try {
                const element = document.querySelector(elementId);
                if (!element) {
                    console.error(`Element ${elementId} nebyl nalezen`);
                    return;
                }
                
                console.log(`Rendering ${chartType} chart`);
                const chart = new ApexCharts(element, options);
                chart.render();
                return chart;
            } catch (e) {
                console.error(`Error rendering ${chartType} chart:`, e);
                const element = document.querySelector(elementId);
                if (element) {
                    element.innerHTML = `<div class="alert alert-danger">Nepodařilo se vykreslit graf: ${e.message}</div>`;
                }
                return null;
            }
        }
        
        // GRAF TRENDU ZOBRAZENÍ
        // Poznámka: Pro zobrazení všech článků je potřeba upravit controller (Statistics.php) 
        // V metodě index() upravte limit pro načítání článků v trendu nebo odstraňte limit
        const viewsTrendOptions = {
            chart: {
                type: 'line',
                height: 350,
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true
                }
            },
            stroke: {
                curve: 'straight',
                width: 2
            },
            series: <?= json_encode($viewsTrendData) ?>,
            xaxis: {
                categories: <?= json_encode($viewsTrendLabels) ?>,
                
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Počet zobrazení'
                },
                min: 0
            },
            colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#fd7e14', '#6f42c1', '#20c997', '#6c757d', '#343a40'],
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '10px',
                itemMargin: {
                    horizontal: 5,
                    vertical: 0
                },
                onItemClick: {
                    toggleDataSeries: true
                },
                onItemHover: {
                    highlightDataSeries: true
                }
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            },
            markers: {
                size: 4,
                hover: {
                    size: 6
                }
            },
            dataLabels: {
                enabled: false
            }
        };
        
        renderChart("#viewsTrendChart", viewsTrendOptions, 'view trend');
        
        // GRAF TOP 5 ČLÁNKŮ
        const topArticlesSeries = <?= json_encode(array_column($topArticles, 'total_views')) ?>;
        const topArticlesLabels = <?= json_encode(array_map(function($title) { 
            return mb_strlen($title) > 25 ? mb_substr($title, 0, 25) . '...' : $title; 
        }, array_column($topArticles, 'nazev'))) ?>;
        
        if (topArticlesSeries.length > 0) {
            console.log('Rendering top articles chart with data:', topArticlesSeries);
            // Zjednodušení grafu, přepnutí na horizontální bar chart pro lepší čitelnost
        const topArticlesOptions = {
            chart: {
                    type: 'bar',
                    height: 350,
                    fontFamily: 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Počet zobrazení',
                    data: topArticlesSeries
                }],
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: {
                            position: 'top',
                        },
                        borderRadius: 4,
                        distributed: true
                    }
                },
                colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                xaxis: {
                    categories: topArticlesLabels,
                    title: {
                        text: 'Počet zobrazení'
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val;
                    },
                    offsetX: 10,
                    style: {
                        fontSize: '12px',
                        colors: ['#333']
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value + ' zobrazení';
                        }
                    }
                },
                title: {
                    align: 'center',
                    style: {
                        fontSize: '14px'
                    }
                }
            };
            
            renderChart("#topArticlesChart", topArticlesOptions, 'top articles');
        } else {
            document.querySelector("#topArticlesChart").innerHTML = '<div class="alert alert-info">Žádná data k zobrazení</div>';
        }
        
        // GRAF KATEGORIÍ
        // Získáme správné klíče pro data
        const categoryViewsData = <?= json_encode(array_column($categoryStats, 'views')) ?>;
        
        // Správný klíč pro články - pokud articles_count není k dispozici, zkontrolujeme article_count nebo count
        let categoryArticlesData = [];
        <?php
        // Zjistíme, jaký klíč používat pro počet článků
        $articleCountKey = 'article_count';
        if (isset($categoryStats[0])) {
            if (isset($categoryStats[0]['articles_count'])) {
                $articleCountKey = 'articles_count';
            } elseif (isset($categoryStats[0]['count'])) {
                $articleCountKey = 'count';
            }
        }
        ?>
        categoryArticlesData = <?= json_encode(array_column($categoryStats, $articleCountKey)) ?>;
        console.log('Category Article Count Key:', '<?= $articleCountKey ?>');
        console.log('Category Articles Data:', categoryArticlesData);
        
        const categoryLabelsData = <?= json_encode(array_column($categoryStats, 'name')) ?>;
        
        if (categoryViewsData.length > 0) {
            console.log('Rendering categories chart with data:', categoryViewsData);
        const categoriesOptions = {
            chart: {
                    type: 'bar',
                    height: 350,
                    stacked: false,
                    fontFamily: 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                series: [
                    {
                        name: 'Počet zobrazení',
                        data: categoryViewsData,
                        type: 'column'
                    }, 
                    {
                        name: 'Počet článků',
                        data: categoryArticlesData,
                        type: 'line'
                    }
                ],
                xaxis: {
                    categories: categoryLabelsData,
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: [
                    {
                        title: {
                            text: 'Počet zobrazení'
                        },
                        labels: {
                            formatter: function(val) {
                                return val > 1000 ? (val / 1000).toFixed(1) + 'k' : val;
                            }
                        },
                        min: 0
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Počet článků'
                        },
                        min: 0,
                        max: Math.max(...categoryArticlesData) * 1.2,
                        labels: {
                            formatter: function(val) {
                                return Math.round(val);
                            }
                        }
                    }
                ],
                colors: ['var(--chart-color-1)', 'var(--chart-color-2)', 'var(--chart-color-3)', 'var(--chart-color-4)', 'var(--chart-color-5)'],
            legend: {
                    position: 'top',
                    horizontalAlign: 'left'
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px',
                        fontWeight: 'normal',
                        colors: ['#333']
                    },
                    offsetY: -5,
                    formatter: function(val, { seriesIndex }) {
                        if (seriesIndex === 0) return val;
                        return val;
                    },
                    background: {
                        enabled: false
                    }
                },
                tooltip: {
                    shared: false,
                    intersect: true,
                    y: {
                        formatter: function(value, { seriesIndex }) {
                            if (seriesIndex === 0) return value + ' zobrazení';
                            return value + ' článků';
                        }
                    }
                },
                stroke: {
                    width: [0, 3]
                }
            };
            
            renderChart("#categoriesChart", categoriesOptions, 'categories');
        } else {
            document.querySelector("#categoriesChart").innerHTML = '<div class="alert alert-info">Žádná data k zobrazení</div>';
        }
        
        // GRAF AUTORŮ
        const authorStats = <?= json_encode($authorStats) ?>;
        
        if (authorStats.length > 0) {
            console.log('Rendering authors chart with data:', authorStats);
        const authorsOptions = {
            chart: {
                type: 'bar',
                    height: 350,
                    stacked: false,
                    fontFamily: 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
                    toolbar: {
                        show: false
                    }
            },
            plotOptions: {
                bar: {
                        horizontal: false,
                        columnWidth: '50%',
                        borderRadius: 4,
                        endingShape: 'rounded'
                    }
                },
                stroke: {
                    width: [0, 3],
                    curve: 'smooth'
                },
                series: [
                    {
                name: 'Počet článků',
                        type: 'column',
                data: <?= json_encode(array_column($authorStats, 'article_count')) ?>
                    }, 
                    {
                name: 'Počet zobrazení (v tis.)',
                        type: 'line',
                data: <?= json_encode(array_map(function($val) { return round($val / 1000, 1); }, array_column($authorStats, 'total_views'))) ?>
                    }
                ],
            xaxis: {
                categories: <?= json_encode(array_column($authorStats, 'name')) ?>,
                    labels: {
                        rotate: -45,
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: [
                    {
                        title: {
                            text: 'Počet článků'
                        },
                        min: 0,
                        max: Math.max(...<?= json_encode(array_column($authorStats, 'article_count')) ?>) * 1.2,
                        labels: {
                            formatter: function(val) {
                                return Math.round(val);
                            }
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Počet zobrazení (v tis.)'
                        },
                        min: 0,
                        labels: {
                            formatter: function(val) {
                                return val + ' tis.';
                            }
                        }
                    }
                ],
                colors: ['var(--chart-color-1)', 'var(--chart-color-2)', 'var(--chart-color-3)', 'var(--chart-color-4)', 'var(--chart-color-5)'],
                markers: {
                    size: 5,
                    colors: ['var(--chart-color-2)'],
                    strokeWidth: 2,
                    hover: {
                        size: 7
                    }
                },
            legend: {
                position: 'top'
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px',
                        fontWeight: 'normal',
                        colors: ['#333']
                    },
                    offsetY: -5,
                    formatter: function(val, { seriesIndex }) {
                        return val;
                    },
                    background: {
                        enabled: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(value, { seriesIndex }) {
                            if (seriesIndex === 0) return value + ' článků';
                            return value + ' tis. zobrazení';
                        }
                    }
                },
                grid: {
                    borderColor: '#e7e7e7',
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    }
                },
                title: {
                    align: 'center',
                    style: {
                        fontSize: '14px'
                    }
                }
            };
            
            renderChart("#authorsChart", authorsOptions, 'authors');
        } else {
            document.querySelector("#authorsChart").innerHTML = '<div class="alert alert-info">Žádná data k zobrazení</div>';
        }
    });
</script>
