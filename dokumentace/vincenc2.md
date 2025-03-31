# DOKUMENTACE MATURITNÍHO PROJEKTU

## NÁZEV PROJEKTU: Redakční systém pro cyklistický magazín cyklistickey.cz
**AUTOR:** Ondřej Vincenc  
**TŘÍDA:** 4.E  
**ŠKOLNÍ ROK:** 2024/2025  
**VEDOUCÍ PRÁCE:** Ondřej Vincenc

Klíčová slova: 
cyklistický magazín, články o cyklistice, audio články, cyklistickey.cz

## OBSAH:

[ÚVOD] 10
1. [ANALÝZA OBDOBNÝCH WEBOVÝCH STRÁNEK] 13
   1.1. [MTBS.CZ] 13
      1. [Kladné stránky] 13
      2. [Záporné stránky] 14
   1.2. [ROADCYCLING.CZ] 14
      1. [Kladné stránky] 14
      2. [Záporné stránky] 15
   1.3. [MTBIKER.SK] 15
      1. [Kladné stránky] 15
      2. [Záporné stránky] 16
2. [NÁVRH PROJEKTU] 17
   2.1. [CÍLOVÉ SKUPINY] 17
      2.1.1 Začátečníci a příležitostní cyklisti 17
      2.2.1 Rekreační jezdci 18
      2.1.3	Zkušení cyklisti a závodníci 18
      2.1.4	Veřejnost 18
   2.2. [ADMINISTRACE WEBU] 18
      2.2.1	Přístup a autorizace 20
      2.2.2	Dashboard (úvodní přehled) 21
      2.2.3	Správa článků 22
      2.2.4	Správa kategorií 23
      2.2.5	Správa uživatelů 24
      2.2.6	Správa propagací 25
      2.2.7	Statistiky 25
      2.2.8	Nastavení systému 26
   2.3. [DATABÁZE] 27
    2.3.1	Hlavní databázové tabulky 28
    2.3.2	Vztahy mezi tabulkami 28
   2.4. [DESIGN A RESPONZIVITA] 30
    2.4.1	Klíčové principy designu 31
    2.4.2	Responzivní prvky 31
3. [POPIS PROJEKTU] 34
   3.1. [FRONTEND] 34
    3.1.1	Struktura frintendu a navigace 34
    3.1.2	Detail jednotlivých stránek 37
    3.1.3	Responzivní design a adaptace na různá zařízení 42
    3.1.4	Interaktivní prvky a uživatelská zkušenost 42
    3.1.5	SEO optimalizace a rychlost načítání 43
   3.2. [BACKEND] 45
    3.2.1	Architektura backendu 45
    3.2.2	Systém směrování (routing) 46
    3.2.3	Klíčové funkce implementované v backendu 47
    3.2.4	Pro zabezpečení aplikace jsem implementoval následující opatření 47
    3.2.5	Sběr statistik 47
[ZÁVĚR] 48
[SEZNAM PŘÍSTUPOVÝCH ÚDAJŮ]	52
[SEZNAM POUŽITÉ LITERATURY A ZDROJŮ OBRÁZKŮ] 53
[SEZNAM OBRÁZKŮ] 55
[PŘÍLOHY] 56

---

## ÚVOD

V rámci této maturitní práce jsem se zaměřil na vývoj redakčního systému pro cyklistický magazín cyklistickey.cz. Tento projekt pro mě není zcela nový - už dříve jsem vytvořil první verzi webu, která aktuálně běží na adrese magazin.cyklistickey.cz. Hlavní motivací pro vytvoření nové verze byla potřeba odstranit nedostatky původního řešení, které nebylo postaveno na žádném návrhovém vzoru a obsahovalo mnoho chyb. Místo pouhých úprav jsem se rozhodl začít zcela od začátku, poučit se z předchozích chyb a implementovat moderní architektonické přístupy.

> [VLOŽIT OBRÁZEK 1: Logo cyklistickey.cz s moderním designem]

Výsledný systém představuje komplexní řešení pro správu a publikování specializovaného obsahu pro cyklistickou komunitu. Nabízí efektivní nástroje pro redakci a zároveň vytváří příjemné prostředí pro čtenáře. Důraz jsem kladl především na intuitivní ovládání, responzivní design a škálovatelnou architekturu.

V rámci přípravné fáze jsem analyzoval existující řešení (mtbs.cz, roadcycling.cz a mtbiker.sk), což mi umožnilo identifikovat jejich silné stránky i nedostatky. Tyto poznatky jsem následně zohlednil při návrhu vlastního systému, kde kombinuji osvědčené postupy s inovativními prvky, jako je integrace audio obsahu nebo pokročilé statistiky čtenosti.

Z technologického hlediska jsem projekt postavil na moderním stacku, který jsme probírali během studia ve škole: PHP s objektově orientovaným přístupem a MVC architekturou pro backend, MySQL pro databázovou vrstvu, a HTML5, CSS3 a JavaScript s frameworkem Bootstrap 5 pro frontend. Dále jsem implementoval populární knihovnu TinyMCE pro WYSIWYG editor při tvorbě článků a jQuery pro zjednodušení práce s JavaScriptem. Tato kombinace zajišťuje nejen robustnost a udržitelnost kódu, ale také optimální uživatelský zážitek napříč různými zařízeními.

Značnou pozornost jsem během vývoje věnoval bezpečnostním aspektům. Systém implementuje:
* Ochranu proti SQL injection útokům pomocí parametrizovaných dotazů (prepared statements)
* Obranu proti XSS útokům prostřednictvím důsledného escapování výstupu (htmlspecialchars)
* Bezpečné ukládání hesel s využitím hashování (password_hash)
* Propracovaný systém řízení přístupu založený na uživatelských rolích

Mezi klíčové funkce implementovaného systému patří:
* Intuitivní TinyMCE WYSIWYG editor pro vytváření a editaci článků
* Flexibilní kategorizace obsahu s možností přiřazení článku do více kategorií
* Systém pro správu multimediálního obsahu včetně obrázků a audio nahrávek
* Propracované statistické nástroje pro sledování čtenosti jednotlivých článků
* Responzivní design optimalizovaný pro všechny typy zařízení pomocí Bootstrapu
* Modulární architektura umožňující snadné rozšiřování funkcionality
* Systém propagace vybraných článků na hlavní stránce
* SEO optimalizace včetně generování přátelských URL (slugů)

Během vývoje jsem čelil několika technickým výzvám. Největší z nich bylo zvládnutí objektově orientovaného PHP a implementace MVC architektury, což pro mě byly relativně nové koncepty. Další výzvou byla optimalizace výkonu při práci s multimediálním obsahem a implementace efektivního systému řízení přístupu. Tyto problémy jsem vyřešil díky kombinaci důkladného studia, experimentování s různými přístupy a postupného testování.

Pro verzování a zálohování projektu používám GitHub, což mi umožňuje sledovat změny v kódu a v případě potřeby se vrátit k předchozím verzím. Toto řešení také usnadňuje budoucí spolupráci s dalšími vývojáři.

V rámci budoucího rozvoje plánuji implementovat:
* Automatické generování zvukových stop článků pomocí text-to-speech technologie
* Pokročilou ochranu proti CSRF útokům prostřednictvím tokenů pro všechny formuláře
* Systém pro uživatelské komentáře s možností moderace
* Automatickou integraci se sociálními sítěmi pro sdílení nového obsahu
* Propojení s existující mobilní aplikací
* Pokročilé analytické nástroje včetně integrace s Google Analytics
* Rozšíření statistik na všechny stránky webu (aktuálně pouze u článků)
* Implementaci pokročilých SEO prvků jako strukturovaná data, Open Graph a Twitter Cards

Z osobního hlediska mi tento projekt poskytl cennou příležitost propojit teoretické znalosti získané během studia s praktickými dovednostmi potřebnými pro vývoj reálné webové aplikace. Věřím, že zkušenosti získané při tvorbě tohoto systému využiji i ve svém budoucím profesním životě, a možná se mi touto cestou podaří i přivydělávat.

---

## 1 ANALÝZA OBDOBNÝCH WEBOVÝCH STRÁNEK

Analýza obdobných webových stránek je klíčovým krokem při vývoji redakčního systému pro cyklistický magazín. Tato analýza nám umožňuje identifikovat běžné funkce, dobré praktiky a případné nedostatky existujících řešení. Pro účely tohoto projektu jsem se zaměřil výhradně na české a slovenské cyklistické weby, které představují přímou konkurenci a zároveň mohou poskytnout cennou inspiraci. Na základě těchto poznatků můžeme navrhnout systém, který bude kombinovat osvědčené postupy a zároveň nabídne inovativní řešení problémů, se kterými se potýkají existující weby.

### 1.1 MTBS.CZ
**Adresa:** https://www.mtbs.cz

MTBS.cz je specializovaný český web zaměřený na horskou cyklistiku, který nabízí články, recenze, technické tipy a informace o závodech. Tento web má dlouhou historii a velkou komunitu českých MTB jezdců.

> [VLOŽIT OBRÁZEK 2: Screenshot hlavní stránky webu mtbs.cz zobrazující rozvržení obsahu a navigaci]

#### 1.1.1 Kladné stránky
* Silné komunitní prvky, včetně diskusních fór a možnosti sdílení tras
* Dobře strukturovaný katalog recenzí vybavení s možností porovnání
* Přehledná správa událostí a závodů s kalendářem a upozorněními
* Jednoduchá a přímočará navigace s důrazem na obsah
* Pravidelné aktualizace obsahu udržující komunitu aktivní

#### 1.1.2 Záporné stránky
* Zastaralejší design, který nepůsobí moderně ve srovnání s konkurencí
* Omezené mobilní rozhraní, které není plně optimalizované
* Absence pokročilých multimediálních prvků (360° fotografie, interaktivní grafy)
* Méně propracovaný systém propagace vybraných článků na hlavní stránce
* Absence integrace s audio obsahem nebo podcasty
    
### 1.2 ROADCYCLING.CZ
**Adresa:** https://www.roadcycling.cz

RoadCycling je český web specializovaný na silniční cyklistiku, který se zaměřuje především na zpravodajství ze závodů, recenze silničních kol a technické články. Web poskytuje aktuální informace z domácí i světové silniční cyklistické scény.

> [VLOŽIT OBRÁZEK 3: Screenshot hlavní stránky webu roadcycling.cz s ukázkou členění zpravodajského obsahu]

