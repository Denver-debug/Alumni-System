<?php
/**
 * File Upload Utilities
 * Alumni Management System
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * File Uploader Class
 */
class FileUploader {
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];
    
    public function __construct(string $subDir = '', array $allowedTypes = null, int $maxSize = null) {
        $this->uploadDir = UPLOAD_DIR . ($subDir ? '/' . trim($subDir, '/') : '');
        $this->allowedTypes = $allowedTypes ?? array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        $this->maxSize = $maxSize ?? UPLOAD_MAX_SIZE;
        
        // Create directory if not exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Upload single file
     */
    public function upload(array $file, string $customName = null): ?string {
        $this->errors = [];

        // Check for upload errors
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE));
            return null;
        }

        if (empty($file['tmp_name']) || !is_file($file['tmp_name'])) {
            $this->errors[] = 'Uploaded file is missing';
            return null;
        }

        // Validate file type
        $mimeType = $this->detectMimeType($file['tmp_name']);
        if ($mimeType === null || $mimeType === 'application/octet-stream') {
            $mimeType = $this->normalizeClientMimeType((string) ($file['type'] ?? ''));
        }
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));

        if ($mimeType === null || !in_array($mimeType, $this->allowedTypes, true)) {
            $this->errors[] = 'File type not allowed. Allowed image types are JPEG, PNG, GIF, and WebP.';
            return null;
        }

        if (!$this->extensionMatchesMimeType($extension, $mimeType)) {
            $this->errors[] = 'File MIME type does not match extension';
            return null;
        }

        // Validate file size
        if ((int) ($file['size'] ?? 0) > $this->maxSize) {
            $this->errors[] = 'File size exceeds limit of ' . ($this->maxSize / 1024 / 1024) . 'MB';
            return null;
        }

        if (strpos($mimeType, 'image/') === 0 && !$this->hasValidImageDimensions($file['tmp_name'])) {
            $this->errors[] = 'Image has invalid dimensions';
            return null;
        }

        // Generate filename
        $filename = $customName ? $customName . '.' . $extension : $this->generateFilename($extension);
        $filepath = $this->uploadDir . '/' . $filename;

        // Move uploaded file
        $moved = move_uploaded_file($file['tmp_name'], $filepath);

        if (!$moved && PHP_SAPI === 'cli') {
            $moved = rename($file['tmp_name'], $filepath);
        }

        if (!$moved) {
            $this->errors[] = 'Failed to move uploaded file';
            return null;
        }

        return $filename;
    }
    
    /**
     * Upload multiple files
     */
    public function uploadMultiple(array $files): array {
        $uploaded = [];
        
        foreach ($files['tmp_name'] as $key => $tmpName) {
            $file = [
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $tmpName,
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            ];
            
            $result = $this->upload($file);
            if ($result) {
                $uploaded[] = $result;
            }
        }
        
        return $uploaded;
    }
    
    /**
     * Delete file
     */
    public function delete(string $filename): bool {
        $filepath = $this->uploadDir . '/' . $filename;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
    
    /**
     * Get file URL
     */
    public function getUrl(string $filename): string {
        $relativePath = str_replace(UPLOAD_DIR, '', $this->uploadDir);
        return '/uploads' . $relativePath . '/' . $filename;
    }
    
    /**
     * Generate unique filename
     */
    private function generateFilename(string $extension): string {
        return uniqid() . '_' . time() . '.' . strtolower($extension);
    }

    /**
     * Detect MIME type from file contents.
     */
    private function detectMimeType(string $path): ?string {
        if (function_exists('mime_content_type')) {
            $mimeType = mime_content_type($path);
            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }

        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($path);
            if (is_string($mimeType) && $mimeType !== '') {
                return $mimeType;
            }
        }

        return null;
    }

    /**
     * Normalize client-provided MIME types into canonical values.
     */
    private function normalizeClientMimeType(string $mimeType): ?string {
        $normalized = strtolower(trim($mimeType));
        if ($normalized === '') {
            return null;
        }

        $map = [
            'image/jpg' => 'image/jpeg',
            'image/pjpeg' => 'image/jpeg',
            'image/x-png' => 'image/png',
        ];

        return $map[$normalized] ?? $normalized;
    }

    /**
     * Ensure file extensions line up with detected MIME type.
     */
    private function extensionMatchesMimeType(string $extension, string $mimeType): bool {
        $extensionMap = [
            'image/jpeg' => ['jpg', 'jpeg', 'jpe'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
        ];

        if (!isset($extensionMap[$mimeType])) {
            return true;
        }

        return in_array(strtolower($extension), $extensionMap[$mimeType], true);
    }

    /**
     * Verify uploaded images have readable, positive dimensions.
     */
    private function hasValidImageDimensions(string $path): bool {
        if (!function_exists('getimagesize')) {
            return true;
        }

        $dimensions = getimagesize($path);
        return is_array($dimensions) && ($dimensions[0] ?? 0) > 0 && ($dimensions[1] ?? 0) > 0;
    }
    
    /**
     * Get upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File is too large';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Check if has errors
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
}

/**
 * Delete an existing profile upload and its thumbnail if it lives in the local uploads tree.
 */
function deleteProfileUploadFile(?string $imageUrl): void {
    $rawPath = trim((string) $imageUrl);
    if ($rawPath === '') {
        return;
    }

    $path = parse_url($rawPath, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        $path = $rawPath;
    }

    $prefix = '/uploads/' . PROFILE_UPLOAD_SUBDIR . '/';
    if (strpos($path, $prefix) === false) {
        return;
    }

    $filename = basename($path);
    if ($filename === '' || $filename === '.' || $filename === '..') {
        return;
    }

    $pathsToDelete = [
        UPLOAD_DIR . '/' . PROFILE_UPLOAD_SUBDIR . '/' . $filename,
        UPLOAD_DIR . '/' . PROFILE_UPLOAD_SUBDIR . '/thumb_' . $filename,
    ];

    foreach ($pathsToDelete as $filePath) {
        if (is_file($filePath)) {
            @unlink($filePath);
        }
    }
}

/**
 * Image processor
 */
class ImageProcessor {
    
    /**
     * Resize image
     */
    public static function resize(string $sourcePath, string $destPath, int $maxWidth, int $maxHeight): bool {
        if (!function_exists('getimagesize')) {
            return false;
        }

        $info = getimagesize($sourcePath);
        if (!$info) return false;
        
        list($width, $height) = $info;
        $mime = $info['mime'];
        
        if ($width <= 0 || $height <= 0) {
            return false;
        }

        if ($width <= $maxWidth && $height <= $maxHeight) {
            return true;
        }

        // Gracefully skip processing when GD extension is not available.
        if (
            !function_exists('imagecreatetruecolor') ||
            !function_exists('imagecopyresampled') ||
            !function_exists('imagedestroy')
        ) {
            return false;
        }

        // Calculate new dimensions without upscaling images that are already within limits.
        $ratio = min(1, $maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);
        
        // Create image resource
        switch ($mime) {
            case 'image/jpeg':
                if (!function_exists('imagecreatefromjpeg')) return false;
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                if (!function_exists('imagecreatefrompng')) return false;
                $source = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                if (!function_exists('imagecreatefromgif')) return false;
                $source = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) return false;
                $source = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$source) return false;
        
        // Create resized image
        $dest = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
            if (function_exists('imagealphablending')) {
                imagealphablending($dest, false);
            }
            if (function_exists('imagesavealpha')) {
                imagesavealpha($dest, true);
            }
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save image
        $result = false;
        switch ($mime) {
            case 'image/jpeg':
                if (!function_exists('imagejpeg')) return false;
                $result = imagejpeg($dest, $destPath, 85);
                break;
            case 'image/png':
                if (!function_exists('imagepng')) return false;
                $result = imagepng($dest, $destPath, 8);
                break;
            case 'image/gif':
                if (!function_exists('imagegif')) return false;
                $result = imagegif($dest, $destPath);
                break;
            case 'image/webp':
                if (!function_exists('imagewebp')) return false;
                $result = imagewebp($dest, $destPath, 85);
                break;
        }
        
        imagedestroy($source);
        imagedestroy($dest);
        
        return $result;
    }
    
    /**
     * Create thumbnail
     */
    public static function thumbnail(string $sourcePath, string $destPath, int $size = 150): bool {
        return self::resize($sourcePath, $destPath, $size, $size);
    }
    
    /**
     * Get image dimensions
     */
    public static function getDimensions(string $path): ?array {
        $info = getimagesize($path);
        if (!$info) return null;
        
        return [
            'width' => $info[0],
            'height' => $info[1],
            'mime' => $info['mime']
        ];
    }
}

/**
 * QR Code Generator (using endroid/qr-code or simple API)
 */
class QRGenerator {
    
    /**
     * Generate QR code using Google Charts API
     */
    public static function generate(string $data, int $size = 200): string {
        // Using Google Charts API as fallback (simple, no dependencies)
        return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($data);
    }
    
    /**
     * Generate and save QR code
     */
    public static function save(string $data, string $filepath, int $size = 200): bool {
        $url = self::generate($data, $size);
        $content = file_get_contents($url);
        
        if ($content === false) {
            return false;
        }
        
        return file_put_contents($filepath, $content) !== false;
    }
}
