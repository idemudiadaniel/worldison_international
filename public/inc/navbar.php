<?php
/**
 * Shared Navigation Bar for public pages.
 */
$navbarType = $navbarType ?? 'simple';
$currentPage = $currentPage ?? basename($_SERVER['SCRIPT_NAME'], '.php');
$navGroups = [
    'index' => ['index'],
    'about' => ['about', 'about-vision'],
    'blog' => ['blog', 'category', 'single-blog', 'static-blog'],
    'services' => ['services', 'cleaning', 'fumigation', 'disinfection', 'fire-training', 'fire-extinguishers', 'safety', 'it-security', 'manpower', 'facility', 'merchandise'],
    'projects' => ['projects', 'single-project'],
    'booking' => ['booking'],
    'contact' => ['contact']
];
$defaultNavItems = [
    ['url' => 'index.php', 'label' => 'Home', 'page' => 'index'],
    ['url' => 'about.php', 'label' => 'About', 'page' => 'about'],
    ['url' => 'blog.php', 'label' => 'Blog', 'page' => 'blog'],
    ['url' => 'services.php', 'label' => 'Services', 'page' => 'services'],
    ['url' => 'contact.php', 'label' => 'Contact', 'page' => 'contact'],
    ['url' => 'https://academy.worldison.org', 'label' => 'Our Academy', 'page' => null, 'external' => true],
];
$navItems = isset($navItems) && is_array($navItems) ? $navItems : $defaultNavItems;
function getNavActive($item, $currentPage, $navGroups) {
    if (!empty($item['page']) && isset($navGroups[$item['page']]) && in_array($currentPage, $navGroups[$item['page']], true)) {
        return 'active';
    }

    if (!empty($item['page']) && $currentPage === $item['page']) {
        return 'active';
    }

    return '';
}
$navActive = function($item) use ($currentPage, $navGroups) {
    return getNavActive($item, $currentPage, $navGroups);
};
?>
<?php if ($navbarType === 'modern'): ?>
  <header class="header navbar-fixed-top">
    <nav class="navbar" role="navigation">
      <div class="container">
        <div class="menu-container js_nav-item">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="toggle-icon"></span>
          </button>
          <div class="logo">
            <a class="logo-wrap" href="index.php">
              <img class="logo-img logo-img-main" src="img/logo.png" alt="Worldison International Logo">
              <img class="logo-img logo-img-active" src="img/logo-dark.png" alt="Worldison International Dark Logo">
            </a>
          </div>
        </div>
        <div class="collapse navbar-collapse nav-collapse">
          <div class="menu-container">
            <ul class="nav navbar-nav navbar-nav-right">
              <li class="js_nav-item nav-item <?php echo $navActive('index'); ?>">
                <a class="nav-item-child nav-item-hover" href="index.php">Home</a>
              </li>
              <li class="js_nav-item nav-item <?php echo $navActive('about'); ?>">
                <a class="nav-item-child nav-item-hover" href="about.php">About</a>
              </li>
              <li class="js_nav-item nav-item <?php echo $navActive('blog'); ?>">
                <a class="nav-item-child nav-item-hover" href="blog.php">Blog</a>
              </li>
              <li class="js_nav-item nav-item <?php echo $navActive('services'); ?>">
                <a class="nav-item-child nav-item-hover" href="services.php">Services</a>
              </li>
              <li class="js_nav-item nav-item <?php echo $navActive('contact'); ?>">
                <a class="nav-item-child nav-item-hover" href="contact.php">Contact</a>
              </li>
              <li class="js_nav-item nav-item">
                <a class="nav-item-child nav-item-hover" href="https://academy.worldison.org">Our Academy</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </nav>
  </header>
<?php else: ?>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm main-nav">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="img/logo-dark.png" alt="Worldison International Logo" class="logo-img" style="max-height:50px;">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav"
        aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ml-auto align-items-lg-center">
          <li class="nav-item <?php echo $navActive('index'); ?>">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item <?php echo $navActive('blog'); ?>">
            <a class="nav-link" href="blog.php">Blog</a>
          </li>
          <li class="nav-item <?php echo $navActive('about'); ?>">
            <a class="nav-link" href="about.php">About Us</a>
          </li>
          <li class="nav-item <?php echo $navActive('services'); ?>">
            <a class="nav-link" href="services.php">Services</a>
          </li>
          <li class="nav-item <?php echo $navActive('contact'); ?>">
            <a class="nav-link" href="contact.php">Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://academy.worldison.org">Our Academy</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<?php endif; ?>
