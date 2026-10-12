<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include "includes/database.php";

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];

// Count this user's applications
$sql = "SELECT
            COUNT(*) AS total,
            SUM(status = 'Saved') AS saved,
            SUM(status = 'Applied') AS applied,
            SUM(status = 'Interview') AS interviews,
            SUM(status = 'Accepted') AS accepted,
            SUM(status = 'Rejected') AS rejected,
            SUM(deadline < CURDATE() AND deadline IS NOT NULL AND deadline != ''
                AND status NOT IN ('Accepted', 'Rejected')) AS overdue,
            SUM(deadline >= CURDATE() AND deadline IS NOT NULL AND deadline != ''
                AND status NOT IN ('Accepted', 'Rejected')) AS upcoming
        FROM applications
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

$total_applications = $data["total"] ?? 0;
$applied = $data["applied"] ?? 0;
$interviews = $data["interviews"] ?? 0;
$accepted = $data["accepted"] ?? 0;
$saved = $data["saved"] ?? 0;
$rejected = $data["rejected"] ?? 0;
$overdue = $data["overdue"] ?? 0;
$upcoming = $data["upcoming"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIWES Tracker</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php" class="active">🏠 Dashboard</a>
        <a href="applications/add.php">➕ Add Application</a>
        <a href="applications/view.php">📋 My Applications</a>
        <a href="logout.php" class="logout-link">🚪 Logout</a>
    </nav>
    <section class="welcome-section">
            <div>
                <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                <p>Keep track of your SIWES applications and stay on top of your deadlines.</p>
            </div>
    </section>

    <section class="application-overview">
            <h2>📊 Your Application Overview</h2>

            <?php if ($total_applications == 0): ?>
                <p>You haven't added any applications yet. Start tracking your SIWES journey today!</p>
            <?php elseif ($total_applications == 1): ?>
                <p>You currently have 1 application in your tracker.</p>
            <?php else: ?>
                <p>You currently have <?php echo $total_applications; ?> applications in your tracker.</p>
            <?php endif; ?>
    </section>


   <section class="statistics"> 
            <h2>Application Statistics</h2> 
 
            <div class="statistics-grid"> 
 
                <div class="stat-card total-card"> 
                    <span class="stat-icon">📁</span> 
                    <p>Total Applications</p> 
                    <span class="stat-number"><?php echo $total_applications; ?></span> 
                </div> 
 
                <div class="stat-card applied-card"> 
                    <span class="stat-icon">📨</span> 
                    <p>Applied</p> 
                    <span class="stat-number"><?php echo $applied; ?></span> 
                </div> 
 
                <div class="stat-card interview-card"> 
                    <span class="stat-icon">🎤</span> 
                    <p>Interviews</p> 
                    <span class="stat-number"><?php echo $interviews; ?></span> 
                </div> 
 
                <div class="stat-card accepted-card"> 
                    <span class="stat-icon">🎉</span> 
                    <p>Accepted</p> 
                    <span class="stat-number"><?php echo $accepted; ?></span> 
                </div> 
 
                <div class="stat-card saved-card"> 
                    <span class="stat-icon">🔖</span> 
                    <p>Saved</p> 
                    <span class="stat-number"><?php echo $saved; ?></span> 
                </div> 
 
                <div class="stat-card rejected-card"> 
                    <span class="stat-icon">❌</span> 
                    <p>Rejected</p> 
                    <span class="stat-number"><?php echo $rejected; ?></span> 
                </div> 
 
                <div class="stat-card overdue-card"> 
                    <span class="stat-icon">⚠️</span> 
                    <p>Overdue Applications</p> 
                    <span class="stat-number"><?php echo $overdue; ?></span> 
                </div> 
 
                <div class="stat-card upcoming-card"> 
                    <span class="stat-icon">📅</span> 
                    <p>Upcoming Deadlines</p> 
                    <span class="stat-number"><?php echo $upcoming; ?></span> 
                </div> 
 
            </div> 
        </section> 


</body>
</html>