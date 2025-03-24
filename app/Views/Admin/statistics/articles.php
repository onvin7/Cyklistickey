<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-newspaper me-2"></i>Statistiky článků</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Články</li>
    </ol>

    <!-- Filtry -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-filter me-2"></i>Filtrování článků</h2>
        </div>
        <div class="p-3">
            <form id="article-filter" class="row g-3" method="GET">
                <div class="col-md-3">
                    <label for="date-range" class="form-label small text-muted">Časové období</label>
                    <select class="form-select form-select-sm shadow-none border" id="date-range" name="date-range">
                        <option value="7" <?= $dateRange == 7 ? 'selected' : '' ?>>Posledních 7 dní</option>
                        <option value="30" <?= $dateRange == 30 ? 'selected' : '' ?>>Posledních 30 dní</option>
                        <option value="90" <?= $dateRange == 90 ? 'selected' : '' ?>>Posledních 90 dní</option>
                        <option value="365" <?= $dateRange == 365 ? 'selected' : '' ?>>Poslední rok</option>
                        <option value="all" <?= $dateRange == 'all' ? 'selected' : '' ?>>Vše</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label small text-muted">Kategorie</label>
                    <select class="form-select form-select-sm shadow-none border" id="category" name="category">
                        <option value="0">Všechny kategorie</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['nazev_kategorie'] ?? $category['nazev'] ?? 'Kategorie #'.$category['id']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="author" class="form-label small text-muted">Autor</label>
                    <select class="form-select form-select-sm shadow-none border" id="author" name="author">
                        <option value="0">Všichni autoři</option>
                        <?php foreach ($authors as $author): ?>
                        <option value="<?= $author['id'] ?>" <?= $selectedAuthor == $author['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($author['name'] . ' ' . ($author['surname'] ?? '')) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label small text-muted">Seřadit podle</label>
                    <select class="form-select form-select-sm shadow-none border" id="sort" name="sort">
                        <option value="views_desc" <?= $sort == 'views_desc' ? 'selected' : '' ?>>Počet zobrazení (sestupně)</option>
                        <option value="views_asc" <?= $sort == 'views_asc' ? 'selected' : '' ?>>Počet zobrazení (vzestupně)</option>
                        <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Datum publikace (nejnovější)</option>
                        <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Datum publikace (nejstarší)</option>
                        <option value="title_asc" <?= $sort == 'title_asc' ? 'selected' : '' ?>>Název (A-Z)</option>
                        <option value="title_desc" <?= $sort == 'title_desc' ? 'selected' : '' ?>>Název (Z-A)</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter me-1"></i>Filtrovat
                    </button>
                    <a href="/admin/statistics/articles" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="fas fa-undo-alt me-1"></i>Resetovat
                    </a>
                </div>
            </form>
        </div>
    </section>

    <!-- Souhrné statistiky -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: var(--secondary-color, #f25c78);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($totalArticles) ?></div>
                        <div class="stat-label">Počet článků</div>
                    </div>
                    <div class="stat-icon" style="color: var(--secondary-color, #f25c78);">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: var(--primary-color, #4d5aea);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($totalViews) ?></div>
                        <div class="stat-label">Celkový počet zobrazení</div>
                    </div>
                    <div class="stat-icon" style="color: var(--primary-color, #4d5aea);">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: #ffbb44;"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($avgViewsPerArticle, 1) ?></div>
                        <div class="stat-label">Průměr na článek</div>
                    </div>
                    <div class="stat-icon" style="color: #ffbb44;">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="stat-border" style="background-color: var(--secondary-color, #f25c78);"></div>
                <div class="stat-content">
                    <div>
                        <div class="stat-value"><?= number_format($articlesWithoutViews) ?></div>
                        <div class="stat-label">Články bez zobrazení</div>
                    </div>
                    <div class="stat-icon" style="color: var(--secondary-color, #f25c78);">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabulka se statistikami všech článků -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-bar me-2"></i>Statistiky článků</h2>
        </div>

        <div class="p-0">
            <div class="table-responsive">
                <table id="articlesTable" class="table table-hover">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Název článku</th>
                            <th class="px-3 py-3 border-bottom">Autor</th>
                            <th class="px-3 py-3 border-bottom">Kategorie</th>
                            <th class="px-3 py-3 border-bottom">Datum</th>
                            <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                            <th class="px-3 py-3 border-bottom text-center">Průměr/den</th>
                            <th class="px-3 py-3 border-bottom text-center">Trend</th>
                            <th class="px-3 py-3 border-bottom text-center">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                        <tr class="align-middle">
                            <td class="px-3 py-3 border-bottom">
                                <a href="/admin/article/edit/<?= $article['id'] ?>" class="text-decoration-none fw-medium"><?= htmlspecialchars($article['nazev']) ?></a>
                            </td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['autor']) ?></td>
                            <td class="px-3 py-3 border-bottom"><?= htmlspecialchars($article['kategorie']) ?></td>
                            <td class="px-3 py-3 border-bottom"><?= date('d.m.Y', strtotime($article['datum'])) ?></td>
                            <td class="px-3 py-3 border-bottom text-center fw-bold">
                                <?= number_format($article['total_views'], 0, ',', ' ') ?>
                            </td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <?= number_format($article['avg_views_per_day'], 1, ',', ' ') ?>
                            </td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <?php if (isset($article['trend']) && $article['trend'] > 0): ?>
                                    <span class="badge rounded-pill bg-success px-2 py-1"><i class="fas fa-arrow-up me-1"></i><?= number_format($article['trend'], 1) ?>%</span>
                                <?php elseif (isset($article['trend']) && $article['trend'] < 0): ?>
                                    <span class="badge rounded-pill bg-danger px-2 py-1"><i class="fas fa-arrow-down me-1"></i><?= number_format(abs($article['trend']), 1) ?>%</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary px-2 py-1"><i class="fas fa-equals me-1"></i>0%</span>
                                <?php endif; ?>
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
            <div id="tableInfo" class="mt-3"></div>
            <div id="tablePagination" class="mt-2"></div>
        </div>
    </section>

    <!-- Distribuce zobrazení -->
    <div class="row">
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar me-2"></i>Distribuce zobrazení článků</h2>
                </div>
                <div class="p-3">
                    <div id="viewsDistributionChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line me-2"></i>Trend publikování článků</h2>
                </div>
                <div class="p-3">
                    <div id="publishingTrendChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>
</div>

<!-- Modal pro detail článku -->
<div class="modal fade" id="articleDetailModal" tabindex="-1" aria-labelledby="articleDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="articleDetailModalLabel">Detail článku</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4" id="articleDetailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Načítání...</span>
                    </div>
                    <p class="mt-3">Načítání statistik...</p>
                </div>
                <div id="articleDetailContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="stat-card h-100">
                                <div class="stat-border" style="background-color: var(--primary-color);"></div>
                                <div class="stat-content">
                                    <div>
                                        <div class="stat-value" id="articleDetailTotalViews">0</div>
                                        <div class="stat-label">Celkem zobrazení</div>
                                    </div>
                                    <div class="stat-icon" style="color: var(--primary-color);">
                                        <i class="fa-solid fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card h-100">
                                <div class="stat-border" style="background-color: var(--chart-color-2);"></div>
                                <div class="stat-content">
                                    <div>
                                        <div class="stat-value" id="articleDetailAvgViews">0</div>
                                        <div class="stat-label">Průměrně za den</div>
                                    </div>
                                    <div class="stat-icon" style="color: var(--chart-color-2);">
                                        <i class="fa-solid fa-chart-simple"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section class="content-section mb-0">
                        <div class="section-header">
                            <h2><i class="fas fa-chart-line me-2"></i>Vývoj zobrazení v čase</h2>
                        </div>
                        <div class="p-3">
                            <div id="articleDetailChart" style="height: 350px;"></div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Zavřít</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DataTable
        $('#articlesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/cs.json',
                lengthMenu: "Zobrazit _MENU_ záznamů",
                info: "Zobrazeno _START_ až _END_ z _TOTAL_ záznamů",
                paginate: {
                    first: "První",
                    last: "Poslední",
                    next: "Další",
                    previous: "Předchozí"
                },
                search: ""
            },
            order: [[4, 'desc']],
            pageLength: 10, // Výchozí hodnota vždy 10
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Vše"]],
            dom: '<"d-flex mb-3"l>rt',
            searching: false,
            initComplete: function(settings, json) {
                // Skrýt volbu počtu záznamů, pokud je celkový počet záznamů <= 10
                let api = this.api();
                let totalRecords = api.page.info().recordsTotal;
                let lengthWrapper = $(this).closest('.dataTables_wrapper').find('.dataTables_length');
                
                if (totalRecords <= 10) {
                    // Skrýt volbu počtu záznamů
                    lengthWrapper.hide();
                } else {
                    // Ponechat standardní DataTables funkcionalitu, ale upravit dostupné volby
                    let options = [[10, "10"]];
                    
                    if (totalRecords > 25) options.push([25, "25"]);
                    if (totalRecords > 50) options.push([50, "50"]);
                    if (totalRecords > 100) options.push([100, "100"]);
                    if (totalRecords > 100) options.push([-1, "Vše"]);
                    
                    // Použít nové nastavení lengthMenu
                    api.settings()[0].oInit.lengthMenu = options;
                    
                    // Aktualizovat HTML select se správnými hodnotami
                    let select = lengthWrapper.find('select');
                    select.empty();
                    
                    options.forEach(opt => {
                        let option = $('<option>', {
                            value: opt[0],
                            text: opt[1]
                        });
                        select.append(option);
                    });
                    
                    // Nastavit výchozí hodnotu na 10
                    api.page.len(10).draw();
                    
                    // Upravit styl pro horizontální uspořádání
                    lengthWrapper.addClass('d-flex align-items-center');
                    lengthWrapper.find('label').addClass('d-flex align-items-center mb-0');
                    lengthWrapper.find('select').addClass('mx-2');
                }
                
                // Aktualizovat informace o stránkování a počtu záznamů
                updateTableInfo();
            },
            drawCallback: function(settings) {
                // Aktualizovat informace o stránkování a počtu záznamů při každé změně
                updateTableInfo();
            }
        });
        
        // Funkce pro aktualizaci informací o počtu záznamů a stránkování
        function updateTableInfo() {
            let api = $('#articlesTable').DataTable();
            let info = api.page.info();
            
            // Aktualizace informací o záznamech
            if (info.recordsTotal <= 10) {
                $('#tableInfo').hide();
            } else {
                $('#tableInfo').html('Zobrazeno ' + (info.start + 1) + ' až ' + info.end + ' z ' + info.recordsTotal + ' záznamů').show();
            }
            
            // Aktualizace stránkování
            if (info.pages <= 1) {
                $('#tablePagination').empty().hide();
            } else {
                // Vytvořit minimalistický styl paginace
                let paginationHTML = '<div class="d-flex justify-content-end">';
                paginationHTML += '<div class="pagination-minimal">';
                
                // Tlačítko Previous - jen šipka
                paginationHTML += '<a href="#" class="page-btn ' + (info.page > 0 ? '' : 'disabled') + '" data-page="prev">&laquo;</a>';
                
                let totalPages = info.pages;
                let currentPage = info.page;
                
                // První stránka
                if (currentPage > 1) {
                    paginationHTML += '<a href="#" class="page-btn" data-page="0">1</a>';
                    
                    // Elipsa (menší než obvykle)
                    if (currentPage > 2) {
                        paginationHTML += '<span class="page-sep">...</span>';
                    }
                }
                
                // Maximálně 3 stránky kolem aktuální
                for (let i = Math.max(0, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                    if (i >= 0 && i < totalPages) {
                        paginationHTML += '<a href="#" class="page-btn ' + (i === currentPage ? 'active' : '') + '" data-page="' + i + '">' + (i + 1) + '</a>';
                    }
                }
                
                // Poslední stránka
                if (currentPage < totalPages - 2) {
                    // Elipsa (menší než obvykle)
                    if (currentPage < totalPages - 3) {
                        paginationHTML += '<span class="page-sep">...</span>';
                    }
                    
                    paginationHTML += '<a href="#" class="page-btn" data-page="' + (totalPages - 1) + '">' + totalPages + '</a>';
                }
                
                // Tlačítko Next - jen šipka
                paginationHTML += '<a href="#" class="page-btn ' + (currentPage < totalPages - 1 ? '' : 'disabled') + '" data-page="next">&raquo;</a>';
                
                paginationHTML += '</div></div>';
                
                $('#tablePagination').html(paginationHTML).show();
                
                // Přidat event listenery pro stránkování
                $('#tablePagination a.page-btn').on('click', function(e) {
                    e.preventDefault();
                    
                    if ($(this).hasClass('disabled')) {
                        return false;
                    }
                    
                    let page = $(this).data('page');
                    
                    if (page === 'prev') {
                        api.page('previous').draw('page');
                    } else if (page === 'next') {
                        api.page('next').draw('page');
                    } else {
                        api.page(parseInt(page)).draw('page');
                    }
                });
                
                // Přidat CSS styly pro minimalistickou paginaci
                if (!$('#paginationStyles').length) {
                    $('head').append(`
                        <style id="paginationStyles">
                            .pagination-minimal {
                                display: flex;
                                align-items: center;
                                gap: 2px;
                            }
                            .pagination-minimal .page-btn {
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                min-width: 28px;
                                height: 28px;
                                padding: 0 6px;
                                font-size: 0.8rem;
                                color: #555;
                                background: #f5f5f5;
                                border-radius: 3px;
                                text-decoration: none;
                                transition: all 0.2s ease;
                            }
                            .pagination-minimal .page-btn:hover {
                                background: #e9e9e9;
                                color: #333;
                            }
                            .pagination-minimal .page-btn.active {
                                background: var(--primary-color, #4d5aea);
                                color: white;
                                font-weight: 500;
                            }
                            .pagination-minimal .page-btn.disabled {
                                opacity: 0.5;
                                pointer-events: none;
                            }
                            .pagination-minimal .page-sep {
                                font-size: 0.75rem;
                                opacity: 0.5;
                                margin: 0 2px;
                            }
                        </style>
                    `);
                }
            }
        }

        // Graf distribuce zobrazení
        const viewsDistributionOptions = {
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
                    columnWidth: '60%'
                }
            },
            series: [{
                name: 'Počet článků',
                data: <?= isset($viewsDistribution['counts']) ? json_encode(array_values($viewsDistribution['counts'])) : '[]' ?>
            }],
            xaxis: {
                categories: <?= isset($viewsDistribution['ranges']) ? json_encode(array_values($viewsDistribution['ranges'])) : '[]' ?>,
                title: {
                    text: 'Rozsah zobrazení'
                },
                labels: {
                    style: {
                        colors: '#666',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Počet článků'
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            colors: ['var(--primary-color)'],
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f8f9fd', 'transparent'],
                    opacity: 0.5
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    fontWeight: '500'
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' článků';
                    }
                }
            }
        };
        
        const viewsDistributionChart = new ApexCharts(document.querySelector("#viewsDistributionChart"), viewsDistributionOptions);
        viewsDistributionChart.render();

        // Graf trendu publikování
        const publishingTrendOptions = {
            chart: {
                type: 'area',
                height: 350,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: false
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
                data: <?= isset($publishingTrend['counts']) ? json_encode(array_values($publishingTrend['counts'])) : '[]' ?>
            }],
            xaxis: {
                categories: <?= isset($publishingTrend['periods']) ? json_encode(array_values($publishingTrend['periods'])) : '[]' ?>,
                title: {
                    text: 'Období'
                },
                labels: {
                    style: {
                        colors: '#666',
                        fontSize: '12px'
                    },
                    rotate: -45,
                    rotateAlways: false
                }
            },
            yaxis: {
                title: {
                    text: 'Počet nových článků'
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            colors: ['var(--chart-color-2)'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f8f9fd', 'transparent'],
                    opacity: 0.5
                }
            },
            markers: {
                size: 4,
                colors: ['var(--chart-color-2)'],
                strokeWidth: 0,
                hover: {
                    size: 6
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' článků';
                    }
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
                                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                                zoom: {
                                    enabled: true
                                },
                                toolbar: {
                                    show: false
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
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
                                },
                                labels: {
                                    style: {
                                        colors: '#666',
                                        fontSize: '12px'
                                    },
                                    rotate: -45,
                                    rotateAlways: false
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Počet zobrazení'
                                },
                                labels: {
                                    formatter: function(val) {
                                        return val > 1000 ? (val / 1000).toFixed(1) + 'k' : val;
                                    }
                                }
                            },
                            colors: ['var(--primary-color)'],
                            markers: {
                                size: 5,
                                strokeWidth: 0,
                                hover: {
                                    size: 7
                                }
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                y: {
                                    formatter: function(value) {
                                        return value + ' zobrazení';
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f8f9fd', 'transparent'],
                                    opacity: 0.5
                                }
                            },
                            dataLabels: {
                                enabled: false
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