#### 1.2.1 Kladné stránky
* Specializovaný obsah zaměřený výhradně na silniční cyklistiku
* Kvalitní zpravodajství ze závodů s detailním obsahem a fotodokumentací
* Přehledné členění obsahu do tematických sekcí
* Propracované recenze vybavení s důrazem na technické detaily
* Kvalitní fotografie a videa doplňující textový obsah

#### 1.2.2 Záporné stránky
* Méně propracovaný responzivní design na mobilních zařízeních
* Absence pokročilých filtrů pro vyhledávání v archivech článků
* Omezená interaktivita a možnosti zapojení uživatelů
* Jednodušší systém administrace bez pokročilých redakčních funkcí
* Chybějící personalizace obsahu pro registrované uživatele

### 1.3 MTBIKER.SK
**Adresa:** https://www.mtbiker.sk

MTBiker je slovenský web, který má silnou pozici i v české cyklistické komunitě. Zaměřuje se primárně na horskou cyklistiku, ale pokrývá i další disciplíny. Vyniká zejména rozsáhlým bazarem a velmi aktivní komunitou.

> [VLOŽIT OBRÁZEK 4: Screenshot hlavní stránky webu mtbiker.sk s ukázkou komunitních prvků a bazaru]

#### 1.3.1 Kladné stránky
* Unikátní kombinace redakčního obsahu a komunitní platformy
* Rozsáhlý bazar s přehlednou kategorizací a vyhledáváním
* Kvalitní databáze tras s možností filtrování podle obtížnosti a lokality
* Aktivní fórum s rychlými reakcemi na dotazy uživatelů
* Integrované prvky sociální sítě pro cyklisty (profily, fotogalerie)

#### 1.3.2 Záporné stránky
* Místy přehlcené uživatelské rozhraní s velkým množstvím informací
* Komplikovaná navigace pro nové uživatele
* Delší doba načítání některých sekcí díky množství obsahu
* Omezené možnosti přizpůsobení zobrazovaného obsahu
* Absence audio obsahu a pokročilých multimediálních prvků

---

## 2 NÁVRH PROJEKTU

Kapitola "Návrh projektu" se zabývá detailním plánováním redakčního systému pro cyklistický magazín. Zde jsou definovány cílové skupiny uživatelů, struktura administračního rozhraní, databázový model a design webu. Tato kapitola je klíčová pro pochopení, jak byl systém navržen a jaké technologie a postupy byly zvoleny pro jeho implementaci. Následující podkapitoly poskytují podrobné informace o každé z těchto oblastí.

> [VLOŽIT OBRÁZEK 5: Homepage webu]

### 2.1 CÍLOVÉ SKUPINY
Redakční systém je navržen jako budoucí verze webu cyklistickey.cz, který je určen pro širokou veřejnost se zájmem o cyklistiku. Web je koncipován tak, aby si v něm každý našel to své - od začátečníků, kteří se teprve seznamují s cyklistikou, přes rekreační jezdce až po profesionální závodníky.

**Začátečníky a příležitostní cyklisti**
* Základní informace o cyklistice a tipy pro začátečníky
* Rady pro výběr vybavení a kola
* Bezpečnostní doporučení a pravidla silničního provozu
* Inspirace pro první výlety a trasy
* Audio verze článků pro pohodlné poslouchání během jiných aktivit

**Rekreační jezdci**
* Tipy na zajímavé trasy a výlety
* Recenze vybavení a doplňků
* Články o tréninku a kondici
* Zprávy o cyklistických událostech v regionu
* Možnost sdílení vlastních zážitků a zkušeností

**Zkušení cyklisti a závodníci**
* Detailní technické články a analýzy
* Zpravodajství ze závodů a profesionální scény
* Pokročilé tréninkové tipy a metodiky
* Recenze profesionálního vybavení
* Specializované sekce pro různé disciplíny (silniční, horská, dráhová cyklistika)

**Veřejnost**
* Přehledné kategorizace článků podle témat a úrovně
* Možnost vyhledávání podle zájmů a zkušeností
* Audio verze článků pro alternativní konzumaci obsahu
* Aktuální zprávy a novinky ze světa cyklistiky
* Interaktivní prvky pro lepší zapojení do komunity

### 2.1 PŘÍSTUP A AUTORIZACE

#### 2.1.1 Přihlášení a registrace
* Přihlašovací formulář
  - Email a heslo
  - Zapamatování přihlášení
  - Odkaz na reset hesla
* Registrační formulář
  - Základní údaje (email, heslo)
  - Validace formuláře
* Reset hesla
  - Formulář pro zadání emailu
  - Odeslání resetovacího odkazu
  - Formulář pro nové heslo
  - Validace shody hesel

> [VLOŽIT OBRÁZEK 1: Přihlašovací a registrační formuláře]

### 2.2 ADMINISTRACE WEBU
Administrační rozhraní je klíčovou součástí redakčního systému, která umožňuje správu veškerého obsahu a nastavení magazínu. Rozhraní jsem navrhl s důrazem na přehlednost, intuitivnost a efektivitu práce redaktorů a administrátorů. Implementace vychází z aktuálních potřeb redakce cyklistického magazínu a zahrnuje všechny nezbytné funkce pro efektivní správu obsahu.

> [VLOŽIT OBRÁZEK 6: Celkový pohled na administrační rozhraní s hlavním menu a přehledovou stránkou]

**Hlavní sekce administračního rozhraní:**
* **Dashboard** - přehledová stránka s nejdůležitějšími informacemi (počet článků, statistiky návštěvnosti, nejnovější komentáře, rozpracované články)
* **Správa článků** - sekce pro vytváření, editaci, publikování a mazání článků, včetně možnosti nahrávání obrázků a audio souborů
* **Správa kategorií** - možnost vytvářet, upravovat a mazat kategorie pro třídění článků
* **Správa uživatelů** - přidávání, editace a mazání uživatelských účtů, správa rolí a oprávnění
* **Statistiky** - podrobné informace o návštěvnosti jednotlivých článků, kategorií a celého webu
* **Nastavení systému** - nastavení uživatelských rolí a oprávnění, správa přístupových práv k jednotlivým sekcím administrace, možnost vytváření a úpravy rolí s různými úrovněmi oprávnění

### 2.2.1 Popis administračních stránek

Administrační rozhraní je rozděleno do několika hlavních sekcí, které poskytují komplexní nástroje pro správu webu. Každá sekce je optimalizována pro specifické potřeby redakce a administrátorů. Rozhraní je navrženo s důrazem na intuitivnost a efektivitu práce.

> [VLOŽIT OBRÁZEK 6: Celkový pohled na administrační rozhraní s hlavním menu a přehledovou stránkou]

**Dashboard (Úvodní přehled)**
* Přehledová stránka s nejdůležitějšími informacemi
  - Seznam nejnovějších článků (posledních 5)
    * Název článku
    * Datum publikace
    * Rychlý odkaz na úpravu
  - Články z posledních 7 dnů
    * Tabulkový přehled s názvem, datem, autorem
    * Rychlý přístup k úpravě článků
  - Nejčtenější články za posledních 7 dní
    * Interaktivní graf zobrazení pomocí ApexCharts
    * Porovnání čtenosti článků
    * Trendy návštěvnosti

> [VLOŽIT OBRÁZEK 7: Dashboard s přehledem článků a grafy]

**Správa článků**
* Seznam všech článků s možností filtrování a řazení
  - Filtrování podle kategorie, autora, stavu
  - Řazení podle data, názvu, počtu zobrazení
  - Rychlé vyhledávání
* Vytváření nových článků pomocí TinyMCE WYSIWYG editoru
  - Pokročilé formátování textu
  - Vkládání obrázků a tabulek
  - Správa odkazů
  - Vkládání audio souborů
* Editace existujících článků
  - Rychlá úprava metadat
  - Správa verzí článků
  - Historie změn
* Správa multimediálního obsahu
  - Nahrávání a optimalizace obrázků
  - Správa audio souborů
  - Vytváření náhledů
  - Organizace médií do složek
* Plánování publikace článků
  - Nastavení data a času publikace
  - Fronta článků k publikaci
  - Připomenutí o plánovaných publikacích
* Nastavení SEO parametrů
  - Meta popisky
  - Klíčová slova
  - SEO-friendly URL
  - Open Graph tagy
* Správa viditelnosti článků
  - Publikováno/Koncept
  - Plánované publikace
  - Archivované články
* Přiřazování článků do kategorií
  - Vícečetná kategorizace
  - Hlavní kategorie
  - Podkategorie

> [VLOŽIT OBRÁZEK 8: Editor článků s TinyMCE a nástroji pro správu obsahu]

**Správa kategorií**
* Seznam všech kategorií
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (název, URL)
  - Rychlé akce (úprava, smazání)
* Vytváření nových kategorií
  - Formulář pro název kategorie
  - Automatické generování URL
* Editace kategorií
  - Úprava názvu kategorie
  - Správa URL

> [VLOŽIT OBRÁZEK 9: Správa kategorií - přehled a editor]

**Správa uživatelů**
* Přihlášení a registrace
  - Přihlašovací formulář
    * Email a heslo
    * Zapamatování přihlášení
    * Odkaz na reset hesla
  - Registrační formulář
    * Základní údaje (email, heslo)
    * Validace formuláře
  - Reset hesla
    * Formulář pro zadání emailu
    * Odeslání resetovacího odkazu
    * Formulář pro nové heslo
    * Validace shody hesel

* Správa uživatelů v administraci
  - Seznam uživatelů
    * Tabulkový přehled s řazením a filtrováním
    * Základní informace (ID, jméno, příjmení, email)
    * Role uživatele (Uživatel, Moderátor, Editor, Administrátor)
    * Rychlé akce (úprava, smazání)
  - Editace uživatele
    * Základní údaje (email, jméno, příjmení)
    * Nastavení role
    * Popis uživatele (TinyMCE editor)
  - Nastavení vlastního účtu
    * Osobní údaje
    * Profilová fotka
    * Záhlaví profilu
    * Popis profilu
    * Správa sociálních sítí
      - Přidávání/odebírání sítí
      - Nastavení odkazů
      - Validace duplicit

> [VLOŽIT OBRÁZEK 10: Správa uživatelů - přehled a editor]

**Správa uživatelů v roli administrátora**
* Seznam uživatelů
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (ID, jméno, příjmení, email)
  - Role uživatele (Uživatel, Moderátor, Editor, Administrátor)
  - Rychlé akce (úprava, smazání)
