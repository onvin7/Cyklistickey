<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-mouse-pointer"></i> Statistiky kliků na odkazy</h2>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <p class="text-muted">Přehled všech kliků na externí odkazy v článcích. Kliky jsou automaticky sledovány pro všechny externí odkazy v obsahu článků.</p>
        </div>
    </div>

    <?php if (empty($clicksByArticle)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i> Zatím nebyly zaznamenány žádné kliky na odkazy.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="artikly-thead text-center">
                    <tr>
                        <th>ID</th>
                        <th>Název</th>
                        <th>Počet odkazů</th>
                        <th>Celkový počet kliků</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php foreach ($clicksByArticle as $data): ?>
                        <tr>
                            <td><?= htmlspecialchars($data['article']['id']) ?></td>
                            <td><?= htmlspecialchars($data['article']['nazev']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= count($data['links']) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-success" style="font-size: 1.1em;">
                                    <?= number_format($data['total_clicks'], 0, ',', ' ') ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/link-clicks/article/<?= htmlspecialchars($data['article']['id']) ?>" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

