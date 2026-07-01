<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Worldison about page</title>

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
          <a class="nav-link" href="https://academy.worldison.org">Our Academy</a>
        </li>
      </ul>

      
    </div>
  </div>
</nav>
<!-- Navbar End -->



<section class="contact about-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="contact-information text-left">
          <h2>About Us</h2>
          <p>
            Welcome to <strong>Worldison International</strong>, your trusted partner in
            <em>cleaning, fumigation, pest control, fire safety, and general contracting services</em>.
            With over 20 years of proven expertise, we are committed to creating cleaner, safer,
            and healthier environments for homes, businesses, and industries across Nigeria.
          </p>
        </div>

        <!-- About -->
        <div id="about">
          <div class="content-lg container">
            <!-- Masonry Grid -->
            <div class="masonry-grid row">
              <div class="masonry-grid-sizer col-xs-6 col-sm-6 col-md-1"></div>

              <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4 sm-margin-b-30">
                <div class="margin-b-60">
                  <h3>Cleaning &amp; Sanitation</h3>
                  <p>
                    From residential to industrial spaces, our skilled cleaners deliver spotless results
                    using eco-friendly products and modern equipment. We guarantee comfort, hygiene, and
                    safety every time.
                  </p>
                </div>
                <img class="full-width img-responsive wow fadeInUp"
                     src="img/500x500/02.jpg"
                     alt="Cleaning Service"
                     data-wow-duration=".3"
                     data-wow-delay=".2s">
              </div>

              <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4">
                <div class="margin-b-60">
                  <h3>Pest Control &amp; Fumigation</h3>
                  <p>
                    Protect your home and workplace with our professional fumigation services.
                    We specialize in long-lasting, environmentally safe treatments against insects,
                    rodents, and disease-spreading pests.
                  </p>
                </div>
                <img class="full-width img-responsive wow fadeInUp"
                     src="img/500x500/01.jpg"
                     alt="Fumigation Service"
                     data-wow-duration=".3"
                     data-wow-delay=".3s">
              </div>

              <div class="masonry-grid-item col-xs-12 col-sm-6 col-md-4">
                <div class="margin-t-60 margin-b-60">
                  <h3>Fire Safety Solutions</h3>
                  <p>
                    We install and maintain certified fire extinguishers, smoke detectors, and
                    alarm systems. Our solutions safeguard lives, property, and ensure compliance
                    with safety regulations.
                  </p>
                </div>
                <img class="full-width img-responsive wow fadeInUp"
                     src="img/500x500/03.jpg"
                     alt="Fire Safety"
                     data-wow-duration=".3"
                     data-wow-delay=".4s">
              </div>
            </div>
            <!-- End Masonry Grid -->
          </div>

          <div class="col-md-5 col-sm-5 md-margin-b-60">
            <div class="margin-t-50 margin-b-30">
              <h3>Why Choose Worldison?</h3>
              <p>
                At Worldison International, we combine <strong>expertise, innovation, and eco-conscious
                practices</strong> to deliver services that consistently exceed expectations.
                Our mission is to protect health, enhance comfort, and support safer living
                and working environments.
              </p>
            </div>
            
            <!-- Buttons Row -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
              <!-- Book Now -->
              <a href="#bookingPopup" 
                onclick="document.getElementById('bookingPopup').style.display='block'; return false;" 
                class="btn-theme btn-theme-sm btn-success text-uppercase">
                Book Us Now
              </a>

              <!-- Learn More -->
              <a href="about-vision.php" class="btn-theme btn-theme-sm btn-primary text-uppercase">
                Learn More
              </a>
            </div>
          </div>


                <div class="col-md-5 col-sm-7 col-md-offset-2">
                  <!-- Accordion -->
                  <div class="accordion">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingOne">
                          <h5 class="panel-title">
                            <a class="panel-title-child" role="button" data-toggle="collapse" data-parent="#accordion"
                               href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                              Comprehensive Solutions
                            </a>
                          </h5>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                          <div class="panel-body">
                            From cleaning and pest control to fire safety and general contracting, 
                            we provide all-in-one services to simplify your facility management needs.
                          </div>
                        </div>
                      </div>

                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingTwo">
                          <h5 class="panel-title">
                            <a class="collapsed panel-title-child" role="button" data-toggle="collapse"
                               data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                              Certified Professionals
                            </a>
                          </h5>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                          <div class="panel-body">
                            Our team of technicians and contractors are highly trained, licensed, 
                            and equipped with industry-standard tools to deliver quality and safety
                            without compromise.
                          </div>
                        </div>
                      </div>

                      <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="headingThree">
                          <h5 class="panel-title">
                            <a class="collapsed panel-title-child" role="button" data-toggle="collapse"
                               data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                              Proven Track Record
                            </a>
                          </h5>
                        </div>
                        <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                          <div class="panel-body">
                            Trusted by residential, corporate, and industrial clients across Nigeria,
                            we pride ourselves on building lasting relationships and delivering 
                            consistent, excellent results.
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
                  <!-- Vision and Mission Section -->
          <section class="page-section " id="vision-mission">
            <div class="container">
              <!-- Title -->
              <div class="row">
                <div class="col-md-12 Left">
                  <h2 class="section-title">🌍 Vision & Mission</h2>
                  <p class="text-muted">Our guiding principles and commitments to clients, partners, and communities worldwide.</p>
                </div>
              </div>

              <!-- Vision -->
              <div class="row mt-4">
                <div class="col-md-12">
                  <h3 class="section-subtitle">🌍 Vision Statement</h3>
                  <p>
                    To be a globally recognized leader in facility management, safety solutions, manpower outsourcing, 
                    and integrated services, setting the benchmark for excellence, innovation, and reliability while 
                    positively impacting lives and businesses worldwide.
                  </p>
                </div>
              </div>

              <!-- Mission -->
              <div class="row mt-4">
                <div class="col-md-12">
                  <h3 class="section-subtitle">🌍 Mission Statement</h3>
                  <ul>
                    <li>Deliver high-quality, sustainable, and customer-focused services in facility management, fumigation, janitorial solutions, PPE supply, fire safety, training, and general merchandise.</li>
                    <li>Provide professional manpower outsourcing that empowers businesses and creates opportunities for growth.</li>
                    <li>Uphold the highest standards of safety, integrity, and accountability in every operation.</li>
                    <li>Leverage innovation and expertise to exceed client expectations and foster long-term partnerships.</li>
                    <li>Build a dedicated, skilled, and transparent workforce, ensuring trust and excellence in service delivery.</li>
                  </ul>
                </div>
              </div>
            </div>
          </section>

        <!-- End About -->
      </div>
    </div>
  </div>
