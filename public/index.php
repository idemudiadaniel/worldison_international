<?php
require_once __DIR__ . "/../inc/db.php";

if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo 'Database connection unavailable.';
    exit;
}

function escapeHtml($str = "") {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function getImagePath($filename) {
  $filename = trim((string)$filename);
  $filename = str_replace(['../', '..\\'], '', $filename);
  $filename = basename($filename);

  if ($filename === '') {
      return "img/970x647/01.jpg";
  }

  if (!preg_match('/^[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|gif)$/i', $filename)) {
      return "img/970x647/01.jpg";
  }

  $file = __DIR__ . "/../uploads/projects/" . $filename;
  if (file_exists($file)) {
      return "uploads/projects/" . rawurlencode($filename);
  }

  return "img/970x647/01.jpg";
}

// Fetch all published projects
$projects = [];
$sql = "SELECT * FROM projects WHERE status='published' ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
  }
}


// Get visitor IP (handles proxies)
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// Check if this IP has already been recorded today
$stmt = $conn->prepare("SELECT id FROM landing_visitors WHERE ip_address = ? AND DATE(visited_at) = CURDATE()");
$stmt->bind_param("s", $ip);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows === 0){
    // Fetch country from free IP API with a short timeout to avoid blocking the page.
    $country = 'Unknown';
    $context = stream_context_create(['http' => ['timeout' => 2]]);
    $response = @file_get_contents("http://ip-api.com/json/{$ip}", false, $context);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['country'])) {
            $country = $data['country'];
        }
    }

    // Insert visitor record
    $stmt = $conn->prepare("INSERT INTO landing_visitors (ip_address, country, user_agent) VALUES (?, ?, ?)");
    if ($stmt) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $stmt->bind_param("sss", $ip, $country, $user_agent);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>

<html lang="en" class="no-js">
<head>
  <meta charset="utf-8"/>
  <title>Worldison International</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta content="Worldison International Ltd — your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services." name="description"/>
  <meta content="optimiscyber" name="author"/>

  <!-- ✅ Universal Social Media Preview (OG + Twitter) -->
  <meta property="og:title" content="Worldison International" />
  <meta property="og:description" content="Your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services." />
  <meta property="og:image" content="https://worldison.org/img/logo.png" />
  <meta property="og:url" content="https://worldison.org/" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Worldison International" />

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Worldison International">
  <meta name="twitter:description" content="Your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services.">
  <meta name="twitter:image" content="https://worldison.org/img/logo.png">

  <!-- Optional favicon and Apple icons -->
  <link href="http://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet" type="text/css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="css/animate.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
  <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet" type="text/css"/>
  <link href="css/layout.min.css" rel="stylesheet" type="text/css"/>
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">
</head>

   
  
   <body id="body" data-spy="scroll" data-target=".header">

  <!-- Header -->
  <header class="header navbar-fixed-top">
    <nav class="navbar" role="navigation">
      <div class="container">

        <!-- Logo + Mobile Toggle -->
        <div class="menu-container js_nav-item">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon"></span>
          </button>

          <div class="logo">
            <a class="logo-wrap" href="#body">
              <img class="logo-img logo-img-main" src="img/logo.png" alt="Worldison International Logo">
              <img class="logo-img logo-img-active" src="img/logo-dark.png" alt="Worldison International Dark Logo">
            </a>
          </div>
        </div>

        <!-- Nav Links -->
        <div class="collapse navbar-collapse nav-collapse">
          <div class="menu-container">
            <ul class="nav navbar-nav navbar-nav-right">
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="#body">Home</a></li>
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="about.php">About</a></li>
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="blog.php" >Blog</a></li>
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="services.php">Services</a></li>
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="contact.php">Contact</a></li>
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="https://academy.worldison.org">Our Academy</a></li>
            </ul>
          </div>
        </div>

      </div>
    </nav>
  </header>

 <!--========== SLIDER ==========-->
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <div class="container">
    <ol class="carousel-indicators">
      <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-example-generic" data-slide-to="1"></li>
    </ol>
  </div>

  <div class="carousel-inner" role="listbox">
    
    <!-- Slide 1 -->
    <div class="item active">
      <img class="img-responsive" src="img/1920x1080/01.jpg" alt="Worldison Sustainability">
      <div class="container">
        <div class="carousel-centered">
          <div class="margin-b-40">
            <h1 class="carousel-title">Building a Sustainable Future</h1>
            <p class="color-white">We deliver innovative solutions that empower communities, protect the environment, and promote global progress.</p>
          </div>
          <a href="booking.php" class="btn-theme btn-theme-sm btn-default-bg text-uppercase">Book Us Now</a>


        </div>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="item">
      <img class="img-responsive" src="img/1920x1080/02.jpg" alt="Worldison Innovation">
      <div class="container">
        <div class="carousel-centered">
          <div class="margin-b-40">
            <h2 class="carousel-title">Global Impact, Local Solutions</h2>
            <p class="color-white">Our projects create measurable change by aligning technology, sustainability, and local development goals.</p>
          </div>
          <a href="services.php" class="btn-theme btn-theme-sm btn-white-brd text-uppercase">Explore Solutions</a>
        </div>
      </div>
    </div>

  </div>
