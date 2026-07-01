<?php
// Serve the root dashboard via public webroot while preserving root-relative includes.
chdir(__DIR__ . '/..');
require_once 'dashboard.php';
