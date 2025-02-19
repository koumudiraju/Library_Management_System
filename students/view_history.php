<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "student") {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION["user_id"];
$transactions = [];

$sql_transactions = "SELECT bt.transaction_id, b.title, b.author, bt.issue_date, bt.return_date, bt.status 
                     FROM book_transactions bt
                     JOIN books b ON bt.book_id = b.id
                     WHERE bt.student_id = ?
                     ORDER BY bt.issue_date DESC";

$stmt = $conn->prepare($sql_transactions);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View History</title>
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
                <li><a href="view_history.php" class="active">History</a></li>
                <li><a href="update_profile.php">Profile</a></li>
                <li><a href="../logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- History Section -->
    <div class="container">
        <h2>📚 Your Book History</h2>
        <table class="history-table">
            <tr>
                <th>📖 Title</th>
                <th>✍️ Author</th>
                <th>📅 Issue Date</th>
                <th>📆 Return Date</th>
                <th>📌 Status</th>
            </tr>
            <?php if (!empty($transactions)) {
                foreach ($transactions as $row) { 
                    // Define status classes
                    $status_class = ($row['status'] == "returned") ? "returned" : (($row['status'] == "overdue") ? "overdue" : "borrowed");
                    ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo date("F j, Y", strtotime($row['issue_date'])); ?></td>
                        <td><?php echo ($row['return_date'] ? date("F j, Y", strtotime($row['return_date'])) : "Not Returned"); ?></td>
                        <td><span class="status <?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="5">No book history found.</td></tr>
            <?php } ?>
        </table>
    </div>

</body>
</html>