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
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link | code | customspellcheck removespellcheck',
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
                
                // Hunspell kontrola pravopisu
                if (typeof SpellChecker !== 'undefined') {
                    const spellChecker = new SpellChecker();
                    
                    // Počkáme na načtení slovníku
                    const checkDictionary = () => {
                        if (spellChecker.isReady()) {
                            setupSpellCheckButtons(editor, spellChecker);
                        } else {
                            setTimeout(checkDictionary, 500);
                        }
                    };
                    
                    checkDictionary();
                }
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

/**
 * Nastaví tlačítka pro kontrolu pravopisu
 */
function setupSpellCheckButtons(editor, spellChecker) {
    // Přidání tlačítka pro kontrolu pravopisu
    editor.ui.registry.addButton('customspellcheck', {
        text: '🔍 Kontrola pravopisu',
        tooltip: 'Zkontrolovat pravopis v textu',
        onAction: function() {
            const content = editor.getContent({format: 'text'});
            const misspelled = spellChecker.checkText(content);
            
            if (misspelled.length > 0) {
                // Vytvoření lepšího dialogu s chybami
                let errorText = `Nalezeno ${misspelled.length} chyb:\n\n`;
                misspelled.forEach((word, index) => {
                    errorText += `${index + 1}. ${word}\n`;
                });
                errorText += '\nChyby budou zvýrazněny v textu červeně.';
                
                // Použití TinyMCE dialogu
                editor.windowManager.alert(errorText, function() {
                    // Zvýraznění chybných slov v editoru
                    spellChecker.highlightErrors(editor, misspelled);
                });
            } else {
                editor.windowManager.alert('✅ Žádné chyby nenalezeny!', function() {});
            }
        }
    });
    
    // Přidání tlačítka pro odstranění zvýraznění
    editor.ui.registry.addButton('removespellcheck', {
        text: '🗑️ Odstranit zvýraznění',
        tooltip: 'Odstranit zvýraznění chyb pravopisu',
        onAction: function() {
            spellChecker.removeHighlighting(editor);
            editor.windowManager.alert('Zvýraznění chyb bylo odstraněno.', function() {});
        }
    });

    // Přidání klávesové zkratky Ctrl+Shift+S pro kontrolu pravopisu
    editor.addShortcut('meta+shift+s', 'Kontrola pravopisu', function() {
        // Spustit vlastní kontrolu pravopisu
        const content = editor.getContent({format: 'text'});
        const misspelled = spellChecker.checkText(content);
        
        if (misspelled.length > 0) {
            let errorText = `Nalezeno ${misspelled.length} chyb:\n\n`;
            misspelled.forEach((word, index) => {
                errorText += `${index + 1}. ${word}\n`;
            });
            errorText += '\nChyby budou zvýrazněny v textu červeně.';
            
            editor.windowManager.alert(errorText, function() {
                spellChecker.highlightErrors(editor, misspelled);
            });
        } else {
            editor.windowManager.alert('✅ Žádné chyby nenalezeny!', function() {});
        }
    });
} 