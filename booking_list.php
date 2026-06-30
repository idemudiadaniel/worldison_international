<?php
session_start();
include("inc/db.php");

// Restrict to specific roles
$allowedRoles = ['admin','ceo','manager', 'accountant'];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// Fetch bookings
$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

<div class="container py-4">
  <h3 class="mb-4">Service Bookings</h3>

  <div class="card shadow">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="thead-dark">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Location</th>
              <th>Services</th>
              <th>Date Needed</th>
              <th>Urgent</th>
              <th>Booked On</th>
              <th>Preview</th>
            </tr>
          </thead>
          <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['services']) ?></td>
                <td><?= $row['date_needed'] ? date("d M Y", strtotime($row['date_needed'])) : "N/A" ?></td>
                <td>
                  <?php if ($row['urgent'] === 'Yes'): ?>
                    <span class="badge badge-danger">Yes</span>
                  <?php else: ?>
                    <span class="badge badge-secondary">No</span>
                  <?php endif; ?>
                </td>
                <td><?= date("d M Y H:i", strtotime($row['created_at'])) ?></td>
                <td>
                  <button class="btn btn-sm btn-info"
                    onclick="previewBooking(
                      '<?= htmlspecialchars($row['full_name']) ?>',
                      '<?= htmlspecialchars($row['email']) ?>',
                      '<?= htmlspecialchars($row['location']) ?>',
                      '<?= htmlspecialchars($row['services']) ?>',
                      '<?= $row['date_needed'] ? date("d M Y", strtotime($row['date_needed'])) : "N/A" ?>',
                      '<?= $row['urgent'] ?>',
                      '<?= date("d M Y H:i", strtotime($row['created_at'])) ?>'
                    )">
                    Preview
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Booking Preview</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <h4 id="previewName"></h4>
        <p><strong>Email:</strong> <span id="previewEmail"></span></p>
        <p><strong>Location:</strong> <span id="previewLocation"></span></p>
        <p><strong>Services:</strong> <span id="previewServices"></span></p>
        <p><strong>Date Needed:</strong> <span id="previewDate"></span></p>
        <p><strong>Urgent:</strong> <span id="previewUrgent"></span></p>
        <p><strong>Booked On:</strong> <span id="previewCreated"></span></p>
      </div>
    </div>
  </div>
</div>

</div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>

<script>
function previewBooking(name, email, location, services, date, urgent, created){
  document.getElementById("previewName").innerText = name;
  document.getElementById("previewEmail").innerText = email;
  document.getElementById("previewLocation").innerText = location;
  document.getElementById("previewServices").innerText = services;
  document.getElementById("previewDate").innerText = date;
  document.getElementById("previewUrgent").innerText = urgent;
  document.getElementById("previewCreated").innerText = created;

  $('#previewModal').modal('show'); 
}
</script>
<?php include("inc/script.php"); ?>script>
    <!-- End custom js for this page -->
  </body>
</html>
