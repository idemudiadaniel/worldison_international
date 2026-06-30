<?php
include("inc/init.php");
include("inc/db.php");

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';

// Fetch user info
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();
$stmt->close();

// Fetch user's appointment letters
$stmt2 = $conn->prepare("
    SELECT id, subject, message, sent_by, date_sent 
    FROM appointment_letters 
    WHERE user_id = ? 
    ORDER BY date_sent DESC
");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result = $stmt2->get_result();
$letters = $result->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<body>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">My Appointment Letters</h3>
    </div>

    <div class="row">
      <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Dear <?= htmlspecialchars($full_name) ?>,</h4>
            <p class="mb-4 text-muted">Below are your appointment letters assigned to you by HR.</p>

            <?php if (count($letters) > 0): ?>
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Subject</th>
                      <th>Sent By</th>
                      <th>Date Sent</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($letters as $i => $letter): ?>
                      <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($letter['subject']) ?></td>
                        <td><?= htmlspecialchars($letter['sent_by']) ?></td>
                        <td><?= htmlspecialchars($letter['date_sent']) ?></td>
                        <td>
                          <!-- ✅ Bootstrap 4 modal trigger -->
                          <button 
                            type="button" 
                            class="btn btn-sm btn-info"
                            data-toggle="modal" 
                            data-target="#viewModal<?= $letter['id'] ?>">
                            <i class="mdi mdi-eye"></i> View
                          </button>
                        </td>
                      </tr>

                      <!-- ✅ Modal (Bootstrap 4 syntax) -->
                      <div class="modal fade" id="viewModal<?= $letter['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?= $letter['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="viewModalLabel<?= $letter['id'] ?>">
                                <?= htmlspecialchars($letter['subject']) ?>
                              </h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <p><strong>To:</strong> <?= htmlspecialchars($full_name) ?> (<?= htmlspecialchars($email) ?>)</p>
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
              <div class="alert alert-info">
                ℹ️ You don’t have any appointment letters yet. Please check back later.
              </div>
            <?php endif; ?>
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