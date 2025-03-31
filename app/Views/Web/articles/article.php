<?php

use App\Helpers\TextHelper;

?>

<div class="container-zobrazit">

    <div id="lightbox" onclick="this.style.display='none'">
        <img loading="lazy" src="#" id="lightbox-img" alt="Fullscreen image">
    </div>

    <div class="foto-header">
        <img loading="lazy" class='parallax-image' width='100%' src='/uploads/thumbnails/velke/<?php echo !empty($article['nahled_foto']) ? $article['nahled_foto'] : 'noimage.png'; ?>' alt='<?php echo htmlspecialchars($article["nazev"], ENT_QUOTES); ?>'>
    </div>

    <div class="text">
        <div class="categories">
            <div class="output">
                <?php
                if (isset($article['kategorie']) && is_array($article['kategorie'])) {
                    foreach ($article['kategorie'] as $kategorie) {
                ?>
                    <div class='kategorie'>
                        <a id='kategorie' href='/category/<?php echo $kategorie['url']; ?>/'>
                            <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                        </a>
                    </div>
                <?php 
                    }
                } 
                ?>
            </div>
        </div>

        <h1><?php echo htmlspecialchars($article["nazev"], ENT_QUOTES); ?></h1>
        <h3><?php echo \App\Helpers\TimeHelper::getRelativeTime($article["datum"], true); ?></h3>

        <?php if (isset($audioUrl) && $audioUrl): ?>
            <div class="prehravac">
                <audio controls>
                    <source src='<?php echo $audioUrl; ?>' type='audio/mpeg'>
                    Váš prohlížeč nepodporuje prvek audio.
                </audio>
            </div>
                    
        <?php endif; ?>

        <div class="text-editor">
            <?php
            if (isset($article['obsah'])) {
                echo $article['obsah'];
            } else {
                include $emptyArticlePath;
            }
            ?>
        </div>
        <script>
            // Oprava relativních cest k obrázkům
            document.addEventListener('DOMContentLoaded', function() {
                const contentImages = document.querySelectorAll('.text-editor img');
                
                contentImages.forEach(function(img) {
                    const src = img.getAttribute('src');
                    
                    // Hledání /uploads/articles/ v cestě
                    if (src && src.includes('/uploads/articles/')) {
                        // Získáme část cesty začínající /uploads/articles/
                        const newSrc = '/uploads/articles/' + src.split('/uploads/articles/')[1];
                        img.setAttribute('src', newSrc);
                    }
                });
            });
        </script>
    </div>
</div>
<script>
    $(document).ready(function() {
        var parallax = -0.3;
        var $parallaxImages = $(".parallax-image");
        var original_offsets = $parallaxImages.map(function() {
            return $(this).offset().top;
        });

        function updateParallax() {
            if ($(window).width() > 930) {
                var dy = $(window).scrollTop();
                $parallaxImages.each(function(i, el) {
                    var original_offset = original_offsets[i];
                    $(el).css("top", (original_offset + dy * parallax) + "px");
                });
            } else {
                $parallaxImages.each(function(i, el) {
                    $(el).css("top", original_offsets[i] + "px");
                });
            }
        }

        $(window).scroll(updateParallax);
        $(window).resize(updateParallax).trigger('resize');
    });

    $(document).ready(function() {
        $('.text').on('click', 'img', function() {
            var src = $(this).attr('src');
            $('#lightbox-img').attr('src', src);
            $('#lightbox').show();
        });

        $('#lightbox-img').on('click', function(e) {
            $('#lightbox').hide();
            e.stopPropagation();
        });
    });
</script>


