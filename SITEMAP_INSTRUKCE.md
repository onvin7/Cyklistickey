# Instrukce pro Sitemap a Google News Sitemap

## Co je potřeba udělat

### 1. Google Search Console nastavení

#### A) Ověření webu
1. Přihlaste se do [Google Search Console](https://search.google.com/search-console)
2. Přidejte property: `https://www.cyklistickey.cz` (nebo `bicenc.cyklistickey.cz` pokud ještě běží na testu)
3. Ověřte vlastnictví webu (doporučuji HTML tag metoda - přidám do base.php)

#### B) Odeslání sitemap
Po implementaci sitemapů odešlete do Search Console:
- `https://www.cyklistickey.cz/sitemap.xml` (hlavní sitemap)
- `https://www.cyklistickey.cz/sitemap-images.xml` (obrázky)
- `https://www.cyklistickey.cz/sitemap-news.xml` (Google News)

### 2. Google News nastavení

#### A) Google News Publisher Center
1. Jděte na [Google News Publisher Center](https://publishers.google.com/)
2. Přihlaste se se stejným Google účtem jako Search Console
3. Přidejte svůj web jako zdroj zpráv
4. Vyplňte informace:
   - Název: "Cyklistický magazín"
   - URL: `https://www.cyklistickey.cz`
   - Jazyk: Čeština
   - Kategorie: Sport, Lifestyle
   - Popis: "Sledujte nejnovější zprávy, tréninkové tipy, technické novinky a rozhovory ze světa cyklistiky."

#### B) Požadavky pro Google News
- Články musí být aktuální (do 2-3 dnů od publikace)
- Musí mít jasné datum publikace
- Musí mít title, description, obrázek
- Musí být v češtině (nebo jiném podporovaném jazyce)
- Web musí být přístupný a rychlý

### 3. Co bude implementováno v kódu

#### A) Hlavní sitemap.xml
**Cesta**: `web/sitemap.php`

**Obsahuje**:
- Homepage (priority 1.0, changefreq: daily)
- Statické stránky (kategorie, autoři, kontakt, events) - priority 0.8, changefreq: weekly
- Všechny články (priority 0.9, changefreq: weekly)
- Kategorie (priority 0.7, changefreq: weekly)
- Autoři (priority 0.6, changefreq: monthly)

**Cache**: 1 hodina (automatická aktualizace při změnách)

**Formát**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://www.cyklistickey.cz/</loc>
    <lastmod>2025-01-15</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <!-- další URL -->
</urlset>
```

#### B) Image Sitemap (sitemap-images.xml)
**Cesta**: `web/sitemap-images.php`

**Obsahuje**:
- Všechny obrázky z článků (featured images)
- Obrázky v galeriích (pokud jsou)
- Logo a další důležité obrázky

**Formát**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
  <url>
    <loc>https://www.cyklistickey.cz/article/clanek-url</loc>
    <image:image>
      <image:loc>https://www.cyklistickey.cz/uploads/clanky/obrazek.jpg</image:loc>
      <image:title>Název obrázku</image:title>
      <image:caption>Popis obrázku</image:caption>
    </image:image>
  </url>
</urlset>
```

#### C) Google News Sitemap (sitemap-news.xml)
**Cesta**: `web/sitemap-news.php`

**Obsahuje**:
- Pouze nejnovější články (posledních 2-3 dny)
- Maximálně 1000 článků (Google limit)
- Pouze články s viditelnost = 1 a datum <= NOW()

**Formát**:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
  <url>
    <loc>https://www.cyklistickey.cz/article/clanek-url</loc>
    <news:news>
      <news:publication>
        <news:name>Cyklistický magazín</news:name>
        <news:language>cs</news:language>
      </news:publication>
      <news:publication_date>2025-01-15T10:00:00+01:00</news:publication_date>
      <news:title>Název článku</news:title>
    </news:news>
  </url>
</urlset>
```

### 4. Aktualizace robots.txt

**Soubor**: `web/robots.txt`

**Přidat**:
```
Sitemap: https://www.cyklistickey.cz/sitemap.xml
Sitemap: https://www.cyklistickey.cz/sitemap-images.xml
Sitemap: https://www.cyklistickey.cz/sitemap-news.xml
```

### 5. Kontrola a testování

#### A) Validace sitemap
1. Otevřete sitemap v prohlížeči: `https://www.cyklistickey.cz/sitemap.xml`
2. Zkontrolujte, že XML je validní (žádné chyby)
3. Zkontrolujte, že všechny URL jsou správné (https://, bez duplicit)
4. Zkontrolujte lastmod datumy

#### B) Testování v Search Console
1. Po odeslání sitemap v Search Console počkejte 1-2 dny
2. Zkontrolujte, zda Google našel všechny URL
3. Zkontrolujte případné chyby (červené ikony)
4. Opravte chyby a znovu odešlete sitemap

#### C) Testování Google News
1. Po přidání do Publisher Center počkejte 1-2 týdny
2. Zkontrolujte, zda se články objevují v Google News
3. Pokud ne, zkontrolujte:
   - Jsou články aktuální (do 2-3 dnů)?
   - Mají správné structured data?
   - Je sitemap-news.xml validní?

### 6. Pravidelné údržba

#### A) Automatická aktualizace
- Sitemap se aktualizuje automaticky při:
  - Publikaci nového článku
  - Úpravě existujícího článku
  - Změně kategorie
- Cache se obnoví automaticky po 1 hodině

#### B) Ruční aktualizace (pokud je potřeba)
- Smazat cache soubory v `web/cache/` (pokud existují)
- Nebo počkat na automatickou aktualizaci

#### C) Monitoring
- Pravidelně kontrolovat Search Console (1x týdně)
- Kontrolovat, zda Google indexuje nové články
- Kontrolovat chyby v sitemapu

### 7. Důležité poznámky

#### A) URL struktura
- Všechny URL musí být absolutní (s https://www.cyklistickey.cz)
- URL musí být kódované (htmlspecialchars)
- URL nesmí obsahovat query parametry (pokud možno)

#### B) Datumy
- lastmod musí být ve formátu YYYY-MM-DD nebo ISO 8601
- Pro Google News: publication_date musí být ISO 8601 s časovou zónou
- Použít skutečné datum z databáze (datum, updated_at)

#### C) Limity
- Hlavní sitemap: max 50,000 URL, max 50MB
- Pokud je více URL, rozdělit do více sitemap souborů
- Google News: max 1000 článků, pouze poslední 2-3 dny

#### D) Performance
- Sitemap má cache (1 hodina) pro rychlost
- Pokud je hodně článků, použít paginaci nebo rozdělit do více souborů
- Rate limiting je implementován (10 requestů/hodinu)

### 8. Checklist před spuštěním

- [ ] Sitemap.xml je přístupný a validní
- [ ] Sitemap-images.xml je přístupný a validní
- [ ] Sitemap-news.xml je přístupný a validní
- [ ] Robots.txt obsahuje odkazy na všechny sitemapy
- [ ] Web je ověřený v Google Search Console
- [ ] Sitemapy jsou odeslané v Search Console
- [ ] Web je přidaný do Google News Publisher Center
- [ ] Všechny URL v sitemapu jsou přístupné (200 OK)
- [ ] Datumy jsou správně formátované
- [ ] Cache mechanismus funguje

### 9. Řešení problémů

#### Problém: Sitemap není přístupný
- Zkontrolujte .htaccess pravidla
- Zkontrolujte oprávnění souborů (755 pro adresáře, 644 pro soubory)
- Zkontrolujte PHP chyby v error logu

#### Problém: Google nenašel URL ze sitemapu
- Počkejte 1-2 dny (Google potřebuje čas)
- Zkontrolujte, zda URL jsou přístupné
- Zkontrolujte robots.txt (nesmí blokovat URL)
- Zkontrolujte, zda stránky nemají noindex

#### Problém: Google News nefunguje
- Zkontrolujte, zda články jsou aktuální (do 2-3 dnů)
- Zkontrolujte structured data (NewsArticle schema)
- Zkontrolujte, zda sitemap-news.xml je validní
- Počkejte 1-2 týdny (Google News potřebuje čas na indexaci)

### 10. Užitečné odkazy

- [Google Sitemap Guidelines](https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview)
- [Google News Sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap)
- [Image Sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps)
- [Google Search Console](https://search.google.com/search-console)
- [Google News Publisher Center](https://publishers.google.com/)

---

**Poznámka**: Po implementaci kódu budete muset:
1. Ověřit web v Google Search Console
2. Odeslat sitemapy v Search Console
3. Přidat web do Google News Publisher Center
4. Počkat na indexaci (1-2 dny pro Search Console, 1-2 týdny pro Google News)

