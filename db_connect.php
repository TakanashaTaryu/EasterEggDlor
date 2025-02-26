<?php
$servername = "localhost";
$username = "tatsuyar_dino";  // Change to your DB username
$password = "t@Mw*AIqVj=o";      // Change to your DB password
$dbname = "tatsuyar_dino";
//$username ="admin";
//$password ="admin";
//$dbname = "dino_game";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>