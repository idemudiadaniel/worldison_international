
<?php
$pageTitle = 'Coming Soon – Worldison International Ltd';
?>
<?php require_once __DIR__ . "/inc/head.php"; ?>
<?php require_once __DIR__ . "/inc/header.php"; ?>
<div class="coming-container">
    <img src="img/logo.png" alt="Worldison Logo" style="max-width:150px; margin-bottom:20px;">
    <h1>We’re Coming Soon!</h1>
    <p>Worldison International Ltd is preparing something amazing for you.<br>
       Cleaning • Fumigation • Pest Control • Safety • tutorials <br>
      Worldison Academy</p>

    <!-- Countdown (optional) -->
    <div class="countdown" id="countdown">Launching Soon</div>

    <a href="https://wa.me/2348130826625" class="btn btn-whatsapp" target="_blank">
      <i class="fab fa-whatsapp"></i> Chat with Us
    </a>

    <div class="social" style="margin-top:30px;">
      <a href="https://www.facebook.com/wsfcompany"><i class="fab fa-facebook-f"></i></a>
      <a href="https://www.instagram.com/worldison_sfc"><i class="fab fa-instagram"></i></a>
      <a href="https://www.twitter.com/worldison"><i class="fab fa-twitter"></i></a>
      <a href="https://www.youtube.com/@worldison"><i class="fab fa-youtube"></i></a>
    </div>
  </div>

  <script>
    // Simple countdown
    const targetDate = new Date("2025-12-01T00:00:00").getTime();
    const countdown = document.getElementById("countdown");

    setInterval(() => {
      const now = new Date().getTime();
      const distance = targetDate - now;
      if (distance < 0) {
        countdown.innerHTML = "We Are Live!";
        return;
      }
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      countdown.innerHTML = `Launching in: ${days}d ${hours}h ${minutes}m`;
    }, 60000);
  </script>
<?php <?php require_once __DIR__ . "/inc/footer.php"; ?>
</html>
<?php require_once __DIR__ . "/inc/scripts.php"; ?>
