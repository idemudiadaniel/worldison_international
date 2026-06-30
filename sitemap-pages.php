<?php
header("Content-Type: application/xml; charset=UTF-8");

$urls = [
  "https://worldison.org/",
  "https://worldison.org/blog",
  "https://worldison.org/about-us",
  "https://worldison.org/services",
  "https://worldison.org/contact",
];

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

foreach ($urls as $url) {
  echo "  <url>\n";
  echo "    <loc>" . htmlspecialchars($url, ENT_XML1, 'UTF-8') . "</loc>\n";
  echo "    <lastmod>" . date("Y-m-d") . "</lastmod>\n";
  echo "  </url>\n";
}

echo "</urlset>";
