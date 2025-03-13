<div class="container mt-4">
    <h2 class="mb-4">⚙ Nastavení účtu</h2>

    <form action="/admin/settings/update" method="POST" enctype="multipart/form-data">
        <div class="card mb-3">
            <div class="card-header">👤 Osobní údaje</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Jméno:</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Příjmení:</label>
                    <input type="text" class="form-control" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">📧 Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="profile_photo" class="form-label">Profilová fotka</label>
                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" onchange="previewImage(event, 'profile-preview', 'profile-container')">
                    <div id="profile-container" class="mt-3">
                        <?php if (!empty($_SESSION['profile_photo'])): ?>
                            <p class="mt-2">Aktuální:</p>
                            <img id="profile-preview" src="/uploads/users/thumbnails/<?= htmlspecialchars($_SESSION['profile_photo']) ?>" alt="Profilový náhled" style="max-width: 200px;">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="header_photo" class="form-label">Záhlaví profilu</label>
                    <input type="file" class="form-control" id="header_photo" name="header_photo" onchange="previewImage(event, 'header-preview', 'header-container')">
                    <div id="header-container" class="mt-3">
                        <?php if (!empty($_SESSION['header_photo'])): ?>
                            <p class="mt-2">Aktuální:</p>
                            <img id="header-preview" src="/uploads/users/background/<?= htmlspecialchars($_SESSION['header_photo']) ?>" alt="Background náhled" style="max-width: 400px;">
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Popis:</label>
                    <textarea class="form-control" name="description"><?= htmlspecialchars($user['popis']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">🌍 Sociální sítě</div>
            <div class="card-body" id="social-links">
                <form method="POST" action="/admin/social-sites/save">
                    <?php foreach ($social_links as $social): ?>
                        <div class="d-flex mb-2 social-entry">
                            <select class="form-select me-2" name="fa_class[]">
                                <option value="">Vyber ikonu</option>
                                <?php foreach ($fontawesome_icons as $icon): ?>
                                    <option value="<?= $icon ?>" <?= $social['fa_class'] == $icon ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($icon) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" class="form-control me-2" name="link[]" placeholder="Odkaz" value="<?= htmlspecialchars($social['link']) ?>" required>
                            <button type="button" class="btn btn-danger remove-social">✖️</button>
                        </div>
                    <?php endforeach; ?>

                    <button type="submit" class="btn btn-primary">Uložit</button>
                    <button type="button" class="btn btn-success" id="add-social">➕ Přidat sociální síť</button>
                </form>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">💾 Uložit vše</button>
    </form>
</div>

<script>
    function filterAvailableSocials() {
        let selectedSocials = [...document.querySelectorAll('select[name="social_id[]"]')]
            .map(select => select.value);
        document.querySelectorAll('select[name="social_id[]"] option').forEach(option => {
            if (selectedSocials.includes(option.value) && option.value !== "") {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });
    }

    document.getElementById('add-social')?.addEventListener('click', function() {
        let container = document.getElementById('social-links');
        let existingSocials = [...document.querySelectorAll('select[name="social_id[]"]')]
            .map(select => select.value);

        let availableSocials = <?php echo json_encode(array_map(function ($site) {
                                    return ["id" => $site['id'], "name" => htmlspecialchars($site['name'])];
                                }, $available_socials)); ?>;

        let filteredSocials = availableSocials.filter(site => !existingSocials.includes(site.id.toString()));

        if (filteredSocials.length === 0) {
            alert("❌ Nelze přidat více sociálních sítí, všechny dostupné jsou již vybrány.");
            return;
        }

        let div = document.createElement('div');
        div.classList.add('d-flex', 'mb-2', 'social-entry');

        let select = document.createElement('select');
        select.classList.add('form-select', 'me-2');
        select.name = 'social_id[]';
        select.onchange = filterAvailableSocials;

        let defaultOption = document.createElement('option');
        defaultOption.value = "";
        defaultOption.text = "Vyber sociální síť";
        select.appendChild(defaultOption);

        availableSocials.forEach(site => {
            let option = document.createElement('option');
            option.value = site.id;
            option.text = site.name;
            select.appendChild(option);
        });

        let input = document.createElement('input');
        input.type = 'text';
        input.classList.add('form-control', 'me-2');
        input.name = 'link[]';
        input.placeholder = 'Uživatelské jméno.';
        input.required = true;

        let button = document.createElement('button');
        button.type = 'button';
        button.classList.add('btn', 'btn-danger', 'remove-social');
        button.innerHTML = '✖️';
        button.addEventListener('click', function(event) {
            event.preventDefault();
            div.remove();
            filterAvailableSocials();
        });

        div.appendChild(select);
        div.appendChild(input);
        div.appendChild(button);
        container.insertBefore(div, document.getElementById('add-social'));
        filterAvailableSocials();
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-social')) {
            event.preventDefault();
            event.target.parentElement.remove();
            filterAvailableSocials();
        }
    });

    filterAvailableSocials();
</script>