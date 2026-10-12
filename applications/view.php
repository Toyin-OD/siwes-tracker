<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/database.php";

$user_id = $_SESSION["user_id"];
//search code begin
$search = isset($_GET["search"]) ? $_GET["search"] : "";
$status_filter = isset($_GET["status"]) ? $_GET["status"] : "";
$sql = "SELECT * FROM applications 
        WHERE user_id = ?
        AND (company_name LIKE ? OR position LIKE ?)";

$search_value = "%" . $search . "%";
if ($status_filter != "") {
    $sql .= " AND status = ?";
}
$sql .= " ORDER BY deadline ASC";
$stmt = mysqli_prepare($conn, $sql);

if ($status_filter != "") {

    mysqli_stmt_bind_param(
        $stmt,
        "isss",
        $user_id,
        $search_value,
        $search_value,
        $status_filter
    );
} else {
    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $user_id,
        $search_value,
        $search_value
    );
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
//search end
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications | SIWES Tracker</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

   <div class="page-header">
    <div>
        <h1>📋 My SIWES Applications</h1>
        <p>Manage your applications and keep track of your progress.</p>
    </div>
    <a href="add.php" class="add-application-btn">➕ Add New Application</a>
   </div>
   
   <form method="GET" action="" class="search-form">
    <input
        type="text"
        name="search"
        placeholder="Search company or position"
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
    >

    <select name="status">

        <option value="">All Statuses</option>
        <option value="Saved">Saved</option>
        <option value="Applied">Applied</option>
        <option value="Interview">Interview</option>
        <option value="Accepted">Accepted</option>
        <option value="Rejected">Rejected</option>

    </select>
    <button type="submit">Search</button>
    </form>
    <br>

<?php if (mysqli_num_rows($result) > 0): ?>

    <div class="table-container">
        <table class="applications-table">

            <thead>
                <tr>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Location</th>
                    <th>Date Applied</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Deadline Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($application = mysqli_fetch_assoc($result)): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($application["company_name"]); ?></td>

                        <td><?php echo htmlspecialchars($application["position"]); ?></td>

                        <td><?php echo htmlspecialchars($application["location"]); ?></td>

                        <td>
                            <?php echo !empty($application["date_applied"])
                                ? htmlspecialchars($application["date_applied"])
                                : "Not set"; ?>
                        </td>

                        <td>
                            <?php echo !empty($application["deadline"])
                                ? htmlspecialchars($application["deadline"])
                                : "Not set"; ?>
                        </td>

                        <td>
                            <span class="status-badge status-<?php echo strtolower(htmlspecialchars($application["status"])); ?>">
                                <?php echo htmlspecialchars($application["status"]); ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            $deadline = $application["deadline"];
                            $today = date("Y-m-d");

                            if (empty($deadline) || $deadline == "0000-00-00") {
                                echo '<span class="deadline-badge no-deadline">No deadline</span>';
                            } elseif ($deadline < $today) {
                                echo '<span class="deadline-badge overdue">Overdue</span>';
                            } elseif ($deadline == $today) {
                                echo '<span class="deadline-badge due-today">Due today</span>';
                            } else {
                                echo '<span class="deadline-badge upcoming">Upcoming</span>';
                            }
                            ?>
                        </td>

                        <td class="action-links">
                            <a class="edit-btn" href="edit.php?id=<?php echo (int)$application['id']; ?>">Edit</a>

                            <form action="delete.php" method="POST" style="display: inline;" 
                             onsubmit="return confirm('Are you sure you want to delete this application?');">
                             <input type="hidden" name="id" value="<?php echo (int)$application['id']; ?>">
                             <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>

                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

<?php else: ?>

    <p class="empty-message">You haven't added any applications yet.</p>

<?php endif; ?>

<br>

    <a href="../dashboard.php" class="back-dashboard">← Back to Dashboard</a>

</body>
</html>