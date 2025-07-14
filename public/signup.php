<?php
include 'init.php';

// Start the session
session_start();

// If the user is already logged in, redirect to LoggedIn.php
if (isset($_SESSION['user_id'])) {
    header("Location: LoggedIn.php");
    exit();
}


if (isset($_POST['signUp'])) {
    // Get user inputs and sanitize
    $firstName = $conn->real_escape_string($_POST['fName']);
    $lastName = $conn->real_escape_string($_POST['lName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing

    // Check if email exists
    $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: login.php?error=email_exists");
        exit();
    }

    // Insert new user into the database
    $insertQuery = "INSERT INTO users (firstName, lastName, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $password);

    if ($stmt->execute()) {
        // Set session variables for the newly registered user
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $firstName . " " . $lastName;
        $_SESSION['email'] = $email;

        // Redirect to LoggedIn.php
        header("Location: LoggedIn.php");
        exit();
    } else {
        // Log the error
        echo "Error: " . $conn->error;
    }
}
?>
