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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Recent Projects | Worldison International</title>


  <!-- SEO Meta -->
  <meta name="description" content="Worldison International Blog - Updates, insights, and stories.">
  <meta name="robots" content="index, follow">

  <!-- Mobile Responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700&display=swap" rel="stylesheet">

  <!-- Font Awesome (Updated to v5 for proper icons) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" />
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet" />

  <!-- Custom Styles -->
  <link href="css/animate.css" rel="stylesheet">
  <link href="css/layout.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />

  <!-- Favicons -->
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

<body>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm main-nav">
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="img/logo-dark.png" alt="Worldison International Logo" class="logo-img" style="max-height:50px;">
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav"
      aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

     <!-- Navigation -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ml-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="blog.php">Blog</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="about.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="services.php">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php">Contact</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="https://coming_soon.php" target="_blank" rel="noopener">Our Academy</a>
        </li>
      </ul>

      
    </div>
  </div>
</nav>
<!-- Navbar End -->


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
<section class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6 mx-auto text-center">
      <div class="">
        <img class="footer-logo" src="img/logo-dark.png" alt="Worldison International Logo">
      </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 mx-auto">
        <div class="footer-nav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Home </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.php">Contact</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 mx-auto">
        <div class="sociale-icon">
          <ul>
            <li>
              <a href="https://www.facebook.com/wsfcompany"><i class="fa fa-facebook"></i></a>
            </li>
            <li>
              <a href="https://www.twitter.com/worldison"><i class="fa fa-twitter"></i></a>
            </li>
            <li>
              <a href="https://www.instagram.com/worldison_sfc"><i class="fa fa-instagram"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <div class="copy-right">
        <p class="margin-b-0"><a class="fweight-700" href="#">Worldison International</a> </p>
      </div>
    </div>
  </div>
</section>


<!-- Vendor JS -->
<script src="vendor/jQuery/jquery.min.js"></script>
<script src="vendor/bootstrap/bootstrap.min.js"></script>
<script src="vendor/slick/slick.min.js"></script>

<!-- Main JS -->
<script src="js/script.js"></script>
</body>

</html>