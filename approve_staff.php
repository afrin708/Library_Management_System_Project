<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "librarydb";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$staff = $conn->query("SELECT s.StaffID, u.Name, u.Email, s.ApprovalStatus
                        FROM staff s
                        JOIN users u ON s.UserID = u.UserID
                        WHERE s.ApprovalStatus = 'Pending'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_id = $_POST['staff_id'];
    $sql = "UPDATE staff SET ApprovalStatus = 'Approved' WHERE StaffID = $staff_id";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Staff approved successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Staff - Library Management System</title>
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
                <li><a href="approve_staff.php">Approve Staff</a></li>
                <li><a href="manage_memberships.php">Memberships</a></li>
                <li><a href="view_reviews.php">Reviews</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Approve Staff</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            if ($staff->num_rows > 0) {
                while($row = $staff->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["StaffID"] . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["Email"] . "</td>
                            <td>" . $row["ApprovalStatus"] . "</td>
                            <td>
                                <form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>
                                    <input type='hidden' name='staff_id' value='" . $row["StaffID"] . "'>
                                    <button type='submit'>Approve</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No pending staff approvals</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>