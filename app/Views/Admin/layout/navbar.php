<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);

$hasProfilePhoto = isset($_SESSION['profil_foto']) && !empty($_SESSION['profil_foto']);
$profilePhoto = $hasProfilePhoto ? "/uploads/users/thumbnails/" . $_SESSION['profil_foto'] : null;

$roleData = [
    1 =>  ["<i class=\"fa-solid fa-user-tie\"></i>", "Moderátor"],
    2 =>  ["<i class=\"fa-solid fa-user-pen\"></i>", "Editor"],
    3 =>  ["<i class=\"fa-solid fa-crown\"></i>", "Administrátor"]
];

if (isset($roleData[$_SESSION['role']])) {
    $userEmoji = $roleData[$_SESSION['role']][0];
    $userRoleText = $roleData[$_SESSION['role']][1];
} else {
    $userEmoji = "👤";
    $userRoleText = "Neznámá role";
}

// Určení aktivní stránky
$currentUri = $_SERVER['REQUEST_URI'];
$activeLinks = [
    'home' => $currentUri === '/admin' || $currentUri === '/admin/',
    'articles' => strpos($currentUri, '/admin/articles') !== false,
    'categories' => strpos($currentUri, '/admin/categories') !== false,
    'statistics' => strpos($currentUri, '/admin/statistics') !== false,
    'promotions' => strpos($currentUri, '/admin/promotions') !== false,
    'flashnews' => strpos($currentUri, '/admin/flashnews') !== false,
    'users' => strpos($currentUri, '/admin/users') !== false,
    'access-control' => strpos($currentUri, '/admin/access-control') !== false,
];

?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['home'] ? 'active' : '' ?>" href="/admin">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['articles'] ? 'active' : '' ?>" href="/admin/articles">Články</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['categories'] ? 'active' : '' ?>" href="/admin/categories">Kategorie</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['statistics'] ? 'active' : '' ?>" href="/admin/statistics">Statistiky</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['promotions'] ? 'active' : '' ?>" href="/admin/promotions">Propagace</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['flashnews'] ? 'active' : '' ?>" href="/admin/flashnews">Flash News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['users'] ? 'active' : '' ?>" href="/admin/users">Uživatelé</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeLinks['access-control'] ? 'active' : '' ?>" href="/admin/access-control">Správa přístupů</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn && isset($_SESSION['email'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-profile-nav" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($hasProfilePhoto): ?>
                                <div class="avatar-container">
                                    <img src="<?= htmlspecialchars($profilePhoto) ?>" alt="Profilová fotka">
                                </div>
                            <?php else: ?>
                                <div class="user-emoji"><?= $userEmoji ?></div>
                            <?php endif; ?>
                            <span><?= htmlspecialchars($_SESSION['email']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><span class="dropdown-item-text text-muted small px-3 py-2"><?= $userRoleText ?></span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/admin/settings">⚙ Nastavení účtu</a></li>
                            <li><a class="dropdown-item text-danger" href="/admin/logout">🚪 Odhlásit se</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>