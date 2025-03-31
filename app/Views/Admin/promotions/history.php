<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-bullhorn"></i> Správa propagací</h2>
        <a href="/admin/promotions/create" class="btn btn-action">
            <i class="fa-solid fa-plus"></i> Vytvořit propagaci
        </a>
    </div>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <ul class="nav nav-pills promotion-tabs">
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions">
                <i class="fa-solid fa-play me-1"></i> Aktuální propagace
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/promotions/upcoming">
                <i class="fa-solid fa-clock me-1"></i> Budoucí propagace
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/admin/promotions/history">
                <i class="fa-solid fa-history me-1"></i> Historie propagací
            </a>
        </li>
    </ul>

    <?php if (empty($promotions)): ?>
        <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i> Zatím nejsou k dispozici žádné historické propagace.
        </div>
    <?php else: ?>
        <div class="card mb-4">
            <div class="card-body">
                <p class="text-muted mb-0">
                    <i class="fa-solid fa-info-circle me-2"></i> Historické propagace nelze mazat, slouží pro statistické účely.
                </p>
            </div>
        </div>

        <div class="row">
            <?php foreach ($promotions as $promotion): 
                $start = new DateTime($promotion['zacatek']);
                $end = new DateTime($promotion['konec']);
                $now = new DateTime();
                
                // Výpočet doby, před kterou propagace skončila (v dnech)
                $daysSinceEnd = ($now->getTimestamp() - $end->getTimestamp()) / (24 * 3600);
            ?>
                <div class="col-md-6">
                    <div class="promotion-card">
                        <div class="promotion-card-header">
                            <span><?= htmlspecialchars($promotion['nazev']) ?></span>
                            <span class="promotion-status-badge promotion-ended">
                                <i class="fa-solid fa-check-circle me-1"></i> Dokončeno
                            </span>
                        </div>
                        <div class="promotion-card-body">
                            <div class="promotion-dates">
                                <div class="promotion-date">
                                    <span>Začátek:</span>
                                    <span><?= $start->format('d.m.Y H:i') ?></span>
                                </div>
                                <div class="promotion-date">
                                    <span>Konec:</span>
                                    <span><?= $end->format('d.m.Y H:i') ?></span>
                                </div>
                            </div>
                            
                            <div class="small text-muted mt-3">
                                <i class="fa-solid fa-calendar-check me-1"></i>
                                <?php 
                                    if ($daysSinceEnd < 1) {
                                        echo 'Skončilo dnes';
                                    } else if ($daysSinceEnd < 2) {
                                        echo 'Skončilo včera';
                                    } else {
                                        echo 'Skončilo před ' . floor($daysSinceEnd) . ' dny';
                                    }
                                ?>
                            </div>
                            
                            <div class="mt-3">
                                <a href="/article/<?= $promotion['url'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-external-link me-1"></i> Zobrazit článek
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>