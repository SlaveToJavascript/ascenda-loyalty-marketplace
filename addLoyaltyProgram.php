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
  <a href='logout.php' class="w3-bar-item w3-button w3-white rounded-pill" style="float: right; margin: 5px; margin-top: 13px; text-decoration:none; font-size: 20px;"><i class="fa fa-sign-out"></i> Logout</a>
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
        <br>
        <h3>
            Add Loyalty Program
        </h3>
        <br>
        <div>
            <form id="addLoyaltyForm" action="include/process_add_loyalty.php" method="post">
                <div class="row mb-3">
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Program ID</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="loyaltyProgramId" placeholder="Loyalty Program ID">
                    </div>
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Description</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="description" placeholder="Description">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Currency Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="loyaltyCurrencyName" placeholder="Loyalty Currency Name">
                    </div>
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Program Name</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="loyaltyProgramName" placeholder="Loyalty Program Name">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Points</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" name="loyaltyPoints" placeholder="Loyalty Points" min='0'>
                    </div>
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Processing Time</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="processingTime" placeholder="Processing Time">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Enrollment Link</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="enrollmentLink" placeholder="Loyalty Enrollment Link" >
                    </div>
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Terms and Conditions Link</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="termsAndConditionsLink" placeholder="Terms and Conditions Link">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="colFormLabel" class="col-sm-2 col-form-label" style="font-size: 13px;">Loyalty Program Code</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="loyaltyProgramCode" placeholder="Loyalty Program Code" >
                    </div>
                </div>
                <div class="col text-right" style=' width:100%; margin:0px; padding: 0px'>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="w3" style="margin-top:18px;">
        <div style="font-size: 18px;" id="message">
        <div style="text-align:center;">
        <?php 
            if (isset($_SESSION['error_add_loyalty_program'])) {
                echo "<p style = 'color:red'>{$_SESSION['error_add_loyalty_program']}</p>";
                unset($_SESSION['error_add_loyalty_program']);
            };
        ?>
        </div>

</html>