</div>
<!--========== END SLIDER ==========-->


      <!--========== PAGE LAYOUT ==========-->
<!-- About -->
<div id="about">
    <div class="content-lg container">
        <!-- Masonry Grid -->
        <div class="masonry-grid row">
            <div class="masonry-grid-sizer col-xs-6 col-sm-6 col-md-1"></div>

            <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4 sm-margin-b-30">
                <div class="margin-b-60">
                    <h2>Cleaning & Sanitation</h2>
                    <p>We provide professional home, office, and industrial cleaning with strict attention to hygiene and safety, leaving your environment spotless and healthy.</p>
                </div>
                <img class="full-width img-responsive wow fadeInUp" src="img/500x500/02.jpg" alt="Cleaning Service" data-wow-duration=".3" data-wow-delay=".2s">
            </div>

            <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4">
                <div class="margin-b-60">
                    <h2>Pest Control & Fumigation</h2>
                    <p>Our expert fumigation and pest control services are tailored to eliminate insects and rodents using eco-friendly, long-lasting solutions.</p>
                </div>
                <img class="full-width img-responsive wow fadeInUp" src="img/500x500/01.jpg" alt="Fumigation Service" data-wow-duration=".3" data-wow-delay=".3s">
            </div>

            <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4">
                <div class="margin-t-60 margin-b-60">
                    <h2>Fire Safety Solutions</h2>
                    <p>We install and maintain certified fire extinguishers and alarm systems to protect your property, staff, and clients from potential hazards.</p>
                </div>
                <img class="full-width img-responsive wow fadeInUp" src="img/500x500/03.jpg" alt="Fire Safety" data-wow-duration=".3" data-wow-delay=".4s">
            </div>
        </div>
        <!-- End Masonry Grid -->
    </div>

    <div class="bg-color-sky-light">
        <div class="content-lg container">
            <div class="row">
                <div class="col-md-5 col-sm-5 md-margin-b-60">
                    <div class="margin-t-50 margin-b-30">
                        <h2>Why Choose Worldison?</h2>
                        <p>With over 20 years of experience, Worldison International delivers reliable, eco-conscious solutions for homes, offices, and industries. Our expert team is driven by quality, safety, and customer satisfaction every step of the way.</p>
                    </div>
                                    <a href="booking.php"class="btn-theme btn-theme-sm btn-default-bg text-uppercase ">Book Us Now</a>
                </div>

                <div class="col-md-5 col-sm-7 col-md-offset-2">
                    <!-- Accordion -->
                    <div class="accordion">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a class="panel-title-child" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Comprehensive Services
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        From pest control to fire equipment, we offer integrated services to meet all your facility needs under one trusted name.
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo">
                                    <h4 class="panel-title">
                                        <a class="collapsed panel-title-child" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Certified Professionals
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <div class="panel-body">
                                        Our technicians are trained and certified to handle sensitive environments with the highest safety and quality standards.
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingThree">
                                    <h4 class="panel-title">
                                        <a class="collapsed panel-title-child" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Proven Track Record
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="panel-body">
                                        Trusted by clients across residential, commercial, and industrial sectors, we consistently deliver results that exceed expectations.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- End Accordion -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End About -->


