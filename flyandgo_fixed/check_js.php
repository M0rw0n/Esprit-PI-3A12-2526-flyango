<?php
$content = file_get_contents(__DIR__ . '/templates/messenger/index.html.twig');

// Extract JavaScript section
$start = strpos($content, '<script>') + 8;
$end = strpos($content, '</script>');
$js = substr($content, $start, $end - $start);

echo "JS Length: " . strlen($js) . " chars\n";

// Check for unclosed strings
$inString = false;
$stringChar = '';
$line = 1;
$col = 0;
$issues = [];

for ($i = 0; $i < strlen($js); $i++) {
    $char = $js[$i];
    
    if ($char === "\n") {
        $line++;
        $col = 0;
        continue;
    }
    $col++;
    
    // Skip escaped chars
    if ($js[$i-1] ?? '' === '\\') continue;
    
    // Check for string start/end
    if ($char === '"' || $char === "'") {
        if (!$inString) {
            $inString = true;
            $stringChar = $char;
        } elseif ($char === $stringChar) {
            $inString = false;
        }
    }
}

if ($inString) {
    echo "WARNING: Unclosed string detected!\n";
}

// Check last 500 chars
echo "\nLast 500 chars of JS:\n";
echo substr($js, -500) . "\n";