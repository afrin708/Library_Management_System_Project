<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='admin') { header('Location: ../index.php'); exit; }
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id=:id');
    $stmt->execute(['id'=>$id]);
    $b = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$b) { echo 'Not found'; exit; }
    $msg='';
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $title = trim($_POST['title']);
      $author = trim($_POST['author']);
      $total = max(1,intval($_POST['total']));
      // adjust available_qty relative to total change
      $available = intval($_POST['available']);
      $upd = $pdo->prepare('UPDATE books SET title=:t,author=:a,total_qty=:tot,available_qty=:av WHERE id=:id');
      $upd->execute(['t'=>$title,'a'=>$author,'tot'=>$total,'av'=>$available,'id'=>$id]);
      $msg='Updated';
      // refresh
      $stmt->execute(['id'=>$id]);
      $b = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Edit Book</title><link rel="stylesheet" href="../assets/styles.css"></head><body>
    <div class="container"><h2>Edit Book</h2>
    <?php if($msg) echo '<div class="success">'.htmlspecialchars($msg).'</div>'; ?>
    <form method="post">
      <label>Title</label><input name="title" value="<?=htmlspecialchars($b['title'])?>" required>
      <label>Author</label><input name="author" value="<?=htmlspecialchars($b['author'])?>">
      <label>Total Quantity</label><input name="total" type="number" value="<?=$b['total_qty']?>" min="1">
      <label>Available Quantity</label><input name="available" type="number" value="<?=$b['available_qty']?>" min="0">
      <button type="submit">Save</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
    </div></body></html>