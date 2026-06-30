
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="robots" content="noindex, nofollow"> <!-- Prevent indexing -->
    <title>iceHRMAdmin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
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
  </head>
  <body>
          <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
            <div class="card col-lg-4 mx-auto">
              <div class="card-body px-5 py-5">
                <h3 class="card-title text-left mb-3">Login</h3>

                <?php if (isset($_GET['error'])): ?>
                  <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']); ?>
                  </div>
                <?php endif; ?>

                <form method="POST" action="app/auth.php">
                  <div class="form-group">
                    <label>Username or Email *</label>
                    <input type="text" name="username" class="form-control p_input" required>
                  </div>
                  <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" class="form-control p_input" required>
                  </div>
                  <div class="form-group d-flex align-items-center justify-content-between">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input"> Remember me 
                      </label>
                    </div>
                    <a href="/attendance/clockin.php" class="forgot-pass">Clockin</a>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Login</button>
                  </div>
                  <button id="installBtn" style="display:none;" class="btn btn-primary btn-block">
  Install App
</button>

                </form>

              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- row ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
<script>
let deferredPrompt;

window.addEventListener("beforeinstallprompt", (e) => {
  e.preventDefault();
  deferredPrompt = e;

  document.getElementById("installBtn").style.display = "block";

  document.getElementById("installBtn").addEventListener("click", () => {
    deferredPrompt.prompt();
    deferredPrompt.userChoice.then(() => {
      deferredPrompt = null;
      document.getElementById("installBtn").style.display = "none";
    });
  });
});
</script>
<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/sw.js")
    .then(() => console.log("Service Worker registered"))
    .catch((err) => console.error("SW registration failed:", err));
}
</script>


    <!-- endinject -->
  </body>
                </html>