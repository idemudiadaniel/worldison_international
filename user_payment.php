<?php
// my_payroll.php
session_start();
include("inc/db.php");

// ✅ Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$staff_name = $_SESSION['full_name'] ?? '';

// ---- recent months helper ----
function recent_months($count = 12) {
    $months = [];
    for ($i = 0; $i < $count; $i++) {
        $time = strtotime("-$i month");
        $months[] = [
            'label' => date('F Y', $time),
            'year'  => date('Y', $time),
            'month' => date('m', $time),
            'ym'    => date('Y-m', $time),
        ];
    }
    return $months;
}

$selected_ym = isset($_GET['ym']) ? $_GET['ym'] : date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $selected_ym)) {
    $selected_ym = date('Y-m');
}
list($sel_year, $sel_month) = explode('-', $selected_ym);
$start_date = "$sel_year-$sel_month-01";
$end_date   = date("Y-m-t", strtotime($start_date));

// ✅ Fetch only current user's payroll
$sql = "
  SELECT p.*, u.staff_id, u.full_name, u.job_title, u.department
  FROM payroll p
  JOIN users u ON p.user_id = u.id
  WHERE p.user_id = ? AND p.pay_date BETWEEN ? AND ?
  ORDER BY p.pay_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$months = recent_months(24);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header d-flex justify-content-between align-items-center">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white mr-2">
          <i class="mdi mdi-cash"></i>
        </span>
        My Payroll - <?= htmlspecialchars(date('F Y', strtotime($start_date))) ?>
      </h3>
      <form method="GET">
        <select name="ym" class="form-control" onchange="this.form.submit()">
          <?php foreach($months as $m): ?>
            <option value="<?= $m['ym'] ?>" <?= $m['ym'] === $selected_ym ? 'selected' : '' ?>>
              <?= $m['label'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Payment History for <?= htmlspecialchars($staff_name) ?></h4>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Pay Date</th>
                <th>Department</th>
                <th>Job Title</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Bonuses</th>
                <th>Net Pay</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              if ($result->num_rows > 0):
                while ($r = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($r['pay_date']) ?></td>
                    <td><?= htmlspecialchars($r['department']) ?></td>
                    <td><?= htmlspecialchars($r['job_title']) ?></td>
                    <td><?= number_format($r['basic_salary'],2) ?></td>
                    <td><?= number_format($r['allowances'],2) ?></td>
                    <td><?= number_format($r['deductions'],2) ?></td>
                    <td><?= number_format($r['bonuses'],2) ?></td>
                    <td><strong><?= number_format($r['net_pay'],2) ?></strong></td>
                  </tr>
                <?php endwhile;
              else: ?>
                <tr><td colspan="9" class="text-center">No payroll record for this month.</td></tr>
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

<?php
$stmt->close();
$conn->close();
?>
