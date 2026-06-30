<?php
session_start();
include("inc/db.php");

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo',])) {
    header("Location: dashboard.php");
    exit;
  }
if (!isset($_GET['file'])) {
    die("Invalid request.");
}

$filename = basename($_GET['file']); // prevent path traversal
$filepath = __DIR__ . "/uploads/" . $filename;

if (!file_exists($filepath)) {
    die("File not found.");
}

// Detect MIME type (so images, pdf, etc. open correctly)
$mimeType = mime_content_type($filepath);

// Set headers
header("Content-Description: File Transfer");
header("Content-Type: " . $mimeType);
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Content-Length: " . filesize($filepath));
flush();
readfile($filepath);
exit;
