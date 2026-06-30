<?php
// payroll_list.php
session_start();
include("inc/db.php");



// restrict roles
$allowed = ['admin','accountant','ceo','manager'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed)) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// helper to build last X months
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

// parse selected month/year
$selected_ym = isset($_GET['ym']) ? $_GET['ym'] : date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $selected_ym)) {
    $selected_ym = date('Y-m');
}
list($sel_year, $sel_month) = explode('-', $selected_ym);

// search filter
$search = isset($_GET['search']) && $_GET['search'] !== '' ? trim($_GET['search']) : null;

// check if PDF download requested
$downloadPdf = isset($_GET['download']) && $_GET['download'] == '1';

// date range for month
$start_date = "$sel_year-$sel_month-01";
$end_date   = date("Y-m-t", strtotime($start_date));

// SQL query
$sql = "
  SELECT p.*, u.staff_id, u.full_name, u.job_title, u.department
  FROM payroll p
  JOIN users u ON p.user_id = u.id
  WHERE p.pay_date BETWEEN ? AND ?
";
if ($search) {
    $sql .= " AND (u.full_name LIKE ? OR u.staff_id LIKE ?)";
}
$sql .= " ORDER BY u.full_name ASC";

$stmt = $conn->prepare($sql);
if ($search) {
    $like = "%$search%";
    $stmt->bind_param("ssss", $start_date, $end_date, $like, $like);
} else {
    $stmt->bind_param("ss", $start_date, $end_date);
}
$stmt->execute();
$result = $stmt->get_result();

// totals
$totalSql = "
  SELECT 
    COUNT(*) AS total_count,
    IFNULL(SUM(p.basic_salary),0) AS sum_basic,
    IFNULL(SUM(p.allowances),0) AS sum_allow,
    IFNULL(SUM(p.deductions),0) AS sum_deduct,
    IFNULL(SUM(p.bonuses),0) AS sum_bonus,
    IFNULL(SUM(p.net_pay),0) AS sum_net
  FROM payroll p
  JOIN users u ON p.user_id = u.id
  WHERE p.pay_date BETWEEN ? AND ?
";
if ($search) {
    $totalSql .= " AND (u.full_name LIKE ? OR u.staff_id LIKE ?)";
}
$totalStmt = $conn->prepare($totalSql);
if ($search) {
    $totalStmt->bind_param("ssss", $start_date, $end_date, $like, $like);
} else {
    $totalStmt->bind_param("ss", $start_date, $end_date);
}
$totalStmt->execute();
$totals = $totalStmt->get_result()->fetch_assoc();
$totalStmt->close();

// month selector
$months = recent_months(24);

