<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Handle delete book action
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $sql = "DELETE FROM books WHERE id=$delete_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Book Deleted Successfully!'); window.location='manage_books.php';</script>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Fetch all books
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
    <script>
        function toggleDropdown() {
            document.getElementById("booksDropdown").classList.toggle("show");
        }
    </script>
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_book.php">Add New Book</a></li>
                <li><a href="search_students.php">Search Students</a></li>
                <li><a href="profile.php">Update Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Manage Books Section -->
    <div class="manage-books-container">
        <h2>📚 Manage Books</h2>
        <div class="manage-books-table">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publication</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
                <?php if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['title']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                            <td><?php echo $row['publication']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td><?php echo $row['stock']; ?></td>
                            <td class="action-buttons">
                                <a href="edit_book.php?id=<?php echo $row['id']; ?>" class="action-btn edit">
                                    ✏ Edit
                                </a>
                                <a href="manage_books.php?delete_id=<?php echo $row['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this book?')">
                                    🗑 Delete
                                </a>
                            </td>
                        </tr>
                    <?php } 
                } else { ?>
                    <tr><td colspan="7">No books available</td></tr>
                <?php } ?>
            </table>
        </div>
    </div>

</body>
</html>