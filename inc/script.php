    <!-- jQuery, Popper, Bootstrap 4 (must come first) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Vendor bundle (Corona template) -->
 

    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap.min.js"></script>
    <script src="assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/vendors/owl-carousel-2/owl.carousel.min.js"></script>
    <!-- End plugin js for this page -->

    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <script src="assets/js/settings.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- endinject -->

    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
<!-- Service Worker registration -->
<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/sw.js")
    .then(() => console.log("Service Worker registered"))
    .catch((err) => console.error("SW failed:", err));
}
</script>

<div id="installPrompt" style="display:none; position:fixed; bottom:20px; right:20px; 
  background:#0055ff; color:white; padding:15px 20px; border-radius:12px; 
  cursor:pointer; z-index:9999;">
    Install Worldison App
</div>

<script>
let deferredPrompt;
const btn = document.getElementById("installPrompt");

window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    deferredPrompt = e;
    btn.style.display = "block";
});

btn.addEventListener("click", async () => {
    btn.style.display = "none";
    deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    deferredPrompt = null;
});
</script>


