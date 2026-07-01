<?php
// Always load init.php (handles session, db, user info, role helpers)
require_once __DIR__ . "/init.php";

// Ensure we have a logged-in user
if (empty($_SESSION['user_id'])) {
    header("Location: login.php?error=session_expired");
    exit;
}

// Escape values for output
$full_name   = htmlspecialchars($full_name ?? 'Guest');
$role        = htmlspecialchars($role ?? 'guest');
$profile_pic = htmlspecialchars($profile_pic ?? 'assets/images/faces/default.png');
?>

<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
    <a class="sidebar-brand brand-logo d-flex align-items-center" href="dashboard.php">
      <img src="assets/images/logo.png?t=<?= time() ?>" alt="logo" class="logo-img" />
    </a>
    <a class="sidebar-brand brand-logo-mini" href="dashboard.php">
      <img src="assets/images/logo-mini.png?t=<?= time() ?>" alt="logo" />
    </a>
  </div>

  <ul class="nav">
    <!-- Profile Section -->
    <li class="nav-item profile">
      <div class="profile-desc">
        <div class="profile-pic">
          <div class="count-indicator">
            <img class="img-xs rounded-circle" src="<?= $profile_pic ?>?t=<?= time() ?>" alt="profile">
            <span class="count bg-success"></span>
          </div>
          <div class="profile-name">
            <h5 class="mb-0 font-weight-normal"><?= $full_name ?></h5>
            <span><?= ucfirst($role) ?></span>
          </div>
        </div>

        <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
        <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
          <a href="profile.php" class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle"><i class="mdi mdi-account-group text-primary"></i></div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1 text-small">Account Settings</p>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="password.php" class="dropdown-item preview-item">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle"><i class="mdi mdi-lock-reset text-info"></i></div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
            </div>
          </a>
        </div>
      </div>
    </li>

    <!-- Navigation -->
    <li class="nav-item nav-category"><span class="nav-link">Navigation</span></li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="dashboard.php">
        <span class="menu-icon"><i class="mdi mdi-view-dashboard"></i></span>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="add_branch.php">
        <span class="menu-icon"><i class="mdi mdi-office-building"></i></span>
        <span class="menu-title">Manage Branches</span>
      </a>
    </li>

    <?php if (canSee('admin','ceo')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="add_user.php">
          <span class="menu-icon"><i class="mdi mdi-account-group"></i></span>
          <span class="menu-title">Staff Management</span>
        </a>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="manage_profile.php">
        <span class="menu-icon"><i class="mdi mdi-office-building"></i></span>
          <span class="menu-title">Manage Profile</span>
        </a>
      </li>
        <li class="nav-item menu-items">
        <a class="nav-link" href="send_appointment.php">
        <span class="menu-icon"><i class="mdi mdi-email"></i></span>
          <span class="menu-title">Send Appointment Letter</span>
        </a>
      </li>
        <li class="nav-item menu-items">
        <a class="nav-link" href="appointment.php">
        <span class="menu-icon"><i class="mdi mdi-file-document-box"></i></span>
          <span class="menu-title">Appointment Letters</span>
        </a>
      </li>
        </li>
        <li class="nav-item menu-items">
        <a class="nav-link" href="my_appointment.php">
        <span class="menu-icon"><i class="mdi mdi-file-accountx"></i></span>
          <span class="menu-title">My Appointment</span>
        </a>
      </li>

    <?php endif; ?>

    <!-- Appraiser Management -->
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#appraiser" aria-expanded="false" aria-controls="appraiser">
        <span class="menu-icon"><i class="mdi mdi-clipboard-outline"></i></span>
        <span class="menu-title">Appraiser Management</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="appraiser">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="self_appraisal.php">Self Appraiser</a></li>
          <li class="nav-item"><a class="nav-link" href="view_appraisals.php">View Appraisal Result</a></li>
          <?php if (canSee('admin','ceo','manager')): ?>
            <li class="nav-item"><a class="nav-link" href="review.php">Manage Appraisal Reviews</a></li>
          <?php endif; ?>
          <?php if (canSee('admin','ceo')): ?>
            <li class="nav-item"><a class="nav-link" href="admin_questions.php">Add Appraiser</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <!-- Attendance Manager -->
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#attendance" aria-expanded="false" aria-controls="attendance">
        <span class="menu-icon"><i class="mdi mdi-calendar-check"></i></span>
        <span class="menu-title">Attendance Manager</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="attendance">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="attendance/clockin.php">Clock-In</a></li>
          <li class="nav-item"><a class="nav-link" href="user_attendance.php">Your Attendance</a></li>
          <?php if (canSee('admin','ceo','manager','accountant')): ?>
            <li class="nav-item"><a class="nav-link" href="attendance_history.php">Attendance History</a></li>
          <?php endif; ?>
          <?php if (canSee('admin','ceo','manager')): ?>
            <li class="nav-item"><a class="nav-link" href="manage.php">Manage Clock-In</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </li>

    <?php if (canSee('admin','ceo','manager','accountant')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="customers.php">
          <span class="menu-icon"><i class="mdi mdi-account-multiple"></i></span>
          <span class="menu-title">Manage Customer</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if (canSee('admin','editor','ceo')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" data-toggle="collapse" href="#cms" aria-expanded="false" aria-controls="cms">
          <span class="menu-icon"><i class="mdi mdi-file-document-edit"></i></span>
          <span class="menu-title">Content Manager</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="cms">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="blog_admin.php">Add Blog Post</a></li>
            <li class="nav-item"><a class="nav-link" href="blog_posts.php">Edit Blog Post</a></li>
            <li class="nav-item"><a class="nav-link" href="add_project.php">Add Project</a></li>
            <li class="nav-item"><a class="nav-link" href="booking_list.php">Booking Details</a></li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

    <li class="nav-item menu-items">
      <a class="nav-link" href="public/index.php">
        <span class="menu-icon"><i class="mdi mdi-web"></i></span>
        <span class="menu-title">Landing Page</span>
      </a>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="user_payment.php">
        <span class="menu-icon"><i class="mdi mdi-credit-card"></i></span>
        <span class="menu-title">Your Payment History</span>
      </a>
    </li>

    <?php if (canSee('accountant','ceo')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="payroll_manager.php">
          <span class="menu-icon"><i class="mdi mdi-cash-multiple"></i></span>
          <span class="menu-title">Payroll Manager</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if (canSee('admin','accountant','ceo')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="payroll_list.php">
          <span class="menu-icon"><i class="mdi mdi-file-document-box"></i></span>
          <span class="menu-title">Payroll List</span>
        </a>
      </li>
    <?php endif; ?>

    <!-- User Pages -->
    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
        <span class="menu-icon"><i class="mdi mdi-shield-account"></i></span>
        <span class="menu-title">User Pages</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="auth">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
      </div>
    </li>

    <?php if (canSee('ceo')): ?>
      <li class="nav-item menu-items">
        <a class="nav-link" href="termination.php">
          <span class="menu-icon"><i class="mdi mdi-account-remove"></i></span>
          <span class="menu-title">Manage Termination</span>
        </a>
      </li>
      <li class="nav-item menu-items">
        <a class="nav-link" href="terminated_list.php">
          <span class="menu-icon"><i class="mdi mdi-history"></i></span>
          <span class="menu-title">Termination History</span>
        </a>
      </li>
    <?php endif; ?>

  </ul>
</nav>
