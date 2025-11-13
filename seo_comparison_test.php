<?php
require_once 'config/autoloader.php';
require_once 'config/db.php';

use App\Helpers\SEOHelper;
use App\Helpers\AISEOHelper;

// Aktuální obsah článku z webu
$articleContent = "Začátkem září se ve Švýcarsku rozběhla jedna z největších cyklistických akcí letošního roku. V kantonu Valais se totiž rozběhlo multidisciplinární mistrovství světa horských kol. Jedny z prvních medailí se rozdávaly v sobotu 6. září v disciplíně cross-country maraton. Česká republika má v tomto odvětví horských kol tradičně velmi silné zastoupení. To se potvrdilo i letos, kdy Ondřej Cink dlouho bojoval o vítězství, ale nakonec nedosáhl ani na medaili.

Trať letošního maratonského mistrovství patřila k jedné z nejtěžších v historii. Celý závodní peloton si musel poradit se 125 km dlouhou alpskou trasou. V kopcích jihozápadního Švýcarska na maratonce čekalo neuvěřitelných 5000 výškových metrů. V úplném závěru závodu se cyklisté museli vyškrábat na Pas de Lona, které se nachází v nadmořské výšce 2787 m.n.m.

Startovní pole elitních mužů se na trasu vydalo už v 6:40 ráno. V úvodních kilometrech se na čele usadila poměrně početná skupina asi 15 závodníků. V první skupině se míchali závodníci, kteří se specializují na maratony a ti, kteří závodí spíše v cross-country. Maratonce v čelní skupině zastupoval např. Wout Alleman a Samuele Porro. Z cross-country závodníků se v první skupině pohyboval Ondřej Cink nebo David Valero Serrano.

Úvodní skupina jela poměrně kompaktně až pod závěrečný kopec. Ten měřil nekonečných patnáct kilometrů, které zakončil výběh na horské sedlo Pas de Lona, odkud už následoval pouze sjezd do cíle. V cílovém stoupání se skupina rozdělila na jednotlivce. Jako první horské sedlo překonal Američan Keegan Swenson, který se následně stal novým mistrem světa v maratonu. Druhý dojel Ital Porro a bronz ukořistil legendární Leonardo Paez.

Z Čechů se závod nejlépe povedl Ondřeji Cinkovi, který finišoval na 8. místě. Filip Adel s Martinem Stoškem obsadili 34. a 35. místo se ztrátou necelé půl hodiny na vítěze. Debut na mistrovství světa si odbyl osmapadesátý Vojtěch Neradil. Mezi ženami obsadila 24. místo Milena Kalšová.

Zatímco nejlepší elitní muži na trati strávili něco málo přes šest hodin, tak nejlepší ženy se s tratí potýkaly ještě o hodinu déle. Spanilou jízdu převedla Američanka Kate Courtney, která zvítězila stylem start cíl. V cíli stříbrná Anna Weinbeer jako jediná ze startovního pole dokázala Američance alespoň chvíli sekundovat. Bronz získala obhájkyně vítězství Mona Mitterwallner.";

$articleTitle = "Cink v TOP 10! Titul mistra světa v maratonu slaví Američan Swenson";

echo "<h1>🔍 SEO COMPARISON TEST</h1>";
echo "<h2>📰 Článek: " . htmlspecialchars($articleTitle) . "</h2>";
echo "<hr>";

echo "<h2>📊 AKTUÁLNÍ SEO (z webu)</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>Title:</strong> " . htmlspecialchars($articleTitle) . "<br>";
echo "<strong>Description:</strong> " . htmlspecialchars("Začátkem září se ve Švýcarsku rozběhla jedna z největších cyklistických akcí letošního roku...") . "<br>";
echo "<strong>Keywords:</strong> " . htmlspecialchars("cyklistika, závody, maraton, mistrovství světa, horská kola") . "<br>";
echo "</div>";

echo "<h2>🤖 NOVÉ SEO (naše AI logika)</h2>";

// Extrahuj klíčová slova pomocí našeho AI helperu
$keywords = AISEOHelper::extractKeywords($articleContent, 8);
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>Extrahovaná klíčová slova:</strong> " . implode(', ', $keywords) . "<br><br>";

// Generuj optimalizovaný title
$optimizedTitle = AISEOHelper::generateOptimizedTitle($articleTitle, $keywords);
echo "<strong>Optimalizovaný title:</strong> " . htmlspecialchars($optimizedTitle) . "<br><br>";

// Generuj optimalizovaný popis
$optimizedDescription = AISEOHelper::generateOptimizedDescription($articleContent, $keywords);
echo "<strong>Optimalizovaný description:</strong> " . htmlspecialchars($optimizedDescription) . "<br><br>";

// Generuj meta keywords
$metaKeywords = AISEOHelper::generateMetaKeywords($articleContent, $articleTitle, $keywords);
echo "<strong>Meta keywords:</strong> " . htmlspecialchars($metaKeywords) . "<br><br>";

