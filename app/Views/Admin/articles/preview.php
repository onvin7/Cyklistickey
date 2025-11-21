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
                        <span class='kategorie-link'>
                            <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                        </span>
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
                    <h6><span><?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?></span>
                    </h6>
                </div>
                <p><?php echo isset($author['popis']) && is_string($author['popis']) ? TextHelper::truncate($author['popis'], 220) : ''; ?></p>
            </div>
        </div>
        <div class="odkaz">
            <span class="odkaz-link">
                <p>Zobrazit profil</p><i class="fa-solid fa-angle-right"></i>
            </span>
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
                        <h6><span><?php echo isset($author["email"]) ? htmlspecialchars($author["email"], ENT_QUOTES) : ''; ?></span>
                        </h6>
                    </div>

                </div>
            </div>
        </div>
        <p><?php echo isset($author['popis']) && is_string($author['popis']) ? TextHelper::truncate($author['popis'], 220) : ''; ?></p>
        <div class="odkaz">
            <span class="odkaz-link">
                <p>Zobrazit profil</p><i class="fa-solid fa-angle-right"></i>
            </span>
        </div>
    </div>
</div>

<style>
    /* Styly pro kategorie - stejné jako na veřejném webu */
    .categories .output .kategorie .kategorie-link {
        display: block;
        margin-right: 5px;
        color: white;
        text-decoration: none;
        pointer-events: none;
        cursor: default;
    }
    
    .categories .output .kategorie .kategorie-link p {
        display: block;
        width: max-content;
        padding: 0.5vh 1vh;
        border-radius: 25px;
        background-color: #f1008d;
        font-size: 12px;
        text-transform: uppercase;
        border: 3px solid transparent;
        transition: 0.2s;
        margin: 0;
        color: white;
    }
    
    .categories .output .kategorie .kategorie-link p:hover {
        border: 3px solid #f1008d;
        background-color: white;
        transition: 0.2s;
        color: #f1008d;
    }
    
    /* Styly pro nefunkční odkazy v autorovi - zachovat design */
    .odkaz-link {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: inherit;
        cursor: default;
        pointer-events: none;
    }
    
    .odkaz-link:hover {
        opacity: 0.8;
    }
</style>

