<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_POST['uname']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])){
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$admin_uname = trim($_POST['aname']);
$admin_pwd = trim($_POST['apwd']);
$username = trim($_POST['uname']);
$adminObj = new Admin($admin_uname, $admin_pwd);
try{  
    if(isset($_POST['dn']) && isset($_POST["userAction"])) {
        switch ($_POST["userAction"]){
             case "revokeVPN":
                            $result = $adminObj -> revoke($_POST['dn'],"lucidVpnCert");
                            break;
             case "revokeSSH": 
                            $result = $adminObj -> revoke($_POST['dn'],"lucidSshKey");
                            break;
             case "disable":
                            $result = $adminObj -> updateAttr($_POST['dn'],"userAccountControl",514);
                            break;
             case "enable":
                            $result = $adminObj -> updateAttr($_POST['dn'],"userAccountControl",512);
                            break;
             default:
                            throw new Exception("Invalid Action");
        }
        if($result === true) {
            $response = array('success' => true, 'errors' => array() );
        } else {
            $response = array('success' => false, 'errors' => array($result) );
        }
    } else {
        // Authorize Users, Check if credentials are currently set.
        if($username === $admin_uname){
            throw new Exception("Error: You Cannot Manage Yourself!");
        }
        $info = $adminObj -> getUserInfo($username,array("userAccountControl"));
        $response = array('success' => true, 'data' => $info, 'errors' => array() );
    }
}
catch (Exception $e){
    $response = array('success' => false, 'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
