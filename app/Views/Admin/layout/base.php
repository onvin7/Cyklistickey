<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/6085fdf718.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php if (!isset($disableNavbar) || !$disableNavbar): ?>
        <?php include 'navbar.php'; ?>
    <?php endif; ?>
    <div class="container mt-4">
        <?php include $view; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>