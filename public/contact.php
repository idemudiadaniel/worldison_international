
<?php
$pageTitle = 'Worldison Contact Page';
$pageDescription = 'Worldison International Blog - Updates, insights, and stories.';
?>
<?php require_once __DIR__ . "/inc/head.php"; ?>
<?php require_once __DIR__ . "/inc/header.php"; ?>
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

<?php require_once __DIR__ . "/inc/footer.php"; ?>
<!-- Back To Top -->
<a href="javascript:void(0);" class="js-back-to-top back-to-top">Top</a>

<!-- JAVASCRIPTS -->



<!-- ✅ Popper.js (needed for Bootstrap collapse/toggle) -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>













<!-- Google Map -->
<?php require_once __DIR__ . "/inc/scripts.php"; ?>
