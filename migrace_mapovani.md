# Mapování migrace databáze - Stará DB → Nová DB

## 1. UŽIVATELÉ (users)

### Stará DB → Nová DB
- `id` → `id` (zachovat)
- `email` → `email` (zachovat)
- `heslo` → `heslo` (zachovat)
- `admin` → `role` (převod: admin=1 → role=1, admin=0 → role=0)
- `name` → `name` (zachovat)
- `surname` → `surname` (zachovat)
- `profil_foto` → `profil_foto` (zachovat)
- `popis` → `popis` (zachovat)
- `datum` → `datum` (zachovat)
- `zahlavi_foto` → **ODSTRANIT** (není v nové DB)
- `ig` → **IGNOROVAT** (vyřeší se později)
- `twitter` → **IGNOROVAT** (vyřeší se později)
- `strava` → **IGNOROVAT** (vyřeší se později)

### Poznámky
- Sociální sítě (ig, twitter, strava) se nepřenášejí, vyřeší se později
- `socials` a `user_social` se vyřeší později

---

## 2. ČLÁNKY (clanky)

### Stará DB → Nová DB
- `id` → `id` (zachovat, pokud možno)
- `nazev` → `nazev` (zachovat)
- `datum` → `datum` (zachovat)
- `viditelnost` → `viditelnost` (zachovat)
- `nahled_foto` → `nahled_foto` (zachovat)
- `user_id` → `user_id` (zachovat, ale ověřit existenci v nové DB; pokud neexistuje, použít 0)
- `url` → `url` (zachovat)
- `autor` → **ODSTRANIT** (není v nové DB)
- **NOVÉ:** `obsah` → načíst z HTML souboru `clanek_{id}.html`

### Poznámky
- Obsah článku je ve staré DB v souborech, musí se načíst z HTML
- `clanky_blog` ze staré DB se **NEPŘENÁŠÍ** (jiná struktura)

---

## 3. KATEGORIE (kategorie)

### Stará DB → Nová DB
- `id` → `id` (zachovat, pokud možno)
- `nazev_kategorie` → `nazev_kategorie` (zachovat)
- `url` → `url` (zachovat)

### Podkategorie
- `podkategorie` ze staré DB → **NEPŘENÁŠÍ SE**
- `podkategorie_clanku` ze staré DB → **NEPŘENÁŠÍ SE**

---

## 4. VAZBY ČLÁNKŮ A KATEGORIÍ

### Stará DB → Nová DB
- `kategorie_clanku` → `clanky_kategorie`
  - `id_clanku` → `id_clanku` (zachovat)
  - `id_kategorie` → `id_kategorie` (zachovat, pokud kategorie existuje)
  
- `podkategorie_clanku` → **NEPŘENÁŠÍ SE**

### Poznámky
- V nové DB je jen jedna vazební tabulka `clanky_kategorie`
- Přenesou se pouze vazby z `kategorie_clanku`
- Podkategorie se nepřenášejí

---

## 5. FOTKY

### Stará DB → Nová DB
- **Tabulka `fotky` se NEPŘENÁŠÍ**
- Fotky se zpracují později (uložení do souborů a zmenšení)

---

## 6. AUDIO

### Stará DB → Nová DB
- **Tabulka `audio` se NEPŘENÁŠÍ do DB**
- Audio soubory se zpracují později:
  - Zkontrolovat, zda existuje soubor `{id_clanku}.mp3` ve staré struktuře
  - Pokud ano, přesunout do nové cesty
  - Přejmenovat podle `id_clanku` (název bude `{id_clanku}.mp3`)

---

## 7. PROPAGACE

### Stará DB → Nová DB
- `id` → `id` (zachovat)
- `id_clanku` → `id_clanku` (zachovat)
- `datum` → **PŘEVÉST NA** `zacatek` a `konec`
  - `konec` = `datum` (zachovat původní datum jako konec)
  - `zacatek` = `datum` - 7 dní (začátek je 7 dní před koncem)
- `user_id` → `0` (vždy 0)

### Poznámky
- V nové DB je `user_id` povinné, vždy se nastaví na 0
- V nové DB je časový rozsah (zacatek/konec), ve staré jen datum

---

## 8. ZOBRAZENÍ ČLÁNKŮ (views_clanku)

### Stará DB → Nová DB
- `id` → `id` (zachovat, pokud možno)
- `id_clanku` → `id_clanku` (zachovat)
- `pocet` → `pocet` (zachovat)
- `datum` → `datum` (zachovat)

### Poznámky
- Struktura je stejná, přímý přenos

---

## 9. RESET HESEL (password_resets)

### Stará DB → Nová DB
- `id` → `id` (zachovat)
- `user_id` → `user_id` (zachovat, ověřit existenci)
- `email` → `email` (zachovat)
- `token` → `token` (zachovat)
- `expires_at` → `expires_at` (zachovat)

