<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // [VULN: SQL Injection — POST]  username payload:  officer' --
    $sql = "SELECT id, username, role, full_name FROM users WHERE username = '$username' AND password = '$password'";
    $res = $conn->query($sql);
    if (!$res) {
        $msg = '<pre class="sqlerror">SQL Error: ' . $conn->error . "\nQuery: " . htmlspecialchars($sql) . '</pre>';
    } elseif ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $_SESSION['user'] = ['id'=>$row['id'],'username'=>$row['username'],'role'=>$row['role'],'name'=>$row['full_name']];
        header('Location: ' . (($row['role']==='officer') ? BASE.'/officer/dashboard.php' : BASE.'/my_reports.php'));
        exit;
    } else {
        $msg = '<div class="msg msg-err">Those credentials don\'t match an account. Check the username and password and try again.</div>';
    }
}
render_header('Sign in', 'login');
?>
<div class="card" style="max-width:440px;margin-inline:auto">
  <p class="eyebrow">Registry access</p>
  <h1>Sign in</h1>
  <p class="lead">Citizens and case officers use the same sign-in.</p>
  <?= $msg ?>
  <form method="post">
    <label>Username</label>
    <input type="text" name="username" required autofocus>
    <label>Password</label>
    <input type="password" name="password" required>
    <div class="row-actions"><button class="btn" type="submit">Sign in</button>
      <a class="btn btn-ghost" href="<?= BASE ?>/register.php">Create account</a></div>
  </form>
  <p class="hint">Seed officer <code>officer / police123</code> · citizen <code>rahim / rahim123</code></p>
</div>
<?php render_footer(); ?>