<!-- Latest Projects Section -->
<div id="about">
    <div class="content-lg container">
        <div class="row margin-b-40">
            <div class="col-sm-6">
                <h2>Recent Projects</h2>
                <p>We take pride in the transformation we bring to every space. Here are some of our recent jobs that speak for themselves.</p>
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
                <!-- page button -->
            <div class="text-center margin-top-20 link" >
               <button class="btn-theme btn-theme-sm btn-default-bg text-uppercase">
                <a href="projects.php">View More Projects</a>
            </button>
            </div>
    </div>
</div>

<!-- Pricing -->
<div id="pricing">
    <div class="bg-color-sky-light">
        <div class="content-lg container">
           <div class="row margin-b-0">
            <div class="col-sm-6">
                <h2>Book Our Service</h2>
                <p> Get the best and swift service from Us with just a click of a botton </p>
              </div>
        </div>
      <div class="row row-space-1">

        <!-- Silver Package -->
        <div class="col-sm-4 sm-margin-b-2">
          <div class="pricing">
            <div class="margin-b-30">
              <img class="img-responsive" src="img/book/cleaning-1.jpg" alt="Office Cleaning Project"></i>
              <h3>Home Fumigation </h3>
              <p>Effective indoor pest control and fumigation for apartments and homes up to 3 rooms.</p>
            </div>
            <ul class="list-unstyled pricing-list margin-b-50">
              <li>Eco-friendly Chemicals</li>
              <li>Professional Staff</li>
              <li>1-Month Guarantee</li>
            </ul>
                <a href="booking.php"class="btn-theme btn-theme-sm btn-default-bg text-uppercase ">Book Us Now</a>
          </div>
        </div>

        <!-- Gold Package -->
        <div class="col-sm-4 sm-margin-b-2">
          <div class="pricing pricing-active">
            <div class="margin-b-30">
              <img class="img-responsive" src="img/book/cleaning-2.jpg" alt="Office Cleaning Project"></i>
              <h3>Office Disinfection </h3>
              <p>Comprehensive disinfection service for offices and workspaces up to 150 sqm.</p>
            </div>
            <ul class="list-unstyled pricing-list margin-b-50">
              <li>Fast Dry Formula</li>
              <li>Flexible Scheduling</li>
              <li>Certificate of Treatment</li>
            </ul>
                <a href="booking.php"class="btn-theme btn-theme-sm btn-default-bg text-uppercase ">Book Us Now</a>
          </div>
        </div>

        <!-- Platinum Package -->
        <div class="col-sm-4">
          <div class="pricing">
            <div class="margin-b-30">
              <img class="img-responsive" src="img/book/cleaning-3.png" alt="Office Cleaning Project"></i>
              <h3>VIP Cleaning Package </h3>
              <p>Premium deep cleaning + fumigation for large homes, hotels, or VIP residences.</p>
            </div>
            <ul class="list-unstyled pricing-list margin-b-50">
              <li>Full Service Cleaning</li>
              <li>Custom Quote Available</li>
            </ul>
                <a href="booking.php"class="btn-theme btn-theme-sm btn-default-bg text-uppercase ">Book Us Now</a>
          </div>
        </div>
      </div>

      <!-- Full Page Link -->
      <div class="text-center margin-top-20">
        <a href="services.php" class="btn-theme btn-theme-sm btn-default-bg text-uppercase">View All Services</a>
    </div>
  </div>
</div>
<!-- End Pricing -->



            <!-- Clients -->
            <div class="content-lg container">
                <!-- Swiper Clients -->
                <div class="swiper-slider swiper-clients">
                    <!-- Swiper Wrapper -->
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/01.png" alt="Clients Logo">
                        </div>
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/02.png" alt="Clients Logo">
                        </div>
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/03.png" alt="Clients Logo">
                        </div>
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/04.png" alt="Clients Logo">
                        </div>
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/05.png" alt="Clients Logo">
                        </div>
                        <div class="swiper-slide">
                            <img class="swiper-clients-img" src="img/clients/06.png" alt="Clients Logo">
                        </div>
                    </div>
                    <!-- End Swiper Wrapper -->
                </div>
                <!-- End Swiper Clients -->
            </div>
            <!-- End Clients -->
        </div>
        <!-- End Work -->

