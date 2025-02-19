<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Preserve search term in session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search_student"])) {
    $_SESSION["search_student_query"] = $_POST["search_student"];
    header("Location: search_students.php"); // Redirect to clear POST request
    exit();
}

// Retrieve stored search query
$search_query = isset($_SESSION["search_student_query"]) ? $_SESSION["search_student_query"] : "";

// Handle book return action
if (isset($_GET['return_id'])) {
    $transaction_id = $_GET['return_id'];

    // Fetch book ID from transaction
    $sql_get_book = "SELECT book_id FROM book_transactions WHERE transaction_id = $transaction_id";
    $result = $conn->query($sql_get_book);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];

        // Update transaction status
        $sql_return = "UPDATE book_transactions SET status = 'returned', return_date = NOW() WHERE transaction_id = $transaction_id";
        $conn->query($sql_return);

        // Increase book stock
        $sql_update_stock = "UPDATE books SET stock = stock + 1 WHERE id = $book_id";
        $conn->query($sql_update_stock);

        // Insert a notification for the student
        $student_id_query = "SELECT student_id FROM book_transactions WHERE transaction_id = $transaction_id";
        $student_result = $conn->query($student_id_query);
        if ($student_result->num_rows > 0) {
            $student_data = $student_result->fetch_assoc();
            $student_id = $student_data['student_id'];

            // Insert notification for the student
            $notification_message = "Your book (ID: $book_id) has been successfully returned!";
            $insert_notification = "INSERT INTO notifications (user_id, message) VALUES ($student_id, '$notification_message')";
            $conn->query($insert_notification);
        }

        // Redirect to avoid resubmission issue
        header("Location: search_students.php");
        exit();
    }
}

// Fetch students based on stored query
$students = [];
$transactions = [];
if (!empty($search_query)) {
    $sql_students = "SELECT * FROM users WHERE role='student' 
                     AND (id LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    
    $stmt = $conn->prepare($sql_students);
    $search_param = "%$search_query%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;

            // Fetch books issued by student using prepared statements
            $student_id = $row['id'];
            $sql_transactions = "SELECT bt.transaction_id, b.title, b.author, bt.issue_date, bt.return_date, bt.status 
                                 FROM book_transactions bt
                                 JOIN books b ON bt.book_id = b.id
                                 WHERE bt.student_id = ?
                                 ORDER BY bt.issue_date DESC";
            
            $stmt_trans = $conn->prepare($sql_transactions);
            $stmt_trans->bind_param("i", $student_id);
            $stmt_trans->execute();
            $transactions[$student_id] = $stmt_trans->get_result();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Students</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
    <script src="../assets/librarian_script.js"></script>
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="approve_students.php">Approve Students</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-btn">Books Related ˅</a>
                    <ul class="dropdown-menu">
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

    <!-- Main Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Search for Students</h1>
            <form method="POST" action="">
                <input type="text" name="search_student" placeholder="Enter Student ID, First Name, or Last Name" 
                       value="<?php echo htmlspecialchars($search_query); ?>" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <!-- Display Student Search Results -->
    <?php if (!empty($students)) { ?>
        <div class="search-results">
            <h3>Student Search Results:</h3>
            <?php foreach ($students as $student) { ?>
                <div class="student-card">
                    <p><strong>ID:</strong> <?php echo $student['id']; ?></p>
                    <p><strong>Name:</strong> <?php echo $student['first_name'] . " " . $student['last_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $student['email']; ?></p>
                    <p><a href="issue_book.php?student_id=<?php echo $student['id']; ?>" class="action-btn issue">📘 Issue Book</a></p>
                </div>

                <h4>Issued Books History</h4>
                <div class="student-history">
                    <table>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Issue Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                        if ($transactions[$student['id']]->num_rows > 0) {
                            while ($row = $transactions[$student['id']]->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><?php echo $row['author']; ?></td>
                                    <td><?php echo $row['issue_date']; ?></td>
                                    <td><?php echo ($row['return_date'] ? $row['return_date'] : "Not Returned"); ?></td>
                                    <td><?php echo ucfirst($row['status']); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'issued') { ?>
                                            <a href="search_students.php?return_id=<?php echo $row['transaction_id']; ?>" 
                                               class="action-btn return"
                                               onclick="return confirm('Are you sure you want to return this book?')">🔄 Return</a>
                                        <?php } else { ?>
                                            ✅ Returned
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } 
                        } else { ?>
                            <tr><td colspan="6">No books issued.</td></tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</body>
</html>