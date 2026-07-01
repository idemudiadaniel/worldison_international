<?php
include("inc/db.php");
require_once "inc/upload.php";
session_start();

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo','manager',])) {
  header("Location: dashboard.php");
  exit;
}

requireCsrf();
// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}
$previewUser_id = (int) $_GET['id'];

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $previewUser_id);
$stmt->execute();
$result = $stmt->get_result();
$previewUser = $result->fetch_assoc();
$stmt->close();

if (!$previewUser) {
    die("User not found.");
}
// Fetch all branches
$branches = [];
$branchQuery = $conn->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name ASC");
if ($branchQuery && $branchQuery->num_rows > 0) {
    while ($b = $branchQuery->fetch_assoc()) {
        $branches[] = $b;
    }
}

// No local upload helper here; use shared upload validation from inc/upload.php.

// Choose display picture: prefer document picture, fallback to profile picture
$displayPicture = !empty($previewUser['document_picture']) 
    ? $previewUser['document_picture'] 
    : $previewUser['profile_picture'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Collect form inputs
        $staff_id       = $_POST['staff_id'] ?? '';
        $full_name      = $_POST['full_name'] ?? '';
        $email          = $_POST['email'] ?? '';
        $phone          = $_POST['phone'] ?? '';
        $address        = $_POST['address'] ?? '';
        $gender         = $_POST['gender'] ?? '';
        $dob            = $_POST['dob'] ?? null;
        $marital_status = $_POST['marital_status'] ?? '';
        $state_of_origin= $_POST['state_of_origin'] ?? '';
        $lga_of_origin  = $_POST['lga_of_origin'] ?? '';
        $country_of_origin = $_POST['country_of_origin'] ?? '';
        $department     = $_POST['department'] ?? '';
        $job_title      = $_POST['job_title'] ?? '';
        $hire_date      = $_POST['hire_date'] ?? null;
        $employment_type= $_POST['employment_type'] ?? '';
        $work_location  = $_POST['work_location'] ?? '';
        $previous_work_experience = $_POST['previous_work_experience'] ?? '';
        $national_id    = $_POST['national_id'] ?? '';
        $tax_id         = $_POST['tax_id'] ?? '';
        $bank_account   = $_POST['bank_account'] ?? '';
        $next_of_kin    = $_POST['next_of_kin'] ?? '';
        $next_of_kin_contact = $_POST['next_of_kin_contact'] ?? '';
        $guarantor_details = $_POST['guarantor_details'] ?? '';
        $previewUsername= $_POST['username'] ?? $previewUser['username'];
        $role           = $_POST['role'] ?? $previewUser['role'];

        // File uploads
        $academic_certificate = $previewUser['academic_certificate'];
        $other_certificate    = $previewUser['other_certificate'];
        $staff_documents      = $previewUser['staff_documents'];
        $document_picture     = $previewUser['document_picture'];

        if (!empty($_FILES['academic_certificate']['name'])) {
            $academic_certificate = validateUploadFile('academic_certificate', __DIR__ . '/uploads/', ['jpg','jpeg','png','pdf','doc','docx'], UPLOAD_MAX_DOCUMENT_BYTES);
        }
        if (!empty($_FILES['other_certificate']['name'])) {
            $other_certificate = validateUploadFile('other_certificate', __DIR__ . '/uploads/', ['jpg','jpeg','png','pdf','doc','docx'], UPLOAD_MAX_DOCUMENT_BYTES);
        }
        if (!empty($_FILES['staff_documents']['name'])) {
            $staff_documents = validateUploadFile('staff_documents', __DIR__ . '/uploads/', ['jpg','jpeg','png','pdf','doc','docx','zip'], UPLOAD_MAX_DOCUMENT_BYTES);
        }
        if (!empty($_FILES['document_picture']['name'])) {
            $document_picture = validateUploadFile('document_picture', __DIR__ . '/uploads/', ['jpg','jpeg','png','gif','webp'], UPLOAD_MAX_IMAGE_BYTES);
        }

        // Always keep existing profile_picture unless changed in a separate form
        $profile_picture = $previewUser['profile_picture'];

        // Update query
        $update = $conn->prepare("UPDATE users SET 
          staff_id=?, full_name=?, email=?, phone=?, address=?, gender=?, dob=?, marital_status=?, 
          state_of_origin=?, lga_of_origin=?, country_of_origin=?, 
          department=?, job_title=?, hire_date=?, employment_type=?, work_location=?, previous_work_experience=?, 
          national_id=?, tax_id=?, bank_account=?, next_of_kin=?, next_of_kin_contact=?, guarantor_details=?, 
          academic_certificate=?, other_certificate=?, staff_documents=?, document_picture=?, profile_picture=?, 
          username=?, role=?, branch_id=? 
          WHERE id=?");


        $update->bind_param(
          "ssssssssssssssssssssssssssssssii",
          $staff_id, $full_name, $email, $phone, $address, $gender, $dob, $marital_status,
          $state_of_origin, $lga_of_origin, $country_of_origin,
          $department, $job_title, $hire_date, $employment_type, $work_location, $previous_work_experience,
          $national_id, $tax_id, $bank_account, $next_of_kin, $next_of_kin_contact, $guarantor_details,
          $academic_certificate, $other_certificate, $staff_documents, $document_picture, $profile_picture,
          $previewUsername, $role, $_POST['branch_id'], $previewUser_id
        );


        if ($update->execute()) {
            header("Location: manage_profile.php?msg=updated");
            exit;
        } else {
            $error = "Update failed: " . $update->error;
        }
        $update->close();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Edit User Profile</h4>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
              <div class="row">

                <!-- Personal Info -->
                <div class="col-md-6 form-group">
                  <label>Staff ID</label>
                  <input type="text" name="staff_id" class="form-control" value="<?= htmlspecialchars($previewUser['staff_id']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Full Name</label>
                  <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($previewUser['full_name']) ?>" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($previewUser['email']) ?>" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Phone</label>
                  <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($previewUser['phone']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Address</label>
                  <textarea name="address" class="form-control"><?= htmlspecialchars($previewUser['address']) ?></textarea>
                </div>
                <div class="col-md-6 form-group">
                  <label>Gender</label>
                  <select name="gender" class="form-control">
                    <option value="Male" <?= $previewUser['gender']=='Male'?'selected':'' ?>>Male</option>
                    <option value="Female" <?= $previewUser['gender']=='Female'?'selected':'' ?>>Female</option>
                    <option value="Other" <?= $previewUser['gender']=='Other'?'selected':'' ?>>Other</option>
                  </select>
                </div>
                <div class="col-md-6 form-group">
                  <label>Date of Birth</label>
                  <input type="date" name="dob" class="form-control" value="<?= $previewUser['dob'] ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Marital Status</label>
                  <input type="text" name="marital_status" class="form-control" value="<?= htmlspecialchars($previewUser['marital_status']) ?>">
                </div>

                <!-- Job Info -->
                <div class="col-md-6 form-group">
                  <label>Department</label>
                  <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($previewUser['department']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Job Title</label>
                  <input type="text" name="job_title" class="form-control" value="<?= htmlspecialchars($previewUser['job_title']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Hire Date</label>
                  <input type="date" name="hire_date" class="form-control" value="<?= $previewUser['hire_date'] ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Employment Type</label>
                  <select name="employment_type" class="form-control">
                    <option value="Full-time" <?= $previewUser['employment_type']=='Full-time'?'selected':'' ?>>Full-time</option>
                    <option value="Part-time" <?= $previewUser['employment_type']=='Part-time'?'selected':'' ?>>Part-time</option>
                    <option value="Contract" <?= $previewUser['employment_type']=='Contract'?'selected':'' ?>>Contract</option>
                  </select>
                </div>
                <div class="col-md-6 form-group">
                  <label>Work Location</label>
                  <input type="text" name="work_location" class="form-control" value="<?= htmlspecialchars($previewUser['work_location']) ?>">
                </div>

                <!-- IDs -->
                <div class="col-md-6 form-group">
                  <label>National ID</label>
                  <input type="text" name="national_id" class="form-control" value="<?= htmlspecialchars($previewUser['national_id']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Tax ID</label>
                  <input type="text" name="tax_id" class="form-control" value="<?= htmlspecialchars($previewUser['tax_id']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Bank Account</label>
                  <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($previewUser['bank_account']) ?>">
                </div>

                <!-- Emergency -->
                <div class="col-md-6 form-group">
                  <label>Next of Kin</label>
                  <input type="text" name="next_of_kin" class="form-control" value="<?= htmlspecialchars($previewUser['next_of_kin']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Next of Kin Contact</label>
                  <input type="text" name="next_of_kin_contact" class="form-control" value="<?= htmlspecialchars($previewUser['next_of_kin_contact']) ?>">
                </div>

                <!-- Account -->
                <div class="col-md-6 form-group">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($previewUser['username']) ?>" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Role</label>
                  <select name="role" class="form-control">
                    <option value="staff" <?= $previewUser['role']=='staff'?'selected':'' ?>>Staff</option>
                    <option value="admin" <?= $previewUser['role']=='admin'?'selected':'' ?>>Admin</option>
                  </select>
                </div>
                 <!-- Extra Origin Details -->
                 <div class="col-md-6 form-group">
                  <label>State of Origin</label>
                  <input type="text" name="state_of_origin" class="form-control" value="<?= htmlspecialchars($previewUser['state_of_origin']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>LGA of Origin</label>
                  <input type="text" name="lga_of_origin" class="form-control" value="<?= htmlspecialchars($previewUser['lga_of_origin']) ?>">
                </div>
                <div class="col-md-6 form-group">
                  <label>Country of Origin</label>
                  <input type="text" name="country_of_origin" class="form-control" value="<?= htmlspecialchars($previewUser['country_of_origin']) ?>">
                </div>

                <!-- Work Experience -->
                <div class="col-md-12 form-group">
                  <label>Previous Work Experience</label>
                  <textarea name="previous_work_experience" class="form-control"><?= htmlspecialchars($previewUser['previous_work_experience']) ?></textarea>
                </div>
                <div class="col-md-6 form-group">
                  <label>Branch</label>
                  <select name="branch_id" class="form-control" required>
                    <option value="">-- Select Branch --</option>
                    <?php foreach ($branches as $b): ?>
                      <option value="<?= $b['branch_id'] ?>" <?= ($previewUser['branch_id'] == $b['branch_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['branch_name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Guarantor -->
                <div class="col-md-12 form-group">
                  <label>Guarantor Details</label>
                  <textarea name="guarantor_details" class="form-control"><?= htmlspecialchars($previewUser['guarantor_details']) ?></textarea>
                </div>

                <div class="col-md-6 form-group">
                  <label>Document Picture</label>
                  <input type="file" name="document_picture" class="form-control">
                  <?php if ($displayPicture): ?>
                    <small>Current: 
                      <img src="uploads/<?= $displayPicture ?>" width="80" style="border-radius:50%">
                    </small>
                  <?php else: ?>
                    <small>No picture uploaded</small>
                  <?php endif; ?>
                </div>
                <!-- Certificates -->
                <div class="col-md-6 form-group">
                  <label>Academic Certificate</label>
                  <input type="file" name="academic_certificate" class="form-control">
                  <?php if ($previewUser['academic_certificate']): ?>
                    <small>Current: <a href="uploads/<?= $previewUser['academic_certificate'] ?>" target="_blank">View</a></small>
                  <?php endif; ?>
                </div>
                <div class="col-md-6 form-group">
                  <label>Other Certificate</label>
                  <input type="file" name="other_certificate" class="form-control">
                  <?php if ($previewUser['other_certificate']): ?>
                    <small>Current: <a href="uploads/<?= $previewUser['other_certificate'] ?>" target="_blank">View</a></small>
                  <?php endif; ?>
                </div>
                <div class="col-md-12 form-group">
                  <label>Staff Documents (ZIP/PDF)</label>
                  <input type="file" name="staff_documents" class="form-control">
                  <?php if ($previewUser['staff_documents']): ?>
                    <small>Current: <a href="uploads/<?= $previewUser['staff_documents'] ?>" target="_blank">Download</a></small>
                  <?php endif; ?>
                </div>
              </div>

              <button type="submit" class="btn btn-gradient-primary">Save Changes</button>
              <a href="manage_profile.php" class="btn btn-light">Cancel</a>
            </form>
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
