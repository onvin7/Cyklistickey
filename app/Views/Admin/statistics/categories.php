<div class="container-fluid px-4">
    <h1 class="dashboard-title mb-4"><i class="fa-solid fa-tags me-2"></i>Statistiky kategorií</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/statistics">Statistiky</a></li>
        <li class="breadcrumb-item active">Kategorie</li>
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
                        <div class="stat-value"><?= number_format($totalCategories) ?></div>
                        <div class="stat-label">Počet kategorií</div>
                        </div>
                    <div class="stat-icon" style="color: var(--primary-color);">
                            <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-2);"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($totalViews) ?></div>
                        <div class="stat-label">Celkový počet zobrazení</div>
                        </div>
                    <div class="stat-icon" style="color: var(--chart-color-2);">
                            <i class="fa-solid fa-eye"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-5);"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($avgArticlesPerCategory, 1) ?></div>
                        <div class="stat-label">Průměr článků v kategorii</div>
                        </div>
                    <div class="stat-icon" style="color: var(--chart-color-5);">
                            <i class="fa-solid fa-calculator"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card mb-4">
                <div class="stat-border" style="background-color: var(--chart-color-4);"></div>
                <div class="stat-content">
                        <div>
                        <div class="stat-value"><?= number_format($emptyCategories) ?></div>
                        <div class="stat-label">Prázdné kategorie</div>
                        </div>
                    <div class="stat-icon" style="color: var(--chart-color-4);">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafy -->
    <div class="row">
        <!-- Kategorie podle počtu zobrazení -->
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar me-2"></i>Kategorie podle počtu zobrazení</h2>
                </div>
                <div class="p-0">
                    <div id="categoriesViewsChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
        <!-- Kategorie podle počtu článků -->
        <div class="col-xl-6">
            <section class="content-section mb-4">
                <div class="section-header">
                    <h2><i class="fas fa-chart-bar me-2"></i>Kategorie podle počtu článků</h2>
                </div>
                <div class="p-0">
                    <div id="categoriesArticlesChart" style="height: 350px;"></div>
                </div>
            </section>
        </div>
    </div>

    <!-- Trend kategorií v čase -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-line me-2"></i>Trend zobrazení kategorií v čase</h2>
        </div>
        <div class="p-0">
            <div id="categoriesTrendChart" style="height: 400px;"></div>
        </div>
    </section>

    <!-- Přehled kategorií - kombinovaný graf -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-chart-bar me-2"></i>Přehled kategorií</h2>
    </div>
        <div class="p-0">
            <div id="categoriesOverviewChart" style="height: 400px;"></div>
        </div>
    </section>

    <!-- Tabulka kategorií -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-table me-2"></i>Statistiky kategorií</h2>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-hover">
                <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-3 border-bottom">Název kategorie</th>
                            <th class="px-3 py-3 border-bottom">Počet článků</th>
                            <th class="px-3 py-3 border-bottom text-center">Celkem zobrazení</th>
                            <th class="px-3 py-3 border-bottom text-center">Průměrný počet zobrazení na 1 článek</th>
                            <th class="px-3 py-3 border-bottom text-center">Trend</th>
                            <th class="px-3 py-3 border-bottom text-center">Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categoriesStats as $category): ?>
                        <tr class="align-middle">
                            <td class="px-3 py-3 border-bottom">
                                <a href="/admin/category/edit/<?= $category['id'] ?>" class="text-decoration-none fw-medium"><?= htmlspecialchars($category['name']) ?></a>
                            </td>
                            <td class="px-3 py-3 border-bottom"><?= number_format($category['articles_count']) ?></td>
                            <td class="px-3 py-3 border-bottom text-center fw-bold"><?= number_format($category['views']) ?></td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <?php if ($category['articles_count'] > 0): ?>
                                    <div>
                                        <span class="fw-medium"><?= isset($category['avg_views_per_article']) && $category['avg_views_per_article'] > 0 
                                            ? number_format($category['avg_views_per_article'], 1) 
                                            : number_format($category['views'] / max(1, $category['articles_count']), 1) ?></span>
                                        <div class="small text-muted">zobrazení na článek</div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 border-bottom text-center">
                                <?php if (isset($category['trend']) && $category['trend'] !== null): ?>
                            <?php if ($category['trend'] > 0): ?>
                                        <span class="badge rounded-pill bg-success px-2 py-1"><i class="fas fa-arrow-up me-1"></i><?= number_format($category['trend'], 1) ?>%</span>
                            <?php elseif ($category['trend'] < 0): ?>
                                        <span class="badge rounded-pill bg-danger px-2 py-1"><i class="fas fa-arrow-down me-1"></i><?= number_format(abs($category['trend']), 1) ?>%</span>
                            <?php else: ?>
                                        <span class="badge rounded-pill bg-secondary px-2 py-1"><i class="fas fa-equals me-1"></i>0%</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary px-2 py-1"><i class="fas fa-minus me-1"></i>0%</span>
                            <?php endif; ?>
                        </td>
                            <td class="px-3 py-3 border-bottom text-center">
                            <button type="button" class="btn btn-sm btn-primary view-detail" data-category-id="<?= $category['id'] ?>" data-category-name="<?= htmlspecialchars($category['name']) ?>">
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

    <!-- Souvislosti mezi kategoriemi -->
    <section class="content-section mb-4">
        <div class="section-header">
            <h2><i class="fas fa-project-diagram me-2"></i>Souvislosti mezi kategoriemi</h2>
        </div>
        <div class="p-0">
            <div id="categoriesHeatmap" style="height: 500px;"></div>
            <div class="text-muted p-3 mt-2">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Heatmapa zobrazuje, jak často se kategorie vyskytují společně u stejných článků. Tmavší barva znamená častější souvislost.
                </small>
            </div>
        </div>
    </section>
