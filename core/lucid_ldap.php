<?php
class Lucid_LDAP {
    public $domain;
    public $basedn;
    public $conn;
    public $logger;
    public $mailDomain;

    public function __construct($filename) {
        $config = parse_ini_file($filename);
        if(!$config)
            throw new Exception("No Config file found");
        $this -> domain = $config["domain"];
        $this -> basedn = $config["basedn"];
        $this -> userBaseDn = $config["userDn"];
        $this -> userdn = ldap_escape("$this->userBaseDn,$this->basedn");
        $this -> groupdn = ldap_escape("ou=groups,$this->basedn");
        $this -> conn = FALSE;
        $this -> logger = new Logger($config["logPath"]);
        $this -> url = $config['hostname'].":".$config['port'];
        $this -> VPN = $config['vpn'];
        $this -> SSH = $config['ssh'];
        $this -> tmpPath = $config['tmpPath'];
    }

    public function bind($username, $password){
        $this -> conn = ldap_connect($this -> url);
        $rdn = "$username@$this->domain";
        $status = ldap_bind($this -> conn, $rdn, $password);
        if(!$status){
            $this -> logger -> log("Lucid_LDAP::error::$username login failed.");
            throw new InvalidCredentialsException(ldap_error($this->conn));
        }
        $this -> logger -> log("Lucid_LDAP::info::$username login successful.");
        return $status;
    }
    // Returns a user entry
    public function searchUser($username,$attr){
        if(!$this -> conn){
            throw new ADNotBoundException();
        }
        $eUserName = ldap_escape($username);
        $entries = ldap_search($this -> conn, $this->userdn,"(sAMAccountName=$eUserName)", $attr);
        $count = ldap_count_entries($this -> conn, $entries);
        $this -> logger -> log("Lucid_LDAP::info::LDAP Search Complete for '$eUserName', found $count entries");
        if($count > 0){
            $entry = ldap_first_entry($this -> conn, $entries);
            $dn = ldap_get_dn($this -> conn,$entry);
            return(array($entry,$dn));
        } else {
            throw new EntryNotFoundException($username);
        }
    }

    public function searchUserWithFilter($filter){
        if(!$this -> conn){
            throw new ADNotBoundException();
        }
        $results = array();
        $entries = ldap_search($this -> conn, $this->userdn, $filter,array("givenName","middleName","sn", "sAMAccountName","sn","memberOf","mobile","mail","homePhone","userAccountControl"));
        $count = ldap_count_entries($this -> conn, $entries);
        $this -> logger -> log("Lucid_LDAP::info::LDAP Search Complete for filter $filter, found $count entries");
        if($count > 0){
            $i=0;
            $entry = ldap_first_entry($this -> conn, $entries);
            if($entry){
                do {
                    $i++;
                    array_push($results, array( 
                        "givenName" => $this->getFirstValue($entry,"givenName"),
                        "middleName" => $this->getFirstValue($entry,"middleName"),
                        "sn" => $this->getFirstValue($entry,"sn"),
                        "sAMAccountName" => $this->getFirstValue($entry, "sAMAccountName"),
                        "memberOf" => $this->getAllGroupNames($entry,"memberOf"),
                        "mobile" => $this->getFirstValue($entry,"mobile"),
                        "homePhone" => $this->getFirstValue($entry,"homePhone"),
                        "mail" => $this->getFirstValue($entry, "mail"),
                        "userAccountControl" => getAccountStatus($this->getFirstValue($entry, "userAccountControl"))
                    ));
                } while(($entry = ldap_next_entry($this-> conn, $entry)));
            }
        } else {
            throw new EntryNotFoundException($filter);
        }
        return($results);
    }

    public function getAttributes($username, $attrs){
        list($entry, $dn) = $this -> searchUser($username, $attrs);
        $result = array();
        for ($i=0; $i<count($attrs); $i++){
            $values = $this -> getAllValues($entry, $attrs[$i]);
            if(empty($values)) {
                $result[$attrs[$i]] = array();
            } else {
                $result[$attrs[$i]] = $values;
            }
        }
        return $result;
    }

