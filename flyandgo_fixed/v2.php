<?php
// Try without fromNumber
$hash = 'ffa45c6e-62ed-455e-9d4d-a37a12a7720c';
$token = '9cfd779a-aa29-4530-b39d-a13a6499ea24';
$phone = '21624306909';

echo "Test 1: With fromNumber=SMS\n";
$ch = curl_init("https://api.smspm.com?hash=$hash&toNumber=$phone&text=Test1&fromNumber=SMS&token=$token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch) . "\n\n";
curl_close($ch);

echo "Test 2: With fromNumber=FlyAndGo\n";
$ch = curl_init("https://api.smspm.com?hash=$hash&toNumber=$phone&text=Test2&fromNumber=FlyAndGo&token=$token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch) . "\n\n";
curl_close($ch);

echo "Test 3: Without fromNumber\n";
$ch = curl_init("https://api.smspm.com?hash=$hash&toNumber=$phone&text=Test3&token=$token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch) . "\n\n";
curl_close($ch);