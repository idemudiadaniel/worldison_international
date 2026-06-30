<?php
include("inc/db.php");
session_start();

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','accountant','ceo','manager',])) {
  header("Location: dashboard.php");
  exit;
}

// Default query
$sql = "SELECT id, customer_id, full_name, email, phone, service_rendered, amount, date_served, staff_in_charge FROM customers WHERE 1=1";
$params = [];
$types  = "";

// Handle filters
if (!empty($_GET['name'])) {
    $sql .= " AND full_name LIKE ?";
    $params[] = "%" . $_GET['name'] . "%";
    $types   .= "s";
}
if (!empty($_GET['service'])) {
    $sql .= " AND service_rendered LIKE ?";
    $params[] = "%" . $_GET['service'] . "%";
    $types   .= "s";
}
if (!empty($_GET['date'])) {
    $sql .= " AND date_served = ?";
    $params[] = $_GET['date'];
    $types   .= "s";
}

// Prepare & execute
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>
      <div class="main-panel">
        <div class="content-wrapper">

          <div class="page-header">
            <h3 class="page-title">
              <span class="page-title-icon bg-gradient-primary text-white mr-2">
                <i class="mdi mdi-account-card-details"></i>
              </span> Manage Customers
            </h3>
          </div>

          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Customer List</h4>
                  <p class="card-description"> Search and filter customer records </p>

                  <!-- Search Form -->
                  <form method="GET" class="form-inline mb-3">
                    <div class="form-group mr-2">
                      <input type="text" name="name" placeholder="Search by Name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                      <input type="text" name="service" placeholder="Search by Service" value="<?= htmlspecialchars($_GET['service'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group mr-2">
                      <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-gradient-primary">Filter</button>
                    <a href="customers.php" class="btn btn-light ml-2">Reset</a>
                  </form>

                  <a href="add_customer.php" class="btn btn-gradient-success mb-3">
                    <i class="mdi mdi-account-plus"></i> Add Customer
                  </a>

                  <!-- Table -->
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead class="thead-dark">
                        <tr>
                          <th>Customer ID</th>
                          <th>Full Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Service Rendered</th>
                          <th>Amount</th>
                          <th>Date Served</th>
                          <th>Staff in Charge</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if ($result->num_rows > 0): ?>
                          <?php while ($row = $result->fetch_assoc()): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['customer_id']) ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['service_rendered']) ?></td>
                            <td>₦<?= number_format($row['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['date_served']) ?></td>
                            <td><?= htmlspecialchars($row['staff_in_charge']) ?></td>
                            <td>
                              <a href="edit_customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-gradient-primary">
                                <i class="mdi mdi-pencil"></i> Edit
                              </a>
                              <a href="app/delete_customer.php?id=<?= $row['id'] ?>" 
                                onclick="return confirm('Are you sure you want to delete this customer?')" 
                                class="btn btn-sm btn-gradient-danger">
                                <i class="mdi mdi-delete"></i> Delete
                              </a>
                            </td>
                          </tr>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <tr><td colspan="9" class="text-center">No customers found</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
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
