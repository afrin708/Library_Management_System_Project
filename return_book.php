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

$issues = $conn->query("SELECT bi.IssueID, b.Title, u.Name, bi.DueDate
                        FROM bookissues bi
                        JOIN books b ON bi.BookID = b.BookID
                        JOIN users u ON bi.UserID = u.UserID
                        WHERE bi.Status = 'Issued'");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $issue_id = $_POST['issue_id'];
    $return_date = date('Y-m-d');
    $due_date = $conn->query("SELECT DueDate FROM bookissues WHERE IssueID = $issue_id")->fetch_assoc()['DueDate'];
    $fine = 0;
    if (strtotime($return_date) > strtotime($due_date)) {
        $days_late = (strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24);
        $fine = $days_late * 1.00; // $1 per day late
    }

    $sql_return = "INSERT INTO bookreturns (IssueID, ReturnDate, Fine) VALUES ($issue_id, '$return_date', $fine)";
    $sql_fine = $fine > 0 ? "INSERT INTO fines (ReturnID, Amount) VALUES (LAST_INSERT_ID(), $fine)" : "";
    $sql_update_issue = "UPDATE bookissues SET Status = 'Returned' WHERE IssueID = $issue_id";
    $sql_update_book = "UPDATE books SET Quantity = Quantity + 1 WHERE BookID = (SELECT BookID FROM bookissues WHERE IssueID = $issue_id)";

    $conn->begin_transaction();
    try {
        $conn->query($sql_return);
        if ($fine > 0) $conn->query($sql_fine);
        $conn->query($sql_update_issue);
        $conn->query($sql_update_book);
        $conn->commit();
        echo "<p class='success'>Book returned successfully! Fine: $$fine</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Book - Library Management System</title>
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
        <h2>Return Book</h2>
        <div class="form-container">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label>Issued Book:</label>
                <select name="issue_id" required>
                    <option value="">Select Issued Book</option>
                    <?php while($row = $issues->fetch_assoc()) {
                        echo "<option value='" . $row['IssueID'] . "'>" . $row['Title'] . " (User: " . $row['Name'] . ", Due: " . $row['DueDate'] . ")</option>";
                    } ?>
                </select>
                <button type="submit">Return Book</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>