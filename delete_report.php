<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();
$u = current_user();
$id = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM reports WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if ($r && can_modify($r)) {
    $del=$conn->prepare("DELETE FROM reports WHERE id=?");
    $del->bind_param('i',$id); $del->execute();
    $_SESSION['flash']='Report '.$r['tracking_code'].' deleted.';
}
header('Location: '.(($u['role']==='officer') ? BASE.'/officer/dashboard.php' : BASE.'/my_reports.php'));
exit;
