<div class="ohraniceni new">
    <div class="logo">
        <img src="/assets/graphics/logo_text_cyklistickey.png" alt="Cyklistickey logo">
    </div>
    <div class="inputy">
        <div class="prvek">
            <span class="form-title">Odkaz pro reset hesla</span>
        </div>
        <div class="prvek">
            <p>Byl vygenerován odkaz pro reset Vašeho hesla:</p>
        </div>
        <div class="prvek">
            <div class="link-container" style="background-color: rgba(0, 0, 0, 0.3); border-radius: 10px; padding: 10px; margin: 10px 0; word-break: break-all; font-size: 0.8em; max-height: 80px; overflow-y: auto; width: 100%;">
                <?= $resetLink ?>
            </div>
        </div>
        <div class="prvek">
            <p>Odkaz je platný 1 hodinu</p>
        </div>
        <div class="prvek">
            <button onclick="window.location.href='<?= $resetLink ?>';" style="width: 100%; margin-top: 10px;">Přejít na reset hesla</button>
        </div>
        <div class="prvek">
            <a href="/login">Zpět na přihlášení</a>
        </div>
    </div>
</div> 