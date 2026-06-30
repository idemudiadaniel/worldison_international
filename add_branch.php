<?php
include("inc/init.php");
include("inc/db.php");



// Restrict access (Admin & CEO only)
$allowedRoles = ['admin', 'ceo'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

// 🗑 Handle delete request
if (isset($_GET['delete'])) {
    $branch_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM branches WHERE branch_id = ?");
    $stmt->bind_param("i", $branch_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Branch deleted successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error deleting branch: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}

// ➕ Handle add branch form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $branch_name    = trim($_POST['branch_name']);
    $branch_address = trim($_POST['branch_address']);
    $branch_city    = trim($_POST['branch_city']);
    $branch_state   = trim($_POST['branch_state']);
    $branch_country = trim($_POST['branch_country']);

    if (!empty($branch_name)) {
        $stmt = $conn->prepare("INSERT INTO branches (branch_name, branch_address, branch_city, branch_state, branch_country) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $branch_name, $branch_address, $branch_city, $branch_state, $branch_country);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Branch added successfully.</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>⚠️ Branch name is required.</div>";
    }
}

// 📋 Fetch branches
$branches = $conn->query("SELECT * FROM branches ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header"><h3 class="page-title">🏢 Manage Branches</h3></div>

        <?= $message ?>

        <div class="row">
          <!-- Add Branch Form -->
          <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Add New Branch</h4>
                <form method="POST" class="forms-sample">
                  <div class="form-group">
                    <label>Branch Name</label>
                    <input type="text" name="branch_name" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Address</label>
                    <textarea name="branch_address" class="form-control" rows="2"></textarea>
                  </div>
                  <div class="form-group">
                    <label>City</label>
                    <input type="text" name="branch_city" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>State</label>
                    <input type="text" name="branch_state" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="branch_country" class="form-control" value="Nigeria">
                  </div>
                  <button type="submit" class="btn btn-primary">Add Branch</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Branch List -->
          <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">All Branches</h4>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Branch Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Country</th>
                        <th>Created At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if ($branches && $branches->num_rows > 0): ?>
                        <?php while($b = $branches->fetch_assoc()): ?>
                          <tr>
                            <td><?= intval($b['branch_id']) ?></td>
                            <td><?= htmlspecialchars($b['branch_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($b['branch_address'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($b['branch_city'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($b['branch_state'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($b['branch_country'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($b['created_at'], ENT_QUOTES) ?></td>
                            <td>
                              <a href="?delete=<?= intval($b['branch_id']) ?>" 
                                 class="btn btn-sm btn-danger"
                                 onclick="return confirm('Delete this branch permanently?');">Delete</a>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr><td colspan="8" class="text-center">No branches found.</td></tr>
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
<?php include("inc/script.php"); ?>
</body>
</html>
