<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_officer();
$flash = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']);
$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT r.*, u.username reporter FROM reports r LEFT JOIN users u ON r.reporter_id=u.id WHERE r.id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

render_header('Case detail', 'dash');
if (!$r) { echo '<div class="card"><div class="empty"><span class="big">?</span>Case not found.</div></div>'; render_footer(); exit; }

$ns = $conn->prepare("SELECT n.*, u.username officer FROM notes n LEFT JOIN users u ON n.officer_id=u.id WHERE n.report_id=? ORDER BY n.id DESC");
$ns->bind_param('i',$id); $ns->execute();
$notes = $ns->get_result();
?>
<?php if ($flash): ?><div class="msg msg-ok"><?= htmlspecialchars($flash) ?></div><?php endif; ?>
<div class="card">
  <div class="section-title">
    <div><p class="eyebrow"><?= htmlspecialchars($r['tracking_code']) ?></p>
      <h1><?= htmlspecialchars($r['title']) ?></h1></div>
    <div><?= status_pill($r['status']) ?></div>
  </div>
  <div class="meta">
    <span><b>Category</b> <?= htmlspecialchars($r['category']) ?></span>
    <span><b>Location</b> <?= htmlspecialchars($r['location']) ?></span>
    <span><b>Reporter</b> <?= htmlspecialchars($r['reporter'] ?? 'unknown') ?></span>
    <span><b>Filed</b> <?= htmlspecialchars($r['created_at']) ?></span>
  </div>
  <div class="desc">
    <?php
      // [VULN: Stored XSS] description printed WITHOUT encoding — a payload
      // stored by a citizen executes here inside the officer's session.
      echo $r['description'];
    ?>
  </div>
  <div class="row-actions">
   
    <form class="inline" method="post" action="<?= BASE ?>/delete_report.php" onsubmit="return confirm('Delete this case permanently?')">
      <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
      <button class="btn btn-danger btn-sm" type="submit">Delete case</button>
    </form>
  </div>
</div>

<div class="card">
  <h2>Update status</h2>
  <!-- [VULN: CSRF] no token on this state-changing action -->
  <form method="post" action="<?= BASE ?>/officer/update_status.php" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
    <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
    <div style="flex:1;min-width:180px"><label>New status</label>
      <select name="status">
        <?php foreach (['open','investigating','closed','dismissed'] as $s): ?>
          <option value="<?= $s ?>"<?= $s===$r['status']?' selected':'' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select></div>
    <button class="btn" type="submit">Update</button>
  </form>
</div>

<div class="card">
  <div class="section-title"><h2>Case notes</h2></div>
  <form method="post" action="<?= BASE ?>/officer/note_add.php">
    <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
    <textarea name="note" placeholder="Add an investigation note…" required style="min-height:80px"></textarea>
    <div class="row-actions"><button class="btn btn-sm" type="submit">Add note</button></div>
  </form>
  <?php if ($notes->num_rows): ?>
    <?php while ($n = $notes->fetch_assoc()): ?>
      <div class="note">
        <?= nl2br(htmlspecialchars($n['note'])) ?>
        <div class="note-meta">— <?= htmlspecialchars($n['officer'] ?? 'officer') ?> · <?= htmlspecialchars($n['created_at']) ?>
          &nbsp;·&nbsp;
          <form class="inline" method="post" action="<?= BASE ?>/officer/note_delete.php" onsubmit="return confirm('Delete this note?')">
            <input type="hidden" name="note_id" value="<?= (int)$n['id'] ?>">
            <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
          </form>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="hint">No notes yet.</p>
  <?php endif; ?>
</div>
<?php render_footer(); ?>
