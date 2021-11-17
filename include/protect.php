<?php
require_once 'common.php';
require_once 'token.php';

$pathSegments = explode('/',$_SERVER['PHP_SELF']); # Current url
$numSegment = count($pathSegments);
$currentFolder = $pathSegments[$numSegment - 2]; # Current folder
$page = $pathSegments[$numSegment -1]; # Current page

$username = '';
$password = '';
if  (isset($_SESSION['userid']) && isset($_SESSION['password'])) {
    $userid = $_SESSION['userid'];
    $password = $_SESSION['password'];
} else {
    if ($currentFolder == "include") {
        header("Location:../login.php");
        return;
    } else {
        header("Location:login.php");
        return;
    }
}
?>