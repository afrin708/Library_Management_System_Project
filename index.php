<?php
    require 'config.php';
    // If logged in, redirect to appropriate dashboard
    if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='admin') {
      header('Location: admin/dashboard.php'); exit;
    } elseif(isset($_SESSION['user_type']) && $_SESSION['user_type']=='user') {
      header('Location: user/dashboard.php'); exit;
    }
    $error = '';
    if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['login'])) {
      $username = trim($_POST['username']);
      $password = $_POST['password'];
      // Check admin first
      if($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['user_type'] = 'admin';
        $_SESSION['username'] = ADMIN_USER;
        header('Location: admin/dashboard.php'); exit;
      } else {
        // Check users table
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u');
        $stmt->execute(['u'=>$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($password, $user['password'])) {
          $_SESSION['user_type']='user';
          $_SESSION['user_id']=$user['id'];
          $_SESSION['username']=$user['username'];
          header('Location: user/dashboard.php'); exit;
        } else {
          $error = 'Invalid credentials';
        }
      }
    }
    ?>
    <!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Library - Login</title>
      <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
    <div class="container">
      <h1>Library System</h1>
      <?php if($error): ?>
        <div class="error"><?=htmlspecialchars($error)?></div>
      <?php endif; ?>
      <form method="post">
        <label>Username</label>
        <input name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button name="login" type="submit">Log in</button>
      </form>
      <p>Don't have an account? <a href="register.php">Register here</a></p>
      <p>Admin login: username <strong>admin</strong> password <strong>admin123</strong></p>
    </div>
    </body>
    </html>