<?php
require_once 'common.php';
require_once 'protect.php';
?>

<?php
$errors = [];

// username not filled in
if (empty($_POST['userid'])) {
    $_SESSION['error'] = 'Please enter your username!';
    header("Location:../login.php");
    return;

// password not filled in
} elseif (empty($_POST['password'])) {
    $_SESSION['error'] = 'Please enter your password!';
    header("Location:../login.php");
    return;

// username and password are filled in
} elseif (isset($_POST['userid']) && isset($_POST['password'])) {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // user is admin
    // successful login for admin
    if ($userid === 'admin' && $password === 'admin') { 
        $_SESSION['userid'] = 'admin'; 
        $_SESSION['password'] = 'admin';
        header("Location: ../admin_home.php");
        return;
    
    // wrong password for admin
    } elseif ($userid === 'admin' && $password !== 'admin') { 
        $_SESSION['error'] = 'Invalid password!';
        header("Location:../login.php");
        return;
    
    // user is student
    } else { 
        $dao = new UserDAO();
        $user = $dao->getUserByID($userid);

        if ($user != null) { // check if user exist
            $userUserID = $user->getUserID();
            $check_password = $user->authenticate($password);
            // wrong username for user 
            if ($userUserID !== $userid) {
                $_SESSION['error'] = 'Invalid username!'; 
                header("Location:../login.php");
                return;
            // successful login for user 
            } elseif ( $userUserID != null && $user->authenticate($password) ) {
                $_SESSION['userid'] = $userid; 
                $_SESSION['password'] = $password;
                header("Location:../home.php");
                return;
            // wrong password for user
            } else {
                $_SESSION['error'] = 'Invalid password!';
                header("Location:../login.php");
                return;
            }
        }
        // wrong username for user
        elseif ($user == null) {
            $_SESSION['error'] = $user; 
            header("Location:../login.php");
            return;
        }
    }
} else {
    header("Location:../login.php");
    return;
}
?>