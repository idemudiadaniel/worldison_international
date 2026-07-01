<?php
/**
 * Shared HTML <head> section for all public pages.
 */
$pageTitle = $pageTitle ?? 'Worldison International';
$pageDescription = $pageDescription ?? 'Worldison International Ltd — your trusted partner in cleaning, fumigation, pest control, fire safety, and general contracting services.';
$pageKeywords = $pageKeywords ?? 'cleaning, fumigation, pest control, fire safety, disinfection';
$pageCanonical = $pageCanonical ?? null;
$pageStyles = $pageStyles ?? [];
$pageLanguage = $pageLanguage ?? 'en';
$htmlClass = $htmlClass ?? 'no-js';
if (is_string($pageStyles)) {
    $pageStyles = [$pageStyles];
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($pageLanguage, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($htmlClass, ENT_QUOTES, 'UTF-8'); ?>">
<head>
  <meta charset="utf-8"/>
  <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="author" content="Worldison International">
  <meta name="robots" content="index, follow">
  <?php if (!empty($pageCanonical)): ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8'); ?>">
  <?php endif; ?>

  <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>" />
  <meta property="og:image" content="https://worldison.org/img/logo.png" />
  <meta property="og:url" content="https://worldison.org/" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Worldison International" />

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image" content="https://worldison.org/img/logo.png">

  <link href="https://fonts.googleapis.com/css?family=Hind:300,400,500,600,700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="vendor/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet" type="text/css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css" rel="stylesheet">
  <link href="css/animate.css" rel="stylesheet" type="text/css"/>
  <link href="css/layout.min.css" rel="stylesheet" type="text/css"/>
  <link href="css/style.css" rel="stylesheet" type="text/css"/>
  <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon">
  <?php foreach ($pageStyles as $style): ?>
    <link href="<?php echo htmlspecialchars($style, ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet" type="text/css"/>
  <?php endforeach; ?>
</head>
