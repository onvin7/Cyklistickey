<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <?php if (!isset($disableBootstrap) || !$disableBootstrap): ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6085fdf718.js" crossorigin="anonymous"></script>

    <link href="/css/admin-dashboard.css" rel="stylesheet">

    <?php if (isset($css) && is_array($css)): ?>
        <?php foreach ($css as $i): ?>
            <link rel="stylesheet" href="/css/<?php echo $i; ?>.css?v=<?php echo time(); ?>">
        <?php endforeach; ?>
    <?php endif; ?>


</head>

<body>
    <?php if (!isset($disableNavbar) || !$disableNavbar): ?>
        <?php include 'navbar.php'; ?>
    <?php endif; ?>
    <div class="container">
        <?php include $view; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>