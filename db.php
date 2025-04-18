<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "fabrica";

$con = mysqli_connect($host, $username, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");
?>