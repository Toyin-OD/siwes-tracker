<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $company_name = $_POST["company_name"];
    $position = $_POST["position"];
    $location = $_POST["location"];
    $date_applied = $_POST["date_applied"];
    $deadline = $_POST["deadline"];
    $status = $_POST["status"];
    $contact_person = $_POST["contact_person"];
    $application_link = $_POST["application_link"];
    $notes = $_POST["notes"];

    $sql = "INSERT INTO applications 
            (user_id, company_name, position, location, date_applied, deadline, status, contact_person, application_link, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isssssssss",
        $user_id,
        $company_name,
        $position,
        $location,
        $date_applied,
        $deadline,
        $status,
        $contact_person,
        $application_link,
        $notes
    );

    if (mysqli_stmt_execute($stmt)) {

        $message = "Application saved successfully!";

    } else {

        $message = "Something went wrong. Please try again.";

    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Application | SIWES Tracker</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php if ($message != ""): ?>
    <p><?php echo $message; ?></p>
    <?php endif; ?>

  <form action="" method="POST" class="application-form">

    <div class="form-group">
       <h2>📝 Add New Application</h2>
     <label for="company_name">🏢 Company Name</label>
     <input type="text" id="company_name" name="company_name" required>
    </div>

    <div class="form-group">
        <label for="position">💼 Position</label>
        <input type="text" id="position" name="position" required>
    </div>

    <div class="form-group">
        <label for="location">📍Location</label>
        <input type="text" id="location" name="location">
    </div>

    <div class="form-group">
        <label for="date_applied">📅Date Applied</label>
        <input type="date" id="date_applied" name="date_applied">
    </div>

    <div class="form-group">
        <label for="deadline">Application Deadline</label>
        <input type="date" id="deadline" name="deadline">
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Saved">Saved</option>
            <option value="Applied">Applied</option>
            <option value="Interview">Interview</option>
            <option value="Accepted">Accepted</option>
            <option value="Rejected">Rejected</option>
        </select>
    </div>

    <div class="form-group">
        <label for="contact_person">Contact Person</label>
        <input type="text" id="contact_person" name="contact_person">
    </div>

    <div class="form-group">
        <label for="application_link">Application Link</label>
        <input type="url" id="application_link" name="application_link">
    </div>

    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes" rows="5"></textarea>
    </div>

    <button type="submit">Save Application</button>

  </form>
    <br>

    <a href="../dashboard.php">Back to Dashboard</a>

</body>
</html>