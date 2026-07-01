<?php
/**
 * Shared page header wrapper for public pages.
 * Opens the <body> tag and includes navigation.
 *
 * Supported variables (optional):
 * - $navbarType: 'modern' or 'simple'
 * - $currentPage: Current script name without extension
 * - $bodyAttributes: Additional body tag attributes
 */
$navbarType = $navbarType ?? 'simple';
$currentPage = $currentPage ?? basename($_SERVER['SCRIPT_NAME'], '.php');
$bodyAttributes = $bodyAttributes ?? 'id="body" data-spy="scroll" data-target=".header"';
?>
<body <?php echo $bodyAttributes; ?>>
<?php require_once __DIR__ . '/navbar.php'; ?>
