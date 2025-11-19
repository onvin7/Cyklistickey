# Systém pro správu závodů (Events)

## Proč databáze místo souborů?

### ✅ **Výhody databáze:**
1. **Dynamické přidávání/upravování** - Závody lze přidávat a upravovat přes admin panel bez nutnosti editovat PHP soubory
2. **Flexibilita** - Snadné přidávání nových polí (např. kategorie, program, výsledky)
3. **Centralizace** - Všechny závody na jednom místě
4. **SEO friendly** - URL struktura `/events/rok/nazev` je konzistentní
5. **Škálovatelnost** - Snadné přidávání nových závodů bez vytváření nových souborů
6. **Admin rozhraní** - Možnost vytvořit CRUD operace v adminu (jako u článků)

### ❌ **Nevýhody souborů:**
1. Pro každý závod nový PHP soubor
2. Těžší údržba a aktualizace
3. Nelze snadno přidávat závody přes admin
4. Duplikace kódu mezi soubory

## Struktura databáze

### Tabulka `events`
- Ukládá jednotlivé závody
- Podporuje jak jednotlivé závody, tak závody v seriálu
- Obsahuje HTML obsah pro detail stránky

### Tabulka `event_series`
- Ukládá informace o seriálech závodů (např. "Cyklistickey Cup 2024")
- Propojena s `events` přes `series_id`

## Jak to použít

### 1. Instalace
```sql
-- Spusť SQL skript
SOURCE config/events_table.sql;
```

### 2. Migrace existujících závodů
Existující závody (cyklistickey_race.php, bezeckey_race.php) lze:
- **Možnost A:** Ponechat jako statické soubory pro zpětnou kompatibilitu
- **Možnost B:** Migrovat obsah do databáze a použít dynamický view

### 3. Použití v kódu

```php
// V HomeController::events()
$eventModel = new Event($db);
$eventsByYear = [];

// Získání všech roků
$years = [2024, 2023, 2022];
foreach ($years as $year) {
    $eventsByYear[$year] = [
        'individual' => $eventModel->getIndividualByYear($year),
        'series' => [
            'info' => $eventModel->getSeriesInfoByYear($year),
            'events' => $eventModel->getSeriesByYear($year)
        ]
    ];
}
```

### 4. Detail závodu
```php
// V HomeController::eventDetail($year, $name)
$event = $eventModel->getByYearAndName($year, $name);
if ($event) {
    // Zobrazit detail z databáze
    // $event['content'] obsahuje HTML
}
```

## Doporučený postup

1. **Fáze 1:** Vytvořit tabulky a model (✅ hotovo)
2. **Fáze 2:** Vytvořit admin rozhraní pro správu závodů (CRUD)
3. **Fáze 3:** Migrovat existující závody do DB
4. **Fáze 4:** Upravit view, aby používalo data z DB místo statických souborů
5. **Fáze 5:** (Volitelné) Odstranit staré PHP soubory závodů

## Alternativní řešení

Pokud chceš zůstat u souborů, můžeš použít:
- **Template systém** - Jeden template soubor, který se naplní daty
- **JSON soubory** - Podobně jako Flash News (jednodušší, ale méně flexibilní)
- **Hybridní řešení** - Důležité závody v DB, ostatní jako soubory

## Doporučení

**Doporučuji databázi**, protože:
- Už máš podobný systém pro články (clanky)
- Budeš moci přidávat závody přes admin
- Snadnější údržba a rozšiřování
- Konzistentní s ostatními částmi aplikace



