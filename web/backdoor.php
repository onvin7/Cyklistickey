<?php

declare(strict_types=1);

$db = new PDO("mysql:host=db.mp.spse-net.cz;dbname=vincenon21_1", "vincenon21", "larahobujulu");

// dump.sql -> soubor se zálohou databáze 
$db->exec(file_get_contents("dump.sql"));
