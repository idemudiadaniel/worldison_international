<?php
include("inc/init.php");
include("inc/db.php");
include("inc/email.php"); // ✅ add this line



// Restrict access
$allowedRoles = ['admin', 'ceo'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Fetch branches for dropdown
$branches = [];
$branchQuery = $conn->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name ASC");
if (!$branchQuery) {
    die("❌ SQL Error fetching branches: " . $conn->error);
}
while ($b = $branchQuery->fetch_assoc()) {
    $branches[] = $b;
}

// ➕ Add New User
if (isset($_POST['add_user'])) {
  $staff_id   = $_POST['staff_id'];
  $full_name  = $_POST['full_name'];
  $username   = $_POST['username'];
  $email      = $_POST['email'];
  $plain_pass = $_POST['password']; // keep plain password temporarily
  $password   = password_hash($plain_pass, PASSWORD_DEFAULT);
  $role       = $_POST['role'];
  $branch_id  = !empty($_POST['branch_id']) ? intval($_POST['branch_id']) : null;

  $sql = "INSERT INTO users (staff_id, full_name, username, email, password, role, branch_id)
          VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssssi", $staff_id, $full_name, $username, $email, $password, $role, $branch_id);

  if ($stmt->execute()) {
      echo "<div class='alert alert-success'>✅ New user added successfully!</div>";

      // ✅ Send welcome email
      $subject = "Welcome to Worldison International Ltd";
      $message = "
          <h2>Welcome to Worldison International Ltd</h2>
          <p>Dear <strong>$full_name</strong>,</p>
          <p>Your staff account has been created successfully. Below are your login details:</p>
          <ul>
              <li><strong>Staff ID:</strong> $staff_id</li>
              <li><strong>Username:</strong> $username</li>
              <li><strong>Email:</strong> $email</li>
              <li><strong>Password:</strong> $plain_pass</li>
          </ul>
          <p>You can log in here: <a href='https://worldison.org/login.php'>Login Now</a></p>
          <br>
          <p>Best regards,<br>Worldison Admin Team</p>
      ";

      // make sure inc/email.php defines sendEmail($to, $subject, $htmlMessage)
      sendEmail($email, $subject, $message);
  } else {
      echo "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
  }
}

  
    // 🔐 Change Password
    if (isset($_POST['change_password'])) {
      $id = intval($_POST['user_id']);
      $new_password = $_POST['new_password'];

      // Get target user's role
      $stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $stmt->bind_result($target_role);
      $stmt->fetch();
      $stmt->close();

      // Restrict who admin can change password for
      if ($_SESSION['role'] === 'admin' && in_array($target_role, ['ceo','admin','accountant'])) {
          echo "<div class='alert alert-danger'>🚫 You cannot change password for CEO, Admin, or Accountant.</div>";
      } else {
          $hashed = password_hash($new_password, PASSWORD_DEFAULT);
          $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
          $stmt->bind_param("si", $hashed, $id);

          if ($stmt->execute()) {
              echo "<div class='alert alert-success'>✅ Password changed successfully!</div>";
          } else {
              echo "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
          }
      }
  }

    // ✏️ Update User
    if (isset($_POST['update_user'])) {
        $id        = intval($_POST['user_id']);
        $staff_id  = $_POST['edit_staff_id'];
        $full_name = $_POST['edit_full_name'];
        $username  = $_POST['edit_username'];
        $email     = $_POST['edit_email'];
        $role      = $_POST['edit_role'];
        $branch_id = !empty($_POST['edit_branch_id']) ? intval($_POST['edit_branch_id']) : null;

        $sql = "UPDATE users SET staff_id=?, full_name=?, username=?, email=?, role=?, branch_id=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $staff_id, $full_name, $username, $email, $role, $branch_id, $id);

        echo $stmt->execute()
            ? "<div class='alert alert-success'>✅ User updated successfully!</div>"
            : "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
    }

    // 🗑 Delete or Request Delete
    if (isset($_POST['delete_user'])) {
        $id = intval($_POST['delete_id']);
        $action = $_POST['delete_action'];

        if ($action === 'request' && $_SESSION['role'] !== 'ceo') {
            $requestedBy = $_SESSION['username'];
            $stmt = $conn->prepare("UPDATE users SET delete_request_by=?, delete_status='pending' WHERE id=?");
            $stmt->bind_param("si", $requestedBy, $id);
            $stmt->execute();
            echo "<div class='alert alert-warning'>⚠️ Deletion request sent for CEO approval.</div>";
        } elseif ($action === 'delete' && $_SESSION['role'] === 'ceo') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>✅ User permanently deleted.</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ Delete failed: " . htmlspecialchars($conn->error) . "</div>";
            }
        }
    }

// 📋 Fetch users and join branch names
$sql = "
  SELECT u.*, b.branch_name
  FROM users u
  LEFT JOIN branches b ON u.branch_id = b.branch_id
  WHERE u.delete_status IS NULL OR u.delete_status!='deleted'
  ORDER BY u.id DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("❌ SQL Error fetching users: " . $conn->error);
}

