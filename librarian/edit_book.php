<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Check if book ID is provided
if (!isset($_GET["id"])) {
    header("Location: manage_books.php");
    exit();
}

$book_id = $_GET["id"];
$sql = "SELECT * FROM books WHERE id=$book_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: manage_books.php");
    exit();
}

$book = $result->fetch_assoc();

// Handle book update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $publication = $_POST["publication"];
    $category = $_POST["category"];
    $stock = $_POST["stock"];

    $update_sql = "UPDATE books SET title='$title', author='$author', publication='$publication', category='$category', stock='$stock' WHERE id=$book_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Book Updated Successfully!'); window.location='manage_books.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
    <script src="../assets/librarian_script.js"></script>
</head>
<body>
    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
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

    <!-- Edit Book Form -->
    <div class="profile-container">
        <h2>📝 Edit Book</h2>
        <form method="POST" action="">
            <label>Book Title:</label>
            <input type="text" name="title" value="<?php echo $book['title']; ?>" required>

            <label>Author Name:</label>
            <input type="text" name="author" value="<?php echo $book['author']; ?>" required>

            <label>Publication Name:</label>
            <input type="text" name="publication" value="<?php echo $book['publication']; ?>">

            <label>Category:</label>
            <input type="text" name="category" value="<?php echo $book['category']; ?>" required>

            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo $book['stock']; ?>" required>

            <button type="submit">Update Book</button>
        </form>
    </div>
</body>
</html>