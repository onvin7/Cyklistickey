// Jednoduchá TinyMCE konfigurace s vestavěnou kontrolou pravopisu prohlížeče
document.addEventListener('DOMContentLoaded', function() {
    // Počkáme na načtení TinyMCE
    const initEditor = function() {
        if (typeof tinymce === 'undefined') {
            setTimeout(initEditor, 100);
            return;
        }

        tinymce.init({
            selector: '#editor',
            plugins: 'image link lists code',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link | code',
            height: 500,
            automatic_uploads: true,
            file_picker_types: 'image',
            images_upload_url: '/admin/upload-image',
            document_base_url: window.location.origin,
            
            // Lokalizace pro češtinu
            language: 'cs',
            language_url: 'https://cdn.tiny.cloud/1/l1vyo5rc4lr9bndoweby2luoq845e7lw20i4gb1rtwn0xify/tinymce/7/langs/cs.js',
            
            // Nastavení pro vestavěnou kontrolu pravopisu prohlížeče
            browser_spellcheck: true,
            
            // Nastavení jazyka pro kontrolu pravopisu
            content_language: 'cs',
            
            // Vlastní nastavení editoru
            setup: function(editor) {
                // Nastavení jazyka pro kontrolu pravopisu při inicializaci
                editor.on('init', function() {
                    // Nastavit jazyk pro kontrolu pravopisu
                    const body = editor.getBody();
                    if (body) {
                        body.setAttribute('lang', 'cs');
                        body.setAttribute('spellcheck', 'true');
                    }
                });
                
                // Zajistit, že se jazyk nastaví při každé změně obsahu
                editor.on('change keyup', function() {
                    const body = editor.getBody();
                    if (body) {
                        body.setAttribute('lang', 'cs');
                        body.setAttribute('spellcheck', 'true');
                    }
                });
            },

            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    fetch('/admin/upload-image', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result && result.location) {
                            resolve(result.location);
                        } else {
                            reject('Chybí "location" v odpovědi serveru.');
                        }
                    })
                    .catch(error => reject(`Chyba při uploadu: ${error.message}`));
                });
            },
        });
    };

    initEditor();
}); 