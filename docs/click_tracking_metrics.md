# Dostupné metriky pro Click Tracking

## Aktuálně sledované informace
- ✅ Počet kliků na odkaz
- ✅ Text odkazu
- ✅ URL odkazu
- ✅ Článek, ve kterém je odkaz
- ✅ První klik (created_at)
- ✅ Poslední aktualizace (updated_at)

## Navržené rozšíření - detailní tracking

### 1. **Časové informace** ⏰
- Přesný čas každého kliku (ne jen agregace)
- Analýza: Kdy jsou lidé nejaktivnější? (ráno, odpoledne, večer)
- Trendy v čase: Graf kliků během dne/týdne/měsíce

### 2. **IP adresa** 🌐
- Detekce botů (stejná IP, mnoho kliků = podezřelé)
- Unikátní uživatelé (přibližně)
- Analýza: Kolik unikátních IP adres kliklo?

### 3. **User Agent** 💻
- Typ zařízení: Desktop / Mobile / Tablet
- Prohlížeč: Chrome, Firefox, Safari, Edge...
- Operační systém: Windows, macOS, iOS, Android, Linux
- Analýza: Jaké zařízení používají čtenáři?

### 4. **Referrer** 🔗
- Odkud přišel uživatel před kliknutím
- Analýza: Přicházejí z Google, Facebook, přímý přístup?
- Které zdroje generují nejvíce kliků?

### 5. **Session ID** 🎫
- Tracking unikátních uživatelů během jedné návštěvy
- Analýza: Kolik unikátních návštěvníků kliklo?
- Opakované kliky stejného uživatele

### 6. **Geolokace** 🌍 (volitelné, vyžaduje API)
- Země (z IP adresy)
- Město (pokud dostupné)
- Analýza: Kde jsou čtenáři? (CZ, SK, další země?)

### 7. **Další možné metriky**
- **Čas strávený na stránce** před kliknutím (JavaScript)
- **Pozice odkazu** v článku (první, střed, konec)
- **Typ odkazu** (externí, sociální sítě, e-shop, atd.)
- **Scroll depth** - jak daleko scrolloval před kliknutím

## Praktické využití

### Pro redakci:
- ✅ Které odkazy jsou nejpopulárnější?
- ✅ Kdy publikovat články s odkazy? (časová analýza)
- ✅ Jaké zařízení používají čtenáři? (optimalizace obsahu)
- ✅ Odkud přicházejí čtenáři? (marketing)

### Pro analýzu:
- ✅ Detekce botů (filtrování falešných kliků)
- ✅ Unikátní vs. celkové kliky
- ✅ Konverzní poměr (zobrazení vs. kliky)
- ✅ A/B testování (různé pozice odkazů)

## Implementace

### Varianta 1: Základní (doporučeno)
- IP adresa
- User Agent (zařízení, prohlížeč, OS)
- Referrer
- Session ID
- Přesný čas kliku

### Varianta 2: Rozšířená
- Vše z Varianty 1
- + Geolokace (země, město)
- + Čas strávený na stránce (JavaScript)

### Varianta 3: Maximální
- Vše z Varianty 2
- + Scroll depth
- + Pozice odkazu v článku
- + Typ odkazu

## GDPR a soukromí

⚠️ **Důležité**: IP adresy jsou osobní údaje podle GDPR
- Možnost anonymizace IP (poslední oktet = 0)
- Možnost hashování IP adres
- Možnost ukládání pouze první 3 oktety
- Uživatelé by měli být informováni v cookies/privacy policy

## Doporučení

Pro začátek doporučuji **Varianta 1** - poskytne užitečné informace bez složitosti:
- ✅ Snadná implementace
- ✅ Žádné externí API (geolokace)
- ✅ Minimální dopad na výkon
- ✅ Užitečné metriky pro analýzu

