# Automatizace na nové vydání - AI generování článků

## Přehled funkcionality

Automatizace vydání umožní efektivně vytvářet obsah prostřednictvím video rozhovorů s autory, závodníky a osobnostmi cyklistické scény. Systém automaticky přepíše video do textu, vygeneruje článek pomocí AI a publikuje ho na web včetně sdílení na sociální sítě.

### Hlavní vlastnosti:
- **Video chat integrace** - online rozhovory s možností nahrávání
- **Automatická transkripce** - převod video/audio na text
- **AI generování článků** - vytvoření kvalitního článku z transkriptu
- **Automatické publikování** - publikace článku na web
- **Social media integrace** - automatické sdílení na Facebook, Instagram, Twitter/X
- **Editační workflow** - možnost kontroly a úpravy před publikací

---

## Případy použití

### Use Case 1: Rozhovor se závodníkem po závodě
1. Redaktor vede video rozhovor se závodníkem
2. Video se automaticky nahrává a ukládá
3. Systém přepíše rozhovor do textu
4. AI vygeneruje článek ve stylu interview
5. Redaktor zkontroluje a případně upraví článek
6. Článek se publikuje včetně odkazů na sociální sítě

### Use Case 2: Týdenní shrnutí událostí
1. Redakční tým diskutuje o událostech týdne (video call)
2. Systém nahrává a transkribuje diskuzi
3. AI vytvoří souhrnný článek s hlavními body
4. Automatické publikování každé pondělí

### Use Case 3: Quick News z tiskových konferencí
1. Video z tiskové konference se nahraje
2. Automatická transkripce do češtiny
3. AI vygeneruje krátký news článek
4. Rychlé publikování pro aktuální zpravodajství

---

## Architektura řešení

```
┌─────────────────────────────────────────────────────────────────┐
│                        VIDEO INTERVIEW                          │
│         (Zoom, Google Meet, nebo vlastní řešení)                │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    VIDEO/AUDIO SOUBOR                           │
│              (MP4, MP3, WAV, uloženo na serveru)                │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                  SPEECH-TO-TEXT SLUŽBA                          │
│     (OpenAI Whisper, Google Speech-to-Text, Azure Speech)       │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                        TRANSKRIPT                               │
│              (Čistý text rozhovoru v češtině)                   │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    AI MODEL PRO ČLÁNEK                          │
│          (OpenAI GPT-4, Claude 3.5, Gemini Pro)                 │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                     VYGENEROVANÝ ČLÁNEK                         │
│        (HTML formát, připravený k editaci v TinyMCE)            │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                    EDITAČNÍ WORKFLOW                            │
│       (Redaktor zkontroluje, upraví, přidá obrázky)            │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│                  PUBLIKOVÁNÍ NA WEB                             │
│           (Automatický insert do databáze clanky)               │
└───────────────────────┬─────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────┐
│               SDÍLENÍ NA SOCIÁLNÍ SÍTĚ                          │
│      (Facebook, Instagram, Twitter/X, LinkedIn, Strava)         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Možnosti implementace

### 1. Video Chat platformy

#### A) Integrace s existujícími platformami

**Zoom API**
- Výhody: Robustní, známá platforma, automatické nahrávání
- Nevýhody: Platná licence (cca $150/rok), závislost na třetí straně
- Cena: Zoom Pro (~$150/rok/host)

**Google Meet API**
- Výhody: Integrace s Google Workspace, zdarma pro účty
- Nevýhody: Omezená kontrola nad nahráváním
- Cena: Zdarma (s Google Workspace účtem)

**Microsoft Teams**
- Výhody: Pokud již máte M365 licenci
- Nevýhody: Složitější API

#### B) Vlastní řešení

**WebRTC (Jitsi, Daily.co)**
- Výhody: Plná kontrola, možnost embedovat přímo do admin panelu
- Nevýhody: Nutná implementace, hosting infrastruktury
- Doporučení: Daily.co (managed WebRTC služba)
  - Cena: Zdarma do 10 účastníků, pak $0.06/minut
  - Jednoduché API, automatické nahrávání

---

### 2. Speech-to-Text (Transkripce)

#### A) OpenAI Whisper API (Doporučeno)

**Výhody:**
- Vynikající kvalita transkripce v češtině
- Automatické rozpoznání jazyka
- Časové značky (timestamps)
- Punktuace a formátování
- Velmi dobrá cena

**Nevýhody:**
- Limit 25 MB na soubor (nutné dělit dlouhá videa)
- Maximální délka 30 minut per request

**Cena:**
- $0.006 za minutu audio
- 1 hodina rozhovoru = $0.36

**Příklad kódu:**
```python
import openai

