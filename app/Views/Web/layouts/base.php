<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    use App\Helpers\SEOHelper;
    
    // Načtení SEO konfigurace
    $seoConfig = SEOHelper::getConfig();
    
    // Generování SEO dat s AI optimalizací
    $seoKeywords = SEOHelper::generateKeywords($content ?? null, $keywords ?? []);
    $keywordsArray = !empty($seoKeywords) ? explode(', ', $seoKeywords) : [];
    $seoTitle = SEOHelper::generateTitle($title ?? null, null, $keywordsArray);
    $seoDescription = SEOHelper::generateDescription($content ?? null, $description ?? null, $keywordsArray);
    $seoCanonical = SEOHelper::generateCanonicalUrl($canonicalPath ?? '');
    $seoRobots = SEOHelper::generateRobotsMeta();
    
    // Open Graph data
    $ogData = SEOHelper::generateOpenGraph(
        $seoTitle,
        $seoDescription,
        $ogImage ?? null,
        $ogType ?? 'website',
        $seoCanonical
    );
    
    // Twitter Card data
    $twitterData = SEOHelper::generateTwitterCard($seoTitle, $seoDescription, $ogImage ?? null);
    
    // Hreflang data
    $hreflangData = SEOHelper::generateHreflangData($seoCanonical);
    ?>

    <!-- ✅ ZÁKLADNÍ SEO META TAGS -->
    <title><?= htmlspecialchars($seoTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($seoDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seoKeywords) ?>">
    <meta name="author" content="<?= htmlspecialchars($author ?? $seoConfig['site']['author']) ?>">
    <meta name="robots" content="<?= $seoRobots ?>">
    <meta name="language" content="<?= $seoConfig['site']['language'] ?>">
    <meta name="revisit-after" content="7 days">
    
    <!-- ✅ CANONICAL URL -->
    <link rel="canonical" href="<?= htmlspecialchars($seoCanonical) ?>">
    
    <!-- ✅ HREFLANG PRO MEZINÁRODNÍ VERZE -->
    <?php foreach ($hreflangData as $lang => $url): ?>
        <link rel="alternate" hreflang="<?= $lang ?>" href="<?= htmlspecialchars($url) ?>">
    <?php endforeach; ?>
    
    <!-- ✅ CSRF TOKEN -->
    <?php
    use App\Helpers\CSRFHelper;
    echo CSRFHelper::generateMetaTag();
    ?>
    
    <!-- ✅ OPEN GRAPH META TAGS -->
    <?php foreach ($ogData as $property => $content): ?>
        <meta property="<?= $property ?>" content="<?= htmlspecialchars($content) ?>">
    <?php endforeach; ?>
    
    <!-- ✅ TWITTER CARD META TAGS -->
    <?php foreach ($twitterData as $name => $content): ?>
        <meta name="<?= $name ?>" content="<?= htmlspecialchars($content) ?>">
    <?php endforeach; ?>
    
    <!-- ✅ PWA A MOBILNÍ OPTIMALIZACE -->
    <meta name="theme-color" content="<?= $seoConfig['site']['theme_color'] ?>">
    <meta name="msapplication-TileColor" content="<?= $seoConfig['site']['theme_color'] ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= $seoConfig['site']['name'] ?>">
    
    <!-- ✅ FAVICON A IKONY -->
    <link id="favicon" rel="icon" href="/assets/graphics/icon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/assets/graphics/logo_text_cyklistickey.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/graphics/logo_text_cyklistickey.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/graphics/icon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/graphics/icon.ico">
    
    <!-- ✅ MANIFEST -->
    <link rel="manifest" href="/manifest.json">

    <!-- ✅ PRECONNECT PRO RYCHLEJŠÍ NAČÍTÁNÍ -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.tiny.cloud">
    <link rel="preconnect" href="https://kit.fontawesome.com">
    
    <!-- ✅ CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    
    <!-- ✅ JAVASCRIPT - OPTIMALIZOVANÉ NAČÍTÁNÍ -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" defer></script>
    <script src="https://kit.fontawesome.com/d74f0b379c.js" crossorigin="anonymous" defer></script>
    <script src="https://cdn.tiny.cloud/1/4zya77m9f7cxct4wa90s8vckad17auk31vflx884mx6xu1a3/tinymce/7/tinymce.min.js" referrerpolicy="origin" defer></script>

    <!-- ✅ STRUCTURED DATA (JSON-LD) -->
    <?php if (isset($structuredData)): ?>
        <script type="application/ld+json">
            <?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
        </script>
    <?php endif; ?>
    
    <!-- ✅ ORGANIZATION STRUCTURED DATA -->
    <script type="application/ld+json">
        <?= json_encode($seoConfig['structured_data']['organization'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
    
    <!-- ✅ WEBSITE STRUCTURED DATA -->
    <script type="application/ld+json">
        <?= json_encode($seoConfig['structured_data']['website'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
    
    <!-- ✅ FAQ STRUCTURED DATA -->
    <script type="application/ld+json">
        <?= json_encode(SEOHelper::generateFAQSchema($seoConfig['faq']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
    
    <!-- ✅ BREADCRUMBS STRUCTURED DATA -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
        <script type="application/ld+json">
            <?= json_encode(SEOHelper::generateBreadcrumbSchema($breadcrumbs), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
        </script>
    <?php endif; ?>

    <script>
        let originalTitle = <?php echo json_encode($seoTitle ?? 'Cyklistický magazín'); ?>; // Bezpečně uloží původní titulek
        let newTitle = "\u{1F525} " + <?php echo json_encode($seoTitle ?? 'Cyklistický magazín'); ?>; // Nový titulek s emoji
        let isChangingTitle = false; // Stav, jestli se má titulek měnit
        let titleInterval; // Proměnná pro interval
        let favicon = document.querySelector('#favicon');

        // Funkce pro změnu titulku
        function changeTitle() {
            document.title = document.title === originalTitle ? newTitle : originalTitle;
        }

        function changeFavicon(src) {
            if (favicon) {
                favicon.href = src;
            }
        }

        // Když uživatel opustí stránku
        window.onblur = function() {
            changeFavicon('/assets/graphics/logo2.png'); // Změní ikonu
            changeTitle(); // Okamžitě změní titulek při prvním blur
            if (!isChangingTitle) {
                titleInterval = setInterval(changeTitle, 2000); // Mění titulek každé 2 vteřiny
                isChangingTitle = true;
            }
        };

        // Když se uživatel vrátí na stránku
        window.onfocus = function() {
            changeFavicon('/assets/graphics/icon.ico'); // Vrátí původní ikonu
            if (isChangingTitle) {
                clearInterval(titleInterval); // Zastaví změnu titulku
                document.title = originalTitle; // Obnoví původní titulek
                isChangingTitle = false;
            }
        };
    </script>

    <link rel="stylesheet" href="/css/navbar-web.css">
    <link rel="stylesheet" href="/css/footer.css">
    <link rel="stylesheet" href="/css/breadcrumbs.css">

    <?php if (isset($css) && is_array($css)): ?>
        <?php foreach ($css as $i): ?>
            <link rel="stylesheet" href="/css/<?php echo $i; ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($script) && is_array($script)): ?>
        <?php foreach ($script as $i): ?>
            <script src="/js/<?php echo $i; ?>.js"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<body>
    <?php
    function generateLinks($links)
    {
        $html = '';
        foreach ($links as $link) {
            $target = isset($link['target']) ? ' target="' . $link['target'] . '"' : '';
            $html .= '<li><a href="' . $link['url'] . '"' . $target . '>' . $link['text'] . '</a></li>';
        }
        return $html;
    }

    $links = generateLinks([
        ['url' => '/', 'text' => 'DOMŮ'],
        ['url' => 'https://www.cycli.cz/vyhledavani?controller=search&s=cyklistickey', 'text' => 'ESHOP', 'target' => '_blank'],
        ['url' => '/categories/', 'text' => 'KATEGORIE'],
        ['url' => '/authors/', 'text' => 'REDAKCE'],
        ['url' => '/events', 'text' => 'EVENTS'],
        ['url' => '/kontakt/', 'text' => 'O NÁS']
    ]);
    
    $footerLinks = generateLinks([
        ['url' => '/', 'text' => 'DOMŮ'],
        ['url' => 'https://www.cycli.cz/vyhledavani?controller=search&s=cyklistickey', 'text' => 'ESHOP', 'target' => '_blank'],
        ['url' => '/categories/', 'text' => 'KATEGORIE'],
        ['url' => '/authors/', 'text' => 'REDAKCE'],
        ['url' => '/events', 'text' => 'EVENTS'],
        ['url' => '/kontakt/', 'text' => 'O NÁS'],
        ['url' => '/appka', 'text' => 'APPKA']
    ]);
     
    include 'flash.php';

    include 'header.php';?>

    <main>
        <!-- ✅ BREADCRUMBS -->
        <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
            <div class="container">
                <?= SEOHelper::generateBreadcrumbsHTML($breadcrumbs) ?>
            </div>
        <?php endif; ?>
        
        <?php include $view; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>

</body>

</html>