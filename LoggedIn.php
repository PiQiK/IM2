<?php
session_start();

// Redirect to login page if the user is not logged in or session is invalid
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include("connect.php");

// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch user ID from session
$user_id = $_SESSION['user_id'];


// Fetch user details from the database using session data
$query = "SELECT firstName, lastName FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user = $result->fetch_assoc();
$firstName = htmlspecialchars($user['firstName']);
$lastName = htmlspecialchars($user['lastName']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage</title>
</head>
<body>
  <h1 class="title">Welcome to Hidden Eats</h1>
  
</body>
</html>
