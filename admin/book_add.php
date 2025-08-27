<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='admin') { header('Location: ../index.php'); exit; }
    $msg='';
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $title = trim($_POST['title']);
      $author = trim($_POST['author']);
      $qty = max(1,intval($_POST['qty']));
      $stmt = $pdo->prepare('INSERT INTO books (title,author,total_qty,available_qty) VALUES (:t,:a,:tot,:av)');
      $stmt->execute(['t'=>$title,'a'=>$author,'tot'=>$qty,'av'=>$qty]);
      $msg='Book added';
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Add Book</title><link rel="stylesheet" href="../assets/styles.css"></head><body>
    <div class="container">
      <h2>Add Book</h2>
      <?php if($msg) echo '<div class="success">'.htmlspecialchars($msg).'</div>'; ?>
      <form method="post">
        <label>Title</label><input name="title" required>
        <label>Author</label><input name="author">
        <label>Quantity</label><input name="qty" type="number" value="1" min="1">
        <button type="submit">Add</button>
      </form>
      <p><a href="dashboard.php">Back</a></p>
    </div></body></html>