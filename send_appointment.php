<?php
include("inc/init.php");
include("inc/db.php");

// Restrict access
$allowedRoles = ['admin', 'ceo'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_letter'])) {
    $user_id = intval($_POST['user_id']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $sent_by = $_SESSION['username'];

    // Validate input
    if (empty($user_id) || empty($subject) || empty($message)) {
        echo "<div class='alert alert-danger'>⚠️ Please fill all fields.</div>";
    } else {
        // Save to DB
        $stmt = $conn->prepare("INSERT INTO appointment_letters (user_id, subject, message, sent_by) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $subject, $message, $sent_by);

        if ($stmt->execute()) {
            // Fetch user email
            $stmt2 = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();
            $stmt2->bind_result($full_name, $email);
            $stmt2->fetch();
            $stmt2->close();

            // Send email (optional)
            $headers = "From: hr@yourcompany.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body = "<p>Dear <strong>$full_name</strong>,</p><p>" . nl2br($message) . "</p><p>Regards,<br>HR Department</p>";

            @mail($email, $subject, $body, $headers);

            echo "<div class='alert alert-success'>✅ Appointment letter sent successfully to $full_name ($email)</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Database error: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}

// Fetch all users for dropdown
$users = [];
$query = $conn->query("SELECT id, full_name, email FROM users ORDER BY full_name ASC");
while ($row = $query->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header"><h3 class="page-title">Send Appointment Letter</h3></div>

    <div class="row">
      <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Appointment Letter Form</h4>

            <form method="POST" class="forms-sample">
              <div class="form-group">
                <label>Select User</label>
                <select name="user_id" class="form-control" required>
                  <option value="">-- Choose a user --</option>
                  <?php foreach ($users as $user): ?>
                    <option value="<?= intval($user['id']) ?>">
                      <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group mt-3">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control" required>
              </div>

              <div class="form-group mt-3">
                <label>Message</label>
                <textarea name="message" rows="8" class="form-control" placeholder="Type the appointment letter here..." required></textarea>
              </div>

              <button type="submit" name="send_letter" class="btn btn-primary mt-3">Send Appointment Letter</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Right side: History of letters -->
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Recent Letters</h4>
            <div style="max-height:400px; overflow-y:auto;">
              <?php
              $letters = $conn->query("
                SELECT a.*, u.full_name 
                FROM appointment_letters a 
                JOIN users u ON a.user_id = u.id 
                ORDER BY a.date_sent DESC LIMIT 10
              ");
              if ($letters->num_rows > 0):
                while ($row = $letters->fetch_assoc()):
              ?>
                <div class="border-bottom mb-2 pb-2">
                  <strong><?= htmlspecialchars($row['subject']) ?></strong><br>
                  <small>To: <?= htmlspecialchars($row['full_name']) ?> | <?= htmlspecialchars($row['date_sent']) ?></small><br>
                  <small><em>By <?= htmlspecialchars($row['sent_by']) ?></em></small>
                </div>
              <?php endwhile; else: ?>
                <p>No letters sent yet.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
  <?php include("inc/footer.php"); ?>
</div>
<?php include("inc/script.php"); ?>
</body>
</html>
