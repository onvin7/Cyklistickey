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
     * Normalizace HTML pro porovnání (odstraní prázdné znaky a normalizuje)
     */
    function normalizeHTML(html) {
        if (!html) return '';
        // Odstranit prázdné znaky na začátku a konci
        return html.trim().replace(/\s+/g, ' ');
    }

    /**
     * Získání aktuálního obsahu TinyMCE editoru
     */
    function getEditorContent() {
        try {
            const editor = tinymce.get('editor');
            if (editor) {
                const content = editor.getContent();
                return normalizeHTML(content);
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
        // Nejdřív uložit hodnoty z ostatních polí (nezávisle na editoru)
        const nazevEl = document.getElementById('nazev');
        const datumEl = document.getElementById('datum_publikace');
        const viditelnostEl = document.getElementById('viditelnost');
        
        originalValues = {
            nazev: nazevEl ? nazevEl.value : '',
            datumPublikace: datumEl ? datumEl.value : '',
            viditelnost: viditelnostEl ? viditelnostEl.checked : false,
            kategorie: getSelectedCategories(),
            editorContent: '' // Bude doplněno později
        };

        // Počkáme na inicializaci TinyMCE pro obsah editoru
        const waitForEditor = function(attempts = 0) {
            if (attempts > 100) {
                console.warn('TinyMCE editor se nepodařilo inicializovat včas, používám hodnoty bez editoru');
                editorReady = true;
                return;
            }

            try {
                const editor = tinymce.get('editor');
                if (editor && editor.initialized) {
                    originalValues.editorContent = getEditorContent();
                    editorReady = true;
                    console.log('Původní hodnoty formuláře uloženy (včetně editoru)');
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
            // Pokud editor ještě není připraven, zkontrolovat alespoň ostatní pole
            const current = getCurrentValues();
            const original = originalValues || {};
            
            // Porovnání názvu
            if (current.nazev !== (original.nazev || '')) {
                return true;
            }
            
            // Porovnání data publikace
            if (current.datumPublikace !== (original.datumPublikace || '')) {
                return true;
            }
            
            // Porovnání viditelnosti
            if (current.viditelnost !== (original.viditelnost || false)) {
                return true;
            }
            
            // Porovnání kategorií (pole)
            if (JSON.stringify(current.kategorie) !== JSON.stringify(original.kategorie || [])) {
                return true;
            }
            
            return false;
        }

        const current = getCurrentValues();

        // Porovnání obsahu editoru
        if (current.editorContent !== (originalValues.editorContent || '')) {
            return true;
        }

        // Porovnání názvu
        if (current.nazev !== (originalValues.nazev || '')) {
            return true;
        }

        // Porovnání data publikace
        if (current.datumPublikace !== (originalValues.datumPublikace || '')) {
            return true;
        }

        // Porovnání viditelnosti
        if (current.viditelnost !== (originalValues.viditelnost || false)) {
            return true;
        }

        // Porovnání kategorií (pole)
        if (JSON.stringify(current.kategorie) !== JSON.stringify(originalValues.kategorie || [])) {
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
            // Moderní prohlížeče vyžadují nastavení returnValue a preventDefault
            const message = 'Máte neuložené změny. Opravdu chcete opustit stránku?';
            event.preventDefault();
            event.returnValue = message; // Pro Chrome a Edge
            return message; // Pro Firefox a starší prohlížeče
        }
    }

    /**
     * Handler pro tlačítko "Zpět" - zobrazí confirm dialog
     */
    function handleBackButton(event) {
        // Pokud byl formulář odeslán, povolit navigaci
        if (formSubmitted) {
            return;
        }

        // Pokud jsou neuložené změny, zobrazit confirm dialog
        if (valuesChanged()) {
            const confirmed = confirm('Máte neuložené změny. Opravdu chcete opustit stránku? Neuložené změny budou ztraceny.');
            if (!confirmed) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        }
    }

    /**
     * Inicializace
     */
    function init() {
        // Uložit původní hodnoty po načtení stránky
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                saveOriginalValues();
                setupEventListeners();
            });
        } else {
            saveOriginalValues();
            setupEventListeners();
        }
    }

    /**
     * Nastavení event listenerů
     */
    function setupEventListeners() {
        // Přidat beforeunload event listener
        window.addEventListener('beforeunload', handleBeforeUnload);

        // Sledovat submit formuláře
        const form = document.querySelector('form[action*="/admin/articles/"]');
        if (form) {
            form.addEventListener('submit', function() {
                formSubmitted = true;
            });
        }

        // Přidat handler na tlačítko "Zpět"
        const backButtons = document.querySelectorAll('a[href="/admin/articles"]');
        backButtons.forEach(function(button) {
            button.addEventListener('click', handleBackButton);
        });

        // Sledovat změny v TinyMCE editoru
        if (typeof tinymce !== 'undefined') {
            // Použít init event místo AddEditor pro lepší kompatibilitu
            tinymce.on('init', function() {
                const editor = tinymce.get('editor');
                if (editor) {
                    // Editor je připraven, aktualizovat původní hodnoty editoru
                    setTimeout(function() {
                        if (originalValues && !originalValues.editorContent) {
                            originalValues.editorContent = getEditorContent();
                            editorReady = true;
                            console.log('Původní hodnoty editoru uloženy (z init event)');
                        } else if (!editorReady) {
                            originalValues.editorContent = getEditorContent();
                            editorReady = true;
                            console.log('Původní hodnoty editoru uloženy (z init event - aktualizace)');
                        }
                    }, 500);
                }
            });

            // Také sledovat AddEditor pro jistotu
            tinymce.on('AddEditor', function(e) {
                const editor = e.editor;
                if (editor.id === 'editor') {
                    setTimeout(function() {
                        if (originalValues && !originalValues.editorContent) {
                            originalValues.editorContent = getEditorContent();
                            editorReady = true;
                            console.log('Původní hodnoty editoru uloženy (z AddEditor event)');
                        } else if (!editorReady) {
                            originalValues.editorContent = getEditorContent();
                            editorReady = true;
                            console.log('Původní hodnoty editoru uloženy (z AddEditor event - aktualizace)');
                        }
                    }, 500);
                }
            });
        }
    }

    // Spustit inicializaci
    init();
})();
