<?php
$title = 'Náhled Flash News';
$css = ['admin'];

// Zajištění, že jsou proměnné definované
$flashNews = $flashNews ?? [];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Náhled Flash News</h1>
                <a href="/admin/flashnews" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Zpět na seznam
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Náhled na webu</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($flashNews)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Žádné aktivní flash news</h5>
                            <p class="text-muted">Vytvořte a aktivujte flash news pro jejich zobrazení.</p>
                            <a href="/admin/flashnews/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Vytvořit Flash News
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Simulace flash news z webu -->
                        <div class="border rounded p-3 bg-light">
                            <h6 class="text-muted mb-3">Náhled Flash News (jak se zobrazí na webu):</h6>
                            
                            <style>
                                .preview-marquees-wrapper {
                                    position: relative;
                                    height: max-content;
                                    width: 100%;
                                    overflow-x: hidden;
                                    z-index: 9999999999999;
                                    top: 0;
                                }
                                .preview-marquee {
                                    --gap: 2em;
                                    display: flex;
                                    gap: var(--gap);
                                    background-color: rgba(255, 255, 255, 1);
                                    backdrop-filter: blur(20px);
                                    -webkit-backdrop-filter: blur(20px);
                                    overflow: hidden;
                                    user-select: none;
                                    color: #00000f;
                                    padding-top: 10px;
                                    padding-bottom: 10px;
                                    width: 100%;
                                }
                                .preview-marquee ul {
                                    margin-bottom: 0;
                                }
                                .preview-marquee__content {
                                    flex-shrink: 0;
                                    display: flex;
                                    justify-content: space-around;
                                    min-width: 200%;
                                    gap: var(--gap);
                                }
                                .preview-marquee-1 .scroll, .preview-marquee-2 .scroll {
                                    animation: preview-scroll 200s linear infinite;
                                }
                                @keyframes preview-scroll {
                                    from { transform: translateX(0); }
                                    to { transform: translateX(-100%); }
                                }
                                .preview-marquee__content li {
                                    list-style: none;
                                    line-height: normal;
                                    text-transform: uppercase;
                                    font-size: 1.1rem;
                                    letter-spacing: 1px;
                                }
                                .preview-marquee__content li i {
                                    color: #f1008d;
                                }
                                @media screen and (max-width: 850px) {
                                    .preview-marquee__content li {
                                        font-size: 0.8rem;
                                    }
                                }
                            </style>

                            <section class="preview-marquees-wrapper">
                                <div class="preview-marquee preview-marquee-1">
                                    <ul class="preview-marquee__content scroll">
                                        <?php foreach ($flashNews as $entry): ?>
                                            <li><?= htmlspecialchars($entry['title']) ?></li>
                                            <li><i class="fa-solid fa-person-biking"></i></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <ul class="preview-marquee__content scroll" aria-hidden="true">
                                        <?php foreach ($flashNews as $entry): ?>
                                            <li><?= htmlspecialchars($entry['title']) ?></li>
                                            <li><i class="fa-solid fa-person-biking"></i></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </section>
                        </div>

                        <!-- Seznam flash news -->
                        <div class="mt-4">
                            <h6 class="text-muted mb-3">Aktivní Flash News (<?= count($flashNews) ?>):</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Pořadí</th>
                                            <th>Typ</th>
                                            <th>Název</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($flashNews as $entry): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary"><?= $entry['sort_order'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $entry['type'] === 'news' ? 'info' : ($entry['type'] === 'tech' ? 'secondary' : 'dark') ?>">
                                                        <?= ucfirst($entry['type']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 400px;" 
                                                         title="<?= htmlspecialchars($entry['title']) ?>">
                                                        <?= htmlspecialchars($entry['title']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
