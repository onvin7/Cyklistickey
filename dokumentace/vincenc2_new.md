**Stránka redakce:**
Stránka s přehledem redakce poskytuje návštěvníkům informace o autorském týmu magazínu. Zobrazuje seznam všech aktivních autorů s jejich fotografiemi, krátkým popisem a odkazy na jejich profily. Na stránce jsou autoři řazeni podle počtu publikovaných článků a jejich aktivitě. Stránka obsahuje:

1. **Úvodní text** - představení redakčního týmu a filozofie magazínu
2. **Seznam autorů** - přehledné karty s fotografiemi a základními informacemi
3. **Odkazy na profily** - každá karta obsahuje odkaz na detailní profil autora
4. **Kontaktní informace** - e-mailové adresy na redakci pro zájemce o spolupráci

Stránka je implementována v souboru **app/Views/Web/redakce/index.php** a data načítá pomocí UserController:

```php
<div class="container-authors">
    <?php foreach ($authors as $author): ?>
        <div class="author-card">
            <a href="/user/<?php echo TextHelper::generateFriendlyUrl($author['name'] . '-' . $author['surname']); ?>/">
                <div class="author-img">
                    <img loading="lazy" src="/uploads/users/thumbnails/<?php echo !empty($author['profil_foto']) ? htmlspecialchars($author['profil_foto']) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($author['name'] . ' ' . $author['surname']); ?>">
                </div>
                <div class="author-info">
                    <h3><?php echo htmlspecialchars($author['name'] . ' ' . $author['surname']); ?></h3>
                    <p class="author-role"><?php echo htmlspecialchars($author['role']); ?></p>
                    <p class="author-articles"><?php echo $author['article_count']; ?> článků</p>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
```

**Kontaktní stránka:**
Kontaktní stránka poskytuje návštěvníkům možnost spojit se s redakcí magazínu. Obsahuje následující prvky:

1. **Kontaktní formulář** - umožňuje odeslání zprávy přímo redakci
2. **Kontaktní údaje** - e-mailové adresy, telefony a odkazy na sociální sítě
3. **Mapa** - interaktivní mapa s umístěním redakce
4. **Sekce FAQ** - odpovědi na nejčastější dotazy čtenářů

Implementace kontaktního formuláře v souboru **app/Views/Web/contact/index.php** zahrnuje validaci na straně klienta i serveru:

```php
<form id="contact-form" method="post" action="/contact/send/">
    <div class="form-group">
        <label for="name">Jméno a příjmení</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="subject">Předmět</label>
        <input type="text" class="form-control" id="subject" name="subject" required>
    </div>
    <div class="form-group">
        <label for="message">Zpráva</label>
        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
    </div>
    <div class="form-group">
        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
    </div>
    <button type="submit" class="btn btn-primary">Odeslat zprávu</button>
</form>
```

Zpracování formuláře je zajištěno metodou `sendMessage()` v `ContactController.php`, která ověřuje reCAPTCHA, validuje vstupní data a odesílá e-mail redakci.

**Stránka závodů:**
Sekce závodů poskytuje přehled nadcházejících cyklistických událostí a závodů. Je implementována jako samostatná část webu s vlastním designem a funkcionalitou. Stránka obsahuje:

1. **Kalendář závodů** - přehledný kalendář s možností filtrování podle data, typu závodu a lokality
2. **Detail závodu** - informace o jednotlivých závodech včetně trasy, profilu, pravidel a registrace
3. **Výsledky závodů** - archiv výsledků z proběhlých závodů s možností filtrování
4. **Fotogalerie** - fotografie z jednotlivých závodů organizované podle ročníků

Implementace v souboru **app/Views/Web/race/index.php** využívá JavaScript pro interaktivní filtrování a zobrazení závodů:

```php
<div class="race-filter">
    <div class="filter-item">
        <label for="race-type">Typ závodu</label>
        <select id="race-type" class="form-control">
            <option value="all">Všechny typy</option>
            <?php foreach ($raceTypes as $type): ?>
                <option value="<?php echo htmlspecialchars($type['id']); ?>"><?php echo htmlspecialchars($type['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-item">
        <label for="race-date">Datum od</label>
        <input type="date" id="race-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="filter-item">
        <label for="race-location">Lokalita</label>
        <select id="race-location" class="form-control">
            <option value="all">Všechny lokality</option>
            <?php foreach ($locations as $location): ?>
                <option value="<?php echo htmlspecialchars($location['id']); ?>"><?php echo htmlspecialchars($location['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button id="filter-submit" class="btn btn-primary">Filtrovat</button>
</div>

<div id="race-list" class="race-list">
    <?php foreach ($races as $race): ?>
        <div class="race-card" data-type="<?php echo $race['type_id']; ?>" data-location="<?php echo $race['location_id']; ?>">
            <div class="race-date">
                <span class="day"><?php echo date('d', strtotime($race['date'])); ?></span>
                <span class="month"><?php echo date('M', strtotime($race['date'])); ?></span>
            </div>
            <div class="race-info">
                <h3><a href="/race/<?php echo htmlspecialchars($race['url']); ?>/"><?php echo htmlspecialchars($race['name']); ?></a></h3>
                <p class="race-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($race['location_name']); ?></p>
                <p class="race-type"><i class="fas fa-bicycle"></i> <?php echo htmlspecialchars($race['type_name']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
```

Sekce závodů využívá samostatný `RaceController.php`, který obsluhuje všechny požadavky týkající se závodů včetně zobrazení detailů, registrace a výsledků. 