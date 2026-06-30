<?php
include("../inc/db.php");
session_start();
 {
    die("Unauthorized");
}

$id = intval($_GET['id']);
$status = $_GET['status'];
$admin_id = $_SESSION['id'];

$stmt = $conn->prepare("UPDATE attendance SET status=?, approved_by=? WHERE id=?");
$stmt->bind_param("sii", $status, $admin_id, $id);
$stmt->execute();

header("Location: manage.php");
exit;
?>
