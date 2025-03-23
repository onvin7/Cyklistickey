<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-gauge-high me-2"></i>Statistiky výkonu</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Výkon</li>
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
                            <option value="7" <?= $period == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                            <option value="30" <?= $period == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                            <option value="90" <?= $period == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                            <option value="365" <?= $period == 365 ? 'selected' : '' ?>>Poslední rok</option>
                            <option value="all" <?= $period == 'all' ? 'selected' : '' ?>>Všechna data</option>
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
                        <div class="stat-value"><?= number_format($totalViews) ?></div>
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
                        <div class="stat-value"><?= number_format($totalArticles) ?></div>
                        <div class="stat-label">Celkem článků</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-2);">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-5);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($avgViewsPerArticle, 1) ?></div>
                        <div class="stat-label">Průměr na článek</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-5);">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-4);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= isset($avgViewsPerDay) ? number_format(round($avgViewsPerDay)) : '0' ?></div>
                        <div class="stat-label">Průměr za den</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-4);">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nejlépe výkonné články -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-rocket me-2"></i>Nejlépe výkonné články</h2>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="topPerformingTable" class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Název článku</th>
                            <th class="px-3 py-3 border-bottom">Autor</th>
                            <th class="px-3 py-3 border-bottom">Kategorie</th>
                            <th class="px-3 py-3 border-bottom">Datum</th>
                            <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                            <th class="px-3 py-3 border-bottom text-center">Průměr/den</th>
                            <th class="px-3 py-3 border-bottom text-center">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPerforming as $article): ?>
                        <tr class="align-middle">
                            <td class="px-3 py-3 border-bottom">
                                <a href="/admin/article/edit/<?= $article['id'] ?>" class="text-decoration-none fw-medium"><?= htmlspecialchars($article['nazev']) ?></a>
                            </td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['autor'] ?? '-') ?></td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['kategorie'] ?? '-') ?></td>
                            <td class="px-3 py-3 border-bottom"><?= isset($article['datum']) ? date('d.m.Y', strtotime($article['datum'])) : '-' ?></td>
                            <td class="px-3 py-3 border-bottom text-center fw-bold"><?= number_format($article['total_views']) ?></td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <span class="badge rounded-pill bg-success px-2 py-1"><?= number_format($article['avg_views_per_day'], 1) ?></span>
                            </td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <button type="button" class="btn btn-sm btn-primary view-detail" data-article-id="<?= $article['id'] ?>" data-article-title="<?= htmlspecialchars($article['nazev']) ?>">
                                    <i class="fas fa-chart-line"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Nejméně výkonné články -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-arrow-trend-down me-2"></i>Podvýkonné články</h2>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="underPerformingTable" class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Název článku</th>
                            <th class="px-3 py-3 border-bottom">Autor</th>
                            <th class="px-3 py-3 border-bottom">Kategorie</th>
                            <th class="px-3 py-3 border-bottom">Datum</th>
                            <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                            <th class="px-3 py-3 border-bottom text-center">Průměr/den</th>
                            <th class="px-3 py-3 border-bottom text-center">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($underPerforming as $article): ?>
                        <tr class="align-middle">
                            <td class="px-3 py-3 border-bottom">
                                <a href="/admin/article/edit/<?= $article['id'] ?>" class="text-decoration-none fw-medium"><?= htmlspecialchars($article['nazev']) ?></a>
                            </td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['autor'] ?? '-') ?></td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['kategorie'] ?? '-') ?></td>
                            <td class="px-3 py-3 border-bottom"><?= isset($article['datum']) ? date('d.m.Y', strtotime($article['datum'])) : '-' ?></td>
                            <td class="px-3 py-3 border-bottom text-center fw-bold"><?= number_format($article['total_views']) ?></td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <span class="badge rounded-pill bg-danger px-2 py-1"><?= number_format($article['avg_views_per_day'], 1) ?></span>
                            </td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <button type="button" class="btn btn-sm btn-primary view-detail" data-article-id="<?= $article['id'] ?>" data-article-title="<?= htmlspecialchars($article['nazev']) ?>">
                                    <i class="fas fa-chart-line"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Nejčtenější kategorie -->
    <div class="row">
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-tags me-2"></i>Nejčtenější kategorie</h2>
                </div>
                <div class="p-0">
                    <div id="categoriesPerformanceChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-pie me-2"></i>Podíl top článků na celkových zobrazeních</h2>
                </div>
                <div class="p-0">
                    <div id="topArticlesShareChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Modal pro detail článku -->
