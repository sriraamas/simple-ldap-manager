<?php
if(!isset($_POST['aname']) || !isset($_POST['apwd']) || !isset($_POST['groupQuery']) || !isset($_COOKIE["xsrftoken"]) || !isset($_POST["xsrftoken"]) || ($_COOKIE["xsrftoken"] !== $_POST['xsrftoken'])) {
    header('Location:/admin-ss.php');
    die();
}
require_once('../core/admin.php');
$aname = trim($_POST['aname']);
$apwd = trim($_POST['apwd']);
$adminObj = new Admin($aname, $apwd);
$groupQuery = $_POST['groupQuery'];
try{
    $valuePattern = "[ ]*([a-zA-Z0-9]+[ a-zA-Z0-9]*)[ ]*";
    if(preg_match("/^$valuePattern$/",$groupQuery,$matches)){
        $value = ldap_escape(trim($matches[1]));
        $filter = "(|(sAMAccountName=$value*)(sn=$value*)(givenName=$value*))";
    } else {
        throw new Exception("Invalid Search Query '$groupQuery'");
    }
    $results = $adminObj -> searchGroups($filter);
    $response = array('success' => true, 'data' => $results, 'errors' => array() );
    
}
catch (EntryNotFoundException $en){
    $response = array('success' => false, 'errors' => array("Search for '$value' returned No Results"));
}
 catch (Exception $e){
    $response = array('success' => false, 'errors' => array($e -> getMessage()));
}
header('Content-Type: application/json',TRUE);
$jsonstring = json_encode($response);
echo $jsonstring;
