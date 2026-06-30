<?php
include("inc/init.php");
include("inc/db.php");

// Restrict access
$allowedRoles = ['admin', 'ceo','staff', 'manager', 'accountant', 'editor'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Handle Delete (CEO only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_letter']) && $_SESSION['role'] === 'ceo') {
    $id = intval($_POST['letter_id']);
    $stmt = $conn->prepare("DELETE FROM appointment_letters WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Appointment letter deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Failed to delete letter: " . htmlspecialchars($conn->error) . "</div>";
    }
}

// Fetch all appointment letters
$sql = "
  SELECT a.*, u.full_name, u.email 
  FROM appointment_letters a
  JOIN users u ON a.user_id = u.id
  ORDER BY a.date_sent DESC
";
$result = $conn->query($sql);
if (!$result) {
    die('❌ Error fetching letters: ' . $conn->error);
}
$letters = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<body>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
      <h3 class="page-title">Appointment Letters</h3>
      <a href="send_appointment.php" class="btn btn-sm btn-primary">
        <i class="mdi mdi-plus"></i> Send New Letter
      </a>
    </div>

    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">All Sent Letters</h4>

            <?php if (count($letters) > 0): ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Subject</th>
                      <th>Recipient</th>
                      <th>Sent By</th>
                      <th>Date Sent</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($letters as $i => $letter): ?>
                      <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($letter['subject']) ?></td>
                        <td>
                          <?= htmlspecialchars($letter['full_name']) ?><br>
                          <small><?= htmlspecialchars($letter['email']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($letter['sent_by']) ?></td>
                        <td><?= htmlspecialchars($letter['date_sent']) ?></td>
                        <td>
                          <!-- ✅ Bootstrap 4 Modal Trigger -->
                          <button type="button" class="btn btn-sm btn-info"
                                  data-toggle="modal"
                                  data-target="#viewModal<?= $letter['id'] ?>">
                            <i class="mdi mdi-eye"></i>
                          </button>

                          <!-- Delete Button (CEO only) -->
                          <?php if ($_SESSION['role'] === 'ceo'): ?>
                            <form method="POST" style="display:inline;">
                              <input type="hidden" name="letter_id" value="<?= intval($letter['id']) ?>">
                              <button type="submit" name="delete_letter" class="btn btn-sm btn-danger"
                                      onclick="return confirm('Delete this letter permanently?')">
                                <i class="mdi mdi-delete"></i>
                              </button>
                            </form>
                          <?php endif; ?>
                        </td>
                      </tr>

                      <!-- ✅ Bootstrap 4 Modal -->
                      <div class="modal fade" id="viewModal<?= $letter['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?= $letter['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="viewModalLabel<?= $letter['id'] ?>">
                                Appointment Letter - <?= htmlspecialchars($letter['subject']) ?>
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <p><strong>To:</strong> <?= htmlspecialchars($letter['full_name']) ?> (<?= htmlspecialchars($letter['email']) ?>)</p>
                              <hr>
                              <p><?= nl2br(htmlspecialchars($letter['message'])) ?></p>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>

                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="alert alert-info">ℹ️ No appointment letters found.</div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include("inc/footer.php"); ?>
<?php include("inc/script.php"); ?>
</body>
</html>
