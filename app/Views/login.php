<?php
use App\Helpers\CSRFHelper;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrfToken = CSRFHelper::generateToken();
?>
<form method="POST" action="/login/submit" onsubmit="console.log('Form submitting to:', this.action); return true;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <div class="container">
        <div class="ohraniceni new">
            <div class="logo"><img src="/assets/graphics/logo_text_cyklistickey.png" alt="Cyklistickey logo">
            </div>
            <div class="inputy">
            <?php if (isset($_SESSION['login_success'])): ?>
                <div class="prvek" style="color: green; margin-bottom: 10px;">
                    <?= $_SESSION['login_success']; ?>
                    <?php unset($_SESSION['login_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="prvek" style="color: red; margin-bottom: 10px;">
                    <?= $_SESSION['login_error']; ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>
                <div class="input-wrapper">
                    <div class="prvek">
                        <div class="input-group validator-msg-holder js-validated-element-wrapper">
                            <label class="input-group__label" for="email">EMAIL</label>
                            <input id="email" class="form-control input-group__input" name="email" required="" type="email" placeholder="jsem@cyklistickey.cz" />
                        </div>
                    </div>

                    <div class="prvek">
                        <div class="input-group validator-msg-holder js-validated-element-wrapper">
                            <label class="input-group__label" for="password">HESLO</label>
                            <input id="password" class="form-control input-group__input2" name="password" required="" type="password" placeholder="ta čo ja viem" />
                        </div>
                    </div>

                <input type="submit" value="Přihlásit se">

                <div class="prvek">
                    <a href="/reset-password">Zapomněl jsi heslo? Nechtěl bych...</a>
                </div>
                
                <div class="prvek">
                    <a href="/register">Nemáš účet? Tak co tady děláš...</a>
                </div>
            </div>

            </div>
        </div>
    </div>
</form>