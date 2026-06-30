<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '1');
session_start();

include("../inc/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Find user by username or email
    $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if account is terminated
        if ($user['status'] !== 'active') {
            $error = "⚠️ Your account has been terminated. Contact HR/Admin.";
        } 
        // Verify password only if account is active
        elseif (password_verify($password, $user['password'])) {
            // Save session data
            $_SESSION['user_id']  = $user['id'];        // numeric id
            $_SESSION['staff_id'] = $user['staff_id']; // string staff_id
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Role-based redirect
            switch ($user['role']) {
                case 'ceo':
                case 'manager':
                case 'accountant':
                case 'auditor':
                case 'admin':
                case 'editor':
                case 'staff':
                    header("Location: ../dashboard.php");
                    break;
                default:
                    // fallback if role is unknown
                    header("Location: ../dashboard.php?error=role_unknown");
                    break;
            }
            exit;
        } else {
            $error = "❌ Invalid password!";
        }
    } else {
        $error = "❌ No user found with that username/email!";
    }
}
?>

<!-- Optional error display -->
<?php if (isset($error)): ?>
    <div style="color:red; text-align:center; margin-top:10px;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