* Editace uživatele
  - Základní údaje (email, jméno, příjmení)
  - Nastavení role
  - Popis uživatele (TinyMCE editor)

> [VLOŽIT OBRÁZEK 10: Správa uživatelů - přehled a editor]

**Nastavení vlastního účtu**
* Osobní údaje
  - Jméno a příjmení
  - Email
  - Popis (TinyMCE editor)
* Správa profilových obrázků
  - Profilová fotka (nahrání, náhled)
  - Záhlaví profilu (nahrání, náhled)
* Správa sociálních sítí
  - Přidávání/odebírání sociálních sítí
  - Nastavení odkazů na profily
  - Validace duplicitních sítí

> [VLOŽIT OBRÁZEK 11: Nastavení vlastního účtu]

**Statistiky**
* Přehled statistik
  - Celkový počet zobrazení
  - Počet článků
  - Počet kategorií
  - Průměrné zobrazení na článek
* Statistiky článků
  - Nejčtenější články
  - Trendy zobrazení
  - Rozložení zobrazení
* Statistiky kategorií
  - Počet zobrazení podle kategorií
  - Počet článků v kategoriích
  - Trendy kategorií v čase
* Statistiky autorů
  - Počet článků podle autorů
  - Celkový počet zobrazení
  - Průměrné zobrazení na autora
* Statistiky zobrazení
  - Filtrování podle časového období
  - Graf zobrazení v čase
  - Nejnavštěvovanější dny
  - Kalendářní tepelná mapa

> [VLOŽIT OBRÁZEK 11: Přehled statistik s grafy a tabulkami]

**Správa propagací**
* Seznam propagací
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (název, článek, datum)
  - Rychlé akce (úprava, smazání)
* Vytváření nových propagací
  - Výběr článku k propagaci
  - Nastavení data a času
  - Priorita propagace
* Editace propagací
  - Úprava základních informací
  - Změna data a času
  - Změna priority

> [VLOŽIT OBRÁZEK 12: Správa propagací - přehled a editor]

**Nastavení systému**
* Správa přístupových práv
  - Nastavení oprávnění pro jednotlivé role
    * Moderátor
    * Editor
    * Administrátor
  - Přehled stránek a jejich oprávnění
  - Rychlá úprava přístupových práv
* Základní nastavení
  - Konfigurace webu
  - Nastavení e-mailů
  - Správa uživatelů

> [VLOŽIT OBRÁZEK 13: Nastavení systému s přehledem přístupových práv]

Každá sekce administračního rozhraní je navržena s důrazem na:
* Intuitivní ovládání
  - Konzistentní design
  - Logická navigace
  - Rychlé akce
* Rychlý přístup k často používaným funkcím
  - Zkratky kláves
  - Rychlé menu
  - Nedávno používané
* Přehledné zobrazení dat
  - Filtry a vyhledávání
  - Řazení a seskupování
  - Export dat
* Možnost filtrování a vyhledávání
  - Pokročilé filtry
  - Fulltextové vyhledávání
  - Uložené filtry
* Responzivní design pro všechny typy zařízení
  - Mobilní optimalizace
  - Adaptivní layout
  - Touch-friendly ovládání

> [VLOŽIT OBRÁZEK 14: Responzivní design administrace na různých zařízeních]

### 2.3 DATABÁZE
Databázovou strukturu projektu jsem navrhl s ohledem na efektivní ukládání a správu veškerého obsahu cyklistického magazínu. Pro implementaci jsem zvolil relační databázi MySQL, se kterou jsme pracovali během studia a která poskytuje dobrou kombinaci výkonu, spolehlivosti a flexibility.

> [VLOŽIT OBRÁZEK 8: ER diagram databáze zobrazující vztahy mezi hlavními tabulkami systému]

**Hlavní databázové tabulky:**
* **clanky** - uchovává informace o všech článcích (id, nazev, obsah, datum, viditelnost, user_id, nahled_foto, url, audio)
* **kategorie** - obsahuje seznam kategorií (id, nazev_kategorie, url)
* **clanky_kategorie** - vazební tabulka pro vztah M:N mezi články a kategoriemi (id_clanku, id_kategorie)
* **users** - informace o uživatelích systému (id, email, heslo, name, surname, role, profil_foto, zahlavi_foto, popis)
* **views_clanku** - statistiky zobrazení článků (id_clanku, pocet)
* **admin_access** - konfigurace přístupových práv k různým částem administrace (page, role_1, role_2)
* **promotions** - propagované články (id, clanek_id, od_data, do_data)
* **social_sites** - odkazy na sociální sítě autorů (id, user_id, nazev, url, ikona)

**Vztahy mezi tabulkami:**
* Články mohou patřit do více kategorií a kategorie mohou obsahovat více článků - tohoto vztahu M:N jsem dosáhl pomocí vazební tabulky clanky_kategorie
* Každý článek má přiřazeného autora - uživatele (vztah 1:N mezi users a clanky)
* Každý článek má svůj záznam v tabulce views_clanku pro sledování počtu zobrazení (vztah 1:1)
* Uživatelé mohou mít více odkazů na sociální sítě (vztah 1:N mezi users a social_sites)
* Články mohou být propagované v různých časových obdobích (vztah 1:N mezi clanky a promotions)

Pro práci s databází jsem implementoval vlastní databázovou vrstvu, která zajišťuje připojení k databázi, provádění dotazů a zpracování výsledků. Použil jsem přístup s připravenými dotazy (prepared statements) pro zajištění bezpečnosti:

```php
// Příklad výběru článků podle kategorie
public function getArticlesByCategory($categoryId) {
    $query = "SELECT c.* FROM clanky c 
              INNER JOIN clanky_kategorie ck ON c.id = ck.id_clanku
              WHERE ck.id_kategorie = ? AND c.viditelnost = 1
              ORDER BY c.datum DESC";
    
    $stmt = $this->db->prepare($query);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
```

Struktura tabulky clanky zahrnuje všechny potřebné atributy pro správu článků, včetně polí pro ukládání cesty k nahraným souborům (nahled_foto pro úvodní obrázek a audio pro zvukovou stopu). Pole viditelnost určuje, zda je článek publikován a zobrazuje se na webu, nebo je uložen jako koncept. Pole url uchovává SEO-friendly URL adresu článku odvozenou z jeho názvu.

Pro generování SEO-friendly URL (slugů) jsem vytvořil funkci, která převádí název článku nebo kategorie na řetězec vhodný pro URL:

```php
public function createSlug($text) {
    // Převod na malá písmena
    $text = mb_strtolower($text, 'UTF-8');
    
    // Nahrazení diakritiky
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    
    // Odstranění speciálních znaků
    $text = preg_replace('/[^a-z0-9]/', '-', $text);
    
    // Odstranění vícenásobných pomlček
    $text = preg_replace('/-+/', '-', $text);
    
    // Odstranění pomlček na začátku a konci
    $text = trim($text, '-');
    
    return $text;
}
```

Databázi jsem navrhl s ohledem na výkon a škálovatelnost. Pro optimalizaci dotazů jsem vytvořil vhodné indexy na často používaných polích (id, user_id, url). Referenční integrita je zajištěna pomocí cizích klíčů, které zabraňují vzniku nekonzistentních dat (např. nelze smazat kategorii, která obsahuje články, bez předchozího ošetření).

### 2.4 DESIGN A RESPONZIVITA
Design webového magazínu jsem navrhl s důrazem na čistotu, přehlednost a snadnou orientaci. Vizuální styl odpovídá zaměření na cyklistiku - využívá dynamických prvků, sportovní barevné schéma a dostatek prostoru pro kvalitní fotografie.

> [VLOŽIT OBRÁZEK 9: Ukázka barevného schématu a typografie použité na webu]

**Klíčové principy designu:**
* Minimalistický přístup s důrazem na obsah - čisté pozadí, kontrastní typografie pro snadnou čitelnost
* Hierarchické uspořádání prvků na stránce - důležité informace a hlavní články jsou zvýrazněny velikostí a umístěním
* Konzistentní vizuální prvky napříč celým webem - jednotný styl tlačítek, odkazů, nadpisů a menu
* Intuitivní navigace - přehledné horizontální menu s hlavními kategoriemi a doplňkové vertikální menu pro další funkce
* Efektivní využití "bílého prostoru" pro oddělení jednotlivých obsahových bloků

Responzivita byla jedním z klíčových požadavků, protože značná část návštěvníků přistupuje k webu z mobilních zařízení. Pro implementaci responzivity jsem využil framework Bootstrap 5, který poskytuje flexibilní grid systém a předdefinované komponenty pro různé velikosti obrazovek.

Responzivní design jsem implementoval s využitím následujících breakpointů, které odpovídají standardním velikostem zařízení:
* Extra small (< 576px) - mobilní telefony
* Small (≥ 576px) - větší mobilní telefony
* Medium (≥ 768px) - tablety
* Large (≥ 992px) - notebooky
* Extra large (≥ 1200px) - desktopy
* XXL (≥ 1400px) - velké desktopy

> [nejspis to dat do prilohy]
> [VLOŽIT OBRÁZEK 10: Ukázka responzivního designu na desktopu]
> [VLOŽIT OBRÁZEK 11: Ukázka responzivního designu na tabletu]
> [VLOŽIT OBRÁZEK 12: Ukázka responzivního designu na mobilu]

**Responzivní prvky, které jsem implementoval:**
* Fluidní layout, který se automaticky přizpůsobuje šířce obrazovky pomocí Bootstrap grid systému:
```html
<div class="container">
  <div class="row">
    <div class="col-md-8 col-lg-9">
      <!-- Hlavní obsah -->
    </div>
    <div class="col-md-4 col-lg-3">
      <!-- Postranní panel -->
    </div>
  </div>
</div>
```
* Flexibilní obrázky pomocí CSS třídy `img-fluid`, která zajišťuje, že obrázky nikdy nepřesáhnou šířku svého kontejneru:
```css
.img-fluid {
  max-width: 100%;
  height: auto;
}
```
* Přeskupení obsahu na menších obrazovkách - např. změna rozložení ze 3 sloupců na 1 pro mobilní telefony
* Přizpůsobení navigačního menu - na malých obrazovkách se horizontální menu transformuje na "hamburger menu"
* Optimalizace formulářů a interaktivních prvků pro dotykové ovládání - větší tlačítka a ovládací prvky

