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

$books = $conn->query("SELECT BookID, Title FROM books WHERE Status = 'Paid'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_book'])) {
        $title = $_POST['title'];
        $author = $_POST['author'];
        $sql = "INSERT INTO bookrequests (UserID, BookTitle, Author, Status)
                VALUES ({$_SESSION['user_id']}, '$title', '$author', 'Pending')";
    } else {
        $book_id = $_POST['book_id'];
        $sql = "INSERT INTO requests (UserID, BookID, Status)
                VALUES ({$_SESSION['user_id']}, $book_id, 'Pending')";
    }
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Request submitted successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Book - Library Management System</title>
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
        <h2>Request Book</h2>
        <div class="form-container">
            <h3>Request Existing Paid Book</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Book:</label>
                <select name="book_id">
                    <option value="">Select Paid Book</option>
                    <?php while($row = $books->fetch_assoc()) {
                        echo "<option value='" . $row['BookID'] . "'>" . $row['Title'] . "</option>";
                    } ?>
                </select>
                <button type="submit">Request Access</button>
            </form>
            <h3>Request New Book</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Book Title:</label>
                <input type="text" name="title" required>
                <label>Author:</label>
                <input type="text" name="author">
                <input type="hidden" name="new_book" value="1">
                <button type="submit">Submit Request</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>