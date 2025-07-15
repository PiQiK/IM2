<?php
include 'connect.php';
session_start();

// Redirect to LoggedIn.php if the user is already logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header("Location: LoggedIn.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signIn'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT id, firstName, lastName, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['firstName'] . " " . $user['lastName'];

            header("Location: LoggedIn.php");
            exit();
        } else {
            header("Location: login.php?error=invalid_credentials");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>
<body>

  <div class="container">
    <h1>Sign In</h1>
    <input type="text" placeholder="Username or Email" required>
    <input type="password" placeholder="Password" required>
    <button>Sign In</button>
    <a href="index.html" class="link">⬅ Back to Menu</a>
  </div>

</body>
</html>
