<?php
/**
 * Hostinger-specific Configuration
 * This file contains optimizations and settings for Hostinger hosting
 */

// Hostinger-specific PHP settings
if (function_exists('ini_set')) {
    // Memory and execution limits
    @ini_set('memory_limit', '256M');
    @ini_set('max_execution_time', '300');
    @ini_set('max_input_time', '300');
    
    // Upload settings
    @ini_set('upload_max_filesize', '10M');
    @ini_set('post_max_size', '10M');
    
    // Error handling for production
    if (getenv('APP_DEBUG') !== 'true') {
        @ini_set('display_errors', '0');
        @ini_set('display_startup_errors', '0');
        @ini_set('log_errors', '1');
        @ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
    }
    
    // Session settings
    @ini_set('session.gc_maxlifetime', '7200'); // 2 hours
    @ini_set('session.cookie_lifetime', '7200');
    @ini_set('session.cookie_httponly', '1');
    @ini_set('session.cookie_secure', '1'); // HTTPS only
    @ini_set('session.use_strict_mode', '1');
    
    // Output buffering
    @ini_set('output_buffering', '4096');
    
    // Timezone
    date_default_timezone_set('Asia/Manila');
}

// Create logs directory if it doesn't exist
$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
    @mkdir($logsDir, 0755, true);
}

// Hostinger database connection optimization
function getHostingerDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');
        $port = getenv('DB_PORT') ?: '3306';
        
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => true, // Connection pooling
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            PDO::MYSQL_ATTR_COMPRESS => true, // Compress data transfer
            PDO::ATTR_TIMEOUT => 10, // Connection timeout
        ];
        
        $pdo = new PDO($dsn, $username, $password, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Hostinger DB Connection Error: " . $e->getMessage());
        throw new Exception("Database connection failed. Please check your configuration.");
    }
}

// Hostinger-optimized file upload handler
function handleHostingerUpload($file, $uploadDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
    // Validate file
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new Exception('Invalid file upload');
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new Exception('File size exceeds limit (10MB)');
        case UPLOAD_ERR_NO_FILE:
            throw new Exception('No file uploaded');
        default:
            throw new Exception('Upload error occurred');
    }
    
    // Validate file size (10MB max for Hostinger)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds 10MB limit');
    }
    
    // Validate file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];
    
    if (!array_key_exists($mimeType, $allowedMimes)) {
        throw new Exception('Invalid file type. Allowed: ' . implode(', ', $allowedTypes));
    }
    
    // Generate unique filename
    $extension = $allowedMimes[$mimeType];
    $filename = sprintf(
        '%s_%s.%s',
        uniqid('upload', true),
        bin2hex(random_bytes(8)),
        $extension
    );
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Move uploaded file
    $destination = $uploadDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Set proper permissions
    chmod($destination, 0644);
    
    return $filename;
}

// Hostinger cache helper
function hostingerCache($key, $callback, $ttl = 3600) {
    $cacheDir = __DIR__ . '/../cache';
    
    // Create cache directory if it doesn't exist
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . '/' . md5($key) . '.cache';
    
    // Check if cache exists and is valid
    if (file_exists($cacheFile)) {
        $cacheData = unserialize(file_get_contents($cacheFile));
        if ($cacheData['expires'] > time()) {
            return $cacheData['data'];
        }
    }
    
    // Generate new data
    $data = $callback();
    
    // Save to cache
    $cacheData = [
        'data' => $data,
        'expires' => time() + $ttl,
    ];
    
    file_put_contents($cacheFile, serialize($cacheData));
    
    return $data;
}

// Clear cache helper
function clearHostingerCache($pattern = '*') {
    $cacheDir = __DIR__ . '/../cache';
    
    if (!is_dir($cacheDir)) {
        return;
    }
    
    $files = glob($cacheDir . '/' . $pattern . '.cache');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

// Hostinger-optimized image resize
function hostingerResizeImage($sourcePath, $destPath, $maxWidth, $maxHeight, $quality = 85) {
    // Get image info
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        throw new Exception('Invalid image file');
    }
    
    list($origWidth, $origHeight, $imageType) = $imageInfo;
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);
    
    // Create source image
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            throw new Exception('Unsupported image type');
    }
    
    // Create destination image
    $dest = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize
    imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
    
    // Save
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($dest, $destPath, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($dest, $destPath, (int)(9 - ($quality / 10)));
            break;
        case IMAGETYPE_GIF:
            imagegif($dest, $destPath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($dest, $destPath, $quality);
            break;
    }
    
    // Clean up
    imagedestroy($source);
    imagedestroy($dest);
    
    return true;
}

// Log helper for Hostinger
function hostingerLog($message, $level = 'INFO') {
    $logFile = __DIR__ . '/../logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Check if running on Hostinger
function isHostinger() {
    return (
        isset($_SERVER['SERVER_SOFTWARE']) &&
        (
            stripos($_SERVER['SERVER_SOFTWARE'], 'hostinger') !== false ||
            stripos($_SERVER['HTTP_HOST'], 'hostinger') !== false
        )
    );
}

// Hostinger-specific optimizations
if (isHostinger()) {
    // Enable output compression if available
    if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
        @ini_set('zlib.output_compression', '1');
        @ini_set('zlib.output_compression_level', '6');
    }
    
    // Optimize session handling
    if (session_status() === PHP_SESSION_NONE) {
        session_cache_limiter('nocache');
        session_cache_expire(120);
    }
}
