<?php
require_once "common.php";

class UserDAO {

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

    # get one user by userID
    public function getUserByID($userid) {

        $sql = "SELECT userid, password, miles, name
                    FROM user
                    WHERE userid=:userid";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        
        $user = NULL;
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User (
                $row['userid'],
                $row['password'],
                $row['miles'],
                $row['name']
            );
        }
        $stmt = null;
        $conn = null;
        return $user;
    }
}

?>