</section>

<section class="instagram">
  <a href="https://www.instagram.com/worldison_sfc" target="_blank">
    <i class="fa fa-instagram" aria-hidden="true"></i>
    <span>@worldison_sfc</span>
  </a>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="instagram-item">
          <div class="instagram-item-thum"><img src="images/blog/case-studies-1.png" alt="Worldison Cleaning"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-2.png" alt="Worldison Pest Control"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-3.png" alt="Worldison Fire Safety"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-4.png" alt="Worldison Industrial Service"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-5.png" alt="Worldison Sanitation"></div>
          <div class="instagram-item-thum"><img src="images/blog/case-studies-6.png" alt="Worldison Team at Work"></div>
        </div>
      </div>
    </div>
  </div>
</section>

  

<!-- Floating "Book Us Now" WhatsApp Button -->
<a href="https://wa.me/2348130826625?text=Hello%20Worldison,%20I%20would%20like%20to%20book%20a%20service" 
   target="_blank" 
   style="position: fixed; bottom: 20px; right: 20px; background-color: #25d366; color: white; border-radius: 30px; padding: 12px 20px; font-size: 16px; font-weight: bold; text-align: center; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.3); z-index: 999; display: flex; align-items: center; gap: 8px;">
    💬 Book Us Now
</a>

<!--========== FOOTER ==========-->
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

<!--========== END FOOTER ==========-->

<!-- Back To Top -->
<a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>


<script src="vendor/jquery.min.js"></script>
<script src="vendor/jquery-migrate.min.js"></script>
<!-- ✅ Popper.js (needed for Bootstrap collapse/toggle) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- JAVASCRIPTS -->
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
</html>
