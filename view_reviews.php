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

$books = $conn->query("SELECT BookID, Title FROM books");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $sql = "INSERT INTO reviews (UserID, BookID, Rating, Comment)
            VALUES ({$_SESSION['user_id']}, $book_id, $rating, '$comment')";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Review submitted successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}

$sql = "SELECT r.ReviewID, b.Title, u.Name, r.Rating, r.Comment
        FROM reviews r
        JOIN books b ON r.BookID = b.BookID
        JOIN users u ON r.UserID = u.UserID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews - Library Management System</title>
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
        <h2>Book Reviews</h2>
        <div class="form-container">
            <h3>Submit a Review</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Book:</label>
                <select name="book_id" required>
                    <option value="">Select Book</option>
                    <?php while($row = $books->fetch_assoc()) {
                        echo "<option value='" . $row['BookID'] . "'>" . $row['Title'] . "</option>";
                    } ?>
                </select>
                <label>Rating (1-5):</label>
                <input type="number" name="rating" min="1" max="5" required>
                <label>Comment:</label>
                <textarea name="comment"></textarea>
                <button type="submit">Submit Review</button>
            </form>
        </div>
        <h3>Existing Reviews</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Book Title</th>
                <th>User</th>
                <th>Rating</th>
                <th>Comment</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["ReviewID"] . "</td>
                            <td>" . $row["Title"] . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["Rating"] . "</td>
                            <td>" . $row["Comment"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No reviews found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>