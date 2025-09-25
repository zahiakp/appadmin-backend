<?php

$servername = "localhost";
$password = "zUBUqJEq0X@";
$username = "root";
$password = "";
$dbname = "addadmin";

// $dbname = "u516533008_application";

// $username = "u516533008_application";
// Create conn
// $conn = mysqli_connect($servername, $username, $password, $dbname);
// // Check conn
// if (!$conn) {
//     die("conn failed: " . mysqli_connect_error());
// }
// mysqli_set_charset($conn, "utf8");

// $servername = "localhost";
// $password = "zIrqkM^4Ft";
// $username = "u528796689_prismbulletin";
// $dbname = "u528796689_prismbulletin";
// // Create conn
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check conn
if (!$conn) {
    die("conn failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn,"utf8");