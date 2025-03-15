<?php use App\Helpers\TextHelper; ?>

<div class="nadpis">
    <h1>Redakce</h1>
    <h2>Naši autoři</h2>
</div>

<div class="authors-page-container">
    <?php foreach ($users as $user) : ?>
        <a href="/user/<?php echo (TextHelper::generateFriendlyUrl($user['name']) . "-" . TextHelper::generateFriendlyUrl($user['surname'])); ?>/" class="author-card">
            <div class="author-image">
                <img loading="lazy" src="/uploads/users/thumbnails/<?php echo !empty($user["profil_foto"]) ? htmlspecialchars($user["profil_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($user["name"] . " " . $user["surname"]); ?>">
            </div>
            <div class="author-info">
                <div>
                    <h5 class="author-name"><?php echo htmlspecialchars($user["name"] . " " . $user["surname"]); ?></h5>
                    <?php if (!empty($user["email"])): ?>
                        <h6 class="author-email"><span><?php echo htmlspecialchars($user["email"]); ?></span></h6>
                    <?php endif; ?>
                </div>
                <?php if (!empty($user["popis"])): ?>
                    <p class="author-description"><?php echo TextHelper::truncate($user["popis"], 120); ?></p>
                <?php endif; ?>
            </div>
            <div class="author-link">
                <span>Zobrazit profil<i class="fa-solid fa-angle-right"></i></span>
            </div>
        </a>
    <?php endforeach; ?>
</div> 