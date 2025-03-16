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
                    <div class="card-content-left">
                        <h5><?php echo htmlspecialchars($result["nazev"]); ?></h5>
                        
                        <span class="datum">
                            <?php 
                                // Použití TimeHelper pro získání relativního času
                                echo \App\Helpers\TimeHelper::getRelativeTime($result["datum"]);
                            ?>
                        </span>
                        
                        <div class="clanek-excerpt">
                            <?php 
                                // Zkrácený výpis textu článku - pokud existuje
                                if (!empty($result["obsah"])) {
                                    echo substr(strip_tags($result["obsah"]), 0, 400) . "...";
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div class="card-content-right">
                        <div class="kategorie">
                            <?php if (!empty($result['kategorie'])): ?>
                                <?php foreach ($result['kategorie'] as $kategorie): ?>
                                    <span class="tag-kategorie" data-url="/category/<?php echo htmlspecialchars($kategorie['url']); ?>/">
                                        <p><?php echo htmlspecialchars($kategorie['nazev_kategorie']); ?></p>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <span class="read-more">Číst článek</span>
                    </div>
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