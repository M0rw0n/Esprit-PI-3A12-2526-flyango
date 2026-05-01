<?php
$content = file_get_contents(__DIR__ . '/templates/messenger/index.html.twig');

// Extract JavaScript section
$start = strpos($content, '<script>') + 8;
$end = strpos($content, '</script>');
$js = substr($content, $start, $end - $start);

$inString = false;
$stringChar = '';
$line = 1;
$col = 0;
$startLine = 0;
$startCol = 0;

for ($i = 0; $i < strlen($js); $i++) {
    $char = $js[$i];
    
    if ($char === "\n") {
        $line++;
        $col = 0;
        continue;
    }
    $col++;
    
    // Skip escaped chars
    if (($js[$i-1] ?? '') === '\\') continue;
    
    // Check for string start/end
    if ($char === '"' || $char === "'") {
        if (!$inString) {
            $inString = true;
            $stringChar = $char;
            $startLine = $line;
            $startCol = $col;
        } elseif ($char === $stringChar) {
            $inString = false;
        }
    }
}

if ($inString) {
    echo "Unclosed string at line $startLine, column $startCol\n";
    
    // Show context around that line
    $lines = explode("\n", $js);
    $startShow = max(0, $startLine - 5);
    $endShow = min(count($lines), $startLine + 5);
    
    echo "\nContext (lines $startShow to $endShow):\n";
    for ($i = $startShow; $i < $endShow; $i++) {
        $marker = ($i === $startLine - 1) ? " >>> " : "     ";
        echo $marker . ($i + 1) . ": " . substr($lines[$i], 0, 200) . "\n";
    }
}