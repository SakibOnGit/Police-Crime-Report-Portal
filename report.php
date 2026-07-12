<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();
$u = current_user();
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title=trim($_POST['title']??''); $category=trim($_POST['category']??'');
    $location=trim($_POST['location']??''); $desc=$_POST['description']??'';
    $code='CR-'.rand(2000,9999);
    // Description stored verbatim (STORED-XSS source; fires in officer view).
    $stmt=$conn->prepare("INSERT INTO reports (tracking_code,reporter_id,title,category,location,description,status) VALUES (?,?,?,?,?,?, 'open')");
    $stmt->bind_param('sissss',$code,$u['id'],$title,$category,$location,$desc);
    if ($stmt->execute()) {
        $_SESSION['flash']='Report filed. Tracking code '.$code.'.';
        header('Location: '.BASE.'/my_reports.php'); exit;
    } else { $msg='<div class="msg msg-err">Couldn\'t file the report. Try again.</div>'; }
}
render_header('File a report', 'report');
?>
<div class="card">
  <p class="eyebrow">New report</p>
  <h1>File a crime report</h1>
  <p class="lead">Give us what you can. You'll get a tracking code as soon as you submit.</p>
  <?= $msg ?>
  <form method="post">
    <label>What happened</label>
    <input type="text" name="title" placeholder="Short summary" required>
    <label>Category</label>
    <select name="category">
      <option>Theft</option><option>Robbery</option><option>Burglary</option>
      <option>Vandalism</option><option>Assault</option><option>Fraud</option><option>Other</option>
    </select>
    <label>Where it happened</label>
    <input type="text" name="location" placeholder="Area, landmark, address">
    <label>Details</label>
    <textarea name="description" placeholder="Describe the incident — time, people involved, anything distinctive."></textarea>
    <div class="row-actions"><button class="btn" type="submit">Submit report</button>
      <a class="btn btn-ghost" href="<?= BASE ?>/my_reports.php">Cancel</a></div>
  </form>
</div>
<?php render_footer(); ?>
