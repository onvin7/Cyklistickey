<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fa-solid fa-tags me-2"></i>Statistiky kategorií</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Kategorie</li>
    </ol>

    <!-- Filtry -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtr časového období
        </div>
        <div class="card-body">
            <form id="period-filter" class="row g-3" method="GET">
                <div class="col-md-4">
                    <select class="form-select" id="period" name="period">
                        <option value="7" <?= $period == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                        <option value="30" <?= $period == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                        <option value="90" <?= $period == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                        <option value="365" <?= $period == 365 ? 'selected' : '' ?>>Poslední rok</option>
                        <option value="all" <?= $period == 'all' ? 'selected' : '' ?>>Všechna data</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Souhrné statistiky -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($totalCategories) ?></h4>
                            <div>Počet kategorií</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($totalViews) ?></h4>
                            <div>Celkový počet zobrazení</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-eye"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($avgArticlesPerCategory, 1) ?></h4>
                            <div>Průměr článků v kategorii</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?= number_format($emptyCategories) ?></h4>
                            <div>Prázdné kategorie</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafy -->
    <div class="row">
        <!-- Kategorie podle počtu zobrazení -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Kategorie podle počtu zobrazení
                </div>
                <div class="card-body">
                    <div id="categoriesViewsChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <!-- Kategorie podle počtu článků -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Kategorie podle počtu článků
                </div>
                <div class="card-body">
                    <div id="categoriesArticlesChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend kategorií v čase -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-1"></i>
            Trend zobrazení kategorií v čase
        </div>
        <div class="card-body">
            <div id="categoriesTrendChart" style="height: 400px;"></div>
        </div>
    </div>

    <!-- Tabulka kategorií -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Statistiky kategorií
        </div>
        <div class="card-body">
            <table id="categoriesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Název kategorie</th>
                        <th>Počet článků</th>
                        <th>Celkem zobrazení</th>
                        <th>Průměr na článek</th>
                        <th>Trend</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriesStats as $category): ?>
                    <tr>
                        <td><a href="/admin/category/edit/<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></td>
                        <td><?= number_format($category['articles_count']) ?></td>
                        <td><?= number_format($category['views']) ?></td>
                        <td><?= number_format($category['avg_views_per_article'], 1) ?></td>
                        <td>
                            <?php if ($category['trend'] > 0): ?>
                                <span class="text-success"><i class="fas fa-arrow-up"></i> <?= number_format($category['trend'], 1) ?>%</span>
                            <?php elseif ($category['trend'] < 0): ?>
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> <?= number_format(abs($category['trend']), 1) ?>%</span>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary view-detail" data-category-id="<?= $category['id'] ?>" data-category-name="<?= htmlspecialchars($category['name']) ?>">
                                <i class="fas fa-chart-line"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Souvislosti mezi kategoriemi -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-project-diagram me-1"></i>
            Souvislosti mezi kategoriemi
        </div>
        <div class="card-body">
            <div id="categoriesHeatmap" style="height: 500px;"></div>
            <div class="text-muted mt-3">
                <small>Heatmapa zobrazuje, jak často se kategorie vyskytují společně u stejných článků. Tmavší barva znamená častější souvislost.</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal pro detail kategorie -->
