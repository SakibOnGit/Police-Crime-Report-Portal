<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();
$u = current_user();
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);

$stmt = $conn->prepare("SELECT id,tracking_code,title,category,status,created_at FROM reports WHERE reporter_id=? ORDER BY id DESC");
$stmt->bind_param('i',$u['id']); $stmt->execute();
$res = $stmt->get_result();
render_header('My reports', 'mine');
?>
<div class="card">
  <div class="section-title"><div><p class="eyebrow">Your filings</p><h1>My reports</h1></div>
    <a class="btn btn-sm" href="<?= BASE ?>/report.php">+ New report</a></div>
  <?php if ($flash): ?><div class="msg msg-ok"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
  <?php if ($res->num_rows): ?>
  <table><thead><tr><th>Code</th><th>Report</th><th>Category</th><th>Status</th><th></th></tr></thead><tbody>
    <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td class="code"><?= htmlspecialchars($r['tracking_code']) ?></td>
        <td class="t-title"><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['category']) ?></td>
        <td><?= status_pill($r['status']) ?></td>
        <td style="text-align:right;white-space:nowrap">
          <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/edit_report.php?id=<?= (int)$r['id'] ?>">Edit</a>
          <form class="inline" method="post" action="<?= BASE ?>/delete_report.php" onsubmit="return confirm('Delete this report? This cannot be undone.')">
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody></table>
  <?php else: ?>
    <div class="empty"><span class="big">§</span>You haven't filed any reports yet.<br>
      <a class="btn btn-sm" style="margin-top:14px" href="<?= BASE ?>/report.php">File your first report</a></div>
  <?php endif; ?>
</div>
<?php render_footer(); ?>
