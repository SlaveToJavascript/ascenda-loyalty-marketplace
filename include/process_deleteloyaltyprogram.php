<?php
require_once 'common.php';
require_once 'protect.php';

$loyaltyProgramId = $_POST['btnClickedValue'];
// delete the data in database
$url = "https://api.cs301-g7.com/loyaltyprogram/delete";
$data = array('loyaltyProgramId'=>$loyaltyProgramId);
$data = json_encode($data);
$response = httpDelete($url, $data);
$response = json_decode($response, true);
if ($response["status"] == 400) {
    header("Location:../failure_loyaltyprogram.php");
    return;
} else if ($response["status"] == 201) {
    header("Location:../success_delete_loyalty.php");
    return;
}

function httpDelete($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
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