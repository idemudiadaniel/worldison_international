<?php
// Database connection settings.
// Adjust values or set environment variables before running the application.
$DB_HOST = getenv('DB_HOST');
$DB_USER = getenv('DB_USER');
$DB_PASS = getenv('DB_PASS');
$DB_NAME = getenv('DB_NAME');
$DB_PORT = getenv('DB_PORT');

$DB_HOST = ($DB_HOST !== false && $DB_HOST !== '') ? $DB_HOST : '127.0.0.1';
$DB_USER = ($DB_USER !== false && $DB_USER !== '') ? $DB_USER : 'worldison_user';
$DB_PASS = ($DB_PASS !== false && $DB_PASS !== '') ? $DB_PASS : 'worldison_pass';
$DB_NAME = ($DB_NAME !== false && $DB_NAME !== '') ? $DB_NAME : 'worldison';
$DB_PORT = ($DB_PORT !== false && $DB_PORT !== '') ? (int) $DB_PORT : 3306;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = mysqli_init();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $conn->real_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    if (!headers_sent()) {
        http_response_code(503);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Service temporarily unavailable. Please try again later.';
    }

    exit;
}
