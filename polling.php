<?php
require_once "include/common.php";
require_once 'include/protect.php';
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
    <div class="w3-container" style="text-align: center;">
      <div class="w3-card w3-round w3-white">
        <div class="w3-container w3-padding">
        <div style="font-size: 80px; margin-top:30px;">
            <i class="fa fa-flag-checkered fa-10x" aria-hidden="true"></i>
        </div>
        <br>
        <h3>
            Check Transaction Status
        </h3>
        <br>
        <br><br><br>
        <div class="w3-container">
            <div class="w3-row" style="text-align:left;">
                <div class="w3-col s2"> <p></p> </div>
                <div class="w3-col s8">
                  <form id="checkTransactionForm" action="include/processPolling.php" method="post">
                      Transaction ID
                      <input type="text" name="transactionID" class="form-control rounded-pill form-control-lg">
                      <br>
                      <input type="submit" name="checkNow" value="Check Now" class="btn mt-3 rounded-pill btn-lg btn-custom btn-block">  
                      <div style="text-align:center;">
                        <?php 
                            if (isset($_SESSION['error_polling'])) {
                                echo "<p style = 'color:red'>{$_SESSION['error_polling']}</p>";
                                unset($_SESSION['error_polling']);
                            };
                        ?>
                      </div>
                  </form>
                </div>
                <div class="w3-col s2"> <p></p> </div>
            </div>
        </div>
        </div>
      </div>
      <br>
    </div>
  </div>
</div>

</body>
</html> 
