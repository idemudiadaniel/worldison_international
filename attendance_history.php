<?php
session_start();
include("inc/db.php");

// Restrict to specific roles
$allowedRoles = ['admin','ceo','manager', 'accountant','staff'];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}


// Fetch all attendance logs with staff details
$sql = "SELECT a.*, u.full_name, u.job_title
        FROM attendance a
        JOIN users u ON a.staff_id = u.staff_id
        ORDER BY a.created_at DESC";
$result = $conn->query($sql);

// Helper for safe image path
function getPhotoPath($filename) {
    if (!empty($filename) && file_exists(__DIR__ . "/uploads/" . $filename)) {
        return "uploads/" . $filename;
    }
    return "assets/images/default.jpg"; // fallback image
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

      <div class="container py-4">
  <h3 class="mb-4">Attendance History</h3>

  <div class="card shadow">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="thead-dark">
            <tr>
              <th>Staff Name</th>
              <th>Staff ID</th>
              <th>Job Title</th>
              <th>Clock Type</th>
              <th>Time</th>
              <th>Date</th>
              <th>Status</th>
              <th>Location</th>
              <th>Preview</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): 
                $time = !empty($row['resumption_time']) ? date("H:i:s", strtotime($row['resumption_time'])) : 'N/A';
                $date = !empty($row['resumption_time']) ? date("d M Y", strtotime($row['resumption_time'])) : 'N/A';
            ?>
              <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['staff_id']) ?></td>
                <td><?= htmlspecialchars($row['job_title']) ?></td>
                <td><?= ucfirst(htmlspecialchars($row['clock_type'])) ?></td>
                <td><?= $time ?></td>
                <td><?= $date ?></td>
                <td>
                  <?php 
                  switch($row['status']){
                      case 'approved': echo '<span class="badge badge-success">Approved</span>'; break;
                      case 'rejected': echo '<span class="badge badge-danger">Rejected</span>'; break;
                      default: echo '<span class="badge badge-warning">Pending</span>';
                  }
                  ?>
                </td>
                <td><?= htmlspecialchars($row['location_lat']) ?>, <?= htmlspecialchars($row['location_long']) ?></td>
                <td>
                  <button class="btn btn-sm btn-info"
                    onclick="previewRecord(
                      '<?= htmlspecialchars($row['full_name']) ?>',
                      '<?= htmlspecialchars($row['staff_id']) ?>',
                      '<?= htmlspecialchars($row['job_title']) ?>',
                      '<?= ucfirst(htmlspecialchars($row['clock_type'])) ?>',
                      '<?= $time ?>',
                      '<?= $date ?>',
                      '<?= getPhotoPath($row['photo_path']) ?>',
                      '<?= htmlspecialchars($row['location_lat']) ?>',
                      '<?= htmlspecialchars($row['location_long']) ?>'
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
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Attendance Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img id="previewPhoto" src="" style="max-width:100%;border-radius:10px;margin-bottom:15px;">
        <h4 id="previewName"></h4>
        <p><strong>Staff ID:</strong> <span id="previewStaffID"></span></p>
        <p><strong>Job Title:</strong> <span id="previewJob"></span></p>
        <p><strong>Clock Type:</strong> <span id="previewType"></span></p>
        <p><strong>Time:</strong> <span id="previewTime"></span> | 
           <strong>Date:</strong> <span id="previewDate"></span></p>
        <p><strong>Location:</strong> <span id="previewLocation">Loading...</span></p>
      </div>
    </div>
  </div>
</div>

<script>
// Reverse Geocoding (OpenStreetMap)
function getAddress(lat, long, callback){
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${long}`)
  .then(res => res.json())
  .then(data => callback(data.display_name || "Unknown Location"))
  .catch(() => callback("Unable to fetch location"));
}

function previewRecord(name, staff_id, job, type, time, date, photo, lat, long){
  document.getElementById("previewPhoto").src = photo;
  document.getElementById("previewName").innerText = name;
  document.getElementById("previewStaffID").innerText = staff_id;
  document.getElementById("previewJob").innerText = job;
  document.getElementById("previewType").innerText = type;
  document.getElementById("previewTime").innerText = time;
  document.getElementById("previewDate").innerText = date;
  document.getElementById("previewLocation").innerText = "Loading...";

  getAddress(lat, long, function(addr){
    document.getElementById("previewLocation").innerText = addr;
  });

  $('#previewModal').modal('show'); 
}
</script>


<?php include("inc/script.php"); ?>
</boby>
</html>
