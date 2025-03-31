# Redakční systém pro cyklistický magazín

## Obsah

- [Úvod](#úvod)
- [1 Analýza obdobných webových stránek](#1-analýza-obdobných-webových-stránek)
  - [1.1 MTBS.CZ](#11-mtbscz)
    - [1.1.1 Kladné stránky](#111-kladné-stránky)
    - [1.1.2 Záporné stránky](#112-záporné-stránky)
  - [1.2 ROADCYCLING.CZ](#12-roadcyclingcz)
    - [1.2.1 Kladné stránky](#121-kladné-stránky)
    - [1.2.2 Záporné stránky](#122-záporné-stránky)
  - [1.3 MTBIKER.SK](#13-mtbikersk)
    - [1.3.1 Kladné stránky](#131-kladné-stránky)
    - [1.3.2 Záporné stránky](#132-záporné-stránky)
- [2 Návrh projektu](#2-návrh-projektu)
  - [2.1 Cílové skupiny](#21-cílové-skupiny)
  - [2.2 Administrace webu](#22-administrace-webu)
  - [2.3 Databáze](#23-databáze)
  - [2.4 Design a responzivita](#24-design-a-responzivita)
- [3 Popis projektu](#3-popis-projektu)
  - [3.1 Frontend](#31-frontend)
  - [3.2 Backend](#32-backend)
- [Závěr](#závěr)
- [Seznam přístupových údajů](#seznam-přístupových-údajů)
- [Seznam použité literatury a zdrojů obrázků](#seznam-použité-literatury-a-zdrojů-obrázků)
- [Seznam obrázků](#seznam-obrázků)
- [Přílohy](#přílohy)

# Úvod

![Logo Cyklistickey](Aspose.Words.0d1d9bf0-c593-45f4-a1ea-656568414b69.001.png)

Tato maturitní práce se zabývala vývojem redakčního systému pro cyklistický magazín cyklistickey.cz, který představuje komplexní řešení pro správu a publikování specializovaného obsahu. Výsledný systém splňuje všechny požadavky stanovené v zadání a úspěšně naplňuje svůj účel – poskytovat redakci efektivní nástroj pro publikování článků a čtenářům příjemné prostředí pro konzumaci obsahu.

V rámci přípravné fáze byly analyzovány existující řešení (roadcycling.cz, mtbiker.sk a mtbs.cz), což umožnilo identifikovat jejich silné stránky i nedostatky. Tyto poznatky byly následně zohledněny při návrhu vlastního systému, který implementuje osvědčené postupy a zároveň nabízí inovativní prvky, jako je integrace audio obsahu nebo pokročilé statistiky čtenosti.

Technologicky je projekt postaven na moderním stacku – PHP s objektově orientovaným přístupem a MVC architekturou pro backend, MySQL pro databázovou vrstvu, a HTML5, CSS3 a JavaScript s frameworkem Bootstrap 5 pro frontend. Tato kombinace zajišťuje nejen robustnost a udržitelnost kódu, ale také optimální uživatelský zážitek napříč různými zařízeními.

Značnou pozornost jsem během vývoje věnoval bezpečnostním aspektům. Systém implementuje:

- Ochranu proti SQL injection útokům pomocí parametrizovaných dotazů (prepared statements)
- Obranu proti XSS útokům prostřednictvím důsledného escapování výstupu (htmlspecialchars)
- Bezpečné ukládání hesel s využitím hashování (password_hash)
- Propracovaný systém řízení přístupu založený na uživatelských rolích

Mezi klíčové funkce implementovaného systému patří:

- Intuitivní WYSIWYG editor pro vytváření a editaci článků
- Flexibilní kategorizace obsahu s možností přiřazení článku do více kategorií
- Systém pro správu multimediálního obsahu včetně obrázků a audio nahrávek
- Propracované statistické nástroje pro sledování čtenosti jednotlivých článků
- Responzivní design optimalizovaný pro všechny typy zařízení
- Modulární architektura umožňující snadné rozšiřování funkcionality

Během vývoje jsem byl konfrontován s několika technickými výzvami, zejména v oblasti optimalizace výkonu při práci s multimediálním obsahem a implementaci efektivního systému řízení přístupu. Tyto problémy byly úspěšně vyřešeny díky kombinaci důkladné analýzy, experimentování s různými přístupy a iterativního testování.

V rámci budoucího rozvoje projektu by bylo vhodné implementovat:

- Pokročilou ochranu proti CSRF útokům prostřednictvím tokenů pro všechny formuláře
- Systém pro uživatelské komentáře s možností moderace
- Automatickou integraci se sociálními sítěmi pro sdílení nového obsahu
- Mobilní aplikaci synchronizovanou s webovou verzí systému
- Pokročilé analytické nástroje pro detailnější analýzu uživatelského chování
- Systém personalizovaných doporučení obsahu pro registrované uživatele

Z osobního hlediska mi tento projekt poskytl cennou příležitost propojit teoretické znalosti získané během studia s praktickými dovednostmi potřebnými pro vývoj reálné webové aplikace. Získané zkušenosti z oblasti webových technologií, databázového návrhu, bezpečnostních principů a responzivního designu představují solidní základ pro mé další profesní působení v oblasti vývoje webových aplikací.

Redakční systém nabízí mnoho možností pro budoucí rozšíření. V další fázi vývoje by bylo možné implementovat:

- Pokročilý systém komentářů s moderací a reakcemi
- Automatickou integraci se sociálními sítěmi pro sdílení obsahu
- Nativní mobilní aplikaci synchronizovanou s webovým rozhraním
- Rozšířené analytické nástroje pro detailnější sledování uživatelského chování
- Systém personalizovaných doporučení obsahu pro přihlášené uživatele
- Implementaci systému flashnews, který je plánován jako součást budoucího vývoje

Vývojové procesy a metodologie: Projekt byl vyvíjen podle agilních principů s využitím Scrum metodologie. Vývoj probíhal v iteracích, přičemž každá iterace přinášela nové funkce nebo vylepšení stávajících. Pro správu verzí byl využit Git, což umožnilo efektivní spolupráci a sledování změn v kódu. Testování bylo prováděno průběžně, včetně unit testů pro kritické komponenty systému a integračních testů pro ověření správné funkčnosti celého řešení. Dokumentace byla průběžně aktualizována a doplňována o nové funkce a změny v systému.

# Redakční systém pro cyklistický magazín

Střední průmyslová škola elektrotechnická 
a Vyšší odborná škola Pardubice
STREDNÍ PRŮMYSLOVÁ ŠKOLA ELEKTROTECHNICKÁ
MATURITNÍ PRÁCE - WEBŮVÉ STRÁNKY


Redakční systém pro cyklistický magazín











brezen 2025	Ondrej Vincenc 4.E
 
      
"Prohlašuji, že jsem maturitní práci vypracoval(a) samostatně a použil(a) jsem literárních 
pramenů, informací a obrázků, které cituji a uvádím v seznamu použitých literatury a zdroje 
informací a v seznamu použitých obrázků a neporušil jsem autorská práva.
Souhlasím s umístěním kompletní maturitní práce nebo její části na školní internetové 
stránce a s použitím jejích ukázek pro výuku."
V Pardubicích dne ...........................	................................................
	podpis


Střední průmyslová škola elektrotechnická a Vyšší odborná škola
Pardubice
MATURITNÍ ZKOUŠKA - PROFILOVÁ ČÁST - MATURITNÍ PROJEKT
zadání maturitní práce
Obor:                                      18-20-M/01 Informacní technologie	
školní rok:                              2024/2025
Trida: 				4.E
Jméno a příjmení ůka: 	Ondrej Vincenc
Téma maturitní práce:	
Vedoucí maturitní práce:	
Pracoviště vedoucího:	SPŠE a VOŠ Pardubice, Karla IV. 13
Zadání:
Zde vložte popis zadání
Hlavním body administrace:
1)	
2)	
3)	
4)	
5)	
Statickými stránkami budou: 

Termín odevzdání maturitní práce:	1. 3. 2019
Vedoucí maturitní práce:		
Dne: 		Ing. Ladislav Štepěnek, reditel školy	 
Maturitní práce bude tvorba praktické části podle zadání a psímné práce. Praktická 
část bude uložena a zprůstupnena na školním serveru.
Sousčasti praktické části budou:
*	responzivní a validní webové stránky umístěné na přidělené adrese na školním 
serveru vcetně ukázekových dat uložených v databázi,
*	veškerá administrace databáze proveditelná ve webových stránkách projektu, 
který bude obsahovat vkládání, edici i mazání údaju prostřednictvím 
uživatelských formulářů a bude zahrnovat nahrávání a mazání obrázku ci jiných 
souborů,
Sousčastí psímné práce bude:
*	jméno a příjmení ůka, trida a název práce,
*	prohlášení o autorských právech, použitých zdrojích a souhlas s umístěním ukázky 
webu na školní internetové stránce a souhlas s použitím ukázek pro výuku,
*	analýza tématu práce, 
*	návrh projektu, popis oprávnení prstupu uživatele, popis použití databáze,
*	grafický návrh designu, popis zpusobu ření responzivity webu,
*	popis funkcí webových stránek,
*	zhodnocení splnění zadání,
*	seznam použitých technologií s popisem méně známých technologií, licence 
k použitímu software, skriptum a knihovnám,
*	adresa webových stránek umístěných na školním serveru, seznam prstupových 
údaju registrovaného uživatele a administrátora webu,
*	seznam použitých literatury, zdroje informací a zdroje obrázků použitých na webu,
*	seznam obrázků použitých v psímné práci,
*	prálohy - E-R diagram databáze, Use Case diagram administrace, screenshoty 
vytvorených webových stránek.
*	pevně vložený a podepsaný CD nebo DVD obsahující kopii adresáře webových 
stránek ze školního serveru, vyexportovanou databázi ve formátu SQL, v případě 
potreby popis specifických požadavků na konfiguraci serveru a psímné práci 
v elektronické podobě ve formátu PDF a ve formátu DOCX nebo ODT.
 
Zpusob zpracování psímné práce
Zpracování psímné práce bude odpovídat požadavkům dle souboru Maturitni prace - 
Formalni stranka dokumentace.pdf dostupném na www.spse.cz. Dodržení stanovených 
pravidel bude jedním z kritérií hodnocení psímné práce.
Pokyny k obsahu a rozsahu psímné práce
Psímná práce bude obsahovat minimálně 15 stránek vlastního textu (pocetno bez 
vložených obrázků a ukázek kódu), obrázky vetší než 1/2 stránky budou uvedeny v práloze. 
Do tohoto poctu se nezapocítávají úvodní listy, zadání, obsah, seznamy prstupových 
údaju, technologií, obrázků, literatury a prálohy. 
Kritéria hodnocení maturitní práce
Hlavní kritéria
*	splnění zadání - práce splňující zadání na méně než 60 % bude hodnocena 
známkou nedostatečně,
*	míra vlastního podílu na ření,
*	nárocnost a rozsah práce,
*	dodržení stanovených pravidel pro zpracování psímné práce.
Vlastní maturitní práce tvorí jednu část třetí profilové zkoušky; druhou částí je její 
obhajoba.
Hodnocení maturitní práce:
*	vedoucí maturitní práce a oponent hodnotí maturitní práci podle stanovených 
kritérií hodnocení známkou výborně až nedostatečně.
Požadavek na počet vyhotovení maturitní práce
Maturitní práci odevzdáte ve stanoveném termínu vedoucímu maturitní práce, a to ve dvou 
vyhotovených. Praktickou část ponechte na školním serveru beze změny.


Anotace
Vytvorte redakční systém zaměřený na správu clánku pro cyklistický magazín. Systém bude 
umožňovat tvorbu, úpravy, mazání a publikaci clánku s různými úrovněmi prstupu pro 
uživatele s různými rolemi (např. redaktor, editor, administrátor). Systém bude rovněž 
zahrnovat správu kategorií clánku, sledování počtu zobrazení clánku, možnost přidání 
zvukové stopy k clánku a funkce pro propagaci vybraných clánků na první pozici na webu. 
Klíčová slova: Zapište klíčová slova (keywords)


Annotation
Insert a translation of the description.
Keywords:


Obsah
Úvod		9
1	Analýza obdobných webových stránek	10
1.1	Zapište název prvního webu	10
1.1.1	Kladné stránky	10
1.1.2	Záporné stránky	10
1.2	Zapište název prvního webu	10
1.2.1	Kladné stránky	10
1.2.2	Záporné stránky	10
1.3	Zapište název prvního webu	10
1.3.1	Kladné stránky	10
1.3.2	Záporné stránky	10
2	Návrh projektu	11
2.1	Cílové skupiny	11
2.2	Administrace webu	11
2.3	Databáze	11
2.4	Design a responzivita	11
3	Popis projektu	12
3.1	Frontend	12
3.2	Backend	12
Závěr	13
Seznam přístupových údajů	14
Seznam použitých literatury a zdrojů obrázků	15
Seznam obrázků	16
Přílohy	17

      

Úvod
      Tato maturitní práce se zabývala vývojem redakčního systému pro cyklistický 
magazín cyklistickey.cz, který představuje komplexní řešení pro správu a publikování 
specializovaného obsahu. Výsledný systém splňuje všechny požadavky stanovené v zadání 
a úspěšně naplňuje svůj účel – poskytovat redakci efektivní nástroj pro publikování clánků 
a čtenářům příjemné prostředí pro konzumaci obsahu.
      
      V rámci přípravné fáze byly analyzovány existující řešení (roadcycling.cz, 
mtbiker.sk a mtbs.cz), což umožnilo identifikovat jejich silné stránky i nedostatky. Tyto 
poznatky byly následně zohledněny při návrhu vlastního systému, který implementuje 
osvědčené postupy a zároveň nabízí inovativní prvky, jako je integrace audio obsahu nebo 
pokročilé statistiky čtenosti.
      Technologicky je projekt postaven na moderním stacku – PHP s objektově orientovaným přístupem a MVC architekturou pro backend, MySQL pro databázovou 
vrstvu, a HTML5, CSS3 a JavaScript s frameworkem Bootstrap 5 pro frontend. Tato 
kombinace zajišťuje nejen robustnost a udržitelnost kódu, ale také optimální uživatelský 
zážitek napříč různými zařízeními.
Znacnou pozornost jsem během vývoje věnoval bezpečnostním aspektům. Systém 
implementuje:
	Ochranu proti SQL injection útokům pomocí parametrizovaných dotazů (prepared 
statements)
	Obranu proti XSS útokům prostřednictvím důsledného escapování výstupu 
(htmlspecialchars)
	Bezpečné ukládání hesel s využitím hashování (password_hash)
	Propracovaný systém řízení přístupu založený na uživatelských rolích
Mezi klíčové funkce implementovaného systému patří:
	Intuitivní WYSIWYG editor pro vytváření a editaci clánků
	Flexibilní kategorizace obsahu s možností přiřazení clánku do více kategorií
	Systém pro správu multimediálního obsahu včetně obrázků a audio nahrávek
	Propracované statistické nástroje pro sledování čtenosti jednotlivých clánků
	Responzivní design optimalizovaný pro všechny typy zařízení
	Modulární architektura umožňující snadné rozšiřování funkcionality

      Behem vývoje jsem byl konfrontován s několika technickými výzvami, zejména v 
oblasti optimalizace výkonu při práci s multimediálním obsahem a implementaci 
efektivního systému řízení přístupu. Tyto problémy byly úspěšně vyřešeny díky kombinaci 
důkladné analýzy, experimentování s různými přístupy a iterativního testování.
V rámci budoucího rozvoje projektu by bylo vhodné implementovat:
	Pokročilou ochranu proti CSRF útokům prostřednictvím tokenů pro všechny 
formuláře
	Systém pro uživatelské komentáře s možností moderace
	Automatickou integraci se sociálními sítěmi pro sdílení nového obsahu
	Mobilní aplikaci synchronizovanou s webovou verzí systému
	Pokročilé analytické nástroje pro detailnější analýzu uživatelského chování
	Systém personalizovaných doporučení obsahu pro registrované uživatele
      
      Z osobního hlediska mi tento projekt poskytl cennou příležitost propojit teoretické 
znalosti získané během studia s praktickými dovednostmi potřebnými pro vývoj reálné 
webové aplikace. Získané zkušenosti z oblasti webových technologií, databázového 
návrhu, bezpečnostních principů a responzivního designu představují solidní základ pro mé 
další profesní působení v oblasti vývoje webových aplikací.
      Redakční systém nabízí mnoho možností pro budoucí rozšíření. V další fázi vývoje 
by bylo možné implementovat:
       Pokročilý systém komentářů s moderací a reakcemi
       Automatickou integraci se sociálními sítěmi pro sdílení obsahu
       Nativní mobilní aplikaci synchronizovanou s webovým rozhraním
       Rozšířené analytické nástroje pro detailnější sledování uživatelského chování
       Systém personalizovaných doporučení obsahu pro přihlášené uživatele
       Implementaci systému flashnews, který je plánován jako součást budoucího 
vývoje
      Vývojové procesy a metodologie: Projekt byl vyvíjen podle agilních principů s 
využitím Scrum metodologie. Vývoj probíhal v iteracích, přičemž každá iterace přinášela 
nové funkce nebo vylepšení stávajících. Pro správu verzí byl využit Git, což umožnilo 
efektivní spolupráci a sledování změn v kódu. Testování bylo prováděno průběžně, včetně 
unit testů pro kritické komponenty systému a integračních testů pro ověření správné 
funkčnosti celého řešení. Dokumentace byla průběžně aktualizována a doplňována o nové 
funkce a změny v systému.
      Historie a kontext projektu: Vývoj redakčního systému pro cyklistický magazín 
započal v reakci na rostoucí poptávku po specializovaných nástrojích pro správu obsahu v 
oblasti cyklistických webů. V posledních letech se cyklistika stala jedním z 
nejpopulárnějších sportů a volnočasových aktivit, což vedlo k nárůstu počtu cyklistických 
webů a magazínů. Tyto weby často používají obecné CMS systémy, které nejsou 
optimalizované pro specifické potřeby cyklistického obsahu. Tato skutečnost vytvořila 
prostor pro vývoj specializovaného řešení, které by lépe odpovídalo požadavkům redakce 
cyklistických webů.
      Výzvy a řešení: Během vývoje projektu jsme narazili na několik výzev, které bylo 
potřeba překonat. Jednou z hlavních výzev byla optimalizace výkonu při práci s 
multimediálním obsahem, zejména s velkými obrázky a audio soubory. Tento problém byl 
vyřešen implementací efektivního systému pro kompresi a cachování médií. Další výzvou 
byla implementace pokročilého systému pro správu oprávnění, který by umožnil flexibilní 
nastavení přístupových práv pro různé role uživatelů. Tato výzva byla překonána 
vytvořením modulárního systému oprávnění, který lze snadno rozšiřovat a upravovat podle 
potreb.
      Inovace a unikátní prvky: Projekt přináší několik inovativních řešení, která ho 
odlišují od běžných redakčních systémů. Jedním z unikátních prvků je integrovaný systém 
pro správu audio obsahu, který umožňuje redaktorům snadno přidávat a spravovat zvukové 
nahrávky přímo v rámci článků. Další inovací je pokročilý systém pro sledování 
návštěvnosti, který poskytuje detailní analýzy chování čtenářů a pomáhá redakci 
optimalizovat obsah. Systém také implementuje moderní přístupy k responzivnímu 
designu, což zajišťuje optimální zobrazení obsahu na všech typech zařízení.
      Budoucí perspektivy: Vytvořený redakční systém má potenciál pro další rozvoj a 
vylepšení. V budoucnu by bylo možné implementovat pokročilé funkce jako umělá 
inteligence pro automatickou kategorizaci článků, personalizované doporučení obsahu pro 
čtenáře nebo automatické generování shrnutí článků. Další možností je rozšíření systému o 
mobilní aplikaci, která by umožnila redaktorům pracovat na článcích i v terénu. Systém je 
navíc navržen tak, aby byl snadno integrovatelný s dalšími službami a platformami, což 
otevírá možnosti pro budoucí rozšíření funkcionality.
      


1	Analýza obdobných webových stránek
      Analýza obdobných webových stránek je klíčovým krokem při vývoji redakčního 
systému pro cyklistický magazín. Tato analýza nám umožňuje identifikovat běžné funkce, 
dobře praktické postupy a případné nedostatky existujících ření. Pro účely tohoto projektu jsem se 
zaměřil výhradně na české a slovenské cyklistické weby, které představují přímou 
konkurenci a zároveň mohou poskytnout cennou inspiraci. Na základě techto poznatků 
mužeme navrhnout systém, který bude kombinovat osvědčené postupy a zároveň nabídnout 
inovativní řešení problémů, se kterými se potkají existující weby.
1.1	MTBS.CZ
      Adresa: https://www.mtbs.cz
      MTBS.cz je specializované české web zaměřený na horskou cyklistiku, který 
nabízí články, recenze, technické tipy a informace o závodech. Tento web má dlouhou 
historii a velkou komunitu českých MTB jezdců.
      
1.1.1	Kladné stránky
	Silné komunitní prvky, včetně diskusních fór a možnosti sdílení tras
	Dobře strukturovaný katalog recenzí vybavený s možností porovnání
	Prehledná správa událostí a závodů s kalendářem a upozorněními
	Jednoduchá a přímočará navigace s důrazem na obsah
	Pravidelná aktualizace obsahu udržující komunitu aktivní
1.1.2	Záporné stránky
	Zastaralejší design, který nepůsobí moderně ve srovnání s konkurencí
	Omezené mobilní rozhraní, které není plně optimalizované
	Absence pokročilých multimediálních prvků (360° fotografie, interaktivní grafy)
	Méně propracovaný systém propagace vybraných článků na hlavní stránce
	Absence integrace s audio obsahem nebo podcasty

1.2	ROADCYCLING.CZ
      Adresa: https://www.roadcycling.cz
      RoadCycling je český web specializovaný na silniční cyklistiku, který se zaměřuje 
především na zpravodajství ze závodů, recenze silničních kol a technické články. Web 
poskytuje aktuální informace z domácí i světové silniční cyklistické scény.
      
1.2.1	Kladné stránky
	Specializovaný obsah zaměřený výhradně na silniční cyklistiku
	Kvalitní zpravodajství ze závodů s detailním obsahem a fotodokumentací
	Prehledné členění obsahu do tematických sekcí
	Propracované recenze vybavená s důrazem na technické detaily
	Kvalitní fotografie a videa doplňující textový obsah
1.2.2	Záporné stránky
	Méně propracovaný responzivní design na mobilních zařízeních
	Absence pokročilých filtrů pro vyhledávání v archivech článků
	Omezená interaktivita a možnosti zapojení uživatelů
	Jednodušší systém administrace bez pokročilých redakčních funkcí
	Chybějící personalizace obsahu pro registrované uživatele
1.3	MTBIKER.SK
      Adresa: https://www.mtbiker.sk
      MTBiker je slovenský web, který má silnou pozici i v české cyklistické komunitě. 
Zaměřuje se primárně na horskou cyklistiku, ale pokrčví i další disciplíny. Vyniká 
zejména rozsáhlým bazarem a velmi aktivní komunitou.
      
1.3.1	Kladné stránky
	Unikátní kombinace redakčního obsahu a komunitní platformy
	Rozsáhlý bazar s prehlednou kategorizací a vyhledáváním
	Kvalitní databáze tras s možností filtrování podle obtížnosti a lokality
	Aktivní fórum s rychlými reakcemi na dotazy uživatele
	Integrované prvky sociální síte pro cyklisty (profily, fotogalerie)
1.3.2	Záporné stránky
	Místy prehlcené uživatelské rozhraní s velkým množstvím informací
	Komplikovaná navigace pro nové uživatele
	Delší doba nátětní nekterých sekcí díky množství obsahu
	Omezené možnosti přizpusobení zobrazovaného obsahu
	Absence audio obsahu a pokročilých multimediálních prvků
 

2	Návrh projektu
      Kapitola "Návrh projektu" se zabývá detailním plánováním redakčního systému pro 
cyklistický magazín. Zde jsou definovány cílové skupiny uživatelů, struktura 
administracního rozhraní, databázový model a design webu. Tato kapitola je klíčová pro 
pochopení, jak byl systém navržen a jaké technologie a postupy byly zvoleny pro jeho 
implementaci. Následující podkapitoly poskytují podrobné informace o každé z techto 
oblastech.
      
      
HOMEPAGE 5. OBRÁZEK

 
2.1	Cílové skupiny
      Redakční systém je navržen jako budoucí verze webu cyklistickey.cz, který je 
urcen pro širokou verejnost se zájmem o cyklistiku. Web je koncipován tak, aby si v něm 
každé našel to své - od zacátečníku, který se teprve seznamuje s cyklistikou, pres rekreacní 
jezdce až po profesionální závodníky.
2.1.1	Zacátečníky a prležitostní cyklisti
	Základní informace o cyklistice a tipy pro zacátečníky
	Rady pro výber vybavení a kol
	Bezpecnostní doporučení a pravidla silničního provozu
	Inspirace pro první výlety a trasy
	Audio verze clánku pro pohodlné poslouchání behem jiných aktivit
2.1.2	Rekreacní jezdci
	Tipy na zajímavé trasy a výlety
	Recenze vybavená a doplnku
	Clánky o tréninku a kondici
	Zprávy o cyklistických událostech v regionu
	Možnost sdílení vlastních zitků a zkušeností
2.1.3	Zkušení cyklisti a závodníci
	Detailní technické clánky a analýzy
	Zpravodajství ze závodu a profesionální scény
	Pokročilé tréninkové tipy a metodiky
	Recenze profesionálního vybavení
	Specializované sekce pro různé disciplíny (silniční, horská, dráhová cyklistika)
2.1.4	Verejnost
	Prehledná kategorizace clánku podle témat a úrovně
	Možnost vyhledávání podle zájmu a zkušeností
	Audio verze clánku pro alternativní konzumaci obsahu
	Aktuální zprávy a novinky ze světa cyklistiky
	Interaktivní prvky pro lepší zapojení do komunity
2.2	Administrace webu
      Administracní rozhraní je klíčovou součástí redakčního systému, který umožňuje 
správu všeho obsahu a nastavení magazínu. Rozhraní je navrženo s durazem na 
prehlednost, intuitivnost a efektivitu práce redaktora a administrátora. Implementace 
vychází z aktuálních potreb redakce cyklistického magazínu a zahrnuje všechny nezbytné 
funkce pro efektivní správu obsahu.
      
      
HOMEPAGE ADMIN 6. OBRAZEK


2.2.1	Hlavní sekce administracního rozhraní:
	Dashboard - prehledová stránka s nejduležitějšími informacemi (pocet clánků, 
statistiky návtevnosti, nejnovější komentáře, rozpracované clánky)
	Správa clánku - sekce pro vytváření, edici, publikování a mazání clánku, vcetně 
možnosti nahrávání obrázku a audio souboru
	Správa kategorií - možnost vytvářet, upravovat a mazat kategorie pro trédení 
clánku
	Správa uživatelů - přidávání, editace a mazání uživatelských účtů, správa rol a 
oprávnení
	Statistiky - podrobné informace o návtevnosti jednotlivých clánků, kategorií a 
celého webu
	Nastavení - konfigurace systému, nastavení SEO parametrů, možnosti pro 
propojení se sociálními sítěmi
2.2.2	Dashboard (úvodní prehled):
	Prehledová stránka poskytující souhrn nejduležitějších informací
	Seznam nejnovějších clánků s indikací jejich stavu (publikováno/koncept)
	Rychlý průstup k základním funkcím systému
	Základní statistiky návtevnosti
2.2.3	Správa clánku:
	WYSIWYG editor pro pohodlnou tvorbu a úpravu clánku
	Nahrávání a správa obrázku vcetně vytváření náhledu
	Možnost plánování publikace clánku
	Systém pro přiřazení clánku do kategorií
	Nastavení SEO parametru jako URL (slug) clánku
	Volba viditelnosti clánku (publikováno/koncept)
	Integrace s audio soubory pro zvukové verze clánků
	Vyhledávání a filtrování clánků podle různých kritérií
	Razení clánků podle ID, názvu, data a viditelnosti
      
      
      WYSIWYG 7. OBRÁZEK
      
      
2.2.4	Správa kategorií:
	Vytvoření a editace kategorií pro trédení clánků
	Přiřazování clánků do kategorií
	Nastavení SEO-friendly URL pro kategorie
2.2.5	Správa uživatelů:
	Vytvoření a správa uživatelských účtů
	Nastavení uživatelských rol a oprávnení
	Editace profilových informací uživatele
	Zabezpecení ukládání hesel pomocí hashovacích funkcí
2.2.6	Správa propagovaných clánků:
	Možnost zvýraznit vybrané clánky na hlavní stránce
	Nastavení priorit propagovaných clánků
2.2.7	Prstupová práva:
	Konfigurace prstupu k různým částem administrace
	Omezené funkce podle uživatelské role
2.2.8	Statistiky:
	Sledování počtu zobrazení jednotlivých clánků
	Prehled ctenosti podle kategorií
	Základní analýza návtevnosti
2.2.9	Technické specifikace administrace:
	Implementace v PHP s využitím objektově orientovaného přístupu
	Responzivní design založený na frameworku Bootstrap 5
	Zabezpecení prstupu s využitím autentizace a autorizace
	Ochrana proti běžným webovým útokům (SQL injection, XSS)
	Optimalizace pro rychlé nátětní a práci s obsahem
      Administracní rozhraní je navrženo tak, aby ho mohli efektivně používat i méně 
technicky zdatní uživatelé. Intuitivní ovládání, prehledná navigace a konzistentní design 
prispívají k efektivitě práce redakčního tmu. Systém poskytuje všechny potřebné nástroje 
pro správu obsahu cyklistického magazínu a muže být v budoucnu dále rozšiřován podle 
rostoucích potreb redakce.
      
      
      ER DIAGRAM  8. OBRÁZEK
      
      
2.3	Databáze
      Databázová struktura projektu je navržena s ohledem na efektivní ukládání a správu 
všeho obsahu cyklistického magazínu. Pro implementaci byla zvolena relacní databáze 
MySQL, která poskytuje dobrou kombinaci výkonu, spolehlivosti a flexibility.
2.3.1	Hlavní databázové tabulky:
	clanky - uchovává informace o všech cláncích (id, název, obsah, datum, 
viditelnost, user_id, náhled_foto, url, audio)
	kategorie - obsahuje seznam kategorií (id, název_kategorie, url)
	clanky_kategorie - vazební tabulka pro vztah M:N mezi clánky a kategoriemi 
(id_clanku, id_kategorie)
	users - informace o uživatelích systému (id, email, heslo, name, surname, role, 
profil_foto, zahlavi_foto, popis)
	views_clanku - statistiky zobrazení clánků (id_clanku, pocet)
	admin_access - konfigurace prstupových práv k různým částem administrace 
(page, role_1, role_2)
2.3.2	Vztahy mezi tabulkami:
	Clánky mohou patřit do více kategorií a kategorie mohou obsahovat více clánků 
(vztah M:N realizovaný pomocí vazební tabulky clanky_kategorie)
	Každý clánek má prirazeného autora - uživatele (vztah 1:N mezi users a clánky)
	Každý clánek má svůj záznam v tabulce views_clanku pro sledování počtu 
zobrazení (vztah 1:1)
      Struktura tabulky clánky zahrnuje všechny potřebné atributy pro správu clánku, 
vcetně polí pro ukládání cesty k nahrávaným souborům (náhled_foto pro úvodní obrázek a 
audio pro zvukovou stopu). Pole viditelnost urcuje, zda je clánek publikován a zobrazuje 
se na webu, nebo je uložen jako koncept. Pole url uchovává SEO-friendly URL adresu 
clánku odvozenou z jeho názvu.
      Databáze je navržena s ohledem na výkon a úklovatelnost. Pro optimalizaci dotazu 
jsou vytvářeny vhodné indexy na často používaných polích (id, user_id, url). Referencní 
integrita je zajištěna pomocí cizích klíčů, které zabrání vzniku nekonzistentních dat 
(napr. nelze smazat kategorii, která obsahuje clánky, bez predchozího očetrení).
2.4	Design a responzivita
      Design webového magazínu je navržen s durazem na cistotu, prehlednost a snadnou 
orientaci. Vizuální styl odpovídá zaměření na cyklistiku - využívá dynamických prvků, 
sportovní barevné schéma a dostatek prostoru pro kvalitní fotografie.
      
      
      BAREVNÉ SCHÉMA A TYPOGRAFIE  9. OBRÁZEK
      
      
2.4.1	Klíčové principy designu:
	Minimalistický přístup s důrazem na obsah – čisté pozadí, kontrastní typografie pro snadnou čitelnost
	Hierarchické uspořádání prvků na stránce – důležité informace a hlavní články jsou zvýrazněny velikostí a umístěním
	Konzistentní vizuální prvky napříč celým webem – jednotný styl tlačítek, odkazů, nadpisů a menu
	Intuitivní navigace – přehledné horizontální menu s hlavními kategoriemi a doplňkové vertikální menu pro další funkce
	Efektivní využití "bílého prostoru" pro oddělení jednotlivých obsahových bloků
      Responzivita je klíčovým aspektem designu, který zajišťuje optimální zobrazení na všech zařízeních od mobilních telefonů přes tablety až po desktopové počítače. Implementace responzivity využívá CSS framework Bootstrap 5, který poskytuje flexibilní grid systém a předdefinované komponenty pro různé velikosti obrazovek.
      
      
      RESPONZIVITA   /PŘÍLOHA
-	PC - 10. OBRÁZEK
-	TABLET - 11. OBRÁZEK
-	MOBIL - 12. OBRÁZEK
      
      
2.4.2	Responzivní prvky:
	Fluidní layout, který se automaticky přizpůsobuje šířce obrazovky
	Flexibilní obrázky, které mění svou velikost v závislosti na dostupném prostoru
	Preskupení obsahu na menších obrazovkách - napr. změna rozložení ze 3 sloupců 
na 1 pro mobilní telefony
	Přizpůsobení navigacního menu - na malých obrazovkách se horizontální menu 
transformuje na "hamburger menu"
	Optimalizace formulářů a interaktivních prvků pro dotykové ovládání
      Testování responzivity bylo provedeno na různých zařízeních a v různých 
prohlécích, aby byla zajištěna konzistentní uživatelská zkušenost bez ohledu na způsob 
přístupu k webu. Díky důslednému použití responzivních technik je zajištěno, že čtenáři 
mohou pohodlně konzumovat obsah magazínu kdekoli a kdykoli.
 
3	Popis projektu
3.1	Frontend
      Frontend projektu představuje veřejnou část webového magazínu, se kterou 
interagují běžní čtenáři. Je navržen tak, aby poskytoval rychlý a intuitivní přístup k obsahu, 
zajímavý vizuální zážitek a optimální funkčnost na všech zařízeních.
      
      
      HLAVNÍ STRÁNKA - KLÍČOVÉ KOMPONENTY  13. OBRÁZEK
      

3.1.1	Struktura frontendové části:
	úvodní stránka - zobrazuje nejnovější a propagované clánky, prehled kategorií a 
stručné statistiky
	Stránky kategorií - výpis clánků patřících do konkrétní kategorie s možností 
filtrování a razení
	Detail clánku - zobrazení kompletního obsahu clánku vcetně multimediálních 
prvků a prehrávače audia
	Profil autora - informace o autorovi s prehledem jeho clánků
	Vyhledávání - funkce pro vyhledávání clánků podle klíčových slov s rozšířeními 
filtrů
3.1.2	Technologická implementace frontendové části využívá:
	HTML5 pro strukturu stránek
	CSS3 a Bootstrap 5 pro stylizaci a responzivitu
	JavaScript pro interaktivní prvky a dynamické nátětní obsahu
	PHP šablony pro generování HTML na serveru
      Každá stránka je optimalizována pro rychlé načítání - obrázky jsou komprimovány 
a načítány v odpovídající velikosti podle zařízení, JavaScript je minimalizován a CSS 
optimalizován. Implementace lazy-loadingu zajišťuje, že náročnější prvky (jako obrázky 
mimo viditelnou oblast nebo audio prehrávače) se načítají až ve chvíli, kdy se uživatel 
dostane do jejich blízkosti.
      Zvlátní pozornost byla věnována prehrávači audio obsahu, který je plně integrován 
do stránky clánku. Prehrávac umožňuje základní ovládání (play/pause, posun v nátětné, 
změna hlasitosti) a je optimalizován i pro mobilní zarázení. Audio soubory jsou ukládány 
ve formátech MP3 a OGG pro zajištění kompatibility s různými prohlécími. 
      
      
      DETAIL CLÁNKU - AUDIO, MULTIMEDIA  14. OBRÁZEK
      
      PROFIL AUTORA   15. OBRÁZEK
      
      
      
3.2	Backend
      Backend projektu zajišťuje veškerou logiku aplikace, komunikaci s databází a 
generování obsahu pro frontend. Je implementován v PHP s využitím objektově 
orientovaného přístupu a MVC architektury pro lepší organizaci kódu a snadnou údržbu.
3.2.1	Architektura backendu:
3.2.1.1	Modely (App/Models) - reprezentují datové entity a zajišťují 
komunikaci s databází
	Article.php - správa clánků vcetně kategorií a statistik
	Category.php - operace s kategoriemi
	User.php - správa uživatelů a autentizace
	AccessControl.php - řízení přístupu a oprávnení
	Statistics.php - sber a analýza statistických dat
3.2.1.2	Kontrolery (App/Controllers) - zpracovávají požadavky uživatelů a 
propojují modely s pohledy
	Admin/ - kontrolery pro administracní rozhraní
	Web/ - kontrolery pro verejnou část webu
	LoginController.php - správa přihlašování a autentizace
3.2.1.3	Pohledy (App/Views) - šablony pro generování HTML výstupu
3.2.1.4	Pomocné třídy (App/Helpers) - utility pro zpracování obrázků, 
validaci formulářů, generování URL atp.
3.2.1.5	Middleware (App/Middleware) - komponenty pro filtrování HTTP 
požadavků, napr. overení autentizace
3.2.2	Klíčové funkce implementované v backendu:
	Autentizace a autorizace uživatelů - bezpečné přihlašování, správa sessions, 
kontrola oprávnení
	CRUD operace pro všechny entity - vytváření, čtení, aktualizace a mazání clánku, 
kategorií a uživatelů
	Správa multimediálního obsahu - nátětní, validace a zpracování obrázku a audio 
souboru
	Generování SEO-friendly URL - automatické vytváření citelných adres z názvu 
clánku a kategorií
	Sber statistik - sledování počtu zobrazení clánku a analýza návtevnosti
	API pro asynchronní operace - endpointy pro AJAX požadavky z frontendu(není 
součástí projektu)


ADMINISTRACE CLÁNKU  16. OBRÁZEK



3.2.3	Pro zabezpecení aplikace jsou implementována následující opatření:
	Hashování hesel pomocí bcrypt algoritmu
	Ochrana proti SQL injection pomocí prepared statements
	Validace a sanitizace všech vstupů od uživatele
	CSRF ochrana pro formuláře v administraci
	XSS prevence pomocí escapování výstupu
      Backend je navržen tak, aby byl modulární a snadno rozšiřitelný. Díky dodržování 
principu OOP a SOLID je možné přidávat nové funkce nebo modifikovat stávající bez 
nutnosti zásadních změn v již existujícím kódu.


ADMINISTRACE STATISTIKY 17. OBRÁZEK


      
      
       
Závěr
      Tato maturitní práce se zabývala vývojem redakčního systému pro cyklistický 
magazín cyklistickey.cz, který představuje komplexní řešení pro správu a publikování 
specializovaného obsahu. Výsledný systém splňuje všechny požadavky stanovené v zadání 
a úspěšně naplňuje svůj účel – poskytovat redakci efektivní nástroj pro publikování clánků 
a čtenářům příjemné prostředí pro konzumaci obsahu.
      V rámci přípravné fáze byly analyzovány existující řešení (bikeandride.cz, 
velonews.com a mtbs.cz), což umožnilo identifikovat jejich silné stránky i nedostatky. Tyto 
poznatky byly následně zohledněny při návrhu vlastního systému, který implementuje 
osvědčené postupy a zároveň nabízí inovativní prvky, jako je integrace audio obsahu nebo 
pokročilé statistiky čtenosti.
      Technologicky je projekt postaven na moderním stacku – PHP s objektově orientovaným přístupem a MVC architekturou pro backend, MySQL pro databázovou 
vrstvu, a HTML5, CSS3 a JavaScript s frameworkem Bootstrap 5 pro frontend. Tato 
kombinace zajišťuje nejen robustnost a udržitelnost kódu, ale také optimální uživatelský 
zážitek napříč různými zařízeními.
Znacnou pozornost jsem během vývoje věnoval bezpečnostním aspektům. Systém 
implementuje:
	Ochranu proti SQL injection útokům pomocí parametrizovaných dotazů (prepared 
statements)
	Obranu proti XSS útokům prostřednictvím důsledného escapování výstupu 
(htmlspecialchars)
	Bezpečné ukládání hesel s využitím hashování (password_hash)
	Propracovaný systém řízení přístupu založený na uživatelských rolích
Mezi klíčové funkce implementovaného systému patří:
	Intuitivní WYSIWYG editor pro vytváření a editaci clánků
	Flexibilní kategorizace obsahu s možností přiřazení clánku do více kategorií
	Systém pro správu multimediálního obsahu včetně obrázků a audio nahrávek
	Propracované statistické nástroje pro sledování čtenosti jednotlivých clánků
	Responzivní design optimalizovaný pro všechny typy zařízení
	Modulární architektura umožňující snadné rozšiřování funkcionality
      Behem vývoje jsem byl konfrontován s několika technickými výzvami, zejména v 
oblasti optimalizace výkonu při práci s multimediálním obsahem a implementaci 
efektivního systému řízení přístupu. Tyto problémy byly úspěšně vyřešeny díky kombinaci 
důkladné analýzy, experimentování s různými přístupy a iterativního testování.
V rámci budoucího rozvoje projektu by bylo vhodné implementovat:
	Pokročilou ochranu proti CSRF útokům prostřednictvím tokenů pro všechny 
formuláře
	Systém pro uživatelské komentáře s možností moderace
	Automatickou integraci se sociálními sítěmi pro sdílení nového obsahu
	Mobilní aplikaci synchronizovanou s webovou verzí systému
	Pokročilé analytické nástroje pro detailnější analýzu uživatelského chování
	Systém personalizovaných doporučení obsahu pro registrované uživatele
      Z osobního hlediska mi tento projekt poskytl cennou příležitost propojit teoretické 
znalosti získané během studia s praktickými dovednostmi potřebnými pro vývoj reálné 
webové aplikace. Získané zkušenosti z oblasti webových technologií, databázového 
návrhu, bezpečnostních principů a responzivního designu představují solidní základ pro mé 
další profesní působení v oblasti vývoje webových aplikací.
 
Seznam přístupových údajů
      URL adresa webu: https://vincenon21.mp.spse-net.cz
úroveň oprávnení
Přihlašovací jméno
Heslo
Administrátor
admin@cyklistickey.cz
admin
Editor
editor@cyklistickey.cz
editor
Redaktor
redaktor@cyklistickey.cz
redaktor
      
 
Seznam použitých literatury a zdrojů obrázků
Literatura:
	DUCKETT, Jon. HTML & CSS: design and build websites. Indianapolis, IN: 
Wiley, 2011. ISBN 978-1118008188.
	NIXON, Robin. Learning PHP, MySQL & JavaScript: with jQuery, CSS & 
HTML5. 5th edition. Sebastopol, CA: O'Reilly Media, 2018. ISBN 978-
1491978917.
�	STAUFFER, Matt. Laravel: Up & Running: A Framework for Building Modern 
PHP Apps. 2nd edition. Sebastopol, CA: O'Reilly Media, 2019. ISBN 978-
1492041214.
�	KRUG, Steve. Don't make me think, revisited: a common sense approach to web 
usability. [3rd ed.]. Berkeley, Calif.: New Riders, 2014. ISBN 978-0321965516.
�	CĚK, Jan. PHP a MySQL: Hotové ření. Brno: Computer Press, 2020. ISBN 
978-80-251-4937-2.
	HOGAN, Brian P. HTML5 a CSS3: výukový kurz webového vývojáře. Brno: 
Computer Press, 2017. ISBN 978-80-251-4365-3.
	HAUSER, Marianne, et al. HTML5 Guidelines for Web Developers. München: 
Addison-Wesley, 2011. ISBN 978-0321772749.
	TATROE, Kevin, MACINTYRE, Peter. Programming PHP. 4th edition. 
Sebastopol, CA: O'Reilly Media, 2020. ISBN 978-1492054139.
      
Online zdroje:
	TRAVERSY, Brad. Modern JavaScript From The Beginning. Udemy [online]. 
2020 [cit. 2024-01-15]. Dostupné z: https://www.udemy.com/course/modern-
javascript-from-the-beginning/
	ACHOUR, Mehdi, et al. PHP Documentation [online]. The PHP Group, 2023 [cit. 
2024-01-10]. Dostupné z: https://www.php.net/docs.php
	Bootstrap Documentation [online]. The Bootstrap Team, 2023 [cit. 2024-01-12]. 
Dostupné z: https://getbootstrap.com/docs/5.0/
	MDN Web Docs [online]. Mozilla Foundation, 2023 [cit. 2024-01-20]. Dostupné z: 
https://developer.mozilla.org/
	CĚK, Pavel. Jak začit tvorit responzivní weby [online]. Zdroják, 2021 [cit. 2024-
02-10]. Dostupné z: https://zdrojak.cz/clanky/jak-zacit-tvorit-responzivni-weby/
	W3Schools. CSS Grid Layout [online]. 2023 [cit. 2024-03-05]. Dostupné z: 
https://www.w3schools.com/css/css_grid.asp
	VOSKA, Martin. Jak efektivně strukturovat kód v PHP aplikaci [online]. 2021 [cit. 
2024-02-20]. Dostupné z: https://blog.martinvoska.cz/jak-efektivně-strukturovat-
kod-v-php-aplikaci 
   Zdroje obrázků:
	úvodní strana, banner: Vlastní fotografie
	Ikony: Font Awesome (https://fontawesome.com) - licence Creative Commons 
Attribution 4.0 

Seznam obrázků
Obrázek 1: Logo a úvodní stránka cyklistickey.cz (strana 10)
Obrázek 2: Screenshot hlavní stránky mtbs.cz (strana 14)
Obrázek 3: Screenshot hlavní stránky roadcycling.cz (strana 15)
Obrázek 4: Screenshot hlavní stránky mtbiker.sk (strana 16)

Obrázek 5: Dashboard administracního rozhraní (strana 14)
Obrázek 6: WYSIWYG editor pro tvorbu clánku (strana 15)
Obrázek 7: ER diagram databáze (strana 18)
Obrázek 8: Barevné schéma a typografie webu (strana 21)
Obrázek 9: Ukázka responzivního designu (strana 22)
Obrázek 10: Hlavní stránka s klíčovými komponentami (strana 25)
Obrázek 11: Detail clánku s audio prehrávačem (strana 27)
Obrázek 12: Profil autora (strana 28)
Obrázek 13: Diagram MVC architektury aplikace (strana 30)
Obrázek 14: Administrace clánku (strana 31)
Obrázek 15: Ukázka administrace statistik (strana 32)
Obrázek 16: Systém flashnews v horní části webu (strana 33) 
Přílohy
Příloha 1: 


      Use case diagram
 
      E-R diagram
 
      Responzivita - vložte obrázek webu na počítač, na mobilu, prp. na tabletu.
 
      Obrázky dalších stránek webu - vložte screenshoty všech hlavních stránek webu 
vcetně ukázek administrace.
      
	- 1 -
      
