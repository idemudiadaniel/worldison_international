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
<?php
$pageTitle = 'Book a Service – Worldison International Ltd';
?>
<?php require_once __DIR__ . "/inc/head.php"; ?>
<?php require_once __DIR__ . "/inc/header.php"; ?>
<!-- Header -->
  
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
<?php <?php require_once __DIR__ . "/inc/footer.php"; ?>
<!--========== END FOOTER ==========-->


        <!-- Back To Top -->
        <a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>

        <!-- JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
        <!-- CORE PLUGINS -->
        
        
        

        <!-- PAGE LEVEL PLUGINS -->
        
        
        
        
        
        
        

        <!-- PAGE LEVEL SCRIPTS -->
        
        
        
        
      
        <!-- Lightbox JS -->
        
        
        <script>
          function toggleServiceList() {
            const list = document.getElementById("serviceList");
            list.style.display = (list.style.display === "block") ? "none" : "block";
          }
        </script>
<?php require_once __DIR__ . "/inc/scripts.php"; ?>
