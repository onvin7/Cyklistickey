# Text to Speech pro články

## Přehled funkcionality

Text to Speech (TTS) umožní uživatelům poslouchat články místo jejich čtení. Tato funkce zlepší dostupnost webu a umožní uživatelům konzumovat obsah při jiných aktivitách (jízda na kole, běh, cestování).

### Hlavní vlastnosti:
- Automatické převedení textového obsahu článků na zvukovou podobu
- Tlačítko "Přehrát článek" v detailu každého článku
- Audio přehrávač s ovládacími prvky (play, pause, rychlost přehrávání)
- Volitelné cachování audio souborů pro rychlejší načítání
- Možnost stáhnout audio soubor pro offline poslech

---

## Možnosti implementace

### 1. Web Speech API (Nejjednodušší - klientská strana)

**Výhody:**
- Zdarma, bez nutnosti API klíčů
- Funguje přímo v prohlížeči
- Žádné dodatečné náklady na server/API
- Okamžité generování, bez prodlevy

**Nevýhody:**
- Omezená podpora prohlížečů (hlavně Chrome, Edge)
- Kvalita hlasu závisí na prohlížeči a OS
- Nelze cachovat (generuje se pokaždé znovu)
- Omezené možnosti přizpůsobení hlasu

**Vhodnost:** Vhodné pro rychlé prototypování nebo jako fallback řešení

---

### 2. Google Cloud Text-to-Speech (Doporučeno)

**Výhody:**
- Vysoká kvalita hlasu (WaveNet technologie)
- Podpora češtiny s přirozeným hlasem
- Možnost cachovat audio soubory
- Stabilní a škálovatelné řešení
- Různé hlasy a rychlosti

**Nevýhody:**
- Platné API (cca $4 za 1M znaků pro Standard, $16 za 1M znaků pro WaveNet)
- Nutnost registrace a API klíče
- Závislost na třetí straně

**Ceny:**
- Standard voices: $4 za 1 milion znaků
- WaveNet voices: $16 za 1 milion znaků
- První 1 milion znaků Standard zdarma měsíčně

**Vhodnost:** Nejvhodnější pro produkční nasazení s vysokou kvalitou

---

### 3. Microsoft Azure Text to Speech

**Výhody:**
- Velmi kvalitní české hlasy (Neural TTS)
- Možnost cachovat audio soubory
- Dobrá cena/výkon poměr
- SSML podpora pro pokročilé formátování

**Nevýhody:**
- Platné API (cca $15 za 1M znaků pro Neural)
- Nutnost Azure účtu a API klíče

**Ceny:**
- Neural voices: $15 za 1 milion znaků
- První 5 milionů znaků Neural zdarma (první měsíc)

**Vhodnost:** Alternativa k Google, pokud již máte Azure infrastrukturu

---

### 4. Amazon Polly

**Výhody:**
- Kvalitní hlasy včetně Neural
- Podpora češtiny
- Možnost cachovat

**Nevýhody:**
- AWS účet nutný
- Složitější integrace

**Ceny:**
- Standard: $4 za 1M znaků
- Neural: $16 za 1M znaků

---

### 5. Open-source lokální řešení (Piper TTS, Coqui TTS)

**Výhody:**
- Zdarma a bez omezení
- Úplná kontrola nad daty
- Žádná závislost na cloudových službách
- Možnost vlastního trénování

**Nevýhody:**
- Nižší kvalita hlasu než komerční řešení
- Vyžaduje výpočetní výkon na serveru
- Složitější nastavení a údržba
- Omezená podpora češtiny

**Vhodnost:** Pro projekty s omezeným rozpočtem nebo vysokými nároky na soukromí

---

## Doporučené řešení

### Pro spuštění: Web Speech API (klientská strana)
- Rychlá implementace bez nákladů
- Dostačující kvalita pro testování

### Pro produkci: Google Cloud Text-to-Speech (WaveNet)
- Nejlepší kvalita hlasu v češtině
- Cachování audio souborů do `/web/uploads/audio/`
- Generování při vytvoření/aktualizaci článku

---

## Architektura řešení

### Komponenty systému:

1. **TTSHelper** (`app/Helpers/TTSHelper.php`)
   - Třída pro práci s TTS API
   - Metody pro generování audio souborů
   - Cachování a správa souborů

2. **ArticleController** (`app/Controllers/Web/ArticleController.php`)
   - Kontrola existence audio souboru
   - Předání cesty k audio souboru do view

