<?php
require_once 'common.php';
require_once 'protect.php';

$name = $_POST['btnClickedValue'];
$name = explode(" ", $name);
$memberFirstName = $name[0];
$memberLastName = $name[1];
$bankID = 'ABCSG';

// delete the data in database
$url = "https://api.cs301-g7.com/validation/deletePII";
$data = array('memberFirstName'=>$memberFirstName, 'memberLastName' => $memberLastName, 'bankID' => $bankID);
$data = json_encode($data);
$response = httpDelete($url, $data);
$response = json_decode($response, true);
if ($response["status"] == 400) {
    header("Location:../failure_delete_PII.php");
    return;
} else if ($response["status"] == 201) {
    header("Location:../success_delete_PII.php");
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