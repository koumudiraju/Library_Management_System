<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Fetch available books
$sql_books = "SELECT * FROM books WHERE stock > 0";
$books_result = $conn->query($sql_books);

// Fetch students
$sql_students = "SELECT * FROM users WHERE role='student' AND status='approved'";
$students_result = $conn->query($sql_students);

// Handle book issue
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST["book_id"];
    $student_id = $_POST["student_id"];
    $return_date = $_POST["return_date"]; // Get return date input

    // Check if the book is available
    $check_book = "SELECT stock FROM books WHERE id=$book_id";
    $book_check_result = $conn->query($check_book);
    $book_data = $book_check_result->fetch_assoc();

    if ($book_data['stock'] > 0) {
        // Insert a new transaction with return date
        $sql_issue = "INSERT INTO book_transactions (book_id, student_id, issue_date, return_date, status) 
                      VALUES ('$book_id', '$student_id', NOW(), '$return_date', 'issued')";

        if ($conn->query($sql_issue) === TRUE) {
            // Reduce the book stock
            $sql_update_stock = "UPDATE books SET stock = stock - 1 WHERE id=$book_id";
            $conn->query($sql_update_stock);

            // Insert a notification for the student with return date
            $notification_message = "Your book (ID: $book_id) has been issued successfully! Please return it by $return_date.";
            $insert_notification = "INSERT INTO notifications (user_id, message) VALUES ($student_id, '$notification_message')";

            if ($conn->query($insert_notification) === TRUE) {
                echo "<script>alert('Book Issued Successfully! Notification sent to student.'); window.location='issue_book.php';</script>";
            } else {
                echo "Error sending notification: " . $conn->error;
            }
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "<script>alert('Sorry, the selected book is out of stock!'); window.location='issue_book.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
    <link rel="stylesheet" href="../assets/issue_book_style.css">
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

    <!-- Issue Book Form -->
    <div class="issue-container">
        <h2>📘 Issue Book to Student</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>Select Book:</label>
                <select name="book_id" required>
                    <option value="">-- Select a Book --</option>
                    <?php while ($book = $books_result->fetch_assoc()) { ?>
                        <option value="<?php echo $book['id']; ?>"><?php echo $book['title']; ?> (Stock: <?php echo $book['stock']; ?>)</option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Select Student:</label>
                <select name="student_id" required>
                    <option value="">-- Select a Student --</option>
                    <?php while ($student = $students_result->fetch_assoc()) { ?>
                        <option value="<?php echo $student['id']; ?>"><?php echo $student['first_name'] . " " . $student['last_name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Return Date:</label>
                <input type="date" name="return_date" required>
            </div>

            <button type="submit">Issue Book</button>
        </form>
    </div>

    <script src="../assets/librarian_script.js"></script>

</body>
</html>