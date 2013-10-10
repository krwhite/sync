<?php
$plaintext = "Please encrypt me!";
$key ='SomeFixedStringX';
$iv = 'randomstringxxxx'; # crypt_random_string(16);

include('Crypt/AES.php');

$rijndael = new Crypt_AES();
$rijndael->setIV($iv);
$rijndael->setKey($key);
$rijndael->enablePadding();

$crypted_by_mcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
$crypted_by_phpseclib = $rijndael->encrypt($plaintext);

echo $crypted_by_phpseclib;
exit;
echo base64_encode($crypted_by_mcrypt);
exit;

print mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypted_by_mcrypt, MCRYPT_MODE_CBC, $iv)."\n"; # Recovers $plaintext
print mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypted_by_phpseclib, MCRYPT_MODE_CBC, $iv)."\n"; # Recovers $plaintext

print $rijndael->decrypt($crypted_by_mcrypt)."\n"; # Fails to recover $plaintext
print $rijndael->decrypt($crypted_by_phpseclib)."\n"; # Recovers $plaintext
