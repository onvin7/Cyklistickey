# Komplexní přehled statistických možností systému

## Obsah
1. [Statistiky zobrazení článků](#1-statistiky-zobrazení-článků)
2. [Statistiky podle kategorií](#2-statistiky-podle-kategorií)
3. [Statistiky autorů](#3-statistiky-autorů)
4. [Statistiky propagace](#4-statistiky-propagace)
5. [Časové statistiky](#5-časové-statistiky)
6. [Statistiky přístupových práv](#6-statistiky-přístupových-práv)
7. [Křížové statistiky](#7-křížové-statistiky)
8. [Životní cyklus obsahu](#8-životní-cyklus-obsahu)
9. [Technické implementace](#9-technické-implementace)

---

## 1. Statistiky zobrazení článků

### 1.1 Nejčtenější články za časové období
- **Datový zdroj:** `views_clanku`, `clanky`
- **Vizualizace:** Sloupcový graf nebo tabulka s řazením
- **SQL dotaz:**
  ```sql
  SELECT c.nazev, SUM(v.pocet) as celkem 
  FROM views_clanku v 
  JOIN clanky c ON v.id_clanku = c.id 
  WHERE v.datum BETWEEN :od AND :do 
  GROUP BY c.id
  ORDER BY celkem DESC 
  LIMIT 10
  ```
- **Použitelnost:** Dashboard, sekce statistik
- **Doporučení:** Interaktivní volba časového období (den, týden, měsíc, rok, vlastní)

### 1.2 Vývoj čtenosti článků v čase
- **Datový zdroj:** `views_clanku`, `clanky`
- **Vizualizace:** Spojnicový graf
- **SQL dotaz:**
  ```sql
  SELECT v.datum, SUM(v.pocet) as denni_pocet 
  FROM views_clanku v 
  WHERE v.datum BETWEEN :od AND :do 
  GROUP BY v.datum
  ORDER BY v.datum
  ```
- **Použitelnost:** Dashboard, detailní analýzy
- **Doporučení:** Přidat možnost porovnání s předchozím obdobím

### 1.3 Souhrnné statistiky zobrazení
- **Datový zdroj:** `views_clanku`, `clanky`
- **Vizualizace:** Karty s čísly (cards)
- **SQL dotaz:**
  ```sql
  -- Celkový počet zobrazení
  SELECT SUM(pocet) FROM views_clanku;
  
  -- Průměrný počet zobrazení na článek
  SELECT AVG(celkem) FROM 
    (SELECT id_clanku, SUM(pocet) as celkem FROM views_clanku GROUP BY id_clanku) AS t;
  
  -- Medián zobrazení
  -- (složitější, vyžaduje procedurální přístup)
  ```
- **Použitelnost:** Dashboard
- **Doporučení:** Zobrazit změnu oproti předchozímu obdobím (růst/pokles v %)

### 1.4 Histogram zobrazení článků
- **Datový zdroj:** `views_clanku` 
- **Vizualizace:** Histogram
- **Implementace:** Články rozdělené do skupin podle počtu zobrazení (0-10, 11-100, 101-1000, 1000+)
- **Použitelnost:** Analytická sekce
- **Doporučení:** Pomáhá identifikovat rozdělení popularity článků

### 1.5 Heatmapa denních zobrazení
- **Datový zdroj:** `views_clanku`
- **Vizualizace:** Heatmapa (kalendářní pohled)
- **Implementace:** Kalendářní pohled s barevným označením dnů podle počtu zobrazení
- **Použitelnost:** Analytická sekce
- **Doporučení:** Pomáhá identifikovat sezónní trendy a špičky návštěvnosti

## 2. Statistiky podle kategorií

### 2.1 Nejpopulárnější kategorie podle počtu zobrazení
- **Datový zdroj:** `kategorie`, `clanky_kategorie`, `views_clanku`
- **Vizualizace:** Koláčový nebo prstencový graf (donut chart)
- **SQL dotaz:**
  ```sql
  SELECT k.nazev_kategorie, SUM(v.pocet) as zobrazeni
  FROM views_clanku v
  JOIN clanky_kategorie ck ON v.id_clanku = ck.id_clanku
  JOIN kategorie k ON ck.id_kategorie = k.id
  GROUP BY k.id
  ORDER BY zobrazeni DESC
  ```
- **Použitelnost:** Dashboard, sekce statistik
- **Doporučení:** Interaktivní klikatelný graf s možností filtrovat články v dané kategorii

### 2.2 Počet článků v jednotlivých kategoriích
- **Datový zdroj:** `kategorie`, `clanky_kategorie`
- **Vizualizace:** Horizontální sloupcový graf
- **SQL dotaz:**
  ```sql
  SELECT k.nazev_kategorie, COUNT(ck.id_clanku) as pocet
  FROM kategorie k
  LEFT JOIN clanky_kategorie ck ON k.id = ck.id_kategorie
  GROUP BY k.id
  ORDER BY pocet DESC
  ```
- **Použitelnost:** Sekce správy kategorií
- **Doporučení:** Barevné odlišení aktivních a neaktivních kategorií

### 2.3 Vývoj popularity kategorií v čase
- **Datový zdroj:** `kategorie`, `clanky_kategorie`, `views_clanku`
- **Vizualizace:** Skládaný plošný graf (stacked area chart)
- **Implementace:** Zobrazení trendů popularity kategorií v čase
- **Použitelnost:** Analytická sekce
- **Doporučení:** Umožnit zapínat/vypínat jednotlivé kategorie v grafu

### 2.4 Matice podobnosti kategorií
- **Datový zdroj:** `kategorie`, `clanky_kategorie`
- **Vizualizace:** Teplotní mapa (heat map)
- **Implementace:** Analýza, jak často se kategorie vyskytují společně u článků
- **Použitelnost:** Analytická sekce, doporučování obsahu
- **Doporučení:** Pomáhá identifikovat příbuzná témata a vylepšit taxonomii webu

### 2.5 Konverzní poměr kategorií (zobrazení na článek)
- **Datový zdroj:** `kategorie`, `clanky_kategorie`, `views_clanku`, `clanky`
- **Vizualizace:** Sloupcový graf + tabulka
- **Implementace:** Průměrný počet zobrazení na jeden článek v kategorii
- **Použitelnost:** Analytická sekce, plánování obsahu
- **Doporučení:** Pomáhá identifikovat kategorie s vysokou návratností investice

## 3. Statistiky autorů

### 3.1 Nejproduktivnější autoři (počet článků)
- **Datový zdroj:** `users`, `clanky`
- **Vizualizace:** Sloupcový graf nebo tabulka
- **SQL dotaz:**
  ```sql
  SELECT u.name, u.surname, COUNT(c.id) as pocet_clanku
  FROM users u
  JOIN clanky c ON u.id = c.user_id
  GROUP BY u.id
  ORDER BY pocet_clanku DESC
  ```
- **Použitelnost:** Dashboard, sekce autorů
- **Doporučení:** Přidat filtr časového období (měsíc, kvartál, rok)

### 3.2 Nejčtenější autoři (celkový počet zobrazení)
- **Datový zdroj:** `users`, `clanky`, `views_clanku`
- **Vizualizace:** Sloupcový graf nebo tabulka
- **SQL dotaz:**
  ```sql
  SELECT u.name, u.surname, SUM(v.pocet) as celkem_zobrazeni
  FROM users u
  JOIN clanky c ON u.id = c.user_id
  JOIN views_clanku v ON c.id = v.id_clanku
  GROUP BY u.id
  ORDER BY celkem_zobrazeni DESC
  ```
- **Použitelnost:** Dashboard, sekce autorů
- **Doporučení:** Možnost přepínat mezi absolutním počtem a normalizovaným (na počet článků)

### 3.3 Průměrný počet zobrazení na článek podle autora
- **Datový zdroj:** `users`, `clanky`, `views_clanku`
- **Vizualizace:** Sloupcový graf s průměrnými hodnotami
- **SQL dotaz:**
  ```sql
  SELECT u.name, u.surname, 
         SUM(v.pocet) / COUNT(DISTINCT c.id) as prumer_zobrazeni
  FROM users u
  JOIN clanky c ON u.id = c.user_id
  JOIN views_clanku v ON c.id = v.id_clanku
  GROUP BY u.id
  ORDER BY prumer_zobrazeni DESC
  ```
- **Použitelnost:** Sekce autorů, hodnocení výkonu
- **Doporučení:** Doplnit o vizualizaci rozptylu (box plot)

### 3.4 Aktivita autorů v čase
- **Datový zdroj:** `users`, `clanky`
- **Vizualizace:** Spojnicový graf nebo heatmapa
- **Implementace:** Počet publikovaných článků v čase podle autorů
- **Použitelnost:** Sekce autorů, plánování obsahu
- **Doporučení:** Umožnit identifikovat aktivní a neaktivní autory

### 3.5 Analýza témat podle autorů
- **Datový zdroj:** `users`, `clanky`, `clanky_kategorie`, `kategorie`
- **Vizualizace:** Radarový graf nebo teplotní mapa
- **Implementace:** Zobrazení afinit autorů k tématům/kategoriím
- **Použitelnost:** Sekce autorů, plánování obsahu
- **Doporučení:** Pomáhá identifikovat expertízu autorů a plánovat obsah

## 4. Statistiky propagace

### 4.1 Efektivita propagace článků
- **Datový zdroj:** `propagace`, `clanky`, `views_clanku`
- **Vizualizace:** Sloupcový nebo spojnicový graf
- **Implementace:** Porovnání zobrazení před, během a po propagaci
- **Použitelnost:** Sekce marketingu, ROI analýza
- **Doporučení:** Automatická analýza efektivity s doporučeními

### 4.2 ROI propagace
- **Datový zdroj:** `propagace`, `views_clanku`
- **Vizualizace:** Tabulka nebo bodový graf
- **Implementace:** Poměr nákladů a získaných zobrazení
- **Použitelnost:** Sekce marketingu, plánování rozpočtu
- **Doporučení:** Doplnit o prediktivní model pro budoucí kampaně

### 4.3 Porovnání období s propagací a bez propagace
- **Datový zdroj:** `propagace`, `views_clanku`
- **Vizualizace:** Srovnávací sloupcový graf
- **Implementace:** A/B srovnání období s a bez propagace
- **Použitelnost:** Sekce marketingu, optimalizace kampaní
- **Doporučení:** Doplnit o statistické testování významnosti rozdílů

### 4.4 Heatmapa úspěšnosti propagace
- **Datový zdroj:** `propagace`, `views_clanku`
- **Vizualizace:** Teplotní mapa
- **Implementace:** Analýza úspěšnosti propagace podle dnů v týdnu a hodin
- **Použitelnost:** Sekce marketingu, optimalizace načasování
- **Doporučení:** Pomáhá identifikovat optimální časy pro propagaci

### 4.5 Analýza dlouhodobého dopadu propagace
- **Datový zdroj:** `propagace`, `views_clanku`
- **Vizualizace:** Spojnicový graf
- **Implementace:** Sledování efektu propagace v delším časovém horizontu
- **Použitelnost:** Sekce marketingu, strategické plánování
- **Doporučení:** Pomáhá identifikovat trvalé změny v popularitě článků

## 5. Časové statistiky

### 5.1 Publikační aktivita v čase
- **Datový zdroj:** `clanky`
- **Vizualizace:** Spojnicový nebo sloupcový graf
- **SQL dotaz:**
  ```sql
  SELECT DATE_FORMAT(datum, '%Y-%m') as mesic, 
         COUNT(*) as pocet_clanku
  FROM clanky
  GROUP BY mesic
  ORDER BY mesic
  ```
- **Použitelnost:** Dashboard, sekce plánování
- **Doporučení:** Doplnit o porovnání s předchozími obdobími

### 5.2 Nejpopulárnější dny v týdnu pro publikaci
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Radarový graf nebo sloupcový graf
- **SQL dotaz:**
  ```sql
  SELECT DAYOFWEEK(c.datum) as den_v_tydnu, 
         AVG(v.pocet) as prumerne_zobrazeni
  FROM clanky c
  JOIN views_clanku v ON c.id = v.id_clanku
  GROUP BY den_v_tydnu
  ORDER BY den_v_tydnu
  ```
- **Použitelnost:** Sekce plánování publikací
- **Doporučení:** Doplnit o analýzu nejlepšího času publikace v rámci dne

### 5.3 Sezónní trendy v zobrazení
- **Datový zdroj:** `views_clanku`
- **Vizualizace:** Spojnicový graf s dekompozicí trendu
- **Implementace:** Analýza sezónních vzorců v zobrazení článků
- **Použitelnost:** Sekce strategie obsahu
- **Doporučení:** Propojit s plánováním témat podle sezóny

### 5.4 Frekvence publikace a její dopad na čtenost
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Bodový graf s regresní křivkou
- **Implementace:** Analýza vztahu mezi frekvencí publikace a čteností
- **Použitelnost:** Sekce strategie obsahu
- **Doporučení:** Pomáhá stanovit optimální frekvenci publikování

### 5.5 Životní cyklus článků
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Kumulativní spojnicový graf
- **Implementace:** Analýza typického vývoje zobrazení článku od publikace
- **Použitelnost:** Sekce analýzy obsahu
- **Doporučení:** Identifikace "evergreen" obsahu vs. krátkodobě populárních článků

## 6. Statistiky přístupových práv

### 6.1 Historie změn v přístupových právech
- **Datový zdroj:** `admin_access_logs`
- **Vizualizace:** Časová osa nebo tabulka
- **SQL dotaz:**
  ```sql
  SELECT l.change_date, u.name, u.surname, l.page,
         l.role_1, l.role_2
  FROM admin_access_logs l
  JOIN users u ON l.changed_by = u.id
  ORDER BY l.change_date DESC
  ```
- **Použitelnost:** Sekce administrace, bezpečnostní audit
- **Doporučení:** Filtrování podle stránky, role nebo administrátora

### 6.2 Přehled administrátorských aktivit
- **Datový zdroj:** `admin_access_logs`, `users`
- **Vizualizace:** Sloupcový graf nebo tabulka
- **Implementace:** Počet změn práv podle administrátora
- **Použitelnost:** Sekce administrace, monitoring
- **Doporučení:** Pomáhá identifikovat aktivní a neaktivní administrátory

### 6.3 Matice přístupových práv
- **Datový zdroj:** `admin_access`
- **Vizualizace:** Teplotní mapa
- **Implementace:** Vizualizace práv podle rolí a stránek
- **Použitelnost:** Sekce správy přístupů
- **Doporučení:** Interaktivní možnost změny práv přímo z matice

### 6.4 Analýza změn práv v čase
- **Datový zdroj:** `admin_access_logs`
- **Vizualizace:** Spojnicový graf nebo skládaný sloupcový graf
- **Implementace:** Sledování trendů v přidělování práv v čase
- **Použitelnost:** Sekce administrace
- **Doporučení:** Pomáhá identifikovat postupné změny v bezpečnostní politice

### 6.5 Audit nekonzistencí v právech
- **Datový zdroj:** `admin_access`, `users`
- **Vizualizace:** Tabulka s upozorněními
- **Implementace:** Identifikace potenciálních bezpečnostních problémů
- **Použitelnost:** Sekce administrace, bezpečnostní kontroly
- **Doporučení:** Automatické upozornění na neobvyklé konfigurace práv

## 7. Křížové statistiky

### 7.1 Matice korelace mezi kategoriemi a autory
- **Datový zdroj:** `kategorie`, `clanky_kategorie`, `clanky`, `users`
- **Vizualizace:** Teplotní mapa
- **Implementace:** Analýza afinit mezi autory a kategoriemi
- **Použitelnost:** Strategické plánování obsahu
- **Doporučení:** Pomáhá identifikovat specializace autorů

### 7.2 Analýza vztahu mezi délkou článku a popularitou
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Bodový graf s regresní křivkou
- **Implementace:** Velikost obsahu vs. počet zobrazení
- **Použitelnost:** Optimalizace obsahu
- **Doporučení:** Stanovení doporučené délky článků

### 7.3 Vývoj témat v čase
- **Datový zdroj:** `clanky`, `clanky_kategorie`, `kategorie`
- **Vizualizace:** Proudový graf (stream graph)
- **Implementace:** Analýza změn podílu témat v čase
- **Použitelnost:** Strategické plánování obsahu
- **Doporučení:** Identifikace rostoucích a klesajících témat

### 7.4 Multidimenzionální analýza
- **Datový zdroj:** Všechny relevantní tabulky
- **Vizualizace:** Interaktivní dashboard
- **Implementace:** Kombinace více dimenzí (autor, kategorie, čas, zobrazení)
- **Použitelnost:** Pokročilá analytika
- **Doporučení:** Možnost filtrování podle všech dimenzí

### 7.5 Prediktivní analýza budoucí popularity
- **Datový zdroj:** `clanky`, `views_clanku`, `kategorie`, `clanky_kategorie`
- **Vizualizace:** Spojnicový graf s predikcí
- **Implementace:** Strojové učení pro predikci budoucí popularity témat
- **Použitelnost:** Strategické plánování obsahu
- **Doporučení:** Aktualizace modelu na základě nových dat

## 8. Životní cyklus obsahu

### 8.1 Věková struktura obsahu
- **Datový zdroj:** `clanky`
- **Vizualizace:** Skládaný sloupcový graf nebo kruhový diagram
- **Implementace:** Rozdělení článků podle stáří (0-30 dnů, 1-3 měsíce, atd.)
- **Použitelnost:** Správa obsahu
- **Doporučení:** Identifikace potřeby aktualizace starých článků

### 8.2 ROI obsahu v čase
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Spojnicový graf
- **Implementace:** Počet zobrazení v závislosti na stáří článku
- **Použitelnost:** Analytika obsahu
- **Doporučení:** Identifikace "evergreen" obsahů

### 8.3 Analýza obnovy obsahu
- **Datový zdroj:** `clanky`
- **Vizualizace:** Sloupcový graf + kruhový diagram
- **Implementace:** Poměr nového vs. aktualizovaného obsahu
- **Použitelnost:** Správa obsahu
- **Doporučení:** Stanovení rovnováhy mezi tvorbou nového a údržbou existujícího obsahu

### 8.4 Konverzní trychtýř obsahu
- **Datový zdroj:** `clanky`, `views_clanku`
- **Vizualizace:** Trychtýřový graf
- **Implementace:** Sledování "cesty čtenáře" mezi souvisejícími články
- **Použitelnost:** Optimalizace obsahu, SEO
- **Doporučení:** Identifikace potřeby propojení obsahu

### 8.5 Analýza sezónnosti obsahu
- **Datový zdroj:** `clanky`, `views_clanku`, `kategorie`, `clanky_kategorie`
- **Vizualizace:** Heat mapa v kalendářním zobrazení
- **Implementace:** Identifikace sezónně populárních témat
- **Použitelnost:** Plánování obsahu
- **Doporučení:** Vytvoření kalendáře publikací na základě sezónních trendů

## 9. Technické implementace

### 9.1 Responzivní dashboard
- **Technologie:** ApexCharts, Bootstrap
- **Funkce:** Interaktivní grafy, mobilní zobrazení, filtry
- **Implementace:** Moduly pro různé statistiky s možností personalizace
- **Ukázka kódu:**
  ```javascript
  // Základní nastavení grafu
  const options = {
      chart: {
          type: 'line',
          height: 350,
          toolbar: {
              show: false
          },
          zoom: {
              enabled: true
          }
      },
      series: [{
          name: 'Zobrazení',
          data: viewData
      }],
      xaxis: {
          categories: dateLabels,
          title: {
              text: 'Datum'
          }
      },
      yaxis: {
          title: {
              text: 'Počet zobrazení'
          }
      },
      responsive: [{
          breakpoint: 768,
          options: {
              chart: {
                  height: 250
              },
              legend: {
                  position: 'bottom'
              }
          }
      }]
  };
  ```

### 9.2 Export dat
- **Formáty:** CSV, PDF, Excel
- **Funkce:** Export grafů i tabulek
- **Implementace:** Tlačítka pro export v každém modulu
- **Ukázka HTML:**
  ```html
  <div class="export-buttons">
      <button class="btn btn-sm btn-outline-primary export-csv">
          <i class="fa-solid fa-file-csv"></i> Exportovat CSV
      </button>
      <button class="btn btn-sm btn-outline-primary export-pdf">
          <i class="fa-solid fa-file-pdf"></i> Exportovat PDF
      </button>
      <button class="btn btn-sm btn-outline-primary export-excel">
          <i class="fa-solid fa-file-excel"></i> Exportovat Excel
      </button>
  </div>
  ```

### 9.3 Interaktivní filtry
- **Funkce:** Filtry pro časové období, kategorie, autory
- **Implementace:** Ajax nahrávání dat podle filtrů
- **Ukázka kódu:**
  ```php
  // Příklad filtru v PHP
  $date_from = $_POST['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
  $date_to = $_POST['date_to'] ?? date('Y-m-d');
  $category_id = (int)($_POST['category_id'] ?? 0);
  $author_id = (int)($_POST['author_id'] ?? 0);
  
  // Sestavení SQL dotazu podle filtrů
  $sql = "SELECT ... WHERE datum BETWEEN :date_from AND :date_to";
  if ($category_id > 0) {
      $sql .= " AND ck.id_kategorie = :category_id";
  }
  if ($author_id > 0) {
      $sql .= " AND c.user_id = :author_id";
  }
  ```

### 9.4 Optimalizace výkonu
- **Techniky:** Cachování, agregace dat
- **Implementace:** Přepočítávání agregovaných statistik v noci
- **Ukázka databázového schématu:**
  ```sql
  CREATE TABLE `statistiky_agregace` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `typ` varchar(50) NOT NULL,
    `den` date NOT NULL,
    `id_entity` int(11) NOT NULL,
    `hodnota` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_typ_den` (`typ`,`den`),
    KEY `idx_entity` (`id_entity`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ```

### 9.5 Real-time aktualizace
- **Technologie:** WebSockets, AJAX polling
- **Funkce:** Aktualizace statistik v reálném čase
- **Implementace:** Automatická aktualizace každých 5 minut
- **Ukázka JavaScriptu:**
  ```javascript
  // Automatická aktualizace dat
  setInterval(function() {
      fetch('/api/statistics/realtime')
          .then(response => response.json())
          .then(data => {
              chart.updateSeries([{
                  data: data.series
              }]);
              document.querySelector('.total-views').textContent = data.totalViews;
          });
  }, 300000); // 5 minut
  ``` 