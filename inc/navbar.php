<?php
include("inc/db.php");
require_once __DIR__ . "/init.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// navbar.php
$currentUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email, phone, address, profile_picture, role FROM users WHERE id=?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$currentUser = $stmt->get_result()->fetch_assoc();

// Sanitize values
$full_name = htmlspecialchars($currentUser['full_name'] ?? '');
$email     = htmlspecialchars($currentUser['email'] ?? '');
$phone     = htmlspecialchars($currentUser['phone'] ?? '');
$address   = htmlspecialchars($currentUser['address'] ?? '');
$role      = $currentUser['role'] ?? 'guest';


// Determine profile picture path
$profile_pic = htmlspecialchars($profile_pic ?? 'assets/images/faces/default.png');
?>

<nav class="navbar p-0 fixed-top d-flex flex-row">
  <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
    <a class="navbar-brand brand-logo-mini" href="dashboard.php">
      <img src="assets/images/logo-mini.png?t=<?=time()?>" alt="logo" />
    </a>
  </div>
  <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="mdi mdi-menu"></span>
    </button>

    <ul class="navbar-nav w-100">
      <li class="nav-item w-100">
        <form class="nav-link mt-2 mt-md-0 d-none d-lg-flex search">
          <input type="text" class="form-control" placeholder="Search ">
        </form>
      </li>
    </ul>

    <ul class="navbar-nav navbar-nav-right">

      <!-- Create New Project Button -->
      <li class="nav-item dropdown d-none d-lg-block">
        <a class="nav-link btn btn-success create-new-button" id="createbuttonDropdown" data-toggle="dropdown" href="#">+ Create New Project</a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="createbuttonDropdown">
          <h6 class="p-3 mb-0">Application</h6>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item" href="self_appraisal.php" >
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-file-outline text-primary"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1">Self appraiser</p>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item" href="view_appraisals.php" >
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-web text-info"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1">View Appraisal Result</p>
            </div>
          </a>
          <?php if (canSee('admin','ceo','manager')): ?>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item" href="review.php" >
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-bulletin-board"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1">Manage Appraisal reviews</p>
            </div>
          </a>
          <?php endif; ?>
      </li>

      <!-- Settings Icon -->
      <li class="nav-item nav-settings d-none d-lg-block">
        <a class="nav-link" href="profile.php">
          <i class="mdi mdi-view-grid"></i>
        </a>
      </li>

      <!-- Profile Section -->
      <li class="nav-item dropdown">
        <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
          <div class="navbar-profile">
            <img class="img-xs rounded-circle" src="<?= htmlspecialchars($profile_pic) ?>?t=<?=time()?>" alt="profile">
            <p class="mb-0 d-none d-sm-block navbar-profile-name"><?=$full_name?></p>
            <i class="mdi mdi-menu-down d-none d-sm-block"></i>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="profileDropdown">
          <h6 class="p-3 mb-0">Profile</h6>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item" href="profile.php">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-settings text-success"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject mb-1">Settings</p>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item preview-item" href="logout.php">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="mdi mdi-logout text-danger"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject mb-1">Log out</p>
            </div>
          </a>
        </div>
      </li>

    </ul>

    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-format-line-spacing"></span>
    </button>
  </div>
</nav>
