<?php

class Membership {
    private $userid;    
    private $loyaltyprogram;
    private $membershipid;

    public function __construct($userid, $loyaltyprogram, $membershipid) {
        $this->userid = $userid;
        $this->loyaltyprogram = $loyaltyprogram;
        $this->membershipid = $membershipid;
    }

    public function getUserID() {
        return $this->userid;
    }

    public function getLoyaltyprogram() {
        return $this->loyaltyprogram;
    }

    public function getMembershipid() {
        return $this->membershipid;
    }

    public function setUserID($userid) {
        $this->userid = $userid;
    }

    public function setLoyaltyprogram($loyaltyprogram) {
        $this->loyaltyprogram = $loyaltyprogram;
    }

    public function setMembershipid($membershipid) {
        $this->membershipid = $membershipid;
    }
}

?>