<?php
include '../database_credentials.php';
$servername = $servername;
$username = $username;
$password = $password;
$database = $database;
// Create connection
$connect_db = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$connect_db) {
  die("Connection failed: " . mysqli_connect_error());
}
$flash_message = "Connected successfully";
?>
