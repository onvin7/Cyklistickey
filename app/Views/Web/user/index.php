<?php use App\Helpers\TextHelper; ?>

<div class="nadpis">
    <h1>Redakce</h1>
    <h2>Naši autoři</h2>
</div>

<div class="container-clanky">
    <?php foreach ($users as $user) : ?>
        <a href="/user/<?php echo (TextHelper::generateFriendlyUrl($user['name']) . "-" . TextHelper::generateFriendlyUrl($user['surname'])); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/users/thumbnails/<?php echo !empty($user["profil_foto"]) ? htmlspecialchars($user["profil_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($user["name"] . " " . $user["surname"]); ?>">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($user["name"] . " " . $user["surname"]); ?></h5>
                    <?php if (!empty($user["popis"])): ?>
                        <p class="truncated-text"><?php echo htmlspecialchars($user["popis"]); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div> 