<?php
require_once 'common.php';
require_once 'protect.php';
?>

<?php
$userid = '';
$loyaltyprogram = '';
$membership = '';
$confirm_membership = '';

// membership not filled in
if (empty($_POST['membership'])) {
    $_SESSION['error_membership'] = 'Please enter your membership!';
    header("Location:../registerMembership.php");
    return;
}
// confirm_membership not filled in
elseif (empty($_POST['confirm_membership'])) {
    $_SESSION['error_membership'] = 'Please enter your confirm membership!';
    header("Location:../registerMembership.php");
    return;
}
// all the fields are filled in 
elseif(isset($_SESSION['userid']) && isset($_POST['membership']) && isset($_POST['confirm_membership']) && isset($_POST['loyalty_name']) && isset($_POST['loyalty_id'])) {
    $userid = $_SESSION['userid'];
    $loyaltyprogram = $_POST['loyalty_name'];
    $loyaltyID = $_POST['loyalty_id'];
    if ($_POST['loyalty_name'] == "" && isset($_SESSION['loyaltyprogram'])) {
        $loyaltyprogram = $_SESSION['loyaltyprogram'];
    } else {
        $_SESSION["loyaltyprogram"] = $_POST['loyalty_name'];
    }
    $membership = $_POST['membership'];
    $confirm_membership = $_POST['confirm_membership'];

    // check whether membership match the confirm membership
    if ($membership != $confirm_membership) {
        $_SESSION['error_membership'] = 'The entered membership does not match the entered confirm membership!';
        header("Location:../registerMembership.php");
        return;
    } 
    // update the data in database
    else {
        $url = "https://api.cs301-g7.com/validation/membership";
        $data = array('userID'=>$membership, 'loyaltyProgramID'=>$loyaltyID);
        $data = json_encode($data);
        $response = httpPost($url, $data);
        $response = json_decode($response, true);
        if ($response["status"] == 400) {
            $_SESSION['error_membership'] = $response["message"];
            header("Location:../registerMembership.php");
        } else if ($response["status"] == 201) {
            $membership_dao = new MembershipDAO();
            $status = $membership_dao->update($userid, $loyaltyprogram, $membership);
            if ($status == TRUE) {
                header("Location:../completeMilesTransfer.php?loyalty_name=".$loyaltyprogram);
            } else {
                $_SESSION['error_membership'] = 'Membership could not be successfully added to the database. Please try again!';
                header("Location:../registerMembership.php");
                return;
            }
        }
    }
}
else {
    header("Location:../registerMembership.php");
    return;
}

function httpPost($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $apiKey = 'tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D';
              curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-API-KEY: ' . $apiKey
                ));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>
