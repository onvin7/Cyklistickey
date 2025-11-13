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

<style>
.flashnews-row { cursor: default; }
.flashnews-row.dragging { opacity: 0.6; }
.flashnews-drag-handle { cursor: grab; font-size: 1.1rem; color: #6c757d; }
.flashnews-drag-handle:active { cursor: grabbing; }
.flashnews-action-group .btn { min-width: 90px; }
</style>

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
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
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
                                        <tr class="flashnews-row" data-id="<?= $item['id'] ?>" draggable="true">
                                            <td><?= $item['id'] ?></td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="flashnews-drag-handle" title="Přetáhnout pořadí">↕</span>
                                                    <span class="badge bg-secondary sort-order-badge" title="Aktuální pořadí"><?= $item['sort_order'] ?></span>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Změna pořadí">
                                                        <button type="button" class="btn btn-outline-secondary move-up" title="Posunout nahoru">▲</button>
                                                        <button type="button" class="btn btn-outline-secondary move-down" title="Posunout dolů">▼</button>
                                                    </div>
                                                </div>
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
                                                <div class="d-flex flex-wrap gap-2 flashnews-action-group">
                                                    <form method="POST" action="/admin/flashnews/toggle-active" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <button type="submit" class="btn btn-sm <?= $item['is_active'] ? 'btn-warning' : 'btn-success' ?> text-white">
                                                            <?= $item['is_active'] ? 'Deaktivovat' : 'Aktivovat' ?>
                                                        </button>
                                                    </form>
                                                    <a href="/admin/flashnews/edit?id=<?= $item['id'] ?>" class="btn btn-sm btn-primary text-white">Upravit</a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger delete-flashnews"
                                                            data-id="<?= $item['id'] ?>"
                                                            data-title="<?= htmlspecialchars($item['title']) ?>">
                                                        Smazat
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
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button type="submit" class="btn btn-danger">Smazat</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#flashNewsTable tbody');
    const csrfToken = '<?= htmlspecialchars($csrfToken) ?>';
    const reorderUrl = '/admin/flashnews/reorder';

    function collectOrder() {
        return Array.from(tableBody.querySelectorAll('tr')).map(row => parseInt(row.dataset.id, 10));
    }

    function updateOrderBadges() {
        tableBody.querySelectorAll('tr').forEach((row, index) => {
            const badge = row.querySelector('.sort-order-badge');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    }

    async function sendReorder(order) {
        const formData = new FormData();
        order.forEach(id => formData.append('order[]', id));
        formData.append('csrf_token', csrfToken);

        const response = await fetch(reorderUrl, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.error || 'Neznámá chyba při ukládání pořadí');
        }
    }

    function moveRow(row, direction) {
        if (!row) return;
        const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;
        if (!sibling) return;

        if (direction === 'up') {
            tableBody.insertBefore(row, sibling);
        } else {
            tableBody.insertBefore(sibling, row);
        }

        const order = collectOrder();
        sendReorder(order)
            .then(updateOrderBadges)
            .catch(error => {
                console.error(error);
                alert('Nepodařilo se uložit nové pořadí. Stránka se obnoví.');
                window.location.reload();
            });
    }

    tableBody.querySelectorAll('.move-up').forEach(button => {
        button.addEventListener('click', function () {
            moveRow(button.closest('tr'), 'up');
        });
    });

    tableBody.querySelectorAll('.move-down').forEach(button => {
        button.addEventListener('click', function () {
            moveRow(button.closest('tr'), 'down');
        });
    });

    tableBody.querySelectorAll('tr').forEach(row => {
        row.addEventListener('dragstart', function (event) {
            if (event.target.closest('button, a, form, input')) {
                event.preventDefault();
                return;
            }
            row.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
        });

        row.addEventListener('dragend', function () {
            row.classList.remove('dragging');
        });
    });

    tableBody.addEventListener('dragover', function (event) {
        event.preventDefault();
        const draggingRow = tableBody.querySelector('.dragging');
        if (!draggingRow) return;

        const rows = Array.from(tableBody.querySelectorAll('tr:not(.dragging)'));
        let insertBeforeRow = null;
        for (const currentRow of rows) {
            const box = currentRow.getBoundingClientRect();
            if (event.clientY < box.top + box.height / 2) {
                insertBeforeRow = currentRow;
                break;
            }
        }

        if (insertBeforeRow) {
            tableBody.insertBefore(draggingRow, insertBeforeRow);
        } else {
            tableBody.appendChild(draggingRow);
        }
    });

    tableBody.addEventListener('drop', function (event) {
        event.preventDefault();
        const order = collectOrder();
        sendReorder(order)
            .then(updateOrderBadges)
            .catch(error => {
                console.error(error);
                alert('Nepodařilo se uložit nové pořadí. Stránka se obnoví.');
                window.location.reload();
            });
    });

    document.querySelectorAll('.delete-flashnews').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('deleteId').value = this.dataset.id;
            document.getElementById('deleteTitle').textContent = this.dataset.title;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    updateOrderBadges();
});
</script>

