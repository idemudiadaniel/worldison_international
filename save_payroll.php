<?php
include("inc/db.php");
session_start();

// Ensure only logged in users can access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id      = intval($_POST['user_id']);
    $basic_salary = floatval($_POST['basic_salary']);
    $allowances   = floatval($_POST['allowances']);
    $deductions   = floatval($_POST['deductions']);
    $days_absent  = intval($_POST['days_absent']);
    $bonuses      = floatval($_POST['bonuses']);

    // Check if payroll already exists for this user
    $stmt = $conn->prepare("SELECT id FROM payroll WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ✅ Update existing payroll
        $update = $conn->prepare("UPDATE payroll 
            SET basic_salary=?, allowances=?, deductions=?, days_absent=?, bonuses=? 
            WHERE user_id=?");
        $update->bind_param("dddisi", $basic_salary, $allowances, $deductions, $days_absent, $bonuses, $user_id);
        $update->execute();
        $update->close();
        $_SESSION['msg'] = "Payroll updated successfully!";
    } else {
        // ✅ Insert new payroll
        $insert = $conn->prepare("INSERT INTO payroll (user_id, basic_salary, allowances, deductions, days_absent, bonuses) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("idddid", $user_id, $basic_salary, $allowances, $deductions, $days_absent, $bonuses);
        $insert->execute();
        $insert->close();
        $_SESSION['msg'] = "Payroll saved successfully!";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to payroll manager
    header("Location: payroll_manager.php");
    exit;
} else {
    header("Location: payroll_manager.php");
    exit;
}
