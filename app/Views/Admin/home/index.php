<!-- Obsah dashboardu -->
<div class="container">
    <h1 class="dashboard-title text-center">Admin Dashboard</h1>

    <section class="content-section">
        <div class="section-header">
            <h2>📄 Nejnovější články</h2>
        </div>
        <ul class="latest-articles-list">
            <?php foreach ($latestArticles as $article): ?>
                <li class="article-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($article['nazev']) ?></strong>
                        <br>
                        <small class="text-muted">Publikováno: <?= htmlspecialchars($article['datum']) ?></small>
                    </div>
                    <a href="/admin/articles/edit/<?= htmlspecialchars($article['id']) ?>" class="btn btn-action">Upravit</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="content-section">
        <div class="section-header">
            <h2>📆 Články z posledních 7 dnů</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Název</th>
                        <th>Datum</th>
                        <th>Autor</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lastWeekArticles)): ?>
                        <tr>
                            <td colspan="4" class="text-center">Za posledních 7 dní nebyly publikovány žádné články.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lastWeekArticles as $article): ?>
                            <tr>
                                <td><?= htmlspecialchars($article['nazev']) ?></td>
                                <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($article['datum']))) ?></td>
                                <td>
                                    <?= htmlspecialchars($article['autor_jmeno'] ?? '') ?> 
                                    <?= htmlspecialchars($article['autor_prijmeni'] ?? '') ?>
                                </td>
                                <td>
                                    <a href="/admin/articles/edit/<?= htmlspecialchars($article['id']) ?>" class="btn btn-action btn-sm">Upravit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="content-section">
        <div class="section-header">
            <h2>📈 Nejčtenější články za posledních 7 dní</h2>
        </div>
        <div id="topArticlesChart" style="width: 100%; height: 500px;"></div>
    </section>
</div>

<!-- ApexCharts knihovna -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Data z controlleru
    const chartData = <?= json_encode($topArticlesData) ?>;
    
    // Inicializace grafu při načtení stránky
    document.addEventListener('DOMContentLoaded', function() {
        // Připravíme data pro sérii
        const series = chartData.articles.map(article => {
            return {
                name: article.nazev,
                data: article.data
            };
        });
    
        // Možnosti grafu
        const options = {
            series: series,
            chart: {
                type: 'line',
                height: 500,
                fontFamily: 'Inter, system-ui, -apple-system, sans-serif',
                toolbar: {
                    show: false
                },
                background: '#fff',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: generateColors(series.length),
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                lineCap: 'round'
            },
            title: {
                text: 'Počet zobrazení za posledních 7 dní',
                align: 'left',
                style: {
                    fontSize: '16px',
                    fontWeight: 600,
                    fontFamily: 'Inter, sans-serif',
                    color: '#3f4254'
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                row: {
                    colors: ['transparent'],
                    opacity: 0.5
                },
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 10
                }
            },
            markers: {
                size: 5,
                strokeWidth: 0,
                hover: {
                    size: 7
                }
            },
            xaxis: {
                categories: chartData.dates.map(date => {
                    const d = new Date(date);
                    return d.getDate() + '.' + (d.getMonth() + 1) + '.';
                }),
                title: {
                    text: 'Den',
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                labels: {
                    style: {
                        colors: '#7E8299',
                        fontSize: '13px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                title: {
                    text: 'Počet zobrazení',
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                min: 0,
                max: 1,
                tickAmount: 1,
                forceNiceScale: false,
                decimalsInFloat: 0,
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    },
                    style: {
                        colors: '#7E8299',
                        fontSize: '13px',
                        fontFamily: 'Inter, sans-serif'
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: false,
                fontSize: '13px',
                fontFamily: 'Inter, sans-serif',
                formatter: function(seriesName, opts) {
                    return seriesName.length > 30 ? seriesName.substring(0, 27) + '...' : seriesName;
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                },
                markers: {
                    width: 12,
                    height: 12,
                    strokeWidth: 0,
                    radius: 12,
                    offsetX: 0,
                    offsetY: 0
                }
            },
            tooltip: {
                theme: 'light',
                marker: {
                    show: true,
                },
                x: {
                    show: true,
                },
                y: {
                    formatter: function (val) {
                        return val + " zobrazení";
                    }
                }
            }
        };
    
        // Vytvoření grafu
        const chart = new ApexCharts(document.querySelector("#topArticlesChart"), options);
        chart.render();
    });

    // Funkce pro generování barev
    function generateColors(count) {
        const colors = [];
        const baseColors = [
            '#4D5AEA', '#F25C78', '#1BD4CD', '#FF9F43', '#8C63FF',
            '#4A89DC', '#54BAB9', '#E84393', '#00B8D9', '#FEBC3B',
            '#20C997', '#805AD5', '#ED8936', '#6B7280', '#3B82F6'
        ];
        
        for (let i = 0; i < count; i++) {
            colors.push(baseColors[i % baseColors.length]);
        }
        
        return colors;
    }
</script>