// SEO analýza
$seoAnalysis = AISEOHelper::analyzeSEOQuality($optimizedTitle, $optimizedDescription, $articleContent, $keywords);
echo "<strong>SEO skóre:</strong> " . $seoAnalysis['percentage'] . "% (" . $seoAnalysis['score'] . "/" . $seoAnalysis['maxScore'] . ")<br>";
echo "<strong>Počet slov:</strong> " . $seoAnalysis['wordCount'] . "<br>";

if (!empty($seoAnalysis['issues'])) {
    echo "<strong>Problémy:</strong><br>";
    foreach ($seoAnalysis['issues'] as $issue) {
        echo "• " . htmlspecialchars($issue) . "<br>";
    }
}

echo "</div>";

echo "<h2>🔧 SEO HELPER INTEGRACE</h2>";

// Simulace použití v SEOHelper
$seoTitle = SEOHelper::generateTitle($articleTitle, null, $keywords);
$seoDescription = SEOHelper::generateDescription($articleContent, null, $keywords);
$seoKeywords = SEOHelper::generateKeywords($articleContent, $keywords);

echo "<div style='background: #e8f4fd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>SEOHelper Title:</strong> " . htmlspecialchars($seoTitle) . "<br><br>";
echo "<strong>SEOHelper Description:</strong> " . htmlspecialchars($seoDescription) . "<br><br>";
echo "<strong>SEOHelper Keywords:</strong> " . htmlspecialchars($seoKeywords) . "<br>";
echo "</div>";

echo "<h2>📈 POROVNÁNÍ VÝSLEDKŮ</h2>";

echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th style='padding: 10px;'>Element</th>";
echo "<th style='padding: 10px;'>Aktuální (web)</th>";
echo "<th style='padding: 10px;'>Nové (AI)</th>";
echo "<th style='padding: 10px;'>Zlepšení</th>";
echo "</tr>";

// Title comparison
$currentTitle = $articleTitle;
$newTitle = $seoTitle;
$titleImprovement = strlen($newTitle) > strlen($currentTitle) ? "✅ Delší" : (strlen($newTitle) < strlen($currentTitle) ? "✅ Kratší" : "➖ Stejné");

echo "<tr>";
echo "<td style='padding: 10px;'><strong>Title</strong></td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($currentTitle) . " (" . strlen($currentTitle) . " znaků)</td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($newTitle) . " (" . strlen($newTitle) . " znaků)</td>";
echo "<td style='padding: 10px;'>" . $titleImprovement . "</td>";
echo "</tr>";

// Description comparison
$currentDesc = "Začátkem září se ve Švýcarsku rozběhla jedna z největších cyklistických akcí letošního roku...";
$newDesc = $seoDescription;
$descImprovement = strlen($newDesc) > strlen($currentDesc) ? "✅ Delší" : (strlen($newDesc) < strlen($currentDesc) ? "✅ Kratší" : "➖ Stejné");

echo "<tr>";
echo "<td style='padding: 10px;'><strong>Description</strong></td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($currentDesc) . " (" . strlen($currentDesc) . " znaků)</td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($newDesc) . " (" . strlen($newDesc) . " znaků)</td>";
echo "<td style='padding: 10px;'>" . $descImprovement . "</td>";
echo "</tr>";

// Keywords comparison
$currentKeywords = "cyklistika, závody, maraton, mistrovství světa, horská kola";
$newKeywords = $seoKeywords;
$keywordCount = count(explode(', ', $newKeywords));
$currentKeywordCount = count(explode(', ', $currentKeywords));
$keywordImprovement = $keywordCount > $currentKeywordCount ? "✅ Více" : ($keywordCount < $currentKeywordCount ? "✅ Méně" : "➖ Stejné");

echo "<tr>";
echo "<td style='padding: 10px;'><strong>Keywords</strong></td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($currentKeywords) . " (" . $currentKeywordCount . " slov)</td>";
echo "<td style='padding: 10px;'>" . htmlspecialchars($newKeywords) . " (" . $keywordCount . " slov)</td>";
echo "<td style='padding: 10px;'>" . $keywordImprovement . "</td>";
echo "</tr>";

echo "</table>";

echo "<h2>🎯 DOPORUČENÍ PRO VYLEPŠENÍ</h2>";
$recommendations = AISEOHelper::generateSEORecommendations($seoAnalysis);
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
foreach ($recommendations as $rec) {
    echo "• " . htmlspecialchars($rec) . "<br>";
}
echo "</div>";

echo "<h2>💡 ZÁVĚR</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "Naše 'AI' logika je ve skutečnosti <strong>pokročilé PHP algoritmy</strong>, které:<br>";
echo "• Analyzují obsah a najdou nejrelevantnější věty<br>";
echo "• Extrahují klíčová slova pomocí frekvenční analýzy<br>";
echo "• Optimalizují délku titulků a popisů<br>";
echo "• Poskytují SEO skóre a doporučení<br><br>";
echo "<strong>NENÍ to skutečná AI</strong> - je to simulace pomocí matematických algoritmů! 🤖➡️📊";
echo "</div>";
?>
