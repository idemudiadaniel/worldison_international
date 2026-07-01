<?php
include __DIR__ . "/../inc/db.php"; // adjust path if needed

function escapeHtml($str = "") {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function getImagePath($filename) {
  $file = "../uploads/projects/" . ltrim($filename, "/");
  if (!empty($filename) && file_exists($file)) {
      return $file; // returns correct relative path
  }
  return "img/970x647/01.jpg"; // fallback
}


// Fetch latest 3 projects
$projects = [];
$sql = "SELECT * FROM projects WHERE status='published' ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
  }
}
?>
<?php
$pageTitle = 'Recent Projects | Worldison International';
$pageDescription = 'Worldison International Blog - Updates, insights, and stories.';
?>
<?php require_once __DIR__ . "/inc/head.php"; ?>
<?php require_once __DIR__ . "/inc/header.php"; ?>
<!-- Latest Projects Section -->
<div id="about">
  <div class="content-lg container">
    <div class="row margin-b-40">
      <div class="col-sm-6">
        <h2>Recent Cleaning Projects</h2>
        <p>We take pride in the transformation we bring to every space. Here are some of our recent cleaning jobs that speak for themselves.</p>
      </div>
    </div>

    <div class="row">
      <?php if (empty($projects)): ?>
        <p>No projects yet.</p>
      <?php else: ?>
        <?php foreach ($projects as $proj): ?>
          <div class="col-sm-4 sm-margin-b-50">
            <div class="margin-b-20">
              <img class="img-responsive"
              src="<?= escapeHtml(getImagePath($proj['image_url'])) ?>" alt="<?= escapeHtml($proj['title']) ?>" class="img-responsive">
            </div>
            <h4>
              <a href="single-project.php?id=<?= (int)$proj['id'] ?>">
                <?= escapeHtml($proj['category'] ?: 'General') ?>
              </a>
              <span class="text-uppercase margin-l-20"><?= escapeHtml($proj['title']) ?></span>
            </h4>
            <p><?= escapeHtml(mb_substr(strip_tags($proj['description']), 0, 120)) ?>…</p>
            <a class="link" href="single-project.php?id=<?= (int)$proj['id'] ?>">Details</a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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
