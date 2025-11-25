# Veřejná viditelnost uživatelů

## Přehled

Funkce umožňuje skrýt některé uživatele z veřejné sekce "Redakce" na webu. Užitečné pro testovací účty, bývalé členy týmu nebo uživatele, kteří nemají veřejný profil.

---

## ✅ Co je hotové

### 1. Databázová struktura
- **Sloupec:** `public_visible` v tabulce `users`
- **Typ:** `TINYINT(1)` 
- **Výchozí hodnota:** `1` (viditelný)
- **Hodnoty:** 
  - `1` = uživatel je viditelný v sekci redakce
  - `0` = uživatel je skrytý
- **Umístění:** Za sloupcem `role`
- **Soubory:**
  - `config/db.sql` (hlavní schéma) - ✅ PŘIDÁNO
  - `config/add_public_visible_column.sql` (migrace pro existující DB)

### 2. Backend - Model
- **Soubor:** `app/Models/User.php`
- **Metoda `getAll()`:**
  - Filtruje uživatele: `WHERE u.public_visible = 1`
  - Zobrazuje pouze viditelné uživatele v sekci redakce
  - Řadí podle posledního článku (DESC)
- **Metoda `update()`:**
  - Ukládá hodnotu `public_visible`
  - Výchozí hodnota `1` pokud není zadáno
  - Binding: `$data['public_visible'] ?? 1`

### 3. Frontend - Admin formulář
- **Soubor:** `app/Views/Admin/users/edit.php`
- **Checkbox:**
  - Label: "Veřejně viditelný v sekci redakce"
  - Ikona: `fa-eye`
  - Výchozí stav: zaškrtnutý (checked)
  - Automaticky zaškrtnutý pro nové i existující uživatele
- **Umístění:** Po poli pro roli, před upload fotky

### 4. Zpětná kompatibilita
- ✅ Výchozí hodnota `1` zajišťuje, že všichni existující uživatelé zůstanou viditelní
- ✅ Pokud sloupec není v DB, kód používá výchozí hodnotu `1`
- ✅ Checkbox je automaticky zaškrtnutý pro nové uživatele

---

## 🔧 Jak použít

### Pro admina:
1. Jít do **Admin** → **Uživatelé**
2. Kliknout na **Upravit** u konkrétního uživatele
3. Odškrtnout checkbox **"Veřejně viditelný v sekci redakce"**
4. Uložit změny
5. Uživatel je nyní skrytý z veřejné sekce redakce

### Pro web návštěvníka:
- V sekci **Redakce** se zobrazují pouze uživatelé s `public_visible = 1`
- Skrytí uživatelé nejsou v seznamu vůbec viditelní
- Články skrytých uživatelů se stále zobrazují normálně

---

## 📝 Poznámky

- **Články:** Skrytí uživatele neovlivňuje zobrazení jeho článků
- **Autor u článku:** Jméno autora se stále zobrazuje u článků
- **Profil:** URL profilu skrytého uživatele je stále přístupná (pokud někdo zná odkaz)
- **Výchozí stav:** Všichni uživatelé jsou viditelní, dokud admin ručně nenastaví jinak

---

## 🎯 Případy použití

### 1. Testovací účty
- Skrýt testovací nebo demo účty z veřejné sekce
- Zachovat funkčnost pro testování v adminu

### 2. Bývalí členové týmu
- Skrýt profily bývalých redaktorů
- Jejich články zůstanou viditelné

### 3. Technické účty
- Skrýt účty pro automatické generování obsahu
- Skrýt účty pro migraci dat

### 4. Neúplné profily
- Skrýt uživatele, kteří ještě nemají kompletní profil
- Zobrazit až po doplnění všech informací

---

## 🔗 Související soubory

- Model: `app/Models/User.php` (řádky 16, 59, 66)
- View: `app/Views/Admin/users/edit.php` (řádky 50-57)
- DB Schema: `config/db.sql` (tabulka `users`)
- Migrace: `config/add_public_visible_column.sql`

---

## ✅ Status: **HOTOVO & TESTOVÁNO**

Funkce je plně implementována a připravena k použití. Stačí spustit SQL migraci na produkční databázi:

```sql
ALTER TABLE `users` 
ADD COLUMN `public_visible` TINYINT(1) NOT NULL DEFAULT 1 
AFTER `role`;
```

Nebo použít migr
