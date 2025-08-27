<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='admin') { header('Location: ../index.php'); exit; }
    // Fetch books and issues
    $books = $pdo->query('SELECT * FROM books ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
    $issues = $pdo->query('SELECT i.*, u.username, b.title FROM issues i JOIN users u ON i.user_id=u.id JOIN books b ON i.book_id=b.id ORDER BY i.issue_date DESC')->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="../assets/styles.css"></head><body>
    <div class="container">
      <div class="topbar"><h2>Admin Dashboard</h2><div><a href="../index.php">Home</a> | <a href="../logout.php">Logout</a></div></div>
      <h3>Manage Books</h3>
      <p><a href="book_add.php" class="btn-small">Add New Book</a></p>
      <table><tr><th>ID</th><th>Title</th><th>Author</th><th>Total</th><th>Available</th><th>Actions</th></tr>
      <?php foreach($books as $b): ?>
        <tr>
          <td><?=$b['id']?></td>
          <td><?=htmlspecialchars($b['title'])?></td>
          <td><?=htmlspecialchars($b['author'])?></td>
          <td><?=$b['total_qty']?></td>
          <td><?=$b['available_qty']?></td>
          <td><a href="book_edit.php?id=<?=$b['id']?>">Edit</a> | <a href="book_delete.php?id=<?=$b['id']?>" onclick="return confirm('Delete?')">Delete</a></td>
        </tr>
      <?php endforeach; ?>
      </table>

      <h3>Issued Books</h3>
      <table><tr><th>ID</th><th>User</th><th>Book</th><th>Issue Date</th><th>Return Date</th><th>Returned</th></tr>
      <?php foreach($issues as $it): ?>
        <tr>
          <td><?=$it['id']?></td>
          <td><?=htmlspecialchars($it['username'])?></td>
          <td><?=htmlspecialchars($it['title'])?></td>
          <td><?=$it['issue_date']?></td>
          <td><?=$it['return_date']?></td>
          <td><?= $it['returned_date']? $it['returned_date'] : 'No' ?></td>
        </tr>
      <?php endforeach; ?>
      </table>
    </div></body></html>