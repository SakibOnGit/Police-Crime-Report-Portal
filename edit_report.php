<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();
$u = current_user();
$id = (int)($_REQUEST['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM reports WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if (!$r || !can_modify($r)) { header('Location: '.BASE.'/index.php'); exit; }

// Officers do not edit a citizen's complaint text — a report is a legal
// record. Officers manage status and notes instead. Block them from edit.
if ($u['role'] === 'officer') { header('Location: '.BASE.'/officer/view_report.php?id='.$id); exit; }

$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title=trim($_POST['title']??''); $category=trim($_POST['category']??'');
    $location=trim($_POST['location']??''); $desc=$_POST['description']??'';
    // Update via prepared statement; description saved verbatim (keeps stored-XSS payload).
    $up=$conn->prepare("UPDATE reports SET title=?,category=?,location=?,description=? WHERE id=?");
    $up->bind_param('ssssi',$title,$category,$location,$desc,$id); $up->execute();
    $back = BASE.'/my_reports.php';
    $_SESSION['flash']='Report '.$r['tracking_code'].' updated.';
    header('Location: '.$back); exit;
}
$cats=['Theft','Robbery','Burglary','Vandalism','Assault','Fraud','Other'];
render_header('Edit report', 'mine');
?>
<div class="card">
  <p class="eyebrow"><?= htmlspecialchars($r['tracking_code']) ?></p>
  <h1>Edit report</h1>
  <form method="post">
    <input type="hidden" name="id" value="<?= (int)$id ?>">
    <label>What happened</label>
    <input type="text" name="title" value="<?= htmlspecialchars($r['title']) ?>" required>
    <label>Category</label>
    <select name="category">
      <?php foreach ($cats as $c): ?>
        <option<?= $c===$r['category']?' selected':'' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
    <label>Where it happened</label>
    <input type="text" name="location" value="<?= htmlspecialchars($r['location']) ?>">
    <label>Details</label>
    <textarea name="description"><?= htmlspecialchars($r['description']) ?></textarea>
    <div class="row-actions"><button class="btn" type="submit">Save changes</button>
      <a class="btn btn-ghost" href="<?= BASE ?>/my_reports.php">Cancel</a></div>
  </form>
</div>
<?php render_footer(); ?>
