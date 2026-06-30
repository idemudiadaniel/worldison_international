<?php
include("inc/db.php");
session_start();
// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo','manager',])) {
  header("Location: dashboard.php");
  exit;
}
// Handle search
$search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT id, staff_id, full_name, email, role, department, job_title 
            FROM users
            WHERE staff_id LIKE '%$search%'
               OR full_name LIKE '%$search%'
               OR email LIKE '%$search%'
               OR department LIKE '%$search%'";
} else {
    $sql = "SELECT id, staff_id, full_name, email, role, department, job_title FROM users";
}
$result = $conn->query($sql);
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
              </span> Manage User Profiles
            </h3>


            <!-- Search Bar -->
            <form method="GET" class="form-inline">
              <input type="text" name="search" class="form-control mr-2" 
                     placeholder="Search staff..." value="<?= htmlspecialchars($search) ?>">
              <button type="submit" class="btn btn-gradient-primary btn-sm">Search</button>
            </form>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Staff List</h4>
                  <p class="card-description"> All registered staff members </p>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>Staff ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Department</th>
                          <th>Job Title</th>
                          <th>Role</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($result->num_rows > 0): ?>
                          <?php while ($row = $result->fetch_assoc()): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['staff_id']) ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['department']) ?></td>
                            <td><?= htmlspecialchars($row['job_title']) ?></td>
                            <td>
                              <span class="badge badge-gradient-info"><?= ucfirst($row['role']) ?></span>
                            </td>
                            <td>
                              <a href="preview_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                <i class="mdi mdi-eye"></i> Preview
                              </a>
                              <a href="edit_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-gradient-primary">
                                <i class="mdi mdi-pencil"></i> Edit
                              </a>
                            </td>
                          </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="7" class="text-center">No staff found.</td></tr>
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
