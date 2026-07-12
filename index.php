<?php
require_once __DIR__ . '/includes/functions.php';
render_header('Home', 'home');
$u = current_user();
?>
<section class="card hero">
  <div class="hero-figure">§</div>
  <p class="eyebrow">Metropolitan Police</p>
  <h1>File it. Track it. Get it on the record.</h1>
  <p class="lead">The city's online crime registry. Report an incident, follow its progress, and let case officers work it — all in one place.</p>
  <form class="hero-track" method="get" action="<?= BASE ?>/track.php">
    <input type="text" name="code" placeholder="Enter a tracking code, e.g. CR-1001">
    <button class="btn btn-brass" type="submit">Track a case</button>
  </form>
</section>

<div class="grid">
<?php if ($u && $u['role'] === 'officer'): ?>
  <!-- Officer view -->
  <div class="tile">
    <h2>Case dashboard</h2>
    <p>Review every filed report, update case status, and record investigation notes.</p>
    <a class="btn" href="<?= BASE ?>/officer/dashboard.php">Open dashboard</a>
  </div>
  <div class="tile">
    <h2>Search cases</h2>
    <p>Find any case by keyword across all filed reports.</p>
    <a class="btn btn-ghost" href="<?= BASE ?>/search.php">Search reports</a>
  </div>
<?php elseif ($u && $u['role'] === 'citizen'): ?>
  <!-- Citizen view -->
  <div class="tile">
    <h2>Report an incident</h2>
    <p>Theft, robbery, fraud, vandalism and more. You'll get a tracking code the moment you file.</p>
    <a class="btn" href="<?= BASE ?>/report.php">File a report</a>
  </div>
  <div class="tile">
    <h2>Follow your case</h2>
    <p>Every report moves through open, investigating, then closed or dismissed. Check the status anytime.</p>
    <a class="btn btn-ghost" href="<?= BASE ?>/my_reports.php">My reports</a>
  </div>
<?php else: ?>
  <!-- Public / logged out -->
  <div class="tile">
    <h2>Report an incident</h2>
    <p>Theft, robbery, fraud, vandalism and more. You'll get a tracking code the moment you file.</p>
    <a class="btn" href="<?= BASE ?>/login.php">Sign in to file</a>
  </div>
  <div class="tile">
    <h2>Follow your case</h2>
    <p>Every report moves through open, investigating, then closed or dismissed. Check the status anytime.</p>
    <a class="btn btn-ghost" href="<?= BASE ?>/track.php">Track by code</a>
  </div>
<?php endif; ?>
</div>
<?php render_footer(); ?>
