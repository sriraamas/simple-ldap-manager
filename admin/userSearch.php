<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_POST['userQuery']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])) {
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$aname = trim($_POST['aname']);
$apwd = trim($_POST['apwd']);
$adminObj = new Admin($aname, $apwd);
$userQ = $_POST['userQuery'];
try{
    $valuePattern = "[ ]*([a-zA-Z0-9]+[ a-zA-Z0-9]*)[ ]*";
    if(preg_match("/^[ ]*(sn|givenName|sAMAccountName)[ ]*:$valuePattern$/", $userQ,$matches)){
        $field = $matches[1];
        $value = ldap_escape(trim($matches[2]));
        $filter = "($field=$value*)";
    } else if (preg_match("/^$valuePattern$/",$userQ,$matches)){
        $value = ldap_escape(trim($matches[1]));
        $filter = "(|(sAMAccountName=$value*)(givenName=$value*)(sn=$value*))";
    } else {
        throw new Exception("Invalid Search Query '$userQ'");
    }
    $results = $adminObj -> searchUsers($filter);
    $response = array('success' => true, 'data' => $results, 'errors' => array() );
}
catch (EntryNotFoundException $en) {
    $response = array('success' => false, 'errors' => array("Search for '$value' returned No Results"));
}
 catch (Exception $e) {
    $response = array('success' => false, 'errors' => array($e -> getMessage()));
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
