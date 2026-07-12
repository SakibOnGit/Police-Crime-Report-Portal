<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_officer();
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);

$counts = ['open'=>0,'investigating'=>0,'closed'=>0,'dismissed'=>0];
$cres = $conn->query("SELECT status, COUNT(*) c FROM reports GROUP BY status");
while ($x=$cres->fetch_assoc()) { $counts[$x['status']] = (int)$x['c']; }
$total = array_sum($counts);

$res = $conn->query("SELECT r.id,r.tracking_code,r.title,r.category,r.status,r.created_at,u.username reporter
                     FROM reports r LEFT JOIN users u ON r.reporter_id=u.id ORDER BY r.id DESC");
render_header('Dashboard', 'dash');
?>
<div class="section-title"><div><p class="eyebrow">Case management</p><h1>Case dashboard</h1></div></div>
<?php if ($flash): ?><div class="msg msg-ok"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<div class="stats">
  <div class="stat"><div class="n"><?= $total ?></div><div class="l">Total cases</div></div>
  <div class="stat"><div class="n" style="color:var(--open)"><?= $counts['open'] ?></div><div class="l">Open</div></div>
  <div class="stat"><div class="n" style="color:var(--inv)"><?= $counts['investigating'] ?></div><div class="l">Investigating</div></div>
  <div class="stat"><div class="n" style="color:var(--closed)"><?= $counts['closed'] ?></div><div class="l">Closed</div></div>
  <div class="stat"><div class="n" style="color:var(--dism)"><?= $counts['dismissed'] ?></div><div class="l">Dismissed</div></div>
</div>
<div class="card">
  <table><thead><tr><th>Code</th><th>Report</th><th>Reporter</th><th>Status</th><th></th></tr></thead><tbody>
    <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td class="code"><?= htmlspecialchars($r['tracking_code']) ?></td>
        <td class="t-title"><?= htmlspecialchars($r['title']) ?><br><span class="hint" style="margin:0"><?= htmlspecialchars($r['category']) ?></span></td>
        <td><?= htmlspecialchars($r['reporter'] ?? '—') ?></td>
        <td><?= status_pill($r['status']) ?></td>
        <td style="text-align:right"><a class="btn btn-ghost btn-sm" href="<?= BASE ?>/officer/view_report.php?id=<?= (int)$r['id'] ?>">Open case</a></td>
      </tr>
    <?php endwhile; ?>
  </tbody></table>
</div>
<?php render_footer(); ?>
