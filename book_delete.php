<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='admin') { header('Location: ../index.php'); exit; }
    $id = intval($_GET['id'] ?? 0);
    if($id) {
      $stmt = $pdo->prepare('DELETE FROM books WHERE id=:id');
      $stmt->execute(['id'=>$id]);
    }
    header('Location: dashboard.php');