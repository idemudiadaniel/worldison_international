<?php
include("inc/db.php");
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username']);
    $email      = trim($_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hashing
    $role       = $_POST['role']; // admin or staff
    $full_name  = trim($_POST['full_name']);

    // Prevent duplicate username or email
    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username or email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $password, $role, $full_name);

        if ($stmt->execute()) {
            $success = "User registered successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>iceHRM- Register</title>
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/favicon.png" />
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
          <div class="card col-lg-4 mx-auto">
            <div class="card-body px-5 py-5">
              <h3 class="card-title text-left mb-3">Register</h3>
              
              <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
              <?php if (!empty($success)) echo "<p class='text-success'>$success</p>"; ?>

              <form method="POST">
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="full_name" class="form-control p_input" required>
                </div>
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control p_input" required>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input type="email" name="email" class="form-control p_input" required>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control p_input" required>
                </div>
                <div class="form-group">
                  <label>Role</label>
                  <select name="role" class="form-control p_input" required>
                    <option value="ceo">Admin (CEO)</option>
                    <option value="manager">Manager</option>
                    <option value="accountant">Accountant</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                  </select>
                </div>
                
                <div class="text-center">
                  <button type="submit" class="btn btn-primary btn-block enter-btn">Register</button>
                </div>
                <p class="sign-up text-center">Already have an account? <a href="login.php">Login</a></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php include("inc/script.php"); ?>
</body>
</html>
