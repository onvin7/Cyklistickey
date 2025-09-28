/**
 * SpellChecker třída pro kontrolu pravopisu s hunspell slovníky
 */
class SpellChecker {
    constructor() {
        this.dictionary = null;
        this.affix = null;
        this.isLoaded = false;
        this.loadDictionary();
    }

    /**
     * Načte hunspell slovník
     */
    async loadDictionary() {
        try {
            // Načtení affix souboru
            const affResponse = await fetch('/js/hunspell/cs_CZ.aff');
            this.affix = await affResponse.text();
            
            // Načtení dictionary souboru
            const dicResponse = await fetch('/js/hunspell/cs_CZ.dic');
            const dicText = await dicResponse.text();
            
            // Parsování dictionary
            this.dictionary = this.parseDictionary(dicText);
            this.isLoaded = true;
            
            console.log('Hunspell slovník načten úspěšně');
        } catch (error) {
            console.error('Chyba při načítání hunspell slovníku:', error);
            this.isLoaded = false;
        }
    }

    /**
     * Parsuje dictionary soubor
     */
    parseDictionary(dicText) {
        const words = new Set();
        const lines = dicText.split('\n');
        
        // První řádek obsahuje počet slov
        const wordCount = parseInt(lines[0]);
        
        // Načte slova (omezeně pro výkon)
        const maxWords = Math.min(wordCount, 50000); // Omezení pro výkon
        
        for (let i = 1; i < lines.length && i <= maxWords; i++) {
            const line = lines[i].trim();
            if (line) {
                // Rozdělí slovo a affixy
                const parts = line.split('/');
                const word = parts[0].toLowerCase();
                
                // Přidá pouze slova s českými znaky nebo běžná slova
                if (word.length > 1 && /^[a-záčďéěíňóřšťúůýž]+$/.test(word)) {
                    words.add(word);
                }
            }
        }
        
        console.log(`Načteno ${words.size} slov ze slovníku`);
        return words;
    }

    /**
     * Zkontroluje text a vrátí seznam chybných slov
     */
    checkText(text) {
        if (!this.isLoaded) {
            console.warn('Slovník není načten');
            return [];
        }

        const misspelled = [];
        const words = this.extractWords(text);
        
        for (const word of words) {
            if (!this.isWordValid(word)) {
                misspelled.push(word);
            }
        }
        
        return misspelled;
    }

    /**
     * Extrahuje slova z textu
     */
    extractWords(text) {
        // Odstraní HTML tagy a extrahuje slova
        const cleanText = text.replace(/<[^>]*>/g, ' ')
                             .replace(/[^\w\s]/g, ' ')
                             .toLowerCase();
        
        const words = cleanText.split(/\s+/)
                              .filter(word => word.length > 2)
                              .filter(word => /^[a-záčďéěíňóřšťúůýž]+$/.test(word));
        
        return [...new Set(words)]; // Odstraní duplicity
    }

    /**
     * Zkontroluje, jestli je slovo platné
     */
    isWordValid(word) {
        if (!this.dictionary) return true;
        
        const lowerWord = word.toLowerCase();
        
        // Přímá kontrola ve slovníku
        if (this.dictionary.has(lowerWord)) {
            return true;
        }
        
        // Kontrola variant (základní implementace)
        const variants = this.generateVariants(lowerWord);
        for (const variant of variants) {
            if (this.dictionary.has(variant)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Generuje varianty slova pro kontrolu
     */
    generateVariants(word) {
        const variants = [];
        
        // Odstranění koncovek (základní implementace)
        const endings = ['y', 'é', 'í', 'á', 'ů', 'ě', 'i', 'e', 'a', 'o', 'u'];
        
        for (const ending of endings) {
            if (word.endsWith(ending)) {
                variants.push(word.slice(0, -1));
            }
        }
        
        // Odstranění dvojitých koncovek
        if (word.endsWith('ých')) {
            variants.push(word.slice(0, -3));
            variants.push(word.slice(0, -3) + 'ý');
        }
        if (word.endsWith('ého')) {
            variants.push(word.slice(0, -3));
            variants.push(word.slice(0, -3) + 'ý');
        }
        if (word.endsWith('ém')) {
            variants.push(word.slice(0, -2));
            variants.push(word.slice(0, -2) + 'ý');
        }
        
        return variants;
    }

    /**
     * Zvýrazní chyby v editoru
     */
    highlightErrors(editor, misspelledWords) {
        // Odstraní předchozí zvýraznění
        this.removeHighlighting(editor);
        
        const content = editor.getContent();
        let newContent = content;
        
        for (const word of misspelledWords) {
            // Vytvoří regex pro hledání slova (case insensitive)
            const regex = new RegExp(`\\b${this.escapeRegex(word)}\\b`, 'gi');
            
            // Nahradí slovo zvýrazněnou verzí
            newContent = newContent.replace(regex, `<span class="spell-error" style="background-color: #ffcccc; border-bottom: 2px solid #ff0000; cursor: help;" title="Možná chyba pravopisu: ${word}">$&</span>`);
        }
        
        editor.setContent(newContent);
    }

    /**
     * Odstraní zvýraznění chyb
     */
    removeHighlighting(editor) {
        const content = editor.getContent();
        const newContent = content.replace(/<span class="spell-error"[^>]*>(.*?)<\/span>/gi, '$1');
        editor.setContent(newContent);
    }

    /**
     * Escapuje speciální znaky pro regex
     */
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Zkontroluje, jestli je slovník načten
     */
    isReady() {
        return this.isLoaded;
    }
}

// Export pro použití v TinyMCE
window.SpellChecker = SpellChecker;
