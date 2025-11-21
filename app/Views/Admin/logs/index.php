<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-file-lines"></i> Logy</h2>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <p class="text-muted">Přehled všech log souborů v systému. Kliknutím na log soubor zobrazíte jeho obsah seřazený od nejnovějších záznamů.</p>
        </div>
    </div>

    <?php if (empty($logs)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle"></i> Žádné log soubory nenalezeny.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                    <tr>
                        <th>Název souboru</th>
                        <th>Velikost</th>
                        <th>Poslední úprava</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <strong><i class="fa-solid fa-file-lines"></i> <?= htmlspecialchars($log['name']) ?></strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= htmlspecialchars($log['size_formatted']) ?></span>
                            </td>
                            <td class="text-center">
                                <?= date('d.m.Y H:i:s', $log['modified']) ?>
                            </td>
                            <td class="text-center">
                                <a href="/admin/logs/view/<?= htmlspecialchars($log['name']) ?>" class="btn btn-sm btn-info">
                                    <i class="fa-solid fa-eye"></i> Zobrazit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

