<?php
// payroll_manager.php
include("inc/db.php");
session_start();

// Only CEO & Accountant can access
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['ceo','accountant'])) {
    header("Location: dashboard.php");
    exit;
}

// small helper for flash messages
function flash($type, $msg) {
    $_SESSION['flash'] = ['type'=>$type,'msg'=>$msg];
}

// Handle Add to Payroll
if (isset($_POST['add_payroll'])) {
    $user_id   = intval($_POST['user_id']);
    $salary    = floatval($_POST['basic_salary']);
    $pay_date  = !empty($_POST['pay_date']) ? $_POST['pay_date'] : date('Y-m-d');

    // validate date
    $d = DateTime::createFromFormat('Y-m-d', $pay_date);
    if (!$d || $d->format('Y-m-d') !== $pay_date) {
        flash('danger','Invalid pay date format. Use YYYY-MM-DD.');
        header("Location: payroll_manager.php");
        exit;
    }

    // Prevent duplicates (user already in payroll)
    $check = $conn->prepare("SELECT id FROM payroll WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        flash('warning','User is already in payroll.');
        $check->close();
        header("Location: payroll_manager.php");
        exit;
    }
    $check->close();

    // Insert
    $ins = $conn->prepare("INSERT INTO payroll (user_id, basic_salary, pay_date) VALUES (?, ?, ?)");
    $ins->bind_param("ids", $user_id, $salary, $pay_date);
    if ($ins->execute()) {
        flash('success','User added to payroll.');
    } else {
        flash('danger','Failed to add to payroll: ' . $conn->error);
    }
    $ins->close();
    header("Location: payroll_manager.php");
    exit;
}

// Handle Remove from Payroll
if (isset($_POST['remove_payroll'])) {
    $user_id = intval($_POST['user_id']);
    $del = $conn->prepare("DELETE FROM payroll WHERE user_id = ?");
    $del->bind_param("i", $user_id);
    if ($del->execute()) {
        flash('success','Removed from payroll.');
    } else {
        flash('danger','Failed to remove from payroll.');
    }
    $del->close();
    header("Location: payroll_manager.php");
    exit;
}

// Handle Save/Edit payroll (modal)
if (isset($_POST['save_payroll'])) {
    $user_id    = intval($_POST['user_id']);
    $basic      = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);
    $days_abs   = intval($_POST['days_absent']);
    $bonuses    = floatval($_POST['bonuses']);
    $pay_date   = !empty($_POST['pay_date']) ? $_POST['pay_date'] : date('Y-m-d');

    // validate date
    $d = DateTime::createFromFormat('Y-m-d', $pay_date);
    if (!$d || $d->format('Y-m-d') !== $pay_date) {
        flash('danger','Invalid pay date format.');
        header("Location: payroll_manager.php");
        exit;
    }

    $upd = $conn->prepare("UPDATE payroll
                           SET basic_salary=?, allowances=?, deductions=?, days_absent=?, bonuses=?, pay_date=?
                           WHERE user_id=?");
    $upd->bind_param("dddidsi", $basic, $allowances, $deductions, $days_abs, $bonuses, $pay_date, $user_id);
    if ($upd->execute()) {
        flash('success','Payroll updated.');
    } else {
        flash('danger','Failed to update payroll: ' . $conn->error);
    }
    $upd->close();
    header("Location: payroll_manager.php");
    exit;
}

// Handle Termination (optional here)
if (isset($_POST['terminate_user'])) {
    $user_id = intval($_POST['user_id']);
    $reason  = $_POST['reason'] ?? '';

    if (empty($reason)) {
        flash('danger','Please choose a termination reason.');
        header("Location: payroll_manager.php");
        exit;
    }

    $ins = $conn->prepare("INSERT INTO terminations (user_id, reason) VALUES (?, ?)");
    $ins->bind_param("is", $user_id, $reason);
    $ins->execute();
    $ins->close();

    $upd = $conn->prepare("UPDATE users SET status='terminated' WHERE id = ?");
    $upd->bind_param("i", $user_id);
    $upd->execute();
    $upd->close();

    $del = $conn->prepare("DELETE FROM payroll WHERE user_id = ?");
    $del->bind_param("i", $user_id);
    $del->execute();
    $del->close();

    flash('success','User terminated and removed from payroll.');
    header("Location: payroll_manager.php");
    exit;
}

/* ---------------------------
   Fetch data for display
---------------------------- */
// Payroll list: join payroll -> users; only for active users (or include all payroll rows)
$listSql = "
  SELECT p.*, u.staff_id, u.full_name, u.job_title
  FROM payroll p
  JOIN users u ON p.user_id = u.id
  WHERE u.status = 'active'
  ORDER BY u.full_name ASC
";
$payResult = $conn->query($listSql);

