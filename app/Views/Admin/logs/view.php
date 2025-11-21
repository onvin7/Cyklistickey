<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-file-lines"></i> <?= htmlspecialchars($fileInfo['name']) ?></h2>
        <a href="/admin/logs" class="btn btn-action">
            <i class="fa-solid fa-arrow-left"></i> Zpět
        </a>
    </div>

    <div style="max-height: 85vh; overflow-y: auto; padding: 15px; background: #1e1e1e; color: #d4d4d4; font-family: 'Courier New', monospace; font-size: 12px;">
        <?php if (empty($lines)): ?>
            <div>Log soubor je prázdný.</div>
        <?php else: ?>
            <?php foreach ($lines as $line): ?>
                <div style="white-space: pre-wrap; word-break: break-all; padding: 2px 0; border-bottom: 1px solid #2d2d2d;">
                    <?= htmlspecialchars($line) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