<div class="container-autor">
    <div class="text">
        <div class="image">
            <img loading="lazy" src="/uploads/users/thumbnails/<?php echo !empty($author['profil_foto']) ? $author['profil_foto'] : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($author['name'] . ' ' . $author['surname']); ?>">
        </div>
        <div class="container-popis">
            <div class="popis">
                <div>
                    <h5><?php echo isset($author["name"]) ? htmlspecialchars($author["name"], ENT_QUOTES) : ''; ?>&nbsp;<?php echo isset($author["surname"]) ? htmlspecialchars($author["surname"], ENT_QUOTES) : ''; ?>
                    </h5>
                    <h6><a href="mailto:<?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?>"><?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?></a>
                    </h6>
                </div>
                <p><?php echo isset($author['popis']) && is_string($author['popis']) ? TextHelper::truncate($author['popis'], 220) : ''; ?></p>
            </div>
        </div>
        <div class="odkaz">
            <a href="/user/<?php echo (isset($author['name']) ? TextHelper::generateFriendlyUrl($author['name']) : '') . "-" . (isset($author['surname']) ? TextHelper::generateFriendlyUrl($author['surname']) : ''); ?>/">
                <p>Zobrazit profil</p><i class="fa-solid fa-angle-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="container-autor-mobile">
    <div class="text">
        <div class="fotka-text">
            <div>
                <div class="img" style="background-image: url('/uploads/users/thumbnails/<?php echo isset($author["profil_foto"]) && !empty($author["profil_foto"]) ? $author["profil_foto"] : 'noimage.png'; ?>')">
                </div>
            </div>
            <div class="container-popis">
                <div class="popis">
                    <div>
                        <h5><?php echo isset($author["name"]) ? htmlspecialchars($author["name"], ENT_QUOTES) : ''; ?><br><?php echo isset($author["surname"]) ? htmlspecialchars($author["surname"], ENT_QUOTES) : ''; ?>
                        </h5>
                        <h6><a href="mailto:<?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?>"><?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?></a>
                        </h6>
                    </div>

                </div>
            </div>
        </div>
        <p><?php echo isset($author['popis']) && is_string($author['popis']) ? TextHelper::truncate($author['popis'], 220) : ''; ?></p>
        <div class="odkaz">
            <a href="/user/<?php echo (isset($author['name']) ? TextHelper::generateFriendlyUrl($author['name']) : '') . "-" . (isset($author['surname']) ? TextHelper::generateFriendlyUrl($author['surname']) : ''); ?>/">
                <p>Zobrazit profil</p><i class="fa-solid fa-angle-right"></i>
            </a>
        </div>
    </div>
</div>



<div class="container-clanky">
    <?php if (is_array($relatedArticles) && !empty($relatedArticles)): ?>
        <?php foreach ($relatedArticles as $result): ?>
            <a href="/article/<?php echo ($result['url']); ?>/">
                <div class="card">
                    <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($result["nahled_foto"]) ? htmlspecialchars($result["nahled_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($result["nazev"]); ?>">
                    <div class="card-body">
                        <div class="kategorie">
                            <?php if (isset($result['kategorie']) && is_array($result['kategorie'])): ?>
                                <?php foreach ($result['kategorie'] as $kategorie): ?>
                                    <a href="/category/<?php echo htmlspecialchars($kategorie["url"]); ?>/">
                                        <p><?php echo htmlspecialchars($kategorie["nazev_kategorie"]); ?></p>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <a href="/articles/<?php echo $result['url']; ?>/">
                            <h5><?php echo htmlspecialchars($result["nazev"]); ?></h5>
                        </a>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    const bannerFiles = [
        'banner1.jpg',
        'banner2.jpg',
        'banner3.jpg',
        'banner4.jpg',
        'banner5.jpg',
        'banner6.jpg',
        'banner7.jpg'
    ];

    function createRandomBanner() {
        const randomBannerName = bannerFiles[Math.floor(Math.random() * bannerFiles.length)];
        const bannerImg = document.createElement('div');
        bannerImg.style.width = "100%";
        bannerImg.style.height = "15vh";
        const imgPath = "https://cyklistickey.cz/assets/img/banner/" + randomBannerName;
        bannerImg.style.backgroundImage = `url('${imgPath}')`;
        bannerImg.style.backgroundSize = 'contain';
        bannerImg.style.backgroundRepeat = "no-repeat";
        bannerImg.style.backgroundPosition = 'center';
        bannerImg.style.cursor = "pointer";
        bannerImg.style.marginTop = "5vh";
        bannerImg.style.marginBottom = "5vh";
        const sponsorHref = document.createElement('a');
        sponsorHref.href = "https://www.cycli.cz/";
        sponsorHref.target = "_blank";
        sponsorHref.classList = "ad-banner"
        sponsorHref.appendChild(bannerImg);
        return sponsorHref;
    }

    function addBanners() {
        const textEditorDiv = document.querySelector('div.text-editor');
        if (textEditorDiv) {
            let secondParagraphIndex = findNthParagraph(textEditorDiv.children);
            textEditorDiv.insertBefore(createRandomBanner(), textEditorDiv.children[secondParagraphIndex]);
            textEditorDiv.append(createRandomBanner())
        }
    }

    function findNthParagraph(children, n = 2) {
        let paragraphCount = 0;
        if (children[0].textContent.toLowerCase()[0] != "<") {
            paragraphCount++;
        }
        for (let i = 0; i < children.length; i++) {
            if (["p", "div"].includes(children[i].tagName.toLowerCase())) {
                if (children[i].innerHtml != "" || !children[i].innerHtml.contains("<br>")) {
                    paragraphCount++;
                }
                if (paragraphCount >= n && i > 0 && !children[i - 1].tagName.toLowerCase().includes("h")) {
                    return i;
                }
            }
        }
        return null;
    }
    addBanners();
</script>