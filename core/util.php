<?php
function ldap_escape($string) {
    return str_replace(array('*', '\\', '(', ')'), array('\\*', '\\\\', '\\(', '\\)'), $string);
  }

// Returns password in AD-specific format ( surrounded by double-quotes and converted to UTF-16)
function adifyPw($pw)
{
    return mb_convert_encoding('"' . $pw . '"', 'utf-16le');
}

//Returns Unix timestamp given Windows Time
function LDAPtoTS($ldapTs) {
    $ts = new DateTime();
    $secsAfterADepoch = $ldapTs / 10000000;
    $unixTs = intval( $secsAfterADepoch - 11644473600.0);
    $ts -> setTimestamp($unixTs);
    return $ts;
}


abstract class ADUserAccountStatus
{
    const Enabled = 512;
    const Disabled = 514;
}

//Returns Client Certificate, PublicKey and PrivateKey for a user of $commonName, $email and length $keyLength
function generateSslKeypair( $commonName, $mail, $keyLength){
  $key = openssl_pkey_new(array("private_key_bits" =>$keyLength));
  $certConf = parse_ini_file("cert.conf",true);
  $dn = $certConf["dn"];
  $dn["commonName"] = $commonName;
  $dn["emailAddress"] = $mail;
  $cert = openssl_csr_new($dn, $key); // Creating a new X509 Certificate Signing Request
  if(($e = error_get_last())){ // Issues found in parsing the arguments will get a warning. A CSR is created, nonetheless
    throw new Exception("Error occured:". $e["message"]);
  }
  $signed = openssl_csr_sign($cert, null, $key, $certConf["csr"]["validity_in_days"], array(
    "config" =>  "../core/cert.conf" ,
    "config_section_name" => "csr",
    "x509_extensions"  => "clientx509_ext"
  )); // Self-signed X509 certificate with SHA256 digest and extensions specified in local openssl.conf
  if(!$signed) {
    throw new Exception("Error occured while signing certificate");
  }
  openssl_pkey_export($key,$privateKey); // Export private-key to $privateKey
  openssl_x509_export($signed, $clientCert, FALSE); // Export signed-certificate to $clientCert
  openssl_x509_export($signed, $publicKey); // Export public-key from the signed-certificate to $publicKey
  return(array($clientCert,$publicKey,$privateKey));
}

//Returns Random password of $len
function randomPassword($len) {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $len; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

//Returns textual status of the account
function getAccountStatus($num) {
  switch($num) {
    case 512 :
      return "Enabled";

    case 514 :
      return "Disabled";

    case 544 :
      return "Enabled,NOPASSWD";

    case 546 :
      return "Disabled,NOPASSWD";

    case 528 :
      return "Locked";

    default :
      return $num;
  }
}

?>
