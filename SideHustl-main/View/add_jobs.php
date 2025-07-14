<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


include("connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $post_date = date('Y-m-d H:i:s');

    $query = "INSERT INTO jobs (user_id, title, description, category, post_date) VALUES ('$user_id', '$title', '$description', '$category', '$post_date')";
    if ($conn->query($query) === TRUE) {
        header("Location: findJobs.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job</title>
</head>
<body>
    <h1>Add New Job</h1>
    <form method="POST">
        <label for="title">Job Title:</label>
        <input type="text" name="title" id="title" required><br>
        
        <label for="description">Job Description:</label>
        <textarea name="description" id="description" required></textarea><br>

        <label for="category">Job Category:</label>
        <select name="category" id="category" required>
            <option value="Accounting">Accounting</option>
            <option value="Marketing">Marketing</option>
            <option value="Landscaping">Landscaping</option>
            <option value="Translation">Translation</option>
            <option value="Art">Art</option>
            <option value="Fashion">Fashion</option>
            <option value="Music">Music</option>
            <option value="Transportation">Transportation</option>
            <option value="Cleaning Services">Cleaning Services</option>
            <option value="Fitness & Wellness">Fitness & Wellness</option>
            <option value="Tutoring">Tutoring</option>
            <option value="Pet Care">Pet Care</option>
            <option value="Technological">Technological</option>
            <option value="Graphic Design">Graphic Design</option>
            <option value="Others">Others</option>
        </select><br>

        <button type="submit">Post Job</button>
    </form>
</body>
</html>