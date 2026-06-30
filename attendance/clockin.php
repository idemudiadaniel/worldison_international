<?php include("../inc/db.php"); 
date_default_timezone_set('Africa/Lagos');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>iceHRM- Staff Clock</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap & Corona Template CSS -->
  <link rel="stylesheet" href="../assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="../assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="../assets/css/style.css">

  
  <style>
    body {
      background: url('../assets/images/lockscreen-bg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .auth-bg {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .navbar {
      background: rgba(0,0,0,0.6) !important; /* translucent overlay */
    }
    .navbar .navbar-brand, 
    .navbar .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .card {
      border-radius: 10px;
      background: rgba(0,0,0,0.6);
    }
    footer {
      margin-top: 20px;
      font-size: 14px;
      color: #777;
      text-align: center;
      
    }
    .navbar {
  background: rgba(0, 0, 0, 0.7) !important;
  width: 100%;
  left: 0;
  right: 0;
}

  </style>
</head>
<body>

<!-- ✅ Responsive Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top">
<div class="container-fluid px-4"> 
    <a class="navbar-brand fw-bold" href="/login.php">iceHRMClock</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="/login.php">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<div class="auth-bg">
  <div class="card col-lg-5 mx-auto shadow">
    <div class="card-body px-5 py-5">

      <!-- Logo -->
      <div class="text-center mb-3">
        <img src="../assets/images/logo.png" alt="Company Logo" class="img-fluid" style="max-width:200px;">
        <p class="mt-2">Time & Attendance Management System 1.0</p>
      </div>

      <!-- Step 1: Select Clock Type -->
      <div id="step1" class="text-center">
        <button class="btn btn-outline-success me-2" onclick="selectType('in')">CLOCK-IN</button>
        <button class="btn btn-outline-danger" onclick="selectType('out')">CLOCK-OUT</button>
      </div>

      <!-- Step 2: Enter Staff ID -->
      <div id="step2" class="mt-4" style="display:none;">
        <h5 class="mb-3 text-center">Enter Staff ID</h5>
        <input type="text" id="staff_id" class="form-control mb-3" placeholder="Staff ID">
        <button class="btn btn-primary w-100" onclick="checkStaff()">Proceed</button>
      </div>

      <!-- Step 3: Confirm Staff -->
      <div id="step3" class="mt-4" style="display:none;">
        <h5 class="mb-3 text-center">Confirm Staff</h5>
        <p><strong>Name:</strong> <span id="staff_name"></span></p>
        <p><strong>Job Title:</strong> <span id="staff_job"></span></p>
        <button class="btn btn-warning w-100" onclick="readyCamera()">Are you ready?</button>
      </div>

 <!-- Step 4: Take Photo -->
<div id="step4" class="mt-4" style="display:none;">
  <h5 class="mb-3 text-center">Take Photo</h5>

  <!-- Video feed -->
  <video id="video" autoplay playsinline style="width:100%; border-radius:5px;"></video>

  <!-- Canvas (hidden, internal use) -->
  <canvas id="canvas" width="400" height="300" style="display:none;"></canvas>

  <!-- Photo preview -->
  <img id="photo_preview" style="display:none; width:100%; margin:10px 0; border:2px solid #333; border-radius:5px;">

  <input type="hidden" id="photo_data">

  <button id="capture_btn" class="btn btn-secondary w-100 mb-2" onclick="capturePhoto()">📸 Take Photo</button>
  <button id="retake_btn" class="btn btn-warning w-100 mb-2" onclick="retakePhoto()" style="display:none;">🔄 Retake Photo</button>
  <button id="submit_btn" class="btn btn-success w-100" onclick="submitClock()" style="display:none;">✅ Submit</button>
</div>


      <footer>
        <p>Powered by iceHRMInternational</p>
      </footer>

    </div>
  </div>
</div>

<!-- Scripts -->
<script>
let clockType = '';
let staff_id = '';
let videoStream;
let userLat = null;
let userLong = null;

// Ask for location
document.addEventListener("DOMContentLoaded", ()=>{
  if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(pos){
      userLat = pos.coords.latitude;
      userLong = pos.coords.longitude;
    }, function(){
      alert("⚠️ Location permission required for clocking.");
    });
  }
});

