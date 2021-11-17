<?php
require_once "common.php";
require_once 'protect.php';
?>
<?php

$isSuccess = false;

if (isset($_POST['status'])) {
    $status = $_POST['status'];
    if (isset($_POST["transfer_points"])) {
        $_SESSION['transfer_points'] = $_POST["transfer_points"];
    }

    if ($status == 400) {
        header("Location:../failure.php");
        return;
    } else {
        if (isset($_POST['convertedPoint']) && isset($_POST['loyaltyProgramId']) && isset($_POST['memberID']) && isset($_SESSION["firstName"]) && isset($_SESSION["lastName"]) && isset($_POST["transfer_points"]) && isset($_SESSION["userid"]) && isset($_POST["points"])) {
            $memberID = $_POST['memberID'];
            $firstName = $_SESSION["firstName"];
            $lastName = $_SESSION["lastName"];
            $transfer_points = $_POST["transfer_points"];
            $transfer_date = date("Y-m-d");
            $partner_code = "ABCSG";
            $userid = $_SESSION["userid"];
            $miles = $_POST["points"];
            $loyaltyProgramId = $_POST["loyaltyProgramId"];
            $convertedPoint = $_POST['convertedPoint'];

            $url = "https://api.cs301-g7.com/addTransferDetails";
            $data = array('Member ID'=>$memberID, 'Member first name'=>$firstName, 'Member last name'=>$lastName, 'Transfer date'=>$transfer_date, 'Amount'=>$convertedPoint, 'Partner code'=>$partner_code, 'Loyalty Program'=>$loyaltyProgramId);
            $data = json_encode($data);
            $response = httpPut($url, $data);
            $response = json_decode($response, true);
            if ($response["status"] != 201) {
                header("Location:../failure.php");
                return;
            }
            $url2 = "https://api.cs301-g7.com/updateMiles";
            $data2 = array('userid'=>$userid, 'miles'=>$miles);
            $data2 = json_encode($data2);
            $response2 = httpPost($url2, $data2);
            $response2 = json_decode($response2, true);
            if ($response2["status"] == 201) {
                $_SESSION["confirmationCode"] = $response["Reference number"];
                $_SESSION["convertedPoint"] = $convertedPoint;

                header("Location:../success.php");
            } else {
                header("Location:../failure.php");
                return;
            }
        }
        
    }
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

function httpPut($url, $data) {
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
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