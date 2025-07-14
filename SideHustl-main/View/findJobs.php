<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'connect.php'; // Include the database connection

$user_id = $_SESSION['user_id']; // Logged-in user ID


// Handle the "Apply" button
if (isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];

    // Check if the user has already applied for this job
    $checkQuery = "SELECT * FROM applications WHERE job_id = ? AND user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $job_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "You have already applied for this job.";
    } else {
        // Insert the application into the database
        $insertQuery = "INSERT INTO applications (job_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $job_id, $user_id);

        if ($stmt->execute()) {
            $message = "Application submitted successfully!";
        } else {
            $message = "Error: Could not submit application.";
        }
    }
}

// Fetch categories for the dropdown
$categoryQuery = "SELECT DISTINCT category FROM jobs";
$categoriesResult = $conn->query($categoryQuery);

// Handle search
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// SQL query with optional search and category filters
$jobsQuery = "
    SELECT j.*, u.firstName, u.lastName, u.id AS user_id 
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id
    JOIN users u ON j.user_id = u.id
    WHERE (a.status IS NULL OR a.status NOT IN ('accepted', 'completed'))
";

if (!empty($searchKeyword)) {
    $jobsQuery .= " AND j.title LIKE '%" . $conn->real_escape_string($searchKeyword) . "%'";
}

if (!empty($categoryFilter)) {
    $jobsQuery .= " AND j.category = '" . $conn->real_escape_string($categoryFilter) . "'";
}

$result = $conn->query($jobsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs Dashboard</title>
</head>
<body>
    
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <!-- Display Feedback Message -->
    <?php if (!empty($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Search and Filter Form -->
    <form method="GET" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search for jobs..." value="<?php echo htmlspecialchars($searchKeyword); ?>">
        
        <select name="category">
            <option value="">All Categories</option>
            <?php while ($categoryRow = $categoriesResult->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($categoryRow['category']); ?>"
                    <?php echo ($categoryFilter === $categoryRow['category']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoryRow['category']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search</button>
    </form>

    <!-- Display Jobs Page -->
<div>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<p>Category: " . htmlspecialchars($row['category']) . "</p>";
            echo "<p>Posted on: " . htmlspecialchars($row['post_date']) . "</p>";
            echo "<p>Posted by: <a href='profile.php?user_id=" . htmlspecialchars($row['user_id']) . "'>"
                . htmlspecialchars($row['firstName']) . " " . htmlspecialchars($row['lastName'])
                . "</a></p>";
            // Add Apply Button for each job
            echo '<form method="POST" style="display:inline;">';
            echo '<input type="hidden" name="job_id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<button type="submit" name="apply">Apply</button>';
            echo '</form>';
            echo "</div><hr>";
        }
    } else {
        echo "<p>No jobs available.</p>";
    }
    ?>
</div>

    <!-- Add Job Button -->
    <button onclick="window.location.href='add_jobs.php';">Add a Job</button>

    <a href="logout.php">Logout</a>
</body>
</html>