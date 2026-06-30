<?php
include("inc/db.php");
session_start();


// ✅ Get all users with status 'terminated'
$sql = "SELECT u.id AS user_id, u.full_name, u.email, u.department, 
               u.job_title, u.role, u.status,
               t.reason, t.termination_date
        FROM users u
        LEFT JOIN terminations t ON u.id = t.user_id
        WHERE u.status = 'terminated'
        ORDER BY t.termination_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white mr-2">
              <i class="mdi mdi-account-off"></i>
            </span> Terminated Staff
          </h3>
        </div>

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Termination History</h4>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Job Title</th>
                    <th>Role</th>
                    <th>Reason</th>
                    <th>Termination Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                      <tr>
                        <td><?= htmlspecialchars($row['full_name'] ?? '', ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['email'] ?? '', ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['department'] ?? '', ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['job_title'] ?? '', ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['role'] ?? ''), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['reason'] ?? 'N/A'), ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($row['termination_date'] ?? 'N/A', ENT_QUOTES) ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">No terminated staff found.</td>
                    </tr>
                  <?php endif; ?>
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
