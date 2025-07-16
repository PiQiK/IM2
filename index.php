<?php
session_start();
include 'connect.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: LoggedIn.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signIn'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header("Location: LoggedIn.php");
        exit();
    }
    header("Location: index.php?error=invalid_credentials");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hidden Eats Login/Register</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="CSS/index.css">
</head>
<body>
<div class="container" id="signup" style="display: none;">
  <h1 class="form-title">Register</h1>
  <form method="post" action="register.php">
    <div class="input-group">
      <i class="fas fa-user"></i>
      <input type="text" name="username" placeholder="Username" required>
    </div>
    <div class="input-group">
      <i class="fas fa-lock"></i>
      <input type="password" name="password" placeholder="Password" required>
    </div>
    <input type="submit" class="btn" name="signUp" value="Sign Up">
  </form>
  <div class="links">
    <p>Already have an account?</p>
    <button id="signInButton">Sign In</button>
  </div>
</div>

<div class="container" id="signIn">
  <h1 class="form-title">Sign In</h1>
  <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
    <p style="color:red; text-align:center;">Invalid username or password.</p>
  <?php endif; ?>
  <form method="post" action="index.php">
    <div class="input-group">
      <i class="fas fa-user"></i>
      <input type="text" name="username" placeholder="Username" required>
    </div>
    <div class="input-group">
      <i class="fas fa-lock"></i>
      <input type="password" name="password" placeholder="Password" required>
    </div>
    <input type="submit" class="btn" name="signIn" value="Sign In">
  </form>
  <div class="links">
    <p>Don't have an account?</p>
    <button id="signUpButton">Sign Up</button>
  </div>
</div>

<script>
  const signUpBtn = document.getElementById('signUpButton');
  const signInBtn = document.getElementById('signInButton');
  const signUpForm = document.getElementById('signup');
  const signInForm = document.getElementById('signIn');

  signUpBtn.addEventListener('click', () => {
    signInForm.style.display = 'none';
    signUpForm.style.display = 'block';
  });

  signInBtn.addEventListener('click', () => {
    signInForm.style.display = 'block';
    signUpForm.style.display = 'none';
  });
</script>
</body>
</html>