<!-- Services -->
<div id="services">
    <div class="bg-color-sky-light" data-auto-height="true">
        <div class="content-lg container">
            <div class="row margin-b-40">
                <div class="col-sm-6">
                    <h2>Services</h2>
                    <p>Worldison International delivers premium cleaning, safety, fumigation, and IT security solutions for homes, businesses, and industries.</p>
                </div>
            </div>
            <!--// end row -->

            <div class="row row-space-1 margin-b-2">
                <div class="col-sm-4 sm-margin-b-2">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-chemistry"></i>
                        </div>
                        <div class="service-info">
                            <h3>Fumigation & Pest Control</h3>
                            <p class="margin-b-5">Professional treatment for residential, commercial, and industrial spaces to eliminate pests effectively.</p>
                        </div>
                        <a href="fumigation.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
                <div class="col-sm-4 sm-margin-b-2">
                    <div class="service bg-color-base" data-height="height">
                        <div class="service-element">
                            <i class="service-icon color-white icon-shield"></i>
                        </div>
                        <div class="service-info">
                            <h3 class="color-white">Disinfection Services</h3>
                            <p class="color-white margin-b-5">Intensive sanitization for establishments to ensure a safe and hygienic environment.</p>
                       </div>
                        <a href="disinfection.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-home"></i>
                        </div>
                        <div class="service-info">
                            <h3>Home & Industrial Cleaning</h3>
                            <p class="margin-b-5">General, deep, and specialized cleaning services tailored to homes and large-scale facilities.</p>
                        </div>
                        <a href="cleaning.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
            </div>
            <!--// end row -->

            <div class="row row-space-1">
                <div class="col-sm-4 sm-margin-b-2">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-lock"></i>
                        </div>
                         <div class="service-info">
                            <h3>Safety Equipment Supply</h3>
                            <p class="margin-b-5">Provision of high-quality safety tools and equipment for workplace and personal protection.</p>
                        </div>
                        <a href="safety.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
                <div class="col-sm-4 sm-margin-b-2">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-fire"></i>
                        </div>
                        <div class="service-info">
                            <h3>Fire Extinguisher Services</h3>
                            <p class="margin-b-5">Supply, installation, servicing, and recharging of fire extinguishers for safety compliance.</p>
                        </div>
                        <a href="fire-extinguishers.php" class="content-wrapper-link"></a>       
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-graduation"></i>
                        </div>
                        <div class="service-info">
                            <h3>Fire Safety Training</h3>
                            <p class="margin-b-5">Expert-led training to equip individuals and teams with essential fire prevention skills.</p>
                        </div>
                        <a href="fire-training.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
            </div>

            <div class="row row-space-1 margin-b-2">
                <div class="col-sm-4">
                    <div class="service" data-height="height">
                        <div class="service-element">
                            <i class="service-icon icon-screen-desktop"></i>
                        </div>
                        <div class="service-info">
                            <h3>IT & Security Management</h3>
                            <p class="margin-b-5">Comprehensive IT solutions and security management services for modern organizations.</p>
                        </div>
                        <a href="it-security.php" class="content-wrapper-link"></a>    
                    </div>
                </div>
            </div>
            <!--// end row -->
        </div>
    </div>
</div>
<!-- End Service -->


