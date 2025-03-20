<section class="content-section">
    <div class="section-header">
        <h2 class="mb-4"><i class="fas fa-cog me-2"></i>Nastavení účtu</h2>
        <div class="text-end">
            <button type="button" class="btn btn-secondary" onclick="history.back()"><i class="fas fa-arrow-left me-2"></i>Zpět</button>
        </div>
    </div>
    <form action="/admin/settings/update" method="POST" enctype="multipart/form-data">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header"><i class="fas fa-user me-2"></i>Osobní údaje</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-signature me-2"></i>Jméno:</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-signature me-2"></i>Příjmení:</label>
                            <input type="text" class="form-control" name="surname" value="<?= htmlspecialchars($user['surname']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope me-2"></i>Email:</label>
                            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="profile_photo" class="form-label"><i class="fas fa-camera me-2"></i>Profilová fotka</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo" onchange="previewImage(event, 'profile-preview', 'profile-container')">
                            <div id="profile-container" class="mt-3">
                                <?php if (!empty($_SESSION['profile_photo'])): ?>
                                    <p class="mt-2">Aktuální:</p>
                                    <img id="profile-preview" src="/uploads/users/thumbnails/<?= htmlspecialchars($_SESSION['profile_photo']) ?>" alt="Profilový náhled" style="max-width: 200px;">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="header_photo" class="form-label"><i class="fas fa-image me-2"></i>Záhlaví profilu</label>
                            <input type="file" class="form-control" id="header_photo" name="header_photo" onchange="previewImage(event, 'header-preview', 'header-container')">
                            <div id="header-container" class="mt-3">
                                <?php if (!empty($_SESSION['header_photo'])): ?>
                                    <p class="mt-2">Aktuální:</p>
                                    <img id="header-preview" src="/uploads/users/background/<?= htmlspecialchars($_SESSION['header_photo']) ?>" alt="Background náhled" style="max-width: 400px;">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-pen me-2"></i>Popis:</label>
                            <textarea id="editor" name="description"><?= htmlspecialchars($user['popis']) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header"><i class="fas fa-share-alt me-2"></i>Sociální sítě</div>
                    <div class="card-body" id="social-links">
                        <form method="POST" action="/admin/social-sites/save">
                            <?php foreach ($social_links as $social): ?>
                                <div class="d-flex mb-2 social-entry">
                                    <select class="form-select me-2" name="social_id[]">
                                        <option value="">Vyber sociální síť</option>
                                        <?php foreach ($available_socials as $site): ?>
                                            <option value="<?= $site['id'] ?>" <?= $social['social_id'] == $site['id'] ? 'selected' : '' ?>>
                                                <?php echo htmlspecialchars($site['nazev']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" class="form-control me-2" name="link[]" placeholder="Odkaz" value="<?= htmlspecialchars($social['link']) ?>" required>
                                    <button type="button" class="btn btn-danger remove-social"><i class="fas fa-times"></i></button>
                                </div>
                            <?php endforeach; ?>

                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Uložit</button>
                            <button type="button" class="btn btn-success" id="add-social"><i class="fas fa-plus me-2"></i>Přidat sociální síť</button>
                        </form>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left me-2"></i>Zpět
                    </button>
                    <button type="submit" class="btn btn-action">
                        <i class="fas fa-save me-2"></i>Uložit vše
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>

<style>
    #editor {
        min-height: 300px;
    }
    
    .tox-tinymce {
        border-radius: 0.25rem !important;
    }
</style>

<!-- TinyMCE + konfigurace -->
<script src="/js/tinymce-config.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#editor',
                height: 400,
                setup: function(editor) {
                    editor.on('init', function() {
                        document.querySelector('.tox-tinymce').style.height = '400px';
                        document.querySelector('.tox-edit-area').style.height = '330px';
                    });
                }
            });
        }
    });
</script>

<!-- Existující script pro správu sociálních sítí -->
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