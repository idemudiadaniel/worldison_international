<?php
/**
 * SEO utilities and structured data for public pages
 */

require_once __DIR__ . '/functions.php';

/**
 * Generate JSON-LD schema.org structured data
 * 
 * @param string $type Schema type (Organization, Article, Product, etc.)
 * @param array $data Schema data
 * @return void Echoes script tag with schema
 */
function renderStructuredData($type, $data = []) {
  $baseSchema = [
      '@context' => 'https://schema.org',
      '@type' => $type,
  ];
  
  // Default Organization schema for Worldison
  if ($type === 'Organization') {
      $baseSchema = array_merge($baseSchema, [
          'name' => 'Worldison International',
          'url' => 'https://worldison.org',
          'logo' => 'https://worldison.org/img/logo.png',
          'description' => 'Worldison International Ltd — your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services.',
          'email' => 'info@worldison.org',
          'telephone' => '+234-813-082-6625',
          'address' => [
              '@type' => 'PostalAddress',
              'streetAddress' => '197, Ugbowo Opp. Union Bank',
              'addressLocality' => 'Benin City',
              'addressRegion' => 'Edo State',
              'postalCode' => '300001',
              'addressCountry' => 'NG'
          ],
          'sameAs' => [
              'https://www.facebook.com/wsfcompany',
              'https://www.twitter.com/worldison',
              'https://www.instagram.com/worldison_sfc',
              'https://www.youtube.com/@worldison'
          ]
      ]);
  }
  
  // Default Article schema
  if ($type === 'Article') {
      $baseSchema = array_merge([
          'author' => [
              '@type' => 'Organization',
              'name' => 'Worldison International'
          ],
          'publisher' => [
              '@type' => 'Organization',
              'name' => 'Worldison International',
              'logo' => [
                  '@type' => 'ImageObject',
                  'url' => 'https://worldison.org/img/logo.png'
              ]
          ]
      ], $baseSchema);
  }
  
  // Merge custom data
  $schema = array_merge($baseSchema, $data);
  
  echo '<script type="application/ld+json">' . PHP_EOL;
  echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
  echo '</script>' . PHP_EOL;
}

/**
 * Get SEO meta data for common pages
 * 
 * @param string $page Page slug
 * @return array SEO meta data
 */
function getPageSEO($page) {
  $seo = [
      'index' => [
          'title' => 'Worldison International - Cleaning, Fumigation & Fire Safety Services',
          'description' => 'Worldison International is your trusted partner for professional cleaning, fumigation, pest control, and fire safety solutions in Nigeria.',
          'keywords' => 'cleaning services, fumigation, pest control, fire safety, disinfection, Nigeria'
      ],
      'about' => [
          'title' => 'About Worldison International - 20+ Years of Excellence',
          'description' => 'Learn about Worldison International - a trusted provider of cleaning, fumigation, pest control, and fire safety services with over 20 years of proven expertise.',
          'keywords' => 'about Worldison, company history, professional services, cleaning expertise'
      ],
      'services' => [
          'title' => 'Our Services - Cleaning, Fumigation, Fire Safety & More',
          'description' => 'Explore our comprehensive range of services including cleaning, fumigation, pest control, fire safety, and disinfection solutions.',
          'keywords' => 'cleaning services, fumigation services, pest control, fire safety solutions, disinfection'
      ],
      'blog' => [
          'title' => 'Worldison Blog - Tips, News & Insights',
          'description' => 'Read our latest articles about cleaning, pest control, fire safety, and facility management best practices.',
          'keywords' => 'blog, cleaning tips, pest control advice, fire safety tips, facility management'
      ],
      'projects' => [
          'title' => 'Our Projects - Worldison International Portfolio',
          'description' => 'Explore our portfolio of successful projects in cleaning, fumigation, and facility management across Nigeria.',
          'keywords' => 'projects, portfolio, case studies, cleaning projects, fumigation projects'
      ],
      'contact' => [
          'title' => 'Contact Worldison International - Get in Touch',
          'description' => 'Contact us for cleaning, fumigation, pest control, and fire safety services. Visit our offices in Benin, Lagos, and Abuja.',
          'keywords' => 'contact us, locations, phone number, email, Benin office, Lagos office, Abuja office'
      ],
      'booking' => [
          'title' => 'Book Our Services - Worldison International',
          'description' => 'Book our professional cleaning, fumigation, and pest control services online. Fast, reliable, and professional service guaranteed.',
          'keywords' => 'book services, cleaning booking, fumigation booking, pest control booking'
      ]
  ];
  
  $page = strtolower($page);
  
  if (isset($seo[$page])) {
      return $seo[$page];
  }
  
  return [
      'title' => 'Worldison International',
      'description' => 'Professional cleaning, fumigation, pest control, and fire safety services.',
      'keywords' => 'cleaning, fumigation, pest control, fire safety, services'
  ];
}

