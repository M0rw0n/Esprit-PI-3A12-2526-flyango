<?php
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$phpWord = new PhpWord();
$section = $phpWord->addSection();
$section->addText('Test Word Document');

$filename = sys_get_temp_dir() . '/test.docx';
$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($filename);

if (file_exists($filename)) {
    echo "Word file created: " . $filename;
} else {
    echo "Failed to create Word file";
}