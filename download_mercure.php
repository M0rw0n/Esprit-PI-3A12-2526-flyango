<?php
$url = 'https://github.com/dunglas/mercure/releases/download/v0.22.1/mercure_0.22.1_windows_x86_64.zip';
$output = __DIR__ . '/mercure.zip';

echo "Downloading Mercure from GitHub...\n";
$ctx = stream_context_create(['http'=>['timeout'=>120]]);

if (@copy($url, $output, $ctx)) {
    echo "Downloaded to: $output\n";
    echo "Extracting...\n";
    
    $zip = new ZipArchive();
    if ($zip->open($output) === TRUE) {
        $zip->extractTo(__DIR__);
        $zip->close();
        echo "Extracted!\n";
        unlink($output);
        echo "Done!\n";
    }
} else {
    echo "Failed to download. Try manually:\n";
    echo "https://github.com/dunglas/mercure/releases\n";
}