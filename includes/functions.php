<?php
// =====================================================================
//  Shared helpers: session, auth guards, layout
//  (Secure-mode/toggle removed — this build demonstrates the raw
//   vulnerabilities only.)
// =====================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// URL base — deploy the project as /var/www/html/police-portal .
// If you rename the folder, change this ONE line.
define('BASE', '/police-portal');

// ---- Auth ----------------------------------------------------------
function current_user() { return $_SESSION['user'] ?? null; }

function require_login() {
    if (!current_user()) { header('Location: ' . BASE . '/login.php'); exit; }
}
function require_officer() {
    $u = current_user();
    if (!$u || $u['role'] !== 'officer') { header('Location: ' . BASE . '/login.php'); exit; }
}
// A citizen may modify their own report; an officer may modify any.
function can_modify($report) {
    $u = current_user();
    if (!$u) return false;
    if ($u['role'] === 'officer') return true;
    return (int)($report['reporter_id'] ?? 0) === (int)$u['id'];
}

// ---- Small view helpers -------------------------------------------
function status_pill($status) {
    $s = htmlspecialchars($status);
    return '<span class="pill pill-' . $s . '">' . $s . '</span>';
}

// ---- Layout --------------------------------------------------------
function render_header($title = 'Crime Report Portal', $active = '') {
    $u = current_user();
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . htmlspecialchars($title) . ' — Metro Police Registry</title>';
    echo '<link rel="stylesheet" href="' . BASE . '/assets/style.css"></head><body>';
    echo '<div class="accentline"></div>';
    echo '<header class="topbar"><a class="brand" href="' . BASE . '/index.php">';
    echo '<span class="badge-mark">&#9733;</span><span class="brand-txt">Metro Police<em> Crime Registry</em></span></a>';
    echo '<nav>';
    $link = function($href, $label, $key) use ($active) {
        $cls = ($active === $key) ? ' class="on"' : '';
        echo '<a' . $cls . ' href="' . BASE . $href . '">' . $label . '</a>';
    };
    $link('/index.php', 'Home', 'home');
    $link('/track.php', 'Track', 'track');
    if ($u) {
        $link('/search.php', 'Search', 'search');
        if ($u['role'] === 'citizen') {
            $link('/report.php', 'File a report', 'report');
            $link('/my_reports.php', 'My reports', 'mine');
        } else {
            $link('/officer/dashboard.php', 'Dashboard', 'dash');
        }
        echo '<span class="who">' . htmlspecialchars($u['username']) . '</span>';
        echo '<a class="ghostnav" href="' . BASE . '/logout.php">Sign out</a>';
    } else {
        $link('/login.php', 'Sign in', 'login');
        echo '<a class="ghostnav" href="' . BASE . '/register.php">Register</a>';
    }
    echo '</nav></header><main class="container">';
}

function render_footer() {
    echo '</main><footer class="foot"><span>Metropolitan Police — Online Crime Registry</span>';
    echo '<span class="foot-note">Security lab build · localhost use only</span></footer></body></html>';
}
