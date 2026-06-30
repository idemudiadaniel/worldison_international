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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $project ? escapeHtml($project['title']) : "Project Not Found" ?></title>
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
  <style>
/* --- Project Page Media Styling --- */


/* Image Styling */
.project-image {
  width: 100%;
  max-width: 500px;
  height: auto;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  margin: 0 auto 2rem;
  display: block;
  object-fit: cover;
}

/* Video Container */
.project-video {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  max-width: 900px;
  margin: 0 auto 2rem;
}

.project-video video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 10px;
  object-fit: cover;
}

</style>

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
          <a class="nav-link" href="https://academy.worldison.org">Our Academy</a>
        </li>
      </ul>

      
    </div>
  </div>
</nav>
<!-- Navbar End -->

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
        <p class="margin-b-0"><a class="fweight-700" href="#">Worldison International</a></p>
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