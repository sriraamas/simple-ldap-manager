<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken']) ){
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$admin_uname = trim($_POST['aname']);
$admin_pwd = trim($_POST['apwd']);
try {
    $adminObj = new Admin($admin_uname, $admin_pwd);
    $result = $adminObj -> getAllBaseGroups();
    if (!$result){
        $response = array('success' => false, 'errors' => array($result));
    } else {
        $response = array('success' => true, 'data' => $result, 'errors' => array() );
    }
} catch (Exception $e) {
    $response = array('success' => false, 'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
 