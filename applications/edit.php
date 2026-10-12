<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/database.php";

$user_id = $_SESSION["user_id"];

if (!isset($_GET["id"])) {
    header("Location: view.php");
    exit;
}

$application_id = $_GET["id"];

$sql = "SELECT * FROM applications 
        WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $application_id, $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: view.php");
    exit;
}

$application = mysqli_fetch_assoc($result);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name = $_POST["company_name"];
    $position = $_POST["position"];
    $location = $_POST["location"];
    $date_applied = $_POST["date_applied"];
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];
    $contact_person = $_POST["contact_person"];
    $application_link = $_POST["application_link"];
    $notes = $_POST["notes"];

    $update_sql = "UPDATE applications SET
                    company_name = ?,
                    position = ?,
                    location = ?,
                    date_applied = ?,
                    deadline = ?,
                    status = ?,
                    contact_person = ?,
                    application_link = ?,
                    notes = ?
                   WHERE id = ? AND user_id = ?";

    $update_stmt = mysqli_prepare($conn, $update_sql);

    mysqli_stmt_bind_param(
        $update_stmt,
        "sssssssssii",
        $company_name,
        $position,
        $location,
        $date_applied,
        $deadline,
        $status,
        $contact_person,
        $application_link,
        $notes,
        $application_id,
        $user_id
    );

    if (mysqli_stmt_execute($update_stmt)) {

        header("Location: view.php");
        exit;

    } else {

        echo "Something went wrong. Please try again.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application | SIWES Tracker</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

   <h2>✏️ Edit Application</h2>
   <p>Update your application details below.</p>

 <form action="" method="POST" class="application-form">

    <div class="form-group">
        <label for="company_name">🏢 Company Name</label>
        <input type="text" id="company_name" name="company_name"
            value="<?php echo htmlspecialchars($application['company_name']); ?>"
            required>
    </div>

    <div class="form-group">
        <label for="position">💼 Position</label>
        <input type="text" id="position" name="position"
            value="<?php echo htmlspecialchars($application['position']); ?>"
            required>
    </div>

    <div class="form-group">
        <label for="location">📍 Location</label>
        <input type="text" id="location" name="location"
            value="<?php echo htmlspecialchars($application['location']); ?>">
    </div>

    <div class="form-group">
        <label for="date_applied">📅 Date Applied</label>
        <input type="date" id="date_applied" name="date_applied"
            value="<?php echo htmlspecialchars($application['date_applied'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="deadline">⏰ Application Deadline</label>
        <input type="date" id="deadline" name="deadline"
            value="<?php echo htmlspecialchars($application['deadline'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="status">📌 Application Status</label>
        <select id="status" name="status">
            <option value="Saved" <?php if ($application['status'] == 'Saved') echo 'selected'; ?>>Saved</option>
            <option value="Applied" <?php if ($application['status'] == 'Applied') echo 'selected'; ?>>Applied</option>
            <option value="Interview" <?php if ($application['status'] == 'Interview') echo 'selected'; ?>>Interview</option>
            <option value="Accepted" <?php if ($application['status'] == 'Accepted') echo 'selected'; ?>>Accepted</option>
            <option value="Rejected" <?php if ($application['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
        </select>
    </div>

    <div class="form-group">
        <label for="contact_person">👤 Contact Person</label>
        <input type="text" id="contact_person" name="contact_person"
            value="<?php echo htmlspecialchars($application['contact_person'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="application_link">🔗 Application Link</label>
        <input type="url" id="application_link" name="application_link"
            value="<?php echo htmlspecialchars($application['application_link'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="notes">📝 Notes</label>
        <textarea id="notes" name="notes" rows="5"><?php echo htmlspecialchars($application['notes'] ?? ''); ?></textarea>
    </div>

    <button type="submit">💾 Update Application</button>

</form>

    <br>

    <a href="view.php">Back to Applications</a>

</body>
</html>