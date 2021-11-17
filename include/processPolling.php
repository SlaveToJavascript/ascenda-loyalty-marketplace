<?php
require_once 'common.php';
require_once 'protect.php';
?>

<?php
$transactionID = '';
$bankCode = 'ABCSG';


$userid = '';
$password = '';

if(isset($_SESSION['userid']) && isset($_SESSION['password'])) {
    $userid = $_SESSION['userid'];
    $user_dao = new UserDAO();
    $user = $user_dao->getUserByID($userid);
    $name = $user->getName();
}

// transactionID not filled in
if (empty($_POST['transactionID'])) {
    $_SESSION['error_polling'] = 'Please enter your transaction ID!';
    header("Location:../polling.php");
    return;
}
// all the fields are filled in 
elseif(isset($_POST['transactionID'])) {
    $transactionID = $_POST['transactionID'];
    $nameList = explode(" ", $name);
    $firstName = $nameList[0];
    $lastName = $nameList[1];
    $url = "https://api.cs301-g7.com/file/getDate";
    $data = array('transactionID'=>$transactionID, 'Member first name'=>$firstName, 'Member last name'=>$lastName);
    $data = json_encode($data);
    $response = httpPost($url, $data);
    $response = json_decode($response, true);
    if ($response["status"] != 201) {
        $_SESSION['error_polling'] = $response["message"];
        header("Location:../polling.php");
        return;
    } else {
        $date = $response["message"]["Transfer date"]["S"];
        $url2 = "https://api.cs301-g7.com/file/getOutcomeCode";
        $data2 = array('transactionID'=>$transactionID, 'transferDate'=>$date, 'bankCode'=>$bankCode);
        $data2 = json_encode($data2);
        $response2 = httpPost($url2, $data2);
        $response2 = json_decode($response2, true);
        var_dump($response2);
        if ($response2["status"] != 201) {
            $_SESSION['error_polling'] = 'Error with retrieving data from Ascenda';
            header("Location:../polling.php");
            return;
        } else {
            $message = $response2["message"];
            header("Location:../pollingResults.php?message=".$message);
        }
    }
}
else {
    $_SESSION['error_polling'] = 'No transaction ID!';
    header("Location:../polling.php");
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
