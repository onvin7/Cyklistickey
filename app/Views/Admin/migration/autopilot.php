<?php
$autopilot = $state['autopilot'] ?? [];
$running = !empty($autopilot['running']);
$index = (int)($autopilot['index'] ?? 0);
$total = is_array($autopilot['queue'] ?? null) ? count($autopilot['queue']) : 0;
?>

<div class="d-flex align-items-center justify-content-between mt-4 mb-3">
    <div>
        <h1 class="h3 mb-1">Migrace: Autopilot</h1>
        <div class="text-muted">Postupně spouští kroky migrace a ukládá checkpoint do `web/cache/migration/state.json`.</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="/admin/migration">Zpět</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-semibold">Stav: <span id="ap-status" class="<?= $running ? 'text-success' : 'text-muted' ?>"><?= $running ? 'běží' : 'neběží' ?></span></div>
                <div class="text-muted small">Krok: <span id="ap-progress"><?= $index ?></span> / <span id="ap-total"><?= $total ?></span></div>
            </div>
            <div class="d-flex gap-2">
                <form method="post" action="/admin/migration/autopilot/stop">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <button class="btn btn-outline-danger">Stop</button>
                </form>
            </div>
        </div>

        <hr>

        <div class="mb-2 fw-semibold">Log</div>
        <pre id="ap-log" style="white-space: pre-wrap; min-height: 220px;"></pre>
    </div>
</div>

<script>
(() => {
    const running = <?= $running ? 'true' : 'false' ?>;
    const csrf = <?= json_encode($csrfToken) ?>;

    const statusEl = document.getElementById('ap-status');
    const progressEl = document.getElementById('ap-progress');
    const totalEl = document.getElementById('ap-total');
    const logEl = document.getElementById('ap-log');

    async function tick() {
        const formData = new FormData();
        formData.append('csrf_token', csrf);

        const res = await fetch('/admin/migration/autopilot/tick', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'fetch'
            }
        });

        const raw = await res.text().catch(() => '');
        let data = null;
        try {
            data = JSON.parse(raw);
        } catch (e) {
            statusEl.textContent = 'chyba';
            statusEl.className = 'text-danger';
            logEl.textContent = `Autopilot tick nevrátil JSON (HTTP ${res.status}).\n\n${raw}`.trim();
            return;
        }

        if (!res.ok) {
            statusEl.textContent = 'chyba';
            statusEl.className = 'text-danger';
            logEl.textContent = (data && data.error) ? data.error : `HTTP ${res.status}`;
            return;
        }

        if (!data.running) {
            statusEl.textContent = 'dokončeno / zastaveno';
            statusEl.className = 'text-muted';
            if (data.last && data.last.log) {
                logEl.textContent = data.last.log;
            }
            return;
        }

        statusEl.textContent = 'běží';
        statusEl.className = 'text-success';
        progressEl.textContent = data.index;
        totalEl.textContent = data.total;
        if (data.last && typeof data.last.log === 'string') {
            logEl.textContent = data.last.log;
        }

        setTimeout(tick, 600);
    }

    if (running) {
        tick();
    } else {
        logEl.textContent = 'Autopilot neběží. Spusť ho z /admin/migration.';
    }
})();
</script>
