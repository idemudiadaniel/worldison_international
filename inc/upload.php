<?php
// Secure upload helper for file upload validation and logging.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const UPLOAD_ALLOWED_EXTENSIONS = [
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip'
];
const UPLOAD_MAX_IMAGE_BYTES = 5 * 1024 * 1024;
const UPLOAD_MAX_DOCUMENT_BYTES = 20 * 1024 * 1024;
const UPLOAD_LOG_FILE = __DIR__ . '/upload.log';

function getClientIp(): string {
    $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            return $ip;
        }
    }
    return 'unknown';
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireCsrf(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

function isAllowedUploadExtension(string $ext): bool {
    return in_array(strtolower($ext), UPLOAD_ALLOWED_EXTENSIONS, true);
}

function getUploadedFileMimeType(string $tmpFile): string {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpFile);
    finfo_close($finfo);
    return $mime ?: 'application/octet-stream';
}

function hasValidImageSignature(string $tmpFile): bool {
    $imageInfo = @getimagesize($tmpFile);
    return $imageInfo !== false;
}

function generateUploadFilename(string $extension): string {
    return bin2hex(random_bytes(16)) . '.' . strtolower($extension);
}

function validateFilenameSafety(string $filename): void {
    if (preg_match('/(\.\.\/|\\\\|\x00)/', $filename)) {
        throw new RuntimeException('Invalid file name.');
    }
}

function logUpload(string $userIdentifier, string $originalFilename, string $newFilename): void {
    $time = date('c');
    $ip = getClientIp();
    $logLine = sprintf("[%s] user=%s ip=%s original=%s new=%s\n", $time, $userIdentifier, $ip, $originalFilename, $newFilename);
    file_put_contents(UPLOAD_LOG_FILE, $logLine, FILE_APPEND | LOCK_EX);
}

function validateUploadFile(string $fileKey, string $destinationDir, array $allowedExtensions, int $maxBytes): string {
    if (empty($_FILES[$fileKey]['name'])) {
        throw new RuntimeException('No file uploaded.');
    }

    $file = $_FILES[$fileKey];
    if (!empty($file['error'])) {
        throw new RuntimeException('Upload error code: ' . $file['error']);
    }

    $originalName = basename($file['name']);
    validateFilenameSafety($originalName);

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions, true)) {
        throw new RuntimeException('Invalid file extension.');
    }
    if (!isAllowedUploadExtension($ext)) {
        throw new RuntimeException('File extension not allowed.');
    }

    $fileSize = (int)$file['size'];
    if ($fileSize > $maxBytes) {
        throw new RuntimeException('Uploaded file exceeds allowed size.');
    }

    $mime = getUploadedFileMimeType($file['tmp_name']);
    $allowedMimes = getAllowedMimesForExtension($ext);
    if (!in_array($mime, $allowedMimes, true)) {
        throw new RuntimeException('Uploaded file MIME type not allowed.');
    }

    if (in_array($ext, ['jpg','jpeg','png','gif','webp'], true) && !hasValidImageSignature($file['tmp_name'])) {
        throw new RuntimeException('Uploaded image failed content validation.');
    }

    if (!is_dir($destinationDir) && !mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
        throw new RuntimeException('Failed to create upload directory.');
    }

    $newName = generateUploadFilename($ext);
    $target = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $newName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Unable to move uploaded file.');
    }

    $userId = $_SESSION['user_id'] ?? 'guest';
    logUpload((string)$userId, $originalName, $newName);

    return $newName;
}

function getAllowedMimesForExtension(string $ext): array {
    static $mimeMap = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'gif'  => ['image/gif'],
        'webp' => ['image/webp'],
        'pdf'  => ['application/pdf'],
        'doc'  => ['application/msword', 'application/octet-stream'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/octet-stream'],
        'xls'  => ['application/vnd.ms-excel', 'application/octet-stream'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream'],
        'ppt'  => ['application/vnd.ms-powerpoint', 'application/octet-stream'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/octet-stream'],
        'zip'  => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
    ];
    return $mimeMap[strtolower($ext)] ?? ['application/octet-stream'];
}
