/**
 * Varování před zavřením stránky při neuložených změnách v článku
 * Detekuje změny v formuláři a zobrazí alert před zavřením stránky
 */

(function() {
    'use strict';

    let originalValues = {};
    let formSubmitted = false;
    let editorReady = false;

    /**
     * Získání aktuálního obsahu TinyMCE editoru
     */
    function getEditorContent() {
        try {
            const editor = tinymce.get('editor');
            if (editor) {
                return editor.getContent();
            }
        } catch (e) {
            console.warn('TinyMCE editor není dostupný:', e);
        }
        return '';
    }

    /**
     * Získání vybraných kategorií jako pole ID
     */
    function getSelectedCategories() {
        const checkboxes = document.querySelectorAll('input[name="kategorie[]"]:checked');
        return Array.from(checkboxes).map(cb => cb.value).sort();
    }

    /**
     * Získání aktuálních hodnot formuláře
     */
    function getCurrentValues() {
        return {
            editorContent: getEditorContent(),
            nazev: document.getElementById('nazev')?.value || '',
            datumPublikace: document.getElementById('datum_publikace')?.value || '',
            viditelnost: document.getElementById('viditelnost')?.checked || false,
            kategorie: getSelectedCategories()
        };
    }

    /**
     * Uložení původních hodnot formuláře
     */
    function saveOriginalValues() {
        // Počkáme na inicializaci TinyMCE
        const waitForEditor = function(attempts = 0) {
            if (attempts > 50) {
                console.warn('TinyMCE editor se nepodařilo inicializovat včas');
                return;
            }

            try {
                const editor = tinymce.get('editor');
                if (editor && editor.initialized) {
                    originalValues = getCurrentValues();
                    editorReady = true;
                    console.log('Původní hodnoty formuláře uloženy');
                } else {
                    setTimeout(() => waitForEditor(attempts + 1), 100);
                }
            } catch (e) {
                setTimeout(() => waitForEditor(attempts + 1), 100);
            }
        };

        waitForEditor();
    }

    /**
     * Porovnání dvou hodnot
     */
    function valuesChanged() {
        if (!editorReady) {
            return false;
        }

        const current = getCurrentValues();

        // Porovnání obsahu editoru
        if (current.editorContent !== originalValues.editorContent) {
            return true;
        }

        // Porovnání názvu
        if (current.nazev !== originalValues.nazev) {
            return true;
        }

        // Porovnání data publikace
        if (current.datumPublikace !== originalValues.datumPublikace) {
            return true;
        }

        // Porovnání viditelnosti
        if (current.viditelnost !== originalValues.viditelnost) {
            return true;
        }

        // Porovnání kategorií (pole)
        if (JSON.stringify(current.kategorie) !== JSON.stringify(originalValues.kategorie)) {
            return true;
        }

        return false;
    }

    /**
     * Beforeunload event handler
     */
    function handleBeforeUnload(event) {
        // Pokud byl formulář odeslán, neukazovat alert
        if (formSubmitted) {
            return;
        }

        // Pokud jsou neuložené změny, zobrazit alert
        if (valuesChanged()) {
            // Moderní prohlížeče ignorují vlastní zprávu, ale vyžadují nastavení returnValue
            event.preventDefault();
            event.returnValue = ''; // Chrome vyžaduje prázdný string
            return ''; // Pro starší prohlížeče
        }
    }

    /**
     * Inicializace
     */
    function init() {
        // Uložit původní hodnoty po načtení stránky
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', saveOriginalValues);
        } else {
            saveOriginalValues();
        }

        // Přidat beforeunload event listener
        window.addEventListener('beforeunload', handleBeforeUnload);

        // Sledovat submit formuláře
        const form = document.querySelector('form[action*="/admin/articles/"]');
        if (form) {
            form.addEventListener('submit', function() {
                formSubmitted = true;
            });
        }

        // Sledovat změny v TinyMCE editoru
        if (typeof tinymce !== 'undefined') {
            tinymce.on('AddEditor', function(e) {
                const editor = e.editor;
                if (editor.id === 'editor') {
                    editor.on('change keyup', function() {
                        // Editor je připraven, můžeme aktualizovat původní hodnoty pokud ještě nejsou uloženy
                        if (!editorReady) {
                            originalValues = getCurrentValues();
                            editorReady = true;
                        }
                    });
                }
            });
        }
    }

    // Spustit inicializaci
    init();
})();

