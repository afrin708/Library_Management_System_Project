<?php
    require 'config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='user') { header('Location: index.php'); exit; }
    $uid = $_SESSION['user_id'];
    $message='';
    if($_SERVER['REQUEST_METHOD']=='POST') {
      if(isset($_POST['plan'])) {
        // show fake payment link and ask for code
        $plan = $_POST['plan'];
        if($plan=='1m') $exp = '+1 month';
        elseif($plan=='6m') $exp = '+6 months';
        else $exp = '+1 year';
        $_SESSION['pending_vip_exp'] = $exp;
        $message = 'Click fake pay link then enter VIP code. <a href="#" onclick="alert(\'This is a demo fake-pay link.\')">Pay (fake)</a>';
      } elseif(isset($_POST['vip_code'])) {
        $code = trim($_POST['vip_code']);
        if($code === VIP_CODE) {
          $exp = $_SESSION['pending_vip_exp'] ?? '+1 month';
          $until = date('Y-m-d', strtotime($exp));
          $upd = $pdo->prepare('UPDATE users SET vip_until=:v WHERE id=:id');
          $upd->execute(['v'=>$until,'id'=>$uid]);
          $message = 'VIP activated until '.$until;
        } else {
          $message = 'Invalid code';
        }
      }
    }
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>Membership</title><link rel="stylesheet" href="assets/styles.css"></head><body>
    <div class="container">
      <div class="topbar"><h2>Membership Plans</h2><div><a href="user/dashboard.php">Back</a></div></div>
      <?php if($message) echo '<div class="success">'.htmlspecialchars($message).'</div>'; ?>
      <form method="post">
        <label>Select Plan</label>
        <select name="plan">
          <option value="1m">1 Month - BDT 100</option>
          <option value="6m">6 Months - BDT 500</option>
          <option value="1y">1 Year - BDT 900</option>
        </select>
        <button type="submit">Proceed to Pay (demo)</button>
      </form>
      <hr>
      <h3>Enter VIP Code</h3>
      <form method="post">
        <input name="vip_code" placeholder="Enter code sent to your phone">
        <button type="submit">Activate</button>
      </form>
    </div></body></html>