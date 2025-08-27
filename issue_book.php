<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Staff'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SESSION['role'] == 'Staff') {
    $staff_check = $conn->query("SELECT ApprovalStatus FROM staff WHERE UserID = {$_SESSION['user_id']}");
    if ($staff_check->num_rows == 0 || $staff_check->fetch_assoc()['ApprovalStatus'] != 'Approved') {
        header("Location: login.php");
        exit();
    }
}

$books = $conn->query("SELECT BookID, Title FROM books WHERE Quantity > 0");
$users = $conn->query("SELECT UserID, Name FROM users WHERE Role IN ('Teacher', 'Student', 'Guest')");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $user_id = $_POST['user_id'];
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days'));

    $sql = "INSERT INTO bookissues (BookID, UserID, IssueDate, DueDate, Status)
            VALUES ($book_id, $user_id, '$issue_date', '$due_date', 'Issued')";
    if ($conn->query($sql) === TRUE) {
        $conn->query("UPDATE books SET Quantity = Quantity - 1 WHERE BookID = $book_id");
        echo "<p class='success'>Book issued successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Book - Library Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
        <nav>
            <ul>
                <li><a href="index.php">Books</a></li>
                <li><a href="view_users.php">Users</a></li>
                <li><a href="add_book.php">Add Book</a></li>
                <li><a href="issue_book.php">Issue Book</a></li>
                <li><a href="return_book.php">Return Book</a></li>
                <li><a href="manage_vendors.php">Vendors</a></li>
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
        <h2>Issue Book</h2>
        <div class="form-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Book:</label>
                <select name="book_id" required>
                    <option value="">Select Book</option>
                    <?php while($row = $books->fetch_assoc()) {
                        echo "<option value='" . $row['BookID'] . "'>" . $row['Title'] . "</option>";
                    } ?>
                </select>
                <label>User:</label>
                <select name="user_id" required>
                    <option value="">Select User</option>
                    <?php while($row = $users->fetch_assoc()) {
                        echo "<option value='" . $row['UserID'] . "'>" . $row['Name'] . "</option>";
                    } ?>
                </select>
                <button type="submit">Issue Book</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>