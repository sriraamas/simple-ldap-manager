<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_POST['uname']) || !isset($_COOKIE["xsrftoken"])){
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$admin_uname = trim($_POST['aname']);
$admin_pwd = trim($_POST['apwd']);
$uname = trim($_POST['uname']);
try {
    $adminObj = new Admin($admin_uname, $admin_pwd);
    $results = $adminObj -> getUsersinGroup($uname);
    $response = array('success' => true,'data' => $results, 'errors' => array() );
} catch (Exception $e){
    $response = array('success' => false, 'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
