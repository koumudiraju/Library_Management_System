<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

include "../config/db.php";

// Mark notification as read when clicked
if (isset($_GET['id'])) {
    $notification_id = $_GET['id'];
    $sql_update_notification = "UPDATE notifications SET is_read = TRUE WHERE id = $notification_id";
    $conn->query($sql_update_notification);
}

// Handle notification deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete_notification = "DELETE FROM notifications WHERE id = $delete_id AND user_id = {$_SESSION['user_id']}";

    if ($conn->query($sql_delete_notification) === TRUE) {
        echo "<script>alert('Notification deleted successfully!'); window.location='notifications.php';</script>";
    } else {
        echo "Error deleting notification: " . $conn->error;
    }
}

// Fetch all notifications for the logged-in student
$sql_notifications = "SELECT * FROM notifications WHERE user_id = {$_SESSION['user_id']} ORDER BY created_at DESC";
$notifications_result = $conn->query($sql_notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/student_style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="view_history.php">History</a></li>
                <li><a href="update_profile.php">Profile</a></li>
                <li><a href="../logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Notifications Section -->
    <div class="notifications-container">
        <h2>📢 Notifications</h2>
        <ul class="notifications-list">
            <?php if ($notifications_result->num_rows > 0) {
                while ($notification = $notifications_result->fetch_assoc()) { ?>
                    <li class="<?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                        <a href="notifications.php?id=<?php echo $notification['id']; ?>">
                            <i class="fas fa-bell"></i>
                            <?php echo $notification['message']; ?>
                        </a>
                        <span class="timestamp"><?php echo date("F j, Y, g:i a", strtotime($notification['created_at'])); ?></span>

                        <!-- Show delete option only if the notification is read -->
                        <?php if ($notification['is_read']) { ?>
                            <a href="notifications.php?delete_id=<?php echo $notification['id']; ?>"
                               class="delete-btn"
                               onclick="return confirm('Are you sure you want to delete this notification?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php } ?>
                    </li>
                <?php }
            } else { ?>
                <p class="no-notifications">No new notifications</p>
            <?php } ?>
        </ul>
    </div>

</body>
</html>