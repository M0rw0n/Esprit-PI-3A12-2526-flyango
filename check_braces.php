<?php
$content = file_get_contents(__DIR__ . '/templates/messenger/index.html.twig');
$open = substr_count($content, '{');
$close = substr_count($content, '}');
$scriptOpen = substr_count($content, '<script>');
$scriptClose = substr_count($content, '</script>');

echo "Braces: { = $open, } = $close\n";
echo "Scripts: <script> = $scriptOpen, </script> = $scriptClose\n";

// Check for last script tag
$lastScriptPos = strrpos($content, '<script>');
$lastScriptClosePos = strrpos($content, '</script>');
echo "Last <script>: $lastScriptPos\n";
echo "Last </script>: $lastScriptClosePos\n";

// Get content after last </script>
$afterScript = substr($content, $lastScriptClosePos + 9);
echo "After last script: " . strlen($afterScript) . " chars\n";
echo "Starts with: " . substr($afterScript, 0, 50) . "\n";