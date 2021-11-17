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
  <a href="admin_home.php" class="w3-bar-item w3-button w3-hover-white rounded-pill" style="float: left; margin: 5px; text-decoration:none; font-size: 30px;"><i class="fa fa-home"></i>  ABC Bank</a>
  <a href='logout.php' class="w3-bar-item w3-button w3-white rounded-pill" style="float: right; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;"><i class="fa fa-sign-out"></i> Logout</a>
 </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px;">    
  <!-- The Grid -->
  <div class="w3-row">
    <!-- User Information -->
    <div class="w3-container">
      <div class="w3-card w3-round w3-white">
        <div class="w3-container w3-padding">
        <h3>
          Admin Home Page
        </h3>
        <div class="w3" style="margin-top:25px;">
        <div class="w3-bar w3-theme w3-left-align w3-large">
          <a href="#" class="w3-bar-item w3-button w3-hover-white rounded-pill" style="float: left; margin: 5px; text-decoration:none; font-size: 15px;">Overview</a>
        </div>
        </div>
        <div class="w3-row" style="height=100px;">
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Add Loyalty Program</p>
            Add new loyalty program
            <br>
            <br><br>
            <a href='addLoyaltyProgram.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Add Loyalty Program</a>
          </div>
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Update Loyalty Program</p>
            Update loyalty program details
            <br>
            <br><bR>
            <a href='display_loyaltyprograms.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Update Loyalty Program</a>
          </div>

          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Delete Loyalty Program</p>
            Delete loyalty program
            <br>
            <br><br>
            <a href='display_loyaltyprograms.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Delete Loyalty Program</a>
          </div>
        </div>
        <div class="w3-row" style="height=100px;">
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Delete Customer's PII</p>
            Delete customer's personal identifiable information (PII)
            <br>
            <br><br>
            <a href='delete_PII.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Delete PII</a>
          </div>
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Send Accrual</p>
            Send accrual file to the loyalty partners
            <br>
            <br><br>
            <a href='sendAccrual.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Send Now</a>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php

?>
</body>
</html> 
