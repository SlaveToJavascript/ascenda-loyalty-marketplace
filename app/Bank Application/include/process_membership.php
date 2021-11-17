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
    $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0 Chrome/89.0.4389.114';
    curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $headerArray = array(
        'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-GB,en;q=0.8,en-US;q=0.6,it-IT;q=0.4,it;q=0.2',
        'Accept-Encoding: gzip, deflate, sdch',
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest',
        'Connection: keep-alive',
    );
    
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_ENCODING => 'gzip',
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => $headerArray,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0
    );
    
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>
