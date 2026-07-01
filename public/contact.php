<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Worldison Contact Page</title>

  <!-- SEO Meta -->
  <meta name="description" content="Worldison International Blog - Updates, insights, and stories.">
  <meta name="robots" content="index, follow">

  <!-- Mobile Responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

  <!-- Vendor CSS -->
  <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet">

  <!-- Custom Styles -->
  <link href="css/animate.css" rel="stylesheet">
  <link href="css/layout.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

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

<section class="contact">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="contact-information">
          <h2>Contact Us</h2>
          <p>We’d love to hear from you! Whether you have a question, feedback, or need support, 
          our team is ready to assist you.</p>

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

            <div class="col-sm-4 sm-margin-b-50">
              <h3><a href="#">Lagos Office</a> <span class="text-uppercase margin-l-20">Branch</span></h3>
              <p>10 Efunshetan Street, off Seriki Street, by Araromi Junction, Iyana Ipaja, Lagos State.</p>
              <ul class="list-unstyled contact-list">
                <li><i class="margin-r-10 color-base icon-call-out"></i> (+234) 905 201 5651</li>
                <li><i class="margin-r-10 color-base icon-envelope"></i> worldisonsfc@gmail.com</li>
              </ul>
            </div>

            <div class="col-sm-4 sm-margin-b-50">
              <h3><a href="#">Abuja Office</a> <span class="text-uppercase margin-l-20">Branch</span></h3>
              <p>36 Tatieye Crescent, Off Berger Quarry, Mpapa, Abuja.</p>
              <ul class="list-unstyled contact-list">
                <li><i class="margin-r-10 color-base icon-call-out"></i> (+234) 706 716 8179</li>
                <li><i class="margin-r-10 color-base icon-envelope"></i> worldisonsfc@gmail.com</li>
              </ul>
            </div>
          </div><!-- End row -->
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Contact Form -->
      <form id="contactForm" class="row" action="contact_process.php" method="POST">
        <div class="col-md-6 mb-3">
          <input type="text" class="form-control" name="name" placeholder="Your Name" required>
        </div>
        <div class="col-md-6 mb-3">
          <input type="email" class="form-control" name="email" placeholder="Your Email" required>
        </div>
        <div class="col-md-12 mb-3">
          <textarea class="form-control" name="message" rows="8" placeholder="Message here…" required></textarea>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="agree" required>
          <label class="form-check-label" for="agree">
           .... I agree that my submitted data is being collected and stored.
          </label>
        </div>

        <div class="col-lg-12">
          <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
      </form>


      <!-- Google Map -->
      <div class="col-lg-6">
        <div class="map height-300">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.103193248315!2d5.6108137000000005!3d6.3806796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x10472d5c8a7b1359%3A0x5bba66ba67794731!2sWORLDISON%20SAFETY%20COMPANY!5e0!3m2!1sen!2sng!4v1760383647810!5m2!1sen!2sng"
            width="100%" 
            height="100%" 
            frameborder="0" 
            style="border:0;" 
            allowfullscreen>
          </iframe>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Instagram Section -->
<section class="instagram">
  <a href="#">
    <i class="fa fa-instagram" aria-hidden="true"></i>
    <span>@worldison_sfc</span>
  </a>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="instagram-item">
          <div class="instagram-item-thum"><img src="images/blog/case-studies-1.png" alt="Instagram image 1"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-2.png" alt="Instagram image 2"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-3.png" alt="Instagram image 3"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-4.png" alt="Instagram image 4"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-5.png" alt="Instagram image 5"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-6.png" alt="Instagram image 6"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/2348130826625?text=Hello%20Worldison,%20I%20would%20like%20to%20book%20a%20service" 
   target="_blank" 
   style="position: fixed; bottom: 20px; right: 20px; background-color: #25d366; color: white; border-radius: 30px; padding: 12px 20px; font-size: 16px; font-weight: bold; text-align: center; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.3); z-index: 999; display: flex; align-items: center; gap: 8px;">
    💬 Book Us Now
</a>

