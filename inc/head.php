<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_samesite', 'None');
    ini_set('session.cookie_secure', '1');
    session_start();
}

// List of pages you don't want indexed
$noindex_pages = [
  'login.php',
  'register.php',
  'logout.php',
  'dashboard.php'
];

$current_page = basename($_SERVER['PHP_SELF']);
if (in_array($current_page, $noindex_pages)) {
    echo '<meta name="robots" content="noindex, nofollow">';
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <title>iceHRMAdmin</title>
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="assets/vendors/jvectormap/jquery-jvectormap.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/vendors/owl-carousel-2/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <!-- iOS PWA Splash Screens -->
<link rel="apple-touch-icon" href="/icons/icon-192.png">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-640x1136.png" media="(device-width: 320px)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-750x1334.png" media="(device-width: 375px)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-1125x2436.png" media="(device-width: 375px) and (-webkit-device-pixel-ratio: 3)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-1242x2208.png" media="(device-width: 414px)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-1242x2688.png" media="(device-width: 414px) and (-webkit-device-pixel-ratio: 3)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-1536x2048.png" media="(device-width: 768px)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-1668x2224.png" media="(device-width: 834px)">
<link rel="apple-touch-startup-image" href="/icons/splash/splash-2048x2732.png" media="(device-width: 1024px)">
<link rel="manifest" href="/manifest.json">
    <style>
    .comment-box {
      background-color: #f8f9fa;
      border-left: 4px solid #007bff;
      margin-top: 10px;
      padding: 10px;
      border-radius: 6px;
    }
    .comment-author {
      font-weight: bold;
      color: #333;
    }
    .comment-time {
      color: #777;
      font-size: 0.85em;
    }
  </style>
    <style>
    #preview { display:block; margin-top:10px; width:120px; border-radius:8px; }
  </style>
    <!-- QuillJS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <style>
    .ql-editor {
    color: #111 !important;   /* dark text for readability */
  }
  .ql-editor.ql-blank::before {
    color: #555 !important;   /* placeholder text darker */
  }
    #editor {
      height: 400px;
      background: #fff;
    }
  </style>
  </head>
  <body>
  <div class="container-scroller">
    <!-- Sidebar -->
    <?php include("inc/sidebar.php"); ?>

    <div class="container-fluid page-body-wrapper">
      <!-- Navbar -->
      <?php include("inc/navbar.php"); ?>
