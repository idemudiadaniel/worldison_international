<?php
// blog.php
include __DIR__ . "/../inc/db.php"; // DB connection

function escapeHtml($str = "") {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Pagination setup
$postsPerPage = 5;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $postsPerPage;

// Count total published posts
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM blogs WHERE status='published'");
$totalPosts  = $totalResult ? (int)$totalResult->fetch_assoc()['total'] : 0;
$totalPages  = max(1, ceil($totalPosts / $postsPerPage));

// Fetch posts for this page
$posts = [];
$result = $conn->query("SELECT * FROM blogs 
                        WHERE status='published' 
                        ORDER BY created_at DESC 
                        LIMIT $postsPerPage OFFSET $offset");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Featured (latest post)
$featured = null;
$resFeat = $conn->query("SELECT * FROM blogs 
                         WHERE status='published' 
                         ORDER BY created_at DESC LIMIT 1");
if ($resFeat && $resFeat->num_rows > 0) {
    $featured = $resFeat->fetch_assoc();
}

// Trending (latest 4 excluding featured)
$trending = [];
$resTrend = $conn->query("SELECT * FROM blogs 
                          WHERE status='published' 
                          ORDER BY created_at DESC LIMIT 4 OFFSET 1");
if ($resTrend && $resTrend->num_rows > 0) {
    while ($row = $resTrend->fetch_assoc()) {
        $trending[] = $row;
    }
}

// Helper for image path
function getImagePath($path) {
    return !empty($path) ? "../" . ltrim($path, "/") : "../assets/images/default.png";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Worldison Blog</title>

  <!-- SEO Meta -->
  <meta name="description" content="Worldison International Blog - Updates, insights, and stories.">
  <meta name="robots" content="index, follow">

  <!-- Mobile Responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
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
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="https://coming_soon.php" target="_blank">Our Academy</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar End -->

<!-- ========== STATIC FEATURED (kept fully static as requested) ========== -->
<section class="featured">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <article class="featured-post">
          <div class="featured-post-content">
            <div class="featured-post-author">
              <img src="images/author.png" alt="author" />
              <p>By <span>Worldison International</span></p>
            </div>
            <!-- static link to featured article (change id if you want) -->
            <a href="#" class="featured-post-title">
              Cleaning plays a crucial role in maintaining a healthy living environment
            </a>
            <ul class="featured-post-meta">
              <li><i class="fa fa-clock-o"></i> October 19, 2020 - 3 min read</li>
            </ul>
          </div>
          <div class="featured-post-thumb">
            <img src="images/featured-post.jpg" alt="feature-post-thumb" />
          </div>
        </article>
      </div>
    </div>
  </div>
</section>
<!-- ========== END STATIC FEATURED ========== -->

<!-- ========== BLOG ========== -->
<section class="blog">
  <div class="container">
    <div class="row">
      <!-- Main column -->
      <div class="col-lg-8">
        <div class="blog-section-title">
          <h2>Articles</h2>
          <p>View the latest news on Blogger</p>
        </div>

        <!-- DYNAMIC POSTS (server-side; original blog-post layout preserved) -->
        <div id="blog-container">
          <?php if (empty($posts)): ?>
            <p>No posts yet.</p>
          <?php else: ?>
            <?php foreach ($posts as $p): 
              $id = (int)$p['id'];
              $title = escapeHtml($p['title']);
              $category = escapeHtml($p['category'] ?: 'General');
              $image = escapeHtml(getImagePath($p['image_url'] ?: $p['cover_url'] ?? ''));
              $dateStr = date("F j, Y", strtotime($p['created_at']));
              $excerpt = escapeHtml(mb_substr(strip_tags($p['content']), 0, 140));
            ?>
            <article class="blog-post">
              <div class="blog-post-thumb">
                <a href="single-blog.php?id=<?= $id ?>">
                  <img src="<?= $image ?>" alt="<?= $title ?>" />
                </a>
              </div>
              <div class="blog-post-content">
                <div class="blog-post-tag">
                  <a href="#"><?= $category ?></a>
                </div>
                <div class="blog-post-title">
                  <a href="single-blog.php?id=<?= $id ?>"><?= $title ?></a>
                </div>
                <div class="blog-post-meta">
                  <ul>
                    <li>By <a href="about.php">Worldison International</a></li>
                    <li><i class="fa fa-clock-o"></i> <?= $dateStr ?></li>
                  </ul>
                </div>
                <p><?= $excerpt ?><?= (mb_strlen(strip_tags($p['content'])) > 140 ? '…' : '') ?></p>
                <a href="single-blog.php?id=<?= $id ?>" class="blog-post-action">
                  read more <i class="fa fa-angle-right"></i>
                </a>
              </div>
            </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Pagination (keeps server pagination) -->
        <div class="blog-post-pagination">
          <nav aria-label="Page navigation example" class="nav-bg">
            <ul class="pagination">
              <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>">Previous</a></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>">Next</a></li>
              <?php endif; ?>
            </ul>
          </nav>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="blog-post-widget">
          <div class="latest-widget-title">
            <h2>Trending post</h2>
          </div>

          <div id="trending-posts">
            <?php if (empty($trending)): ?>
              <p>No trending posts.</p>
            <?php else: ?>
              <?php foreach ($trending as $t):
                $tid = (int)$t['id'];
                $ttitle = escapeHtml($t['title']);
                $timage = escapeHtml(getImagePath($t['image_url'] ?: $t['cover_url'] ?? ''));
                $tdate = date("F j, Y", strtotime($t['created_at']));
              ?>
              <div class="latest-widget">
                <div class="latest-widget-thum">
                  <a href="single-blog.php?id=<?= $tid ?>">
                    <img src="<?= $timage ?>" alt="<?= $ttitle ?>" />
                  </a>
                  <div class="icon">
                    <a href="single-blog.php?id=<?= $tid ?>">
                      <img src="images/blog/icon.svg" alt="icon" />
                    </a>
                  </div>
                </div>
                <div class="latest-widget-content">
                  <div class="content-title">
                    <a href="single-blog.php?id=<?= $tid ?>"><?= $ttitle ?></a>
                  </div>
                  <div class="content-meta">
                    <ul>
                      <li><i class="fa fa-clock-o"></i> <?= $tdate ?></li>
                    </ul>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ========== END BLOG ========== -->

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

<!-- Footer (restored to original design) -->
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
            <li class="nav-item"><a class="nav-link" href="index.php">Home </a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About </a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 mx-auto">
        <div class="sociale-icon">
          <ul>
            <li><a href="https://www.facebook.com/wsfcompany"><i class="fa fa-facebook"></i></a></li>
            <li><a href="https://www.twitter.com/worldison"><i class="fa fa-twitter"></i></a></li>
            <li><a href="https://www.instagram.com/worldison_sfc"><i class="fa fa-instagram"></i></a></li>
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
