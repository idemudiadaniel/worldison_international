<?php
require_once __DIR__ . "/../inc/db.php"; // mysqli connection ($conn)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['clientName']);
    $email    = trim($_POST['email']);
    $location = trim($_POST['location']);
    $date     = !empty($_POST['serviceDate']) ? $_POST['serviceDate'] : null;
    $urgent   = isset($_POST['urgentCheckbox']) ? "Yes" : "No";
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : "";

    // Insert into DB (mysqli)
    $stmt = $conn->prepare("INSERT INTO bookings (name, email, location, services, date_needed, urgent, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssss", $name, $email, $location, $services, $date, $urgent);
    $stmt->execute();
    $stmt->close();

    // WhatsApp message
    $msg = "Hello, I want to book a service.\n"
    . "Name: $name\n"
    . "Email: $email\n"
    . "Location: $location\n"
    . "Services: $services\n"
    . "Date Needed: $date\n"
    . "Urgent: $urgent";

$msg = urlencode($msg);
header("Location: https://wa.me/2348130826625?text=$msg");
exit;

}
?>

<!DOCTYPE html>

<html lang="en" class="no-js">
   
    <head>
        <meta charset="utf-8"/>
        <title>Book a Service – Worldison International Ltd</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta content="worldison international Ldt, your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services." name="description"/>
        <meta content="optimiscyber" name="author"/>
        <link href="http://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet" type="text/css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/animate.css" rel="stylesheet">
        <!-- Lightbox CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
        <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet" type="text/css"/>
        <link href="css/layout.min.css" rel="stylesheet" type="text/css"/>
        <!-- Favicons -->
        <link href="img/favicon.png" rel="icon">
        <link href="img/apple-touch-icon.png" rel="apple-touch-icon">