3. **ArticleAdminController** (`app/Controllers/Admin/ArticleAdminController.php`)
   - Automatické generování TTS při vytvoření/aktualizaci článku
   - Volitelné manuální re-generování

4. **View - article.php** (`app/Views/Web/articles/article.php`)
   - Zobrazení audio přehrávače s TTS
   - UI tlačítka pro ovládání
   - Fallback na Web Speech API

5. **Databáze** - rozšíření tabulky `clanky`
   - Přidat sloupec `audio_tts_generated` (TINYINT, 0/1)
   - Přidat sloupec `audio_tts_path` (VARCHAR, cesta k souboru)
   - Přidat sloupec `audio_tts_size` (INT, velikost v bytech)

---

## Postup implementace

### Fáze 1: Web Speech API (Rychlé prototypování)

#### Krok 1: Přidání UI tlačítka do `article.php`

V souboru `app/Views/Web/articles/article.php` po nadpisu článku (za řádek 37):

```php
<!-- Text to Speech přehrávač -->
<div class="tts-player">
    <button id="tts-play-btn" class="tts-btn tts-play">
        <i class="fa-solid fa-play"></i> Přečíst článek
    </button>
    <button id="tts-pause-btn" class="tts-btn tts-pause" style="display:none;">
        <i class="fa-solid fa-pause"></i> Pozastavit
    </button>
    <button id="tts-stop-btn" class="tts-btn tts-stop" style="display:none;">
        <i class="fa-solid fa-stop"></i> Zastavit
    </button>
    
    <div class="tts-controls" style="display:none;">
        <label for="tts-speed">Rychlost:</label>
        <select id="tts-speed" class="tts-speed-select">
            <option value="0.75">0.75x</option>
            <option value="1" selected>1x (Normální)</option>
            <option value="1.25">1.25x</option>
            <option value="1.5">1.5x</option>
            <option value="2">2x</option>
        </select>
        
        <span class="tts-progress">
            <span id="tts-current-word">0</span> / <span id="tts-total-words">0</span> slov
        </span>
    </div>
    
    <div id="tts-status" class="tts-status"></div>
</div>
```

#### Krok 2: JavaScript pro Web Speech API

Vytvořit nový soubor `web/js/tts-speech-api.js`:

