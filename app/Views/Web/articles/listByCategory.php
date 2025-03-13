<?php
use App\Helpers\FileHelper;

$title = "Kategorie - " . htmlspecialchars($category['nazev_kategorie']);
$description = "Články v kategorii " . htmlspecialchars($category['nazev_kategorie']) . ".";
include '../app/Views/Web/layouts/header.php';
?>

<div class="container mt-4">
    <h1 class="text-center mb-4"><?= htmlspecialchars($category['nazev_kategorie']) ?></h1>

    <?php if (!empty($articles)): ?>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-4">
                    <div class="card mb-3 h-100">
                        <img src="/uploads/thumbnails/<?= !empty($article['nahled_foto']) ? htmlspecialchars($article['nahled_foto']) : 'noimage.png' ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($article['nazev']) ?>">
                        <div class="card-body">
                            <div class="kategorie mb-2">
                                <?php foreach ($article['kategorie'] as $kategorie): ?>
                                    <a href="/category/<?= htmlspecialchars($kategorie['url']) ?>/" class="badge bg-primary text-decoration-none me-1">
                                        <?= htmlspecialchars($kategorie['nazev_kategorie']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($article['nazev']) ?></h5>
                            <p class="card-text">
                                <small class="text-muted"><?= htmlspecialchars(date('d.m.Y', strtotime($article['datum']))) ?></small>
                            </p>
                            <a href="/article/<?= htmlspecialchars($article['url']) ?>" class="btn btn-primary">Číst více</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center">V této kategorii nejsou žádné články.</p>
    <?php endif; ?>
</div>

<?php include '../app/Views/Web/layouts/footer.php'; ?>