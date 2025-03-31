<div class="ohraniceni new">
    <div class="logo">
        <img src="/assets/graphics/logo_text_cyklistickey.png" alt="Cyklistickey logo">
    </div>
    <div class="inputy">
        <form method="POST" action="/reset-password/save" class="input-wrapper">
            <?php 
            // Bezpečné získání tokenu z URL a zobrazení ve formuláři
            $token = isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '';
            if (empty($token)) {
                echo '<div class="prvek alert alert-danger">Chybí token pro reset hesla!</div>';
            }
            ?>
            <input type="hidden" name="token" value="<?= $token ?>">
            <div class="prvek" style="margin-top: 10px;">
                <span class="form-title">Obnova hesla</span>
            </div>
            <div class="prvek">
                <div class="input-group validator-msg-holder js-validated-element-wrapper">
                    <label class="input-group__label" for="new_password">NOVÉ HESLO</label>
                    <input id="new_password" class="form-control input-group__input" name="new_password" required="" type="password" placeholder="Nové heslo" />
                </div>
            </div>
            <div class="prvek">
                <div class="input-group validator-msg-holder js-validated-element-wrapper">
                    <label class="input-group__label" for="confirm_password">POTVRDIT HESLO</label>
                    <input id="confirm_password" class="form-control input-group__input" name="confirm_password" required="" type="password" placeholder="Potvrdit heslo" />
                </div>
            </div>
            <input type="submit" value="Změnit heslo">
            <div class="prvek">
                <a href="/login">Zpět na přihlášení</a>
            </div>
        </form>
    </div>
</div>

<!-- ✅ Jednoduchá JS validace pro kontrolu hesel -->
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Hesla se neshodují!');
        }
    });
</script>