<section class="content-section">
    <div class="section-header">
        <h2>Správa propagace článků</h2>
        <div>
            <a href="/admin/promotions/create" class="btn btn-action">
                <i class="fas fa-plus-circle me-1"></i> Přidat propagaci
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions">Aktuální propagace</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/admin/promotions/upcoming">Budoucí propagace</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions/history">Historie propagací</a>
        </li>
    </ul>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="artikly-thead">
                <tr>
                    <th>Název článku</th>
                    <th class="text-center">Datum začátku</th>
                    <th class="text-center">Datum konce</th>
                    <th class="text-center">Akce</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promotions as $promo): ?>
                    <tr>
                        <td><?= htmlspecialchars($promo['nazev']) ?></td>
                        <td class="text-center"><?= date("d.m.Y H:i", strtotime($promo['zacatek'])) ?></td>
                        <td class="text-center"><?= date("d.m.Y H:i", strtotime($promo['konec'])) ?></td>
                        <td class="text-center">
                            <a href="/admin/promotions/delete/<?= $promo['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Opravdu odstranit tuto propagaci?')">
                                <i class="fas fa-trash me-1"></i> Odebrat
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($promotions)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-3">Žádné budoucí propagace nebyly nalezeny.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>