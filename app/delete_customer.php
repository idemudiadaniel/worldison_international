<?php
include("../inc/db.php");
session_start();

// Restrict to admin

// Restrict to specific roles
$allowedRoles = ['admin','ceo','manager',];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = intval($_GET['id']);

// Delete customer
$stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: customers.php?msg=deleted");
    exit;
} else {
    die("Error deleting record: " . $conn->error);
}
