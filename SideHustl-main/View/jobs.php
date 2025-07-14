<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your jobs.");
}

include("connect.php");

$query = "SELECT * FROM jobs ORDER BY post_date DESC";
$result = $conn->query($query);

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        .job-listing {
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .job-title {
            font-size: 1.2em;
            margin-bottom: 5px;
            color: #333;
        }
        .job-description {
            margin-bottom: 10px;
            color: #555;
        }
        .job-date {
            font-size: 0.9em;
            color: #999;
        }
    </style>
</head>
<body>
    <h1>Job Listings</h1>

    <?php if (count($jobs) > 0): ?>
        <?php foreach ($jobs as $job): ?>
            <div class="job-listing">
                <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                <div class="job-description"><?php echo htmlspecialchars($job['description']); ?></div>
                <div class="job-date">Posted on: <?php echo htmlspecialchars($job['post_date']); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No job listings found.</p>
    <?php endif; ?>
</body>
</html>