<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = trim($_POST['full_name'] ?? '');
    if ($username === '' || $password === '') {
        $msg = '<div class="msg msg-err">A username and password are both required.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,password,role,full_name) VALUES (?,?,'citizen',?)");
        $stmt->bind_param('sss', $username, $password, $fullname);
        if ($stmt->execute()) {
            $msg = '<div class="msg msg-ok">Account created. <a href="'.BASE.'/login.php">Sign in</a> to continue.</div>';
        } else {
            $msg = '<div class="msg msg-err">That username is already taken. Try another.</div>';
        }
    }
}
render_header('Register', 'login');
?>
<div class="card" style="max-width:440px;margin-inline:auto">
  <p class="eyebrow">New citizen account</p>
  <h1>Create an account</h1>
  <?= $msg ?>
  <form method="post">
    <label>Full name</label>
    <input type="text" name="full_name" placeholder="Your name">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <div class="row-actions"><button class="btn" type="submit">Create account</button></div>
  </form>
</div>
<?php render_footer(); ?>
