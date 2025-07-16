<?php
session_start();
include 'connect.php';

if (!empty($_SESSION['user_id'])) {
    header("Location: LoggedIn.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: index.php?error=username_exists");
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['is_admin'] = 0;
        header("Location: LoggedIn.php");
        exit();
    } else {
        echo "Registration failed: " . $conn->error;
    }
} else {
    header("Location: index.php");
    exit();
}
