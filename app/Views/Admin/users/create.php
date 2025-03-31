<div class="ohraniceni new">
    <div class="logo register">
        <img src="/assets/graphics/logo_text_cyklistickey.png" alt="Cyklistickey logo">
    </div>
    <div class="inputy">
        <form method="POST" action="/register/submit">
                <div class="prvek">
                    <span class="form-title">Registrace</span>
                </div>
                <?php if (isset($_SESSION['registration_error'])): ?>
                <div class="prvek" style="color: red; margin-bottom: 10px;">
                    <?= $_SESSION['registration_error']; ?>
                    <?php unset($_SESSION['registration_error']); ?>
                </div>
                <?php endif; ?>
                <div class="prvek">
                    <div class="input-group validator-msg-holder js-validated-element-wrapper">
                        <label class="input-group__label" for="email">EMAIL</label>
                        <input id="email" class="form-control input-group__input" name="email" required="" type="email"
                            placeholder="jsem@cyklistickey.cz" />
                    </div>
                </div>
                <div class="prvek">
                    <div class="input-group validator-msg-holder js-validated-element-wrapper">
                        <label class="input-group__label" for="name">JMÉNO</label>
                        <input id="name" class="form-control input-group__input" name="name" required="" type="text"
                            placeholder="Jsem" />
                    </div>
                </div>
                <div class="prvek">
                    <div class="input-group validator-msg-holder js-validated-element-wrapper">
                        <label class="input-group__label" for="surname">PŘÍJMENÍ</label>
                        <input id="surname" class="form-control input-group__input" name="surname" required=""
                            type="text" placeholder="Cyklistickey" />
                    </div>
                </div>
                <div class="prvek">
                    <div class="input-group validator-msg-holder js-validated-element-wrapper">
                        <label class="input-group__label" for="heslo">HESLO</label>
                        <input id="heslo" class="form-control input-group__input" name="heslo" required=""
                            type="password" placeholder="heslo1234" />
                    </div>
                </div>
                <div class="prvek">
                    <div class="input-group validator-msg-holder js-validated-element-wrapper">
                        <label class="input-group__label" for="confirm_heslo">POTVRDIT HESLO</label>
                        <input id="confirm_heslo" class="form-control input-group__input" name="confirm_heslo"
                            required="" type="password" placeholder="heslo1234" />
                    </div>
                </div>

                <input type="submit" value="Registrovat">

            <div class="prvek">
                <a href="/login">Zpět na přihlášení</a>
            </div>
        </form>
    </div>
</div>