<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default XAMPP password is empty
$db   = 'hidden_eats'; // make sure this matches your database name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>