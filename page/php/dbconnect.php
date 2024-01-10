<?php
$servername = "localhost";
$username = "root"; //dramranc_user_28
$password = ""; // "YT?zHjnkAh=d"
$dbname = "dramranc_28";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}



?>