```javascript
/**
 * Text to Speech pomocí Web Speech API
 * Pro články na Cyklistický magazín
 */

class ArticleTTS {
    constructor(articleContentSelector = '.text-editor') {
        this.contentElement = document.querySelector(articleContentSelector);
        this.utterance = null;
        this.synthesis = window.speechSynthesis;
        this.isPaused = false;
        this.currentWordIndex = 0;
        this.words = [];
        
        // Kontrola podpory prohlížeče
        if (!('speechSynthesis' in window)) {
            this.showError('Váš prohlížeč nepodporuje Text to Speech.');
            return;
        }
        
        this.initializeControls();
        this.extractArticleText();
    }
    
    extractArticleText() {
        if (!this.contentElement) {
            console.error('Článek nenalezen');
            return;
        }
        
        // Extrahování textu bez HTML tagů
        const clonedContent = this.contentElement.cloneNode(true);
        
        // Odstranění skriptů, stylů a reklam
        const unwantedElements = clonedContent.querySelectorAll('script, style, .ad-banner, .advertisement');
        unwantedElements.forEach(el => el.remove());
        
        // Získání čistého textu
        this.text = clonedContent.textContent || clonedContent.innerText || '';
        this.text = this.text.trim().replace(/\s+/g, ' ');
        
        // Rozdělení na slova pro tracking progress
        this.words = this.text.split(/\s+/);
        document.getElementById('tts-total-words').textContent = this.words.length;
    }
    
    initializeControls() {
        const playBtn = document.getElementById('tts-play-btn');
        const pauseBtn = document.getElementById('tts-pause-btn');
        const stopBtn = document.getElementById('tts-stop-btn');
        const speedSelect = document.getElementById('tts-speed');
        
        if (playBtn) {
            playBtn.addEventListener('click', () => this.play());
        }
        
        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => this.pause());
        }
        
        if (stopBtn) {
            stopBtn.addEventListener('click', () => this.stop());
        }
        
        if (speedSelect) {
            speedSelect.addEventListener('change', (e) => this.changeSpeed(e.target.value));
        }
    }
    
    play() {
        if (this.isPaused) {
            // Resume přehrávání
            this.synthesis.resume();
            this.isPaused = false;
            this.updateUIState('playing');
            return;
        }
        
        // Zastavení předchozího přehrávání
        this.synthesis.cancel();
        
        // Vytvoření nové utterance
        this.utterance = new SpeechSynthesisUtterance(this.text);
        
        // Nastavení jazyka (čeština)
        this.utterance.lang = 'cs-CZ';
        
        // Nastavení rychlosti
        const speed = parseFloat(document.getElementById('tts-speed').value);
        this.utterance.rate = speed;
        
        // Výběr českého hlasu (pokud existuje)
        const voices = this.synthesis.getVoices();
        const czechVoice = voices.find(voice => voice.lang === 'cs-CZ' || voice.lang.startsWith('cs'));
        if (czechVoice) {
            this.utterance.voice = czechVoice;
        }
        
        // Event handlers
        this.utterance.onstart = () => {
            this.updateUIState('playing');
            this.showStatus('Přehrávání...');
        };
        
        this.utterance.onend = () => {
            this.updateUIState('stopped');
            this.showStatus('Přehrávání dokončeno');
            this.currentWordIndex = 0;
            document.getElementById('tts-current-word').textContent = '0';
        };
        
        this.utterance.onerror = (event) => {
            console.error('TTS error:', event);
            this.showError('Chyba při přehrávání: ' + event.error);
            this.updateUIState('stopped');
        };
        
        // Tracking progress (přibližný - Web Speech API nemá přesný tracking)
        this.utterance.onboundary = (event) => {
            if (event.name === 'word') {
                this.currentWordIndex++;
                document.getElementById('tts-current-word').textContent = this.currentWordIndex;
            }
        };
        
        // Spuštění přehrávání
        this.synthesis.speak(this.utterance);
    }
    
    pause() {
        if (this.synthesis.speaking && !this.synthesis.paused) {
            this.synthesis.pause();
            this.isPaused = true;
            this.updateUIState('paused');
            this.showStatus('Pozastaveno');
        }
    }
    
    stop() {
        this.synthesis.cancel();
        this.isPaused = false;
        this.currentWordIndex = 0;
        document.getElementById('tts-current-word').textContent = '0';
        this.updateUIState('stopped');
        this.showStatus('Zastaveno');
    }
    
    changeSpeed(speed) {
        if (this.synthesis.speaking) {
            // Restart s novou rychlostí
            const wasPaused = this.isPaused;
            this.stop();
            if (!wasPaused) {
                this.play();
            }
        }
    }
    
    updateUIState(state) {
        const playBtn = document.getElementById('tts-play-btn');
        const pauseBtn = document.getElementById('tts-pause-btn');
        const stopBtn = document.getElementById('tts-stop-btn');
        const controls = document.querySelector('.tts-controls');
        
        switch(state) {
            case 'playing':
                playBtn.style.display = 'none';
                pauseBtn.style.display = 'inline-block';
                stopBtn.style.display = 'inline-block';
                controls.style.display = 'flex';
                break;
            case 'paused':
                playBtn.style.display = 'inline-block';
                pauseBtn.style.display = 'none';
                stopBtn.style.display = 'inline-block';
                controls.style.display = 'flex';
                break;
            case 'stopped':
                playBtn.style.display = 'inline-block';
                pauseBtn.style.display = 'none';
                stopBtn.style.display = 'none';
                controls.style.display = 'none';
                break;
        }
    }
    
    showStatus(message) {
        const statusEl = document.getElementById('tts-status');
        if (statusEl) {
            statusEl.textContent = message;
            statusEl.style.display = 'block';
        }
    }
    
    showError(message) {
        const statusEl = document.getElementById('tts-status');
        if (statusEl) {
            statusEl.textContent = '⚠️ ' + message;
            statusEl.style.display = 'block';
            statusEl.style.color = '#d9534f';
        }
    }
}

// Inicializace po načtení stránky
document.addEventListener('DOMContentLoaded', function() {
    // Pouze na stránce s článkem
    if (document.querySelector('.text-editor')) {
        window.articleTTS = new ArticleTTS();
    }
});
```

#### Krok 3: CSS styly

Vytvořit soubor `web/css/tts-player.css`:

