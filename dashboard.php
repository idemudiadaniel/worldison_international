<?php
include("inc/init.php");
include("inc/db.php");

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','accountant','ceo','manager','editor','staff'])) {
  header("Location: dashboard.php");
  exit;
}
// Prevent undefined index warnings
$user_role = $_SESSION['user_role'] ?? null;

// Helper function to calculate percentage change
function percentChange($today, $yesterday) {
    if ($yesterday == 0) return $today > 0 ? 100 : 0;
    return round((($today - $yesterday) / $yesterday) * 100, 2);
}

// Get total active staff
$totalStaff = $conn->query("SELECT COUNT(*) as total FROM users WHERE status='active'")->fetch_assoc()['total'];

// ----------------- Total Clock-Ins -----------------
$totalToday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(resumption_time) = CURDATE()")->fetch_assoc()['total'];
$totalYesterday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE DATE(resumption_time) = CURDATE() - INTERVAL 1 DAY")->fetch_assoc()['total'];
$totalClockIn = $totalToday; // only today
$totalChange = percentChange($totalToday, $totalYesterday);
$clockedInPercent = $totalStaff ? round(($totalClockIn / $totalStaff) * 100, 2) : 0;

// ----------------- Approved Clock-Ins -----------------
$approvedToday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='approved' AND DATE(resumption_time) = CURDATE()")->fetch_assoc()['total'];
$approvedYesterday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='approved' AND DATE(resumption_time) = CURDATE() - INTERVAL 1 DAY")->fetch_assoc()['total'];
$approvedClockIn = $approvedToday;
$approvedChange = percentChange($approvedToday, $approvedYesterday);
$approvedPercent = $totalStaff ? round(($approvedClockIn / $totalStaff) * 100, 2) : 0;

// ----------------- Rejected Clock-Ins -----------------
$rejectedToday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='rejected' AND DATE(resumption_time) = CURDATE()")->fetch_assoc()['total'];
$rejectedYesterday = $conn->query("SELECT COUNT(*) as total FROM attendance WHERE status='rejected' AND DATE(resumption_time) = CURDATE() - INTERVAL 1 DAY")->fetch_assoc()['total'];
$rejectedClockIn = $rejectedToday;
$rejectedChange = percentChange($rejectedToday, $rejectedYesterday);
$rejectedPercent = $totalStaff ? round(($rejectedClockIn / $totalStaff) * 100, 2) : 0;

