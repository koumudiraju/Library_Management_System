<?php
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $new_password = password_hash($_POST["new_password"], PASSWORD_DEFAULT); // Secure hashing

    // Check if email exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Update password in the database
        $sql_update = "UPDATE users SET password='$new_password' WHERE email='$email'";
        if ($conn->query($sql_update) === TRUE) {
            echo "<script>alert('Password successfully updated!'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/home_style.css">
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Forgot Password Form -->
    <div class="profile-container">
        <h2>🔒 Reset Your Password</h2>
        <form action="" method="POST">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>New Password:</label>
            <input type="password" name="new_password" placeholder="Enter your new password" required>

            <button type="submit">Reset Password</button>
        </form>

        <p class="register-link">Remembered your password? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>