```css
/* Text to Speech přehrávač */
.tts-player {
    margin: 20px 0;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.tts-btn {
    padding: 10px 20px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    background-color: #fff;
    color: #667eea;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.tts-btn:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.tts-btn:active {
    transform: translateY(0);
}

.tts-btn i {
    font-size: 16px;
}

.tts-controls {
    margin-top: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    color: #fff;
}

.tts-controls label {
    font-weight: 600;
    font-size: 14px;
}

.tts-speed-select {
    padding: 5px 10px;
    border: 2px solid #fff;
    border-radius: 5px;
    background-color: rgba(255, 255, 255, 0.9);
    color: #667eea;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.tts-progress {
    font-size: 14px;
    font-weight: 600;
    background-color: rgba(255, 255, 255, 0.2);
    padding: 5px 15px;
    border-radius: 20px;
}

.tts-status {
    margin-top: 10px;
    font-size: 14px;
    color: #fff;
    font-weight: 500;
    display: none;
}

/* Mobilní responzivita */
@media (max-width: 768px) {
    .tts-player {
        padding: 10px;
    }
    
    .tts-btn {
        padding: 8px 15px;
        font-size: 12px;
    }
    
    .tts-controls {
        font-size: 12px;
    }
    
    .tts-speed-select {
        font-size: 12px;
    }
}
```

#### Krok 4: Načtení JS a CSS v base.php

V `app/Views/Web/layouts/base.php` přidat do `<head>`:

```php
<!-- Text to Speech -->
<link rel="stylesheet" href="/css/tts-player.css">
```

A před `</body>`:

```php
<!-- Text to Speech -->
<script src="/js/tts-speech-api.js"></script>
```

---

### Fáze 2: Google Cloud Text-to-Speech (Produkční řešení)

#### Krok 1: Registrace Google Cloud a aktivace API

