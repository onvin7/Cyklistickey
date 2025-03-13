<?php use App\Helpers\TextHelper; ?>

<div class="container-zobrazit-user">
    <div class="text">
        <img loading="lazy" src='/uploads/users/thumbnails/<?php echo !empty($user["profil_foto"]) ? htmlspecialchars($user["profil_foto"]) : 'noimage.png'; ?>' alt='<?php echo ($user['name'] . " " . $user['surname']); ?>'>

        <h1><?php echo ($user['name'] . " " . $user['surname']); ?></h1>

        <div class="stats">
            <div class="pocet">
                <h2><?php echo $user['views']; ?> článků</h2>
            </div> 
            <div class="socky">
                <?php foreach ($socials as $social) { ?>
                    <a href="<?php echo $social['link']; ?>" target="_blank"><i class="<?php echo $social['fa_class']; ?>"></i></a>
                <?php }?>
            </div>
        </div>
        <div class="text-editor">
            <?php echo $user['popis']; ?>
        </div>
    </div>
</div>

<div class="user-articles-button">
    <a href="/user/<?php echo (TextHelper::generateFriendlyUrl($user['name']) . "-" . TextHelper::generateFriendlyUrl($user['surname'])); ?>/articles/" class="btn-view-articles">
        <span class="text">Zobrazit všechny články</span>
    </a>
</div>

<div class="container-clanky">
    <?php foreach ($relatedArticles as $article) : ?>
        <a href="/article/<?php echo htmlspecialchars($article['url']); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($article["nahled_foto"]) ? htmlspecialchars($article["nahled_foto"]) : 'noimage.png'; ?>" alt="Náhled">
                <div class="card-body">
                    <div class="kategorie">
                        <?php foreach ($article['category'] as $category) {?>
                            <a href='/category/<?php echo htmlspecialchars($category["url_kategorie"]);?>'><p><?php echo htmlspecialchars($category["nazev_kategorie"]);?></p></a>
                        <?php }?>
                    </div>
                    <a href="/article/<?php echo htmlspecialchars($article['url']); ?>/">
                        <h5><?php echo htmlspecialchars($article["nazev"]); ?></h5>
                    </a>        
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>