<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user details
$sql = "SELECT first_name, last_name, email FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST["first_name"]);
    $last_name = $conn->real_escape_string($_POST["last_name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = !empty($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : null;

    // Update query
    $sql_update = "UPDATE users SET 
        first_name = '$first_name',
        last_name = '$last_name',
        email = '$email' " . 
        ($password ? ", password = '$password'" : "") . 
        " WHERE id = $user_id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Profile updated successfully!'); window.location='update_profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../assets/student_style.css">
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="notifications.php">Notifications</a></li>
                <li><a href="view_history.php">History</a></li>
                <li><a href="update_profile.php" class="active">Profile</a></li>
                <li><a href="../logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Profile Update Section -->
    <div class="profile-container">
        <h2>📝 Update Your Profile</h2>
        <form method="POST" action="">
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>New Password (leave blank to keep current):</label>
            <input type="password" name="password">

            <button type="submit">Update Profile</button>
        </form>
    </div>

</body>
</html>