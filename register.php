<?php
    require 'config.php';
    $errors=[];
    $success='';
    if($_SERVER['REQUEST_METHOD']=='POST') {
      $username = trim($_POST['username']);
      $email = trim($_POST['email']);
      $phone = trim($_POST['phone']);
      $password = $_POST['password'];
      if(!$username) $errors[]='Username required';
      if(!$email || !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Valid email required';
      if(!$phone) $errors[]='Phone required';
      if(strlen($password)<6) $errors[]='Password must be at least 6 chars';
      // uniqueness checks
      $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username=:u OR email=:e OR phone=:p');
      $stmt->execute(['u'=>$username,'e'=>$email,'p'=>$phone]);
      $count = $stmt->fetchColumn();
      if($count>0) {
        // find which one
        $s = $pdo->prepare('SELECT username,email,phone FROM users WHERE username=:u OR email=:e OR phone=:p');
        $s->execute(['u'=>$username,'e'=>$email,'p'=>$phone]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if($row) {
          if($row['username']==$username) $errors[]='Username already used';
          if($row['email']==$email) $errors[]='Email already used';
          if($row['phone']==$phone) $errors[]='Phone already used';
        }
      }
      if(empty($errors)) {
        $hp = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (username,email,phone,password) VALUES (:u,:e,:p,:pw)');
        $ins->execute(['u'=>$username,'e'=>$email,'p'=>$phone,'pw'=>$hp]);
        $success='Registration successful. You can now <a href="index.php">log in</a>.';
      }
    }
    ?>
    <!doctype html>
    <html><head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="assets/styles.css"></head><body>
    <div class="container">
      <h2>Register</h2>
      <?php if(!empty($errors)): ?>
        <div class="error">
          <ul><?php foreach($errors as $er) echo '<li>'.htmlspecialchars($er).'</li>'; ?></ul>
        </div>
      <?php endif; ?>
      <?php if($success): ?>
        <div class="success"><?= $success ?></div>
      <?php else: ?>
      <form method="post">
        <label>Username</label><input name="username" required>
        <label>Email</label><input name="email" type="email" required>
        <label>Phone</label><input name="phone" required>
        <label>Password</label><input name="password" type="password" required>
        <button type="submit">Register</button>
      </form>
      <?php endif; ?>
      <p><a href="index.php">Back to Login</a></p>
    </div></body></html>