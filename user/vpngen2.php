<?php
if(!isset($_POST['uname']) || !isset($_POST['pwd']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])) {
    header('Location:/user-vpn-ss.php');
    die();
}
require_once('../core/user.php');
$username = trim($_POST['uname']);
$password = trim($_POST['pwd']);
$user = new User($username, $password);
try{
    $filename = $user -> genMySecondaryVpnKeys();
    $contents = file_get_contents($filename);
    unlink($filename);
    $pathInfo = pathinfo($filename);
    $response = array(
        'success' => true,
        'data' => array(
            'filename' => $pathInfo["basename"],
            'contents' => base64_encode($contents)
        ),
        'errors' => array());
}
catch (Exception $e){
    $response = array('success' => false,'errors' => array($e -> getMessage()));
}
header('Content-Type: application/json',TRUE);
echo(json_encode($response));