<!-- Footer -->
<footer class="footer">
  <div class="section-seperator">
    <div class="content-md container">
      <div class="row">
        <!-- Navigation Links -->
        <div class="col-sm-2 sm-margin-b-30">
                        <li class="footer-list-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li class="footer-list-item"><a href="services.php"><i class="fas fa-concierge-bell"></i> Services</a></li>
                        <li class="footer-list-item"><a href="about.php"><i class="fas fa-users"></i> About Us</a></li>
                        <li class="footer-list-item"><a href="contact.php"><i class="fas fa-envelope-open-text"></i> Contact</a></li>
                        <li class="footer-list-item"><a href="blog.php"><i class="fas fa-envelope-open-text"></i> blog</a></li>
                        <li class="footer-list-item"><a href="#"><i class="fas fa-concierge-bell"></i> Academy</a></li>
                        <li class="footer-list-item"><a href="../login.php"><i class="fas fa-users"></i> Portal</a></li>
                    </ul>
        </div>

        <!-- Social Links -->
        <div class="col-sm-2 sm-margin-b-30">
          <ul class="list-unstyled footer-list">
            <li><a href="https://www.twitter.com/worldison" target="_blank"><i class="fab fa-twitter mr-2"></i> Twitter</a></li>
            <li><a href="https://www.facebook.com/wsfcompany" target="_blank"><i class="fab fa-facebook-f mr-2"></i> Facebook</a></li>
            <li><a href="https://www.instagram.com/worldison_sfc" target="_blank"><i class="fab fa-instagram mr-2"></i> Instagram</a></li>
            <li><a href="https://www.youtube.com/@worldison" target="_blank"><i class="fab fa-youtube mr-2"></i> YouTube</a></li>
            <li><a href="https://www.google.com/search?q=WORLDISON+SAFETY+COMPANY" target="_blank"><i class="fab fa-google mr-2"></i> Google</a></li>
          </ul>
        </div>

        <!-- Extra Links -->
        <div class="col-sm-3">
          <ul class="list-unstyled footer-list">
            <li><a href="refund-policy.php"><i class="fas fa-newspaper mr-2"></i> Refund Policy</a></li>
            <li><a href="privacy.php"><i class="fas fa-user-shield mr-2"></i> Privacy Policy</a></li>
            <li><a href="terms.php"><i class="fas fa-file-contract mr-2"></i> Terms &amp; Conditions</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-sm-5">
          <ul class="list-unstyled footer-list">
            <li><i class="fas fa-phone mr-2"></i> (+234) 8130826625, (+234)9052015651, (+234)7067168179</li>
            <li><i class="fas fa-envelope mr-2"></i> info@worldison.org, worldisonsfc@gmail.com</li>
            <li><i class="fas fa-map-marker-alt mr-2"></i> 197, Ugbowo Opp. Union Bank, Benin City, Edo State, Nigeria</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="content container">
    <div class="row">
      <div class="col-xs-6">
        <img class="footer-logo" src="img/logo-dark.png" alt="Worldison International Logo">
      </div>
      <div class="col-xs-6 text-right">
        <p class="margin-b-0">
          <a class="fweight-700" href="#">Worldison International</a> 
          
        </p>
      </div>
    </div>
  </div>
</footer>


<!-- Back To Top -->
<a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>

<!-- JAVASCRIPTS -->
<script src="vendor/jquery.min.js"></script>
<script src="vendor/jquery-migrate.min.js"></script>

<!-- ✅ Popper.js (needed for Bootstrap collapse/toggle) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="vendor/jquery.easing.js"></script>
<script src="vendor/jquery.back-to-top.js"></script>
<script src="vendor/jquery.smooth-scroll.js"></script>
<script src="vendor/jquery.wow.min.js"></script>
<script src="vendor/swiper/js/swiper.jquery.min.js"></script>
<script src="vendor/masonry/jquery.masonry.pkgd.min.js"></script>
<script src="vendor/masonry/imagesloaded.pkgd.min.js"></script>
<script src="js/layout.min.js"></script>
<script src="js/components/wow.min.js"></script>
<script src="js/components/swiper.min.js"></script>
<script src="js/components/masonry.min.js"></script>

<!-- Google Map -->
<script src="vendor/slick/slick.min.js"></script>
<script src="js/script.js"></script>

</body>

</body>
</html>
