<?php
include("inc/db.php");
session_start();

// ✅ Restrict to specific roles
$allowedRoles = ['admin', 'ceo'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// ✅ Get user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}
$previewUser_id = intval($_GET['id']);
// ✅ Fetch user details along with branch name
$sql = "SELECT u.*, b.branch_name 
        FROM users u 
        LEFT JOIN branches b ON u.branch_id = b.branch_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $previewUser_id);
$stmt->execute();
$result = $stmt->get_result();
$previewUser = $result->fetch_assoc();
if (!$previewUser) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Staff Profile Preview</h4>

            <div class="row">
            <div class="col-md-4 text-center">
              <?php 
                // Pick the image to display (priority: document_picture > profile_picture > default)
                $displayPicture = null;

                if (!empty($previewUser['document_picture'])) {
                    $displayPicture = "uploads/" . htmlspecialchars($previewUser['document_picture']);
                } elseif (!empty($previewUser['profile_picture'])) {
                    $displayPicture = "uploads/" . htmlspecialchars($previewUser['profile_picture']);
                } else {
                    $displayPicture = "assets/images/default-avatar.png";
                }
              ?>
              
              <img src="<?= $displayPicture ?>" 
                  class="img-fluid rounded-circle mb-3" width="150" alt="Profile Picture">

              <h5><?= htmlspecialchars($previewUser['full_name']) ?></h5>
              <p class="text-muted">
                <?= htmlspecialchars($previewUser['job_title']) ?> - <?= htmlspecialchars($previewUser['department']) ?>
              </p>
            </div>


              <div class="col-md-8">
                <table class="table table-bordered">
                  <tr><th>Staff ID</th><td><?= htmlspecialchars($previewUser['staff_id']) ?></td></tr>
                  <tr><th>Email</th><td><?= htmlspecialchars($previewUser['email']) ?></td></tr>
                  <tr><th>Phone</th><td><?= htmlspecialchars($previewUser['phone']) ?></td></tr>
                  <tr><th>Address</th><td><?= htmlspecialchars($previewUser['address']) ?></td></tr>
                  <tr><th>Gender</th><td><?= htmlspecialchars($previewUser['gender']) ?></td></tr>
                  <tr><th>Date of Birth</th><td><?= htmlspecialchars($previewUser['dob']) ?></td></tr>
                  <tr><th>Marital Status</th><td><?= htmlspecialchars($previewUser['marital_status']) ?></td></tr>
                  <tr><th>State of Origin</th><td><?= htmlspecialchars($previewUser['state_of_origin']) ?></td></tr>
                  <tr><th>LGA of Origin</th><td><?= htmlspecialchars($previewUser['lga_of_origin']) ?></td></tr>
                  <tr><th>Country of Origin</th><td><?= htmlspecialchars($previewUser['country_of_origin']) ?></td></tr>
                  <tr><th>Hire Date</th><td><?= htmlspecialchars($previewUser['hire_date']) ?></td></tr>
                  <tr><th>Employment Type</th><td><?= htmlspecialchars($previewUser['employment_type']) ?></td></tr>
                  <tr><th>Work Location</th><td><?= htmlspecialchars($previewUser['work_location']) ?></td></tr>
                  <tr><th>Branch</th><td><?= $previewUser['branch_name'] ? htmlspecialchars($previewUser['branch_name']) : 'Not assigned' ?></td></tr>
                  <tr><th>National ID</th><td><?= htmlspecialchars($previewUser['national_id']) ?></td></tr>
                  <tr><th>Tax ID</th><td><?= htmlspecialchars($previewUser['tax_id']) ?></td></tr>
                  <tr><th>Bank Account</th><td><?= htmlspecialchars($previewUser['bank_account']) ?></td></tr>
                  <tr><th>Next of Kin</th><td><?= htmlspecialchars($previewUser['next_of_kin']) ?></td></tr>
                  <tr><th>Next of Kin Contact</th><td><?= htmlspecialchars($previewUser['next_of_kin_contact']) ?></td></tr>
                  <tr><th>Guarantor</th><td><?= nl2br(htmlspecialchars($previewUser['guarantor_details'])) ?></td></tr>
                  <tr><th>Previous Work Experience</th><td><?= nl2br(htmlspecialchars($previewUser['previous_work_experience'])) ?></td></tr>
                  <tr><th>Role</th><td><?= htmlspecialchars($previewUser['role']) ?></td></tr>
                  <tr>
                  <th>Academic Certificate</th>
                  <td>
                    <?php if ($previewUser['academic_certificate']): ?>
                      <a href="uploads/<?= htmlspecialchars($previewUser['academic_certificate']) ?>" target="_blank">
                        View
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['academic_certificate']) ?>" 
                        class="btn btn-sm btn-outline-success">Download</a>
                    <?php else: ?>
                      <span class="text-muted">Not uploaded</span>
                    <?php endif; ?>
                  </td>
                </tr>

                <tr>
                  <th>Other Certificate</th>
                  <td>
                    <?php if ($previewUser['other_certificate']): ?>
                      <a href="uploads/<?= htmlspecialchars($previewUser['other_certificate']) ?>" target="_blank">
                        View
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['other_certificate']) ?>" 
                        class="btn btn-sm btn-outline-success">Download</a>
                    <?php else: ?>
                      <span class="text-muted">Not uploaded</span>
                    <?php endif; ?>
                  </td>
                </tr>

                <tr>
                  <th>Staff Documents</th>
                  <td>
                    <?php if ($previewUser['staff_documents']): ?>
                      <a href="uploads/<?= htmlspecialchars($previewUser['staff_documents']) ?>" target="_blank">
                        View
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['staff_documents']) ?>" 
                        class="btn btn-sm btn-outline-success">Download</a>
                    <?php else: ?>
                      <span class="text-muted">Not uploaded</span>
                    <?php endif; ?>
                  </td>
                </tr>

                </table>

                <!-- ✅ Certificates / Documents -->
                <h5>Documents</h5>
                <ul>
                  <?php if ($previewUser['academic_certificate']): ?>
                    <li>
                      <a href="uploads/<?= htmlspecialchars($previewUser['academic_certificate']) ?>" target="_blank">
                        View Academic Certificate
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['academic_certificate']) ?>" 
                         class="btn btn-sm btn-outline-success">Download</a>
                    </li>
                  <?php endif; ?>

                  <?php if ($previewUser['other_certificate']): ?>
                    <li>
                      <a href="uploads/<?= htmlspecialchars($previewUser['other_certificate']) ?>" target="_blank">
                        View Other Certificate
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['other_certificate']) ?>" 
                         class="btn btn-sm btn-outline-success">Download</a>
                    </li>
                  <?php endif; ?>

                  <?php if ($previewUser['staff_documents']): ?>
                    <li>
                      <a href="uploads/<?= htmlspecialchars($previewUser['staff_documents']) ?>" target="_blank">
                        View Staff Documents
                      </a> | 
                      <a href="download_file.php?file=<?= urlencode($previewUser['staff_documents']) ?>" 
                         class="btn btn-sm btn-outline-success">Download</a>
                    </li>
                  <?php endif; ?>
                </ul>

                <a href="manage_profile.php" class="btn btn-light">Back</a>
                <a href="download_profile.php?id=<?= $previewUser['id'] ?>" 
                   class="btn btn-gradient-primary">Download Profile</a>
              </div>
            </div>

          </div>
        </div>

      </div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>

<?php include("inc/script.php"); ?>
</body>
</html>
