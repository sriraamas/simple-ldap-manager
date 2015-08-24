<?php
require_once("user.php");
class Admin extends User {
    //Resets password of user given by $dn to $newPwd
    public function resetPwd($dn, $newPwd){
        $unicodePwd = adifyPw($newPwd); 
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $status = $ldapObj -> updateAttribute($dn, "unicodePwd", $unicodePwd); 
        $ldapObj -> destroy();
        if($status === true){
            $this->loggerObj -> log("ADMIN::info::$this->username has Reset Password for $dn successfully");
        } else {
            $this->loggerObj -> log("ADMIN::error::{$this->username}'s Attempt to Reset Password for $dn has failed. Reason: $status");
        }
        return $status;
    }


    //Returns an array containing 'name', 'mail' and 'dn' of User
    public function getUserInfo($uname,$attr=array()){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $result = $ldapObj -> getAttributes($uname,array_merge($attr, array("name", "mail","distinguishedName")));
        $ldapObj -> destroy();
        $final =  array( 
            "name" => $result["name"][0],
            "mail" =>  $result["mail"][0],
            "dn" => $result["distinguishedName"][0]
        );
        foreach ($attr as $val){
            if ( isset($result[$val]) )
                $final[$val] = $result[$val][0];
                if ($val == "userAccountControl"){
                    $final[$val] = getAccountStatus($result[$val][0]);
                }
            else 
                $final[$val] = "";
        }
        return($final);
    }

    //Returns the status of deleting attribute $attrib for user given by $dn
    public function revoke($dn, $attrib){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $status  = $ldapObj -> delAttribute($dn, $attrib);
        $ldapObj -> destroy();
        if($status === true){
            $this->loggerObj -> log("ADMIN::info::$this->username has removed $attrib for $dn successfully");
        } else {
            $this->loggerObj -> log("ADMIN::error::{$this->username}'s Attempt to remove $attrib for $dn has failed. Reason: $status");
        }
        return $status;
    }

    //Returns the status of creating the user with given attributes
    public function createUser($fn, $ln, $mn, $uname, $pwd, $groups, $phType, $ph){
        $ldapObj = new Lucid_LDAP($this->configFile);
        // Use middle name in commonName 
        if(!empty($mn)){
            $cn = "$fn $mn $ln";
        } else {
            $cn = "$fn $ln";
        }
        $newEntry = array(
            'givenName'  => $fn,
            'sn'         => $ln,
            'cn'         => $cn,
            'name'       => $cn,
            'displayName'=> $cn,
            'objectClass'=> array( "top", "person", "organizationalPerson", "user"),
            'objectCategory' => "CN=Person,CN=Schema,CN=Configuration,".$ldapObj -> basedn,
            'sAMAccountName' => $uname,
            'mail'           => "$uname@".$ldapObj -> mailDomain,
            'userAccountControl' => 512,
            'unicodePwd' => adifyPw($pwd)
        );
        if(!empty($mn)){
            $newEntry['middleName'] = $mn;
        }
        if($phType == "home"){
            $newEntry['homePhone'] = $ph;
        } else if($phType == "mobile"){
            $newEntry['mobile'] = $ph;
        }
        // The DN for the new user
        $dn = ldap_escape("CN=".$newEntry['cn'].",OU=Users,OU=People,".$ldapObj->basedn);
        $ldapObj -> bind($this->username, $this->password);
        $status = $ldapObj -> addEntry($dn,$newEntry);
        if(!empty($groups)){
            $this -> addUserToGroups($ldapObj,$dn, $groups);
        }
        $this->loggerObj -> log("ADMIN::info::$this->username has successfully created User $uname successfully");
        $ldapObj -> destroy();
        return $status;
    }

    // Adds user given by $dn to groups $groups
    private function addUserToGroups($ldapObj, $dn, $groups){
        for($i=0;$i<count($groups);$i++){
            $status= $ldapObj -> addAttribute($groups[$i],"member",$dn);
        }
        return $status;
    }

    public function getAllGroups(){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $result = $ldapObj -> searchGroups("(cn=*)");
        $ldapObj -> destroy();
        return $result;
    }

    public function searchGroups($filter,$withUsers=false){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $result = $ldapObj -> searchGroups($filter,$withUsers);
        $ldapObj -> destroy();
        return $result;
    }

    public function isUnique($uname){
        try{
            $status = false;
            $ldapObj = new Lucid_LDAP($this->configFile);
            $ldapObj -> bind($this->username, $this->password);
            $result = $ldapObj -> searchUser($uname,array("sAMAccountName"));
        } catch (EntryNotFoundException $e){
            $status = true;
        } finally {
            $ldapObj -> destroy();
            return $status;
        }
    }

    public function isValid($uname){
        return preg_match("/^([a-zA-Z]+[a-zA-Z0-9]*)$/",trim($uname));
    }

    public function getUsersInGroup($uname){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username,$this->password);
        $results = $ldapObj -> searchGroups("(sAMAccountName=$uname)");
        $users = $ldapObj -> getUsersInGroup($results[0]["dn"]);
        $ldapObj -> destroy();
        return $users;
    }

    public function searchUsers($filter){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $results = $ldapObj -> searchUserWithFilter($filter);
        $ldapObj -> destroy();
        return $results;
    }

    public function updateAttr($dn, $attrib, $value){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $status = $ldapObj -> updateAttribute($dn, $attrib, $value);
        $ldapObj -> destroy();
         if($status === true){
            $this->loggerObj -> log("ADMIN::info::$this->username has updated $attrib for $dn successfully");
        } else {
            $this->loggerObj -> log("ADMIN::error::{$this->username}'s Attempt to update $attrib for $dn has failed. Reason: $status");
        }
        return $status;
    }
}
?>