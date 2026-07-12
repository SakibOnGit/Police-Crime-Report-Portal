<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_officer();
$u = current_user();
$report_id = (int)($_POST['report_id'] ?? 0);
$note = trim($_POST['note'] ?? '');
if ($report_id>0 && $note!=='') {
    $stmt=$conn->prepare("INSERT INTO notes (report_id,officer_id,note) VALUES (?,?,?)");
    $stmt->bind_param('iis',$report_id,$u['id'],$note); $stmt->execute();
}
header('Location: '.BASE.'/officer/view_report.php?id='.$report_id);
exit;
