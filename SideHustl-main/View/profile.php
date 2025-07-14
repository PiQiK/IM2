<?php
session_start();
require_once 'connect.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


// Determine whose profile to fetch: the logged-in user's or a specific user's
$profile_id = $_SESSION['user_id']; // Default to logged-in user's profile

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $profile_id = intval($_GET['user_id']);
}

// Fetch user details
$user_query = $conn->prepare("SELECT firstName, lastName, email, profile_picture, bio FROM users WHERE id = ?");
$user_query->bind_param("i", $profile_id);
$user_query->execute();
$user_result = $user_query->get_result();

// If no user found, show an error or redirect
if ($user_result->num_rows === 0) {
    header("Location: error.php"); // Redirect to an error page (you can create this)
    exit;
}

$user = $user_result->fetch_assoc();

// Fetch completed jobs with employer details for this user
$jobs_query = $conn->prepare("
    SELECT j.id, j.title, j.description, e.firstName AS employer_firstName, e.lastName AS employer_lastName
    FROM jobs j
    INNER JOIN applications a ON j.id = a.job_id
    INNER JOIN users e ON j.user_id = e.id
    WHERE a.user_id = ? AND a.status = 'completed'
");
$jobs_query->bind_param("i", $profile_id);
$jobs_query->execute();
$jobs_result = $jobs_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../CSS/profile.css">
</head>
<body>
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user['firstName'] . " " . $user['lastName']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Bio: <?php echo htmlspecialchars($user['bio']); ?></p>
        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="150" height="150">
        
        <h2>Completed Jobs</h2>
        <?php if ($jobs_result->num_rows > 0): ?>
            <div id="completed-jobs">
                <?php while ($job = $jobs_result->fetch_assoc()): ?>
                    <div class="job-item" onclick="showJobDetails('<?php echo htmlspecialchars($job['title']); ?>', '<?php echo htmlspecialchars($job['description']); ?>', '<?php echo htmlspecialchars($job['employer_firstName'] . ' ' . $job['employer_lastName']); ?>')">
                        <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No completed jobs yet.</p>
        <?php endif; ?>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <div id="overlay"></div>
    <div id="job-popup">
        <h3 id="job-title"></h3>
        <p id="job-description"></p>
        <p><strong>Employer:</strong> <span id="job-employer"></span></p>
        <button class="logout-button" onclick="closePopup()">Close</button>
    </div>

    <script>
        function showJobDetails(title, description, employer) {
            document.getElementById('job-title').innerText = title;
            document.getElementById('job-description').innerText = description;
            document.getElementById('job-employer').innerText = employer;

            document.getElementById('overlay').style.display = 'block';
            document.getElementById('job-popup').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('job-popup').style.display = 'none';
        }
    </script>
</body>
</html>