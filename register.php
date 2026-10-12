<?php

include "includes/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {

        $message = "An account with this email already exists.";

    } else {

        // Securely hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {

            $message = "Account created successfully!";

        } else {

            $message = "Something went wrong. Please try again.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SIWES Tracker</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="auth-page">

    <div class="auth-container">

        <div class="auth-icon">✨</div>

        <h1>Create Your Account</h1>
        <p class="auth-subtitle">
            Start organising your SIWES applications in one place.
        </p>

        <?php if ($message != ""): ?>
            <div class="<?php echo strpos($message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="auth-form">

            <div class="form-group">
                <label for="name">👤 Full Name</label>
                <input type="text" id="name" name="name"
                       placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">📧 Email Address</label>
                <input type="email" id="email" name="email"
                       placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password"
                       placeholder="Create a password" required>
            </div>

            <button type="submit">Create Account →</button>

        </form>

        <p class="auth-switch">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>

    </div>

</body>
</html>