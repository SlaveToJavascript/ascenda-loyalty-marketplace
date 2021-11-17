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
    $apiKey = 'tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D';
              curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-API-KEY: ' . $apiKey
                ));
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
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_POSTFIELDS => $data
    );
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
?>