<?php
use App\Helpers\FlashMessageHelper;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="flash-messages-container">
    <?= FlashMessageHelper::display('success', 'Byl vygenerován odkaz pro reset Vašeho hesla.') ?>
    <?= FlashMessageHelper::display('info', 'Odkaz je platný 1 hodinu.') ?>
</div>
<div class="ohraniceni new">
    <div class="logo">
        <img src="/assets/graphics/logo_text_cyklistickey.png" alt="Cyklistickey logo">
    </div>
    <div class="inputy">
        <div class="prvek">
            <span class="form-title">Odkaz pro reset hesla</span>
        </div>
        <div class="prvek">
            <div class="link-container" style="background-color: rgba(0, 0, 0, 0.3); border-radius: 10px; padding: 10px; margin: 10px 0; word-break: break-all; font-size: 0.8em; max-height: 80px; overflow-y: auto; width: 100%;">
                <?= htmlspecialchars($resetLink) ?>
            </div>
        </div>
        <div class="prvek">
            <button onclick="window.location.href='<?= $resetLink ?>';" style="width: 100%; margin-top: 10px;">Přejít na reset hesla</button>
        </div>
        <div class="prvek">
            <a href="/login">Zpět na přihlášení</a>
        </div>
    </div>
</div> 