</div>

<!-- Modal pro detail kategorie -->
<div class="modal fade" id="categoryDetailModal" tabindex="-1" aria-labelledby="categoryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="categoryDetailModalLabel">Detail kategorie</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="categoryDetailLoading">
                    <div class="spinner-border" style="color: var(--primary-color)" role="status">
                        <span class="visually-hidden">Načítání...</span>
                    </div>
                    <p class="mt-2">Načítání statistik...</p>
                </div>
                <div id="categoryDetailContent" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-border" style="background-color: var(--primary-color);"></div>
                                <div class="stat-content">
                                    <div>
                                        <div class="stat-value" id="categoryDetailTotalViews">0</div>
                                        <div class="stat-label">Celkem zobrazení</div>
                                    </div>
                                    <div class="stat-icon" style="color: var(--primary-color);">
                                        <i class="fa-solid fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-border" style="background-color: var(--chart-color-2);"></div>
                                <div class="stat-content">
                                    <div>
                                        <div class="stat-value" id="categoryDetailArticlesCount">0</div>
                                        <div class="stat-label">Počet článků</div>
                                    </div>
                                    <div class="stat-icon" style="color: var(--chart-color-2);">
                                        <i class="fa-solid fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-card">
                                <div class="stat-border" style="background-color: var(--chart-color-5);"></div>
                                <div class="stat-content">
                                    <div>
                                        <div class="stat-value" id="categoryDetailAvgViews">0</div>
                                        <div class="stat-label">Průměr na článek</div>
                                </div>
                                    <div class="stat-icon" style="color: var(--chart-color-5);">
                                        <i class="fa-solid fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                    
                    <section class="content-section mb-4">
                        <div class="section-header">
                            <h2><i class="fas fa-chart-line me-2"></i>Vývoj zobrazení v čase</h2>
                        </div>
                        <div class="p-0">
                    <div id="categoryDetailChart" style="height: 300px;"></div>
                        </div>
                    </section>
                    
                    <section class="content-section">
                        <div class="section-header">
                            <h2><i class="fas fa-star me-2"></i>Nejčtenější články v kategorii</h2>
                        </div>
                    <div class="table-responsive">
                            <table class="table table-hover" id="categoryDetailArticlesTable">
                            <thead>
                                    <tr class="bg-light">
                                        <th class="px-3 py-3 border-bottom">Název článku</th>
                                        <th class="px-3 py-3 border-bottom">Autor</th>
                                        <th class="px-3 py-3 border-bottom">Datum</th>
                                        <th class="px-3 py-3 border-bottom text-center">Zobrazení</th>
                                </tr>
                            </thead>
                            <tbody id="categoryDetailArticlesList">
                                <!-- Zde se dynamicky načtou články -->
                            </tbody>
                        </table>
                    </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                <a href="#" id="categoryDetailViewAllBtn" class="btn btn-primary">
                    <i class="fas fa-list me-1"></i>Zobrazit všechny články
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DataTable
        $('#categoriesTable').DataTable({
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
            order: [[2, 'desc']], // Řazení podle celkem zobrazení sestupně
            pageLength: 10, // Výchozí hodnota vždy 10
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Vše"]],
            dom: '<"d-flex mb-3"l>rt',
            searching: false,
            columnDefs: [
                { orderable: false, targets: 5 } // Sloupec "Akce" není řaditelný (index 5)
            ],
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
            let api = $('#categoriesTable').DataTable();
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
        
        // Automatické odeslání formuláře při změně hodnoty filtru
        document.getElementById('period').addEventListener('change', function() {
            document.getElementById('period-filter').submit();
        });

        // Graf kategorií podle počtu zobrazení
        const categoriesViewsOptions = {
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                foreColor: '#3f4254',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 5,
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return new Intl.NumberFormat('cs-CZ').format(val);
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            series: [{
                name: 'Počet zobrazení',
                data: <?= !empty(array_column($categoriesStats, 'views')) ? 
                    json_encode(array_column($categoriesStats, 'views')) : 
                    json_encode([0]) ?>
            }],
            xaxis: {
                categories: <?= !empty(array_column($categoriesStats, 'name')) ? 
                    json_encode(array_column($categoriesStats, 'name')) : 
                    json_encode(['Žádná data']) ?>,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    rotate: -45,
                    rotateAlways: true
                }
            },
            yaxis: {
                title: {
                    text: 'Počet zobrazení',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: function(val) {
                        return new Intl.NumberFormat('cs-CZ').format(val);
                    }
                }
                    },
                    legend: {
                show: false
            },
            colors: [
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-1').trim() || '#4d5aea',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-2').trim() || '#1bd4cd',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-3').trim() || '#f25c78',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-4').trim() || '#ffbb44',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-5').trim() || '#7367f0'
            ],
            tooltip: {
                y: {
                    formatter: function(val) {
                        return new Intl.NumberFormat('cs-CZ').format(val) + ' zobrazení';
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4
            },
            noData: {
                text: 'Žádná data k zobrazení',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '16px'
                }
            }
        };
        
        const categoriesViewsChart = new ApexCharts(document.querySelector("#categoriesViewsChart"), categoriesViewsOptions);
        categoriesViewsChart.render();

        // Graf kategorií podle počtu článků
        const categoriesArticlesOptions = {
            chart: {
                type: 'bar',
                height: 350,
                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                foreColor: '#3f4254',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    columnWidth: '70%',
                    borderRadius: 4,
                    distributed: true,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            series: [{
                name: 'Počet článků',
                data: <?= !empty(array_column($categoriesStats, 'articles_count')) ? json_encode(array_column($categoriesStats, 'articles_count')) : json_encode([0]) ?>
            }],
            xaxis: {
                categories: <?= !empty(array_column($categoriesStats, 'name')) ? json_encode(array_column($categoriesStats, 'name')) : json_encode(['Žádná data']) ?>,
                title: {
                    text: 'Počet článků',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                }
            },
            colors: [
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-1').trim() || '#4d5aea',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-2').trim() || '#1bd4cd',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-3').trim() || '#f25c78',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-4').trim() || '#ffbb44',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-5').trim() || '#7367f0'
            ],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return new Intl.NumberFormat('cs-CZ').format(val);
                },
                offsetX: 20,
                style: {
                    fontSize: '12px',
                    colors: ['#333'],
                    fontWeight: 600
                }
            },
            tooltip: {
                enabled: true,
                style: {
                    fontSize: '14px'
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4
            },
            noData: {
                text: 'Žádná data k zobrazení',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '16px'
                }
            },
            legend: {
                show: false
            }
        };
        
        const categoriesArticlesChart = new ApexCharts(document.querySelector("#categoriesArticlesChart"), categoriesArticlesOptions);
        categoriesArticlesChart.render();

        // Trend kategorií v čase - zobrazíme jen top 5 kategorií
        let trendSeriesData = [];
        let trendDates = [];
        
        // Získáme data a připravíme je pro graf
        <?php if (isset($categoriesTrendData) && !empty($categoriesTrendData)): ?>
            trendSeriesData = <?= json_encode($categoriesTrendData['series']) ?>;
            trendDates = <?= json_encode($categoriesTrendData['dates']) ?>;
        <?php else: ?>
            // Ukázková data, pokud nejsou k dispozici skutečná data
            trendSeriesData = [
                { name: 'Kategorie 1', data: [10, 15, 12, 18, 20] },
                { name: 'Kategorie 2', data: [5, 8, 10, 12, 15] },
                { name: 'Kategorie 3', data: [20, 18, 15, 10, 8] }
            ];
            trendDates = ['2023-01-01', '2023-01-08', '2023-01-15', '2023-01-22', '2023-01-29'];
        <?php endif; ?>
        
        // Omezíme počet zobrazených kategorií na 5 nejsledovanějších
        if (trendSeriesData.length > 5) {
            // Seřadíme podle součtu hodnot (celkového počtu zobrazení) a vezmeme top 5
            trendSeriesData.sort((a, b) => {
                const sumA = a.data.reduce((sum, val) => sum + val, 0);
                const sumB = b.data.reduce((sum, val) => sum + val, 0);
                return sumB - sumA;
            });
            trendSeriesData = trendSeriesData.slice(0, 5);
        }
        
        const categoriesTrendOptions = {
            chart: {
                type: 'line',
                height: 400,
                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                foreColor: '#3f4254',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    speed: 500
                }
            },
            series: trendSeriesData,
            xaxis: {
                type: 'category',
                categories: trendDates,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    rotate: -45,
                    rotateAlways: true,
                    formatter: function(value) {
                        // Zkontrolujeme, zda je datum ve formátu ISO
                        if (value && value.includes('-')) {
                            const parts = value.split('-');
                            // Pokud je to kompletní datum (YYYY-MM-DD)
                            if (parts.length === 3) {
                                return parts[2] + '.' + parts[1] + '.';
                            } 
                            // Pokud je to jen měsíc (YYYY-MM)
                            else if (parts.length === 2) {
                                return parts[1] + '/' + parts[0].substring(2);
                            }
                        }
                        return value;
                    }
                },
                title: {
                    text: 'Datum',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Počet zobrazení',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                },
                min: 0,
                labels: {
                    formatter: function(val) {
                        return new Intl.NumberFormat('cs-CZ').format(val);
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            markers: {
                size: 4,
                hover: {
                    size: 6
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '14px'
            },
            tooltip: {
                enabled: true,
                shared: true,
                style: {
                    fontSize: '14px'
                },
                y: {
                    formatter: function(val) {
                        return new Intl.NumberFormat('cs-CZ').format(val) + ' zobrazení';
                    }
                }
            },
            colors: [
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-1').trim() || '#4d5aea',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-2').trim() || '#1bd4cd',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-3').trim() || '#f25c78',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-4').trim() || '#ffbb44',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-5').trim() || '#7367f0'
            ],
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4
            },
            noData: {
                text: 'Žádná data k zobrazení',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '16px'
                }
            }
        };
        
        const categoriesTrendChart = new ApexCharts(document.querySelector("#categoriesTrendChart"), categoriesTrendOptions);
        categoriesTrendChart.render();

        // Heatmapa souvislostí mezi kategoriemi
        const categoriesHeatmapOptions = {
            chart: {
                type: 'heatmap',
                height: 500,
                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                foreColor: '#3f4254',
                animations: {
                    enabled: true,
                    speed: 500
                }
            },
            plotOptions: {
                heatmap: {
                    shadeIntensity: 0.5,
                    radius: 4,
                    useFillColorAsStroke: false,
                    distributed: false,
                    enableShades: true,
                    colorScale: {
                        ranges: [{
                            from: 0,
                            to: 0,
                            name: 'Žádná souvislost',
                            color: '#F0F0F0'
                        }],
                        min: 0,
                        inverse: false
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            series: <?= isset($categoriesCorrelationData) && !empty($categoriesCorrelationData) ? json_encode($categoriesCorrelationData) : json_encode([['name' => 'Žádná data', 'data' => [[0, 0, 0]]]]) ?>,
            xaxis: {
                categories: <?= !empty(array_column($categoriesStats, 'name')) ? json_encode(array_column($categoriesStats, 'name')) : json_encode(['Žádná data']) ?>,
                labels: {
                    rotate: -45,
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    }
                },
                title: {
                    text: 'Kategorie',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                categories: <?= !empty(array_column($categoriesStats, 'name')) ? json_encode(array_column($categoriesStats, 'name')) : json_encode(['Žádná data']) ?>,
                title: {
                    text: 'Kategorie',
                    style: {
                        fontSize: '14px',
                        fontWeight: 600
                    }
                }
            },
            tooltip: {
                enabled: true,
                style: {
                    fontSize: '14px'
                },
                theme: 'light',
                custom: function({series, seriesIndex, dataPointIndex, w}) {
                    const xCategory = w.globals.labels[dataPointIndex];
                    const yCategory = w.globals.seriesNames[seriesIndex];
                    const value = series[seriesIndex][dataPointIndex];
                    
                    if (value === 0) {
                        return '<div class="p-3 bg-light rounded-3 shadow-sm border">' +
                            '<div class="mb-1"><strong>' + yCategory + '</strong> a <strong>' + xCategory + '</strong></div>' +
                            '<div class="text-muted">Nemají žádné společné články</div>' +
                        '</div>';
                    }
                    
                    return '<div class="p-3 bg-light rounded-3 shadow-sm border">' +
                        '<div class="mb-1"><strong>' + yCategory + '</strong> a <strong>' + xCategory + '</strong></div>' +
                        '<div>Počet společných článků: <strong>' + value + '</strong></div>' +
                    '</div>';
                }
            },
            colors: [
                getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4d5aea'
            ],
            grid: {
                borderColor: '#f1f1f1',
                padding: {
                    right: 10,
                    left: 10
                }
            },
            stroke: {
                width: 1,
                colors: ['#fff']
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
                        
                        if (data.topArticles && data.topArticles.length > 0) {
                        data.topArticles.forEach(article => {
                            const row = document.createElement('tr');
                                row.className = 'align-middle';
                            row.innerHTML = `
                                    <td class="px-3 py-3 border-bottom">
                                        <a href="/admin/article/edit/${article.id}" class="text-decoration-none fw-medium">${article.nazev}</a>
                                    </td>
                                    <td class="px-3 py-3 border-bottom">${article.autor}</td>
                                    <td class="px-3 py-3 border-bottom">${new Date(article.datum).toLocaleDateString('cs-CZ')}</td>
                                    <td class="px-3 py-3 border-bottom text-center fw-bold">${new Intl.NumberFormat('cs-CZ').format(article.views)}</td>
                            `;
                            articlesList.appendChild(row);
                        });
                        } else {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td colspan="4" class="text-center py-3">
                                    <div class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nejsou k dispozici žádné články pro tuto kategorii.
                                    </div>
                                </td>
                            `;
                            articlesList.appendChild(row);
                        }
                        
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
                                },
                                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                                foreColor: '#3f4254',
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
                                data: data.viewsTrend.data
                            }],
                            xaxis: {
                                categories: data.viewsTrend.dates,
                                title: {
                                    text: 'Datum',
                                    style: {
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                },
                                labels: {
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Počet zobrazení',
                                    style: {
                                        fontSize: '14px',
                                        fontWeight: 600
                                    }
                                },
                                labels: {
                                    formatter: function(val) {
                                        return new Intl.NumberFormat('cs-CZ').format(val);
                                    }
                                }
                            },
                            colors: [getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4d5aea'],
                            markers: {
                                size: 4,
                                strokeWidth: 0,
                                hover: {
                                    size: 6
                                }
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                style: {
                                    fontSize: '14px'
                                },
                                theme: 'light',
                                y: {
                                    formatter: function(val) {
                                        return new Intl.NumberFormat('cs-CZ').format(val) + ' zobrazení';
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                                strokeDashArray: 4,
                                padding: {
                                    left: 10,
                                    right: 10
                                }
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shade: 'light',
                                    type: 'vertical',
                                    shadeIntensity: 0.3,
                                    opacityFrom: 0.6,
                                    opacityTo: 0.1,
                                    stops: [0, 100]
                                }
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

        // Kombinovaný graf pro přehled kategorií (články a zobrazení)
        const categoriesOverviewOptions = {
            chart: {
                type: 'bar',
                height: 400,
                stacked: false,
                fontFamily: '"Inter", system-ui, -apple-system, sans-serif',
                foreColor: '#3f4254',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 5,
                }
            },
            series: [
                {
                    name: 'Počet článků',
                    type: 'column',
                    data: <?= !empty(array_column($categoriesStats, 'articles_count')) ? 
                        json_encode(array_column($categoriesStats, 'articles_count')) : 
                        json_encode([0]) ?>
                },
                {
                    name: 'Počet zobrazení',
                    type: 'line',
                    data: <?= !empty(array_column($categoriesStats, 'views')) ? 
                        json_encode(array_column($categoriesStats, 'views')) : 
                        json_encode([0]) ?>
                }
            ],
            xaxis: {
                categories: <?= !empty(array_column($categoriesStats, 'name')) ? 
                    json_encode(array_column($categoriesStats, 'name')) : 
                    json_encode(['Žádná data']) ?>,
                labels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    rotate: -45,
                    rotateAlways: true
                }
            },
            yaxis: [
                {
                    title: {
                        text: 'Počet článků',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('cs-CZ').format(val);
                        }
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Počet zobrazení',
                        style: {
                            fontSize: '14px',
                            fontWeight: 600
                        }
                    },
                    labels: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('cs-CZ').format(val);
                        }
                    }
                }
            ],
            markers: {
                size: 5,
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            colors: [
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-2').trim() || '#1bd4cd',
                getComputedStyle(document.documentElement).getPropertyValue('--chart-color-1').trim() || '#4d5aea'
            ],
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                fontSize: '14px'
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val, { seriesIndex }) {
                        if(seriesIndex === 0) {
                            return new Intl.NumberFormat('cs-CZ').format(val) + ' článků';
                        } else {
                            return new Intl.NumberFormat('cs-CZ').format(val) + ' zobrazení';
                        }
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4
            },
            noData: {
                text: 'Žádná data k zobrazení',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    fontSize: '16px'
                }
            }
        };
        
        const categoriesOverviewChart = new ApexCharts(document.querySelector("#categoriesOverviewChart"), categoriesOverviewOptions);
        categoriesOverviewChart.render();
    });
</script> 