// ---- build HTML table ----
ob_start();
?>
<style>
  body { font-family: 'DejaVu Sans', sans-serif; font-size:12px; }
  table { width:100%; border-collapse: collapse; margin-bottom:10px; }
  table th, table td { border:1px solid #ddd; padding:6px; text-align:left; }
  .right { text-align:right; }
  .center { text-align:center; }
  h2, h3 { margin:0 0 6px 0; }
</style>

<h2>Payroll - <?= htmlspecialchars(date('F Y', strtotime($start_date))) ?></h2>
<p>Generated: <?= date('Y-m-d H:i') ?></p>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Staff ID</th>
      <th>Name</th>
      <th>Position</th>
      <th>Basic</th>
      <th>Allow</th>
      <th>Deductions</th>
      <th>Bonuses</th>
      <th>Net Pay</th>
      <th>Pay Date</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $i = 0;
  $result->data_seek(0);
  while ($r = $result->fetch_assoc()) {
      $i++;
      echo "<tr>";
      echo "<td class='center'>{$i}</td>";
      echo "<td>".htmlspecialchars($r['staff_id'])."</td>";
      echo "<td>".htmlspecialchars($r['full_name'])."</td>";
      echo "<td>".htmlspecialchars($r['job_title'])."</td>";
      echo "<td class='right'>".number_format($r['basic_salary'],2)."</td>";
      echo "<td class='right'>".number_format($r['allowances'],2)."</td>";
      echo "<td class='right'>".number_format($r['deductions'],2)."</td>";
      echo "<td class='right'>".number_format($r['bonuses'],2)."</td>";
      echo "<td class='right'>".number_format($r['net_pay'],2)."</td>";
      echo "<td class='center'>".htmlspecialchars($r['pay_date'])."</td>";
      echo "</tr>";
  }
  if ($i === 0) {
      echo "<tr><td colspan='10' class='center'>No records for this month</td></tr>";
  }
  ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="4" class="right">Totals</th>
      <th class="right"><?= number_format($totals['sum_basic'],2) ?></th>
      <th class="right"><?= number_format($totals['sum_allow'],2) ?></th>
      <th class="right"><?= number_format($totals['sum_deduct'],2) ?></th>
      <th class="right"><?= number_format($totals['sum_bonus'],2) ?></th>
      <th class="right"><?= number_format($totals['sum_net'],2) ?></th>
      <th></th>
    </tr>
  </tfoot>
</table>
<?php
// ✅ Capture everything into $html
$html = ob_get_clean();

// ---- PDF export ----
if ($downloadPdf) {
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);   // now $html has content
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    $filename = "payroll-" . $selected_ym . ".pdf";
    $dompdf->stream($filename, ["Attachment" => true]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white mr-2"><i class="mdi mdi-cash"></i></span>
        Payroll List - <?= htmlspecialchars(date('F Y', strtotime($start_date))) ?>
      </h3>

      <div>
        <form method="GET" class="d-inline-block mr-2">
          <select name="ym" class="form-control" onchange="this.form.submit()">
            <?php foreach($months as $m): ?>
              <option value="<?= $m['ym'] ?>" <?= $m['ym'] === $selected_ym ? 'selected' : '' ?>>
                <?= $m['label'] ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="text" name="search" placeholder="search name or staff id" class="form-control mt-2" value="<?= $search ? htmlspecialchars(trim($_GET['search'])) : '' ?>">
        </form>

        <a href="?ym=<?= urlencode($selected_ym) ?>&download=1<?= $search ? '&search='.urlencode(trim($_GET['search'])) : '' ?>" class="btn btn-sm btn-danger ml-2"></a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Payroll Records (<?= intval($totals['total_count']) ?>)</h4>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Position</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Bonuses</th>
                <th>Net Pay</th>
                <th>Pay Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              if ($result->num_rows > 0):
                $result->data_seek(0);
                while ($r = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($r['staff_id']) ?></td>
                    <td><?= htmlspecialchars($r['full_name']) ?></td>
                    <td><?= htmlspecialchars($r['department']) ?></td>
                    <td><?= htmlspecialchars($r['job_title']) ?></td>
                    <td><?= number_format($r['basic_salary'],2) ?></td>
                    <td><?= number_format($r['allowances'],2) ?></td>
                    <td><?= number_format($r['deductions'],2) ?></td>
                    <td><?= number_format($r['bonuses'],2) ?></td>
                    <td><strong><?= number_format($r['net_pay'],2) ?></strong></td>
                    <td><?= htmlspecialchars($r['pay_date']) ?></td>
                  </tr>
                <?php endwhile;
              else: ?>
                <tr><td colspan="11" class="text-center">No payroll records for this month.</td></tr>
              <?php endif; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="5" class="text-right">Totals</th>
                <th><?= number_format($totals['sum_basic'],2) ?></th>
                <th><?= number_format($totals['sum_allow'],2) ?></th>
                <th><?= number_format($totals['sum_deduct'],2) ?></th>
                <th><?= number_format($totals['sum_bonus'],2) ?></th>
                <th><?= number_format($totals['sum_net'],2) ?></th>
                <th></th>
              </tr>
            </tfoot>
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
// cleanup
$stmt->close();
$conn->close();
