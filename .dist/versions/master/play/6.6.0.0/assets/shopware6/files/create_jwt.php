<?php

$privateKeyPath = '/var/www/html/config/jwt/private.pem';
$publicKeyPath = '/var/www/html/config/jwt/public.pem';

$passphrase = 'shopware';

$key = openssl_pkey_new([
    'digest_alg' => 'aes256',
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'encrypt_key' => $passphrase,
    'encrypt_key_cipher' => OPENSSL_CIPHER_AES_256_CBC
]);

// export private key
openssl_pkey_export_to_file($key, $privateKeyPath, $passphrase);
chmod($privateKeyPath, 0660);

if ($publicKeyPath) {
    // export public key
    $keyData = openssl_pkey_get_details($key);
    file_put_contents($publicKeyPath, $keyData['key']);
    chmod($publicKeyPath, 0660);
}
