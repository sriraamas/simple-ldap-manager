<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_POST['uname']) || !isset($_COOKIE["xsrftoken"])){
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$admin_uname = trim($_POST['aname']);
$admin_pwd = trim($_POST['apwd']);
$username = trim($_POST['uname']);
$adminObj = new Admin($admin_uname, $admin_pwd);
$property = $_POST['property'];
if($property == "SSH"){
    $attrib = "lucidSshKey";
} 
if($property == "VPN"){
    $attrib = "lucidVpnCert";
}
try{  
    if(isset($_POST['dn'])) {
        // Perform actual Revoke
        $result = $adminObj -> revoke($_POST['dn'], $attrib);
        if($result === true){
            $response = array('success' => true, 'errors' => array() );
        } else {
            $response = array('success' => false, 'errors' => array($result) );
        }
    } else {
        // Authorize Users, Check if credentials are currently set.
        if($username === $admin_uname){
            throw new Exception("Error: Cannot Revoke Your $property Credentials by Yourself!");
        }
        $info = $adminObj -> getUserInfo($username,array($attrib));
        $response = array('success' => true, 'data' => $info, 'errors' => array() );
    }
}
catch (Exception $e){
    $response = array('success' => false, 'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