function selectType(type){
  clockType = type;
  document.getElementById('step1').style.display = "none";
  document.getElementById('step2').style.display = "block";
}

function checkStaff(){
  staff_id = document.getElementById('staff_id').value.trim();
  if(!staff_id){ alert("Enter Staff ID"); return; }

  fetch("get_staff.php?staff_id="+staff_id)
  .then(res=>res.json())
  .then(data=>{
    if(data.status === 'success'){
      document.getElementById('staff_name').innerText = data.name;
      document.getElementById('staff_job').innerText = data.job;
      document.getElementById('step2').style.display = "none";
      document.getElementById('step3').style.display = "block";
    }else{
      alert("❌ Staff not found");
    }
  })
  .catch(()=>alert("Network error!"));
}

function readyCamera(){
  document.getElementById('step3').style.display = "none";
  document.getElementById('step4').style.display = "block";
  startCamera();
}

function startCamera(){
  if(videoStream){ videoStream.getTracks().forEach(track => track.stop()); }

  navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then(stream => { 
      videoStream = stream;
      const video = document.getElementById('video');
      video.srcObject = stream;
      video.style.display = "block";
    })
    .catch(err => { alert("Camera error: "+err); });
}

function capturePhoto(){
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const ctx = canvas.getContext('2d');

  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  const dataURL = canvas.toDataURL('image/png');
  document.getElementById('photo_data').value = dataURL;

  const preview = document.getElementById('photo_preview');
  preview.src = dataURL;
  preview.style.display = "block";

  // Hide video and capture button
  video.style.display = "none";
  document.getElementById('capture_btn').style.display = "none";

  // Show submit & retake buttons
  document.getElementById('submit_btn').style.display = "block";
  document.getElementById('retake_btn').style.display = "block";

  // Stop camera stream
  if(videoStream){ videoStream.getTracks().forEach(track => track.stop()); }
}

function retakePhoto(){
  const video = document.getElementById('video');
  const preview = document.getElementById('photo_preview');

  // Clear previous photo
  document.getElementById('photo_data').value = "";
  preview.style.display = "none";

  // Show video and capture button
  video.style.display = "block";
  document.getElementById('capture_btn').style.display = "block";

  // Hide submit & retake buttons
  document.getElementById('submit_btn').style.display = "none";
  document.getElementById('retake_btn').style.display = "none";

  // Restart camera
  startCamera();
}


function submitClock(){
  if(!document.getElementById('photo_data').value){
    alert("Take a photo first!");
    return;
  }
  if(!userLat || !userLong){
    alert("⚠️ Location not available. Enable GPS.");
    return;
  }

  fetch("save_clock.php", {
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:"staff_id="+staff_id+"&clock_type="+clockType+"&photo_data="+encodeURIComponent(document.getElementById('photo_data').value)+"&lat="+userLat+"&long="+userLong
  })
  .then(res=>res.json())
  .then(data=>{
    if(data.status === 'success'){
      alert("✅ Clock "+clockType+" successful!");
      window.location.href = "clockin.php";
    }else{
      alert("❌ "+data.message);
    }
  })
  .catch(()=>alert("Network error!"));
}
// Listen for messages from Cordova app
window.addEventListener("message", (event) => {
  if (event.origin !== "https://worldison.org") return;

  if (event.data.type === "clockin-photo") {
    document.getElementById("photo_preview").src = event.data.image;
    document.getElementById("photo_preview").style.display = "block";
    document.getElementById("photo_data").value = event.data.image;

    // Hide video if open
    const video = document.getElementById("video");
    if (video) video.style.display = "none";
    document.getElementById("submit_btn").style.display = "block";
  }
});
const now = new Date();
const localTime = now.toISOString(); // User's local time in ISO format
const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

// When clocking in:
fetch("https://worldison.org/api/clockin.php", {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({
    latitude,
    longitude,
    timezone,
    localTime,
  }),
});

</script>
<!-- ✅ Correct Script Order -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="../assets/vendors/js/vendor.bundle.base.js"></script>
<script src="../assets/js/off-canvas.js"></script>
<script src="../assets/js/hoverable-collapse.js"></script>
<script src="../assets/js/misc.js"></script>
</body>
</html>