<div class="modal fade" id="articleDetailModal" tabindex="-1" aria-labelledby="articleDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">z
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="articleDetailModalLabel">Detail článku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Obsah modálního okna se načte dynamicky -->
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
        
        // DataTable pro nejlépe výkonné články
        $('#topPerformingTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/cs.json',
                lengthMenu: "Zobrazit _MENU_ záznamů",
                info: "Zobrazeno _START_ až _END_ z _TOTAL_ záznamů"
            },
            order: [[5, 'desc']], // Řazení podle průměru zobrazení za den
            paging: false,
            searching: false,
            info: false,
            columnDefs: [
                { orderable: false, targets: 6 } // Sloupec "Akce" není řaditelný
            ]
        });
        
        // DataTable pro nejméně výkonné články
        $('#underPerformingTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/cs.json',
                lengthMenu: "Zobrazit _MENU_ záznamů",
                info: "Zobrazeno _START_ až _END_ z _TOTAL_ záznamů"
            },
            order: [[5, 'asc']], // Řazení podle průměru zobrazení za den
            paging: false,
            searching: false,
            info: false,
            columnDefs: [
                { orderable: false, targets: 6 } // Sloupec "Akce" není řaditelný
            ]
        });
        
        // Detail článku po kliknutí
        const articleDetailModal = new bootstrap.Modal(document.getElementById('articleDetailModal'));

        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const articleId = this.getAttribute('data-article-id');
                const articleTitle = this.getAttribute('data-article-title');
                
                document.getElementById('articleDetailModalLabel').textContent = 'Detail článku: ' + articleTitle;
                
                articleDetailModal.show();
                
                // Načtení detailních dat přes AJAX
                fetch(`/admin/statistics/api/article/${articleId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Zde by se zobrazila data detailu článku
                    })
                    .catch(error => {
                        console.error('Chyba při načítání dat:', error);
                    });
            });
        });
        
        // Načtení dat o kategoriích
        let categoryData = [];
        
        // Zkontrolujeme, zda již máme data kategorií
        <?php if (isset($categoryStats) && is_array($categoryStats)): ?>
            console.log('Data kategorií jsou k dispozici:', <?= json_encode($categoryStats) ?>);
            categoryData = <?= json_encode($categoryStats) ?>;
            renderCategoriesChart(categoryData);
        <?php else: ?>
            console.log('Data kategorií nejsou k dispozici, načítám z API');
            // Načtení dat o kategoriích pomocí API
            fetch('/admin/statistics/api/categories?period=<?= $period ?>')
                .then(response => response.json())
                .then(data => {
                    categoryData = data;
                    renderCategoriesChart(categoryData);
                })
                .catch(error => {
                    console.error('Chyba při načítání dat o kategoriích:', error);
                    
                    // Ukázkové údaje pro případ, že skutečná data nejsou k dispozici
                    const sampleData = [
                        { name: 'Cyklistické trasy', views: 1850 },
                        { name: 'Novinky', views: 1240 },
                        { name: 'Vybavení', views: 980 },
                        { name: 'Závody', views: 780 },
                        { name: 'Tréninky', views: 650 },
                        { name: 'Zdraví', views: 450 },
                        { name: 'Ostatní', views: 350 }
                    ];
                    renderCategoriesChart(sampleData);
                });
        <?php endif; ?>
        
        // Funkce pro vykreslení grafu kategorií
        function renderCategoriesChart(data) {
            // Seřazení dat podle počtu zobrazení (sestupně)
            data.sort((a, b) => b.views - a.views);
            
            // Omezení na maximálně 8 kategorií
            const chartData = data.slice(0, 8);
            
            // Příprava dat pro graf
            const categories = chartData.map(item => item.name);
            const viewCounts = chartData.map(item => item.views);
            
            // Vytvoření grafu
            const categoriesOptions = {
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
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                series: [{
                    name: 'Počet zobrazení',
                    data: viewCounts
                }],
                xaxis: {
                    categories: categories,
                    labels: {
                        style: {
                            colors: categories.map(() => '#666'),
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#666',
                            fontSize: '12px'
                        }
                    }
                },
                colors: [
                    '#4361ee', '#3a0ca3', '#7209b7', '#f72585', 
                    '#4cc9f0', '#06d6a0', '#118ab2', '#073b4c'
                ],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toLocaleString('cs-CZ');
                    },
                    style: {
                        colors: ['#333']
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return value.toLocaleString('cs-CZ') + ' zobrazení';
                        }
                    }
                }
            };
            
            const categoriesChart = new ApexCharts(document.getElementById("categoriesPerformanceChart"), categoriesOptions);
            categoriesChart.render();
        }
        
        // Graf podílu top článků
        const topArticlesShareOptions = {
            chart: {
                type: 'pie',
                height: 350,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif'
            },
            series: <?= json_encode($topArticlesShare ?? [0, 0]) ?>,
            labels: ['Top 10 článků', 'Ostatní články'],
            colors: ['#4361ee', '#3a0ca3', '#7209b7', '#f72585', '#4cc9f0'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(2) + '%';
                }
            },
            legend: {
                position: 'bottom'
            },
            tooltip: {
                y: {
                    formatter: function(value, { seriesIndex, dataPointIndex, w }) {
                        const totalViews = <?= $totalViews ?>;
                        const topArticlesViews = <?= $topArticlesViews ?>;
                        
                        let viewsCount = 0;
                        if (seriesIndex === 0) {
                            viewsCount = topArticlesViews;
                        } else {
                            viewsCount = totalViews - topArticlesViews;
                        }
                        
                        // Funkce pro formátování čísel v JavaScriptu
                        function formatNumber(num) {
                            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ');
                        }
                        
                        return `${formatNumber(Math.round(viewsCount))} zobrazení (${value.toFixed(2)}%)`;
                    }
                }
            }
        };
        
        const topArticlesShareChart = new ApexCharts(document.querySelector("#topArticlesShareChart"), topArticlesShareOptions);
        topArticlesShareChart.render();
    });
</script> 