openai.api_key = "your-api-key"

audio_file = open("interview.mp3", "rb")
transcript = openai.Audio.transcribe(
    model="whisper-1",
    file=audio_file,
    language="cs"
)

print(transcript.text)
```

#### B) Google Cloud Speech-to-Text

**Výhody:**
- Velmi dobrá kvalita v češtině
- Podpora dlouhých audio souborů
- Real-time transkripce možná

**Nevýhody:**
- Dražší než Whisper
- Složitější API

**Cena:**
- Standard: $0.024 za minutu ($1.44/hodinu)
- Enhanced: $0.09 za minutu ($5.40/hodinu)

#### C) Azure Speech Services

**Výhody:**
- Kvalitní česká transkripce
- Možnost custom modelů

**Cena:**
- Standard: $1 za hodinu
- Custom models: $1.40 za hodinu

#### D) AssemblyAI

**Výhody:**
- Moderní API, speaker diarization (rozlišení mluvčích)
- Automatické summary, sentiment analysis

**Nevýhody:**
- Slabší podpora češtiny

**Cena:**
- $0.00025 za sekundu ($0.90/hodina)

---

### 3. AI generování článků

#### A) OpenAI GPT-4 (Doporučeno pro kvalitu)

**Výhody:**
- Nejlepší kvalita generování v češtině
- Skvělé pochopení kontextu
- Konzistentní styl psaní

**Nevýhody:**
- Nejdražší varianta
- Rate limiting

**Cena:**
- GPT-4: $30 za 1M input tokens, $60 za 1M output tokens
- GPT-4-turbo: $10/$30
- Typický článek: cca $0.50-$2.00

**Příklad promptu:**
```
Z následujícího transkriptu video rozhovoru vytvořte kvalitní článek pro cyklistický magazín.

TRANSKRIPT:
[transkript zde]

POŽADAVKY:
- Formát: HTML s <p>, <h2>, <h3> tagy
- Styl: Profesionální, ale přístupný
- Délka: 800-1200 slov
- Struktura: Úvod, hlavní body, závěr
- Zachovat důležité citace přesně
- Přidat meta description (150-160 znaků)
- Navrhnout 3-5 klíčových slov

KONTEXT:
- Web: Cyklistický magazín (www.cyklistickey.cz)
- Audience: Cyklisté, běžci, fanoušci sportu
- Tón: Informativní, nadšený pro sport
```

#### B) Claude 3.5 Sonnet (Doporučeno pro cenu/výkon)

**Výhody:**
- Vynikající kvalita v češtině
- Lepší cena než GPT-4
- 200k context window (lepší pro dlouhé rozhovory)

**Nevýhody:**
- Menší známost než GPT-4

**Cena:**
- $3 za 1M input tokens, $15 za 1M output tokens
- Typický článek: cca $0.15-$0.60

#### C) Google Gemini Pro

**Výhody:**
- Dobrá cena
- Integrace s Google službami

**Nevýhody:**
- Menší kvalita v češtině než GPT-4/Claude

**Cena:**
- Gemini 1.5 Pro: $3.50/$10.50 za 1M tokens
- Gemini 1.5 Flash: $0.35/$1.05 (nejlevnější)

#### D) Open-source lokální modely

**Mistral, LLaMA 3, Czech GPT**
- Výhody: Zdarma, soukromí
- Nevýhody: Nižší kvalita, vyžaduje GPU server
- Doporučení: Pouze pro testování nebo low-budget projekty

---

### 4. Social Media integrace

#### A) Meta (Facebook + Instagram)

**Facebook Graph API**
- Publikování postů na Facebook stránku
- Automatické sdílení odkazu na článek
- Možnost naplánovat publikování

**Instagram Graph API**
- Sdílení obrázků a odkazů (stories)
- Omezení: nelze přímo publikovat feed posty (pouze stories)

**Implementace:**
```php
// Facebook post
$fb = new Facebook\Facebook([
  'app_id' => '{app-id}',
  'app_secret' => '{app-secret}',
  'default_access_token' => '{access-token}',
]);

