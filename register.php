<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = "student"; // Default role
    $status = "pending"; // Awaiting librarian approval

    // Check if email exists
    $check_email = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        // Insert user into the database
        $sql = "INSERT INTO users (first_name, last_name, email, password, role, status) 
                VALUES ('$first_name', '$last_name', '$email', '$password', '$role', '$status')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registered successfully! Wait for admin approval.'); window.location='login.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
                <li><a href="events.php">Events</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <!-- Register Form -->
    <div class="profile-container">
        <h2>📝 Create a New Account</h2>
        <form action="" method="POST">
            <label>First Name:</label>
            <input type="text" name="first_name" placeholder="Enter your first name" required>

            <label>Last Name:</label>
            <input type="text" name="last_name" placeholder="Enter your last name" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Register</button>
        </form>

        <p class="register-link">Already have an account? <a href="login.php">Login here</a></p>
    </div>

</body>
</html>