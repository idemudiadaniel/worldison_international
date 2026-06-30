<?php
// single-blog.php
include __DIR__ . "/../inc/db.php"; // MySQL connection

// Escape plain text safely
function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Image path helper
function getImagePath($path) {
    return !empty($path) ? "../" . ltrim($path, "/") : "../assets/images/default.png";
}

// Get post ID from URL
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $name        = trim($_POST['name'] ?? '');
    $commentText = trim($_POST['commentText'] ?? '');

    if ($postId && $name !== '' && $commentText !== '') {
        if (mb_strlen($name) > 150) $name = mb_substr($name, 0, 150);
        if (mb_strlen($commentText) > 2000) $commentText = mb_substr($commentText, 0, 2000);

        $stmt = $conn->prepare("INSERT INTO comments (blog_id, name, comment, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $postId, $name, $commentText);
        $stmt->execute();
        $stmt->close();

        header("Location: single-blog.php?id=" . $postId);
        exit;
    }
}

// Fetch blog post
$post = null;
if ($postId) {
    $stmt = $conn->prepare("SELECT id, title, category, content, image_url, author, created_at 
                            FROM blogs 
                            WHERE id = ? AND status = 'published' LIMIT 1");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post   = $result->fetch_assoc();
    $stmt->close();
}

// Fetch comments
$comments = [];
if ($post) {
    $stmt = $conn->prepare("SELECT id, name, comment, created_at 
                            FROM comments 
                            WHERE blog_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    $stmt->close();
}

// Prepare SEO & share URL
$postUrl = "https://yourdomain.com/single-blog.php?id=" . $postId;
$metaDesc = $post ? substr(strip_tags($post['content']), 0, 160) : "Worldison International Blog - Updates, insights, and stories.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?= $post ? escape($post['title']) . " | Worldison Blog" : "Post not found | Worldison Blog" ?></title>
  <meta name="description" content="<?= escape($metaDesc) ?>">
  <meta name="robots" content="index, follow">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" />
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet" />
  <link href="css/animate.css" rel="stylesheet">
  <link href="css/layout.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm main-nav">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="img/logo-dark.png" alt="Worldison International Logo" class="logo-img" style="max-height:50px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ml-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="https://academy.worldison.org">Our Academy</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar End -->

<section class="blog-single">
  <div class="container">
    <div class="row">
      <div class="col-lg-2 order-2 order-lg-1">
        <div class="share-now">
          <a href="#" class="scrol">Share</a>
          <div class="social-icon">
            <ul>
              <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>"><i class="fa fa-facebook"></i></a></li>
              <li><a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&text=<?= urlencode($post['title'] ?? 'Worldison Blog') ?>"><i class="fa fa-twitter"></i></a></li>
              <li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($postUrl) ?>&title=<?= urlencode($post['title'] ?? '') ?>"><i class="fa fa-linkedin"></i></a></li>
              <li><a href="https://www.instagram.com/worldison_sfc"><i class="fa fa-instagram"></i></a></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Blog Content -->
      <div class="col-lg-10 order-1 order-lg-2">
        <article class="single-blog" id="blog-post">
          <?php if (!$post): ?>
            <p>Post not found or not published.</p>
          <?php else: ?>
            <a href="#" class="tag"><?= escape($post['category'] ?: 'Uncategorized') ?></a>
            <h1 class="title"><?= escape($post['title']) ?></h1>
            <ul class="meta">
              <li>By <a href="about.php"><?= escape($post['author'] ?: 'Worldison International') ?></a></li>
              <li><i class="fa fa-clock-o"></i> <?= date("M d, Y H:i", strtotime($post['created_at'])) ?></li>
            </ul>

            <img src="<?= escape(getImagePath($post['image_url'])) ?>" alt="banner" class="img-fluid mb-3 w-100">
            <div class="post-content"><?= ($post['content']) ?></div>
          <?php endif; ?>
        </article>

        <!-- Comments Section -->
        <div class="my-5">
          <h3 class="mb-4">Comments</h3>
          <div id="comments" class="mb-4">
            <?php if (!$post): ?>
              <p class='text-muted'>No comments available because the post does not exist.</p>
            <?php elseif (empty($comments)): ?>
              <p class='text-muted'>No comments yet. Be the first!</p>
            <?php else: ?>
              <?php foreach ($comments as $c): ?>
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-primary"><?= escape($c['name']) ?></h6>
                    <p class="card-text"><?= nl2br(escape($c['comment'])) ?></p>
                    <small class="text-muted"><?= date("M d, Y H:i", strtotime($c['created_at'])) ?></small>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <?php if ($post): ?>
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Leave a Comment</h5>
              <form id="commentForm" method="POST" class="mt-3" action="single-blog.php?id=<?= $postId ?>">
                <div class="form-group">
                  <label for="name">Your Name</label>
                  <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required maxlength="150">
                </div>
                <div class="form-group">
                  <label for="commentText">Comment</label>
                  <textarea name="commentText" id="commentText" class="form-control" rows="3" placeholder="Write your comment..." required maxlength="2000"></textarea>
                </div>
                <!-- Optional CSRF token -->
                <!-- <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>"> -->
                <button type="submit" name="comment_submit" class="btn btn-primary">Post Comment</button>
              </form>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <!-- End Comments Section -->
      </div>
    </div>
  </div>
</section>

<!-- Instagram + Footer (unchanged) -->
<section class="instagram">
  <a href="https://www.instagram.com/worldison_sfc" target="_blank" class="d-block text-center mb-3">
    <i class="fa fa-instagram"></i>
    <span>@worldison_sfc</span>
  </a>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="instagram-item">
          <div class="instagram-item-thum"><img src="images/blog/case-studies-1.png" alt="image"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-2.png" alt="image"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-3.png" alt="image"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-4.png" alt="image"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-5.png" alt="image"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-6.png" alt="image"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-6 mx-auto text-center">
        <img class="footer-logo" src="img/logo-dark.png" alt="Worldison International Logo">
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
      <div class="col-lg-12">
        <div class="copy-right">
          <p class="margin-b-0"><a class="fweight-700" href="worldison.org">Worldison International</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="vendor/jQuery/jquery.min.js"></script>
<script src="vendor/bootstrap/bootstrap.min.js"></script>
<script src="vendor/slick/slick.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
