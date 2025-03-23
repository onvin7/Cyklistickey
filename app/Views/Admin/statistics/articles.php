<div class="container-fluid px-4">
    <h1 class="mt-4"><i class="fa-solid fa-newspaper me-2"></i>Statistiky článků</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Články</li>
    </ol>

    <!-- Filtry -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtrování článků
        </div>
        <div class="card-body">
            <form id="article-filter" class="row g-3" method="GET">
                <div class="col-md-3">
                    <label for="date-range" class="form-label">Časové období</label>
                    <select class="form-select" id="date-range" name="date-range">
                        <option value="7" <?= $dateRange == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                        <option value="30" <?= $dateRange == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                        <option value="90" <?= $dateRange == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                        <option value="365" <?= $dateRange == 365 ? 'selected' : '' ?>>Poslední rok</option>
                        <option value="all" <?= $dateRange == 'all' ? 'selected' : '' ?>>Vše</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Kategorie</label>
                    <select class="form-select" id="category" name="category">
                        <option value="0">Všechny kategorie</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['nazev_kategorie']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="author" class="form-label">Autor</label>
                    <select class="form-select" id="author" name="author">
                        <option value="0">Všichni autoři</option>
                        <?php foreach ($authors as $author): ?>
                        <option value="<?= $author['id'] ?>" <?= $selectedAuthor == $author['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($author['name'] . ' ' . $author['surname']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Seřadit podle</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="views_desc" <?= $sort == 'views_desc' ? 'selected' : '' ?>>Počet zobrazení (sestupně)</option>
                        <option value="views_asc" <?= $sort == 'views_asc' ? 'selected' : '' ?>>Počet zobrazení (vzestupně)</option>
                        <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Datum publikace (nejnovější)</option>
                        <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Datum publikace (nejstarší)</option>
                        <option value="title_asc" <?= $sort == 'title_asc' ? 'selected' : '' ?>>Název (A-Z)</option>
                        <option value="title_desc" <?= $sort == 'title_desc' ? 'selected' : '' ?>>Název (Z-A)</option>
                    </select>
                </div>
                <div class="col-12 text-end">
                    <a href="/admin/statistics/articles" class="btn btn-sm btn-outline-secondary">Resetovat filtry</a>
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
                            <h4 class="mb-0"><?= number_format($totalArticles) ?></h4>
                            <div>Počet článků</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-newspaper"></i>
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
                            <h4 class="mb-0"><?= number_format($avgViewsPerArticle, 1) ?></h4>
                            <div>Průměr na článek</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-chart-simple"></i>
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
                            <h4 class="mb-0"><?= number_format($articlesWithoutViews) ?></h4>
                            <div>Články bez zobrazení</div>
                        </div>
                        <div class="fs-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabulka se statistikami všech článků -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Statistiky článků
        </div>
        <div class="card-body">
            <table id="articlesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Název článku</th>
                        <th>Autor</th>
                        <th>Kategorie</th>
                        <th>Datum publikace</th>
                        <th>Počet zobrazení</th>
                        <th>Průměrně za den</th>
                        <th>Trend</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><a href="/admin/article/edit/<?= $article['id'] ?>"><?= htmlspecialchars($article['nazev']) ?></a></td>
                        <td><?= htmlspecialchars($article['autor']) ?></td>
                        <td><?= htmlspecialchars($article['kategorie']) ?></td>
                        <td><?= date('d.m.Y', strtotime($article['datum'])) ?></td>
                        <td><?= number_format($article['total_views']) ?></td>
                        <td><?= number_format($article['avg_views_per_day'], 1) ?></td>
                        <td>
                            <?php if ($article['trend'] > 0): ?>
                                <span class="text-success"><i class="fas fa-arrow-up"></i> <?= number_format($article['trend'], 1) ?>%</span>
                            <?php elseif ($article['trend'] < 0): ?>
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> <?= number_format(abs($article['trend']), 1) ?>%</span>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-equals"></i> 0%</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary view-detail" data-article-id="<?= $article['id'] ?>" data-article-title="<?= htmlspecialchars($article['nazev']) ?>">
                                <i class="fas fa-chart-line"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Distribuce zobrazení -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Distribuce zobrazení článků
                </div>
                <div class="card-body">
                    <div id="viewsDistributionChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Trend publikování článků
                </div>
                <div class="card-body">
                    <div id="publishingTrendChart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pro detail článku -->
<div class="modal fade" id="articleDetailModal" tabindex="-1" aria-labelledby="articleDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="articleDetailModalLabel">Detail článku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="articleDetailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Načítání...</span>
                    </div>
                    <p class="mt-2">Načítání statistik...</p>
                </div>
                <div id="articleDetailContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Celkem zobrazení</h5>
                                    <h2 class="card-text text-primary" id="articleDetailTotalViews">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Průměrně za den</h5>
                                    <h2 class="card-text text-success" id="articleDetailAvgViews">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="articleDetailChart" style="height: 350px;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Automatické odeslání formuláře při změně hodnot filtrů
        const filterForm = document.getElementById('article-filter');
        const filterSelects = filterForm.querySelectorAll('select');
        
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });
        
        // DataTable
        $('#articlesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/cs.json'
            },
            order: [[4, 'desc']],
            pageLength: 25
        });

        // Graf distribuce zobrazení
        const viewsDistributionOptions = {
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                }
            },
            series: [{
                name: 'Počet článků',
                data: <?= json_encode(array_values($viewsDistribution['counts'])) ?>
            }],
            xaxis: {
                categories: <?= json_encode(array_values($viewsDistribution['ranges'])) ?>,
                title: {
                    text: 'Počet zobrazení'
                }
            },
            yaxis: {
                title: {
                    text: 'Počet článků'
                }
            },
            colors: ['#4e73df']
        };
        
        const viewsDistributionChart = new ApexCharts(document.querySelector("#viewsDistributionChart"), viewsDistributionOptions);
        viewsDistributionChart.render();

        // Graf trendu publikování
        const publishingTrendOptions = {
            chart: {
                type: 'area',
                height: 350,
                zoom: {
                    enabled: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            series: [{
                name: 'Nové články',
                data: <?= json_encode(array_values($publishingTrend['counts'])) ?>
            }],
            xaxis: {
                categories: <?= json_encode(array_values($publishingTrend['periods'])) ?>,
                title: {
                    text: 'Období'
                }
            },
            yaxis: {
                title: {
                    text: 'Počet nových článků'
                }
            },
            colors: ['#1cc88a'],
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
        
        const publishingTrendChart = new ApexCharts(document.querySelector("#publishingTrendChart"), publishingTrendOptions);
        publishingTrendChart.render();

        // Detail článku po kliknutí
        const articleDetailModal = new bootstrap.Modal(document.getElementById('articleDetailModal'));
        const articleDetailChart = {
            chart: null
        };

        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const articleId = this.getAttribute('data-article-id');
                const articleTitle = this.getAttribute('data-article-title');
                
                document.getElementById('articleDetailModalLabel').textContent = 'Detail článku: ' + articleTitle;
                document.getElementById('articleDetailLoading').style.display = 'block';
                document.getElementById('articleDetailContent').style.display = 'none';
                
                articleDetailModal.show();
                
                // Načtení detailních dat přes AJAX
                fetch(`/admin/statistics/api/article/${articleId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('articleDetailTotalViews').textContent = new Intl.NumberFormat('cs-CZ').format(data.totalViews);
                        document.getElementById('articleDetailAvgViews').textContent = new Intl.NumberFormat('cs-CZ', { maximumFractionDigits: 1 }).format(data.avgViewsPerDay);
                        
                        document.getElementById('articleDetailLoading').style.display = 'none';
                        document.getElementById('articleDetailContent').style.display = 'block';
                        
                        // Vykreslení grafu
                        if (articleDetailChart.chart) {
                            articleDetailChart.chart.destroy();
                        }
                        
                        const options = {
                            chart: {
                                type: 'line',
                                height: 350,
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
                                data: data.viewsData
                            }],
                            xaxis: {
                                categories: data.dates,
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
                                size: 4
                            },
                            tooltip: {
                                shared: false,
                                intersect: true
                            }
                        };
                        
                        articleDetailChart.chart = new ApexCharts(document.querySelector("#articleDetailChart"), options);
                        articleDetailChart.chart.render();
                    })
                    .catch(error => {
                        console.error('Chyba při načítání dat:', error);
                        document.getElementById('articleDetailLoading').innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>Nepodařilo se načíst data. Zkuste to prosím později.
                            </div>
                        `;
                    });
            });
        });
    });
</script> 