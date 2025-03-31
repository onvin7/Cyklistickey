# Redakční systém pro cyklistický magazín

## Obsah dokumentace
1. [Úvod](#úvod)
2. [Veřejná část](#veřejná-část)
   - [Struktura webu](#struktura-webu)
   - [Funkce pro návštěvníky](#funkce-pro-návštěvníky)
   - [Seznam všech stránek](#seznam-všech-stránek)
3. [Administrační část](#administrační-část)
   - [Přístup a autorizace](#přístup-a-autorizace)
   - [Správa článků](#správa-článků)
   - [Správa kategorií](#správa-kategorií)
   - [Správa uživatelů](#správa-uživatelů)
   - [Statistiky](#statistiky)
   - [Propagace článků](#propagace-článků)
   - [Seznam administračních stránek](#seznam-administračních-stránek)
4. [Architektura systému](#architektura-systému)
   - [MVC struktura](#mvc-struktura)
   - [Databázový model](#databázový-model)
   - [Systém směrování (routing)](#systém-směrování-routing)
   - [Middlewares](#middlewares)
   - [Helpery](#helpery)
5. [Moduly a funkce](#moduly-a-funkce)
   - [Články](#články)
   - [Kategorie](#kategorie)
   - [Uživatelé a role](#uživatelé-a-role)
   - [Statistiky a sledování](#statistiky-a-sledování)
   - [Systém nahrávaní souborů](#systém-nahrávaní-souborů)
   - [Bezpečnost](#bezpečnost)
6. [Technická implementace](#technická-implementace)
   - [Databázové schéma](#databázové-schéma)
   - [Konfigurace](#konfigurace)

## Úvod

Redakční systém pro cyklistický magazín je webová aplikace vyvinutá za účelem správy a publikace článků s cyklistickou tématikou. Systém umožňuje tvorbu, úpravu, mazání a publikaci článků s různými úrovněmi přístupu pro uživatele s různými rolemi (redaktor, editor, administrátor).

Systém je vybudován na architektuře MVC (Model-View-Controller) s využitím PHP a MySQL databáze. Hlavní funkcionalita zahrnuje správu článků, kategorií, uživatelů, sledování statistik a propagaci vybraných článků.

## Veřejná část

### Struktura webu

Veřejná část webu je přístupná všem návštěvníkům a obsahuje následující sekce:

- **Úvodní stránka** - zobrazuje nejnovější a propagované články
- **Kategorie** - články roztříděné podle tématických kategorií 
- **Seznam článků** - kompletní seznam všech publikovaných článků
- **Detail článku** - zobrazení konkrétního článku včetně zvukové stopy (pokud je přiložena)
- **Autoři** - seznam všech autorů s odkazy na jejich články
- **Detail autora** - informace o autorovi a jeho články
- **Kontakt** - kontaktní informace redakce
- **Závody** - informace o závodech (Cyklistickey, Bezeckey)
- **Přihlášení a registrace** - možnost přihlášení pro autory a registrace nových uživatelů
- **Chybová stránka 404** - vlastní stránka pro neexistující URL adresy

### Funkce pro návštěvníky

Návštěvníci mohou:
- Prohlížet publikované články
- Filtrovat články podle kategorií
- Zobrazit profily autorů a jejich články
- Registrovat se jako nový uživatel
- Přihlásit se do systému (pokud mají účet)
- Resetovat zapomenuté heslo
- Poslouchat zvukové stopy přiložené k článkům
- Procházet související články

**Důležitá poznámka:** Systém registrace a přihlašování je plně funkční pro uživatelské účty. Uživatel si může vytvořit účet přes /login, kde mu systém automaticky přidělí roli běžného uživatele. Administrátor má následně možnost tomuto účtu přidělit vyšší roli (redaktor, editor, administrátor), čímž získá přístup do administrační části webu. Přístup do administrace je tedy podmíněn přidělením odpovídající role administrátorem systému.

### Seznam všech stránek

**Veřejná část** - všechny dostupné URL adresy pro běžné návštěvníky:

1. **Hlavní stránky**
   - `/` nebo `/home` - Úvodní stránka
   - `/categories` - Seznam všech kategorií
   - `/articles` - Seznam všech článků
   - `/authors` - Seznam všech autorů
   - `/kontakt` - Kontaktní informace
   - `/404` - Chybová stránka (automaticky zobrazena při neexistující URL)

2. **Články**
   - `/article/[název-článku]` - Detail konkrétního článku
   - `/category/[název-kategorie]` - Seznam článků v dané kategorii

3. **Uživatelé a autoři**
   - `/user/[jméno-autora]` - Profil autora
   - `/user/[jméno-autora]/articles` - Seznam článků daného autora

4. **Závody**
   - `/race` - Hlavní stránka závodů
   - `/race/cyklistickey` - Informace o cyklistickém závodu
   - `/race/bezeckey` - Informace o běžeckém závodu

5. **Uživatelský účet a přihlášení**
   - `/login` - Přihlašovací formulář
   - `/login/submit` - Zpracování přihlašovacích údajů (POST)
   - `/logout` - Odhlášení uživatele
   - `/register` - Registrační formulář
   - `/register/submit` - Zpracování registrace (POST)
   - `/reset-password` - Formulář pro resetování hesla
   - `/reset-password/submit` - Zpracování žádosti o reset hesla (POST)
   - `/reset-password/save` - Uložení nového hesla (POST)

6. **Nahrané soubory**
   - `/uploads/articles/...` - Obrázky článků
   - `/uploads/audio/...` - Zvukové stopy
   - `/uploads/thumbnails/...` - Náhledy obrázků
   - `/uploads/users/...` - Profilové fotografie uživatelů
   - `/uploads/social_icons/...` - Ikony sociálních sítí

## Administrační část

### Přístup a autorizace

Administrační část je zabezpečena systémem přihlášení a řízení přístupu:
- Kontrola autentizace pomocí AuthMiddleware
- Řízení přístupu k různým částem administrace podle role uživatele
- Různé úrovně oprávnění pro redaktory, editory a administrátory
- Superadmin (role 3) má neomezený přístup ke všem funkcím
- Dynamické nastavení přístupových práv ke konkrétním stránkám

### Správa článků

Rozhraní pro správu článků obsahuje:
- **Seznam článků** - přehled všech článků s možností filtrování a řazení
- **Vytváření článků** - formulář pro vytvoření nového článku
- **Editace článků** - možnost úpravy existujících článků
- **Mazání článků** - odstranění článků ze systému
- **Nahrávání médií** - nahrávání obrázků a zvukových stop k článkům
- **Nastavení viditelnosti** - možnost publikovat nebo skrýt článek
- **Plánování publikace** - možnost naplánovat zveřejnění článku na konkrétní datum
- **WYSIWYG editor** - pokročilý editor pro formátování obsahu článků

### Správa kategorií

Nástroje pro organizaci obsahu:
- **Seznam kategorií** - přehled všech kategorií
- **Vytváření kategorií** - přidání nové kategorie
- **Editace kategorií** - úprava existujících kategorií
- **Mazání kategorií** - odstranění kategorií
- **Přiřazení článků ke kategoriím** - propojení mezi články a kategoriemi

### Správa uživatelů

Funkce pro správu uživatelských účtů:
- **Seznam uživatelů** - přehled všech uživatelů systému
- **Editace uživatelů** - úprava údajů o uživatelích
- **Mazání uživatelů** - odstranění uživatelských účtů
- **Správa sociálních sítí** - přidání odkazů na sociální sítě uživatelů
- **Nastavení uživatelského profilu** - úprava osobních údajů a nastavení
- **Změna hesla** - možnost změnit své přihlašovací údaje
- **Správa rolí** - přiřazování uživatelských rolí (redaktor, editor, administrátor)

### Statistiky

Komplexní statistické nástroje:
- **Přehled statistik** - obecný přehled o stavu systému
- **Statistiky článků** - údaje o počtu zobrazení článků
- **Statistiky kategorií** - popularity jednotlivých kategorií
- **Statistiky autorů** - aktivita a popularita autorů
- **Výkonnostní ukazatele** - sledování výkonu webu
- **Top žebříčky** - nejpopulárnější obsah
- **Detaily článků** - podrobné statistiky o konkrétních článcích
- **Časové analýzy** - trendy v popularity obsahu

### Propagace článků

Systém pro zvýraznění vybraných článků:
- **Seznam propagací** - přehled aktuálních a plánovaných propagací
- **Vytváření propagací** - nastavení nové propagace článku
- **Nadcházející propagace** - přehled budoucích propagací
- **Historie propagací** - záznam předchozích propagací
- **Mazání propagací** - odstranění propagace
- **Časově omezené propagace** - nastavení začátku a konce propagace

### Seznam administračních stránek

**Administrační část** - všechny dostupné URL adresy v administraci (přístupné na `/admin/...`):

1. **Základ administrace**
   - `/admin` nebo `/admin/home` - Dashboard administrace

2. **Správa článků**
   - `/admin/articles` - Seznam všech článků
   - `/admin/articles/create` - Vytvoření nového článku
   - `/admin/articles/store` - Uložení nového článku (POST)
   - `/admin/articles/edit/{id}` - Editace článku
   - `/admin/articles/update/{id}` - Aktualizace článku (POST)
   - `/admin/articles/delete/{id}` - Smazání článku
   - `/admin/upload-image` - Endpoint pro nahrávání obrázků

3. **Správa kategorií**
   - `/admin/categories` - Seznam všech kategorií
   - `/admin/categories/create` - Vytvoření nové kategorie
   - `/admin/categories/store` - Uložení nové kategorie (POST)
   - `/admin/categories/edit/{id}` - Editace kategorie
   - `/admin/categories/update/{id}` - Aktualizace kategorie (POST)
   - `/admin/categories/delete/{id}` - Smazání kategorie

4. **Správa uživatelů**
   - `/admin/users` - Seznam všech uživatelů
   - `/admin/users/edit` - Editace uživatele
   - `/admin/users/update` - Aktualizace uživatele (POST)
   - `/admin/users/delete` - Smazání uživatele
   - `/admin/settings` - Nastavení uživatelského profilu
   - `/admin/settings/update` - Aktualizace nastavení (POST)
   - `/admin/social-sites` - Správa odkazů na sociální sítě
   - `/admin/social-sites/save` - Uložení odkazu na sociální síť (POST)
   - `/admin/social-sites/delete` - Smazání odkazu na sociální síť

5. **Statistiky**
   - `/admin/statistics` - Hlavní přehled statistik
   - `/admin/statistics/articles` - Statistiky článků
   - `/admin/statistics/categories` - Statistiky kategorií
   - `/admin/statistics/authors` - Statistiky autorů
   - `/admin/statistics/performance` - Výkonnostní ukazatele
   - `/admin/statistics/views` - Statistiky zobrazení
   - `/admin/statistics/top` - Top žebříčky
   - `/admin/statistics/view` - Zobrazení detailní statistiky
   - `/admin/statistics/article-details/{id}` - Detaily článku
   - `/admin/statistics/category-details/{id}` - Detaily kategorie
   - `/admin/statistics/author-details/{id}` - Detaily autora

6. **Správa propagací**
   - `/admin/promotions` - Seznam propagací
   - `/admin/promotions/create` - Vytvoření nové propagace
   - `/admin/promotions/store` - Uložení nové propagace (POST)
   - `/admin/promotions/upcoming` - Nadcházející propagace
   - `/admin/promotions/history` - Historie propagací
   - `/admin/promotions/delete` - Smazání propagace

7. **Řízení přístupu**
   - `/admin/access-control` - Správa přístupových práv
   - `/admin/access-control/update` - Aktualizace přístupových práv

8. **Uživatelský účet v administraci**
   - `/admin/logout` - Odhlášení z administrace

## Architektura systému

### MVC struktura

Systém využívá architekturu Model-View-Controller:

- **Modely** (`app/Models/`) - třidy pro práci s daty:
  - Article.php - správa článků
  - Category.php - správa kategorií
  - User.php - správa uživatelů
  - Statistics.php - statistiky
  - AccessControl.php - řízení přístupu
  - Promotion.php - propagace článků

- **Controllery** (`app/Controllers/`) - řídící logika aplikace:
  - **Web/** - controllery pro veřejnou část
    - HomeController.php - úvodní stránka
    - ArticleController.php - články
    - CategoryController.php - kategorie
    - UserController.php - uživatelé
  - **Admin/** - controllery pro administraci
    - HomeAdminController.php - dashboard
    - ArticleAdminController.php - správa článků
    - CategoryAdminController.php - správa kategorií
    - UserAdminController.php - správa uživatelů
    - StatisticsAdminController.php - statistiky
    - AccessControlAdminController.php - řízení přístupu
    - PromotionAdminController.php - propagace článků
  - LoginController.php - přihlášení a registrace

- **Views** (`app/Views/`) - šablony pro zobrazení:
  - **Web/** - šablony pro veřejnou část
  - **Admin/** - šablony pro administraci
  - login.php - přihlašovací formulář

### Databázový model

Hlavní tabulky v databázi:
- **clanky** - články (id, nazev, text, datum, viditelnost...)
- **kategorie** - kategorie článků
- **clanky_kategorie** - vazební tabulka mezi články a kategoriemi
- **users** - uživatelé systému
- **views_clanku** - statistiky zobrazení článků
- **promotions** - propagované články
- **social_sites** - odkazy na sociální sítě uživatelů
- **access_control** - nastavení přístupových práv

### Systém směrování (routing)

Systém používá dva hlavní soubory pro směrování:

1. **web/index.php** - router pro veřejnou část
   - Definice rout pro zobrazení článků, kategorií, autorů
   - Ošetření požadavků na nahrané soubory
   - Zpracování parametrů v URL

2. **admin/index.php** - router pro administrační část
   - Kontrola přístupu pomocí AuthMiddleware
   - Definice rout pro správu obsahu
   - Dynamické zpracování parametrů v URL
   - Kontrola oprávnění podle role uživatele

### Middlewares

Systém obsahuje middleware pro kontrolu přihlášení a oprávnění:
- **AuthMiddleware** (`app/Middleware/AuthMiddleware.php`) - hlavní komponenta pro ověření přihlášení uživatele a kontrolu oprávnění
  - Kontrola existence session
  - Ověření role uživatele
  - Dynamická kontrola oprávnění ke stránkám
  - Přesměrování na přihlašovací stránku při neoprávněném přístupu

### Helpery

Pomocné funkce a třídy:
- **TextHelper** (`app/Helpers/TextHelper.php`) - funkce pro práci s textem
  - Formátování textu
  - Generování URL
  - Zkracování textu
- **TimeHelper** (`app/Helpers/TimeHelper.php`) - funkce pro práci s časem
  - Formátování datumů
  - Převody časových formátů
  - Výpočty časových intervalů

## Moduly a funkce

### Články

Kompletní systém pro správu článků:
- Vytváření, editace a mazání článků
- Přiřazování článků do kategorií
- Nahrávání obrázků (náhled, hlavní obrázek)
- Přidávání zvukových stop k článkům
- Počítání zobrazení článků
- Zobrazení souvisejících článků
- Nastavení URL adresy článku
- Plánování publikace článků

### Kategorie

Organizace obsahu podle témat:
- Hierarchická struktura kategorií
- Přiřazování článků do více kategorií
- Filtrace článků podle kategorií
- SEO optimalizované URL adresy kategorií

### Uživatelé a role

Správa uživatelských účtů a oprávnění:
- Registrace a přihlašování
- Resetování hesla
- Přiřazování rolí (redaktor, editor, administrátor)
- Profily autorů s odkazy na sociální sítě
- Správa sociálních profilů
- Omezení přístupu podle role

### Statistiky a sledování

Systém pro analýzu výkonu webu:
- Sledování počtu zobrazení článků
- Statistiky popularity kategorií
- Aktivita autorů
- Výkonnostní metriky
- Grafické znázornění statistik
- Export dat pro další analýzu

### Systém nahrávaní souborů

Správa mediálních souborů:
- Nahrávání obrázků pro články (složka `web/uploads/articles/`)
- Nahrávání profilových obrázků uživatelů (složka `web/uploads/users/`)
- Nahrávání zvukových stop (složka `web/uploads/audio/`)
- Generování náhledů obrázků (složka `web/uploads/thumbnails/`)
- Správa ikon sociálních sítí (složka `web/uploads/social_icons/`)
- Ošetření typů souborů a omezení velikosti

### Bezpečnost

Implementované bezpečnostní mechanismy:
- Hashování hesel
- Ochrana proti SQL injection
- Validace vstupních dat
- Kontrola oprávnění ke stránkám a akcím
- Ochrana nahrávaných souborů

## Technická implementace

### Databázové schéma

Databázové schéma obsahuje definice všech tabulek včetně relací mezi nimi a je uloženo v SQL souboru. Toto schéma definuje strukturu dat pro celou aplikaci.

### Konfigurace

Konfigurační soubory:
- **config/db.php** - nastavení připojení k databázi
- **config/autoloader.php** - konfigurace automatického načítání tříd
- **web/flash_config.php** - konfigurace pro flash zprávy
- **web/refresh_config.php** - konfigurace obnovovacích procesů