    // bind happens in external function
    public function updateAttribute($dn, $attrib, $value){
        $arr = array();
        $arr[$attrib] = $value;
        $status = ldap_mod_replace($this->conn, $dn, $arr);
        if (!$status){
            $status = ldap_error($this->conn);
        }
        return $status;
    }

    // bind happens in external function
    public function delAttribute($dn,$attrib,$value=array()){
        $arr = array();
        $arr[$attrib] = $value;
        $status = ldap_mod_del($this->conn, $dn, $arr);
        if (!$status){
            $status = ldap_error($this->conn);
        }
        return $status;
    }

    // bind happens in external function
    public function addEntry($dn,$entry){
        $status = ldap_add($this->conn, $dn, $entry);
        if (!$status){
            throw new Exception(ldap_error($this->conn));
        }
        return $status;
    }

    public function searchGroups($filter,$groupDn=NULL){
        $groups = array();
        if(!$groupDn)
            $groupDn = $this->groupdn;
        $results = ldap_search($this->conn, $groupDn , $filter, array("sAMAccountName", "cn", "member"));
        $count = ldap_count_entries($this->conn, $results);
        if($count == 0) {
            throw new EntryNotFoundException($filter);
        }
        $entry = ldap_first_entry($this->conn, $results);
        do {
            $dn = ldap_get_dn($this->conn, $entry);
            $groupName = $this->getFirstValue( $entry, "sAMAccountName");
            array_push($groups, array(
                "sAMAccountName" => $groupName,
                "cn" => $this->getFirstValue($entry, "cn"),
                "dn" => $dn
            ));
        } while($entry  = ldap_next_entry($this->conn, $entry));
        return $groups;
    }
    public function getUsersinGroup($groupDn){
        $users = array();
        $results = ldap_search($this->conn, $this->userdn, "(memberof:1.2.840.113556.1.4.1941:=$groupDn)",array("cn"));
        $count = ldap_count_entries($this->conn,$results);
        if ($count > 0){
            $entry = ldap_first_entry($this->conn,$results);
            do {
                array_push($users, $this->getFirstValue($entry,"cn"));
            }
            while(($entry=ldap_next_entry($this->conn,$entry)));
        }
        return $users;
    }

    private function getFirstValue($entry, $attrib){
        $values = @ldap_get_values($this->conn, $entry,$attrib);
        if(empty($values) || ($values["count"] === 0)){
            return NULL;
        } else {
            return $values[0];
        }
    }

    public function getAllValues($entry,$attrib){
        $results = array();
        $values = @ldap_get_values($this->conn, $entry,$attrib);
        if(!empty($values)){
            for($i=0;$i<$values["count"];$i++){
                array_push($results, $values[$i]);
            }
            natsort($results);
        }
        return array_values($results);
    }

    private function getAllGroupNames($entry,$attrib){
        $results = array();
        $values = @ldap_get_values($this->conn, $entry,$attrib);
        if(!empty($values)){
            for($i=0;$i<$values["count"];$i++){
                $ar = ldap_explode_dn($values[$i],1);
                array_push($results,$ar[0]);
            }
        }
        natsort($results);
        return $results;
    }

    function addAttribute($dn,$attrib, $value){
        $arr = array();
        $arr[$attrib] = $value;
        $status = ldap_mod_add($this->conn, $dn, $arr);
        if (!$status){
            $status = ldap_error($this->conn);
        }
        return $status;
    }

    function __destruct() {
        if($this->conn)
            return ldap_unbind($this-> conn);
    }

    function destroy(){
        ldap_unbind($this->conn);
        $this -> conn = FALSE;
    }

}

class EntryNotFoundException extends Exception {
    public function __construct($uname, $code = 0, Exception $previous = null) {
        parent::__construct("User: $uname Not Found in Active Directory", $code, $previous);
    }

}

class InvalidCredentialsException extends Exception {
    public function __construct($msg = "Invalid Credentials", $code = 0, Exception $previous = null) {
        parent::__construct($msg, $code, $previous);
    }
}

class ADNotBoundException extends Exception {
    public function __construct($msg = "Not Bound to Active Directory", $code = 0, Exception $previous = null) {
        parent::__construct($msg, $code, $previous);
    }
}

?>