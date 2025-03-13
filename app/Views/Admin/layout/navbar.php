<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);

// Pokud uživatel má fotku, použije se. Jinak se zobrazí Font Awesome ikona.
$hasProfilePhoto = isset($_SESSION['profil_foto']) && !empty($_SESSION['profil_foto']);
$profilePhoto = $hasProfilePhoto ? "/uploads/users/thumbnails/" . $_SESSION['profil_foto'] : null;

// Výchozí pohlaví (pokud není nastaveno)
$gender = $_SESSION['gender'] ?? 'male';

// Emoji a popisy podle role a pohlaví
$roleData = [
    1 => ['male' => ["🧑‍💼", "Redaktor"], 'female' => ["👩‍💼", "Redaktorka"]],
    2 => ['male' => ["👨‍🏫", "Vydavatel"], 'female' => ["👩‍🏫", "Vydavatelka"]],
    3 => ['male' => ["👨‍💻", "Admin"], 'female' => ["👩‍💻", "Admin"]]
];

// Výběr emoji a popisu role
if (isset($roleData[$_SESSION['role']])) {
    $userEmoji = $roleData[$_SESSION['role']][$gender][0];
    $userRoleText = $roleData[$_SESSION['role']][$gender][1];
} else {
    $userEmoji = "👤";
    $userRoleText = "Neznámá role";
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item"><a class="nav-link" href="/admin/articles">Články</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/categories">Kategorie</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/statistics">Statistiky</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/promotions">Propagace</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/users">Uživatelé</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/access-control">Správa přístupů</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn && isset($_SESSION['email'])): ?>
                    <!-- Dropdown menu s profilovou fotkou nebo ikonou -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if ($hasProfilePhoto): ?>
                                <img src="<?= htmlspecialchars($profilePhoto) ?>" class="rounded-circle" width="40" height="40" alt="Profilová fotka">
                            <?php else: ?>
                                <span style="font-size: 30px;"><?= $userEmoji ?></span>
                            <?php endif; ?>
                            <span class="ms-2"><?= htmlspecialchars($_SESSION['email']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="/admin/settings">⚙ Nastavení účtu</a></li>
                            <li><a class="dropdown-item text-danger" href="/admin/logout">🚪 Odhlásit se</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>