### Poznámky
- Struktura je stejná, přímý přenos
- **Vypršelé tokeny se NEPŘENÁŠÍ** (kontrola: `expires_at` < NOW())

---

## 10. TABULKY, KTERÉ SE NEPŘENÁŠÍ

### Ze staré DB se NEPŘENÁŠÍ:
- `clanky_blog` - jiná struktura, jiný systém
- `produkty` - není v nové DB
- `pageviews` - není v nové DB
- `users_online` - není v nové DB
- `views_user` - není v nové DB
- `podkategorie` - nepřenáší se
- `podkategorie_clanku` - nepřenáší se
- `fotky` - nepřenáší se (zpracuje se později)
- `audio` - nepřenáší se do DB (zpracuje se později jako soubory)

### Nové tabulky v nové DB (není ve staré):
- `admin_access` - **NEPŘEPISOVAT!** Už jsou tam vyplněné hodnoty
- `admin_access_logs` - nová funkcionalita
- `socials` - vyřeší se později
- `user_social` - vyřeší se později

---

## POŘADÍ MIGRACE (doporučené)

1. **Kategorie** (nejdřív, protože na ně odkazují články)
   - `kategorie` → `kategorie`
   - `podkategorie` → **NEPŘENÁŠÍ SE**

2. **Uživatelé**
   - `users` → `users` (s převodem admin→role)
   - `ig`, `twitter`, `strava` → **IGNOROVAT** (vyřeší se později)

3. **Články**
   - Načíst obsah z HTML souborů `clanek_{id}.html`
   - `clanky` → `clanky` (s obsahem)
   - Pokud `user_id` neexistuje v nové DB, použít `0`

4. **Vazby**
   - `kategorie_clanku` → `clanky_kategorie`
   - `podkategorie_clanku` → **NEPŘENÁŠÍ SE**

5. **Propagace**
   - `propagace` → `propagace`
   - `datum` → `konec` (zachovat)
   - `zacatek` = `datum` - 7 dní
   - `user_id` = `0`

6. **Statistiky**
   - `views_clanku` → `views_clanku`

7. **Reset hesel**
   - `password_resets` → `password_resets`
   - **Filtrovat:** pouze tokeny kde `expires_at` >= NOW()

8. **Soubory** (zpracuje se později)
   - Fotky: načíst z `fotky` tabulky, uložit do souborů, zmenšit
   - Audio: zkontrolovat `{id_clanku}.mp3`, přesunout a přejmenovat

---

## DŮLEŽITÉ POZNÁMKY

1. **admin_access** - **NIKDY NEPŘEPISOVAT!** Už jsou tam vyplněné hodnoty
2. **user_id u článků** - pokud uživatel neexistuje v nové DB, použít `0`
3. **Podkategorie** - nepřenášejí se vůbec
4. **Fotky** - tabulka se nepřenáší, zpracuje se později (soubory + zmenšení)
5. **Audio** - tabulka se nepřenáší, zpracuje se později (soubory, přejmenování podle id_clanku)
6. **Propagace** - `user_id` = `0`, `konec` = původní `datum`, `zacatek` = `datum` - 7 dní
7. **Password resets** - nepřenášet vypršelé tokeny (`expires_at` < NOW())
8. **Sociální sítě** - `ig`, `twitter`, `strava` se ignorují, vyřeší se později


## CESTY K SOUBORŮM

### Staré cesty (starý server - magazin subdoména)
- **Náhledy článků (velké):** `/www/subdom/magazin/assets/img/upload/clanek_nahled/*`
- **Náhledy článků (malé):** `/www/subdom/magazin/assets/img/upload/clanek_nahled/nahled/*`
- **Fotky v obsahu článku:** `/www/subdom/magazin/assets/img/upload/clanek_obsah/*`
- **Profilové fotky uživatelů:** `/www/subdom/magazin/assets/img/upload/profil_foto/*`
- **Obsah článku (HTML):** `/www/subdom/magazin/assets/html/clanek_{id}.html`
- **Audio soubory:** `/www/subdom/magazin/assets/audio/{id_clanku}.mp3`

### Ignorované cesty (nepřenáší se)
- **Fotky v galerii:** `/www/subdom/magazin/assets/img/upload/clanek_gallery/*` - **IGNOROVAT**
- **Záhlaví uživatelů:** `/www/subdom/magazin/assets/img/upload/profil_zahlavi/*` - **IGNOROVAT**

### Nové cesty (nový server - bicenc subdoména)
- **Fotky v obsahu článku:** `/www/subdom/bicenc/web/uploads/articles/*`
- **Audio soubory:** `/www/subdom/bicenc/web/uploads/audio/{id_clanku}.mp3`
- **Náhledy článků (velké):** `/www/subdom/bicenc/web/uploads/thumbnails/velke/*`
- **Náhledy článků (malé):** `/www/subdom/bicenc/web/uploads/thumbnails/male/*`
- **Profilové fotky uživatelů:** `/www/subdom/bicenc/web/uploads/users/thumbnails/*`
