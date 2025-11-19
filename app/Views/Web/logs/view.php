<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Log: <?= htmlspecialchars($logFileName) ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            background: #000;
            color: #ccc;
            margin: 0;
            padding: 20px;
        }
        .line {
            white-space: pre-wrap;
            word-break: break-all;
        }
        .error { color: #ff4444; }
        .warning { color: #ffaa00; }
        .debug { color: #00aaff; }
    </style>
</head>
<body>
<?php if (empty($lines)): ?>
    <div>Log soubor je prázdný.</div>
<?php else: ?>
    <?php foreach ($lines as $line): ?>
        <div class="line<?php 
            if (stripos($line, 'ERROR') !== false || stripos($line, 'Fatal error') !== false) echo ' error';
            elseif (stripos($line, 'WARNING') !== false || stripos($line, 'Warning') !== false) echo ' warning';
            elseif (stripos($line, 'DEBUG') !== false) echo ' debug';
        ?>"><?= htmlspecialchars($line) ?></div>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>
