<?php
require_once "include/common.php";
require_once 'include/protect.php';
require_once 'include/process_home.php';
?>

<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/style.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<body>
<!-- Navbar -->
<div class="w3-top">
 <div class="w3-bar w3-theme w3-left-align w3-large">
  <a href="home.php" class="w3-bar-item w3-button w3-hover-white rounded-pill" style="float: left; margin: 5px; text-decoration:none; font-size: 30px;"><i class="fa fa-home"></i>  ABC Bank</a>
  <a href='logout.php' class="w3-bar-item w3-button w3-white rounded-pill" style="float: right; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;"><i class="fa fa-sign-out"></i> Logout</a>
 </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;">    
  <!-- The Grid -->
  <div class="w3-row">
    <!-- User Information -->
    <?php 
        if (isset($_SESSION['userid']) && isset($_GET['loyalty_name'])) {
            $userid = $_SESSION['userid'];
            $loyalty_name = $_GET['loyalty_name'];
            
            $dao = new UserDAO();
            $user = $dao->getUserByID($userid);
            $miles = $user->getMiles();

            $m_dao = new MembershipDAO();
            $user_membership = $m_dao->getMembershipByIDLoyaltyProgram($userid, $loyalty_name);
            $user_membership_id = $user_membership->getMembershipid();
        }
    ?>
    <div class="w3-container" style="text-align: center;">
      <div class="w3-card w3-round w3-white">
        <div class="w3-container w3-padding">
        <div style="font-size: 80px; margin-top:30px;">
            <i class="fa fa-gift fa-10x" aria-hidden="true"></i>
        </div>
        <br>
        <h3>
            Transfer Your Miles
        </h3>
        <br>
        <div class="w3" style="margin-top:10px;">
        <div style="font-size: 15px;">
            Transfer your miles to your <b><?php echo $loyalty_name; ?></b> account.
            <br>
            <b style="font-size: 20px;"><?php echo($user_membership_id)?></b>
        </div>

        <div class="w6-row" style="height=100px; margin-top: 15px; padding-top:30px; padding-bottom: 80px; background-color: #30cbe0; border-radius: 10px; font-size: 20px; text-align: center;">
          <div class="w3-col s3">
            <?php echo $miles; ?>
            <br>
            AVAILABLE
          </div>
          <div class="w3-col s1"><p>-</p></div>
          <div class="w3-col s3" id="displayPoints">
            0
            <br>
            USING
          </div>
          <div class="w3-col s2"><p>=</p></div>
          <div class="w3-col s3" id="displayRemainingPoints">
            <?php echo $miles; ?>
            <br>
            REMAINING
          </div>
        </div>

        <br><br><br>
        <div class="w3-container">
            <div class="w3-row" style="font-size: 18px;">
                <div class="w3-col s4"> <p></p> </div>
                <div class="w3-col s4">
                  <form id="completeMilesTransferForm" action="include/process_completeMilesTransfer.php" method="post">
                      Total Rewards to Transfer
                      <input type="text" name="transfer_points" class="form-control rounded-pill form-control-lg" value="0" style="text-align:center;">
                      <div style="text-align:center; font-size: 15px;" id="error_miles"></div>
                      <div style="text-align:center; font-size: 15px;" id="convertedPoints"></div>
                      <input type="hidden" id="memberID" name="memberID" value=<?php echo($user_membership_id)?>>
                      <input type="hidden" id="status" name="status" value=400>
                      <input type="hidden" id="points" name="points" value=0>
                      <input type="hidden" id="convertedPoint" name="convertedPoint" value=0>
                      <input type="hidden" id="loyaltyProgramId" name="loyaltyProgramId" value="">
                      <input type="submit" name="complete_transfer" value="Complete Transfer" class="btn mt-3 rounded-pill btn-lg btn-custom btn-block">  
                  </form>
                </div>
                <div class="w3-col s4"> <p></p> </div>
            </div>
        </div>
        <div style="font-size: 12px;">
            <b>All transfers are final.</b>
        </div>
        <br>
        <div style="font-size: 10px;">
            Once rewards have been transferred, they are subject to the terms of the Loyalty Program to which they were transferred.
        </div>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script>
            var input = document.getElementsByName("transfer_points")[0];
            var loyaltyPoints = sessionStorage.getItem("loyaltyPoints");
            var loyaltyCurrencyName = sessionStorage.getItem("loyaltyCurrencyName");
            var loyaltyProgramId = sessionStorage.getItem("loyaltyProgramId");

            input.addEventListener("input", function(){
                var points = input.value;
                axios.post('https://api.cs301-g7.com/loyaltyprogram/convertCurrencies', {
                    miles: <?php echo($miles)?>,
                    points: points,
                    loyaltyPoints: loyaltyPoints,
                    rewards_to_transfer: points,
                    balance: <?php echo $miles;?>,
                    loyaltyCurrencyName: loyaltyCurrencyName,
                    loyaltyProgramId : loyaltyProgramId,
                  })
                  .then(function (response) {
                    if (response["data"]["remaining_points"] != <?php echo($miles)?>) {
                      remaining_points = response["data"]["remaining_points"];
                      document.getElementById("displayPoints").innerHTML = points + "<br> USING";
                      document.getElementById("displayRemainingPoints").innerHTML = remaining_points + "<br> REMAINING";
                      document.getElementById("error_miles").innerHTML = "<br><p style = color:red;>" + response["data"]["errorMilesMessage"] + "</p>";
                      document.getElementById("convertedPoints").innerHTML = "<p>" + response["data"]["convertedPointsMessage"] + "</p>";
                      document.getElementById("status").value = response["data"]["status"];
                      document.getElementById("points").value = points;
                      document.getElementById("convertedPoint").value = response["data"]["convertedPoints"];
                      document.getElementById("loyaltyProgramId").value = loyaltyProgramId;
                    }
                  })
            })
        </script>
        </div>
      </div>
      <br>
    </div>
  </div>
</div>

</body>
</html> 
