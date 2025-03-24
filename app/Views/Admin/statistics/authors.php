<!-- Statistiky autorů -->
<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-user-pen me-2"></i>Statistiky autorů</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Autoři</li>
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
                        <div class="stat-value"><?= number_format($totalAuthors) ?></div>
                        <div class="stat-label">Celkem autorů</div>
                    </div>
                    <div class="stat-icon" style="color: var(--primary-color);">
                        <i class="fa-solid fa-users"></i>
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
                        <div class="stat-value"><?= number_format($avgArticlesPerAuthor, 1) ?></div>
                        <div class="stat-label">Průměr článků na autora</div>
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
                        <div class="stat-value"><?= number_format($inactiveAuthors) ?></div>
                        <div class="stat-label">Neaktivní autoři</div>
                    </div>
                    <div class="stat-icon" style="color: var(--chart-color-4);">
                        <i class="fa-solid fa-user-slash"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafy -->
    <div class="row">
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-column me-2"></i>Autoři podle zobrazení</h2>
                </div>
                <div class="p-0">
                    <div id="authorsByViewsChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar me-2"></i>Autoři podle článků</h2>
                </div>
                <div class="p-0">
                    <div id="authorsByArticlesChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <!-- Trend v čase -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-line me-2"></i>Trend zobrazení podle autorů</h2>
        </div>
        <div class="p-0">
            <div id="authorsTrendChart" style="height: 400px;"></div>
        </div>
    </section>

    <!-- Tabulka autorů -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-users me-2"></i>Podrobný přehled autorů</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Autor</th>
                        <th class="text-center">Články</th>
                        <th class="text-center">Zobrazení</th>
                        <th>Poslední publikace</th>
                        <th class="text-center">Aktivita</th>
                    </tr>
                </thead>
                <tbody id="authorsTableBody">
                    <!-- Javascriptem generovaný obsah -->
                </tbody>
            </table>
        </div>
    </section>
</div>

