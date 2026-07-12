<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_officer();

// Accept GET or POST so a simple attacker link/redirect triggers it.
$report_id = (int)($_REQUEST['report_id'] ?? 0);
$status    = $_REQUEST['status'] ?? '';
$valid     = ['open','investigating','closed','dismissed'];

// [VULN: CSRF] no anti-CSRF token, no method check — any page that makes a
// logged-in officer's browser hit this URL silently changes the case status.
if (in_array($status,$valid,true) && $report_id>0) {
    $stmt=$conn->prepare("UPDATE reports SET status=? WHERE id=?");
    $stmt->bind_param('si',$status,$report_id); $stmt->execute();
}
header('Location: '.BASE.'/officer/view_report.php?id='.$report_id);
exit;
