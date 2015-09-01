<?php
class Logger{
    public $filename = "ldapss.log";
    public function __construct($filePath) {
        $this -> filename = "$filePath/ldapss.log";
    }
    public function log($str) {
        $time = date('Y-m-d h:i:s A');
        file_put_contents( $this->filename, $time.":".$str."\n", FILE_APPEND | LOCK_EX);
    }

}
?>