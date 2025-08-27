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

$categories = $conn->query("SELECT CategoryID, CategoryName FROM bookcategories");
$publishers = $conn->query("SELECT PublisherID, Name FROM publishers");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $quantity = $_POST['quantity'];
    $publisher_id = $_POST['publisher_id'];

    $sql = "INSERT INTO books (Title, Author, CategoryID, Price, Status, Quantity, PublisherID)
            VALUES ('$title', '$author', $category_id, $price, '$status', $quantity, $publisher_id)";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Book added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Book - Library Management System</title>
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
        <h2>Add New Book</h2>
        <div class="form-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Title:</label>
                <input type="text" name="title" required>
                <label>Author:</label>
                <input type="text" name="author">
                <label>Category:</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while($row = $categories->fetch_assoc()) {
                        echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryName'] . "</option>";
                    } ?>
                </select>
                <label>Price:</label>
                <input type="number" step="0.01" name="price" required>
                <label>Status:</label>
                <select name="status" required>
                    <option value="Free">Free</option>
                    <option value="Paid">Paid</option>
                </select>
                <label>Quantity:</label>
                <input type="number" name="quantity" min="1" required>
                <label>Publisher:</label>
                <select name="publisher_id" required>
                    <option value="">Select Publisher</option>
                    <?php while($row = $publishers->fetch_assoc()) {
                        echo "<option value='" . $row['PublisherID'] . "'>" . $row['Name'] . "</option>";
                    } ?>
                </select>
                <button type="submit">Add Book</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>