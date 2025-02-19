<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "librarian") {
    header("Location: ../login.php");
    exit();
}

// Approve student action
if (isset($_GET["approve_id"])) {
    $approve_id = $_GET["approve_id"];
    $sql = "UPDATE users SET status='approved' WHERE id=$approve_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Student Approved!'); window.location='approve_students.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch students who are pending approval
$sql = "SELECT * FROM users WHERE role='student' AND status='pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Students</title>
    <link rel="stylesheet" href="../assets/librarian_style.css">
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

    <!-- Approve Students Section -->
    <div class="content-container">
        <h2>📋 Approve Students</h2>

        <?php if ($result->num_rows > 0) { ?>
            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td>
                                <a href="approve_students.php?approve_id=<?php echo $row['id']; ?>" 
                                   class="action-btn approve"
                                   onclick="return confirm('Approve this student?')">
                                    ✅ Approve
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } else { ?>
            <p class="no-data">No students waiting for approval.</p>
        <?php } ?>
    </div>

    <script src="../assets/librarian_script.js"></script>
</body>
</html>