Pro zajištění konzistentního vzhledu napříč různými prohlížeči jsem použil normalizační CSS, které sjednocuje výchozí styly. CSS styly jsem strukturoval do několika souborů podle funkčnosti:
* `cyklistickey_cz.css` - základní styly pro celý web
* `main-page.css` - styly pro úvodní stránku
* `clanek.css` - styly pro stránku s detailem článku
* `kategorie.css` - styly pro výpis článků v kategorii
* `navbar-web.css` - styly pro navigační menu
* `footer.css` - styly pro patičku webu
* `autor_clanku.css` - styly pro profil autora
* `kontakt.css` - styly pro kontaktní stránku
* `race.css` a `race-main.css` - styly pro stránky závodů
* `admin.css` a `admin-dashboard.css` - styly pro administrační rozhraní

Testování responzivity jsem prováděl na různých zařízeních a v různých prohlížečích, aby byla zajištěna konzistentní uživatelská zkušenost bez ohledu na způsob přístupu k webu. Díky důslednému použití responzivních technik je zajištěno, že čtenáři mohou pohodlně konzumovat obsah magazínu kdekoli a kdykoli.

---

## 3 POPIS PROJEKTU

Projekt cyklistickey.cz představuje plnohodnotný redakční systém pro správu a publikování obsahu zaměřeného na cyklistiku. Jedná se o komplexní webovou aplikaci rozdělenou na veřejnou část dostupnou běžným návštěvníkům a administrační rozhraní určené pro redaktory a správce obsahu.

Hlavním cílem projektu je poskytnout uživatelsky přívětivou platformu pro publikování cyklistického obsahu s důrazem na moderní design, intuitivní navigaci a optimální uživatelský zážitek. Systém je navržen tak, aby umožňoval efektivní kategorizaci článků, správu multimediálního obsahu a poskytoval pokročilé nástroje pro sledování čtenosti a popularity obsahu.

Veřejná část webu nabízí přehlednou prezentaci publikovaných článků, jejich filtrování podle kategorií, zobrazení profilů autorů a další funkce běžné pro moderní webový magazín. Administrační část pak poskytuje komplexní nástroje pro správu obsahu, uživatelů, kategorií a další aspekty redakčního systému.

Z technologického hlediska je projekt postaven na architektuře MVC (Model-View-Controller) s využitím PHP pro serverovou část, MySQL pro databázovou vrstvu a HTML, CSS a JavaScript pro klientskou část. Důraz je kladen na responzivní design, optimalizaci pro vyhledávače a zabezpečení proti běžným typům útoků.

### 3.1 FRONTEND
Frontend projektu představuje veřejnou část webového magazínu, se kterou interagují běžní čtenáři. Navrhl jsem ho tak, aby poskytoval rychlý a intuitivní přístup k obsahu, zajímavý vizuální zážitek a optimální funkčnost na všech zařízeních.

> [VLOŽIT OBRÁZEK 13: Hlavní stránka webu s označením klíčových komponent rozhraní]

#### 3.1.1 Struktura frontendu a navigace

Frontend magazínu je strukturován tak, aby nabízel intuitivní navigaci mezi jednotlivými částmi obsahu a zároveň uživatele vizuálně zaujal. Každá stránka obsahuje následující klíčové prvky:

**Hlavička (Header):**
* Logo magazínu s odkazem na úvodní stránku
* Hlavní navigační menu s kategoriemi
* Přihlašovací odkaz nebo informace o přihlášeném uživateli
* Ikony pro přístup k sociálním sítím magazínu

> [VLOŽIT OBRÁZEK 14: Detail hlavičky webu s navigačním menu a vyhledávacím polem]

Hlavička je fixně umístěna v horní části obrazovky a zůstává viditelná i při scrollování, což zajišťuje, že uživatel má vždy přístup k hlavní navigaci. Na mobilních zařízeních se menu transformuje do kompaktního "hamburger" tlačítka, které po kliknutí rozbalí plnohodnotné menu.

> [VLOŽIT OBRÁZEK 15: Screenshot mobilního menu po rozbalení hamburger ikony]

**Hlavní obsah (Main content):**
* Úvodní stránka - zobrazuje nejnovější a propagované články, přehled kategorií a stručné statistiky
* Stránky kategorií - výpis článků patřících do konkrétní kategorie s možností filtrování a řazení
* Seznam všech článků - kompletní přehled publikovaných článků
* Detail článku - zobrazení kompletního obsahu článku včetně multimediálních prvků a přehrávače audia
* Profil autora - informace o autorovi s přehledem jeho článků a odkazy na sociální sítě
* Kontaktní stránka - kontaktní informace redakce
* Stránky závodů - informace o pořádaných závodech (Cyklistickey, Bezeckey)
* Přihlašovací a registrační formuláře - možnost vytvoření účtu a přihlášení do systému

**Patička (Footer):**
* Odkazy na důležité sekce webu
* Kontaktní informace
* Informace o autorských právech
* Odkazy na sociální sítě

> [VLOŽIT OBRÁZEK 27: Detail patičky webu se všemi navigačními odkazy a kontaktními informacemi]

#### 3.1.2 Detail jednotlivých stránek

**Úvodní stránka:**
Úvodní stránka slouží jako rozcestník a zároveň jako výkladní skříň nejnovějšího obsahu. Je rozdělena do několika sekcí:

1. **Hero sekce** - dominantní část stránky zobrazující nejnovější článek s velkým obrázkem na pozadí a názvem. Jedná se o první článek získaný metodou `getLatestArticles()` v kontroleru.

> [VLOŽIT OBRÁZEK 28: Hero sekce úvodní stránky s nejnovějším článkem]

2. **Nejnovější články** - sekce zobrazující další nejnovější publikované články (druhý až čtvrtý nejnovější článek) ve formě karet s náhledovým obrázkem, kategorií, nadpisem a datem publikace. Tyto články jsou získány metodou `getLatestArticles(4, 0)` v kontroleru.

3. **Kategorie** - přehled všech kategorií, kde každá obsahuje své nejnovější články seřazené podle data publikace. Implementováno metodou `getCategoriesWithArticlesSorted()` v kontroleru.

4. **O nás sekce** - sekce představující redakční tým s krátkým popisem a odkazem na stránku "O nás" pro více informací.

> [VLOŽIT OBRÁZEK 29: Sekce nejnovějších článků na úvodní stránce]

Nahradit s:

**Úvodní stránka:**
Úvodní stránka slouží jako rozcestník a zároveň jako výkladní skříň nejnovějšího obsahu. Je rozdělena do několika sekcí:

1. **Hero sekce** - dominantní část stránky zobrazující nejnovější článek s velkým obrázkem na pozadí a názvem. Jedná se o první článek získaný metodou `getLatestArticles()` v kontroleru.

> [VLOŽIT OBRÁZEK 28: Hero sekce úvodní stránky s nejnovějším článkem]

2. **Nejnovější články** - sekce zobrazující další nejnovější publikované články (druhý až čtvrtý nejnovější článek) ve formě karet s náhledovým obrázkem, kategorií, nadpisem a datem publikace. Tyto články jsou získány metodou `getLatestArticles(4, 0)` v kontroleru.

3. **Kategorie** - přehled všech kategorií, kde každá obsahuje své nejnovější články seřazené podle data publikace. Implementováno metodou `getCategoriesWithArticlesSorted()` v kontroleru.

4. **O nás sekce** - sekce představující redakční tým s krátkým popisem a odkazem na stránku "O nás" pro více informací.

> [VLOŽIT OBRÁZEK 29: Sekce nejnovějších článků na úvodní stránce]

Pro implementaci hero sekce jsem použil statický banner, který zobrazuje nejnovější článek s velkým obrázkem na pozadí a názvem článku. Toto řešení je jednoduché, přehledné a rychle se načítá. Implementace v souboru **app/Views/Web/home/index.php**:

```php
<!-- Hlavní banner - nejnovější článek -->
<?php
// První článek - banner
if (!empty($articles) && count($articles) > 0) {
    $row = $articles[0];
?>
    <div class="pinned">
        <div class="image" style="background-image: linear-gradient(to top, rgba(0, 0, 15, 1) 0%, rgba(0, 0, 15, 0) 50%), 
                        url('/uploads/thumbnails/velke/<?php echo !empty($row["nahled_foto"]) ? htmlspecialchars($row["nahled_foto"]) : 'noimage.png'; ?>');">
            <div class="pinned-body">
                <h5><?php echo htmlspecialchars($row["nazev"]); ?></h5>
                <div class="cist-clanek">
                    <a href="/article/<?php echo htmlspecialchars($row['url']); ?>/">ČÍST ČLÁNEK <i class="fa-solid fa-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
<?php 
}
?>
```

**Stránka kategorie:**
Každá kategorie má svou vlastní stránku, která zobrazuje články patřící do dané kategorie. Stránka obsahuje:

1. **Záhlaví kategorie** - název kategorie
2. **Seznam článků** - výpis článků dané kategorie ve formě karet

> [VLOŽIT OBRÁZEK 30: Záhlaví kategorie s názvem a popisem]

> [VLOŽIT OBRÁZEK 31: Filtrační nástroje pro řazení článků v kategorii]

Implementován je základní výpis článků v kategorii bez pokročilých filtračních nástrojů. Stránka s výpisem kategorie je přehledná a snadno použitelná, přičemž navigace mezi články je intuitivní. Implementace v souboru **app/Views/Web/category/categoryDetail.php**:

```php
<div class="container-clanky">
    <?php foreach ($articles as $result) : ?>
        <a href="/article/<?php echo htmlspecialchars($result['url']); ?>/">
            <div class="card">
                <img loading="lazy" src="/uploads/thumbnails/male/<?php echo !empty($result["nahled_foto"]) ? htmlspecialchars($result["nahled_foto"]) : 'noimage.png'; ?>" alt="<?php echo htmlspecialchars($result["nazev"]); ?>">
                <div class="card-body">
                    <div class="card-content-left">
                        <h5><?php echo htmlspecialchars($result["nazev"]); ?></h5>
                        
                        <span class="datum">
                            <?php 
                                // Použití TimeHelper pro získání relativního času
                                echo \App\Helpers\TimeHelper::getRelativeTime($result["datum"]);
                            ?>
                        </span>
                        
                        <div class="clanek-excerpt">
                            <?php 
                                // Zkrácený výpis textu článku - pokud existuje
                                if (!empty($result["obsah"])) {
                                    echo substr(strip_tags($result["obsah"]), 0, 400) . "...";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
```

