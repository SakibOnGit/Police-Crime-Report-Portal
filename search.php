<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();
$q = $_GET['q'] ?? '';
$rows = [];
if ($q !== '') {
    $like = '%'.$q.'%';   // DB query kept safe so this page is ONLY reflected XSS
    $stmt = $conn->prepare("SELECT tracking_code,title,category,status FROM reports WHERE title LIKE ? OR description LIKE ?");
    $stmt->bind_param('ss',$like,$like); $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
}
render_header('Search', 'search');
?>
<div class="card">
  <p class="eyebrow">Case search</p>
  <h1>Search reports</h1>
  <form method="get" style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap">
    <input style="flex:1;min-width:220px" type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search titles and details…">
    <button class="btn" type="submit">Search</button>
  </form>
</div>
<?php if ($q !== ''): ?>
  <div class="card">
    <?php
      // [VULN: Reflected XSS] raw query echoed into the page:
      //   ?q=<script>alert(document.cookie)</script>
      echo '<div class="section-title"><h2>Results for: '.$q.'</h2></div>';
    ?>
    <?php if ($rows): ?>
      <table><thead><tr><th>Code</th><th>Report</th><th>Category</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="code"><?= htmlspecialchars($r['tracking_code']) ?></td>
            <td class="t-title"><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['category']) ?></td>
            <td><?= status_pill($r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    <?php else: ?><div class="empty">No reports match that search.</div><?php endif; ?>
  </div>
<?php endif; ?>
<?php render_footer(); ?>
