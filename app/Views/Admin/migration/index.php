<?php
$last = $state ?? [];
$countsOld = $status['counts']['old'] ?? [];
$countsNew = $status['counts']['new'] ?? [];
$uploads = $status['uploads'] ?? [];
$overview = $status['overview'] ?? [];
$summary = $overview['summary'] ?? ['ok' => 0, 'pending' => 0, 'failed' => 0, 'total' => 1, 'progress_percent' => 0];
$checks = $overview['checks'] ?? [];
$autopilotOverview = $overview['autopilot'] ?? ['running' => false, 'index' => 0, 'total' => 0];
$articleCursor = $last['article_import_cursor'] ?? null;
$articleCursorInt = ($articleCursor === null || $articleCursor === '') ? 0 : (int)$articleCursor;
?>

<div class="d-flex align-items-center justify-content-between mt-4 mb-3">
    <div>
        <h1 class="h3 mb-1">Migrace</h1>
        <div class="text-muted">Stav dat, souborů a ovládání skriptů.</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/admin/migration/autopilot">Autopilot</a>
    </div>
</div>

<?php if (!empty($last['last_run_at'])): ?>
    <div class="alert <?= !empty($last['last_ok']) ? 'alert-success' : 'alert-danger' ?>">
        <div><strong>Poslední běh:</strong> <?= htmlspecialchars($last['last_task'] ?? 'neznámý') ?> (<?= htmlspecialchars($last['last_run_at']) ?>)</div>
        <?php if (!empty($last['last_error'])): ?>
            <div class="mt-1"><strong>Chyba:</strong> <?= htmlspecialchars($last['last_error']) ?></div>
        <?php endif; ?>
        <?php if (!empty($last['last_duration_ms'])): ?>
            <div class="mt-1 text-muted">Doba: <?= (int)$last['last_duration_ms'] ?> ms</div>
        <?php endif; ?>
        <?php if (!empty($last['last_output'])): ?>
            <details class="mt-2">
                <summary>Výstup</summary>
                <pre class="mt-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($last['last_output']); ?></pre>
            </details>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">Přehled migrace</h2>
                <div class="small text-muted mb-2">Kontrola, co je hotové, co zbývá a kde se data liší.</div>
                <div class="progress mb-2" role="progressbar" aria-label="Progress migrace" aria-valuenow="<?= (int)$summary['progress_percent'] ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: <?= (int)$summary['progress_percent'] ?>%"><?= (int)$summary['progress_percent'] ?>%</div>
                </div>
                <div class="small text-muted">
                    Hotovo: <strong><?= (int)$summary['ok'] ?></strong> /
                    Celkem kontrol: <strong><?= (int)$summary['total'] ?></strong> |
                    Zbývá: <strong><?= (int)$summary['pending'] ?></strong> |
                    Neshoda/Navíc: <strong><?= (int)$summary['failed'] ?></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="h5">Autopilot stav</h2>
                <?php
                $apRunning = !empty($autopilotOverview['running']);
                $apTextClass = $apRunning ? 'text-success' : 'text-muted';
                ?>
                <div class="mb-1">Stav: <strong class="<?= $apTextClass ?>"><?= $apRunning ? 'běží' : 'neběží' ?></strong></div>
                <div class="mb-1">Krok: <strong><?= (int)($autopilotOverview['index'] ?? 0) ?></strong> / <strong><?= (int)($autopilotOverview['total'] ?? 0) ?></strong></div>
                <?php if (!empty($autopilotOverview['created_at'])): ?>
                    <div class="small text-muted">Start: <?= htmlspecialchars((string)$autopilotOverview['created_at']) ?></div>
                <?php endif; ?>
                <?php if (!empty($autopilotOverview['finished_at'])): ?>
                    <div class="small text-muted">Dokončeno: <?= htmlspecialchars((string)$autopilotOverview['finished_at']) ?></div>
                <?php elseif (!empty($autopilotOverview['stopped_at'])): ?>
                    <div class="small text-muted">Zastaveno: <?= htmlspecialchars((string)$autopilotOverview['stopped_at']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h2 class="h5">Validace: je to stejné a správně?</h2>
        <div class="small text-muted mb-2">OK = sedí. Chybí/Nutné opravit = zbývá udělat. Navíc = v nové DB je víc záznamů než ve staré.</div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Kontrola</th>
                        <th>Stará</th>
                        <th>Nová</th>
                        <th>Rozdíl (nová-stará)</th>
                        <th>Stav</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checks as $check): ?>
                        <?php
                        $stateCode = (string)($check['state_code'] ?? 'na');
                        $badgeClass = 'bg-secondary';
                        if ($stateCode === 'ok') $badgeClass = 'bg-success';
                        elseif ($stateCode === 'pending') $badgeClass = 'bg-warning text-dark';
                        elseif ($stateCode === 'warn') $badgeClass = 'bg-danger';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string)($check['label'] ?? '')) ?></td>
                            <td><?= $check['old'] === null ? '<span class="text-muted">n/a</span>' : (int)$check['old'] ?></td>
                            <td><?= $check['new'] === null ? '<span class="text-muted">n/a</span>' : (int)$check['new'] ?></td>
                            <td>
                                <?php if ($check['delta'] === null): ?>
                                    <span class="text-muted">n/a</span>
                                <?php else: ?>
                                    <?= (int)$check['delta'] ?>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars((string)($check['state'] ?? 'n/a')) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Databáze</h2>
                <div class="small text-muted mb-2">Kontroly čtou počty záznamů ve staré a nové DB (pokud je nastavené `config/db_credentials.php`).</div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Tabulka</th>
                                <th>Stará</th>
                                <th>Nová</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rows = [
                                ['kategorie', 'kategorie'],
                                ['users', 'users'],
                                ['clanky', 'clanky'],
                                ['kategorie_clanku', 'clanky_kategorie'],
                                ['propagace', 'propagace'],
                                ['views_clanku', 'views_clanku'],
                                ['password_resets_valid', 'password_resets'],
                                ['clanky_bez_obsahu', 'clanky_bez_obsahu'],
                            ];
                            foreach ($rows as [$oldKey, $newKey]):
                                $oldVal = array_key_exists($oldKey, $countsOld) ? $countsOld[$oldKey] : null;
                                $newVal = array_key_exists($newKey, $countsNew) ? $countsNew[$newKey] : null;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($oldKey === 'password_resets_valid' ? 'password_resets (valid)' : ($newKey === 'clanky_bez_obsahu' ? 'clanky (bez obsahu)' : $oldKey)) ?></td>
                                    <td><?= $oldVal === null ? '<span class="text-muted">n/a</span>' : (int)$oldVal ?></td>
                                    <td><?= $newVal === null ? '<span class="text-muted">n/a</span>' : (int)$newVal ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h2 class="h5">Soubory v `web/uploads`</h2>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Složka</th>
                                <th>Počet souborů</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>articles</td><td><?= (int)($uploads['articles'] ?? 0) ?></td></tr>
                            <tr><td>audio</td><td><?= (int)($uploads['audio'] ?? 0) ?></td></tr>
                            <tr><td>thumbnails/velke</td><td><?= (int)($uploads['thumb_velke'] ?? 0) ?></td></tr>
                            <tr><td>thumbnails/male</td><td><?= (int)($uploads['thumb_male'] ?? 0) ?></td></tr>
                            <tr><td>users/thumbnails</td><td><?= (int)($uploads['users'] ?? 0) ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <div class="fw-semibold mb-1">Co typicky zkopírovat ručně (FTP)</div>
                    <ul class="mb-0">
                        <li>Fotky v obsahu článků → `web/uploads/articles/`</li>
                        <li>Náhledy (velké) → `web/uploads/thumbnails/velke/`</li>
                        <li>Náhledy (malé) → `web/uploads/thumbnails/male/`</li>
                        <li>Audio → `web/uploads/audio/`</li>
                        <li>Profilové fotky → `web/uploads/users/thumbnails/` (případně přes skript)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h2 class="h5">Spouštění skriptů</h2>
        <div class="small text-muted mb-3">Běží jen v administraci a vyžaduje CSRF. Migrační skripty jsou zároveň blokované proti veřejnému spuštění bez tokenu.</div>

        <div class="alert alert-light border mb-3">
            <div class="fw-semibold mb-1">Články (od nejnovějšího dolů)</div>
            <div class="small text-muted mb-2">
                Kurzor pro další batch (uložený po běhu kroku 3):
                <?php if ($articleCursor === null && !empty($last['article_import_finished_at'])): ?>
                    <strong class="text-success">dokončeno</strong> (<?= htmlspecialchars((string)$last['article_import_finished_at']) ?>)
                <?php elseif ($articleCursor === null): ?>
                    <strong>0</strong> (začít od nejvyššího ID ve staré DB)
                <?php else: ?>
                    <strong><?= (int)$articleCursorInt ?></strong> (další batch pokračuje pod tímto ID)
                <?php endif; ?>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <form method="post" action="/admin/migration/run" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="task" value="migrate_db">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="direction" value="desc">
                    <input type="hidden" name="limit" value="5">
                    <input type="hidden" name="cursor_id" value="0">
                    <input type="hidden" name="reset" value="0">
                    <button type="submit" class="btn btn-warning">Test: posledních 5 článků</button>
                </form>
                <form method="post" action="/admin/migration/run" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="task" value="migrate_db">
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="direction" value="desc">
                    <input type="hidden" name="limit" value="10">
                    <input type="hidden" name="cursor_id" value="<?= (int)$articleCursorInt ?>">
                    <input type="hidden" name="reset" value="0">
                    <button type="submit" class="btn btn-primary">Dalších 10 článků</button>
                </form>
                <form method="post" action="/admin/migration/run" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="task" value="check_missing_images">
                    <button type="submit" class="btn btn-outline-secondary">Zkontrolovat chybějící obrázky</button>
                </form>
                <form method="post" action="/admin/migration/run" class="d-inline"
                      onsubmit="return confirm('Opravdu smazat všechny články? Vyprázdní se tabulky clanky_kategorie a clanky (FOREIGN_KEY_CHECKS vypnuto jen na tento krok).');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="task" value="truncate_articles">
                    <button type="submit" class="btn btn-outline-danger">Smazat všechny články (TRUNCATE)</button>
                </form>
            </div>
            <div class="small text-muted mt-2 mb-0">
                „Dalších 10“ použije kurzor z posledního běhu kroku 3. Úplný cyklus (kategorie → uživatelé → články po 10 od nejnovějšího včetně resetu článků → kroky 4–10) spusť níže jako Autopilot (batch článků).
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="1">
                <button class="btn btn-outline-primary">Data: krok 1</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="2">
                <button class="btn btn-outline-primary">Data: krok 2</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="3">
                <button class="btn btn-outline-primary">Data: krok 3</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="4">
                <button class="btn btn-outline-primary">Data: krok 4</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="5">
                <button class="btn btn-outline-primary">Data: krok 5</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="6">
                <button class="btn btn-outline-primary">Data: krok 6</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="7">
                <button class="btn btn-outline-primary">Data: krok 7</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="8">
                <button class="btn btn-outline-primary">Data: krok 8</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="9">
                <button class="btn btn-outline-primary">Data: krok 9</button>
            </form>
            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_db">
                <input type="hidden" name="step" value="10">
                <button class="btn btn-outline-primary">Data: krok 10</button>
            </form>

            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_images">
                <input type="hidden" name="type" value="all">
                <button class="btn btn-outline-success">Zpracovat fotky (resize)</button>
            </form>

            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_users_complete">
                <button class="btn btn-outline-success">Uživatelé + profily (komplet)</button>
            </form>

            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="migrate_audio_table_to_clanky_audio">
                <button class="btn btn-outline-info">Audio (stará tabulka audio → clanky.audio)</button>
            </form>

            <form method="post" action="/admin/migration/run" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="task" value="sync_assets">
                <input type="hidden" name="mode" value="all">
                <input type="hidden" name="batch" value="25">
                <button class="btn btn-outline-warning">Stáhnout chybějící fotky (batch)</button>
            </form>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
            <form method="post" action="/admin/migration/autopilot/start" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="btn btn-primary">Autopilot: kroky 1–10 (jednou)</button>
            </form>
            <form method="post" action="/admin/migration/autopilot/articles-batch-start" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="btn btn-success">Autopilot: články po 10 od nejnovějšího (1–2 → 3×batch → 4–10)</button>
            </form>
            <form method="post" action="/admin/migration/autopilot/stop" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button class="btn btn-outline-danger">Zastavit Autopilot</button>
            </form>
        </div>
    </div>
</div>