$linkData = [
  'link' => 'https://cyklistickey.cz/article/novy-clanek',
  'message' => 'Nový článek na našem webu! 🚴‍♂️',
];

$fb->post('/me/feed', $linkData);
```

#### B) Twitter/X API

**Výhody:**
- Rychlé sdílení novinek
- Dobrý reach pro sportovní komunitu

**Implementace:**
- Twitter API v2
- Automatické tweety s odkazem na článek
- Možnost thread pro delší obsah

**Cena:**
- Free tier: 1500 tweetů/měsíc
- Basic: $100/měsíc - 3000 tweetů

#### C) LinkedIn API

**Výhody:**
- Profesionální síť
- Vhodné pro delší formy obsahu

#### D) Strava API (specifické pro cyklistiku!)

**Výhody:**
- Přímý kontakt s cyklistickou komunitou
- Možnost sdílet aktivity a články

---

## Doporučené řešení

### Fáze 1: MVP (Minimum Viable Product)

**Stack:**
1. Video chat: **Daily.co** (nejjednodušší integrace)
2. Transkripce: **OpenAI Whisper API** (nejlepší kvalita/cena pro češtinu)
3. AI článek: **Claude 3.5 Sonnet** (nejlepší poměr cena/kvalita)
4. Social media: **Meta Graph API** (Facebook + Instagram)

**Odhad nákladů:**
- Daily.co: $0 (free tier)
- Whisper: ~$2/měsíc (5-6 rozhovorů)
- Claude: ~$5/měsíc (10 článků)
- **Celkem: ~$7/měsíc**

---

## Postup implementace

### KROK 1: Databázové rozšíření

Vytvořit tabulku `ai_content_pipeline` pro tracking automatizace:

```sql
CREATE TABLE `ai_content_pipeline` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `status` ENUM('uploaded', 'transcribing', 'transcribed', 'generating', 'generated', 'reviewing', 'published', 'failed') DEFAULT 'uploaded',
  `video_path` VARCHAR(500) DEFAULT NULL,
  `video_duration` INT DEFAULT 0 COMMENT 'Délka v sekundách',
  `transcript_text` LONGTEXT DEFAULT NULL,
  `transcript_cost` DECIMAL(10,4) DEFAULT 0,
  `ai_article_html` LONGTEXT DEFAULT NULL,
  `ai_article_cost` DECIMAL(10,4) DEFAULT 0,
  `article_id` INT DEFAULT NULL COMMENT 'ID publikovaného článku',
  `user_id` INT NOT NULL COMMENT 'ID redaktora',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `transcribed_at` TIMESTAMP NULL,
  `generated_at` TIMESTAMP NULL,
  `published_at` TIMESTAMP NULL,
  `error_message` TEXT DEFAULT NULL,
  FOREIGN KEY (`article_id`) REFERENCES `clanky`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Vytvořit tabulku pro social media posty:

