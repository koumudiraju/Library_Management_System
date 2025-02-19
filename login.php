<?php
session_start();
include "config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password with password_verify()
        if (password_verify($_POST["password"], $user["password"])) {
            if ($user['status'] == "pending") {
                echo "<script>alert('Account not approved yet!');</script>";
            } else {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["first_name"] = $user["first_name"];
                $_SESSION["last_name"] = $user["last_name"];
                $_SESSION["role"] = $user["role"];

                // Redirect based on role
                if ($user["role"] == "librarian") {
                    header("Location: librarian/dashboard.php");
                } else {
                    header("Location: students/dashboard.php");
                }
                exit();
            }
        } else {
            echo "<script>alert('Invalid email or password!');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/home_style.css">
</head>
<body>

    <!-- Navbar (Same as Home) -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Login Form -->
    <div class="profile-container">
        <h2>🔑 Login to Your Account</h2>
        <form action="" method="POST">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>

        <p class="register-link"><a href="forgot_password.php">Forgot password?</a></p>
        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</body>
</html>