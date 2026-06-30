<?php
include("inc/db.php");
session_start();

// Restrict to specific roles
$allowedRoles = ['admin','ceo','manager',];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id    = trim($_POST['customer_id']);
    $full_name      = trim($_POST['full_name']);
    $email          = trim($_POST['email']);
    $phone          = trim($_POST['phone']);
    $address        = trim($_POST['address']);
    $service_rendered = trim($_POST['service_rendered']);
    $amount         = trim($_POST['amount']);
    $date_served    = trim($_POST['date_served']);
    $staff_in_charge = trim($_POST['staff_in_charge']);

    $stmt = $conn->prepare("INSERT INTO customers (customer_id, full_name, email, phone, address, service_rendered, amount, date_served, staff_in_charge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdss", $customer_id, $full_name, $email, $phone, $address, $service_rendered, $amount, $date_served, $staff_in_charge);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Customer added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }    
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
    <div class="main-panel">
      <div class="content-wrapper">

        <div class="page-header">
          <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white mr-2">
              <i class="mdi mdi-account-plus"></i>
            </span> Add Customer
          </h3>
        </div>

        <div class="row">
          <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <?= $message ?>
                <form method="POST">
                  <div class="form-group">
                    <label>Customer ID</label>
                    <input type="text" name="customer_id" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control">
                  </div>
                  <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control"></textarea>
                  </div>
                  <div class="form-group">
                    <label>Service Rendered</label>
                    <input type="text" name="service_rendered" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Date Served</label>
                    <input type="date" name="date_served" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label>Staff in Charge</label>
                    <input type="text" name="staff_in_charge" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-gradient-primary">Save</button>
                  <a href="customers.php" class="btn btn-light">Cancel</a>
                </form>
              </div>
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
