# Flash News JSON Administrace

## Přehled

Flash News administrace nyní pracuje s JSON souborem místo databáze. Flash news se automaticky aktualizují z externího API a ukládají se do `web/flash.json` souboru.

## Struktura

### JSON Struktura
```json
{
    "news": {
        "titles": [
            {
                "title": "Název novinky",
                "is_active": 1,
                "sort_order": 0,
                "created_at": "2025-01-01 12:00:00",
                "updated_at": "2025-01-01 12:00:00",
                "created_by_name": "API"
            }
        ]
    },
    "tech": {
        "titles": [...]
    },
    "custom": {
        "titles": [...]
    }
}
```

### Typy Flash News
- **news** - Cyklistické novinky z API
- **tech** - Technické novinky z API  
- **custom** - Vlastní flash news vytvořené v admin panelu

## Funkce

### Automatická aktualizace
- Flash news se automaticky aktualizují z API
- API endpointy: `https://api.cyklistickey.cz/api/news` a `https://api.cyklistickey.cz/api/tech`
- Aktualizace se provádí přes `web/flash_config.php`

### Administrace
- **Vytváření** vlastních flash news
- **Úprava** existujících flash news
- **Mazání** flash news
- **Přepínání** aktivního stavu
- **Změna pořadí** zobrazení
- **Náhled** jak se zobrazí na webu
- **Aktualizace z API** - manuální aktualizace dat z API

### Bezpečnost
- CSRF ochrana všech formulářů
- XSS ochrana ve výstupech
- Validace vstupů
- Error handling a logging

## Soubory

### Model
- `app/Models/FlashNewsJSON.php` - Model pro práci s JSON souborem

### Controller
- `app/Controllers/Admin/FlashNewsJSONAdminController.php` - Admin controller

### Views
- `app/Views/Admin/flashnews/index.php` - Seznam flash news
- `app/Views/Admin/flashnews/create.php` - Vytvoření nové flash news
- `app/Views/Admin/flashnews/edit.php` - Úprava flash news
- `app/Views/Admin/flashnews/preview.php` - Náhled flash news

### Web zobrazení
- `app/Views/Web/layouts/flash.php` - Zobrazení flash news na webu

### Skripty
- `web/flash_config.php` - Aktualizace dat z API
- `web/refresh_flash_news.php` - Manuální aktualizace
- `web/cron_flash_news.php` - Cron job pro automatickou aktualizaci

## Použití

### Manuální aktualizace
```bash
php web/refresh_flash_news.php
```

### Cron job (každých 30 minut)
```bash
*/30 * * * * php /path/to/web/cron_flash_news.php
```

### Admin panel
1. Přejdi na `/admin/flashnews`
2. Klikni na "Aktualizovat z API" pro manuální aktualizaci
3. Vytvoř vlastní flash news pomocí "Nová Flash News"
4. Uprav nebo smaž existující flash news

## Výhody JSON implementace

1. **Rychlost** - Žádné databázové dotazy
2. **Jednoduchost** - Snadná údržba a zálohování
3. **Flexibilita** - Snadné přidávání nových polí
4. **API integrace** - Automatická aktualizace z externích zdrojů
5. **Offline práce** - Funguje i bez databáze

## Poznámky

- Flash news z API se automaticky označují jako `created_by_name: "API"`
- Vlastní flash news se označují jako `created_by_name: "Admin"`
- Aktualizace z API přepíše všechna data v JSON souboru
- Vlastní flash news se při aktualizaci z API zachovají (pokud nejsou ve stejné kategorii)
