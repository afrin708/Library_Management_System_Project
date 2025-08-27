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

$users = $conn->query("SELECT UserID, Name FROM users");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $membership_type = $_POST['membership_type'];
    $payment_status = $_POST['payment_status'];
    $sql = "INSERT INTO memberships (UserID, MembershipType, PaymentStatus)
            VALUES ($user_id, '$membership_type', '$payment_status')";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>Membership added successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}

$memberships = $conn->query("SELECT m.MembershipID, u.Name, m.MembershipType, m.PaymentStatus
                             FROM memberships m
                             JOIN users u ON m.UserID = u.UserID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Memberships - Library Management System</title>
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
        <h2>Manage Memberships</h2>
        <div class="form-container">
            <h3>Add Membership</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>User:</label>
                <select name="user_id" required>
                    <option value="">Select User</option>
                    <?php while($row = $users->fetch_assoc()) {
                        echo "<option value='" . $row['UserID'] . "'>" . $row['Name'] . "</option>";
                    } ?>
                </select>
                <label>Membership Type:</label>
                <input type="text" name="membership_type" required>
                <label>Payment Status:</label>
                <select name="payment_status" required>
                    <option value="Unpaid">Unpaid</option>
                    <option value="Paid">Paid</option>
                </select>
                <button type="submit">Add Membership</button>
            </form>
        </div>
        <h3>Existing Memberships</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Membership Type</th>
                <th>Payment Status</th>
            </tr>
            <?php
            if ($memberships->num_rows > 0) {
                while($row = $memberships->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["MembershipID"] . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["MembershipType"] . "</td>
                            <td>" . $row["PaymentStatus"] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No memberships found</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </main>
</body>
</html>