<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] == 'Staff') {
    $staff_check = $conn->query("SELECT ApprovalStatus FROM staff WHERE UserID = {$_SESSION['user_id']}");
    if ($staff_check->num_rows == 0 || $staff_check->fetch_assoc()['ApprovalStatus'] != 'Approved') {
        header("Location: login.php");
        exit();
    }
}

$sql = "SELECT b.BookID, b.Title, b.Author, bc.CategoryName, b.Price, b.Status, b.Quantity, p.Name AS Publisher
        FROM books b
        LEFT JOIN bookcategories bc ON b.CategoryID = bc.CategoryID
        LEFT JOIN publishers p ON b.PublisherID = p.PublisherID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Management System</title>
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
        <h2>Books List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Publisher</th>
                <th>Action</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["BookID"] . "</td>
                            <td>" . $row["Title"] . "</td>
                            <td>" . $row["Author"] . "</td>
                            <td>" . $row["CategoryName"] . "</td>
                            <td>" . $row["Price"] . "</td>
                            <td>" . $row["Status"] . "</td>
                            <td>" . $row["Quantity"] . "</td>
                            <td>" . $row["Publisher"] . "</td>
                            <td>";
                    if ($row["Status"] == 'Paid') {
                        $check_access = $conn->query("SELECT * FROM paidbooks WHERE UserID = {$_SESSION['user_id']} AND BookID = {$row['BookID']} AND EndDate >= CURDATE()");
                        if ($check_access->num_rows > 0) {
                            echo "Accessible";
                        } else {
                            echo "<a href='pay_book.php?book_id={$row['BookID']}'>Pay to Access</a>";
                        }
                    } else {
                        echo "Free";
                    }
                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No books found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>