// Fetch into an array so we can iterate safely in HTML (avoids consuming the result prematurely)
$users = $result->fetch_all(MYSQLI_ASSOC);
$result->free();
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header"><h3 class="page-title">User Management</h3></div>

        <div class="row">
          <!-- Add User Form -->
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Add New User</h4>
                <form method="POST" class="forms-sample">
                  <div class="form-group">
                    <label>Staff ID</label>
                    <input type="text" name="staff_id" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                  </div>

                  <!-- Branch Dropdown -->
                  <div class="form-group">
                    <label>Branch</label>
                    <select name="branch_id" class="form-control" required>
                      <option value="">-- Select Branch --</option>
                      <?php foreach ($branches as $branch): ?>
                        <option value="<?= intval($branch['branch_id']) ?>"><?= htmlspecialchars($branch['branch_name'], ENT_QUOTES) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Role Dropdown -->
                  <div class="form-group">
                    <label>Role</label>
                    <select name="role" id="add_role" class="form-control">
                      <?php if($_SESSION['role'] === 'ceo'): ?>
                        <option value="ceo">Admin (CEO)</option>
                        <option value="admin">Admin</option>
                        <option value="accountant">Accountant</option>
                      <?php endif; ?>
                      <option value="manager">Manager</option>
                      <option value="editor">Editor</option>
                      <option value="staff" selected>Staff</option>
                    </select>
                  </div>

                  <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </form>
              </div>
            </div>
          </div>

          <!-- User List -->
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">All Users</h4>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Staff ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Role</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($users)): ?>
                        <?php foreach ($users as $row): ?>
                          <tr>
                            <td><?= intval($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['staff_id'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['full_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['username'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['email'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['branch_name'] ?? '—', ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['role'], ENT_QUOTES) ?></td>
                            <td>
                              <!-- Edit button -->
                              <button type="button" class="btn btn-sm btn-primary editBtn"
                                data-id="<?= intval($row['id']) ?>"
                                data-staff_id="<?= htmlspecialchars($row['staff_id'], ENT_QUOTES) ?>"
                                data-full_name="<?= htmlspecialchars($row['full_name'], ENT_QUOTES) ?>"
                                data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>"
                                data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                                data-role="<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>"
                                data-branch_id="<?= intval($row['branch_id'] ?? 0) ?>"
                              >Edit</button>
                              <button type="button" class="btn btn-sm btn-warning changePassBtn"
                              data-id="<?= intval($row['id']) ?>"
                              data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>"
                              data-role="<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>"
                            >Change Password</button>        
                                </form>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr><td colspan="8" class="text-center">No users found.</td></tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
        <?php include("inc/footer.php"); ?>
      </div>
    </div>
  </div>
</div>

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="edit_id">
          <div class="form-group"><label>Staff ID</label><input type="text" name="edit_staff_id" id="edit_staff_id" class="form-control" required></div>
          <div class="form-group"><label>Full Name</label><input type="text" name="edit_full_name" id="edit_full_name" class="form-control" required></div>
          <div class="form-group"><label>Username</label><input type="text" name="edit_username" id="edit_username" class="form-control" required></div>
          <div class="form-group"><label>Email</label><input type="email" name="edit_email" id="edit_email" class="form-control" required></div>

          <!-- Branch Dropdown -->
          <div class="form-group">
            <label>Branch</label>
            <select name="edit_branch_id" id="edit_branch_id" class="form-control" required>
              <option value="">-- Select Branch --</option>
              <?php foreach ($branches as $branch): ?>
                <option value="<?= intval($branch['branch_id']) ?>"><?= htmlspecialchars($branch['branch_name'], ENT_QUOTES) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Role</label>
            <select name="edit_role" id="edit_role" class="form-control">
              <option value="ceo">Admin (CEO)</option>
              <option value="admin">Admin</option>
              <option value="manager">Manager</option>
              <option value="accountant">Accountant</option>
              <option value="editor">Editor</option>
              <option value="staff">Staff</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="update_user" class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- 🔐 Change Password Modal -->
<div class="modal fade" id="changePassModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Change User Password</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="pass_user_id">
          <div class="form-group">
            <label>Username</label>
            <input type="text" id="pass_username" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required minlength="5">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="change_password" class="btn btn-success">Update Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include("inc/script.php"); ?>

<script>
  // Fill Edit Modal (using jQuery + Bootstrap 4 compatible)
  $(document).on('click', '.editBtn', function() {
    var btn = $(this);
    $('#edit_id').val(btn.data('id'));
    $('#edit_staff_id').val(btn.data('staff_id'));
    $('#edit_full_name').val(btn.data('full_name'));
    $('#edit_username').val(btn.data('username'));
    $('#edit_email').val(btn.data('email'));
    $('#edit_role').val(btn.data('role'));
    $('#edit_branch_id').val(btn.data('branch_id'));
    $('#editModal').modal('show');
  });
    // Open Change Password Modal
    $(document).on('click', '.changePassBtn', function() {
    var btn = $(this);
    $('#pass_user_id').val(btn.data('id'));
    $('#pass_username').val(btn.data('username'));
    $('#changePassModal').modal('show');
  });

</script>
</body>
</html>
