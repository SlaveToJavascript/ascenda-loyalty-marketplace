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
    <div class="w3-container">
      <div class="w3-card w3-round w3-white">
        <div class="w3-container w3-padding">
        <h3>
          <?php echo"<b>$name</b>";?>, take a look at your rewards.
        </h3>
        <div class="w3" style="margin-top:25px;">
        <div class="w3-bar w3-theme w3-left-align w3-large">
          <a href="#" class="w3-bar-item w3-button w3-hover-white rounded-pill" style="float: left; margin: 5px; text-decoration:none; font-size: 15px;">Overview</a>
        </div>
        </div>

        <div class="w6-row" style="height=100px;">
          <div class="w3-col s4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; background-color: #30cbe0; border-radius: 10px; font-size: 20px; text-align: center;">
            ABC Bank
            <br>
            Rewards
          </div>
          <div class="w3-col s4" style="margin-right: 100px; margin-top: 15px; padding-top:20px; padding-bottom: 20px; border-radius: 10px; font-size: 15px; text-align: center;">
            <span style="font-size: 60px;">
              <?php
                echo"$miles";
              ?>
            </span>
            <br>
            AVAILABLE MILES
          </div>
        </div>
        <br>
        <br>
        <div class="w6-row" style="padding-top:140px; height=100px;">
          <hr>
          <h7>Use your <b><?php echo"$miles"; ?> miles</b> for the things that matter most.</h7>
        </div>
        <div class="w3-row" style="height=100px;">
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">...</p>
            ...
            <br>
            <br><br>
            <a href='#' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">............</a>
          </div>
          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Check Transaction Status </p>
            Poll the system for the status of the transaction periodically until completion of the transfer.
            <br><br>
            <a href='polling.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Check Status</a>
          </div>

          <div class="w3-col m4" style="margin-top: 15px; padding-top:50px; padding-bottom: 50px; border-width: thin; border-style: solid; border-color: #30cbe0; font-size: 15px; text-align: center;">
            <i class="fa fa-gg-circle fa-5x" aria-hidden="true"></i>
            <p style="font-size:20px;">Transfer Your Rewards</p>
            Transfer miles to rack your rewards with one of your travel Loyalty Programs.
            <br><br>
            <a href='transfer.php' class="w3-bar-item w3-button rounded-pill" style="background-color: #30cbe0; margin: 5px; margin-top: 10px; text-decoration:none; font-size: 20px;">Use My Miles</a>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  $_SESSION["firstName"] = explode(" ", $name)[0];
  $_SESSION["lastName"] = explode(" ", $name)[1];
?>
</body>
</html> 
