<?php
$host = "localhost";
$username = "manpreet";
$pass = "root";
$dbname = "employ_management";

$conn = mysqli_connect($host, $username, $pass, $dbname);
if(!$conn){
    die("Connection failed" . mysqli_connect_error());
}
?>