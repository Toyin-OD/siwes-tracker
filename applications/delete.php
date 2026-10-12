<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

include "../includes/database.php";

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: view.php");
    exit;
}

$application_id = filter_var($_POST["id"], FILTER_VALIDATE_INT);

if (!$application_id || $application_id < 1) {
    header("Location: view.php");
    exit;
}

$sql = "DELETE FROM applications WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ii", $application_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: view.php");
    exit;
} else {
    mysqli_stmt_close($stmt);
    echo "Something went wrong. Please try again.";
}

?>