<!-- Modal pro detail autora -->
<div class="modal fade" id="authorDetailModal" tabindex="-1" aria-labelledby="authorDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="authorDetailModalLabel">Detail autora</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-primary" id="detail-total-articles">0</h3>
                                <p class="text-muted">Celkem článků</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-success" id="detail-total-views">0</h3>
                                <p class="text-muted">Celkem zobrazení</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body text-center">
                                <h3 class="display-4 fw-bold text-info" id="detail-avg-views">0</h3>
                                <p class="text-muted">Průměr na článek</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Aktivita v čase</h5>
                        <div id="author-activity-chart" style="height: 300px;"></div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Nejčtenější články</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Název článku</th>
                                        <th class="text-center">Zobrazení</th>
                                    </tr>
                                </thead>
                                <tbody id="author-top-articles">
                                    <!-- Obsah se načte dynamicky -->
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        // Automatické odeslání formuláře při změně hodnoty filtru
        document.getElementById('period').addEventListener('change', function() {
            document.getElementById('period-filter').submit();
        });

        // Naplnění tabulky autorů
        const authorsData = <?= json_encode($authorsStats) ?>;
        const tableBody = document.getElementById('authorsTableBody');
        
        if (authorsData && authorsData.length > 0) {
            authorsData.forEach(author => {
                tableBody.innerHTML += createAuthorRow(author);
            });
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>Žádní autoři k zobrazení
                        </div>
                    </td>
                </tr>
            `;
        }
        
        // Graf autorů podle zobrazení
        const authorsByViewsOptions = {
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
                data: <?= !empty($authorsByViews) ? json_encode(array_column($authorsByViews, 'total_views')) : json_encode(array_column(array_slice($authorsStats, 0, 10), 'total_views')) ?>
            }],
            xaxis: {
                categories: <?= !empty($authorsByViews) ? json_encode(array_column($authorsByViews, 'name')) : json_encode(array_column(array_slice($authorsStats, 0, 10), 'name')) ?>,
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
            colors: ['var(--primary-color)']
        };

        const authorsByViewsChart = new ApexCharts(document.querySelector("#authorsByViewsChart"), authorsByViewsOptions);
        authorsByViewsChart.render();

        // Graf autorů podle článků
        const authorsByArticlesOptions = {
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
                name: 'Články',
                data: <?= !empty($authorsByArticles) ? json_encode(array_column($authorsByArticles, 'article_count')) : json_encode(array_column(array_slice($authorsStats, 0, 10), 'article_count')) ?>
            }],
            xaxis: {
                categories: <?= !empty($authorsByArticles) ? json_encode(array_column($authorsByArticles, 'name')) : json_encode(array_column(array_slice($authorsStats, 0, 10), 'name')) ?>,
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
                        return val + " článků";
                    }
                }
            },
            colors: ['var(--chart-color-2)']
        };

        const authorsByArticlesChart = new ApexCharts(document.querySelector("#authorsByArticlesChart"), authorsByArticlesOptions);
        authorsByArticlesChart.render();

        // Graf trendu autorů v čase
        const authorsTrendOptions = {
            chart: {
                type: 'line',
                height: 400,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                toolbar: {
                    show: false
                },
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            series: <?= !empty($authorsTrend) && !empty($authorsTrend['series']) ? json_encode($authorsTrend['series']) : generateSampleAuthorsTrendData() ?>,
            xaxis: {
                type: 'datetime',
                categories: <?= !empty($authorsTrend) && !empty($authorsTrend['dates']) ? json_encode($authorsTrend['dates']) : generateSampleTrendDates() ?>,
                labels: {
                    formatter: function(value, timestamp) {
                        return new Date(value).toLocaleDateString('cs-CZ');
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                }
            },
            colors: ['var(--primary-color)', 'var(--chart-color-2)', 'var(--chart-color-3)', 
                    'var(--chart-color-4)', 'var(--chart-color-5)', 'var(--chart-color-6)']
        };

        const authorsTrendChart = new ApexCharts(document.querySelector("#authorsTrendChart"), authorsTrendOptions);
        authorsTrendChart.render();

        // Funkce pro generování vzorových dat pro trend autorů
        function generateSampleAuthorsTrendData() {
            const topAuthors = <?= json_encode(array_column(array_slice($authorsStats, 0, 5), 'name')) ?>;
            const result = [];
            
            // Pro každého autora vytvoříme náhodná data
            topAuthors.forEach(author => {
                const data = [];
                // 30 dní nazpátek
                for (let i = 0; i < 30; i++) {
                    data.push(Math.floor(Math.random() * 100)); // Náhodné hodnoty
                }
                
                result.push({
                    name: author,
                    data: data
                });
            });
            
            return result;
        }
        
        // Funkce pro generování vzorových dat pro dny trendu
        function generateSampleTrendDates() {
            const dates = [];
            for (let i = 29; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                dates.push(date.toISOString().split('T')[0]);
            }
            return dates;
        }

        // Detail autora po kliknutí
        const authorDetailModal = new bootstrap.Modal(document.getElementById('authorDetailModal'));

        document.querySelectorAll('.view-author-detail').forEach(button => {
            button.addEventListener('click', function() {
                const authorId = this.getAttribute('data-author-id');
                const authorName = this.getAttribute('data-author-name');
                
                document.getElementById('authorDetailModalLabel').textContent = 'Detail autora: ' + authorName;
                
                authorDetailModal.show();
                
                // Načtení detailních dat přes AJAX
                fetch(`/admin/statistics/api/author/${authorId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('detail-total-articles').textContent = data.articles_count.toLocaleString('cs-CZ');
                        document.getElementById('detail-total-views').textContent = data.total_views.toLocaleString('cs-CZ');
                        document.getElementById('detail-avg-views').textContent = data.avg_views_per_article.toFixed(1);
                        
                        // Vykreslení grafu aktivity
                        const authorActivityOptions = {
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
                                name: 'Články',
                                data: data.activity_trend.map(item => item.articles_count)
                            },{
                                name: 'Zobrazení',
                                data: data.activity_trend.map(item => item.views_count)
                            }],
                            xaxis: {
                                type: 'datetime',
                                categories: data.activity_trend.map(item => item.period)
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
                            colors: ['var(--chart-color-2)', 'var(--primary-color)']
                        };
                        
                        const authorActivityChart = new ApexCharts(document.querySelector("#author-activity-chart"), authorActivityOptions);
                        authorActivityChart.render();
                        
                        // Naplnění tabulky nejčtenějších článků
                        const topArticlesHTML = data.top_articles.map(article => `
                            <tr>
                                <td>
                                    <a href="/admin/article/edit/${article.id}" class="text-decoration-none">
                                        ${article.title}
                                    </a>
                                </td>
                                <td class="text-center">${article.views.toLocaleString('cs-CZ')}</td>
                            </tr>
                        `).join('');
                        
                        document.getElementById('author-top-articles').innerHTML = topArticlesHTML;
                    })
                    .catch(error => {
                        console.error('Chyba při načítání dat:', error);
                    });
            });
        });

        // Funkce pro vytvoření HTML řádku pro tabulku autorů
        function createAuthorRow(author) {
            const lastArticleDate = author.last_article_date ? new Date(author.last_article_date) : null;
            const now = new Date();
            const twoWeeksAgo = new Date();
            twoWeeksAgo.setDate(now.getDate() - 14);
            
            const isRecent = lastArticleDate && lastArticleDate > twoWeeksAgo;
            
            // Formátování data publikace
            const formattedDate = lastArticleDate ? 
                lastArticleDate.toLocaleDateString('cs-CZ') + ' ' + lastArticleDate.toLocaleTimeString('cs-CZ', {hour: '2-digit', minute:'2-digit'}) : 
                'Žádný článek';
            
            // Formátování čísel s oddělením tisíců
            const formattedArticleCount = new Intl.NumberFormat('cs-CZ').format(author.article_count || 0);
            const formattedViews = new Intl.NumberFormat('cs-CZ').format(author.total_views || 0);
            
            return `
                <tr>
                    <td>
                        <div class="author-info">
                            <div class="author-name">${author.name || 'Neznámý autor'}</div>
                            ${author.email ? `<div class="author-email">${author.email}</div>` : ''}
                        </div>
                    </td>
                    <td class="text-center">${formattedArticleCount}</td>
                    <td class="text-center fw-medium">${formattedViews}</td>
                    <td>${formattedDate}</td>
                    <td class="text-center">
                        <span class="activity-badge ${isRecent ? 'active' : 'inactive'}">
                            ${isRecent ? 'Aktivní' : 'Neaktivní'}
                        </span>
                    </td>
                </tr>
            `;
        }
    });
</script> 