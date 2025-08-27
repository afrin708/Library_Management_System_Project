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

$sql = "SELECT UserID, Name, Role, Email, CreatedAt FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users - Library Management System</title>
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
        <h2>Users List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["UserID"] . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["Role"] . "</td>
                            <td>" . $row["Email"] . "</td>
                            <td>" . $row["CreatedAt"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>