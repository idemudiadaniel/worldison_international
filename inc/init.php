<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

// Base URL (adjust if you move project folder)
define("BASE_URL", "/iceHRM/");

// Defaults
$full_name   = 'Guest';
$profile_pic = 'assets/images/faces/default.png';
$role        = 'guest';

// Check if user is logged in
if (!empty($_SESSION['user_id'])) {
    $user_id = (int) $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT full_name, profile_picture, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        // Set user details
        $full_name = $user['full_name'] ?: 'User';

        // Use uploaded profile picture if it exists
        $upload_path = __DIR__ . '/../uploads/';
        if (!empty($user['profile_picture']) && file_exists($upload_path . $user['profile_picture'])) {
            $profile_pic = "uploads/" . $user['profile_picture'];
        }

        $role = strtolower(trim($user['role'] ?: 'staff'));
        $_SESSION['role'] = $role;

    } else {
        // Invalid user → log them out
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "login.php?error=invalid_session");
        exit;
    }
} else {
    $_SESSION['role'] = 'guest';
}

/**
 * 🔒 Restrict 'role' updates to add_user.php only
 * Prevents role changes from any other page that submits POST data.
 */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['role']) &&
    strpos($_SERVER['PHP_SELF'], 'add_user.php') === false
) {
    unset($_POST['role']); // Remove 'role' from POST data to block update
}

/**
 * Restrict access by role(s).
 * Example: requireRole(['admin']) → only admins allowed.
 */
function requireRole($roles = []) {
    // Public page (no roles required)
    if (empty($roles)) {
        return;
    }

    // Must be logged in
    if (empty($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $userRole = $_SESSION['role'] ?? 'guest';

    // CEO has full access
    if ($userRole === 'ceo') {
        return;
    }

    // Check if allowed
    if (!in_array($userRole, (array)$roles)) {
        header("Location: " . BASE_URL . "dashboard.php?error=unauthorized");
        exit;
    }
}

/**
 * Sidebar visibility helper.
 * Example: if (canSee('admin')) { … }
 */
function canSee(...$roles) {
    $userRole = $_SESSION['role'] ?? 'guest';
    return in_array($userRole, $roles) || $userRole === 'ceo';
}
?>
