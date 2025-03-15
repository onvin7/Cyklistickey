<div class="nadpis">
    <h1><?php echo $category['nazev_kategorie']; ?></h1>
    <h2><?php echo $category['nazev_kategorie']; ?></h2>
</div>

<div class="container-clanky">
    <?php foreach ($articles as $result) : ?>
        <a href="/article/<?php echo htmlspecialchars($result['url']); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($result["nahled_foto"]) ? htmlspecialchars($result["nahled_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($result["nazev"]); ?>">
                <div class="card-body">
                    <h5><?php echo htmlspecialchars($result["nazev"]); ?></h5>
                    
                    <span class="datum">
                        <?php 
                            // Zobrazení data publikace, pokud je k dispozici
                            if (!empty($result["datum"])) {
                                echo date("d. m. Y", strtotime($result["datum"]));
                            } else {
                                echo date("d. m. Y"); // Aktuální datum, pokud není datum článku k dispozici
                            }
                        ?>
                    </span>
                    
                    <div class="kategorie">
                        <?php if (!empty($result['kategorie'])): ?>
                            <?php foreach ($result['kategorie'] as $kategorie): ?>
                                <span class="tag-kategorie" data-url="/category/<?php echo htmlspecialchars($kategorie['url']); ?>/">
                                    <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="clanek-excerpt">
                        <?php 
                            // Zobrazení obsahu článku
                            if (!empty($result["text"])) {
                                // Odstranění HTML tagů a zkrácení textu
                                $text = strip_tags($result["text"]);
                                $excerpt = mb_substr($text, 0, 150);
                                if (mb_strlen($text) > 150) {
                                    $excerpt .= '...';
                                }
                                echo htmlspecialchars($excerpt);
                            } else if (!empty($result["perex"])) {
                                echo htmlspecialchars($result["perex"]);
                            } else {
                                echo ""; // Prázdný řetězec místo výchozího textu
                            }
                        ?>
                    </div>
                    
                    <span class="read-more">Číst článek</span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Přidání event listeneru na všechny kategorie tagy
    const kategorieTags = document.querySelectorAll('.tag-kategorie');
    
    kategorieTags.forEach(tag => {
        tag.addEventListener('click', function(e) {
            // Zastavení propagace události, aby se neaktivoval nadřazený odkaz
            e.stopPropagation();
            e.preventDefault();
            
            // Získání URL z data atributu
            const url = this.getAttribute('data-url');
            
            // Přesměrování na URL kategorie
            if (url) {
                window.location.href = url;
            }
        });
    });
});
</script>