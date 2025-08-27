<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='user') { header('Location: ../index.php'); exit; }
    $uid = $_SESSION['user_id'];
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $book_id = intval($_POST['book_id']);
      // fetch book
      $b = $pdo->prepare('SELECT * FROM books WHERE id=:id'); $b->execute(['id'=>$book_id]); $book=$b->fetch(PDO::FETCH_ASSOC);
      if(!$book) { header('Location: dashboard.php'); exit; }
      if($book['available_qty']<=0) { $_SESSION['msg']='Unavailable'; header('Location: dashboard.php'); exit; }
      // check user's current active issues
      $active = $pdo->prepare('SELECT COUNT(*) FROM issues WHERE user_id=:u AND returned_date IS NULL');
      $active->execute(['u'=>$uid]); $activeCount=$active->fetchColumn();
      $total_books = $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
      $max_allowed = max(1, ceil($total_books * 0.6));
      // check VIP
      $u = $pdo->prepare('SELECT vip_until FROM users WHERE id=:id'); $u->execute(['id'=>$uid]); $usr=$u->fetch(PDO::FETCH_ASSOC);
      $is_vip = ($usr['vip_until'] && $usr['vip_until'] >= date('Y-m-d'));
      if(!$is_vip && $activeCount >= $max_allowed) { $_SESSION['msg']='You reached your issue limit'; header('Location: dashboard.php'); exit; }
      // issue
      $issue_date = date('Y-m-d');
      $return_date = date('Y-m-d', strtotime($issue_date.($is_vip? ' +14 days':' +7 days')));
      $ins = $pdo->prepare('INSERT INTO issues (user_id,book_id,issue_date,return_date) VALUES (:u,:b,:idate,:rdate)');
      $ins->execute(['u'=>$uid,'b'=>$book_id,'idate'=>$issue_date,'rdate'=>$return_date]);
      // decrement available
      $upd = $pdo->prepare('UPDATE books SET available_qty = available_qty - 1 WHERE id=:id');
      $upd->execute(['id'=>$book_id]);
      $_SESSION['msg']='Book issued';
    }
    header('Location: dashboard.php');