<div class="modal fade" id="categoryDetailModal" tabindex="-1" aria-labelledby="categoryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryDetailModalLabel">Detail kategorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="categoryDetailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Načítání...</span>
                    </div>
                    <p class="mt-2">Načítání statistik...</p>
                </div>
                <div id="categoryDetailContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Celkem zobrazení</h5>
                                    <h2 class="card-text text-primary" id="categoryDetailTotalViews">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Počet článků</h5>
                                    <h2 class="card-text text-success" id="categoryDetailArticlesCount">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Průměr na článek</h5>
                                    <h2 class="card-text text-info" id="categoryDetailAvgViews">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="categoryDetailChart" style="height: 300px;"></div>
                    
                    <h5 class="mt-4 mb-3">Nejčtenější články v kategorii</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="categoryDetailArticlesTable">
                            <thead>
                                <tr>
                                    <th>Název článku</th>
                                    <th>Autor</th>
                                    <th>Datum</th>
                                    <th>Zobrazení</th>
                                </tr>
                            </thead>
                            <tbody id="categoryDetailArticlesList">
                                <!-- Zde se dynamicky načtou články -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                <a href="#" id="categoryDetailViewAllBtn" class="btn btn-primary">Zobrazit všechny články</a>
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
        
        // Inicializace různých grafů
        const categoriesOptions = {
            chart: {
                type: 'donut',
                height: 350
            },
            series: <?= json_encode(array_column($categoriesStats, 'views')) ?>,
            labels: <?= json_encode(array_column($categoriesStats, 'name')) ?>,
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                itemMargin: {
                    horizontal: 5,
                    vertical: 5
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796', '#4e73df', '#1cc88a', '#36b9cc']
        };
        
        const categoriesViewsChart = new ApexCharts(document.querySelector("#categoriesViewsChart"), categoriesOptions);
        categoriesViewsChart.render();

        // Graf kategorií podle počtu článků
        const categoriesArticlesOptions = {
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            series: [{
                name: 'Počet článků',
                data: <?= json_encode(array_column($categoriesStats, 'articles_count')) ?>
            }],
            xaxis: {
                categories: <?= json_encode(array_column($categoriesStats, 'name')) ?>,
                title: {
                    text: 'Počet článků'
                }
            },
            colors: ['#1cc88a'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val;
                },
                offsetX: 20,
                style: {
                    fontSize: '12px',
                    colors: ['#333']
                }
            }
        };
        
        const categoriesArticlesChart = new ApexCharts(document.querySelector("#categoriesArticlesChart"), categoriesArticlesOptions);
        categoriesArticlesChart.render();

        // Trend kategorií v čase
        const categoriesTrendOptions = {
            chart: {
                type: 'area',
                height: 400,
                stacked: true,
                toolbar: {
                    show: true
                },
                zoom: {
                    enabled: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 1
            },
            series: <?= json_encode($categoriesTrendData) ?>,
            xaxis: {
                categories: <?= json_encode($trendPeriods) ?>,
                title: {
                    text: 'Datum'
                }
            },
            yaxis: {
                title: {
                    text: 'Počet zobrazení'
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            }
        };
        
        const categoriesTrendChart = new ApexCharts(document.querySelector("#categoriesTrendChart"), categoriesTrendOptions);
        categoriesTrendChart.render();

        // Heatmapa souvislostí mezi kategoriemi
        const categoriesHeatmapOptions = {
            chart: {
                type: 'heatmap',
                height: 500
            },
            plotOptions: {
                heatmap: {
                    shadeIntensity: 0.5,
                    radius: 0,
                    useFillColorAsStroke: true,
                    colorScale: {
                        ranges: [{
                            from: 0,
                            to: 0,
                            name: 'Žádná souvislost',
                            color: '#F0F0F0'
                        }]
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            series: <?= json_encode($categoriesCorrelationData) ?>,
            xaxis: {
                categories: <?= json_encode(array_column($categoriesStats, 'name')) ?>,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '11px'
                    }
                }
            },
            yaxis: {
                categories: <?= json_encode(array_column($categoriesStats, 'name')) ?>,
            },
            tooltip: {
                enabled: true,
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const xCategory = w.globals.labels[dataPointIndex];
                    const yCategory = w.globals.seriesNames[seriesIndex];
                    const value = series[seriesIndex][dataPointIndex];
                    return `<div class="p-2">
                        <div><strong>${yCategory}</strong> a <strong>${xCategory}</strong></div>
                        <div>Počet společných článků: <strong>${value}</strong></div>
                    </div>`;
                }
            }
        };
        
        const categoriesHeatmapChart = new ApexCharts(document.querySelector("#categoriesHeatmap"), categoriesHeatmapOptions);
        categoriesHeatmapChart.render();

        // Detail kategorie po kliknutí
        const categoryDetailModal = new bootstrap.Modal(document.getElementById('categoryDetailModal'));
        const categoryDetailChart = {
            chart: null
        };

        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');
                
                document.getElementById('categoryDetailModalLabel').textContent = 'Detail kategorie: ' + categoryName;
                document.getElementById('categoryDetailLoading').style.display = 'block';
                document.getElementById('categoryDetailContent').style.display = 'none';
                document.getElementById('categoryDetailViewAllBtn').href = `/admin/statistics/articles?category=${categoryId}`;
                
                categoryDetailModal.show();
                
                // Načtení detailních dat přes AJAX
                fetch(`/admin/statistics/api/category/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Nastavení základních hodnot
                        document.getElementById('categoryDetailTotalViews').textContent = new Intl.NumberFormat('cs-CZ').format(data.totalViews);
                        document.getElementById('categoryDetailArticlesCount').textContent = new Intl.NumberFormat('cs-CZ').format(data.articlesCount);
                        document.getElementById('categoryDetailAvgViews').textContent = new Intl.NumberFormat('cs-CZ', { maximumFractionDigits: 1 }).format(data.avgViewsPerArticle);
                        
                        // Naplnění tabulky nejčtenějších článků
                        const articlesList = document.getElementById('categoryDetailArticlesList');
                        articlesList.innerHTML = '';
                        
                        data.topArticles.forEach(article => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><a href="/admin/article/edit/${article.id}">${article.nazev}</a></td>
                                <td>${article.autor}</td>
                                <td>${new Date(article.datum).toLocaleDateString('cs-CZ')}</td>
                                <td>${new Intl.NumberFormat('cs-CZ').format(article.views)}</td>
                            `;
                            articlesList.appendChild(row);
                        });
                        
                        // Zobrazení obsahu
                        document.getElementById('categoryDetailLoading').style.display = 'none';
                        document.getElementById('categoryDetailContent').style.display = 'block';
                        
                        // Vykreslení grafu
                        if (categoryDetailChart.chart) {
                            categoryDetailChart.chart.destroy();
                        }
                        
                        const options = {
                            chart: {
                                type: 'line',
                                height: 300,
                                zoom: {
                                    enabled: true
                                },
                                toolbar: {
                                    show: true
                                }
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 3
                            },
                            series: [{
                                name: 'Počet zobrazení',
                                data: data.viewsTrend.data
                            }],
                            xaxis: {
                                categories: data.viewsTrend.dates,
                                title: {
                                    text: 'Datum'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Počet zobrazení'
                                }
                            },
                            colors: ['#4e73df'],
                            markers: {
                                size: 3
                            },
                            tooltip: {
                                shared: true,
                                intersect: false
                            }
                        };
                        
                        categoryDetailChart.chart = new ApexCharts(document.querySelector("#categoryDetailChart"), options);
                        categoryDetailChart.chart.render();
                    })
                    .catch(error => {
                        console.error('Chyba při načítání dat:', error);
                        document.getElementById('categoryDetailLoading').innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>Nepodařilo se načíst data. Zkuste to prosím později.
                            </div>
                        `;
                    });
            });
        });
    });
</script> 