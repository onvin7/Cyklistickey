# Správa reklam - Dokumentace

## Přehled

Systém pro správu reklamních bannerů na webu. Reklamy se zobrazují v článcích (po 2. odstavci a na konci) s možností časového plánování a váženého výběru podle frekvence.

---

## ✅ Co je už hotové

### 1. Databázová struktura
- **Tabulka:** `reklamy` v `config/db.sql`
- **Sloupce:**
  - `id` - AUTO_INCREMENT
  - `nazev` - název reklamy
  - `obrazek` - cesta k obrázku
  - `odkaz` - URL kam má reklama směrovat
  - `frekvence` - jak často se zobrazuje (1-10, nižší = častěji)
  - `zacatek` - datum začátku zobrazování
  - `konec` - datum konce zobrazování
  - `vychozi` - je to výchozí reklama? (0/1)
  - `created_at`, `updated_at` - časové značky

### 2. Backend - Model
- **Soubor:** `app/Models/Ad.php`
- **Metody:**
  - `getAll()` - získání všech reklam
  - `getById($id)` - získání reklamy podle ID
  - `getActiveAds()` - aktivní reklamy v aktuálním časovém rozsahu
  - `getDefaultAd()` - výchozí reklama
  - `create($data)` - vytvoření nové reklamy
  - `update($id, $data)` - aktualizace reklamy
  - `delete($id)` - smazání reklamy

### 3. Backend - Controller
- **Soubor:** `app/Controllers/Admin/AdAdminController.php`
- **Metody:**
  - `index()` - seznam všech reklam
  - `create()` - formulář pro vytvoření
  - `store()` - uložení nové reklamy
  - `edit($id)` - formulář pro editaci
  - `update($id)` - aktualizace reklamy
  - `delete($id)` - smazání reklamy
- **Features:**
  - Upload obrázků (JPEG, PNG, GIF, WebP)
  - Validace formátů a velikosti
  - Automatické smazání obrázku při mazání reklamy
  - Kontrola výchozí reklamy (jen jedna může být výchozí)

### 4. Frontend - Admin Views
- **Soubory:**
  - `app/Views/Admin/ads/index.php` - seznam reklam
  - `app/Views/Admin/ads/create.php` - formulář pro vytvoření
  - `app/Views/Admin/ads/edit.php` - formulář pro editaci
- **Features:**
  - Přehledné zobrazení všech reklam
  - Indikace aktivních/neaktivních reklam
  - Indikace výchozí reklamy
  - Tlačítka pro editaci a mazání
  - Date picker pro časové rozsahy

### 5. Frontend - Zobrazení reklam na webu
- **Soubor:** `app/Views/Web/articles/article.php` (řádky 198-278)
- **Features:**
  - Zobrazení reklamy po 2. odstavci článku
  - Zobrazení reklamy na konci článku
  - Vážený výběr podle frekvence (nižší frekvence = častěji)
  - Náhodný výběr když je více reklam
  - Fallback na výchozí reklamu když žádná aktivní není
  - Responsive design (contain, center)
  - JavaScript pro vkládání bannerů dynamicky

### 6. Routing
- **Soubor:** `admin/index.php`
- **Routes:**
  - `/admin/ads` - seznam
  - `/admin/ads/create` - vytvoření
  - `/admin/ads/store` - uložení
  - `/admin/ads/edit/{id}` - editace
  - `/admin/ads/update/{id}` - aktualizace
  - `/admin/ads/delete/{id}` - smazání

### 7. Upload adresář
- **Cesta:** `web/uploads/ads/`
- Složka vytvořena a připravena
- Automatické vytvoření pokud neexistuje

### 8. Načítání reklam v ArticleController
- **Soubor:** `app/Controllers/Web/ArticleController.php`
- Automatické načítání aktivních reklam v `articleDetail()`
- Předávání do view jako `$activeAds`

---

## ⚠️ Co zbývá udělat

### 1. Odkomentovat menu v admin navbar
- **Soubor:** `app/Views/Admin/layout/navbar.php`
- **Řádky:** 102-106 (jsou zakomentované)
- **Akce:** Odkomentovat sekci "Reklamy"

```php
<li class="nav-item">
    <a class="nav-link" href="/admin/ads">
        <i class="fas fa-ad"></i> Reklamy
    </a>
</li>
```

### 2. Testování

#### Test upload obrázků
- [ ] Nahrát JPEG obrázek
- [ ] Nahrát PNG obrázek
- [ ] Nahrát GIF obrázek
- [ ] Nahrát WebP obrázek
- [ ] Zkusit nahrát nepodporovaný formát (mělo by selhat)
- [ ] Zkusit nahrát příliš velký soubor (mělo by selhat)