// Users not yet in payroll (for add-select)
$availStmt = $conn->prepare("
  SELECT id, staff_id, full_name, job_title 
  FROM users 
  WHERE status='active' AND id NOT IN (SELECT user_id FROM payroll)
  ORDER BY full_name ASC
");
$availStmt->execute();
$avail = $availStmt->get_result();

// Payroll count
$countRes = $conn->query("SELECT COUNT(*) AS cnt FROM payroll");
$countRow = $countRes->fetch_assoc();
$payrollCount = intval($countRow['cnt']);

// grab any flash and clear it
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
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
            </span> Payroll Manager
          </h3>
      <div>
        <strong>Total on Payroll:</strong> <?= $payrollCount ?>
        <a href="terminated_list.php" class="btn btn-danger btn-sm ml-3">View Terminated Staff</a>
      </div>
    </div>

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Add form -->
    <div class="row mb-3">
      <div class="col-md-8">
        <form method="POST" class="form-inline">
          <label class="mr-2">Add staff</label>
          <select name="user_id" class="form-control mr-2">
            <?php while($r = $avail->fetch_assoc()): ?>
              <option value="<?= $r['id'] ?>">[<?=htmlspecialchars($r['staff_id'])?>] <?=htmlspecialchars($r['full_name'])?> - <?=htmlspecialchars($r['job_title'])?></option>
            <?php endwhile; ?>
          </select>

          <input type="number" name="basic_salary" step="0.01" class="form-control mr-2" placeholder="Basic Salary" required>
          <input type="date" name="pay_date" class="form-control mr-2" value="<?= date('Y-m-d') ?>">
          <button type="submit" name="add_payroll" class="btn btn-success">Add to Payroll</button>
        </form>
      </div>
      <div class="col-md-4 text-right">
        <form method="GET" class="form-inline">
          <input type="text" name="search" class="form-control mr-2" placeholder="Search payroll...">
          <button class="btn btn-primary">Search</button>
        </form>
      </div>
    </div>

    <!-- Payroll table -->
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Staff in Payroll</h4>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Staff ID</th>
                <th>Name</th>
                <th>Position</th>
                <th>Basic</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Days Absent</th>
                <th>Bonuses</th>
                <th>Net Pay</th>
                <th>Pay Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $sn = 1; while($row = $payResult->fetch_assoc()): ?>
              <tr>
                <td><?= $sn++ ?></td>
                <td><?= htmlspecialchars($row['staff_id']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['job_title']) ?></td>
                <td><?= number_format($row['basic_salary'],2) ?></td>
                <td><?= number_format($row['allowances'],2) ?></td>
                <td><?= number_format($row['deductions'],2) ?></td>
                <td><?= (int)$row['days_absent'] ?></td>
                <td><?= number_format($row['bonuses'],2) ?></td>
                <td><?= number_format($row['net_pay'],2) ?></td>
                <td><?= htmlspecialchars($row['pay_date']) ?></td>
                <td>
                  <button class="btn btn-sm btn-primary editBtn"
                    data-id="<?= $row['user_id'] ?>"
                    data-basic="<?= $row['basic_salary'] ?>"
                    data-allow="<?= $row['allowances'] ?>"
                    data-deduc="<?= $row['deductions'] ?>"
                    data-absent="<?= $row['days_absent'] ?>"
                    data-bonus="<?= $row['bonuses'] ?>"
                    data-paydate="<?= $row['pay_date'] ?>"
                  >Edit</button>

                  <form method="POST" style="display:inline" onsubmit="return confirm('Remove from payroll?')">
                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                    <button type="submit" name="remove_payroll" class="btn btn-sm btn-danger">Remove</button>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
              <?php if ($payrollCount === 0): ?>
                <tr><td colspan="12" class="text-center">No payroll entries</td></tr>
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

<!-- Edit Modal -->
<div class="modal fade" id="editPayrollModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Payroll</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="user_id" id="modalUserId">
        <div class="form-group">
          <label>Basic Salary</label>
          <input type="number" step="0.01" name="basic_salary" id="modalBasic" class="form-control">
        </div>
        <div class="form-group">
          <label>Allowances</label>
          <input type="number" step="0.01" name="allowances" id="modalAllow" class="form-control">
        </div>
        <div class="form-group">
          <label>Deductions</label>
          <input type="number" step="0.01" name="deductions" id="modalDeduc" class="form-control">
        </div>
        <div class="form-group">
          <label>Days Absent</label>
          <input type="number" name="days_absent" id="modalAbsent" class="form-control">
        </div>
        <div class="form-group">
          <label>Bonuses</label>
          <input type="number" step="0.01" name="bonuses" id="modalBonus" class="form-control">
        </div>
        <div class="form-group">
          <label>Pay Date</label>
          <input type="date" name="pay_date" id="modalPayDate" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="save_payroll" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php include("inc/script.php"); ?>
<script>
document.querySelectorAll('.editBtn').forEach(btn=>{
  btn.addEventListener('click', function(){
    document.getElementById('modalUserId').value = this.dataset.id;
    document.getElementById('modalBasic').value = this.dataset.basic;
    document.getElementById('modalAllow').value = this.dataset.allow;
    document.getElementById('modalDeduc').value = this.dataset.deduc;
    document.getElementById('modalAbsent').value = this.dataset.absent;
    document.getElementById('modalBonus').value = this.dataset.bonus;
    document.getElementById('modalPayDate').value = this.dataset.paydate;
    $('#editPayrollModal').modal('show');
  });
});
</script>
</body>
</html>
