<?php
session_start();
include("connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid application ID.");
}


$application_id = intval($_GET['id']);

// Fetch application details
$query = "
    SELECT a.id AS application_id, a.status, u.firstName, u.lastName, u.bio, u.id AS applicant_id 
    FROM applications a
    INNER JOIN users u ON a.user_id = u.id
    WHERE a.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Application not found.");
}

$application = $result->fetch_assoc();

// Handle Accept/Reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // 'accept' or 'reject'
    $status = $action === 'accept' ? 'accepted' : 'rejected';

    // Update application status and notification flag
    $updateQuery = "UPDATE applications SET status = ?, notification_sent = TRUE WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $application_id);
    $stmt->execute();

    // Redirect back to the loggedin page
    header("Location: loggedin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .profile {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: auto;
            background: #f9f9f9;
        }
        .profile h1 {
            margin-top: 0;
        }
        .actions button {
            margin: 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .accept {
            background-color: #4CAF50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="profile">
        <h1>Applicant Profile</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($application['firstName'] . ' ' . $application['lastName']); ?></p>
        <p><strong>Bio:</strong> <?php echo htmlspecialchars($application['bio']); ?></p>

        <form method="post" class="actions">
            <button type="submit" name="action" value="accept" class="accept">Accept</button>
            <button type="submit" name="action" value="reject" class="reject">Reject</button>
        </form>
    </div>
</body>
</html>