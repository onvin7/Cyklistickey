<section class="content-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-ad"></i> Vytvoření nové reklamy</h2>
        <a href="/admin/ads" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Zpět
        </a>
    </div>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form action="/admin/ads/store" method="post" enctype="multipart/form-data">
                <?= \App\Helpers\CsrfHelper::formInput() ?>
                <div class="mb-4">
                    <label for="nazev" class="form-label">Název reklamy <span class="text-danger">*</span></label>
                    <input type="text" name="nazev" id="nazev" class="form-control" required 
                           placeholder="Např. Banner Cycli.cz">
                    <div class="form-text">Název pro identifikaci reklamy v adminu</div>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block">Typ reklamy <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="typ" id="typ_image" value="image" checked>
                        <label class="form-check-label" for="typ_image">Obrázek s odkazem</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="typ" id="typ_code" value="code">
                        <label class="form-check-label" for="typ_code">Vlastní kód (Google Ads / Script)</label>
                    </div>
                </div>

                <div id="image_fields">
                    <div class="mb-4">
                        <label for="obrazek" class="form-label">Obrázek reklamy <span class="text-danger">*</span></label>
                        <input type="file" name="obrazek" id="obrazek" class="form-control" accept="image/*">
                        <div class="form-text">Podporované formáty: JPEG, PNG, GIF, WebP. Požadovaný rozměr: 1024×180 px.</div>
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img id="previewImg" src="" alt="Náhled" style="max-width: 300px; max-height: 200px; object-fit: contain;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="odkaz" class="form-label">Odkaz (URL) <span class="text-danger">*</span></label>
                        <input type="url" name="odkaz" id="odkaz" class="form-control" 
                               placeholder="https://www.example.com">
                        <div class="form-text">URL adresa, na kterou má reklama odkazovat</div>
                    </div>
                </div>

                <div id="code_fields" style="display: none;">
                    <div class="mb-4">
                        <label for="kod" class="form-label">Vlastní kód / Script <span class="text-danger">*</span></label>
                        <textarea name="kod" id="kod" class="form-control" rows="5" placeholder="Sem vložte kód reklamy (např. od Google Ads)"></textarea>
                        <div class="form-text">Vložený kód bude vykreslen přímo do stránky.</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="start" class="form-label">Datum začátku <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="date" name="start_date" id="start_date" class="form-control" required
                                  min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                            <div class="form-text">Datum</div>
                        </div>
                        <div class="col-md-6">
                            <input type="time" name="start_time" id="start_time" class="form-control" required
                                  value="<?= date('H:i') ?>">
                            <div class="form-text">Čas (HH:MM)</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="end" class="form-label">Datum konce <span class="text-danger">*</span></label>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="date" name="end_date" id="end_date" class="form-control" required
                                  min="<?= date('Y-m-d') ?>">
                            <div class="form-text">Datum</div>
                        </div>
                        <div class="col-md-6">
                            <input type="time" name="end_time" id="end_time" class="form-control" required>
                            <div class="form-text">Čas (HH:MM)</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="frekvence" class="form-label">Váha reklamy (1–10)</label>
                    <input type="number" name="frekvence" id="frekvence" class="form-control" 
                           value="1" min="1" max="10" required>
                    <div class="form-text">Relativní váha ve výběru (vyšší = častěji). 1–10.</div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktivni" id="aktivni" value="1" checked>
                        <label class="form-check-label" for="aktivni">
                            Aktivní
                        </label>
                    </div>
                    <div class="form-text">Aktivní reklamy se zobrazují v článcích</div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="vychozi" id="vychozi" value="1">
                        <label class="form-check-label" for="vychozi">
                            Výchozí reklama
                        </label>
                    </div>
                    <div class="form-text">Výchozí reklama se zobrazí, pokud nejsou žádné aktivní reklamy v daném časovém rozsahu</div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-action">
                        <i class="fa-solid fa-save me-1"></i> Vytvořit reklamu
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Přepínání typů reklamy
    const typImage = document.getElementById('typ_image');
    const typCode = document.getElementById('typ_code');
    const imageFields = document.getElementById('image_fields');
    const codeFields = document.getElementById('code_fields');
    const obrazekInput = document.getElementById('obrazek');
    const odkazInput = document.getElementById('odkaz');
    const kodInput = document.getElementById('kod');

    function toggleFields() {
        if (typImage.checked) {
            imageFields.style.display = 'block';
            codeFields.style.display = 'none';
            obrazekInput.required = true;
            odkazInput.required = true;
            kodInput.required = false;
        } else {
            imageFields.style.display = 'none';
            codeFields.style.display = 'block';
            obrazekInput.required = false;
            odkazInput.required = false;
            kodInput.required = true;
        }
    }

    typImage.addEventListener('change', toggleFields);
    typCode.addEventListener('change', toggleFields);
    toggleFields(); // Inicializace

    // Náhled obrázku
    const imageInput = document.getElementById('obrazek');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });

    // Nastaví minimální koncové datum podle zvoleného datumu začátku
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Inicializace koncového datumu
    endDateInput.value = startDateInput.value;
    endDateInput.min = startDateInput.value;
    
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        
        // Pokud je aktuální koncové datum dřívější než počáteční, aktualizujeme ho
        if (endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });
    
    // Nastaví výchozí čas konce na o 1 hodinu více než čas začátku
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    function addDaysToDateString(dateString, days) {
        const date = new Date(`${dateString}T00:00:00`);
        date.setDate(date.getDate() + days);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function setDefaultEndTime() {
        const startTime = startTimeInput.value;
        if (startTime) {
            const [hours, minutes] = startTime.split(':').map(Number);
            const wrapsToNextDay = hours + 1 >= 24;
            const endHours = (hours + 1) % 24;
            endTimeInput.value = `${endHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

            if (wrapsToNextDay && startDateInput.value && endDateInput.value === startDateInput.value) {
                endDateInput.value = addDaysToDateString(startDateInput.value, 1);
            }
        }
    }
    
    // Nastavení výchozího času konce při načtení stránky
    setDefaultEndTime();
    
    // Aktualizace času konce při změně času začátku
    startTimeInput.addEventListener('change', setDefaultEndTime);
});
</script>


