<div class="nadpis">
    <h1>Články</h1>
    <h2>Všechny články</h2>
</div>

<div class="container-clanky">
    <?php foreach ($articles as $article) : ?>
        <a href="/article/<?php echo htmlspecialchars($article['url']); ?>">
            <div class="card">
                <?php
                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/thumbnails/male/' . ($article["nahled_foto"] ?? '');
                    $imageUrl = !empty($article["nahled_foto"]) && file_exists($imagePath) ? 
                        htmlspecialchars($article["nahled_foto"]) : 
                        'noimage.png';
                ?>
                <?php /*
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo $imageUrl; ?>" alt="<?php echo htmlspecialchars($article["nazev"] ?? ''); ?>">
                */?>
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($article["nazev"] ?? ''); ?></h5>
                    <?php if (!empty($article["perex"])): ?>
                        <p class="truncated-text"><?php echo htmlspecialchars($article["perex"]); ?></p>
                    <?php endif; ?>
                    <div class="card-footer">
                        <?php if (!empty($article["author_name"]) || !empty($article["author_surname"])): ?>
                            <span class="author">
                                <?php echo htmlspecialchars(trim($article["author_name"] . " " . $article["author_surname"])); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($article["datum_vydani"])): ?>
                            <span class="date">
                                <?php echo date('d.m.Y', strtotime($article["datum_vydani"])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
