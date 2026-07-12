<?php
// =====================================================================
//  Database connection (MariaDB / MySQL compatible)
//  Change these 4 lines only if your setup differs.
// =====================================================================

$DB_HOST = 'localhost';
$DB_USER = 'portal';
$DB_PASS = 'portal123';
$DB_NAME = 'police_portal';

// Return false on query errors (instead of throwing) so the vulnerable
// pages can echo the raw SQL error text -> makes error-based SQLi easy.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
