<div class="container mt-4">
    <h2 class="mb-4">📅 Budoucí propagace článků</h2>

    <nav class="nav nav-pills mb-4">
        <a class="nav-link" href="/admin/promotions">📢 Aktuální propagace</a>
        <a class="nav-link active" href="/admin/promotions/upcoming">📅 Budoucí propagace</a>
        <a class="nav-link" href="/admin/promotions/history">📜 Historie propagací</a>
        <a class="nav-link btn btn-success text-white" href="/admin/promotions/create">➕ Přidat propagaci</a>
    </nav>

    <div class="card">
        <div class="card-header">📅 Naplánované propagace</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Název článku</th>
                        <th>Od</th>
                        <th>Do</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promotions as $promo): ?>
                        <tr>
                            <td><?= htmlspecialchars($promo['nazev']) ?></td>
                            <td><?= date("d.m.Y H:i", strtotime($promo['zacatek'])) ?></td>
                            <td><?= date("d.m.Y H:i", strtotime($promo['konec'])) ?></td>
                            <td>
                                <a href="/admin/promotions/delete/<?= $promo['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Opravdu odstranit tuto propagaci?')">✖️ Odebrat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>