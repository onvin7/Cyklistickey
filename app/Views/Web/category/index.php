<div class="nadpis">
    <h1>Kategorie</h1>
    <h2>Kategorie</h2>
</div>

<div class="container-clanky kategorie">
    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>
            <a href="/category/<?php echo htmlspecialchars($category['url']); ?>">
                <div class="card">
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($category['nazev_kategorie']); ?></h5>
                        <?php if (!empty($category['popis'])): ?>
                            <p class="truncated-text"><?php echo htmlspecialchars($category['popis']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-content">
            <p>Žádné kategorie k zobrazení.</p>
        </div>
    <?php endif; ?>
</div>
