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
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return null;
        }
        
        // Validate file type
        $mimeType = mime_content_type($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes)) {
            $this->errors[] = 'File type not allowed';
            return null;
        }
        
        // Validate file size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File size exceeds limit of ' . ($this->maxSize / 1024 / 1024) . 'MB';
            return null;
        }
        
        // Generate filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $customName ? $customName . '.' . $extension : $this->generateFilename($extension);
        $filepath = $this->uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
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
 * Image processor
 */
class ImageProcessor {
    
    /**
     * Resize image
     */
    public static function resize(string $sourcePath, string $destPath, int $maxWidth, int $maxHeight): bool {
        $info = getimagesize($sourcePath);
        if (!$info) return false;
        
        list($width, $height) = $info;
        $mime = $info['mime'];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int) ($width * $ratio);
        $newHeight = (int) ($height * $ratio);
        
        // Create image resource
        switch ($mime) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
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
            imagealphablending($dest, false);
            imagesavealpha($dest, true);
            $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
            imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save image
        $result = false;
        switch ($mime) {
            case 'image/jpeg':
                $result = imagejpeg($dest, $destPath, 85);
                break;
            case 'image/png':
                $result = imagepng($dest, $destPath, 8);
                break;
            case 'image/gif':
                $result = imagegif($dest, $destPath);
                break;
            case 'image/webp':
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
