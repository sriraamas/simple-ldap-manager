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

function getDefaultConfPath() {
  $val = getenv("LDAP_MGR_CONF");
  if(!$val){
    $val = "/etc/simple-ldap-manager";
  }
  return $val;
}

function getConfigFile() {
  $default = getDefaultConfPath();
  if(file_exists($default."/config-overrides.ini")){
    return $default."/config-overrides.ini";
  } else {
    return $_SERVER["DOCUMENT_ROOT"]."/conf/config.ini";
  }
}

function getConfig($configName) {
    $config = parse_ini_file(getConfigFile());
    if(!$config)
            throw new Exception("No Config file found");
    return $config[$configName];
}

abstract class ADUserAccountStatus
{
    const Enabled = 512;
    const Disabled = 514;
}

//Returns Client Certificate and PrivateKey for a user of $commonName, $email and length $keyLength
function generateSslKeypair( $commonName, $keyLength){
  $key = openssl_pkey_new(array("private_key_bits" =>$keyLength));
  $certConf = parse_ini_file("cert.conf",true);
  $default = getDefaultConfPath();
  if(file_exists($default."/cert-overrides.ini")){
    $confFile = $default."/cert-overrides.ini";
  } else {
    $confFile = $_SERVER["DOCUMENT_ROOT"]."/conf/cert.ini";
  }
  $certConf = parse_ini_file($confFile,true);
  $dn = $certConf["dn"];
  $dn["commonName"] = $commonName;

  $cert = openssl_csr_new($dn, $key); // Creating a new X509 Certificate Signing Request
  if(($e = error_get_last())){ // Issues found in parsing the arguments will get a warning. A CSR is created, nonetheless
    throw new Exception("Error occured:". $e["message"]);
  }
  $signed = openssl_csr_sign($cert, null, $key, $certConf["csr"]["validity_in_days"], array(
    "config" =>  $confFile ,
    "config_section_name" => "csr",
    "x509_extensions"  => "clientx509_ext"
  )); // Self-signed X509 certificate with SHA256 digest and extensions specified in local openssl.conf
  if(!$signed) {
    throw new Exception("Error occured while signing certificate");
  }
  openssl_pkey_export($key,$privateKey); // Export private-key to $privateKey
  openssl_x509_export($signed, $clientCert); // Export signed-certificate to $clientCert without Extra Details
  return(array($clientCert,$privateKey));
}

//Returns Random password of $len with $complexity(default : 3)
// complexity 1 : just lowercase letters
// complexity 2 : atleast one lowercase + atleast one uppercase
// complexity 3 : atleast one lowercase + atleast one uppercase + atleast one number
// complexity 4 : atleast one lowercase + atleast one uppercase + atleast one number + atleast one special char
function randomPassword($len,$complexity=3) {
    $lowerCase = "abcdefghijklmnopqrstuvwxyz";
    $allChars = $lowerCase;
    $pass = array(); //remember to declare $pass as an array
    $origComplexity = $complexity;
    if($complexity > 0){
      $n = rand(0, 25);
      $pass[] = $lowerCase[$n];
      $complexity -= 1;
    }
    if($complexity > 0){
      $upperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $n = rand(0, 25);
      $pass[] = $upperCase[$n];
      $complexity -= 1;
      $allChars .= $upperCase;
    }
    if ($complexity > 0){
      $numbers = "0123456789";
      $n = rand(0, 9);
      $pass[] = $numbers[$n];
      $complexity -= 1;
      $allChars .= $numbers;
    }
    if ($complexity > 0){
      $specialChars = getConfig("specialChars");
      $n = rand(0, strlen($specialChars) - 1);
      $pass[] = $specialChars[$n];
      $complexity -= 1;
    }
    $allCharsLength = strlen($allChars) - 1;
    for ($i = 0; $i < $len-$origComplexity; $i++) {
        $n = rand(0, $allCharsLength);
        $pass[] = $allChars[$n];
    }
    return implode($pass); //turn the array into a string
}
  //Zips all the files and folders in $folder, using relative path. NOT TESTED WITH LINKS
  function zipFolder($zipArchive, $folder, $basePath=null){
    if(!is_dir($folder)){
        return(-1);
    }
    $entries = array_diff(scandir($folder), array('..', '.'));
    foreach ($entries as $f){
        $absFilePath = "$folder/$f";
        if($basePath){
            $newBasePath = $basePath."/".$f;
        } else {
            $newBasePath = $f;
        }
        if(is_dir($absFilePath)){
            zipFolder($zipArchive, $absFilePath, $newBasePath);
        } else {
            $zipArchive -> addFile($absFilePath, $newBasePath);
        }
    }
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
