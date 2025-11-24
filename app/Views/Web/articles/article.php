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
    <?php if (!empty($activeAds)): ?>
    const activeAds = <?= json_encode($activeAds) ?>;
    
    function createAdBanner(ad) {
        const bannerImg = document.createElement('div');
        bannerImg.style.width = "100%";
        bannerImg.style.height = "15vh";
        const imgPath = "/uploads/ads/" + ad.obrazek;
        bannerImg.style.backgroundImage = `url('${imgPath}')`;
        bannerImg.style.backgroundSize = 'contain';
        bannerImg.style.backgroundRepeat = "no-repeat";
        bannerImg.style.backgroundPosition = 'center';
        bannerImg.style.cursor = "pointer";
        bannerImg.style.marginTop = "5vh";
        bannerImg.style.marginBottom = "5vh";
        const sponsorHref = document.createElement('a');
        sponsorHref.href = ad.odkaz;
        sponsorHref.target = "_blank";
        sponsorHref.rel = "nofollow";
        sponsorHref.classList = "ad-banner";
        sponsorHref.appendChild(bannerImg);
        return sponsorHref;
    }

    function getRandomAd() {
        if (activeAds.length === 0) return null;
        if (activeAds.length === 1) return activeAds[0];
        
        // Vážený výběr podle frekvence (nižší frekvence = častěji)
        const weightedAds = [];
        activeAds.forEach(ad => {
            const weight = Math.max(1, Math.floor(10 / ad.frekvence));
            for (let i = 0; i < weight; i++) {
                weightedAds.push(ad);
            }
        });
        
        return weightedAds[Math.floor(Math.random() * weightedAds.length)];
    }

    function addBanners() {
        const textEditorDiv = document.querySelector('div.text-editor');
        if (textEditorDiv && activeAds.length > 0) {
            let secondParagraphIndex = findNthParagraph(textEditorDiv.children);
            
            // Reklama po 2. odstavci
            const ad1 = getRandomAd();
            if (ad1 && secondParagraphIndex !== null) {
                textEditorDiv.insertBefore(createAdBanner(ad1), textEditorDiv.children[secondParagraphIndex]);
            }
            
            // Reklama na konci
            const ad2 = getRandomAd();
            if (ad2) {
                textEditorDiv.appendChild(createAdBanner(ad2));
            }
        }
    }

    function findNthParagraph(children, n = 2) {
        let paragraphCount = 0;
        if (children[0] && children[0].textContent && children[0].textContent.toLowerCase()[0] != "<") {
            paragraphCount++;
        }
        for (let i = 0; i < children.length; i++) {
            if (["p", "div"].includes(children[i].tagName.toLowerCase())) {
                if (children[i].innerHTML != "" && !children[i].innerHTML.includes("<br>")) {
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
    <?php endif; ?>
</script>

<script>
    // Tracking času na stránce a scroll depth pro link clicks
    (function() {
        const pageLoadTime = Date.now();
        let maxScrollDepth = 0;
        let viewportWidth = window.innerWidth || document.documentElement.clientWidth;
        let viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        
        // Trackování scroll depth
        function updateScrollDepth() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const documentHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrollPercent = documentHeight > 0 ? Math.round((scrollTop / documentHeight) * 100) : 0;
            maxScrollDepth = Math.max(maxScrollDepth, scrollPercent);
        }
        
        // Trackování změny velikosti viewportu
        function updateViewport() {
            viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        }
        
        // Přidání tracking parametrů ke všem tracking odkazům
        function enhanceTrackingLinks() {
            const trackingLinks = document.querySelectorAll('a[href^="/track/"]');
            
            trackingLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    const timeOnPage = Math.round((Date.now() - pageLoadTime) / 1000); // v sekundách
                    const currentScrollDepth = maxScrollDepth;
                    
                    // Získáme původní href
                    const originalHref = link.getAttribute('href');
                    
                    // Přidáme tracking parametry
                    const separator = originalHref.includes('?') ? '&' : '?';
                    const enhancedHref = originalHref + separator + 
                        'time=' + encodeURIComponent(timeOnPage) + 
                        '&scroll=' + encodeURIComponent(currentScrollDepth) +
                        '&vw=' + encodeURIComponent(viewportWidth) +
                        '&vh=' + encodeURIComponent(viewportHeight);
                    
                    // Nastavíme nový href
                    link.setAttribute('href', enhancedHref);
                });
            });
        }
        
        // Inicializace
        window.addEventListener('scroll', updateScrollDepth, { passive: true });
        window.addEventListener('resize', updateViewport, { passive: true });
        
        // Po načtení DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', enhanceTrackingLinks);
        } else {
            enhanceTrackingLinks();
        }
        
        // Periodické aktualizace scroll depth (pro případ, že uživatel scrolluje rychle)
        setInterval(updateScrollDepth, 100);
    })();
</script>