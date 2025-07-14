<?php
include 'init.php';
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
  <title>Sign In - Hidden Eats</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fffaf2;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .container {
      background: #ffffff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 0 25px rgba(0,0,0,0.1);
      width: 350px;
      text-align: center;
    }
    h1 {
      color: #d35400;
      margin-bottom: 20px;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      background-color: #e67e22;
      color: white;
    }
    button:hover {
      background-color: #d35400;
    }
    .link {
      color: #3498db;
      cursor: pointer;
      display: block;
      margin-top: 12px;
      text-decoration: none;
    }
  </style>
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
