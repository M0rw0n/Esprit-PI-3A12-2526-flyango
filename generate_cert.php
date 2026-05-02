<?php

if (!extension_loaded('openssl')) {
    die("Extension OpenSSL non chargee\n");
}

$config = array(
    "private_key_bits" => 2048,
    "private_key_type" => 0,
);

$privkey = openssl_pkey_new($config);

$dn = array(
    "countryName" => "US",
    "stateOrProvinceName" => "State",
    "localityName" => "City",
    "organizationName" => "Fly&Go",
    "commonName" => "localhost",
);

$csr = openssl_csr_new($dn, $privkey, array('config' => __DIR__ . '/openssl.cnf'));
$san = array(
    "subjectAltName" => "DNS:localhost,IP:127.0.0.1"
);
$x509 = openssl_csr_sign($csr, null, $privkey, 365, array(
    "config" => __DIR__ . '/openssl.cnf',
    "x509_extensions" => "usr_cert"
));

$certsDir = __DIR__ . '/config/certs';
if (!is_dir($certsDir)) {
    mkdir($certsDir, 0777, true);
}

openssl_x509_export($x509, $certOut);
file_put_contents($certsDir . '/server.crt', $certOut);

openssl_pkey_export($privkey, $keyOut);
file_put_contents($certsDir . '/server.key', $keyOut);

echo "Certificats generes!\n";