**Detail článku:**
Stránka s detailem článku je nejdůležitější částí frontendu, protože zde uživatelé tráví nejvíce času. Je navržena s důrazem na čitelnost a pohodlnou konzumaci obsahu. Obsahuje:

1. **Hlavička článku** - název článku, perex, informace o autorovi a datum publikace
2. **Hlavní obrázek** - dominantní ilustrační fotografie k článku
3. **Audio přehrávač** - pokud článek obsahuje audio verzi, zobrazí se přehrávač umožňující poslouchání obsahu
4. **Obsah článku** - samotný text článku s formátováním, obrázky, videi a dalšími multimediálními prvky
5. **Související články** - návrhy dalších článků, které by mohly čtenáře zajímat
6. **Informace o autorovi** - fotografie autora, krátký bio a odkazy na jeho další články

> [VLOŽIT OBRÁZEK 32: Hlavička článku s názvem, perexem a informacemi o autorovi]

> [VLOŽIT OBRÁZEK 33: Sekce se souvisejícími články na  stránky článku]

**Profil autora:**
Stránka profilu autora poskytuje informace o konkrétním členovi redakčního týmu a přehled jeho publikovaných článků. Obsahuje:

1. **Fotografie autora** - profesionální portrét autora
2. **Biografické informace** - jméno, specializace, krátký životopis
3. **Odkazy na sociální sítě** - propojení s autorovou online přítomností
4. **Seznam publikovaných článků** - přehled všech článků daného autora s možností filtrování podle kategorií
5. **Statistiky** - počet publikovaných článků, celkový počet zobrazení atp.

> [VLOŽIT OBRÁZEK 34: Profil autora s biografickými informacemi a odkazy na sociální sítě]

> [VLOŽIT OBRÁZEK 35: Seznam publikovaných článků autora s možností filtrování]

**Stránka redakce:**
Stránka s přehledem redakce poskytuje návštěvníkům informace o autorském týmu magazínu. Zobrazuje seznam všech aktivních autorů s jejich fotografiemi, krátkým popisem a odkazy na jejich profily. Na stránce jsou autoři řazeni podle počtu publikovaných článků a jejich aktivitě. Stránka obsahuje:

1. **Úvodní text** - představení redakčního týmu a filozofie magazínu
2. **Seznam autorů** - přehledné karty s fotografiemi a základními informacemi
3. **Odkazy na profily** - každá karta obsahuje odkaz na detailní profil autora
4. **Kontaktní informace** - e-mailové adresy na redakci pro zájemce o spolupráci

> [VLOŽIT OBRÁZEK 36: Přehled stránky redakce se seznamem autorů]

**Kontaktní stránka:**
Kontaktní stránka poskytuje návštěvníkům informace o týmu Cyklistickey a jejich projektech. Je implementována jako statická stránka v souboru **app/Views/Web/home/kontakt.php**. Stránka obsahuje následující sekce:

1. **O nás** - představení projektu Cyklistickey a jeho vývoje od Instagramového profilu po komplexní online platformu
2. **Náš tým** - fotografie a jména členů týmu rozdělených do kategorií (CEO, redakce, IT, kamera, BikeLab)
3. **Podporujeme** - informace o partnerství s cyklistickými událostmi a organizacemi
4. **Pozornost médií** - zmínky o projektu v médiích včetně odkazu na článek v iDnes
5. **Naši partneři** - seznam a loga partnerských organizací (FOR BIKES, Road Classics, KOLO PRO ŽIVOT, Prague Bike Fest)

> [VLOŽIT OBRÁZEK 37: Kontaktní stránka s formulářem a mapou]

**Stránka závodů:**
Sekce závodů poskytuje přehled nadcházejících cyklistických událostí a závodů. Je implementována jako statická stránka v souboru **app/Views/Web/race/index.php**. Stránka obsahuje:

1. **Kalendář závodů** - přehledný kalendář s možností filtrování podle data, typu závodu a lokality
2. **Detail závodu** - informace o jednotlivých závodech včetně trasy, profilu, pravidel a registrace
3. **Výsledky závodů** - archiv výsledků z proběhlých závodů s možností filtrování
4. **Fotogalerie** - fotografie z jednotlivých závodů organizované podle ročníků


> [VLOŽIT OBRÁZEK 38: Stránka závodů s kalendářem a filtry]

#### 3.1.3 Responzivní design a adaptace na různá zařízení

Responsivita je klíčovým aspektem frontendu, protože přibližně 60% návštěvníků přistupuje k webu prostřednictvím mobilních zařízení. Implementoval jsem komplexní responzivní přístup, který zajišťuje optimální zobrazení na všech typech zařízení:

Pro implementaci responzivity jsem použil kombinaci vlastních Media Queries a Bootstrap 5 grid systému. Projekt využívá jak vlastní breakpointy, tak standardní Bootstrap 5 breakpointy (xs < 576px, sm ≥ 576px, md ≥ 768px, lg ≥ 992px, xl ≥ 1200px, xxl ≥ 1400px).

Tento přístup zajišťuje, že web se přizpůsobuje nejen různým velikostem obrazovek, ale také různým orientacím zařízení (portrét/krajina).

Implementoval jsem také koncept "mobile-first", kdy jsem nejprve navrhl a implementoval mobilní verzi a poté postupně přidával funkce a úpravy pro větší obrazovky. Tento přístup zajišťuje, že i základní mobilní verze poskytuje plnohodnotný přístup ke všem důležitým funkcím.

> [VLOŽIT OBRÁZEK 42: Screenshot z Chrome DevTools ukazující testování responzivity na různých zařízeních]

#### 3.1.4 Interaktivní prvky a uživatelská zkušenost

Frontend obsahuje řadu interaktivních prvků, které zlepšují uživatelskou zkušenost a činí konzumaci obsahu zajímavější a pohodlnější:

**Audio přehrávač** - umožňuje poslech článku ve formě audia, což je užitečné zejména pro uživatele, kteří chtějí konzumovat obsah během jiných aktivit. Přehrávač podporuje:
* Přehrávání/pozastavení
* Skok vpřed/vzad
* Regulaci hlasitosti
* Zobrazení aktuálního času a celkové délky
* Uložení pozice poslechu pro pozdější návrat

> [VLOŽIT OBRÁZEK 43: Detail audio přehrávače s ovládacími prvky]

**Lazy loading** - technika, která odkládá načítání obrázků a dalších náročných prvků mimo viditelnou oblast, což výrazně zrychluje načítání stránky. Implementoval jsem nativní HTML lazy loading pomocí atributu `loading="lazy"` u standardních `<img>` tagů, který je nyní podporován všemi moderními prohlížeči a zajišťuje, že obrázky se načtou až ve chvíli, kdy se uživatel přiblíží k místu jejich zobrazení. Obrázky jsou ukládány v různých velikostech v adresářích "male" a "velke", čímž je zajištěno, že se načítají v odpovídající velikosti podle typu stránky.

**Animace a přechody** - subtilní animace a přechody jsou použity pro zlepšení vizuálního zážitku a poskytnutí vizuální zpětné vazby na akce uživatele. Například tlačítka mění barvu při najetí myší, články se jemně zvětšují při hover efektu a přechody mezi stránkami jsou plynulé díky CSS animacím.

#### 3.1.5 SEO optimalizace a rychlost načítání

Frontend je navržen s důrazem na optimalizaci pro vyhledávače a rychlost načítání, což jsou klíčové faktory pro úspěch online magazínu:

**SEO optimalizace:**
* Každá stránka má unikátní, popisný title tag a meta description
* Sémantická struktura HTML s využitím HTML5 elementů jako article, section, nav
* Strukturovaná data (JSON-LD) pro lepší zobrazení ve výsledcích vyhledávání
* Automatické generování alt tagů pro obrázky
* SEO-friendly URL struktury (např. /kategorie/nazev-clanku)
* Implementace kanonických URL pro prevenci duplicitního obsahu
* Optimalizovaná struktura nadpisů (H1, H2, H3...) v souladu s SEO best practices

> [VLOŽIT OBRÁZEK 43: Ukázka implementace strukturovaných dat v detailu článku]

V kontrolerech je implementováno dynamické nastavování meta tagů a dalších SEO prvků podle obsahu stránky:

```php
// Ukázka nastavení SEO proměnných v kontroleru článku
// Tyto proměnné se pak použijí v šabloně base.php
$title = $article->nazev . ' | Cyklistickey.cz';
$description = substr(strip_tags($article->text), 0, 160);
$ogTitle = $article->nazev;
$ogDescription = $description;
$ogImage = 'https://cyklistickey.cz/uploads/articles/' . $article->nahled_foto;
$ogUrl = 'https://cyklistickey.cz/clanek/' . $article->url;
$canonicalUrl = 'https://cyklistickey.cz/clanek/' . $article->url;

// Strukturovaná data pro lepší zobrazení ve vyhledávačích
$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->nazev,
    'datePublished' => date('c', strtotime($article->datum)),
    'author' => [
        '@type' => 'Person',
        'name' => $article->author_name . ' ' . $article->author_surname
    ]
];
```

**Optimalizace rychlosti načítání:**
* Minifikace a sloučení CSS a JavaScript souborů
* Komprese obrázků s minimální ztrátou kvality
* Implementace lazy loadingu pro obrázky a videa
* Využití browser caching pro statické soubory
* Server-side caching často požadovaných stránek
* Asynchronní načítání méně důležitých skriptů
* Prioritizace zobrazení obsahu visible viewport

> [VLOŽIT OBRÁZEK 44: Screenshot výsledků testování rychlosti načítání v nástroji Google PageSpeed Insights]

Rychlost načítání jsem průběžně testoval pomocí nástrojů jako Google PageSpeed Insights a Lighthouse, což mi umožnilo identifikovat a eliminovat potenciální problémy.

**Technologická implementace frontendové části využívá:**
* HTML5 pro strukturu stránek s využitím sémantických prvků (header, nav, main, article, footer)
* CSS3 a Bootstrap 5 pro stylování a responzivitu
* JavaScript a jQuery pro interaktivní prvky a dynamické načítání obsahu
* PHP šablony pro generování HTML na serveru s využitím MVC architektury

> [VLOŽIT OBRÁZEK 45: Screenshot části zdrojového kódu HTML s použitými sémantickými elementy]

