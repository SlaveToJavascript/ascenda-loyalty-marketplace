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
          $curl = curl_init();
          $config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0 Chrome/89.0.4389.114';
          curl_setopt($curl, CURLOPT_USERAGENT, $config['useragent']);
          // curl_setopt($curl, CURLOPT_POST, true);
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
              CURLOPT_SSL_VERIFYPEER => 0
          );
          
          curl_setopt_array($curl, $options);
          $output = curl_exec($curl);
          curl_close($curl);
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

              var points_para = document.createElement('p');
              points_para.append(document.createTextNode('1000 ABC Points = ' + '" . $loyaltyData['loyaltyPoints'] . "' + ' ' + '" . $loyaltyData['loyaltyCurrencyName'] . "'));
              div.appendChild(points_para);

              var button_div = document.createElement('div');
              button_div.classList.add('w3-col');
              button_div.classList.add('s2');
              var button = document.createElement('button');
              button.classList.add('w3-bar-item');
              button.classList.add('w3-button');
              button.classList.add('rounded-pill');
              button.setAttribute('id', '" . $loyaltyData['loyaltyProgramName'] . "');
              button.setAttribute('name'," . $index . ");
              button.style.backgroundColor = '#30cbe0';
              button.style.marginTop = '10px';
              button.style.float = 'right';
              button.append(document.createTextNode('Transfer Miles'));
              button_div.append(button);

              parent_div.appendChild(div);
              parent_div.append(button_div);
              loyalty_div.appendChild(parent_div);

              loyalty_div.append(document.createElement('hr'));

              button.addEventListener(\"click\", function() {
                document.body.innerHTML += '<form id=\"transferForm\" action=\"include/process_transfer.php\" method=\"post\"><input type=\"hidden\" name=\"btnClickedValue\" value=\"' + this.id + '\"></form>';
                document.getElementById(\"transferForm\").submit();
                var x = " . json_encode($data) . "
                sessionStorage.loyaltyPoints = x[this.name][\"loyaltyPoints\"];
                sessionStorage.loyaltyCurrencyName = x[this.name][\"loyaltyCurrencyName\"];
                sessionStorage.loyaltyProgramName = x[this.name][\"loyaltyProgramName\"];
                sessionStorage.loyaltyProgramId = x[this.name][\"loyaltyProgramId\"];
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
