<div class="nadpis">
    <h1><?php echo $user['name'] . " " . $user['surname']; ?></h1>
    <h2><?php echo $user['name'] . " " . $user['surname']; ?></h2>
</div>

<div class="container-clanky">
    <?php foreach ($articles as $result) : ?>
        <a href="/article/<?php echo htmlspecialchars($result['url']); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($result["nahled_foto"]) ? htmlspecialchars($result["nahled_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($result["nazev"]); ?>">
                <div class="card-body">
                    <div class="kategorie">
                        <?php if (!empty($result['kategorie'])): ?>
                            <?php foreach ($result['kategorie'] as $kategorie): ?>
                                <a href="/category/<?php echo htmlspecialchars($kategorie['url']); ?>/">
                                    <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <h5><?php echo htmlspecialchars($result["nazev"]); ?></h5>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>