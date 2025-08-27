<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$book_id = $_GET['book_id'];
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+7 days'));

$sql = "INSERT INTO paidbooks (UserID, BookID, StartDate, EndDate)
        VALUES ({$_SESSION['user_id']}, $book_id, '$start_date', '$end_date')";
if ($conn->query($sql) === TRUE) {
    echo "<p class='success'>Book access granted for 7 days!</p>";
    echo "<a href='index.php'>Back to Books</a>";
} else {
    echo "<p class='error'>Error: " . $conn->error . "</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay for Book - Library Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Books</a></li>
                <li><a href="view_users.php">Users</a></li>
                <?php if (in_array($_SESSION['role'], ['Admin', 'Staff'])): ?>
                    <li><a href="add_book.php">Add Book</a></li>
                    <li><a href="issue_book.php">Issue Book</a></li>
                    <li><a href="return_book.php">Return Book</a></li>
                    <li><a href="manage_vendors.php">Vendors</a></li>
                <?php endif; ?>
                <li><a href="request_book.php">Request Book</a></li>
                <?php if ($_SESSION['role'] == 'Admin'): ?>
                    <li><a href="approve_staff.php">Approve Staff</a></li>
                    <li><a href="manage_memberships.php">Memberships</a></li>
                <?php endif; ?>
                <li><a href="view_reviews.php">Reviews</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Pay for Book Access</h2>
    </main>
</body>
</html>