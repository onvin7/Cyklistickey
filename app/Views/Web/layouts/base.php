<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ✅ Výchozí SEO hodnoty (přepíší se, pokud kontroler nastaví jiné) -->
    <?php
    $defaultTitle = "Cyklistický magazín – Novinky, závody a technika";
    $defaultDescription = "Sledujte nejnovější zprávy, tréninkové tipy, technické novinky a rozhovory ze světa cyklistiky.";
    $defaultOgImage = "https://vincenon21.mp.spse-net.cz/assets/graphics/logo_text_cyklistickey.png";
    $defaultOgUrl = "https://vincenon21.mp.spse-net.cz";
    ?>

    <!-- ✅ Dynamické SEO (pokud není nastavena proměnná, použije se výchozí hodnota) -->
    <title><?= htmlspecialchars($title ?? $defaultTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($description ?? $defaultDescription) ?>">
    <meta name="robots" content="noindex, nofollow">

    <!-- Open Graph pro sociální sítě -->
    <meta property="og:title" content="<?= htmlspecialchars($ogTitle ?? $title ?? $defaultTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($ogDescription ?? $description ?? $defaultDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($ogUrl ?? $defaultOgUrl) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage ?? $defaultOgImage) ?>">

    <!-- ✅ Canonical URL -->
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl ?? $defaultOgUrl) ?>">

    <!-- Favicon -->
    <link id="favicon" rel="icon" href="/assets/graphics/icon.ico" type="image/x-icon">

    <script src="https://kit.fontawesome.com/d74f0b379c.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined" rel="stylesheet">
    
    <script src="https://cdn.tiny.cloud/1/4zya77m9f7cxct4wa90s8vckad17auk31vflx884mx6xu1a3/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/9cc50b8ba6.js" crossorigin="anonymous"></script>

    <!-- ✅ Structured Data (JSON-LD) -->
    <?php if (isset($structuredData)): ?>
        <script type="application/ld+json">
            <?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
        </script>
    <?php endif; ?>

    <script>
        let originalTitle = <?php echo json_encode($title ?? $defaultTitle); ?>; // Bezpečně uloží původní titulek
        let newTitle = "\u{1F525} " + <?php echo json_encode($title ?? $defaultTitle); ?>; // Nový titulek s emoji
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

    <?php if (isset($css) && is_array($css)): ?>
        <?php foreach ($css as $i): ?>
            <link rel="stylesheet" href="/css/<?php echo $i; ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (isset($script) && is_array($script)): ?>
        <?php foreach ($script as $i): ?>
            <link rel="stylesheet" href="/js/<?php echo $i; ?>.js">
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
        <?php include $view; ?>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>

</body>

</html>