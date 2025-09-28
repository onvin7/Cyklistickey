<?php
$title = 'Správa Flash News';
$css = ['admin'];

// Zajištění, že jsou proměnné definované
$flashNews = $flashNews ?? [];
$stats = $stats ?? [
    'total' => 0,
    'active' => 0,
    'inactive' => 0,
    'news_count' => 0,
    'tech_count' => 0,
    'custom_count' => 0
];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Správa Flash News</h1>
                <div>
                    <a href="/admin/flashnews/preview" class="btn btn-info me-2" target="_blank">
                        <i class="fas fa-eye"></i> Náhled
                    </a>
                    <form method="POST" action="/admin/flashnews/refresh" style="display: inline;" class="me-2">
                        <input type="hidden" name="csrf_token" value="<?= CSRFHelper::generateToken() ?>">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Opravdu chcete aktualizovat flash news z API? Tím se přepíšou aktuální data.')">
                            <i class="fas fa-sync-alt"></i> Aktualizovat z API
                        </button>
                    </form>
                    <a href="/admin/flashnews/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nová Flash News
                    </a>
                </div>
            </div>

            <!-- Error/Success zprávy -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Statistiky -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['total'] ?? 0 ?></h4>
                            <small>Celkem</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['active'] ?? 0 ?></h4>
                            <small>Aktivní</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['inactive'] ?? 0 ?></h4>
                            <small>Neaktivní</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['news_count'] ?? 0 ?></h4>
                            <small>Novinky</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['tech_count'] ?? 0 ?></h4>
                            <small>Technologie</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-dark text-white">
                        <div class="card-body text-center">
                            <h4><?= $stats['custom_count'] ?? 0 ?></h4>
                            <small>Vlastní</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabulka flash news -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Seznam Flash News</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($flashNews)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Žádné flash news</h5>
                            <p class="text-muted">Začněte vytvořením první flash news.</p>
                            <a href="/admin/flashnews/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Vytvořit první Flash News
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="flashNewsTable">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th width="100">Pořadí</th>
                                        <th>Název</th>
                                        <th width="100">Typ</th>
                                        <th width="100">Stav</th>
                                        <th width="150">Vytvořeno</th>
                                        <th width="200">Akce</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($flashNews as $item): ?>
                                        <tr data-id="<?= $item['id'] ?>">
                                            <td><?= $item['id'] ?></td>
                                            <td>
                                                <input type="number" 
                                                       class="form-control form-control-sm sort-order" 
                                                       value="<?= $item['sort_order'] ?>" 
                                                       data-id="<?= $item['id'] ?>"
                                                       style="width: 60px;">
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;" 
                                                     title="<?= htmlspecialchars($item['title']) ?>">
                                                    <?= htmlspecialchars($item['title']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $item['type'] === 'news' ? 'info' : ($item['type'] === 'tech' ? 'secondary' : 'dark') ?>">
                                                    <?= ucfirst($item['type']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $item['is_active'] ? 'success' : 'danger' ?>">
                                                    <?= $item['is_active'] ? 'Aktivní' : 'Neaktivní' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d.m.Y H:i', strtotime($item['created_at'])) ?>
                                                    <?php if ($item['created_by_name']): ?>
                                                        <br><small>od <?= htmlspecialchars($item['created_by_name']) ?></small>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" 
                                                            class="btn btn-<?= $item['is_active'] ? 'warning' : 'success' ?> toggle-active"
                                                            data-id="<?= $item['id'] ?>"
                                                            title="<?= $item['is_active'] ? 'Deaktivovat' : 'Aktivovat' ?>">
                                                        <i class="fas fa-<?= $item['is_active'] ? 'pause' : 'play' ?>"></i>
                                                    </button>
                                                    <a href="/admin/flashnews/edit?id=<?= $item['id'] ?>" 
                                                       class="btn btn-primary" title="Upravit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger delete-flashnews"
                                                            data-id="<?= $item['id'] ?>"
                                                            data-title="<?= htmlspecialchars($item['title']) ?>"
                                                            title="Smazat">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Potvrzovací dialog pro smazání -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Potvrdit smazání</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Opravdu chcete smazat tuto flash news?</p>
                <p><strong id="deleteTitle"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušit</button>
                <form method="POST" action="/admin/flashnews/delete" style="display: inline;">
                    <input type="hidden" name="id" id="deleteId">
                    <input type="hidden" name="csrf_token" value="<?= CSRFHelper::generateToken() ?>">
                    <button type="submit" class="btn btn-danger">Smazat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle aktivní stav
    document.querySelectorAll('.toggle-active').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/flashnews/toggle-active';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= CSRFHelper::generateToken() ?>';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            
            form.appendChild(csrfInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        });
    });

    // Smazání flash news
    document.querySelectorAll('.delete-flashnews').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('deleteId').value = this.dataset.id;
            document.getElementById('deleteTitle').textContent = this.dataset.title;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Aktualizace pořadí
    document.querySelectorAll('.sort-order').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            const sortOrder = this.value;
            
            fetch('/admin/flashnews/update-sort-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= CSRFHelper::generateToken() ?>'
                },
                body: `id=${id}&sort_order=${sortOrder}&csrf_token=<?= CSRFHelper::generateToken() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Úspěch - možno přidat notifikaci
                } else {
                    alert('Chyba při aktualizaci pořadí');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Chyba při aktualizaci pořadí');
            });
        });
    });
});
</script>