Při vývoji frontendové části jsem se snažil o dodržování moderních principů webového vývoje:
* Progresivní vylepšování (Progressive Enhancement) - základní funkce fungují i bez JavaScriptu
* Mobile-first přístup - design optimalizovaný primárně pro mobilní zařízení
* Přístupnost (Accessibility) - použití správných HTML elementů a atributů pro čtečky obrazovky
* Optimalizace výkonu - minimalizace HTTP požadavků, komprese obsahu, lazy loading

Každá stránka je optimalizována pro rychlé načítání - obrázky jsou komprimovány a načítány v odpovídající velikosti podle zařízení, JavaScript je minimalizován a CSS optimalizován. Implementoval jsem lazy-loading, který zajišťuje, že náročnější prvky (jako obrázky mimo viditelnou oblast nebo audio přehrávače) se načítají až ve chvíli, kdy se uživatel dostane do jejich blízkosti.

Zvláštní pozornost jsem věnoval přehrávači audio obsahu, který je plně integrován do stránky článku. Přehrávač jsem implementoval pomocí standardního HTML5 audio elementu s atributem `controls`, který poskytuje nativní ovládací prvky prohlížeče.

---

## 3.2 BACKEND
Backend projektu zajišťuje veškerou logiku aplikace, komunikaci s databází a generování obsahu pro frontend. Implementoval jsem ho v PHP s využitím objektově orientovaného přístupu a MVC architektury pro lepší organizaci kódu a snadnou údržbu.

> [VLOŽIT OBRÁZEK 16: Diagram MVC architektury aplikace znázorňující vztahy mezi modely, kontrolery a pohledy]

**Architektura backendu:**
* **Modely (App/Models)** - reprezentují datové entity a zajišťují komunikaci s databází
  - Article.php - správa článků včetně kategorií a statistik
  - Category.php - operace s kategoriemi
  - User.php - správa uživatelů a autentizace
  - AccessControl.php - řízení přístupu a oprávnění
  - Statistics.php - sběr a analýza statistických dat
  - Promotion.php - správa propagovaných článků
* **Kontrolery (App/Controllers)** - zpracovávají požadavky uživatelů a propojují modely s pohledy
  - Admin/ - kontrolery pro administrační rozhraní
    - HomeAdminController.php - dashboard
    - ArticleAdminController.php - správa článků
    - CategoryAdminController.php - správa kategorií
    - UserAdminController.php - správa uživatelů
    - StatisticsAdminController.php - statistiky
    - AccessControlAdminController.php - řízení přístupu
    - PromotionAdminController.php - propagace článků
  - Web/ - kontrolery pro veřejnou část
    - HomeController.php - úvodní stránka
    - ArticleController.php - články
    - CategoryController.php - kategorie
    - UserController.php - uživatelé
  - LoginController.php - správa přihlašování a autentizace
* **Pohledy (App/Views)** - šablony pro generování HTML výstupu
  - Web/ - šablony pro veřejnou část
  - Admin/ - šablony pro administraci
  - Layout/ - společné layouty a komponenty
* **Pomocné třídy (App/Helpers)** - utility pro zpracování obrázků, validaci formulářů, generování URL atp.
  - TextHelper.php - funkce pro práci s textem, formátování, zkracování
  - TimeHelper.php - funkce pro práci s časem, formátování datumů
* **Middleware (App/Middleware)** - komponenty pro filtrování HTTP požadavků
  - AuthMiddleware.php - ověření autentizace a oprávnění uživatelů

**Systém směrování (routing):**
Implementoval jsem vlastní jednoduchý routing systém s dvěma hlavními vstupními body:
1. **web/index.php** - router pro veřejnou část webu
2. **admin/index.php** - router pro administrační část (s kontrolou přístupu)

Základní logika routeru spočívá v analýze URL adresy, extrakci parametrů a směrování požadavku na příslušný kontroler a jeho metodu.

**Klíčové funkce implementované v backendu:**
* Autentizace a autorizace uživatelů - bezpečné přihlašování, správa sessions, kontrola oprávnění
* CRUD operace pro všechny entity - vytváření, čtení, aktualizace a mazání článků, kategorií a uživatelů
* Správa multimediálního obsahu - nahrávání, validace a zpracování obrázků a audio souborů
* Generování SEO-friendly URL - automatické vytváření čitelných adres z názvů článků a kategorií
* Sběr statistik - sledování počtu zobrazení článků a analýza návštěvnosti
* Plánování publikace - možnost nastavit datum, kdy se článek automaticky zveřejní
* Plánování propagace - systém pro časově omezené zvýraznění vybraných článků


