<?php
session_start();
include("inc/db.php"); // ✅ corrected path

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo','manager'])) {
  header("Location: dashboard.php");
  exit;
}

// Handle approve/reject
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch attendance logs with staff details
$sql = "SELECT a.*, u.full_name, u.job_title
        FROM attendance a
        JOIN users u ON a.staff_id = u.staff_id
        ORDER BY a.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <div class="row">
          <div class="col-12 grid-margin">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Staff Attendance Manager</h4>
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th></th>
                        <th> Staff Name </th>
                        <th> Staff ID </th>
                        <th> Time </th>
                        <th> Clock Type </th>
                        <th> Location </th>
                        <th> Date </th>
                        <th> Action </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                          <td>
                            <div class="form-check form-check-muted m-0">
                              <label class="form-check-label">
                                <input type="checkbox" class="form-check-input">
                              </label>
                            </div>
                          </td>
                          <td>
                            <img src="uploads/<?= htmlspecialchars($row['photo_path']) ?>" 
                                alt="image" style="width:40px;height:40px;border-radius:50%;" /> <!-- ✅ -->
                            <span class="pl-2"><?= htmlspecialchars($row['full_name']) ?></span>
                          </td>
                          <td><?= $row['staff_id'] ?></td>
                          <td><?= date("H:i:s", strtotime($row['resumption_time'])) ?></td>
                          <td><?= ucfirst($row['clock_type']) ?></td>
                          <td>
                            <button class="btn btn-sm btn-info" 
                              onclick="previewRecord(
                                '<?= htmlspecialchars($row['full_name']) ?>',
                                '<?= $row['staff_id'] ?>',
                                '<?= htmlspecialchars($row['job_title']) ?>',
                                '<?= ucfirst($row['clock_type']) ?>',
                                '<?= date("H:i:s", strtotime($row['resumption_time'])) ?>',
                                '<?= date("d M Y", strtotime($row['resumption_time'])) ?>',
                                'uploads/<?= htmlspecialchars($row['photo_path']) ?>', <!-- ✅ -->
                                '<?= $row['location_lat'] ?>',
                                '<?= $row['location_long'] ?>'
                              )">
                              Preview
                            </button>
                          </td>
                          <td><?= date("d M Y", strtotime($row['resumption_time'])) ?></td>
                          <td>
                            <?php if ($row['status'] === 'pending'): ?>
                              <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve" class="badge badge-success">Approve</button>
                                <button type="submit" name="action" value="reject" class="badge badge-danger">Reject</button>
                              </form>
                            <?php elseif ($row['status'] === 'approved'): ?>
                              <span class="badge badge-success">Approved</span>
                            <?php elseif ($row['status'] === 'rejected'): ?>
                              <span class="badge badge-danger">Rejected</span>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
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
                <p><strong>Time:</strong> <span id="previewTime"></span> | <strong>Date:</strong> <span id="previewDate"></span></p>
                <p><strong>Location:</strong> <span id="previewLocation">Loading...</span></p>
              </div>
            </div>
          </div>
        </div>

      </div>
      <?php include("inc/footer.php"); ?> <!-- ✅ -->
    </div>
  </div>
</div>

<script>
// Reverse Geocoding (Google API or OpenStreetMap Nominatim)
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

  $('#previewModal').modal('show'); // ✅ Bootstrap 4 syntax
}
</script>
<?php include("inc/script.php"); ?>
</body>
</html>
