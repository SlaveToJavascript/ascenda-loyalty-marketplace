<?php
require_once 'common.php';
require_once 'protect.php';
?>

<?php
$userid = '';
$loyaltyprogram = '';

if(isset($_SESSION['userid']) && isset($_POST['btnClickedValue'])) {
    $userid = $_SESSION['userid'];
    $loyaltyprogram = $_POST['btnClickedValue'];
    $membership_dao = new MembershipDAO();
    $membership = $membership_dao->getMembershipByIDLoyaltyProgram($userid, $loyaltyprogram);
    $membershipid = $membership->getMembershipid();
    if ($membershipid == NULL) {
        header("Location:../registerMembership.php?loyalty_name=".$loyaltyprogram);
    } else {
        header("Location:../completeMilesTransfer.php?loyalty_name=".$loyaltyprogram);
    }
} else {
    header("Location:../transfer.php");
}
?>

