<!-- Hlavní banner - nejnovější článek -->
<?php

use App\Helpers\TextHelper;
use App\Helpers\FileHelper;

for ($i = 0; $i < 1; $i++) {
    $row = $articles[$i];

    if ($row['kategorie']) {
        // Rozdělí řetězec na pole podle ', '
        $articleCategories = explode(', ', $row["kategorie"]);

        // Vybere náhodný klíč z pole kategorií
        $randomKey = array_rand($articleCategories);

        // Získá náhodně vybranou kategorii pomocí náhodného klíče
        $kategorie = $articleCategories[$randomKey];
    } else {
        $kategorie = "Magazín";
    }

?>
    <div class="pinned">
        <div class="image" style="background-image: linear-gradient(to top, rgba(0, 0, 15, 1) 0%, rgba(0, 0, 15, 0) 50%), 
                        url('/uploads/thumbnails/velke/<?php echo htmlspecialchars($row["nahled_foto"]); ?>');">
            <div class="pinned-body">
                <h5><?php echo htmlspecialchars($row["nazev"]); ?></h5>
                <div class="cist-clanek">
                    <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">ČÍST ČLÁNEK <i class="fa-solid fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<style>
    @media screen and (max-width: 750px) {
        .pinned .image {
            height: 80vh;
            background-image: linear-gradient(to top, rgba(0, 0, 15, 1) 0%, rgba(0, 0, 15, 0) 80%),
                url('/uploads/thumbnails/velke/<?php echo htmlspecialchars($row["nahled_foto"]); ?>') !important;
        }
    }
</style>

<div class="container-clanky">
    <?php for ($i = 1; $i < count($articles); $i++) {
        $row = $articles[$i];

        if ($row['kategorie']) {
            // Rozdělí řetězec na pole podle ', '
            $articleCategories = explode(', ', $row["kategorie"]);

            // Vybere náhodný klíč z pole kategorií
            $randomKey = array_rand($articleCategories);

            // Získá náhodně vybranou kategorii pomocí náhodného klíče
            $kategorie = $articleCategories[$randomKey];
        } else {
            $kategorie = "Magazín";
        }
    ?>
        <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($row["nahled_foto"]) ? htmlspecialchars($row["nahled_foto"]) : 'noimage.png'; ?>" alt="Náhled">
                <div class="card-body">
                    <div class="kategorie">
                        <?php
                        if ($row['kategorie']) {
                            $articleCategories = explode(', ', $row["kategorie"]);
                            foreach ($articleCategories as $category) {
                                echo "<a href='/category/" . htmlspecialchars($category) . "/'><p>" . htmlspecialchars($category) . "</p></a>";
                            }
                        }
                        ?>
                    </div>
                    <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">
                        <h5 class="truncated-text"><?php echo htmlspecialchars($row["nazev"]); ?></h5>
                    </a>
                </div>
            </div>
        </a>
    <?php } ?>
</div>

<?php include '../app/Views/Web/templates/yt.php'; ?>
<?php if (!empty($categories) && is_array($categories)): ?>
    <?php foreach ($categories as $category): ?>
        <div class="container-clanky-kategorie">
            <div class="card text">
                <h2><?php echo htmlspecialchars($category['nazev_kategorie']); ?></h3>
                    <div class="cist-clanek">
                        <a href="/category/<?php echo htmlspecialchars($category['url']); ?>/">
                            ZOBRAZIT KATEGORII <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </div>
            </div>

            <?php if (!empty($category['articles']) && is_array($category['articles'])): ?>
                <?php foreach ($category['articles'] as $clanek): ?>

                    <div class="card">
                        <a href="/article/<?php echo htmlspecialchars($clanek['url']); ?>/">
                            <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($clanek['nahled_foto']) ? htmlspecialchars($clanek['nahled_foto']) : 'noimage.png'; ?>" alt="Náhled článku">
                        </a>
                        <div class="card-body">
                            <div class="gradient"></div>
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
        <h1>Zajímá tě<br> nějaká konkrétní disciplína?</h1>
    </div>
    <div class="podkategorie">

        <div class="prvek">
            <h2><a href="#">test</a></h2>
        </div>
    </div>
</div>