**Pro zabezpečení aplikace jsem implementoval následující opatření:**
* Hashování hesel pomocí PHP funkce password_hash (bcrypt algoritmus)
* Ochrana proti SQL injection pomocí prepared statements:
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
```
* Validace a sanitizace všech vstupů od uživatelů:
```php
$text = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
```
* Kontrola oprávnění podle role uživatele s dynamickým ověřováním přístupu:
```php
if ($this->accessControl->hasAccess($page, $userRole)) {
    // Uživatel má přístup
} else {
    // Přesměrování na chybovou stránku
}
```
* Ošetření nahrávaných souborů:
```php
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($fileType, $allowedTypes)) {
    // Nepovolený typ souboru
}
```

Backend jsem navrhl tak, aby byl modulární a snadno rozšiřitelný. Díky dodržování principů OOP a důrazu na strukturu MVC je možné přidávat nové funkce nebo modifikovat stávající bez nutnosti zásadních změn v již existujícím kódu.

**Sběr statistik** - sledování počtu zobrazení článků a analýza návštěvnosti

> [VLOŽIT OBRÁZEK 18: Ukázka administrace statistik s grafy a přehledy návštěvnosti]

### 3.2.1 Popis administračních stránek

Administrační rozhraní je rozděleno do několika hlavních sekcí, které poskytují komplexní nástroje pro správu webu. Rozhraní je navrženo s důrazem na intuitivnost a efektivitu práce.

> [VLOŽIT OBRÁZEK 6: Celkový pohled na administrační rozhraní s hlavním menu a přehledovou stránkou]

**Dashboard (Úvodní přehled)**
* Přehledová stránka s nejdůležitějšími informacemi
  - Seznam nejnovějších článků (posledních 5)
    * Název článku
    * Datum publikace
    * Rychlý odkaz na úpravu
  - Články z posledních 7 dnů
    * Tabulkový přehled s názvem, datem, autorem
    * Rychlý přístup k úpravě článků
  - Nejčtenější články za posledních 7 dní
    * Interaktivní graf zobrazení pomocí ApexCharts
    * Porovnání čtenosti článků
    * Trendy návštěvnosti

> [VLOŽIT OBRÁZEK 7: Dashboard s přehledem článků a grafy]

**Správa článků**
* Seznam všech článků s možností filtrování a řazení
  - Filtrování podle kategorie, autora, stavu
  - Řazení podle data, názvu, počtu zobrazení
  - Rychlé vyhledávání
* Vytváření nových článků pomocí TinyMCE WYSIWYG editoru
  - Pokročilé formátování textu
  - Vkládání obrázků a tabulek
  - Správa odkazů
  - Vkládání audio souborů
* Editace existujících článků
  - Rychlá úprava metadat
  - Správa verzí článků
  - Historie změn
* Správa multimediálního obsahu
  - Nahrávání a optimalizace obrázků
  - Správa audio souborů
  - Vytváření náhledů
  - Organizace médií do složek
* Plánování publikace článků
  - Nastavení data a času publikace
  - Fronta článků k publikaci
  - Připomenutí o plánovaných publikacích
* Nastavení SEO parametrů
  - Meta popisky
  - Klíčová slova
  - SEO-friendly URL
  - Open Graph tagy
* Správa viditelnosti článků
  - Publikováno/Koncept
  - Plánované publikace
  - Archivované články
* Přiřazování článků do kategorií
  - Vícečetná kategorizace
  - Hlavní kategorie
  - Podkategorie

> [VLOŽIT OBRÁZEK 8: Editor článků s TinyMCE a nástroji pro správu obsahu]

**Správa kategorií**
* Seznam všech kategorií
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (název, URL)
  - Rychlé akce (úprava, smazání)
* Vytváření nových kategorií
  - Formulář pro název kategorie
  - Automatické generování URL
* Editace kategorií
  - Úprava názvu kategorie
  - Správa URL

> [VLOŽIT OBRÁZEK 9: Správa kategorií - přehled a editor]

**Správa uživatelů**
* Přihlášení a registrace
  - Přihlašovací formulář
    * Email a heslo
    * Zapamatování přihlášení
    * Odkaz na reset hesla
  - Registrační formulář
    * Základní údaje (email, heslo)
    * Validace formuláře
  - Reset hesla
    * Formulář pro zadání emailu
    * Odeslání resetovacího odkazu
    * Formulář pro nové heslo
    * Validace shody hesel

* Správa uživatelů v administraci
  - Seznam uživatelů
    * Tabulkový přehled s řazením a filtrováním
    * Základní informace (ID, jméno, příjmení, email)
    * Role uživatele (Uživatel, Moderátor, Editor, Administrátor)
    * Rychlé akce (úprava, smazání)
  - Editace uživatele
    * Základní údaje (email, jméno, příjmení)
    * Nastavení role
    * Popis uživatele (TinyMCE editor)
  - Nastavení vlastního účtu
    * Osobní údaje
    * Profilová fotka
    * Záhlaví profilu
    * Popis profilu
    * Správa sociálních sítí
      - Přidávání/odebírání sítí
      - Nastavení odkazů
      - Validace duplicit

> [VLOŽIT OBRÁZEK 10: Správa uživatelů - přehled a editor]

**Správa uživatelů v roli administrátora**
* Seznam uživatelů
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (ID, jméno, příjmení, email)
  - Role uživatele (Uživatel, Moderátor, Editor, Administrátor)
  - Rychlé akce (úprava, smazání)
* Editace uživatele
  - Základní údaje (email, jméno, příjmení)
  - Nastavení role
  - Popis uživatele (TinyMCE editor)

> [VLOŽIT OBRÁZEK 10: Správa uživatelů - přehled a editor]

**Nastavení vlastního účtu**
* Osobní údaje
  - Jméno a příjmení
  - Email
  - Popis (TinyMCE editor)
* Správa profilových obrázků
  - Profilová fotka (nahrání, náhled)
  - Záhlaví profilu (nahrání, náhled)
* Správa sociálních sítí
  - Přidávání/odebírání sociálních sítí
  - Nastavení odkazů na profily
  - Validace duplicitních sítí

> [VLOŽIT OBRÁZEK 11: Nastavení vlastního účtu]

**Statistiky**
* Přehled statistik
  - Celkový počet zobrazení
  - Počet článků
  - Počet kategorií
  - Průměrné zobrazení na článek
* Statistiky článků
  - Nejčtenější články
  - Trendy zobrazení
  - Rozložení zobrazení
* Statistiky kategorií
  - Počet zobrazení podle kategorií
  - Počet článků v kategoriích
  - Trendy kategorií v čase
* Statistiky autorů
  - Počet článků podle autorů
  - Celkový počet zobrazení
  - Průměrné zobrazení na autora
* Statistiky zobrazení
  - Filtrování podle časového období
  - Graf zobrazení v čase
  - Nejnavštěvovanější dny
  - Kalendářní tepelná mapa

> [VLOŽIT OBRÁZEK 11: Přehled statistik s grafy a tabulkami]

**Správa propagací**
* Seznam propagací
  - Tabulkový přehled s řazením a filtrováním
  - Základní informace (název, článek, datum)
  - Rychlé akce (úprava, smazání)
* Vytváření nových propagací
  - Výběr článku k propagaci
  - Nastavení data a času
  - Priorita propagace
* Editace propagací
  - Úprava základních informací
  - Změna data a času
  - Změna priority

> [VLOŽIT OBRÁZEK 12: Správa propagací - přehled a editor]

**Nastavení systému**
* Správa přístupových práv
  - Nastavení oprávnění pro jednotlivé role
    * Moderátor
    * Editor
    * Administrátor
  - Přehled stránek a jejich oprávnění
  - Rychlá úprava přístupových práv
* Základní nastavení
  - Konfigurace webu
  - Nastavení e-mailů
  - Správa uživatelů

> [VLOŽIT OBRÁZEK 13: Nastavení systému s přehledem přístupových práv]

Každá sekce administračního rozhraní je navržena s důrazem na:
* Intuitivní ovládání
  - Konzistentní design
  - Logická navigace
  - Rychlé akce
* Rychlý přístup k často používaným funkcím
  - Zkratky kláves
  - Rychlé menu
  - Nedávno používané
* Přehledné zobrazení dat
  - Filtry a vyhledávání
  - Řazení a seskupování
  - Export dat
* Možnost filtrování a vyhledávání
  - Pokročilé filtry
  - Fulltextové vyhledávání
  - Uložené filtry
* Responzivní design pro všechny typy zařízení
  - Mobilní optimalizace
  - Adaptivní layout
  - Touch-friendly ovládání

> [VLOŽIT OBRÁZEK 14: Responzivní design administrace na různých zařízeních]

---

## ZÁVĚR

V rámci této maturitní práce jsem vytvořil redakční systém pro cyklistický magazín cyklistickey.cz, který nabízí komplexní řešení pro správu a publikování specializovaného obsahu. Výsledný systém úspěšně naplňuje svůj účel – poskytuje redakci efektivní nástroj pro publikování článků a čtenářům příjemné prostředí pro konzumaci obsahu.

Hlavní motivací pro vytvoření tohoto systému byla potřeba nahradit původní verzi webu, kterou jsem také vytvořil, ale která obsahovala mnoho nedostatků. Nový systém odstraňuje tyto nedostatky a přináší moderní architekturu, lepší bezpečnost a rozšířenou funkcionalitu.

V rámci přípravné fáze jsem analyzoval existující řešení (mtbs.cz, roadcycling.cz a mtbiker.sk), což mi umožnilo identifikovat jejich silné stránky i nedostatky. Tyto poznatky jsem následně zohlednil při návrhu vlastního systému, který implementuje osvědčené postupy a zároveň nabízí inovativní prvky, jako je integrace audio obsahu nebo pokročilé statistiky čtenosti.

Technologicky jsem projekt postavil na stacku, který jsme probírali během studia – PHP s objektově orientovaným přístupem a MVC architekturou pro backend, MySQL pro databázovou vrstvu, a HTML5, CSS3 a JavaScript s frameworkem Bootstrap 5 pro frontend. Pro WYSIWYG editor jsem použil TinyMCE a pro zjednodušení JavaScriptu knihovnu jQuery. Tato kombinace zajišťuje nejen robustnost a udržitelnost kódu, ale také optimální uživatelský zážitek napříč různými zařízeními.

**Během vývoje jsem věnoval značnou pozornost bezpečnostním aspektům. Systém implementuje:**
* Ochranu proti SQL injection útokům pomocí parametrizovaných dotazů
* Obranu proti XSS útokům prostřednictvím důsledného escapování výstupu
* Bezpečné ukládání hesel s využitím hashování
* Propracovaný systém řízení přístupu založený na uživatelských rolích

> [VLOŽIT OBRÁZEK 19: Ukázka implementace zabezpečení pomocí prepared statements v souboru App/Models/Database.php]

**Mezi klíčové funkce implementovaného systému patří:**
* Intuitivní TinyMCE editor pro vytváření a editaci článků
* Flexibilní kategorizace obsahu s možností přiřazení článku do více kategorií
* Systém pro správu multimediálního obsahu včetně obrázků a audio nahrávek
* Propracované statistické nástroje pro sledování čtenosti jednotlivých článků
* Responzivní design optimalizovaný pro všechny typy zařízení
* Modulární architektura umožňující snadné rozšiřování funkcionality
* Systém propagace vybraných článků s možností plánování

> [VLOŽIT OBRÁZEK 20: Screenshot administrace kategorií s možností přiřazení článků do více kategorií]

Během vývoje jsem musel překonat několik technických výzev, především v oblasti zvládnutí objektově orientovaného PHP a MVC architektury, což pro mě byly relativně nové koncepty. Další výzvou byla optimalizace výkonu při práci s multimediálním obsahem a implementace efektivního systému řízení přístupu. Tyto problémy jsem vyřešil díky kombinaci důkladného studia, experimentování s různými přístupy a postupného testování.

Zvláštní pozornost jsem věnoval user experience (UX) a uživatelskému rozhraní (UI), protože jsem si vědom, že i nejlépe naprogramovaný systém může selhat, pokud je pro uživatele obtížné s ním pracovat. Pro redakční tým jsem proto navrhl intuitivní administrační rozhraní s jasnou navigací a konzistentním designem. Pro čtenáře jsem vytvořil přehledný frontend s důrazem na čitelnost a snadnou orientaci. Obě části jsem podrobil několika kolům testování s potenciálními uživateli, což mi umožnilo identifikovat a odstranit problematické prvky.

> [VLOŽIT OBRÁZEK 21: Ukázka UI komponenty - karty článku na hlavní stránce s přehledně uspořádanými informacemi]

Implementace responzivního designu byla další významnou částí projektu. S rostoucím podílem mobilních zařízení mezi čtenáři cyklistického magazínu bylo nezbytné zajistit optimální zobrazení na všech typech zařízení. Díky využití frameworku Bootstrap 5 a důslednému testování na reálných zařízeních se mi podařilo vytvořit konzistentní uživatelskou zkušenost napříč různými velikostmi obrazovek - od mobilních telefonů přes tablety až po velké desktopové monitory.

> [VLOŽIT OBRÁZEK 22: Srovnání zobrazení webu na počítači, tabletu a mobilním telefonu]

Z technického hlediska považuji za největší přínos projektu implementaci modulární MVC architektury, která poskytuje solidní základ pro budoucí rozšiřování systému. Díky jasnému oddělení datové vrstvy (modely), prezentační vrstvy (pohledy) a řídící logiky (kontrolery) je kód přehledný, snadno udržovatelný a rozšiřitelný. Tato architektura také usnadňuje týmovou spolupráci, protože různí vývojáři mohou pracovat na různých částech systému bez vzájemných konfliktů.

> [VLOŽIT OBRÁZEK 23: Diagram struktury MVC architektury implementované v projektu]

Součástí projektu byla také optimalizace pro vyhledávače (SEO), která je pro online magazín klíčová. Implementoval jsem automatické generování meta tagů, SEO-friendly URL adresy a strukturovaný markup, což přispívá k lepší viditelnosti obsahu ve vyhledávačích. Tyto techniky jsou zvláště důležité pro specializovaný magazín, jako je cyklistickey.cz, který cílí na specifickou komunitu zájemců o cyklistiku.

> [VLOŽIT OBRÁZEK 24: Ukázka kódu pro generování SEO-friendly URL (slugů) v souboru App/Helpers/TextHelper.php]

**V rámci budoucího rozvoje plánuji implementovat:**
* Automatické generování zvukových stop článků pomocí text-to-speech technologie
* Pokročilou ochranu proti CSRF útokům prostřednictvím tokenů pro všechny formuláře
* Systém pro uživatelské komentáře s možností moderace
* Automatickou integraci se sociálními sítěmi pro sdílení nového obsahu
* Propojení s existující mobilní aplikací
* Pokročilé analytické nástroje včetně integrace s Google Analytics
* Rozšíření statistik na všechny stránky webu (aktuálně pouze u článků)
* Implementaci pokročilých SEO prvků jako strukturovaná data, Open Graph a Twitter Cards

Z osobního hlediska mi tento projekt poskytl cennou příležitost propojit teoretické znalosti získané během studia s praktickými dovednostmi potřebnými pro vývoj reálné webové aplikace. Musel jsem se naučit pracovat s objektově orientovaným PHP, implementovat MVC architekturu a současně myslet na bezpečnostní aspekty webových aplikací.

Získané zkušenosti z oblasti webových technologií, databázového návrhu, bezpečnostních principů a responzivního designu představují solidní základ pro mé další profesní působení v oblasti vývoje webových aplikací. V budoucnu bych se rád této činnosti věnoval i profesionálně a potenciálně si tímto způsobem i přivydělával.

Vzhledem k tomu, že cyklistickey.cz je reálný projekt, který bude po dokončení práce pokračovat ve svém provozu, plánuji systém dále rozvíjet, optimalizovat a přizpůsobovat měnícím se potřebám redakce a čtenářů. Dlouhodobým cílem je vybudovat stabilní platformu, která se stane respektovaným zdrojem informací pro českou cyklistickou komunitu.

---

## SEZNAM PŘÍSTUPOVÝCH ÚDAJŮ

**Administrační rozhraní:**
* URL: https://vincenon21.mp.spse-net.cz

* Úroveň oprávnění	    Přihlašovací email	            Heslo
* Administrátor	        admin@cyklistickey.cz	        admin
* Editor	            editor@cyklistickey.cz	        editor
* Redaktor	            redaktor@cyklistickey.cz	    redaktor


## SEZNAM POUŽITÉ LITERATURY A ZDROJŮ OBRÁZKŮ

### Literatura:
1. DUCKETT, Jon. *HTML & CSS: design and build websites*. Indianapolis, IN: Wiley, 2011, 490 s. ISBN 978-1-118-00818-8.
2. DUCKETT, Jon. *JavaScript & jQuery: interactive front-end web development*. Indianapolis, IN: Wiley, 2014, 622 s. ISBN 978-1-118-53164-8.
3. NIXON, Robin. *Learning PHP, MySQL & JavaScript: with jQuery, CSS & HTML5*. 5. vydání. Sebastopol: O'Reilly Media, 2018, 805 s. ISBN 978-1-491-97891-7.
4. TATROE, Kevin, Peter MacINTYRE a Rasmus LERDORF. *Programming PHP*. 4. vydání. Sebastopol: O'Reilly Media, 2020, 570 s. ISBN 978-1-492-05489-2.
5. GILMORE, W. Jason. *Easy PHP Websites with the Zend Framework*. 2. vydání. Columbus, OH: WJ Gilmore, 2009, 498 s. ISBN 978-0-9738621-5-2.

### Online zdroje:
1. Bootstrap 5 Documentation [online]. 2023 [cit. 2023-03-15]. Dostupné z: https://getbootstrap.com/docs/5.0/
2. PHP: Hypertext Preprocessor [online]. 2023 [cit. 2023-03-10]. Dostupné z: https://www.php.net/docs.php
3. MDN Web Docs: JavaScript [online]. 2023 [cit. 2023-03-08]. Dostupné z: https://developer.mozilla.org/en-US/docs/Web/JavaScript
4. W3Schools Online Web Tutorials [online]. 2023 [cit. 2023-03-12]. Dostupné z: https://www.w3schools.com/
5. TinyMCE Documentation [online]. 2023 [cit. 2023-03-20]. Dostupné z: https://www.tiny.cloud/docs/
6. MySQL 8.0 Reference Manual [online]. 2023 [cit. 2023-03-18]. Dostupné z: https://dev.mysql.com/doc/refman/8.0/en/
7. Jak psát web: HTML5 a CSS3 [online]. 2023 [cit. 2023-03-14]. Dostupné z: https://www.jakpsatweb.cz/
8. CSS-Tricks [online]. 2023 [cit. 2023-03-25]. Dostupné z: https://css-tricks.com/

### Zdroje obrázků:
1. Logo cyklistickey.cz - vlastní tvorba
2. Screenshoty analyzovaných webů (mtbs.cz, roadcycling.cz, mtbiker.sk) - pořízeny se souhlasem provozovatelů těchto webů pouze pro účely analýzy
3. Screenshoty administračního rozhraní - vlastní tvorba
4. ER diagram databáze - vytvořeno pomocí nástroje MySQL Workbench
5. Ikony použité v administračním rozhraní - Font Awesome, licence SIL OFL 1.1
6. Ukázkové obrázky pro články - pořízeny z bezplatných fotobank:
   * Unsplash.com - licence Unsplash License (https://unsplash.com/license)
   * Pexels.com - licence Pexels License (https://www.pexels.com/license/)
   * Pixabay.com - licence Pixabay License (https://pixabay.com/service/license/)
7. Mockupy zobrazení na různých zařízeních - vytvořeno pomocí nástroje Responsive Mockups
8. Diagramy MVC architektury - vlastní tvorba pomocí nástroje draw.io

## SEZNAM OBRÁZKŮ

    Obrázek 1: Logo cyklistickey.cz s moderním designem  
    Obrázek 2: Screenshot hlavní stránky webu mtbs.cz zobrazující rozvržení obsahu a navigaci  
    Obrázek 3: Screenshot hlavní stránky webu roadcycling.cz s ukázkou členění zpravodajského obsahu  
    Obrázek 4: Screenshot hlavní stránky webu mtbiker.sk s ukázkou komunitních prvků a bazaru  
    Obrázek 5: Homepage webu cyklistickey.cz  
    Obrázek 6: Dashboard administračního rozhraní zobrazující přehled nejdůležitějších statistik a funkcí  
Obrázek 7: TinyMCE WYSIWYG editor pro tvorbu článků s nástroji pro formátování textu a vkládání médií  
    Obrázek 8: ER diagram databáze zobrazující vztahy mezi hlavními tabulkami systému  
Obrázek 9: Ukázka barevného schématu a typografie použité na webu  
Obrázek 10: Ukázka responzivního designu na desktopu  
Obrázek 11: Ukázka responzivního designu na tabletu  
Obrázek 12: Ukázka responzivního designu na mobilu  
Obrázek 13: Hlavní stránka webu s označením klíčových komponent rozhraní  
Obrázek 14: Detail článku s integrovaným audio přehrávačem a multimediálními prvky  
Obrázek 15: Ukázka stránky profilu autora s informacemi a přehledem publikovaných článků  
Obrázek 16: Diagram MVC architektury aplikace znázorňující vztahy mezi modely, kontrolery a pohledy  
Obrázek 17: Ukázka administrace článků s možnostmi pro vytváření, editaci a mazání obsahu  
Obrázek 18: Ukázka administrace statistik s grafy a přehledy návštěvnosti  
Obrázek 19: Ukázka implementace zabezpečení pomocí prepared statements v souboru App/Models/Database.php  
Obrázek 20: Screenshot administrace kategorií s možností přiřazení článků do více kategorií  
Obrázek 21: Ukázka UI komponenty - karty článku na hlavní stránce s přehledně uspořádanými informacemi  
Obrázek 22: Srovnání zobrazení webu na počítači, tabletu a mobilním telefonu  
Obrázek 23: Diagram struktury MVC architektury implementované v projektu  
Obrázek 24: Ukázka kódu pro generování SEO-friendly URL (slugů) v souboru App/Helpers/TextHelper.php  
Obrázek 25: Detail hlavičky webu s navigačním menu a vyhledávacím polem  
Obrázek 26: Screenshot mobilního menu po rozbalení hamburger ikony  
Obrázek 27: Detail patičky webu se všemi navigačními odkazy a kontaktními informacemi  
Obrázek 28: Hero sekce úvodní stránky s nejnovějším článkem  
Obrázek 29: Sekce nejnovějších článků na úvodní stránce  
Obrázek 30: Záhlaví kategorie s názvem a popisem  
Obrázek 31: Filtrační nástroje pro řazení článků v kategorii  
Obrázek 32: Hlavička článku s názvem, perexem a informacemi o autorovi  
Obrázek 33: Sekce se souvisejícími články na konci stránky článku  
Obrázek 34: Profil autora s biografickými informacemi a odkazy na sociální sítě  
Obrázek 35: Seznam publikovaných článků autora s možností filtrování  
Obrázek 36: Detail zobrazení webu na mobilním telefonu s jednosloupovou strukturou  
Obrázek 37: Detail zobrazení webu na tabletu s dvousloupovou strukturou  
Obrázek 38: Detail zobrazení webu na desktopovém počítači s třísloupcovou strukturou  
Obrázek 39: Screenshot z Chrome DevTools ukazující testování responzivity na různých zařízeních  
Obrázek 40: Detail audio přehrávače s ovládacími prvky  
Obrázek 41: Ukázka implementace audio přehrávače v designu článku  
Obrázek 42: Náhled audioarchivu s přehledem dostupných audio verzí článků  
Obrázek 43: Ukázka implementace strukturovaných dat v detailu článku  
Obrázek 44: Screenshot výsledků testování rychlosti načítání v nástroji Google PageSpeed Insights  
Obrázek 45: Screenshot části zdrojového kódu HTML s použitými sémantickými elementy  

## SEZNAM KÓDŮ

Kód 1: Implementace připojení k databázi v souboru Database.php  
Kód 2: Ukázka CRUD operací v modelu ArticleModel.php  
Kód 3: Implementace správy uživatelů v souboru UserModel.php  
Kód 4: Ukázka MVC struktury v App/Controllers/ArticleController.php  
Kód 5: Implementace routování v souboru Routes.php  
Kód 6: Ukázka generování SEO-friendly URL v souboru TextHelper.php  
Kód 7: JavaScript pro interaktivní prvky na stránce článku  
Kód 8: Implementace statistického sledování v souboru StatisticsHelper.php  
Kód 9: Základní struktura šablony v souboru base.php  
Kód 10: Ukázka implementace uploadování souborů v souboru FileUploader.php

## PŘÍLOHY

### Příloha A: Kompletní ER diagram databáze
Detailní diagram zobrazující všechny tabulky v databázi včetně jejich atributů, datových typů, primárních a cizích klíčů a vztahů mezi tabulkami.

### Příloha B: Ukázky zdrojových kódů
Vybrané ukázky implementace klíčových částí projektu:
- Ukázka implementace MVC architektury
- Implementace zabezpečení přihlašování
- Správa nahrávání a zpracování multimediálních souborů
- Implementace statistického systému

### Příloha C: Wireframy a mockupy
Původní návrhy uživatelského rozhraní pro desktop i mobilní zařízení:
- Wireframy hlavní stránky
- Wireframy detailu článku
- Wireframy administračního rozhraní

### Příloha D: Uživatelská příručka administračního rozhraní
Podrobný návod pro redaktory a administrátory:
- Vytvoření a editace článků
- Správa kategorií
- Nahrávání multimediálního obsahu
- Práce se statistikami
- Nastavení propagace článků

### Příloha E: Testovací protokoly
Výsledky testování projektu:
- Testování kompatibility napříč prohlížeči
- Testování responzivity
- Zátěžové testování
- Bezpečnostní testování

### Příloha F: Optimalizace SEO a výkonu
Analýza a implementace SEO optimalizací:
- Výsledky testů v PageSpeed Insights
- Implementace OpenGraph meta tagů
- Ukázky strukturovaných dat

### Příloha G: Porovnání s původní verzí webu
Srovnání nové a původní verze webu cyklistickey.cz:
- Architektura
- Funkcionalita
- Uživatelské rozhraní
- Výkon a optimalizace

