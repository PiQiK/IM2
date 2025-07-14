<?php
session_start();

// Redirect to login page if the user is not logged in or session is invalid
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("connect.php");

// Enable MySQLi error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch user ID from session
$user_id = $_SESSION['user_id'];


// Handle form submissions for job actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;

    if ($action === 'complete') {
        // Mark the job and its applications as 'completed'
        $completeJobQuery = "
            UPDATE applications 
            SET status = 'completed' 
            WHERE job_id = ? AND status = 'accepted'";
        $stmt = $conn->prepare($completeJobQuery);
        $stmt->bind_param("i", $job_id);
        if ($stmt->execute()) {
            $success_message = "Job marked as completed successfully!";
        } else {
            $error_message = "Failed to complete the job.";
        }
        $stmt->close();
        // Redirect to avoid form resubmission
        header("Location: loggedin.php");
        exit();
    } elseif ($action === 'delete') {
        // First delete related applications
        $deleteApplicationsQuery = "DELETE FROM applications WHERE job_id = ?";
        $stmt = $conn->prepare($deleteApplicationsQuery);
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $stmt->close();

        // Then delete the job itself
        $deleteJobQuery = "DELETE FROM jobs WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($deleteJobQuery);
        $stmt->bind_param("ii", $job_id, $user_id);
        if ($stmt->execute()) {
            $success_message = "Job deleted successfully!";
        } else {
            $error_message = "Failed to delete the job.";
        }
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: loggedin.php");
        exit();
    }
}

// Fetch user details from the database using session data
$query = "SELECT firstName, lastName, profile_picture, bio FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();
$firstName = htmlspecialchars($user['firstName']);
$lastName = htmlspecialchars($user['lastName']);
$profilePicture = htmlspecialchars($user['profile_picture']) ?: 'imgs/default-profile.png';
$bio = htmlspecialchars($user['bio']);

// Fetch jobs added by the logged-in user excluding completed jobs
$jobsAddedQuery = "
    SELECT 
        j.id AS job_id, 
        j.title, 
        j.description, 
        j.category,  -- Add this line to fetch the category
        u.id AS employee_id, 
        u.firstName, 
        u.lastName, 
        a.status 
    FROM jobs j
    LEFT JOIN applications a 
        ON j.id = a.job_id 
        AND a.status = 'accepted' 
    LEFT JOIN users u 
        ON a.user_id = u.id
    WHERE j.user_id = ? AND j.id NOT IN (
        SELECT job_id 
        FROM applications 
        WHERE status = 'completed'
    )";
$stmt = $conn->prepare($jobsAddedQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$jobsAdded = $stmt->get_result();

// Fetch ongoing jobs where the user is the employee
$ongoingJobsQuery = "
    SELECT j.id AS job_id, j.title, j.description, u.id AS employer_id, u.firstName, u.lastName 
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.id
    INNER JOIN users u ON j.user_id = u.id
    WHERE a.user_id = ? AND a.status = 'accepted'";
$stmt = $conn->prepare($ongoingJobsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$ongoingJobs = $stmt->get_result();

// Fetch pending applications for notifications
$applicationsQuery = "
    SELECT a.id AS application_id, a.status, u.firstName, u.lastName, u.bio, j.title 
    FROM applications a
    INNER JOIN jobs j ON a.job_id = j.id
    INNER JOIN users u ON a.user_id = u.id
    WHERE j.user_id = ? AND a.status = 'pending'
";
$stmt = $conn->prepare($applicationsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$applications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../CSS/LoggedIn.css">
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('show');
        }
    </script>
</head>
<body>
    <nav>
        <div class="nav-logo">
            <img src="../imgs/logo.png" alt="logo.png">
        </div>
        <div class="nav-cont">
            <ul class="nav-list">
                <ol><button>Category</button></ol>
                <ol>
                    <a href="findJobs.php" style="text-decoration: none;">
                        <button>Find Talent</button>
                    </a>
                </ol>
                <div class="notification">
                    <div class="notification-icon" onclick="toggleDropdown()">
                        <img src="imgs/notification-icon.png" alt="Notifications">
                        <?php if ($applications->num_rows > 0): ?>
                            <span class="notification-badge"><?php echo $applications->num_rows; ?></span>
                        <?php endif; ?>
                    </div>
                    <div id="notification-dropdown" class="dropdown-content">
                        <?php if ($applications->num_rows > 0): ?>
                            <?php while ($row = $applications->fetch_assoc()): ?>
                                <div>
                                    <strong>Applicant:</strong> <?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?><br>
                                    <strong>Job:</strong> <?php echo htmlspecialchars($row['title']); ?><br>
                                    <button onclick="window.location.href='view_application.php?id=<?php echo $row['application_id']; ?>'">View</button>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div>No new applications</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="nav-profile">
                    <a href="profile.php">
                        <img src="<?php echo $profilePicture; ?>" alt="Profile" class="profile-icon">
                    </a>
                </div>
            </ul>
        </div>
    </nav>
    <main>
        <h1>Welcome, <?php echo $firstName . ' ' . $lastName; ?>!</h1>

        <!-- Display success or error messages -->
        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Jobs Added by the User -->
        <h2>Your Posted Jobs</h2>
        <div class="jobs-list">
            <?php if ($jobsAdded->num_rows > 0): ?>
                <?php while ($job = $jobsAdded->fetch_assoc()): ?>
                    <div class="job-card" id="job-<?php echo $job['job_id']; ?>">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <?php if ($job['employee_id']): ?>
                            <p>
                                <strong>Employee:</strong>
                                <a href="profile.php?user_id=<?php echo $job['employee_id']; ?>">
                                    <?php echo htmlspecialchars($job['firstName'] . ' ' . $job['lastName']); ?>
                                </a>
                            </p>
                        <?php else: ?>
                            <p><strong>Employee:</strong> Not assigned yet</p>
                        <?php endif; ?>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                        <?php if ($job['status'] === 'accepted'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                <button type="submit" name="action" value="complete">Complete Job</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                            <button type="submit" name="action" value="delete">Delete Job</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No jobs posted yet.</p>
            <?php endif; ?>
        </div>

        <!-- Ongoing Jobs -->
        <h2>Your Ongoing Jobs</h2>
        <div class="jobs-list">
            <?php if ($ongoingJobs->num_rows > 0): ?>
                <?php while ($job = $ongoingJobs->fetch_assoc()): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p><?php echo htmlspecialchars($job['description']); ?></p>
                        <p>
                            <strong>Employer:</strong>
                            <a href="profile.php?user_id=<?php echo $job['employer_id']; ?>">
                                <?php echo htmlspecialchars($job['firstName'] . ' ' . $job['lastName']); ?>
                            </a>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No ongoing jobs.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
