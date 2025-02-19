<?php
session_start();
include "config/db.php"; 

$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search_query = $conn->real_escape_string($_POST["search"]);
    $sql_search = "SELECT * FROM books 
                   WHERE title LIKE '%$search_query%' 
                   OR author LIKE '%$search_query%' 
                   OR category LIKE '%$search_query%'";
    $result = $conn->query($sql_search);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Stark Library</title>
    <link rel="stylesheet" href="assets/home_style.css">
</head>
<body>
    
    <div class="container">
    <!-- Header Section -->
    <header>
        <div class="logo">The Stark Library</div>
        <nav>
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="login.php">Login/Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Search for Books</h1>
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Enter book title, author, or category..." required>
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
                </tr>
                <?php foreach ($search_results as $book) { ?>
                    <tr>
                        <td><?php echo $book['title']; ?></td>
                        <td><?php echo $book['author']; ?></td>
                        <td><?php echo $book['publication']; ?></td>
                        <td><?php echo $book['category']; ?></td>
                        <td><?php echo $book['stock']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>
    </div>
    <!-- Footer -->
    <footer>
        <p>&copy; 2025 The Stark Library | 📍 123 Library St, Stark City | 📧 support@starklibrary.com</p>
    </footer>

</body>
</html>