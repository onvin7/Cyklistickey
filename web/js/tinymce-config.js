// Inicializace TinyMCE po načtení stránky
document.addEventListener('DOMContentLoaded', function() {
    // Počkáme na načtení TinyMCE
    if (typeof tinymce !== 'undefined') {
        initTinyMCE();
    } else {
        // Pokud TinyMCE není načteno, počkáme
        setTimeout(function() {
            if (typeof tinymce !== 'undefined') {
                initTinyMCE();
            } else {
                console.error('TinyMCE se nepodařilo načíst');
            }
        }, 1000);
    }
});

function initTinyMCE() {
    tinymce.init({
        selector: '#editor',
        plugins: 'image link lists code',
        toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link | code | customspellcheck removespellcheck',
        height: 500,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '/admin/upload-image',
        document_base_url: window.location.origin, // Explicitně nastavíme base URL
        
        // Lokalizace pro češtinu
        language: 'cs',
        language_url: 'https://cdn.tiny.cloud/1/l1vyo5rc4lr9bndoweby2luoq845e7lw20i4gb1rtwn0xify/tinymce/7/langs/cs.js',
        
        // Vlastní kontrola pravopisu pomocí JavaScript
        setup: function(editor) {
            // Kontrola, jestli je SpellChecker dostupný
            if (typeof SpellChecker === 'undefined') {
                console.error('SpellChecker není načten');
                return;
            }
            
            // Vytvoření vlastní kontroly pravopisu
            const spellChecker = new SpellChecker();
            
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
}
