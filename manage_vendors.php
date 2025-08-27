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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $sql = "INSERT INTO vendors (Name, Contact) VALUES ('$name', '$contact')";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Vendor added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}

$vendors = $conn->query("SELECT VendorID, Name, Contact FROM vendors");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendors - Library Management System</title>
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
        <h2>Manage Vendors</h2>
        <div class="form-container">
            <h3>Add Vendor</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Name:</label>
                <input type="text" name="name" required>
                <label>Contact:</label>
                <input type="text" name="contact">
                <button type="submit">Add Vendor</button>
            </form>
        </div>
        <h3>Existing Vendors</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
            </tr>
            <?php
            if ($vendors->num_rows > 0) {
                while($row = $vendors->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["VendorID"] . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["Contact"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No vendors found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>