<!-- Contact -->
<div id="contact">
    <!-- Contact List -->
    <div class="section-seperator">
        <div class="content-lg container">
             <div class="row margin-b-40">
                <div class="col-sm-6">
                    <h2>Contact Us</h2>
                </div>
            </div>
            <div class="row">
                <!-- Contact List -->
                <div class="col-sm-4 sm-margin-b-50">
                    <h3><a href="#">Edo Office</a> <span class="text-uppercase margin-l-20">Head Office</span></h3>
                    <p>197, Ugbowo opposite Union Bank, Benin City, Edo State.</p>
                    <ul class="list-unstyled contact-list">
                        <li><i class="margin-r-10 color-base icon-call-out"></i> (+234) 813 082 6625</li>
                        <li><i class="margin-r-10 color-base icon-envelope"></i> info@worldison.org</li>
                    </ul>
                </div>
                <!-- End Contact List -->

                <!-- Contact List -->
                <div class="col-sm-4 sm-margin-b-50">
                    <h3><a href="#">Lagos Office</a> <span class="text-uppercase margin-l-20">Branch</span></h3>
                    <p>10 Efunshetan Street, off Seriki Street, by Araromi Junction, Iyana Ipaja, Lagos State.</p>
                    <ul class="list-unstyled contact-list">
                        <li><i class="margin-r-10 color-base icon-call-out"></i> (+234) 905 201 5651</li>
                        <li><i class="margin-r-10 color-base icon-envelope"></i> worldisonsfc@gmail.com</li>
                    </ul>
                </div>
                <!-- End Contact List -->

                <!-- Contact List -->
                <div class="col-sm-4 sm-margin-b-50">
                    <h3><a href="#">Abuja Office</a> <span class="text-uppercase margin-l-20">Branch</span></h3>
                    <p>36 Tatieye Crescent, Off Berger Quarry, Mpapa, Abuja.</p>
                    <ul class="list-unstyled contact-list">
                        <li><i class="margin-r-10 color-base icon-call-out"></i> (+234) 706 716 8179</li>
                        <li><i class="margin-r-10 color-base icon-envelope"></i> worldisonsfc@gmail.com</li>
                    </ul>
                </div>
                <!-- End Contact List -->
            </div>
            <!--// end row -->
        </div>
    </div>
    <!-- End Contact List -->
            <!-- Google Map -->
            <div class="map height-300">
			<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.103193248315!2d5.6108137000000005!3d6.3806796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10472d5c8a7b1359%3A0x5bba66ba67794731!2sWORLDISON%20SAFETY%20COMPANY!5e0!3m2!1sen!2sng!4v1760383647810!5m2!1sen!2sng" width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
			</div>
</div>
<!-- End Contact -->

<!-- Page content above -->

  

<!-- Floating "Book Us Now" WhatsApp Button -->
<a href="https://wa.me/2348130826625?text=Hello%20Worldison,%20I%20would%20like%20to%20book%20a%20service" 
   target="_blank" 
   style="position: fixed; bottom: 20px; right: 20px; background-color: #25d366; color: white; border-radius: 30px; padding: 12px 20px; font-size: 16px; font-weight: bold; text-align: center; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.3); z-index: 999; display: flex; align-items: center; gap: 8px;">
    💬 Book Us Now
</a>



        <!--========== END PAGE LAYOUT ==========-->

