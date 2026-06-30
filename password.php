<?php
include("inc/init.php");
include("inc/db.php");



// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_my_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>⚠️ All fields are required.</div>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-warning'>❌ New passwords do not match.</div>";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_password, $hashed)) {
            $message = "<div class='alert alert-danger'>🚫 Incorrect current password.</div>";
        } else {
            // Update new password
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $new_hash, $user_id);

            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Password changed successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Error updating password: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header"><h3 class="page-title">Change My Password</h3></div>

    <div class="row">
      <div class="col-md-6 grid-margin stretch-card mx-auto">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Change Your Password</h4>
            <?= $message ?? '' ?>

            <form method="POST" class="forms-sample">
              <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?= htmlspecialchars($username, ENT_QUOTES) ?>" class="form-control" readonly>
              </div>

              <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
              </div>

              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="5">
              </div>

              <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="5">
              </div>

              <button type="submit" name="change_my_password" class="btn btn-success">Update Password</button>
              <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
          </div>
        </div>
      </div>
    </div>

    <?php include("inc/footer.php"); ?>
  </div>
</div>

<?php include("inc/script.php"); ?>
</body>
</html>