1. Přejít na [Google Cloud Console](https://console.cloud.google.com/)
2. Vytvořit nový projekt nebo vybrat existující
3. Aktivovat **Cloud Text-to-Speech API**
4. Vytvořit API klíč v **APIs & Services > Credentials**
5. Uložit API klíč do `.env` souboru

#### Krok 2: Instalace Google Cloud SDK

```bash
composer require google/cloud-text-to-speech
```

#### Krok 3: Vytvoření TTSHelper

Vytvořit soubor `app/Helpers/TTSHelper.php`:

```php
<?php

namespace App\Helpers;

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

class TTSHelper
{
    private $client;
    private $uploadPath;
    private $audioUrl;
    
    public function __construct()
    {
        // Inicializace Google Cloud TTS klienta
        $this->client = new TextToSpeechClient([
            'credentials' => $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] ?? null
        ]);
        
        $this->uploadPath = __DIR__ . '/../../web/uploads/audio/';
        $this->audioUrl = '/uploads/audio/';
        
        // Vytvoření složky pokud neexistuje
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Generování TTS audio pro článek
     * 
     * @param int $articleId ID článku
     * @param string $articleTitle Název článku
     * @param string $articleContent HTML obsah článku
     * @return array ['success' => bool, 'path' => string, 'url' => string, 'size' => int, 'error' => string]
     */
    public function generateForArticle($articleId, $articleTitle, $articleContent)
    {
        try {
            // Čištění HTML a příprava textu
            $text = $this->prepareTextFromHTML($articleContent);
            
            // Kontrola délky (max 5000 znaků pro jeden request)
            if (strlen($text) > 5000) {
                // Pro dlouhé články můžeme rozdělit nebo zkrátit
                $text = $this->splitLongText($text);
            }
            
            // Přidání úvodu
            $fullText = "Článek: " . $articleTitle . ". " . $text;
            
            // Vytvoření synthesis inputu
            $synthesisInput = new SynthesisInput();
            $synthesisInput->setText($fullText);
            
            // Nastavení hlasu (čeština, WaveNet kvalita)
            $voice = new VoiceSelectionParams();
            $voice->setLanguageCode('cs-CZ');
            $voice->setName('cs-CZ-Wavenet-A'); // Ženský hlas, můžete změnit
            
            // Konfigurace audio výstupu
            $audioConfig = new AudioConfig();
            $audioConfig->setAudioEncoding(AudioEncoding::MP3);
            $audioConfig->setSpeakingRate(1.0); // Normální rychlost
            $audioConfig->setPitch(0.0); // Normální výška hlasu
            
            // Volání Google Cloud TTS API
            $response = $this->client->synthesizeSpeech($synthesisInput, $voice, $audioConfig);
            $audioContent = $response->getAudioContent();
            
            // Uložení souboru
            $filename = 'article_' . $articleId . '_tts.mp3';
            $filePath = $this->uploadPath . $filename;
            file_put_contents($filePath, $audioContent);
            
            $fileSize = filesize($filePath);
            
            return [
                'success' => true,
                'path' => $filename,
                'url' => $this->audioUrl . $filename,
                'size' => $fileSize,
                'error' => null
            ];
            
        } catch (\Exception $e) {
            error_log('TTS Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'path' => null,
                'url' => null,
                'size' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Odstranění HTML tagů a příprava čistého textu
     */
    private function prepareTextFromHTML($html)
    {
        // Odstranění HTML tagů
        $text = strip_tags($html);
        
        // Dekódování HTML entit
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Normalizace bílých znaků
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        return $text;
    }
    
    /**
     * Rozdělení dlouhého textu na menší části
     */
    private function splitLongText($text, $maxLength = 4500)
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        // Zkrácení na první N znaků + dokončení věty
        $shortened = substr($text, 0, $maxLength);
        $lastPeriod = strrpos($shortened, '.');
        
        if ($lastPeriod !== false) {
            $shortened = substr($shortened, 0, $lastPeriod + 1);
        }
        
        return $shortened;
    }
    
    /**
     * Smazání TTS audio pro článek
     */
    public function deleteForArticle($articleId)
    {
        $filename = 'article_' . $articleId . '_tts.mp3';
        $filePath = $this->uploadPath . $filename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Kontrola existence TTS audio
     */
    public function existsForArticle($articleId)
    {
        $filename = 'article_' . $articleId . '_tts.mp3';
        $filePath = $this->uploadPath . $filename;
        
        return file_exists($filePath);
    }
    
    /**
     * Získání URL k TTS audio
     */
    public function getUrlForArticle($articleId)
    {
        if ($this->existsForArticle($articleId)) {
            return $this->audioUrl . 'article_' . $articleId . '_tts.mp3';
        }
        
        return null;
    }
}
```

#### Krok 4: Rozšíření databáze

Vytvořit SQL migrační soubor `config/add_tts_columns.sql`:

```sql
-- Přidání sloupců pro Text to Speech do tabulky clanky

ALTER TABLE `clanky` 
ADD COLUMN `audio_tts_generated` TINYINT(1) DEFAULT 0 COMMENT 'Zda bylo TTS audio vygenerováno',
ADD COLUMN `audio_tts_path` VARCHAR(255) DEFAULT NULL COMMENT 'Cesta k TTS audio souboru',
ADD COLUMN `audio_tts_size` INT DEFAULT 0 COMMENT 'Velikost TTS audio v bytech';

-- Index pro rychlé vyhledávání článků s TTS
CREATE INDEX idx_audio_tts_generated ON `clanky` (`audio_tts_generated`);
```

#### Krok 5: Integrace do ArticleAdminController

V `app/Controllers/Admin/ArticleAdminController.php` upravit metodu `store()`:

```php
// Po uložení článku
if ($articleId) {
    // ... existující kód ...
    
    // Generování TTS audio (volitelné, můžete přidat checkbox v admin formuláři)
    if (isset($postData['generate_tts']) && $postData['generate_tts'] === '1') {
        $ttsHelper = new \App\Helpers\TTSHelper();
        $result = $ttsHelper->generateForArticle(
            $articleId,
            $postData['nazev'],
            $postData['obsah']
        );
        
        if ($result['success']) {
            // Uložení informací do databáze
            $stmt = $this->db->prepare("
                UPDATE clanky 
                SET audio_tts_generated = 1,
                    audio_tts_path = :path,
                    audio_tts_size = :size
                WHERE id = :id
            ");
            $stmt->execute([
                'path' => $result['path'],
                'size' => $result['size'],
                'id' => $articleId
            ]);
        }
    }
    
    // ... zbytek kódu ...
}
```

#### Krok 6: Zobrazení v ArticleController

V `app/Controllers/Web/ArticleController.php` upravit metodu `articleDetail()`:

```php
// Po získání článku
$article = $this->articleModel->getByUrl($url);

// ... existující kód ...

// Kontrola TTS audio
$audioTtsUrl = null;
if ($article['audio_tts_generated'] == 1 && !empty($article['audio_tts_path'])) {
    $audioTtsUrl = '/uploads/audio/' . $article['audio_tts_path'];
}

// Předání do view
$view = '../app/Views/Web/articles/article.php';
require '../app/Views/Web/layouts/base.php';
```

#### Krok 7: Aktualizace article.php view

V `app/Views/Web/articles/article.php` nahradit existující audio přehrávač (řádky 39-47):

```php
<?php if (isset($audioUrl) && $audioUrl): ?>
    <!-- Původní audio nahrávka -->
    <div class="prehravac">
        <h4><i class="fa-solid fa-microphone"></i> Audio nahrávka</h4>
        <audio controls>
            <source src='<?php echo $audioUrl; ?>' type='audio/mpeg'>
            Váš prohlížeč nepodporuje prvek audio.
        </audio>
    </div>
<?php endif; ?>

<?php if (isset($audioTtsUrl) && $audioTtsUrl): ?>
    <!-- TTS automaticky vygenerované audio -->
    <div class="prehravac tts-audio">
        <h4><i class="fa-solid fa-robot"></i> Poslechnout článek (AI hlas)</h4>
        <audio controls>
            <source src='<?php echo $audioTtsUrl; ?>' type='audio/mpeg'>
            Váš prohlížeč nepodporuje prvek audio.
        </audio>
    </div>
<?php endif; ?>
```

---

## Možná vylepšení

### 1. Automatické generování TTS na pozadí
- Použití cron jobu pro generování TTS u nových článků
- Queue systém (např. Redis) pro asynchronní zpracování

### 2. Možnost výběru hlasu v admin panelu
- Dropdown pro výběr různých českých hlasů
- Preview hlasu před generováním

### 3. Cachování a CDN
- Nahrávání TTS audio na CDN (Cloudflare, AWS CloudFront)
- Rychlejší načítání a nižší zátěž serveru

### 4. Statistiky a tracking
- Sledování, kolikrát bylo TTS přehráno
- Analýza oblíbenosti funkce

### 5. Možnost stažení offline
- Tlačítko "Stáhnout audio" pro offline poslech
- Export do podcast feedu (RSS)

### 6. Přizpůsobení TTS
- Uživatelská nastavení rychlosti a výšky hlasu
- Uložení preferencí do cookies/localStorage

---

## Odhad nákladů (Google Cloud TTS)

### Příklad kalkulace:
- Průměrný článek: 3000 znaků
- 100 článků měsíčně: 300,000 znaků
- Cena WaveNet: $16 / 1M znaků = **$4.80 za měsíc**

### První měsíc:
- 1M znaků Standard TTS zdarma
- Cca 330 článků zdarma

### Tip pro snížení nákladů:
- Používat Standard voices místo WaveNet ($4 vs $16)
- Generovat TTS pouze pro vybrané články (např. propagované)
- Cachovat audio soubory navždy (generovat pouze jednou)

---

## Testování

### Kontrolní seznam:
- [ ] TTS funguje v různých prohlížečích (Chrome, Firefox, Safari, Edge)
- [ ] Audio se správně načítá a přehrává
- [ ] Ovládací prvky fungují (play, pause, stop, speed)
- [ ] Mobilní responzivita
- [ ] Kvalita českého hlasu je akceptovatelná
- [ ] TTS nefunguje na článcích bez obsahu
- [ ] Chybové stavy jsou správně zobrazeny
- [ ] Cache funguje správně (audio se negeneruje opakovaně)

---

## Bezpečnost

### Opatření:
1. **API klíče** - ukládat v `.env`, nikdy v kódu
2. **Rate limiting** - omezit počet TTS requestů na IP/uživatele
3. **Validace** - kontrolovat délku textu před odesláním do API
4. **Přístup k souborům** - zabezpečit upload složku (.htaccess)
5. **GDPR** - informovat uživatele o použití Google služeb

---

## Závěr

Text to Speech je skvělé vylepšení pro dostupnost a uživatelský komfort. Doporučujeme začít s Web Speech API pro rychlé testování a poté přejít na Google Cloud TTS pro produkční nasazení s vysokou kvalitou.

**Doporučený postup:**
1. Implementovat Web Speech API (Fáze 1) - 2-4 hodiny práce
2. Testovat s uživateli a získat feedback
3. Pokud je zájem, implementovat Google Cloud TTS (Fáze 2) - 4-8 hodin práce
4. Průběžně optimalizovat a přidávat vylepšení

**Kontakt pro pomoc:**
- Google Cloud TTS dokumentace: https://cloud.google.com/text-to-speech/docs
- Web Speech API dokumentace: https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API

