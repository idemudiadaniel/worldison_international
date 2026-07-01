<?php
/**
 * Shared helper functions for public pages
 */

/**
 * Safely escape HTML output
 * 
 * @param string $str Input string
 * @return string Escaped string
 */
function escapeHtml($str = "") {
  return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * Get safe image path with validation
 * Falls back to default image if file doesn't exist
 * 
 * @param string $filename Image filename
 * @param string $uploadPath Path to uploads folder (relative to public/)
 * @param string $defaultImage Default fallback image
 * @return string Safe image path
 */
function getImagePath($filename, $uploadPath = "uploads/projects/", $defaultImage = "img/970x647/01.jpg") {
  $filename = trim((string)$filename);
  
  // Prevent directory traversal
  $filename = str_replace(['../', '..\\'], '', $filename);
  $filename = basename($filename);

  if ($filename === '') {
      return $defaultImage;
  }

  // Validate file extension
  if (!preg_match('/^[a-zA-Z0-9_\-]+\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
      return $defaultImage;
  }

  // Check if file exists
  $file = __DIR__ . "/../" . $uploadPath . $filename;
  if (file_exists($file)) {
      return $uploadPath . rawurlencode($filename);
  }

  return $defaultImage;
}

/**
 * Truncate text and add ellipsis
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to add (default: "…")
 * @return string Truncated text
 */
function truncateText($text, $length = 120, $suffix = "…") {
  $text = strip_tags((string)$text);
  
  if (mb_strlen($text) > $length) {
      return mb_substr($text, 0, $length) . $suffix;
  }
  
  return $text;
}

/**
 * Get visitor IP address (handles proxies)
 * 
 * @return string IP address
 */
function getVisitorIP() {
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      return $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
  } else {
      return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  }
}

/**
 * Format phone number for display
 * 
 * @param string $phone Phone number
 * @return string Formatted phone
 */
function formatPhone($phone) {
  $phone = preg_replace('/[^0-9]/', '', (string)$phone);
  
  if (strlen($phone) === 13 && substr($phone, 0, 1) === '2') {
      return '+' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 4) . ' ' . substr($phone, 10);
  }
  
  return (string)$phone;
}

/**
 * Get page canonical URL
 * 
 * @param string $path Page path (without domain)
 * @return string Full canonical URL
 */
function getCanonicalURL($path = null) {
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $domain = $_SERVER['HTTP_HOST'] ?? 'worldison.org';
  
  if ($path === null) {
      $path = $_SERVER['REQUEST_URI'] ?? '/';
  }
  
  return $protocol . '://' . $domain . '/' . ltrim((string)$path, '/');
}

/**
 * Generate breadcrumbs HTML
 * 
 * @param array $breadcrumbs Breadcrumb items (path => title)
 * @param bool $asJson Return as JSON-LD schema
 * @return string HTML or JSON breadcrumbs
 */
function generateBreadcrumbs($breadcrumbs = [], $asJson = false) {
  $home = ['/' => 'Home'];
  $breadcrumbs = array_merge($home, $breadcrumbs);
  
  if ($asJson) {
      $items = [];
      $position = 1;
      
      foreach ($breadcrumbs as $path => $title) {
          $items[] = [
              '@type' => 'ListItem',
              'position' => $position++,
              'name' => $title,
              'item' => getCanonicalURL($path)
          ];
      }
      
      return json_encode([
          '@context' => 'https://schema.org',
          '@type' => 'BreadcrumbList',
          'itemListElement' => $items
      ]);
  }
  
  $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
  $count = count($breadcrumbs);
  $index = 1;
  
  foreach ($breadcrumbs as $path => $title) {
      if ($index === $count) {
          $html .= '<li class="breadcrumb-item active" aria-current="page">' . escapeHtml($title) . '</li>';
      } else {
          $html .= '<li class="breadcrumb-item"><a href="' . escapeHtml($path) . '">' . escapeHtml($title) . '</a></li>';
      }
      $index++;
  }
  
  $html .= '</ol></nav>';
  return $html;
}

/**
 * Render meta tags for social sharing
 * 
 * @param array $meta Meta data (title, description, image, url, type)
 * @return string Meta tags HTML
 */
function renderOGTags($meta = []) {
  $meta = array_merge([
      'title' => 'Worldison International',
      'description' => 'Your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services.',
      'image' => 'https://worldison.org/img/logo.png',
      'url' => getCanonicalURL(),
      'type' => 'website'
  ], $meta);
  
  $html = '';
  $html .= '<meta property="og:title" content="' . escapeHtml($meta['title']) . '" />' . PHP_EOL;
  $html .= '<meta property="og:description" content="' . escapeHtml($meta['description']) . '" />' . PHP_EOL;
  $html .= '<meta property="og:image" content="' . escapeHtml($meta['image']) . '" />' . PHP_EOL;
  $html .= '<meta property="og:url" content="' . escapeHtml($meta['url']) . '" />' . PHP_EOL;
  $html .= '<meta property="og:type" content="' . escapeHtml($meta['type']) . '" />' . PHP_EOL;
  
  return $html;
}

/**
 * Check if a page is active (for navigation highlighting)
 * 
 * @param string $pageName Page name without .php extension
 * @param string $current Current page (defaults to basename of script)
 * @return bool True if page is active
 */
function isPageActive($pageName, $current = null) {
  if ($current === null) {
      $current = basename($_SERVER['SCRIPT_NAME'], '.php');
  }
  
  return $current === (string)$pageName;
}
