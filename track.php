<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$code = $_GET['code'] ?? '';
$rows = []; $err = '';
if ($code !== '') {
    // [VULN: SQL Injection — GET / sqlmap target]  4 columns:
    //   ' UNION SELECT username,password,role,id FROM users-- -
    $sql = "SELECT tracking_code, title, status, category FROM reports WHERE tracking_code = '$code'";
    $res = $conn->query($sql);
    if (!$res) { $err = 'SQL Error: ' . $conn->error . "\nQuery: $sql"; }
    else { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }
}
render_header('Track a case', 'track');
?>
<div class="card">
  <p class="eyebrow">Public lookup</p>
  <h1>Track a case</h1>
  <p class="lead">Enter the tracking code from your report to see where it stands. No sign-in needed.</p>
  <form method="get" style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap">
    <input style="flex:1;min-width:220px" type="text" name="code" value="<?= htmlspecialchars($code) ?>" placeholder="e.g. CR-1001">
    <button class="btn" type="submit">Track</button>
  </form>
</div>
<?php if ($code !== ''): ?>
  <div class="card">
    <?php
      // [VULN: Reflected XSS] the searched code is echoed back WITHOUT encoding.
      //   ?code=<script>alert(document.cookie)</script>
      echo '<div class="section-title"><h2>Showing results for: '.$code.'</h2></div>';
    ?>
    <?php if ($err): ?>
      <pre class="sqlerror"><?= htmlspecialchars($err) ?></pre>
    <?php elseif ($rows): ?>
      <table><thead><tr><th>Field 1</th><th>Field 2</th><th>Field 3</th><th>Field 4</th></tr></thead><tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="code"><?= htmlspecialchars($r['tracking_code']) ?></td>
            <td class="t-title"><?= htmlspecialchars($r['title']) ?></td>
            <td><?= status_pill($r['status']) ?></td>
            <td><?= htmlspecialchars($r['category']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    <?php else: ?>
      <div class="empty"><span class="big">?</span>No case matches that code.</div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?php render_footer(); ?>
