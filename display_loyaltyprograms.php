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
            Loyalty Programs
        </h3>
        <div class="w3" style="margin-top:10px;">
        <div class="w3-bar w3-theme w3-left-align w3-large" style="margin-bottom:10px;">
          <a class="w3-bar-item w3-button w3-hover-white rounded-pill" style="float: left; margin: 5px; text-decoration:none; font-size: 15px;"></a>
        </div>
        <div id="loyalty" style="margin-top=100px;"></div>

        <?php
          $url = "https://api.cs301-g7.com/loyaltyprogram/bank/ABC%20Bank";
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $apiKey = 'tExsjIZfRLa3CRck1E4G51BYlljnBE35ats6dP6D';
              curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-API-KEY: ' . $apiKey
                ));
          $output = curl_exec($ch);
          curl_close($ch);
          $output = json_decode($output, true);

          if (count($output["Items"]) > 0) {
            $data = $output["Items"];
            $index = 0;
            foreach ($data as $loyaltyData) {
              // console.log('" . $data['this.name'] . "');
              echo "<script type=\"text/javascript\">
              var loyalty_div = document.getElementById('loyalty');
              var parent_div = document.createElement('div');
              parent_div.classList.add('w3-row-padding');

              var div = document.createElement('div');
              div.classList.add('w3-col');
              div.classList.add('s10');
              var div_header = document.createElement('h5');
              div_header.append('" . $loyaltyData['loyaltyProgramName'] . "');
              div.appendChild(div_header)

              var button_div = document.createElement('div');
              button_div.classList.add('w3-col');
              button_div.classList.add('s2');
              var button = document.createElement('button');
              button.classList.add('w3-bar-item');
              button.classList.add('w3-button');
              button.classList.add('rounded-pill');
              button.setAttribute('id', '" . $loyaltyData['loyaltyProgramId'] . "');
              button.setAttribute('name'," . $index . ");
              button.style.backgroundColor = '#30cbe0';
              button.style.marginTop = '10px';
              button.style.float = 'right';
              button.append(document.createTextNode('Update'));

              var button_delete = document.createElement('button');
              button_delete.classList.add('w3-bar-item');
              button_delete.classList.add('w3-button');
              button_delete.classList.add('rounded-pill');
              button_delete.setAttribute('id', '" . $loyaltyData['loyaltyProgramId'] . "');
              button_delete.setAttribute('name'," . $index . ");
              button_delete.style.backgroundColor = '#30cbe0';
              button_delete.style.marginTop = '10px';
              button_delete.style.marginLeft = '5px';
              button_delete.style.float = 'right';
              button_delete.append(document.createTextNode('Delete'));

              button_div.append(button_delete);
              button_div.append(button);
              parent_div.appendChild(div);
              parent_div.append(button_div);
              loyalty_div.appendChild(parent_div);

              loyalty_div.append(document.createElement('hr'));

              button.addEventListener(\"click\", function() {
                document.body.innerHTML += '<form id=\"updateForm\" action=\"updateLoyaltyProgram.php\" method=\"post\"><input type=\"hidden\" name=\"btnClickedValue\" value=\"' + this.id + '\"></form>';
                document.getElementById(\"updateForm\").submit();
                var x = " . json_encode($data) . "
                sessionStorage.loyaltyPoints = x[this.name][\"loyaltyPoints\"];
                sessionStorage.loyaltyCurrencyName = x[this.name][\"loyaltyCurrencyName\"];
                sessionStorage.loyaltyProgramName = x[this.name][\"loyaltyProgramName\"];
                sessionStorage.loyaltyProgramId = x[this.name][\"loyaltyProgramId\"];
                sessionStorage.description = x[this.name][\"description\"];
                sessionStorage.enrollmentLink = x[this.name][\"enrollmentLink\"];
                sessionStorage.processingTime = x[this.name][\"processingTime\"];
                sessionStorage.termsAndConditionsLink = x[this.name][\"termsAndConditionsLink\"];
                sessionStorage.loyaltyProgramCode = x[this.name][\"loyaltyProgramCode\"];
              });
              
              button_delete.addEventListener(\"click\", function() {
                document.body.innerHTML += '<form id=\"delete\" action=\"include/process_deleteloyaltyprogram.php\" method=\"post\"><input type=\"hidden\" name=\"btnClickedValue\" value=\"' + this.id + '\"></form>';
                document.getElementById('delete').submit();
              });
              </script>";
              $index++;
            }
          }
        ?>
        </div>
      </div>
      <br>
    </div>
  </div>
</div>

</body>
</html> 
