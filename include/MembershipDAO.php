<?php
require_once "common.php";

class MembershipDAO {

    private $conn;

    function __construct()
    {
        $this->conn = $this->getConn();
    }

    private function getConn()
    {
        $connManager = new ConnectionManager();
        $conn = $connManager->getConnection();
        return $conn;
    }

    # get one user by userID and loyaltyprogram
    public function getMembershipByIDLoyaltyProgram($userid, $loyaltyprogram) {

        $sql = "SELECT userid, loyaltyprogram, membershipid
                    FROM membership
                    WHERE userid=:userid AND loyaltyprogram=:loyaltyprogram";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':loyaltyprogram', $loyaltyprogram, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        
        $membership = NULL;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $membership = new Membership (
                $row['userid'],
                $row['loyaltyprogram'],
                $row['membershipid']
            );
        }
        $stmt = null;
        $conn = null;
        return $membership;
    }

    // Update one user membershipid
    public function update($userid, $loyaltyprogram, $membershipid) {
        $sql = "UPDATE membership SET membershipid = :membershipid 
                WHERE userid = :userid AND loyaltyprogram=:loyaltyprogram";

        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':loyaltyprogram', $loyaltyprogram, PDO::PARAM_STR);
        $stmt->bindParam(':membershipid', $membershipid,PDO::PARAM_STR);

        $isUpdateOK = FALSE;
        if ($stmt->execute()) {
            $isUpdateOK = TRUE;
        }

        $stmt = null;
        $conn = null;

        return $isUpdateOK;
    }
}

?>