</head>
  <style>
    body { background: #f8f9fa; padding: 40px 10px; }
    .booking-container {
      max-width: 650px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    .booking-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #28a745;
      font-weight: bold;
    }
    label { font-weight: 600; margin-top: 12px; }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
      transition: border-color 0.2s ease-in-out;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #28a745;
      outline: none;
      box-shadow: 0 0 5px rgba(40,167,69,0.2);
    }
    .services-box { border: 1px solid #ddd; padding: 12px; background: #f9f9f9; margin-top: 6px; }
    .services-box label { display: block; margin-bottom: 8px; cursor: pointer; }
    .services-box input[type="checkbox"] { margin-right: 8px; accent-color: #28a745; }
    button { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
    .btn-primary { background: #28a745; color: white; transition: background 0.3s; }
    .btn-primary:hover { background: #218838; }
    .btn-secondary { background: #6c757d; color: white; margin-left: 10px; }
  
    .urgent-box {
  margin-top: 10px;
  white-space: nowrap; /* keeps text on the same line */
}

.urgent-box label {
  display: inline-block;
  vertical-align: middle;
  margin: 0;           /* remove any default spacing */
  font-weight: 600;
  cursor: pointer;
}
.urgent-box input[type="checkbox"] {
  appearance: none;        /* remove browser default style */
  -webkit-appearance: none;
  -moz-appearance: none;

  width: 16px;
  height: 16px;
  border: 2px solid #000;
  border-radius: 3px;
  display: inline-block;
  vertical-align: middle;
  margin: 0 3px 0 0;       /* tight spacing */
  cursor: pointer;
  position: relative;
}

.urgent-box input[type="checkbox"]:checked::after {
  content: "✓";
  position: absolute;
  top: -2px;
  left: 2px;
  font-size: 14px;
}

#serviceList label {
  display: block;              /* each service on its own line */
  margin: 4px 0;
  cursor: pointer;
}

#serviceList input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;

  width: 16px;
  height: 16px;
  border: 2px solid #000;
  border-radius: 3px;
  display: inline-block;
  vertical-align: middle;
  margin: 0 4px 0 0;   /* tight gap before text */
  position: relative;
  cursor: pointer;
}

#serviceList input[type="checkbox"]:checked::after {
  content: "✓";
  position: absolute;
  top: -2px;
  left: 2px;
  font-size: 14px;
}

  </style>
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
              <li class="js_nav-item nav-item"><a class="nav-item-child nav-item-hover" href="index.php">Home</a></li>
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
  </header><br><br>
<div class="booking-container">
  <h2>Book a Service</h2>
  <form method="POST" action="">
    <label>Your Name</label>
    <input type="text" name="clientName" placeholder="Enter your name" required>

    <label>Service Needed</label>
<div id="serviceSelectContainer">
  <div id="serviceDropdown" onclick="toggleServiceList()" 
       style="padding:10px; border:1px solid #ccc; border-radius:6px; cursor:pointer; background:#f9f9f9;">
    Select services...
  </div>
  <div id="serviceList" 
     style="display:none; border:1px solid #ccc; border-radius:6px; background:#fff; max-height:200px; overflow-y:auto; padding:10px; margin-top:5px; white-space:nowrap;">

  <!-- Cleaning Services -->
  <label><input type="checkbox" name="services[]" value="Residential Cleaning"> Residential Cleaning</label>
  <label><input type="checkbox" name="services[]" value="Office Cleaning"> Office Cleaning</label>
  <label><input type="checkbox" name="services[]" value="Post-Construction Cleaning"> Post-Construction Cleaning</label>
  <label><input type="checkbox" name="services[]" value="Industrial Cleaning"> Industrial Cleaning</label>
  <label><input type="checkbox" name="services[]" value="Carpet & Upholstery Cleaning"> Carpet & Upholstery Cleaning</label>
  <label><input type="checkbox" name="services[]" value="Window Cleaning"> Window Cleaning</label>

  <!-- Fumigation & Pest Control -->
  <label><input type="checkbox" name="services[]" value="Home Fumigation"> Home Fumigation</label>
  <label><input type="checkbox" name="services[]" value="Office Fumigation"> Office Fumigation</label>
  <label><input type="checkbox" name="services[]" value="Pest Control"> Pest Control</label>
  <label><input type="checkbox" name="services[]" value="Termite Control"> Termite Control</label>
  <label><input type="checkbox" name="services[]" value="Rodent Control"> Rodent Control</label>
  <label><input type="checkbox" name="services[]" value="Bed Bug Treatment"> Bed Bug Treatment</label>

  <!-- Safety & Fire -->
  <label><input type="checkbox" name="services[]" value="Fire Extinguisher Installation"> Fire Extinguisher Installation</label>
  <label><input type="checkbox" name="services[]" value="Fire Extinguisher Refilling"> Fire Extinguisher Refilling</label>
  <label><input type="checkbox" name="services[]" value="Fire Safety Training"> Fire Safety Training</label>
  <label><input type="checkbox" name="services[]" value="Safety Equipment Supply"> Safety Equipment Supply</label>

  <!-- Security & IT -->
  <label><input type="checkbox" name="services[]" value="Security Guard Services"> Security Guard Services</label>
  <label><input type="checkbox" name="services[]" value="CCTV Installation"> CCTV Installation</label>
  <label><input type="checkbox" name="services[]" value="Access Control Systems"> Access Control Systems</label>
  <label><input type="checkbox" name="services[]" value="Alarm & Surveillance Systems"> Alarm & Surveillance Systems</label>

  <!-- General Contracting -->
  <label><input type="checkbox" name="services[]" value="Facility Maintenance"> Facility Maintenance</label>
  <label><input type="checkbox" name="services[]" value="General Contracting"> General Contracting</label>
  <label><input type="checkbox" name="services[]" value="Manpower Outsourcing"> Manpower Outsourcing</label>
  <label><input type="checkbox" name="services[]" value="Equipment Supply"> Equipment Supply</label>

  <!-- Other -->
  <label><input type="checkbox" name="services[]" value="Sanitization & Disinfection"> Sanitization & Disinfection</label>
  <label><input type="checkbox" name="services[]" value="Training & Consultancy"> Training & Consultancy</label>

</div>

</div>


    <label>Location</label>
    <input type="text" name="location" placeholder="Enter your city or state" required list="nigeriaLocations">
    <datalist id="nigeriaLocations">
      <option value="Abuja">
      <option value="Lagos">
      <option value="Port Harcourt">
      <option value="Kano">
      <option value="Enugu">
      <option value="Benin City">
      <option value="Ibadan">
      <option value="Kaduna">
      <option value="Jos">
      <option value="Abeokuta">
    </datalist>

    <label>Email Address</label>
    <input type="email" name="email" placeholder="Enter your email" required>

    <label>Date Needed</label>
    <input type="date" name="serviceDate">
    <div class="urgent-box">
      <label><input type="checkbox" name="urgentCheckbox" value="1"> Urgent Service</label>
    </div>





    <div style="text-align:center; margin-top:25px;">
      <button type="submit" class="btn-primary">Submit & Chat</button>
      <button type="reset" class="btn-secondary">Clear</button>
    </div>
  </form>
</div>
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
                        <li class="footer-list-item"><a href="/login.php"><i class="fas fa-users"></i> Portal</a></li>
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
        
        <script>
          function toggleServiceList() {
            const list = document.getElementById("serviceList");
            list.style.display = (list.style.display === "block") ? "none" : "block";
          }
        </script>
    </body>
    <!-- END BODY -->
</html>