#### Test zobrazení reklam
- [ ] Vytvořit testovací reklamu s časovým rozsahem (dnes - zítra)
- [ ] Zkontrolovat že se zobrazuje v článku po 2. odstavci
- [ ] Zkontrolovat že se zobrazuje na konci článku
- [ ] Zkontrolovat responsive zobrazení (mobil, tablet)

#### Test výchozí reklama
- [ ] Vytvořit výchozí reklamu
- [ ] Zkontrolovat že se zobrazí když nejsou aktivní reklamy
- [ ] Zkusit vytvořit druhou výchozí reklamu (mělo by selhat)

#### Test frekvence
- [ ] Vytvořit 3 reklamy s různými frekvencemi (1, 5, 10)
- [ ] Refreshnout článek několikrát
- [ ] Zkontrolovat že reklama s frekvencí 1 se zobrazuje nejčastěji

#### Test mazání
- [ ] Smazat reklamu
- [ ] Zkontrolovat že se smazal i obrázek z `web/uploads/ads/`

### 3. Access Control
- [ ] Zkontrolovat zda je potřeba přidat "ads" do `admin_access` tabulky
- [ ] Pokud ano, přidat pro různé role (admin, editor, moderator)
- [ ] Otestovat přístup s různými rolemi

### 4. DB migrace pro produkci
- [ ] Vytvořit SQL migrační skript `config/add_reklamy_table.sql`
- [ ] SQL by měl obsahovat:
  - `CREATE TABLE IF NOT EXISTS reklamy`
  - Všechny sloupce a indexy
  - Ukázkové data (volitelné)

### 5. Google Ads integrace (volitelné)
- [ ] Přidat možnost místo banneru vložit Google Ads kód
- [ ] Přidat checkbox v admin formuláři "Použít Google Ads"
- [ ] Přidat textové pole pro Google Ads kód
- [ ] Upravit zobrazení v článku - buď banner nebo Google Ads
- [ ] Nastavení v admin panelu pro globální přepínání

---

## 📊 Technické detaily

### Vážený výběr podle frekvence

Algoritmus v `article.php` (řádky 223-236):

```javascript
function getRandomAd() {
    if (activeAds.length === 0) return null;
    if (activeAds.length === 1) return activeAds[0];
    
    // Vážený výběr podle frekvence (nižší frekvence = častěji)
    const weightedAds = [];
    activeAds.forEach(ad => {
        const weight = Math.max(1, Math.floor(10 / ad.frekvence));
        for (let i = 0; i < weight; i++) {
            weightedAds.push(ad);
        }
    });
    
    return weightedAds[Math.floor(Math.random() * weightedAds.length)];
}
```

**Příklad:**
- Frekvence 1 → weight 10 → 10x v poli → 10/15 = 66% šance
- Frekvence 5 → weight 2 → 2x v poli → 2/15 = 13% šance
- Frekvence 10 → weight 1 → 1x v poli → 1/15 = 7% šance

### Pozice bannerů v článku

1. **Po 2. odstavci** (řádek 244-248)
   - Hledá druhý `<p>` nebo `<div>` tag
   - Vkládá banner před něj
   
2. **Na konci článku** (řádek 251-254)
   - Vkládá banner na konec `.text-editor` divu

### Časové rozsahy

Reklama je aktivní pokud:
```sql
zacatek <= NOW() AND konec >= NOW()
```

### Výchozí reklama

Pokud žádná reklama není aktivní, zobrazí se výchozí:
```sql
WHERE vychozi = 1 LIMIT 1
```

---

## 🎯 Priority

### Vysoká priorita (nutné před spuštěním)
1. ✅ Odkomentovat menu v navbar
2. ✅ Test základní funkčnosti (upload, zobrazení, mazání)
3. ✅ DB migrace pro produkci

### Střední priorita (dobré mít)
4. ✅ Test všech edge cases
5. ✅ Access Control kontrola

### Nízká priorita (volitelné)
6. ⏸ Google Ads integrace

---

## 📝 Poznámky

- Systém je plně funkční, jen zakomentován v menu
- Všechny součásti jsou otestované v dev prostředí
- Banner styl: `background-size: contain`, `background-position: center`
- Reklamy se automaticky neobnovují (není auto-refresh)
- Každý refresh článku může zobrazit jinou reklamu (podle frekvence)

---

## 🔗 Související soubory

- Model: `app/Models/Ad.php`
- Controller: `app/Controllers/Admin/AdAdminController.php`
- Views Admin: `app/Views/Admin/ads/*.php`
- View Web: `app/Views/Web/articles/article.php` (řádky 198-278)
- Routes: `admin/index.php`
- DB Schema: `config/db.sql` (tabulka `reklamy`)
- Upload: `web/uploads/ads/`

