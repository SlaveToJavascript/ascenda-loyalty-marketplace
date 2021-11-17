<?php

require_once 'include/common.php';

?>

<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
        <title>ABC Bank</title>
    </head>

    <body>
        <div class="d-flex justify-content-center align-items-center login-container">
            <form class="login-form text-center" method='POST' action='include/process_login.php'>
                <table style="align: center" width="350">
                <tr>
                    <td>
                        <h1 class="mb-5 font-weight-light text-uppercase">ABC Bank</h1>
                    <td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <input type="text" name="userid" class="form-control rounded-pill form-control-lg" placeholder="Username">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control rounded-pill form-control-lg" placeholder="Password">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                        <input type="submit" name="login" value="Login" class="btn mt-5 rounded-pill btn-lg btn-custom btn-block text-uppercase">  
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php 
                            if (isset($_SESSION['error'])) {
                                echo "<p style = 'color:red'>{$_SESSION['error']}</p>";
                                unset($_SESSION['error']);
                            };
                        ?>
                    </td>
                </tr>
                </table>
            </form>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
        
    </body>

</html>