<?php
require_once 'common.php';
require_once 'protect.php';
?>

<?php
$userid = '';
$password = '';

if(isset($_SESSION['userid']) && isset($_SESSION['password'])) {
    $userid = $_SESSION['userid'];
    $user_dao = new UserDAO();
    $user = $user_dao->getUserByID($userid);
    $name = $user->getName();
    $miles = $user->getMiles();
}
?>

