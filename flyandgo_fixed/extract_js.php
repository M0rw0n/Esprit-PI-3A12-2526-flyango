<?php
$content = file_get_contents(__DIR__ . '/templates/messenger/index.html.twig');

// Extract JavaScript section
$start = strpos($content, '<script>') + 8;
$end = strpos($content, '</script>');
$js = substr($content, $start, $end - $start);

// Save JS to separate file
file_put_contents(__DIR__ . '/test_messenger.js', $js);

// Check for function definitions
$hasShowNewChatModal = strpos($js, 'function showNewChatModal') !== false;
$hasSwitchMessengerTab = strpos($js, 'function switchMessengerTab') !== false;

echo "JS file saved: test_messenger.js\n";
echo "showNewChatModal found: " . ($hasShowNewChatModal ? 'YES' : 'NO') . "\n";
echo "switchMessengerTab found: " . ($hasSwitchMessengerTab ? 'YES' : 'NO') . "\n";

// Try to parse JS with Node if available
$nodeExists = shell_exec('node --version');
if ($nodeExists) {
    echo "\nTrying Node.js syntax check...\n";
    $result = shell_exec('node -e "require(\"fs\").readFileSync(\"test_messenger.js\", \"utf8\")" 2>&1');
    echo $result;
} else {
    echo "\nNode.js not available for syntax check\n";
}