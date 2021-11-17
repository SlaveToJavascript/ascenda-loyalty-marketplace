<?php
require_once 'common.php';
require_once 'protect.php';


$loyaltyProgramBank = 'ABC Bank';
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
$url = "https://api.cs301-g7.com/loyaltyprogram/update";
$data = array('loyaltyProgramId'=>$loyaltyProgramId, 'description'=>$description, 'enrollmentLink'=>$enrollmentLink, 'loyaltyCurrencyName'=>$loyaltyCurrencyName, 'loyaltyProgramName'=>$loyaltyProgramName, 'processingTime'=>$processingTime, 'termsAndConditionsLink'=>$termsAndConditionsLink, 'loyaltyPoints'=>$loyaltyPoints , 'loyaltyProgramBank'=>$loyaltyProgramBank, 'loyaltyProgramCode' => $loyaltyProgramCode);
$data = json_encode($data);
$response = httpPut($url, $data);
$response = json_decode($response, true);
if ($response["status"] == 400) {
    $_SESSION['error_update_loyalty_program'] = $response["message"];
    header("Location:../updateLoyaltyProgram.php");
} else if ($response["status"] == 201) {
    header("Location:../success_update_loyalty.php");
    return;
}

function httpPut($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
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