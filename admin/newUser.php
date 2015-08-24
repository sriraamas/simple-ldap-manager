<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])){
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$fn = $_POST['fn'];
$ln = $_POST['ln'];
$mn = $_POST['mn'];
$uname = $_POST['uname'];
$ph = $_POST['ph'];
$admin_uname = $_POST['aname'];
$admin_pwd = $_POST['apwd'];
$groups = $_POST['groups'];
$phoneType = $_POST['phone'];
$ph = $_POST['ph'];
try {
    $adminObj = new Admin($admin_uname, $admin_pwd);
    if($adminObj->isUnique($uname)){
        $pwd = randomPassword(8);
        $status = $adminObj -> createUser($fn, $ln, $mn, $uname, $pwd, $groups, $phoneType, $ph);
        if ($status === true){
            $response = array('success' => true, 'data' => array("username" => $uname, "password" => $pwd) ,'errors' => array() );
        } else {
            $response = array('success' => false, 'errors' => array($status));
        }
    } else {
        throw new Exception("UserName Already Exists. Please Choose another username");
    }
}
catch (Exception $e) {
        $response = array('success' => false, 'errors' => array($e -> getMessage()) );
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
