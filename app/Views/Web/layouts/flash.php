<?php

use App\Models\FlashNewsJSONSimple;

$flashNews = [];
try {
    $flashNewsModel = new FlashNewsJSONSimple();
    $flashNews = $flashNewsModel->getForDisplay();
} catch (Throwable $e) {
    $flashNews = [];
}

$typeIcons = [
    'news' => '📰',
    'tech' => '⚙️',
    'custom' => '🚴'
];
?>

<style>
    .marquees-wrapper {
        position: relative;
        height: max-content;
        width: 100%;
        overflow-x: hidden;
        z-index: 9999999999999;
        top: 0;
    }

    .marquee {
        --gap: 2em;
        display: flex;
        gap: var(--gap);
        background-color: rgba(255, 255, 255, 1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        overflow: hidden;
        user-select: none;
        color: #00000f;
        padding: 10px 0;
        width: 100%;
    }

    .marquee ul {
        margin-bottom: 0;
    }

    .marquee__content {
        flex-shrink: 0;
        display: flex;
        justify-content: space-around;
        min-width: 200%;
        gap: var(--gap);
    }

    .marquee-1 .scroll, .marquee-2 .scroll {
        animation: scroll 200s linear infinite;
    }

    @keyframes scroll {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
    }

    .marquee__content li {
        list-style: none;
        line-height: normal;
        text-transform: uppercase;
        font-size: 1.1rem;
        letter-spacing: 1px;
        white-space: nowrap;
    }

    .marquee__content li i {
        color: #f1008d;
    }

    @media screen and (max-width: 850px) {
        .marquee__content li {
            font-size: 0.8rem;
        }
    }
</style>

<section class="marquees-wrapper">
    <div class="marquee marquee-1">
        <ul class="marquee__content scroll">
            <?php if (!empty($flashNews)): ?>
                <?php foreach ($flashNews as $entry): ?>
                    <li><?= htmlspecialchars($entry['title']) ?></li>
                    <li><i class="fa-solid fa-person-biking"></i></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Žádné flash news nejsou k dispozici.</li>
            <?php endif; ?>
        </ul>
        <ul class="marquee__content scroll" aria-hidden="true">
            <?php if (!empty($flashNews)): ?>
                <?php foreach ($flashNews as $entry): ?>
                    <li><?= htmlspecialchars($entry['title']) ?></li>
                    <li><i class="fa-solid fa-person-biking"></i></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Žádné flash news nejsou k dispozici.</li>
            <?php endif; ?>
        </ul>
    </div>
</section>

