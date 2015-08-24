<?php
if(!isset($_POST['uname']) || !isset($_POST['pwd']) || !isset($_POST['newPwd']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken']) ) {
    header('Location:/user-chpass-ss.php');
    die();
}
require_once('../core/user.php');
$username = trim($_POST['uname']);
$oldPassword = trim($_POST['pwd']);
$newPassword = trim($_POST['newPwd']);
$userObj = new User($username, $oldPassword);
try{
    list($result,$err) = $userObj -> changePwd($newPassword);
    if($result == 0){
        $response = array('success' => true, 'errors' => array() );
    } else {
        $response = array('success' => false, 'errors' => array(ldap_err2str($result)) );
    }
}
catch (Exception $e){
    $response = array('success' => false,  'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