<!--========== FOOTER ==========-->
<footer class="footer">
    <!-- Links -->
    <div class="section-seperator">
        <div class="content-md container">
            <div class="row">
                <div class="col-sm-2 sm-margin-b-30">
                    <!-- List -->
                    <ul class="list-unstyled footer-list">
                        <li class="footer-list-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li class="footer-list-item"><a href="services.php"><i class="fas fa-concierge-bell"></i> Services</a></li>
                        <li class="footer-list-item"><a href="about.php"><i class="fas fa-users"></i> About Us</a></li>
                        <li class="footer-list-item"><a href="contact.php"><i class="fas fa-envelope-open-text"></i> Contact</a></li>
                        <li class="footer-list-item"><a href="blog.php"><i class="fas fa-envelope-open-text"></i> blog</a></li>
                        <li class="footer-list-item"><a href="#"><i class="fas fa-concierge-bell"></i> Academy</a></li>
                        <li class="footer-list-item"><a href="../login.php"><i class="fas fa-users"></i> Portal</a></li>
                    </ul>
                    <!-- End List -->
                </div>
                <div class="col-sm-2 sm-margin-b-30">
                    <!-- Social Links -->
                    <ul class="list-unstyled footer-list">
                        <li class="footer-list-item"><a href="https://www.twitter.com/worldison"><i class="fab fa-twitter"></i> Twitter</a></li>
                        <li class="footer-list-item"><a href="https://www.facebook.com/wsfcompany"><i class="fab fa-facebook-f"></i> Facebook</a></li>
                        <li class="footer-list-item"><a href="https://www.instagram.com/worldison_sfc"><i class="fab fa-instagram"></i> Instagram</a></li>
                        <li class="footer-list-item"><a href="https://www.youtube.com/@worldison" target="_blank"><i class="fab fa-youtube mr-2"></i> YouTube</a></li>
                        <li class="footer-list-item"><a href="https://www.google.com/search?q=WORLDISON+SAFETY+COMPANY" target="_blank"><i class="fab fa-google mr-2"></i> Google</a></li>
                    </ul>
                    <!-- End Social Links -->
                </div>
                <div class="col-sm-3">
                    <!-- List -->
                    <ul class="list-unstyled footer-list">
                        <li class="footer-list-item"><a href="refund-policy.php"><i class="fas fa-newspaper"></i> Refund Policy</a></li>
                        <li class="footer-list-item"><a href="privacy.php"><i class="fas fa-user-shield"></i> Privacy Policy</a></li>
                        <li class="footer-list-item"><a href="terms.php"><i class="fas fa-file-contract"></i> Terms &amp; Conditions</a></li>
                    </ul>
                    <!-- End List -->
                </div>
                <div class="col-sm-5">
                    <!-- Company Info -->
                    <ul class="list-unstyled footer-list">
                        <li class="footer-list-item"><i class="fas fa-phone-alt"></i> (+234) 8130826625, (+234)9052015651, (+234)7067168179</li>
                        <li class="footer-list-item"><i class="fas fa-envelope"></i> info@worldison.org, worldisonsfc@gmail.com</li>
                        <li class="footer-list-item"><i class="fas fa-map-marker-alt"></i> 197, Ugbowo Opp. Union Bank, Benin City, Edo State, Nigeria</li>
                    </ul>
                    <!-- End Company Info -->
                </div>
            </div>
            <!--// end row -->
        </div>
    </div>
    <!-- End Links -->

    <!-- Copyright -->
    <div class="content container">
        <div class="row">
            <div class="col-xs-6">
                <img class="footer-logo" src="img/logo-dark.png" alt="Worldison International Logo">
            </div>
            <div class="col-xs-6 text-right">
                <p class="margin-b-0"><a class="fweight-700" href="#">Worldison International</a> </p>
            </div>
        </div>
        <!--// end row -->
    </div>
    <!-- End Copyright -->
</footer>
<!--========== END FOOTER ==========-->


        <!-- Back To Top -->
        <a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>

        <!-- JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- CORE PLUGINS -->
        <script src="vendor/jquery.min.js" type="text/javascript"></script>
        <script src="vendor/jquery-migrate.min.js" type="text/javascript"></script>
        <script src="vendor/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

        <!-- PAGE LEVEL PLUGINS -->
        <script src="vendor/jquery.easing.js" type="text/javascript"></script>
        <script src="vendor/jquery.back-to-top.js" type="text/javascript"></script>
        <script src="vendor/jquery.smooth-scroll.js" type="text/javascript"></script>
        <script src="vendor/jquery.wow.min.js" type="text/javascript"></script>
        <script src="vendor/swiper/js/swiper.jquery.min.js" type="text/javascript"></script>
        <script src="vendor/masonry/jquery.masonry.pkgd.min.js" type="text/javascript"></script>
        <script src="vendor/masonry/imagesloaded.pkgd.min.js" type="text/javascript"></script>

        <!-- PAGE LEVEL SCRIPTS -->
        <script src="js/layout.min.js" type="text/javascript"></script>
        <script src="js/components/wow.min.js" type="text/javascript"></script>
        <script src="js/components/swiper.min.js" type="text/javascript"></script>
        <script src="js/components/masonry.min.js" type="text/javascript"></script>
      
        <!-- Lightbox JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    </body>
    <!-- END BODY -->
</html>