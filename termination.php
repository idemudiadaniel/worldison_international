<?php
include("inc/db.php");
session_start();

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ceo',])) {
  header("Location: dashboard.php");
  exit;
}
// ✅ Handle termination
if (isset($_POST['terminate'])) {
  $user_id = intval($_POST['user_id']);
  $reason  = $_POST['reason'];

  if (!empty($reason)) {
      // Insert into terminations table
      $stmt = $conn->prepare("INSERT INTO terminations (user_id, reason) VALUES (?, ?)");
      $stmt->bind_param("is", $user_id, $reason);
      $stmt->execute();

      // Mark user as terminated
      $stmt3 = $conn->prepare("UPDATE users SET status='terminated' WHERE id=?");
      $stmt3->bind_param("i", $user_id);
      $stmt3->execute();

      // Remove from payroll automatically
      $stmtDel = $conn->prepare("DELETE FROM payroll WHERE user_id=?");
      $stmtDel->bind_param("i", $user_id);
      $stmtDel->execute();
  }
}

// ✅ Handle reactivation
if (isset($_POST['reactivate'])) {
    $user_id = intval($_POST['user_id']);

    // Reactivate user
    $stmt4 = $conn->prepare("UPDATE users SET status='active' WHERE id=?");
    if ($stmt4) {
        $stmt4->bind_param("i", $user_id);
        $stmt4->execute();
    }

    // Re-add to payroll with default values
    $stmt5 = $conn->prepare("INSERT INTO payroll (user_id, basic_salary, allowances, deductions, days_absent, bonuses) 
                             VALUES (?, 0, 0, 0, 0, 0)");
    if ($stmt5) {
        $stmt5->bind_param("i", $user_id);
        $stmt5->execute();
    }
}

// ✅ Search users
$search = isset($_GET['search']) ? "%".$_GET['search']."%" : "%";
$stmt = $conn->prepare("SELECT id, full_name, email, phone, department, job_title, role, status 
                        FROM users 
                        WHERE full_name LIKE ? OR email LIKE ? OR role LIKE ?
                        ORDER BY created_at DESC");
if (!$stmt) {
    die("SQL Error (user select): " . $conn->error);
}
$stmt->bind_param("sss", $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
      <div class="main-panel">
        <div class="content-wrapper">

          <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-account-multiple"></i>
              </span> Manage User Termination
            </h3>
          </div>

          <!-- Search Bar -->
          <form method="GET" class="mb-3">
            <div class="input-group">
              <input type="text" class="form-control" name="search" 
                     value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                     placeholder="Search by name, email, or role...">
              <div class="input-group-append">
                <button class="btn btn-gradient-primary" type="submit">Search</button>
              </div>
            </div>
          </form>

          <!-- Staff Table -->
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">All Staff</h4>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Department</th>
                      <th>Job Title</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['full_name']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td><?= htmlspecialchars($row['department']); ?></td>
                        <td><?= htmlspecialchars($row['job_title']); ?></td>
                        <td><?= ucfirst($row['role']); ?></td>
                        <td>
                          <?php if ($row['status'] == 'terminated'): ?>
                            <span class="badge badge-danger">Terminated</span>
                          <?php else: ?>
                            <span class="badge badge-success">Active</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($row['status'] != 'terminated'): ?>
                          <!-- Terminate -->
                          <form method="POST" style="display:inline-block; min-width:200px;">
                            <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                            <select name="reason" class="form-control mb-2" required>
                              <option value="">-- Reason --</option>
                              <option value="resignation">Resignation</option>
                              <option value="abscondment">Abscondment</option>
                              <option value="retirement">Retirement</option>
                              <option value="misconduct">Misconduct</option>
                              <option value="layoff">Layoff</option>
                            </select>
                            <button type="submit" name="terminate" class="btn btn-sm btn-danger">Terminate</button>
                          </form>
                          <?php else: ?>
                          <!-- Reactivate -->
                          <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                            <button type="submit" name="reactivate" class="btn btn-sm btn-success">Reactivate</button>
                          </form>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <?php include("inc/footer.php"); ?>
        </div>
      </div>
    </div>
  </div>
  <?php include("inc/script.php"); ?>
  </body>
</html>