/**
 * Generate sitemap XML for a list of pages
 * 
 * @param array $pages Array of page data (url, lastmod, priority)
 * @return void Echoes XML
 */
function generateSitemap($pages = []) {
  $default_pages = [
      ['url' => 'index.php', 'priority' => '1.0'],
      ['url' => 'about.php', 'priority' => '0.8'],
      ['url' => 'services.php', 'priority' => '0.8'],
      ['url' => 'projects.php', 'priority' => '0.8'],
      ['url' => 'blog.php', 'priority' => '0.7'],
      ['url' => 'contact.php', 'priority' => '0.7'],
      ['url' => 'booking.php', 'priority' => '0.8'],
      ['url' => 'privacy.php', 'priority' => '0.5'],
      ['url' => 'terms.php', 'priority' => '0.5'],
      ['url' => 'refund-policy.php', 'priority' => '0.5'],
  ];
  
  $pages = array_merge($default_pages, $pages);
  
  header('Content-Type: application/xml; charset=utf-8');
  echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
  echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
  
  foreach ($pages as $page) {
      $url = isset($page['url']) ? rtrim((string)$page['url'], '/') : '';
      $priority = isset($page['priority']) ? (float)$page['priority'] : '0.5';
      
      if (!empty($url)) {
          echo '  <url>' . PHP_EOL;
          echo '    <loc>https://worldison.org/' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</loc>' . PHP_EOL;
          
          if (isset($page['lastmod'])) {
              echo '    <lastmod>' . htmlspecialchars((string)$page['lastmod'], ENT_QUOTES, 'UTF-8') . '</lastmod>' . PHP_EOL;
          }
          
          echo '    <priority>' . number_format($priority, 1) . '</priority>' . PHP_EOL;
          echo '  </url>' . PHP_EOL;
      }
  }
  
  echo '</urlset>' . PHP_EOL;
}

/**
 * Add rel=canonical tag to prevent duplicate content
 * 
 * @param string $url Canonical URL
 * @return string HTML link tag
 */
function getCanonicalTag($url = null) {
  if ($url === null) {
      $url = getCanonicalURL();
  }
  
  return '<link rel="canonical" href="' . escapeHtml($url) . '">' . PHP_EOL;
}

/**
 * Generate robots.txt meta tag
 * 
 * @param array $rules Robots rules (index, follow, googlebot, etc.)
 * @return string Meta tag HTML
 */
function getRobotsTag($rules = []) {
  $default_rules = [
      'index' => true,
      'follow' => true,
      'archive' => true
  ];
  
  $rules = array_merge($default_rules, $rules);
  
  $content = [];
  
  if ($rules['index']) {
      $content[] = 'index';
  } else {
      $content[] = 'noindex';
  }
  
  if ($rules['follow']) {
      $content[] = 'follow';
  } else {
      $content[] = 'nofollow';
  }
  
  if (isset($rules['archive']) && !$rules['archive']) {
      $content[] = 'noarchive';
  }
  
  return '<meta name="robots" content="' . implode(', ', $content) . '">' . PHP_EOL;
}