// Handle approve/reject actions
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch first 5 customers
$customerResult = $conn->query("
    SELECT customer_id, full_name, email, phone, service_rendered, amount, date_served, staff_in_charge
    FROM customers
    ORDER BY id DESC
    LIMIT 5
");

// Fetch attendance logs with staff details
$attendanceResult = $conn->query("
    SELECT a.*, u.full_name, u.job_title
    FROM attendance a
    JOIN users u ON a.staff_id = u.staff_id
    ORDER BY a.created_at DESC
    LIMIT 5
");



// Determine filter
$filter = $_GET['filter'] ?? 'today';
$where = "";

switch($filter){
    case 'week':
        $where = "WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $where = "WHERE MONTH(visited_at) = MONTH(CURDATE()) AND YEAR(visited_at) = YEAR(CURDATE())";
        break;
    default: // today
        $where = "WHERE DATE(visited_at) = CURDATE()";
}

// Fetch visitors grouped by country
$sql = "SELECT country, COUNT(*) AS total 
        FROM landing_visitors
        $where
        GROUP BY country
        ORDER BY total DESC";
$result = $conn->query($sql);

$visitors_by_country = [];
$total_visitors = 0;

if($result){
    while($row = $result->fetch_assoc()){
        $visitors_by_country[] = $row;
        $total_visitors += $row['total'];
    }
}

// Prepare JS-friendly data for vector map
$map_data = [];
$country_iso = [
    "United States" => "US",
    "Germany" => "DE",
    "Australia" => "AU",
    "United Kingdom" => "GB",
    "Romania" => "RO",
    "Brazil" => "BR",
    // add more as needed
];

foreach($visitors_by_country as $row){
    $iso = $country_iso[$row['country']] ?? null;
    if($iso){
        $map_data[$iso] = (int)$row['total'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
      <!-- Main Panel -->
      <div class="main-panel">
        <div class="content-wrapper">
        <div class="row">


        <h3 class="page-title">Welcome, <?php echo $_SESSION['username']; ?> 👋</h3>

<div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">My Profile</h4>
        <?php
        $id = $_SESSION['user_id'];
        $result = $conn->query("SELECT * FROM users WHERE id=$id");
        $user = $result->fetch_assoc();
        ?>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Staff ID:</strong> <?php echo $user['staff_id']; ?></p>
        <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
        <p><strong>Employment Type:</strong> <?php echo ($user['employment_type']); ?></p>
        <p><strong>Job Title:</strong> <?php echo ($user['job_title']); ?></p>
        <p><strong>Branch Name:</strong> <?php echo ($user['branch_name']); ?></p>
                          <button id="installBtn" style="display:none;" class="btn btn-primary btn-block">
  Install App
</button>

        <!-- Later we can add profile editing -->
      </div>
    </div>
  </div>
</div>
        <?php if (canSee('admin','ceo','manager')): ?>
          <div class="row">
                <!-- Total Clock-In -->
                <div class="col-sm-4 grid-margin">
                  <div class="card">
                    <div class="card-body">
                      <h5>Number of Clock-Ins Today</h5>
                      <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                          <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0"><?= $totalClockIn ?></h2>
                            <p class="text-<?= $totalChange >= 0 ? 'success' : 'danger' ?> ml-2 mb-0 font-weight-medium">
                              <?= ($totalChange >= 0 ? '+' : '') . $totalChange ?>% vs yesterday
                            </p>
                          </div>
                          <h6 class="text-muted font-weight-normal"><?= $totalStaff ? $clockedInPercent : 0 ?>% of active staff clocked in</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                          <i class="icon-lg mdi mdi-codepen text-primary ml-auto"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Rejected Clock-Ins -->
                <div class="col-sm-4 grid-margin">
                  <div class="card">
                    <div class="card-body">
                      <h5>Rejected Clock-Ins Today</h5>
                      <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                          <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0"><?= $rejectedClockIn ?></h2>
                            <p class="text-<?= $rejectedChange >= 0 ? 'danger' : 'success' ?> ml-2 mb-0 font-weight-medium">
                              <?= ($rejectedChange >= 0 ? '+' : '') . $rejectedChange ?>% vs yesterday
                            </p>
                          </div>
                          <h6 class="text-muted font-weight-normal"><?= $totalStaff ? $rejectedPercent : 0 ?>% of active staff rejected</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                          <i class="icon-lg mdi mdi-wallet-travel text-danger ml-auto"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Approved Clock-Ins -->
                <div class="col-sm-4 grid-margin">
                  <div class="card">
                    <div class="card-body">
                      <h5>Approved Clock-Ins Today</h5>
                      <div class="row">
                        <div class="col-8 col-sm-12 col-xl-8 my-auto">
                          <div class="d-flex d-sm-block d-md-flex align-items-center">
                            <h2 class="mb-0"><?= $approvedClockIn ?></h2>
                            <p class="text-<?= $approvedChange >= 0 ? 'success' : 'danger' ?> ml-2 mb-0 font-weight-medium">
                              <?= ($approvedChange >= 0 ? '+' : '') . $approvedChange ?>% vs yesterday
                            </p>
                          </div>
                          <h6 class="text-muted font-weight-normal"><?= $totalStaff ? $approvedPercent : 0 ?>% of active staff approved</h6>
                        </div>
                        <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
                          <i class="icon-lg mdi mdi-monitor text-success ml-auto"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <!-- Staff Clockin Table -->
                <div class="row">
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Staff Clockin</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Staff Name</th>
                                                <th>Staff ID</th>
                                                <th>Time</th>
                                                <th>Clock Type</th>
                                                <th>Location</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $attendanceResult->fetch_assoc()): ?>
                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input"></td>
                                                    <td>
                                                        <img src="uploads/<?= htmlspecialchars($row['photo_path']) ?>" 
                                                             alt="image" style="width:40px;height:40px;border-radius:50%;" />
                                                        <?= htmlspecialchars($row['full_name']) ?>
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
                                                            'uploads/<?= htmlspecialchars($row['photo_path']) ?>',
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
                                                                <button type="submit" name="action" value="approve" class="badge badge-outline-success">Approve</button>
                                                                <button type="submit" name="action" value="reject" class="badge badge-outline-danger">Reject</button>
                                                            </form>
                                                        <?php elseif ($row['status'] === 'approved'): ?>
                                                            <div class="badge badge-outline-success">Approved</div>
                                                        <?php else: ?>
                                                            <div class="badge badge-outline-danger">Rejected</div>
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
                <?php endif; ?>
                <?php if (canSee('admin','ceo','manager','accountant')): ?>    
                <!-- Recent Customers Table -->
                <div class="row">
                    <div class="col-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Recent Customers</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Customer ID</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Service Rendered</th>
                                                <th>Amount</th>
                                                <th>Date Served</th>
                                                <th>Staff in Charge</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($customerResult->num_rows > 0): ?>
                                                <?php while ($row = $customerResult->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['customer_id']) ?></td>
                                                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                                        <td><?= htmlspecialchars($row['phone']) ?></td>
                                                        <td><?= htmlspecialchars($row['service_rendered']) ?></td>
                                                        <td>₦<?= number_format($row['amount'], 2) ?></td>
                                                        <td><?= htmlspecialchars($row['date_served']) ?></td>
                                                        <td><?= htmlspecialchars($row['staff_in_charge']) ?></td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="8" class="text-center">No customers found</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <a href="customers.php" class="btn btn-sm btn-gradient-primary mt-2">View All Customers</a>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Visitors Filter & Table Row -->
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between mb-3">
      <h4 class="card-title">Visitors by Countries</h4>
      <select id="visitorFilter" class="form-control w-auto" onchange="filterVisitors()">
        <option value="today" <?= $filter=='today' ? 'selected' : '' ?>>Today</option>
        <option value="week" <?= $filter=='week' ? 'selected' : '' ?>>Last 7 Days</option>
        <option value="month" <?= $filter=='month' ? 'selected' : '' ?>>This Month</option>
      </select>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <!-- Visitors Table -->
          <div class="col-md-5">
            <div class="table-responsive">
              <table class="table" id="visitorsTable">
                <tbody>
                  <?php foreach($visitors_by_country as $row): 
                      $country = $row['country'];
                      $count = $row['total'];
                      $percent = $total_visitors ? round(($count / $total_visitors) * 100, 2) : 0;

                      $flags = [
                          "United States" => "us",
                          "Germany" => "de",
                          "Australia" => "au",
                          "United Kingdom" => "gb",
                          "Romania" => "ro",
                          "Brazil" => "br",
                      ];
                      $flag_code = $flags[$country] ?? 'un';
                  ?>
                  <tr>
                    <td><i class="flag-icon flag-icon-<?= htmlspecialchars($flag_code) ?>"></i></td>
                    <td><?= htmlspecialchars($country) ?></td>
                    <td class="text-right"><?= $count ?></td>
                    <td class="text-right font-weight-medium"><?= $percent ?>%</td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Visitors Map -->
          <div class="col-md-7">
            <div id="audience-map" class="vector-map"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<script>
function filterVisitors(){
    const filter = document.getElementById('visitorFilter').value;
    window.location.href = "dashboard.php?filter=" + filter;
}
</script>
<?php include("inc/script.php"); ?>
    <!-- End custom js for this page -->
    <!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Clock-in Preview</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 text-center">
            <img id="previewPhoto" src="" class="img-fluid rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;" alt="Staff Photo">
            <h5 id="previewName"></h5>
            <p id="previewJob"></p>
          </div>
          <div class="col-md-8">
            <table class="table table-bordered">
              <tr><th>Staff ID</th><td id="previewStaffId"></td></tr>
              <tr><th>Clock Type</th><td id="previewClockType"></td></tr>
              <tr><th>Time</th><td id="previewTime"></td></tr>
              <tr><th>Date</th><td id="previewDate"></td></tr>
              <p><strong>Location:</strong> <span id="previewLocation">Loading...</span></p>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function getAddress(lat, long, callback) {
  if (!lat || !long) {
    callback("Location not available");
    return;
  }

  fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${long}&format=json`)
    .then(res => res.json())
    .then(data => {
      if (data && data.display_name) {
        callback(data.display_name);
      } else {
        callback("Unknown location");
      }
    })
    .catch(() => callback("Error fetching location"));
}

function previewRecord(name, staffId, jobTitle, clockType, time, date, photo, lat, long) {
    document.getElementById("previewName").textContent = name;
    document.getElementById("previewJob").textContent = jobTitle;
    document.getElementById("previewStaffId").textContent = staffId;
    document.getElementById("previewClockType").textContent = clockType;
    document.getElementById("previewTime").textContent = time;
    document.getElementById("previewDate").textContent = date;
    document.getElementById("previewPhoto").src = photo;

    let locationEl = document.getElementById("previewLocation");
    locationEl.textContent = "Loading...";

    // Fetch real address from OpenStreetMap
    getAddress(lat, long, function(addr){
        locationEl.textContent = addr;
    });

    // Show modal
    $('#previewModal').modal('show');
}
</script>

<script>
$(function(){
    // Convert PHP array to JS
    var visitorsData = <?= json_encode($map_data) ?>;

    // Initialize the map
    $('#audience-map').vectorMap({
        map: 'world_mill',
        backgroundColor: '#fff',
        regionStyle: {
            initial: {
                fill: '#e4e4e4'
            },
            hover: {
                fill: '#a0a0a0'
            }
        },
        series: {
            regions: [{
                values: visitorsData,
                scale: ['#C8EEFF', '#0071A4'], // color scale
                normalizeFunction: 'linear'
            }]
        },
        onRegionTipShow: function(e, el, code){
            if(visitorsData[code]){
                el.html(el.html() + ' (' + visitorsData[code] + ' visitors)');
            }
        }
    });
});
</script>

  </body>
</html>
