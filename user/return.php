<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='user') { header('Location: ../index.php'); exit; }
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $issue_id = intval($_POST['issue_id']);
      // set returned_date
      $upd = $pdo->prepare('UPDATE issues SET returned_date = :rd WHERE id=:id');
      $rd = date('Y-m-d');
      $upd->execute(['rd'=>$rd,'id'=>$issue_id]);
      // increment book available
      $bid = $pdo->prepare('SELECT book_id FROM issues WHERE id=:id'); $bid->execute(['id'=>$issue_id]); $b=$bid->fetch(PDO::FETCH_ASSOC);
      if($b) {
        $u = $pdo->prepare('UPDATE books SET available_qty = available_qty + 1 WHERE id=:id');
        $u->execute(['id'=>$b['book_id']]);
      }
    }
    header('Location: dashboard.php');