<?php
if(!isset($_POST['uname']) || !isset($_POST['pwd']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])) {
    header('Location:/user-ssh-ss.php');
    die();
}
require_once('../core/user.php');
$username = trim($_POST['uname']);
$password = trim($_POST['pwd']);
$passphrase = trim($_POST['passphrase']);
$user = new User($username, $password);
try{
    $filename = $user -> genMySshKeys($passphrase);
    $contents = file_get_contents($filename);
    unlink($filename);
    $response = array(
        'success' => true,
        'data' => array(
            'filename' => $filename,
            'contents' => base64_encode($contents)
        ),
        'errors' => array());
}
catch (Exception $e){
    $response = array('success' => false,'errors' => array($e -> getMessage()));
}
header('Content-Type: application/json',TRUE);
echo(json_encode($response));
