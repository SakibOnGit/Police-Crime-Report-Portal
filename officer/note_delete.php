<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_officer();
$note_id   = (int)($_POST['note_id'] ?? 0);
$report_id = (int)($_POST['report_id'] ?? 0);
if ($note_id>0) {
    $stmt=$conn->prepare("DELETE FROM notes WHERE id=?");
    $stmt->bind_param('i',$note_id); $stmt->execute();
}
header('Location: '.BASE.'/officer/view_report.php?id='.$report_id);
exit;
