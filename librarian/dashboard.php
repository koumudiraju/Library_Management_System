<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Preserve search term in session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $_SESSION["search_query"] = $_POST["search"];
}

// Retrieve stored search query
$search_query = isset($_SESSION["search_query"]) ? $_SESSION["search_query"] : "";

$search_results = [];
if (!empty($search_query)) {
    $sql_search = "SELECT * FROM books 
                   WHERE title LIKE ? 
                   OR author LIKE ? 
                   OR category LIKE ?";
    
    $stmt = $conn->prepare($sql_search);
    $search_param = "%$search_query%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
    $stmt->close();
}

// Handle book deletion
if (isset($_GET["delete_id"])) {
    $delete_id = $_GET["delete_id"];
    $sql_delete = "DELETE FROM books WHERE id=$delete_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
    <script src="../assets/librarian_script.js"></script>
    <script>
        function toggleDropdown() {
            document.getElementById("booksDropdown").classList.toggle("show");
        }

        // Prevent form resubmission on back navigation
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</head>
<body>

    <!-- Navbar Section -->
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
            <h1>Search for Books</h1>
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Enter book title, author, or category..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <!-- Display Search Results -->
    <?php if (!empty($search_results)) { ?>
        <div class="search-results">
            <h3>Search Results:</h3>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publication</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($search_results as $book) { ?>
                    <tr>
                        <td><?php echo $book['title']; ?></td>
                        <td><?php echo $book['author']; ?></td>
                        <td><?php echo $book['publication']; ?></td>
                        <td><?php echo $book['category']; ?></td>
                        <td><?php echo $book['stock']; ?></td>
                        <td class="action-buttons">
                            <a href="issue_book.php?book_id=<?php echo $book['id']; ?>" class="action-btn issue">
                            <span>📘</span> Issue</a> 
                            <a href="return_book.php?book_id=<?php echo $book['id']; ?>" class="action-btn return">
                            <span>🔄</span> Return</a> 
                            <a href="dashboard.php?delete_id=<?php echo $book['id']; ?>" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this book?')">
                            <span>🗑</span> Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>

</body>
</html>