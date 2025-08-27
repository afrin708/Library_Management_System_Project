<?php
    require '../config.php';
    if(!isset($_SESSION['user_type']) || $_SESSION['user_type']!=='user') { header('Location: ../index.php'); exit; }
    $uid = $_SESSION['user_id'];
    // fetch user
    $u = $pdo->prepare('SELECT * FROM users WHERE id=:id');
    $u->execute(['id'=>$uid]); $user = $u->fetch(PDO::FETCH_ASSOC);
    // list books with avg rating
    $books = $pdo->query('SELECT b.*, (SELECT AVG(rating) FROM ratings r WHERE r.book_id=b.id) as avg_rating FROM books b ORDER BY b.title')->fetchAll(PDO::FETCH_ASSOC);
    // user's issues
    $my_issues = $pdo->prepare('SELECT i.*, b.title FROM issues i JOIN books b ON i.book_id=b.id WHERE i.user_id=:uid ORDER BY i.issue_date DESC');
    $my_issues->execute(['uid'=>$uid]); $my_issues = $my_issues->fetchAll(PDO::FETCH_ASSOC);
    // calculate max allowed (60% of total distinct books)
    $total_books = $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
    $max_allowed = max(1, ceil($total_books * 0.6));
    ?>
    <!doctype html><html><head><meta charset="utf-8"><title>User Dashboard</title><link rel="stylesheet" href="../assets/styles.css"></head><body>
    <div class="container">
      <div class="topbar"><h2>Welcome, <?=htmlspecialchars($user['username'])?> <?= $user['vip_until'] ? '(VIP)' : '' ?></h2><div><a href="../index.php">Home</a> | <a href="../vip.php">Membership</a> | <a href="../logout.php">Logout</a></div></div>
      <h3>Available Books</h3>
      <table><tr><th>Title</th><th>Author</th><th>Available</th><th>Rating</th><th>Action</th></tr>
      <?php foreach($books as $b): ?>
        <tr>
          <td><?=htmlspecialchars($b['title'])?></td>
          <td><?=htmlspecialchars($b['author'])?></td>
          <td><?=$b['available_qty']?></td>
          <td><?= $b['avg_rating'] ? number_format($b['avg_rating'],1) : '-' ?></td>
          <td>
            <?php if($b['available_qty'] <= 0): ?>
    Unavailable
<?php elseif($b['is_vip'] == 1 && (!$user['vip_until'] || strtotime($user['vip_until']) < time())): ?>
    <button disabled style="background:#ccc; cursor:not-allowed;">VIP Only</button>
<?php else: ?>
    <form method="post" action="issue.php" style="display:inline">
        <input type="hidden" name="book_id" value="<?=$b['id']?>">
        <button type="submit" class="btn-small">Issue</button>
    </form>
<?php endif; ?>

            <form method="post" action="rate.php" style="display:inline">
              <input type="hidden" name="book_id" value="<?=$b['id']?>">
              <select name="rating" required>
                <option value="">Rate</option>
                <option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option>
              </select>
              <button type="submit" class="btn-small">Send</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </table>

      <h3>Your Issued Books (Limit <?=$max_allowed?>)</h3>
      <table><tr><th>Book</th><th>Issue Date</th><th>Return Date</th><th>Returned</th></tr>
      <?php foreach($my_issues as $it): ?>
        <tr>
          <td><?=htmlspecialchars($it['title'])?></td>
          <td><?=$it['issue_date']?></td>
          <td><?=$it['return_date']?></td>
          <td><?= $it['returned_date'] ? $it['returned_date'] : '<form method="post" action="return.php" style="display:inline"><input type="hidden" name="issue_id" value="'.$it['id'].'"><button class="btn-small">Return</button></form>' ?></td>
        </tr>
      <?php endforeach; ?>
      </table>

      <h3>Most Popular Books</h3>
      <table><tr><th>Title</th><th>Avg Rating</th></tr>
      <?php
        $popular = $pdo->query('SELECT b.title, AVG(r.rating) as avgR FROM books b JOIN ratings r ON b.id=r.book_id GROUP BY b.id ORDER BY avgR DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
        foreach($popular as $p) {
          echo '<tr><td>'.htmlspecialchars($p['title']).'</td><td>'.number_format($p['avgR'],1).'</td></tr>';
        }
      ?>
      </table>
    </div></body></html>