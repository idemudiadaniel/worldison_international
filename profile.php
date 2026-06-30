<?php
require_once "inc/init.php"; // handles session + db

// Only staff and admin can access
requireRole(['admin','staff','ceo','manager','editor', 'accountant']);

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'] ?? 'guest';

// Fetch user data
$stmt = $conn->prepare("SELECT id, full_name, email, phone, address, profile_picture, role 
                        FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc() ?: [];

// Paths
$default_pic   = "assets/images/faces/default.png";
$upload_dir    = __DIR__ . "/uploads/";
$upload_url    = "uploads/";

$profile_filename = $user['profile_picture'] ?? '';
$profile_pic = (!empty($profile_filename) && file_exists($upload_dir . $profile_filename))
    ? $upload_url . $profile_filename
    : $default_pic;

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $full_name = $user['full_name'] ?? '';
    $email     = $user['email'] ?? '';

    // Upload new profile picture if provided
    if (!empty($_FILES['profile_picture']['name'])) {
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['profile_picture']['name']));
        $target   = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
            // Delete old picture (if not default)
            if (!empty($profile_filename) && file_exists($upload_dir . $profile_filename)) {
                unlink($upload_dir . $profile_filename);
            }
            $profile_filename = $filename;
        }
    }

    if ($role === 'staff') {
        $stmt = $conn->prepare("UPDATE users SET phone=?, address=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("sssi", $phone, $address, $profile_filename, $user_id);
    } else { // admin
        $full_name = trim($_POST['full_name'] ?? $full_name);
        $email     = trim($_POST['email'] ?? $email);
        $stmt = $conn->prepare("UPDATE users SET full_name=?, email=?, phone=?, address=?, profile_picture=? WHERE id=?");
        $stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $profile_filename, $user_id);
    }

    $stmt->execute();
    header("Location: profile.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <h3 class="page-title mb-4">
          <i class="mdi mdi-account-circle text-primary"></i> Edit Profile
        </h3>

        <?php if (isset($_GET['updated'])): ?>
          <p class="alert alert-success">Profile updated successfully!</p>
        <?php endif; ?>

        <div class="card">
          <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
              <?php if ($role === 'admin'): ?>
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="form-control" required>
                </div>
              <?php else: ?>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name'] ?? '') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '') ?></p>
              <?php endif; ?>

              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control">
              </div>
              <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
              </div>
              <div class="form-group">
                <label>Profile Picture</label><br>
                <img src="<?= htmlspecialchars($profile_pic) ?>?t=<?= time() ?>" id="preview" alt="Profile Picture">
                <input type="file" name="profile_picture" class="form-control mt-2" onchange="previewImage(event)">
              </div>
              <button type="submit" class="btn btn-gradient-primary">Update Profile</button>
            </form>
          </div>
        </div>

      </div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>

<script>
function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById('preview').src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}
</script>
<?php include("inc/script.php"); ?>
</body>
</html>
