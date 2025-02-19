<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Fetch all issued books
$sql_transactions = "SELECT bt.transaction_id, b.title, u.first_name, u.last_name, bt.issue_date 
                     FROM book_transactions bt
                     JOIN books b ON bt.book_id = b.id
                     JOIN users u ON bt.student_id = u.id
                     WHERE bt.status = 'issued'";
$transactions_result = $conn->query($sql_transactions);

// Handle book return
if (isset($_GET['return_id'])) {
    $transaction_id = $_GET['return_id'];
    $sql_return = "UPDATE book_transactions SET return_date = NOW(), status = 'returned' WHERE transaction_id = $transaction_id";

    if ($conn->query($sql_return) === TRUE) {
        // Update book stock
        $sql_update_stock = "UPDATE books SET stock = stock + 1 WHERE id = (SELECT book_id FROM book_transactions WHERE transaction_id = $transaction_id)";
        $conn->query($sql_update_stock);

        // Insert a notification for the student
        $student_id_query = "SELECT student_id FROM book_transactions WHERE transaction_id = $transaction_id";
        $student_result = $conn->query($student_id_query);
        $student_data = $student_result->fetch_assoc();
        $student_id = $student_data['student_id'];

        $notification_message = "Your book (ID: $transaction_id) has been successfully returned!";
        $insert_notification = "INSERT INTO notifications (user_id, message) VALUES ($student_id, '$notification_message')";
        if ($conn->query($insert_notification) === TRUE) {
            echo "<script>alert('Book Returned Successfully! Notification sent to student.'); window.location='return_book.php';</script>";
        } else {
            echo "Error sending notification: " . $conn->error;
        }

    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="../assets/student_style.css">
    <link rel="stylesheet" href="../assets/librarian_style.css">
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="approve_students.php">Approve Students</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Books Related ˅</a>
                    <ul class="dropdown-menu" id="booksDropdown">
                        <li><a href="add_book.php">Add New Book</a></li>
                        <li><a href="manage_books.php">Manage Books</a></li>
                    </ul>
                </li>
                <li><a href="search_students.php">Search Students</a></li>
                <li><a href="profile.php">Update Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Return Book Table -->
    <div class="container">
        <h2>🔄 Return Book</h2>
        <table>
            <tr>
                <th>Transaction ID</th>
                <th>Book Title</th>
                <th>Student</th>
                <th>Issue Date</th>
                <th>Return Book</th>
            </tr>
            <?php while ($transaction = $transactions_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $transaction['transaction_id']; ?></td>
                    <td><?php echo $transaction['title']; ?></td>
                    <td><?php echo $transaction['first_name'] . " " . $transaction['last_name']; ?></td>
                    <td><?php echo $transaction['issue_date']; ?></td>
                    <td>
                        <a href="return_book.php?return_id=<?php echo $transaction['transaction_id']; ?>" 
                           class="return-btn" 
                           onclick="return confirm('Are you sure you want to return this book?')">
                           🔄 Return
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script src="../assets/librarian_script.js"></script>
</body>
</html>