<?php
require_once 'common.php';
require_once 'protect.php';

$loyaltyProgramId = '';
$description = '';
$enrollmentLink = '';
$loyaltyCurrencyName = '';
$loyaltyProgramName = '';
$processingTime = '';
$termsAndConditionsLink = '';
$loyaltyPoints = '';
$loyaltyProgramBank = 'ABC Bank';
$loyaltyProgramCode = '';

// loyalty program id not filled in
if (empty($_POST['loyaltyProgramId'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the loyalty program ID!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
// confirm_membership not filled in
elseif (empty($_POST['description'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the description!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['enrollmentLink'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the enrollment link!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['loyaltyCurrencyName'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter loyalty currency name!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['loyaltyProgramName'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter loyalty program name!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['processingTime'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the loyalty program processing time!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['termsAndConditionsLink'])) {
    $_SESSION['error_add_loyalty_program'] = "Please enter the loyalty program's terms and conditions link!";
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['loyaltyPoints'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the loyalty points!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
elseif (empty($_POST['loyaltyProgramCode'])) {
    $_SESSION['error_add_loyalty_program'] = 'Please enter the loyalty program code!';
    header("Location:../addLoyaltyProgram.php");
    return;
}
// all the fields are filled in 
elseif(isset($_POST['loyaltyProgramId']) && isset($_POST['description']) && isset($_POST['enrollmentLink']) && isset($_POST['loyaltyCurrencyName']) && isset($_POST['loyaltyProgramName']) && isset($_POST['processingTime']) && isset($_POST['termsAndConditionsLink']) && isset($_POST['loyaltyPoints']) && isset($_POST['loyaltyProgramCode'])) {
    $loyaltyProgramId = $_POST['loyaltyProgramId'];
    $description = $_POST['description'];
    $enrollmentLink = $_POST['enrollmentLink'];
    $loyaltyCurrencyName = $_POST['loyaltyCurrencyName'];
    $loyaltyProgramName = $_POST['loyaltyProgramName'];
    $processingTime = $_POST['processingTime'];
    $termsAndConditionsLink = $_POST['termsAndConditionsLink'];
    $loyaltyPoints = $_POST['loyaltyPoints'];
    $loyaltyProgramCode = $_POST['loyaltyProgramCode'];

    // update the data in database
    $url = "https://api.cs301-g7.com/loyaltyprogram/create";
    $data = array('loyaltyProgramId'=>$loyaltyProgramId, 'description'=>$description, 'enrollmentLink'=>$enrollmentLink, 'loyaltyCurrencyName'=>$loyaltyCurrencyName, 'loyaltyProgramName'=>$loyaltyProgramName, 'processingTime'=>$processingTime, 'termsAndConditionsLink'=>$termsAndConditionsLink, 'loyaltyPoints'=>$loyaltyPoints , 'loyaltyProgramBank'=>$loyaltyProgramBank, 'loyaltyProgramCode' => $loyaltyProgramCode);
    $data = json_encode($data);
    $response = httpPost($url, $data);
    $response = json_decode($response, true);
    if ($response["status"] == 400) {
        $_SESSION['error_add_loyalty_program'] = $response["message"];
        header("Location:../addLoyaltyProgram.php");
    } else if ($response["status"] == 201) {
        header("Location:../success_add_loyalty.php");
        return;
    }

}
else {
    header("Location:../addLoyaltyProgram.php");
    return;
}

function httpPost($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    $apiKey = 'tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D';
              curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-API-KEY: ' . $apiKey
                ));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>