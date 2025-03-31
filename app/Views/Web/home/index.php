<!-- Hlavní banner - nejnovější článek -->
<?php

use App\Helpers\TextHelper;
use App\Helpers\FileHelper;

// První článek - banner
if (!empty($articles) && count($articles) > 0) {
    $row = $articles[0];
?>
    <div class="pinned">
        <div class="image" style="background-image: linear-gradient(to top, rgba(0, 0, 15, 1) 0%, rgba(0, 0, 15, 0) 50%), 
                        url('/uploads/thumbnails/velke/<?php echo !empty($row["nahled_foto"]) ? htmlspecialchars($row["nahled_foto"]) : 'noimage.png'; ?>');">
            <div class="pinned-body">
                <h5><?php echo htmlspecialchars($row["nazev"]); ?></h5>
                <div class="cist-clanek">
                    <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">ČÍST ČLÁNEK <i class="fa-solid fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
<?php 
}

// Další články
if (!empty($articles) && count($articles) > 1) { 
?>
<div class="container-clanky">
    <?php 
    for ($i = 1; $i < count($articles); $i++) {
        $row = $articles[$i];
    ?>
        <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">
            <div class="card">
                <div class="image-container">
                    <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($row["nahled_foto"]) ? htmlspecialchars($row["nahled_foto"]) : 'noimage.png'; ?>" alt="Náhled">
                </div>
                <div class="card-body">
                    <div class="kategorie">
                        <?php 
                        // Zpracování kategorií - kontrola jestli existují v novém nebo starém formátu
                        if (!empty($row['kategorie']) && is_array($row['kategorie'])) {
                            // Nový formát - pole objektů
                            foreach ($row['kategorie'] as $kategorie) {
                        ?>
                            <a href='/category/<?php echo htmlspecialchars($kategorie['url']); ?>/'>
                                <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                            </a>
                        <?php
                            }
                        } elseif (!empty($row['kategorie']) && is_string($row['kategorie'])) {
                            // Starý formát - řetězec oddělený čárkami
                            $kategorieArray = explode(', ', $row['kategorie']);
                            foreach ($kategorieArray as $kat) {
                        ?>
                            <a href='/category/<?php echo htmlspecialchars(strtolower($kat)); ?>/'>
                                <p><?php echo htmlspecialchars($kat); ?></p>
                            </a>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <span class="datum"><?php echo \App\Helpers\TimeHelper::getRelativeTime($row['datum']); ?></span>
                    <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">
                        <h5 class="truncated-text"><?php echo htmlspecialchars($row["nazev"]); ?></h5>
                    </a>
                </div>
            </div>
        </a>
    <?php } ?>
</div>
<?php } ?>

<?php include '../app/Views/Web/templates/yt.php'; ?>

<?php if (!empty($categories) && is_array($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <div class="container-clanky-kategorie">
            <div class="card text">
                <h2><?php echo htmlspecialchars($category['nazev_kategorie']); ?></h2>
                <div class="cist-clanek">
                    <a href="/category/<?php echo htmlspecialchars($category['url']); ?>/">
                        ZOBRAZIT KATEGORII <i class="fa-solid fa-angle-right"></i>
                    </a>
                </div>
            </div>

            <?php if (!empty($category['articles']) && is_array($category['articles'])): ?>
                <?php foreach ($category['articles'] as $clanek): ?>
                    <div class="card clanek">
                        <a href="/article/<?php echo htmlspecialchars($clanek['url']); ?>/">
                            <div class="image-container">
                                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($clanek['nahled_foto']) ? htmlspecialchars($clanek['nahled_foto']) : 'noimage.png'; ?>" alt="Náhled článku">
                            </div>
                        </a>
                        <div class="card-body">
                            <div class="gradient"></div>
                                <span class="datum"><?php echo \App\Helpers\TimeHelper::getRelativeTime($clanek['datum']); ?></span>
                            <a href="/article/<?php echo htmlspecialchars($clanek['url']); ?>/">
                                <h5 class="truncated-text"><?php echo htmlspecialchars(TextHelper::truncate($clanek['nazev'], 100)); ?></h5>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Žádné články v této kategorii.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Nebyly nalezeny žádné kategorie.</p>
<?php endif; ?>

<div class="podkategorie-container">
    <div class="text">
        <h1>Poznejte náš tým!</h1>
        <p>Z instagramového meme profilu jsme se rozrostli v přední online destinaci pro cyklisty všech disciplín. Náš tým nadšenců vytváří obsah napříč všemi platformami, pořádá závody a spolupracuje s předními značkami v oboru. Od silničních kol až po MTB, spojujeme komunitu cyklistů a přinášíme jim to nejlepší z cyklistického světa.</p>
    </div>
    <div class="podkategorie">
        <div class="prvek">
            <h2><a href="/kontakt">O NÁS <i class="fa-solid fa-angle-right"></i></a></h2>
        </div>
    </div>
</div>