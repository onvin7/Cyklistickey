# Click Tracking - Detailní implementace

## Přehled
Implementován kompletní systém pro tracking kliků na odkazy v článcích s detailními metrikami.

## Sledované metriky

### 1. Základní informace
- ✅ Počet kliků na odkaz (agregované)
- ✅ URL odkazu
- ✅ Text odkazu
- ✅ ID článku
- ✅ První a poslední klik

### 2. Detailní informace o každém kliku
- ✅ **Přesný čas kliku** (`clicked_at`)
- ✅ **IP adresa** (`ip_address`) - s podporou proxy
- ✅ **User Agent** (`user_agent`) - kompletní string
- ✅ **Referrer** (`referrer`) - odkud přišel uživatel
- ✅ **Session ID** (`session_id`) - pro tracking unikátních uživatelů
- ✅ **Typ zařízení** (`device_type`) - desktop/mobile/tablet/bot/unknown
- ✅ **Prohlížeč** (`browser`) - Chrome, Firefox, Safari, Edge, atd.
- ✅ **Operační systém** (`os`) - Windows, macOS, iOS, Android, Linux
- ✅ **Geolokace** (`country`, `city`) - země a město z IP adresy
- ✅ **Čas na stránce** (`time_on_page`) - v sekundách před kliknutím
- ✅ **Pozice odkazu** (`link_position`) - first/middle/last/top/bottom/only
- ✅ **Scroll depth** (`scroll_depth`) - procento scrollu (0-100)
- ✅ **Typ odkazu** (`link_type`) - external/social/shop
- ✅ **Viewport** (`viewport_width`, `viewport_height`) - rozměry okna

## Databázové struktury

### Tabulka: `link_clicks`
Agregované statistiky kliků na odkazy (existující).

### Tabulka: `link_click_events` (NOVÁ)
Detailní záznamy každého jednotlivého kliku.

**Migrace:** `config/link_click_events_table.sql`

## Implementované soubory

### Models
- `app/Models/LinkClickEvent.php` - Model pro práci s detailními kliky
  - `recordEvent()` - uložení detailního kliku
  - `getEventsByArticle()` - získání eventů pro článek
  - `getDeviceStatsByArticle()` - statistiky zařízení
  - `getBrowserStatsByArticle()` - statistiky prohlížečů
  - `getCountryStatsByArticle()` - statistiky zemí
  - `getHourlyStatsByArticle()` - časové rozložení
  - `getAvgTimeOnPageByArticle()` - průměrný čas na stránce
  - `getUniqueIPsByArticle()` - počet unikátních IP

### Helpers
- `app/Helpers/UserAgentHelper.php` - Parsing User Agent
  - `detectDeviceType()` - detekce typu zařízení
  - `detectBrowser()` - detekce prohlížeče
  - `detectOS()` - detekce operačního systému
  - `getClientIP()` - získání IP adresy (s podporou proxy)

- `app/Helpers/GeoLocationHelper.php` - Geolokace z IP
  - `getLocationFromIP()` - získání země a města z IP (ip-api.com)

- `app/Helpers/LinkTrackingHelper.php` (upraveno)
  - Rozšířeno o detekci pozice odkazu v článku
  - Rozšířeno o detekci typu odkazu (social/shop/external)

### Controllers
- `app/Controllers/Web/LinkTrackingController.php` (upraveno)
  - Sběr všech dostupných metrik při kliku
  - Uložení do `link_click_events`
  - Asynchronní geolokace (s timeoutem)

- `app/Controllers/Admin/LinkClicksAdminController.php` (upraveno)
  - Zobrazení detailních statistik
  - Statistiky zařízení, prohlížečů, zemí
  - Časové rozložení kliků
  - Seznam posledních kliků

### Views
- `app/Views/Web/articles/article.php` (upraveno)
  - JavaScript tracking pro čas na stránce
  - JavaScript tracking pro scroll depth
  - JavaScript tracking pro viewport rozměry
  - Automatické přidání parametrů do tracking URL

- `app/Views/Admin/link-clicks/article.php` (upraveno)
  - Zobrazení detailních statistik
  - Grafy a tabulky pro různé metriky
  - Seznam posledních kliků s detaily

## Jak to funguje

### 1. Při zobrazení článku
1. `LinkTrackingHelper::addTrackingToLinks()` upraví HTML
2. Všechny externí odkazy se převedou na `/track/{token}`
3. K tokenu se přidají metadata (pozice, typ odkazu)

### 2. Při kliku na odkaz
1. JavaScript zachytí klik a přidá parametry:
   - `time` - čas na stránce (sekundy)
   - `scroll` - scroll depth (%)
   - `vw`, `vh` - rozměry viewportu
2. Uživatel je přesměrován na `/track/{token}?time=X&scroll=Y&vw=W&vh=H`
3. `LinkTrackingController::track()` zpracuje požadavek:
   - Dekóduje token
   - Zaznamená agregovaný klik (`link_clicks`)
   - Sběr metrik:
     - IP adresa (s podporou proxy)
     - User Agent
     - Referrer
     - Session ID
     - Detekce zařízení, prohlížeče, OS
     - Geolokace (asynchronně)
     - Parametry z URL (time, scroll, viewport)
   - Uloží detailní event (`link_click_events`)
4. Uživatel je přesměrován na cílovou URL

### 3. V admin panelu
- Přehled všech článků s celkovými kliky
- Detailní statistiky pro každý článek:
  - Rozložení zařízení
  - Rozložení prohlížečů
  - Rozložení zemí
  - Časové rozložení (po hodinách)
  - Průměrný čas na stránce
  - Počet unikátních IP
  - Seznam posledních 50 kliků s detaily

## GDPR a soukromí

⚠️ **Důležité**: IP adresy jsou osobní údaje podle GDPR

**Možná řešení:**
- Anonymizace IP (poslední oktet = 0)
- Hashování IP adres
- Ukládání pouze první 3 oktety
- Informování uživatelů v cookies/privacy policy

**Aktuální stav:** IP adresy se ukládají v plné formě. Doporučuji implementovat anonymizaci.

## API pro geolokaci

Používá se **ip-api.com** (free tier):
- Max 45 requestů/minutu
- Timeout 2 sekundy
- Automatické ignorování lokálních IP adres
- Tichá chyba při selhání (neblokuje redirect)

## Výkon

- Geolokace se volá asynchronně s timeoutem
- Chyby při ukládání eventů neblokují redirect
- JavaScript tracking je non-blocking
- Indexy v databázi pro rychlé dotazy

## Další možné rozšíření

1. **Grafy** - vizualizace časových trendů
2. **Export dat** - CSV/Excel export statistik
3. **Filtry** - filtrování podle zařízení, země, atd.
4. **A/B testování** - testování různých pozic odkazů
5. **E-mailové reporty** - automatické týdenní/měsíční reporty
6. **Heatmapy** - vizualizace kliků na stránce

## Instalace

1. Spustit SQL migraci: `config/link_click_events_table.sql`
2. Systém začne automaticky sbírat data při kliku na odkazy
3. Statistiky jsou dostupné v admin panelu: `/admin/link-clicks`

