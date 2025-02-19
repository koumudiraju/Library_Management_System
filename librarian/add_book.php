<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Handle book submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST["title"]);
    $author = $conn->real_escape_string($_POST["author"]);
    $publication = $conn->real_escape_string($_POST["publication"]);
    $category = $conn->real_escape_string($_POST["category"]);
    $stock = intval($_POST["stock"]);

    $sql = "INSERT INTO books (title, author, publication, category, stock) 
            VALUES ('$title', '$author', '$publication', '$category', '$stock')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Book Added Successfully!'); window.location='add_book.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
</head>
<body>

    <!-- Navbar -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_books.php">Manage Books</a></li>
                <li><a href="search_students.php">Search Students</a></li>
                <li><a href="profile.php">Update Profile</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Add Book Form -->
    <div class="profile-container">
        <h2>📚 Add a New Book</h2>
        <form method="POST" action="">
            <label>Book Title:</label>
            <input type="text" name="title" required>

            <label>Author Name:</label>
            <input type="text" name="author" required>

            <label>Publication Name:</label>
            <input type="text" name="publication">

            <label>Category:</label>
            <input type="text" name="category" required>

            <label>Stock:</label>
            <input type="number" name="stock" min="1" required>

            <button type="submit">Add Book</button>
        </form>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("booksDropdown").classList.toggle("show");
        }
    </script>

</body>
</html>