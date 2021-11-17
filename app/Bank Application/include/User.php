<?php

class User {
    private $userid;    
    private $password;
    private $miles;
    private $name;

    public function __construct($userid, $password, $miles, $name) {
        $this->userid = $userid;
        $this->password = $password;
        $this->miles = $miles;
        $this->name = $name;
    }

    public function getUserID() {
        return $this->userid;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getMiles() {
        return $this->miles;
    }

    public function getName() {
        return $this->name;
    }

    public function setUserID($userid) {
        $this->userid = $userid;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setMiles($miles) {
        $this->miles = $miles;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function authenticate($enteredPwd) {
        $databasePwd = $this->password;
        if ($enteredPwd === $databasePwd) {
            return true;
        } return false;
    }
}

?>