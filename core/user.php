<?php
require_once("lucid_ldap.php");
require_once("util.php");
require_once("logger.php");
class User {
    public $username;
    public $password;
    public $loggerObj;
    public $configFile;

    public function __construct($username, $password){
        $this -> username = $username;
        $this -> password = $password;
        $this -> loggerObj = new Logger(getConfig("logPath"));
        $this -> configFile = getConfigFile();
        $this -> vpnFolder = getConfig("vpnFolderPath");
        $this -> vpn2Folder = getConfig("vpn2FolderPath");
    }

    public function isAuthorized(){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $status = $ldapObj->bind($this->username, $this->password);
        $ldapObj -> destroy();
        return($status);
    }

    private function getMyDN($ldapObj){
        list ($entry, $dn) = $ldapObj -> searchUser($this->username,array());
        return $dn;
    }

    public function changePwd($newPwd){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        list ($entry, $dn) = $ldapObj -> searchUser($this -> username,array("sAMAccountName"));
        $ldapObj -> destroy();
        $this -> loggerObj -> log( "Changing password for $this->username");
        $oldPwdEnc = base64_encode(adifyPw($this->password));
        $newPwdEnc = base64_encode(adifyPw($newPwd));
        $cmdPwd = "'$this->password'";
        $cmd = "ldapmodify -H $ldapObj->url -D '$dn' -x -w $cmdPwd";
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $child = proc_open(escapeshellcmd($cmd),$descriptorspec,$pipes);
        $ldif_file = array("dn: $dn",
            "changetype: modify",
            "delete: unicodePwd",
            "unicodePwd:: $oldPwdEnc",
            "-",
            "add: unicodePwd",
            "unicodePwd:: $newPwdEnc",
            "-");
        fwrite($pipes[0], implode("\n", $ldif_file)."\n");
        fclose($pipes[0]);
        $output1 = stream_get_contents($pipes[1]);
        $output2 = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($child);
        $this -> loggerObj -> log("LDAPModify exited with status: $status");
        $this -> loggerObj -> log("LDAPModify Output: $output1\n $output2");
        return(array($status,$output2));
    }

    public function genMyVpnKeys(){
        return $this->genUserVpnKeys("VPN", $this->vpnFolder);
    }

    public function genMySecondaryVpnKeys(){
        return $this->genUserVpnKeys("VPN-Secondary", $this->vpn2Folder);
    }

    private function genUserVpnKeys($vpnProperty, $vpnFolder){
        $tmpPath = getConfig("tmpPath");
        $result = $this->getMyAttributes(array("cn","sAMAccountName"));
        $this -> loggerObj -> log( "Regenerating VPN Credentials for $this->username");
        $zip = new ZipArchive();
        $zipFilename = "$tmpPath/$this->username.$vpnProperty.credentials.zip";

        $status = $zip -> open("$zipFilename", ZipArchive::OVERWRITE);
        if ($status !== TRUE){
            throw new Exception("Cannot create Zip File");
        }

        $commonName = $result["sAMAccountName"][0];

        list ($cert,$priv) = generateSslKeypair($commonName, intval(getConfig("vpnKeyLength")));

        // If vpn targets are configured, put the client key and cert in each target
        $vpnTargets = getConfig("vpnTargets");
        if ($vpnTargets == NULL) {
            $zip -> addFromString("client.key", $priv);
            $zip -> addFromString("client.crt", $cert);
        } else {
            foreach ($vpnTargets as $target) {
                $zip -> addFromString("$target/client.key", $priv);
                $zip -> addFromString("$target/client.crt", $cert);
            }
        }

        // Try to add the source vpn files
        if(file_exists($vpnFolder)){
            zipFolder($zip,$vpnFolder);
        }
        else {
            throw new Exception("VPN Container is Not Found. Kindly contact the server administrator!");
        }

        // Compress and save the files
        $status = $zip -> close();
        if ($status !== TRUE){
            throw new Exception("Cannot create Zip File");
        }
        $updateStatus = $this -> updateMyProperty($vpnProperty, $cert);
        if($updateStatus !== TRUE){
            unlink($zipFilename);
            throw new Exception("Error occured during LDAP Update: $updateStatus. Please try again Later!");
        }
        $this -> loggerObj -> log( "VPN Credentials for $this->username have been reset successfully");
        return($zipFilename);
    }

    public function getMyAttributes($attrs){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $result = $ldapObj -> getAttributes($this->username, $attrs);
        $ldapObj -> destroy();
        return($result);
    }

    public function updateMyProperty($prop, $value){
        $ldapObj = new Lucid_LDAP($this->configFile);
        $ldapObj -> bind($this->username, $this->password);
        $myDn = $this->getMyDN($ldapObj);
        if($prop === "SSH"){
            $attr = $ldapObj -> SSH;
        }
        if($prop === "VPN"){
            $attr = $ldapObj -> VPN;
        }
        if($prop === "VPN-Secondary"){
            $attr = $ldapObj -> VPN2;
        }
        if (!isset($attr)){
            throw new Exception("Unknown Property $prop");
        }
        $update_status = $ldapObj -> updateAttribute($myDn, $attr, $value);
        $ldapObj -> destroy();
        return($update_status);
    }

    public function genMySshKeys($passphrase){
        $tmpPath = getConfig("tmpPath");
        $this -> isAuthorized();
        $this -> loggerObj -> log( "Regenerating SSH Credentials for $this->username");
        if(!!file_exists("$tmpPath/$this->username.pem")){
            unlink("$tmpPath/$this->username.pem.pub");
            unlink("$tmpPath/$this->username.pem");
        }
        $keyLength = getConfig("sshKeyLength");
        $cmd = "ssh-keygen -q -b $keyLength -t rsa -N $passphrase -C '$this->username lucid account' -f $tmpPath/$this->username.pem";
        system(escapeshellcmd($cmd), $cmd_status);
        if(!!$cmd_status){
            throw new Exception("SSH Key Generation Failed:$cmd_status");
        }
        $zip = new ZipArchive();
        $zipFilename = "$tmpPath/$this->username.ssh.credentials.zip";
        $zipStatus = $zip -> open($zipFilename,ZipArchive::OVERWRITE);
        if ($zipStatus!== TRUE){
            throw new Exception("Cannot create Zip File");
        }
        $zip -> addFile("$tmpPath/$this->username.pem.pub","$this->username.pem.pub");
        $zip -> addFile("$tmpPath/$this->username.pem","$this->username.pem");
        $closeStatus = $zip->close();
        if(!$closeStatus){
            throw new Exception("Error Creating a zip archive. Try again Later");
        }
        $pubKey = file_get_contents("$tmpPath/$this->username.pem.pub");
        unlink("$tmpPath/$this->username.pem.pub");
        unlink("$tmpPath/$this->username.pem");
        $updateStatus = $this -> updateMyProperty("SSH", trim($pubKey));
        if(!$updateStatus){
            unlink($zipFilename);
            throw new Exception("Error occured during LDAP Update. Please try again Later!");
        }
        $this -> loggerObj -> log( "SSH Credentials for $this->username have been reset succesfully");
        return($zipFilename);
    }

}
?>
