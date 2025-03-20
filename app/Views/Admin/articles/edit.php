<section class="content-section">
    <div class="section-header">
        <h2>Upravit článek</h2>
    </div>
    
    <form action="/admin/articles/update/<?= htmlspecialchars($article['id']) ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" class="form-control" id="id" name="id" value="<?= htmlspecialchars($article['id']) ?>" required>
        
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="nazev" class="form-label">Název článku</label>
                    <input type="text" class="form-control" id="nazev" name="nazev" value="<?= htmlspecialchars($article['nazev']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="kategorie" class="form-label">Kategorie</label>
                    <select class="form-select" id="kategorie" name="kategorie">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>" <?= $article['id_kategorie'] == $category['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['nazev_kategorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mb-4">
                    <label for="nahled_foto" class="form-label">Náhledové foto</label>
                    <input type="file" class="form-control" id="nahled_foto" name="nahled_foto" onchange="previewImage(event)">
                    
                    <div class="mt-3 preview-box">
                        <?php if (!empty($article['nahled_foto'])): ?>
                            <div class="text-center">
                                <p class="mb-2 text-muted small">Aktuální obrázek:</p>
                                <img src="../../../uploads/thumbnails/male/<?= htmlspecialchars($article['nahled_foto']) ?>" alt="Náhled" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                                <input type="hidden" name="current_foto" value="<?= htmlspecialchars($article['nahled_foto']) ?>">
                            </div>
                        <?php else: ?>
                            <p class="text-muted small text-center">Článek nemá nahraný žádný obrázek</p>
                        <?php endif; ?>
                    </div>
                    
                    <div id="preview-container" class="mt-3 text-center"></div>
                </div>
                
                <div class="mt-4 mb-3">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="viditelnost" name="viditelnost" value="1" <?= $article['viditelnost'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="viditelnost">Viditelný článek</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="editor" class="form-label">Obsah článku</label>
            <textarea id="editor" name="content"><?= $article['obsah'] ?? '' ?></textarea>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="/admin/articles" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Zpět na seznam
            </a>
            <button type="submit" class="btn btn-action">
                <i class="fas fa-save me-1"></i> Uložit změny
            </button>
        </div>
    </form>
</section>

<style>
    #editor {
        min-height: 400px;
    }
    
    .preview-box {
        min-height: 150px;
        background-color: #f8f9fa;
        border: 1px dashed #ced4da;
        border-radius: 0.25rem;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    
    .form-check.form-switch .form-check-input {
        height: 1.5rem;
        width: 3rem;
    }
    
    .form-check.form-switch .form-check-label {
        padding-top: 0.25rem;
        padding-left: 0.5rem;
    }
    
    .tox-tinymce {
        border-radius: 0.25rem !important;
    }
</style>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('preview-container');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="preview-box">
                        <p class="mb-2 text-muted small">Nový obrázek k nahrání:</p>
                        <img src="${e.target.result}" alt="Náhled" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                    </div>`;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    }
</script>
<!-- ✅ TinyMCE + konfigurace -->
<script src="/js/tinymce-config.js"></script>
<script>
    // Nastavení větší výšky editoru
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#editor',
                height: 500,
                // Toto zachová ostatní nastavení z tinymce-config.js a pouze přepíše height
                setup: function(editor) {
                    editor.on('init', function() {
                        // Zajistí viditelnost editoru
                        document.querySelector('.tox-tinymce').style.height = '500px';
                        document.querySelector('.tox-edit-area').style.height = '430px';
                    });
                }
            });
        }
    });
</script>