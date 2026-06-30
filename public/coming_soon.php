<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Coming Soon – Worldison International Ltd</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <meta content="Worldison International Ltd – Cleaning, Fumigation, Pest Control, Fire Safety & General Contracting. Coming Soon." name="description"/>
  <meta content="optimiscyber" name="author"/>
  <link href="http://fonts.googleapis.com/css?family=Hind:300,400,500,600,700" rel="stylesheet" type="text/css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <style>
    body {
      background: linear-gradient(135deg, #28a745, #218838);
      color: #fff;
      font-family: 'Hind', sans-serif;
      text-align: center;
      padding: 80px 20px;
    }
    .coming-container {
      max-width: 700px;
      margin: auto;
      background: rgba(0,0,0,0.3);
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h1 {
      font-size: 48px;
      font-weight: 700;
      margin-bottom: 20px;
    }
    p {
      font-size: 18px;
      margin-bottom: 25px;
    }
    .countdown {
      font-size: 24px;
      font-weight: bold;
      margin: 20px 0;
    }
    .social a {
      color: #fff;
      font-size: 22px;
      margin: 0 12px;
      transition: color 0.3s;
    }
    .social a:hover {
      color: #ffe066;
    }
    .btn {
      padding: 12px 24px;
      border-radius: 6px;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
    }
    .btn-whatsapp {
      background: #25D366;
      color: #fff;
    }
    .btn-whatsapp:hover {
      background: #1ebe5b;
      color: #fff;
    }
  </style>
</head>
<body>
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
</body>
</html>
