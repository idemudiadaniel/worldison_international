<?php
include __DIR__ . "/../inc/db.php";

function escapeHtml($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// Return the real image path; no default fallback
function getImagePath($path) {
    $fullPath = "../uploads/projects/" . ltrim($path, "/");
    if (!empty($path) && file_exists($fullPath)) return $fullPath;
    return null; // returns null if image not found
}

// Return the real video path; no default
function getVideoPath($path) {
    $fullPath = "../uploads/projects/videos/" . ltrim($path, "/");
    if (!empty($path) && file_exists($fullPath)) return $fullPath;
    return null;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$project = null;

$sql = "SELECT * FROM projects WHERE id=$id AND status='published' LIMIT 1";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $project = $result->fetch_assoc();
}

// build post url for share
$postUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
           . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<?php
$pageTitle = '<?= $project ? escapeHtml($project[\'title\']) : "Project Not Found" ?>';
$pageDescription = 'Worldison International Blog - Updates, insights, and stories.';
?>
<?php require_once __DIR__ . "/inc/head.php"; ?>
<?php require_once __DIR__ . "/inc/header.php"; ?>
<div class="container mt-5">
    <div class="col-lg-10 order-1 order-lg-2">
      <div class="project-content">
        <h2><?= escapeHtml($project['title']) ?></h2>

        <?php if (!empty($project['image_url']) && getImagePath($project['image_url'])): ?>
          <img class="project-image" src="<?= escapeHtml(getImagePath($project['image_url'])) ?>" alt="Project Image">
        <?php endif; ?>

        <?php if (!empty($project['video_url']) && getVideoPath($project['video_url'])): ?>
          <div class="project-video">
            <video controls>
              <source src="<?= escapeHtml(getVideoPath($project['video_url'])) ?>" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
        <?php endif; ?>
        <p><?= $project['description'] ?></p>
      </div>
    </div>
  </div>

<!-- Instagram Section -->
<section class="instagram">
  <a href="https://www.instagram.com/worldison_sfc" target="_blank" class="d-block text-center mb-3">
    <i class="fa fa-instagram" aria-hidden="true"></i>
    <span>@worldison_sfc</span>
  </a>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="instagram-item">


          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-1.png" alt="image">
          </div>
          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-2.png" alt="image">
          </div>
          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-3.png" alt="image">
          </div>
          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-4.png" alt="image">
          </div>
          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-5.png" alt="image">
          </div>
          <div class="instagram-item-thum">
            <img src="images/blog/case-studies-6.png" alt="image">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php <?php require_once __DIR__ . "/inc/footer.php"; ?>
<!-- Vendor JS -->
<script src="vendor/jQuery/jquery.min.js"></script>



<!-- Main JS -->
<?php require_once __DIR__ . "/inc/scripts.php"; ?>
