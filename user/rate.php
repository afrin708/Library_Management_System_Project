<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='user') { header('Location: ../index.php'); exit; }
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $uid = $_SESSION['user_id'];
      $book_id = intval($_POST['book_id']);
      $rating = max(1,min(5,intval($_POST['rating'])));
      $stmt = $pdo->prepare('INSERT INTO ratings (user_id,book_id,rating) VALUES (:u,:b,:r)');
      $stmt->execute(['u'=>$uid,'b'=>$book_id,'r'=>$rating]);
    }
    header('Location: dashboard.php');