```sql
CREATE TABLE `social_media_posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `article_id` INT NOT NULL,
  `platform` ENUM('facebook', 'instagram', 'twitter', 'linkedin', 'strava') NOT NULL,
  `post_id` VARCHAR(255) DEFAULT NULL COMMENT 'ID postu na platformě',
  `post_url` VARCHAR(500) DEFAULT NULL,
  `status` ENUM('pending', 'scheduled', 'published', 'failed') DEFAULT 'pending',
  `scheduled_at` TIMESTAMP NULL,
  `published_at` TIMESTAMP NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`article_id`) REFERENCES `clanky`(`id`) ON DELETE CASCADE,
  INDEX idx_article_platform (`article_id`, `platform`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### KROK 2: Vytvoření AIContentHelper

Soubor `app/Helpers/AIContentHelper.php`:

```php
<?php

namespace App\Helpers;

use Anthropic\Anthropic;

class AIContentHelper
{
    private $anthropicApiKey;
    private $openaiApiKey;
    
    public function __construct()
    {
        $this->anthropicApiKey = $_ENV['ANTHROPIC_API_KEY'] ?? null;
        $this->openaiApiKey = $_ENV['OPENAI_API_KEY'] ?? null;
    }
    
    /**
     * Transkripce audio/video pomocí OpenAI Whisper
     * 
     * @param string $filePath Cesta k audio/video souboru
     * @return array ['success' => bool, 'text' => string, 'cost' => float, 'error' => string]
     */
    public function transcribeAudio($filePath)
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'text' => '', 'cost' => 0, 'error' => 'Soubor nenalezen'];
        }
        
        try {
            $curl = curl_init();
            
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.openai.com/v1/audio/transcriptions",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer " . $this->openaiApiKey,
                ],
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($filePath),
                    'model' => 'whisper-1',
                    'language' => 'cs',
                    'response_format' => 'json'
                ]
            ]);
            
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            
            if ($httpCode !== 200) {
                return ['success' => false, 'text' => '', 'cost' => 0, 'error' => 'API error: ' . $httpCode];
            }
            
            $data = json_decode($response, true);
            
            // Výpočet ceny ($0.006 za minutu)
            $durationMinutes = $this->getAudioDuration($filePath) / 60;
            $cost = $durationMinutes * 0.006;
            
            return [
                'success' => true,
                'text' => $data['text'] ?? '',
                'cost' => round($cost, 4),
                'error' => null
            ];
            
        } catch (\Exception $e) {
            error_log('Transcription error: ' . $e->getMessage());
            return ['success' => false, 'text' => '', 'cost' => 0, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Generování článku z transkriptu pomocí Claude
     * 
     * @param string $transcript Text transkriptu
     * @param array $metadata Metadata (název, kategorie, atd.)
     * @return array ['success' => bool, 'html' => string, 'title' => string, 'description' => string, 'keywords' => array, 'cost' => float, 'error' => string]
     */
    public function generateArticleFromTranscript($transcript, $metadata = [])
    {
        try {
            $client = Anthropic::client($this->anthropicApiKey);
            
            $prompt = $this->buildArticlePrompt($transcript, $metadata);
            
            $response = $client->messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 4096,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]);
            
            $content = $response->content[0]->text;
            
            // Parsování odpovědi (očekává JSON formát)
            $articleData = json_decode($content, true);
            
            // Výpočet ceny
            $inputTokens = $response->usage->input_tokens;
            $outputTokens = $response->usage->output_tokens;
            $cost = ($inputTokens / 1000000 * 3) + ($outputTokens / 1000000 * 15);
            
            return [
                'success' => true,
                'html' => $articleData['content'] ?? '',
                'title' => $articleData['title'] ?? 'Bez názvu',
                'description' => $articleData['description'] ?? '',
                'keywords' => $articleData['keywords'] ?? [],
                'cost' => round($cost, 4),
                'error' => null
            ];
            
        } catch (\Exception $e) {
            error_log('Article generation error: ' . $e->getMessage());
            return [
                'success' => false,
                'html' => '',
                'title' => '',
                'description' => '',
                'keywords' => [],
                'cost' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Vytvoření promptu pro generování článku
     */
    private function buildArticlePrompt($transcript, $metadata)
    {
        $interviewType = $metadata['type'] ?? 'rozhovor';
        $category = $metadata['category'] ?? 'Aktuality';
        
        return <<<PROMPT
Z následujícího transkriptu video rozhovoru vytvořte kvalitní článek pro cyklistický magazín Cyklistický.

TRANSKRIPT:
$transcript

POŽADAVKY:
- Typ: $interviewType
- Kategorie: $category
- Formát výstupu: JSON
- Jazyk: Čeština

JSON STRUKTURA:
{
  "title": "Catchy a SEO-friendly název článku",
  "description": "Meta description (150-160 znaků)",
  "keywords": ["klíčové", "slovo1", "slovo2", ...],
  "content": "HTML obsah článku s <p>, <h2>, <h3>, <strong>, <em> tagy"
}

STYL PSANÍ:
- Profesionální, ale přístupný
- Zachovat důležité citace v uvozovkách
- Přidat emotivní prvky (nadšení pro sport)
- Optimalizováno pro SEO
- Délka: 800-1200 slov

HTML STRUKTURA:
<p>Úvodní odstavec...</p>
<h2>První hlavní bod</h2>
<p>Obsah...</p>
<h2>Druhý hlavní bod</h2>
<p>Obsah...</p>
<h2>Závěr</h2>
<p>Závěrečný odstavec...</p>

DŮLEŽITÉ:
- Vraťte pouze validní JSON, žádný další text
- Kontrola gramatiky a pravopisu
- Zachovat faktickou přesnost
PROMPT;
    }
    
    /**
     * Získání délky audio/video souboru v sekundách
     */
    private function getAudioDuration($filePath)
    {
        // Použití ffprobe (ffmpeg nástroj)
        $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
        $duration = shell_exec($cmd);
        return (int)trim($duration);
    }
}
```

---

### KROK 3: Vytvoření SocialMediaHelper

Soubor `app/Helpers/SocialMediaHelper.php`:

```php
<?php

namespace App\Helpers;

class SocialMediaHelper
{
    private $facebookAccessToken;
    private $facebookPageId;
    private $twitterApiKey;
    private $twitterApiSecret;
    
    public function __construct()
    {
        $this->facebookAccessToken = $_ENV['FACEBOOK_ACCESS_TOKEN'] ?? null;
        $this->facebookPageId = $_ENV['FACEBOOK_PAGE_ID'] ?? null;
        $this->twitterApiKey = $_ENV['TWITTER_API_KEY'] ?? null;
        $this->twitterApiSecret = $_ENV['TWITTER_API_SECRET'] ?? null;
    }
    
    /**
     * Publikování článku na Facebook
     * 
     * @param array $article Data článku
     * @return array ['success' => bool, 'post_id' => string, 'post_url' => string, 'error' => string]
     */
    public function publishToFacebook($article)
    {
        try {
            $message = $this->createFacebookMessage($article);
            $link = 'https://www.cyklistickey.cz/article/' . $article['url'] . '/';
            
            $postData = [
                'message' => $message,
                'link' => $link,
                'access_token' => $this->facebookAccessToken
            ];
            
            $url = "https://graph.facebook.com/v18.0/{$this->facebookPageId}/feed";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return ['success' => false, 'post_id' => null, 'post_url' => null, 'error' => 'HTTP ' . $httpCode];
            }
            
            $data = json_decode($response, true);
            $postId = $data['id'] ?? null;
            $postUrl = "https://www.facebook.com/{$postId}";
            
            return [
                'success' => true,
                'post_id' => $postId,
                'post_url' => $postUrl,
                'error' => null
            ];
            
        } catch (\Exception $e) {
            error_log('Facebook publish error: ' . $e->getMessage());
            return ['success' => false, 'post_id' => null, 'post_url' => null, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Vytvoření Facebook message textu
     */
    private function createFacebookMessage($article)
    {
        $title = $article['nazev'];
        $excerpt = $this->extractExcerpt($article['obsah'], 150);
        
        return "🚴‍♂️ {$title}\n\n{$excerpt}\n\n👉 Celý článek na našem webu:";
    }
    
    /**
     * Publikování na Twitter/X
     */
    public function publishToTwitter($article)
    {
        // Implementace Twitter API v2
        // Podobný princip jako Facebook
        // Tweet max 280 znaků
        
        try {
            $message = $this->createTwitterMessage($article);
            $link = 'https://www.cyklistickey.cz/article/' . $article['url'] . '/';
            
            // Twitter API v2 implementace...
            
            return [
                'success' => true,
                'post_id' => 'tweet_id',
                'post_url' => 'https://twitter.com/...',
                'error' => null
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'post_id' => null, 'post_url' => null, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Vytvoření Twitter message (max 280 znaků)
     */
    private function createTwitterMessage($article)
    {
        $title = $article['nazev'];
        $link = 'https://www.cyklistickey.cz/article/' . $article['url'] . '/';
        
        $maxLength = 280 - strlen($link) - 5; // 5 = emoji + spacing
        
        if (strlen($title) > $maxLength) {
            $title = substr($title, 0, $maxLength - 3) . '...';
        }
        
        return "🚴‍♂️ {$title}\n\n{$link}";
    }
    
    /**
     * Extrakce excerpta z HTML obsahu
     */
    private function extractExcerpt($html, $maxLength = 150)
    {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', trim($text));
        
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength);
            $lastSpace = strrpos($text, ' ');
            if ($lastSpace !== false) {
                $text = substr($text, 0, $lastSpace);
            }
            $text .= '...';
        }
        
        return $text;
    }
}
```

---

### KROK 4: Admin Controller pro AI Pipeline

Soubor `app/Controllers/Admin/AIContentAdminController.php`:

```php
<?php

namespace App\Controllers\Admin;

use App\Helpers\AIContentHelper;
use App\Helpers\SocialMediaHelper;
use App\Models\Article;

class AIContentAdminController
{
    private $db;
    private $aiHelper;
    private $socialHelper;
    private $articleModel;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->aiHelper = new AIContentHelper();
        $this->socialHelper = new SocialMediaHelper();
        $this->articleModel = new Article($db);
    }
    
    /**
     * Zobrazení seznamu AI pipeline
     */
    public function index()
    {
        $stmt = $this->db->query("
            SELECT p.*, u.name, u.surname 
            FROM ai_content_pipeline p
            LEFT JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");
        $pipelines = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $view = '../app/Views/Admin/ai-content/index.php';
        require '../app/Views/Admin/layout/layout.php';
    }
    
    /**
     * Upload videa
     */
    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $file = $_FILES['video'] ?? null;
            
            if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Chyba při nahrávání souboru.';
                header('Location: /admin/ai-content/');
                exit;
            }
            
            // Uložení souboru
            $uploadDir = __DIR__ . '/../../../web/uploads/ai-videos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $filename = uniqid('video_') . '_' . basename($file['name']);
            $filepath = $uploadDir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                $_SESSION['error'] = 'Nepodařilo se uložit soubor.';
                header('Location: /admin/ai-content/');
                exit;
            }
            
            // Vytvoření záznamu v DB
            $stmt = $this->db->prepare("
                INSERT INTO ai_content_pipeline (status, video_path, user_id)
                VALUES ('uploaded', :path, :user_id)
            ");
            $stmt->execute([
                'path' => $filename,
                'user_id' => $userId
            ]);
            
            $pipelineId = $this->db->lastInsertId();
            
            $_SESSION['success'] = 'Video nahráno. Můžete spustit transkripci.';
            header('Location: /admin/ai-content/detail/' . $pipelineId);
            exit;
        }
        
        $view = '../app/Views/Admin/ai-content/upload.php';
        require '../app/Views/Admin/layout/layout.php';
    }
    
    /**
     * Spuštění transkripce
     */
    public function transcribe($pipelineId)
    {
        $stmt = $this->db->prepare("SELECT * FROM ai_content_pipeline WHERE id = :id");
        $stmt->execute(['id' => $pipelineId]);
        $pipeline = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$pipeline) {
            $_SESSION['error'] = 'Pipeline nenalezen.';
            header('Location: /admin/ai-content/');
            exit;
        }
        
        // Update status
        $this->db->prepare("UPDATE ai_content_pipeline SET status = 'transcribing' WHERE id = :id")
            ->execute(['id' => $pipelineId]);
        
        // Transkripce
        $videoPath = __DIR__ . '/../../../web/uploads/ai-videos/' . $pipeline['video_path'];
        $result = $this->aiHelper->transcribeAudio($videoPath);
        
        if ($result['success']) {
            // Uložení transkriptu
            $stmt = $this->db->prepare("
                UPDATE ai_content_pipeline 
                SET status = 'transcribed', 
                    transcript_text = :text, 
                    transcript_cost = :cost,
                    transcribed_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'text' => $result['text'],
                'cost' => $result['cost'],
                'id' => $pipelineId
            ]);
            
            $_SESSION['success'] = 'Transkripce dokončena. Náklady: $' . $result['cost'];
        } else {
            $this->db->prepare("UPDATE ai_content_pipeline SET status = 'failed', error_message = :error WHERE id = :id")
                ->execute(['error' => $result['error'], 'id' => $pipelineId]);
            
            $_SESSION['error'] = 'Chyba transkripce: ' . $result['error'];
        }
        
        header('Location: /admin/ai-content/detail/' . $pipelineId);
        exit;
    }
    
    /**
     * Generování článku z transkriptu
     */
    public function generate($pipelineId)
    {
        $stmt = $this->db->prepare("SELECT * FROM ai_content_pipeline WHERE id = :id");
        $stmt->execute(['id' => $pipelineId]);
        $pipeline = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$pipeline || empty($pipeline['transcript_text'])) {
            $_SESSION['error'] = 'Transkript není k dispozici.';
            header('Location: /admin/ai-content/detail/' . $pipelineId);
            exit;
        }
        
        // Update status
        $this->db->prepare("UPDATE ai_content_pipeline SET status = 'generating' WHERE id = :id")
            ->execute(['id' => $pipelineId]);
        
        // Generování článku
        $metadata = [
            'type' => $_POST['type'] ?? 'rozhovor',
            'category' => $_POST['category'] ?? 'Aktuality'
        ];
        
        $result = $this->aiHelper->generateArticleFromTranscript($pipeline['transcript_text'], $metadata);
        
        if ($result['success']) {
            // Uložení vygenerovaného článku
            $stmt = $this->db->prepare("
                UPDATE ai_content_pipeline 
                SET status = 'generated', 
                    ai_article_html = :html, 
                    ai_article_cost = :cost,
                    generated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                'html' => $result['html'],
                'cost' => $result['cost'],
                'id' => $pipelineId
            ]);
            
            $_SESSION['success'] = 'Článek vygenerován. Náklady: $' . $result['cost'];
        } else {
            $this->db->prepare("UPDATE ai_content_pipeline SET status = 'failed', error_message = :error WHERE id = :id")
                ->execute(['error' => $result['error'], 'id' => $pipelineId]);
            
            $_SESSION['error'] = 'Chyba generování: ' . $result['error'];
        }
        
        header('Location: /admin/ai-content/detail/' . $pipelineId);
        exit;
    }
    
    /**
     * Publikování článku (po editaci)
     */
    public function publish($pipelineId)
    {
        // Zde logika pro publikování článku
        // Vytvoření záznamu v tabulce clanky
        // Automatické sdílení na social media
        
        // TODO: implementace
    }
}
```

---

## Možná vylepšení

### 1. Real-time transkripce
- Live transkripce během video hovoru
- Okamžité zobrazení textu redaktorovi

### 2. Multi-jazyková podpora
- Automatický překlad článků do angličtiny/němčiny
- Rozšíření audience

### 3. Automatické výběr obrázků
- AI generování thumbnailů (DALL-E, Midjourney)
- Nebo výběr z stock photos podle klíčových slov

### 4. Kalendář publikování
- Naplánování článků na konkrétní dny/časy
- Automatické publikování bez manuálního zásahu

### 5. A/B testování nadpisů
- Generování více variant nadpisu
- Automatický výběr podle CTR

### 6. Analytics integrace
- Tracking úspěšnosti AI-generovaných článků
- Optimalizace promptů podle výkonu

---

## Odhad nákladů

### Měsíční náklady (10 článků):
- **Video hosting** (Daily.co): $0 (free tier)
- **Transkripce** (Whisper): $2 (10 × 30min rozhovorů)
- **AI generování** (Claude): $5 (10 článků)
- **Social media API**: $0 (Facebook/Instagram free, Twitter $100/měsíc pro paid tier)
- **Celkem: ~$7/měsíc** (nebo $107 s Twitter paid tier)

### ROI (Return on Investment):
- **Ušetřený čas**: 5-10 hodin/měsíc (psaní článků)
- **Hodnota času**: cca $50-100/hodina = **$250-1000 ušetřených nákladů**
- **ROI: 3500-14000%** 🚀

---

## Bezpečnost a GDPR

### Opatření:
1. **Souhlas účastníků** - informovat o nahrávání video hovorů
2. **Šifrování** - ukládání video/audio souborů šifrovaně
3. **API klíče** - bezpečné uložení v `.env`, nikdy v kódu
4. **Data retention** - automatické mazání starých video souborů (30-90 dní)
5. **Access control** - pouze admin má přístup k AI pipeline
6. **Audit log** - zaznamenávání všech operací (kdo, kdy, co)

---

## Testování

### Kontrolní seznam:
- [ ] Video upload funguje správně (podporované formáty: MP4, MOV, AVI)
- [ ] Transkripce produkuje kvalitní český text
- [ ] AI generovaný článek je čitelný a obsahově správný
- [ ] Editační workflow umožňuje úpravy před publikací
- [ ] Publikování článku vytvoří záznam v DB správně
- [ ] Social media sdílení funguje na všech platformách
- [ ] Chybové stavy jsou správně zachyceny a zobrazeny
- [ ] Nákladový tracking je přesný

---

## Závěr

Automatizace vydání prostřednictvím AI je mocný nástroj pro efektivnější tvorbu obsahu. Kombinace video rozhovorů, automatické transkripce a AI generování článků může dramaticky snížit čas potřebný na vytvoření kvalitního obsahu.

**Doporučený postup:**
1. Implementovat MVP s Daily.co + Whisper + Claude (Krok 1-4) - 20-30 hodin práce
2. Testovat s 2-3 články a získat feedback od redakce
3. Iterativně vylepšovat AI prompty pro lepší kvalitu výstupu
4. Přidat social media automatizaci (Krok 5) - 10-15 hodin práce
5. Průběžně optimalizovat workflow podle zpětné vazby

**Tipy pro úspěch:**
- Začít s jednoduchým use case (krátké rozhovory, 10-15 min)
- AI generovaný článek vždy zkontrolovat redaktorem před publikací
- Průběžně ukládat a analyzovat úspěšné prompty
- Měřit kvalitu výstupu a ROI

**Kontakt pro pomoc:**
- OpenAI Whisper dokumentace: https://platform.openai.com/docs/guides/speech-to-text
- Anthropic Claude dokumentace: https://docs.anthropic.com/
- Daily.co dokumentace: https://docs.daily.co/
- Meta Graph API dokumentace: https://developers.facebook.com/docs/graph-api/

