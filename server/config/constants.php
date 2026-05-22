<?php
/**
 * Application Constants
 * Alumni Management System
 */

// Application Info
define('APP_NAME', 'Alumni Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');

// API Settings
define('API_VERSION', 'v1');
define('API_PREFIX', '/api/' . API_VERSION);

// File Upload Settings
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('PROFILE_UPLOAD_SUBDIR', 'profiles');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Rate Limiting
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 60); // seconds

// Verification Code Settings
define('VERIFICATION_CODE_LENGTH', 6);
define('VERIFICATION_CODE_EXPIRY', 10 * 60); // 10 minutes

// Login Security
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 15 * 60); // 15 minutes

// Points Configuration (default values, can be overridden in database)
define('POINTS_PROFILE_COMPLETION', 50);
define('POINTS_FIRST_LOGIN', 10);
define('POINTS_REFERRAL', 25);
define('POINTS_PROFILE_UPDATE', 5);

// Badge Thresholds (default values)
define('BADGE_BRONZE_MIN', 0);
define('BADGE_SILVER_MIN', 100);
define('BADGE_GOLD_MIN', 500);
define('BADGE_PLATINUM_MIN', 1000);
define('BADGE_DIAMOND_MIN', 5000);

// Google OAuth
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID') ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');

// Firebase (for Google Auth verification)
define('FIREBASE_PROJECT_ID', getenv('FIREBASE_PROJECT_ID') ?: '');

// CORS Settings
$corsAllowedOrigins = getenv('CORS_ALLOWED_ORIGINS');
if ($corsAllowedOrigins === false || $corsAllowedOrigins === '') {
    $corsAllowedOrigins = getenv('CORS_ORIGINS');
}

define('CORS_ALLOWED_ORIGINS', $corsAllowedOrigins ?: '*');
define('CORS_ALLOWED_METHODS', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
define('CORS_ALLOWED_HEADERS', 'Content-Type, Authorization, X-Requested-With, X-CSRF-Token');

// Pagination
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Session
define('SESSION_LIFETIME', 86400 * 30); // 30 days

// WebSocket
define('WS_HOST', getenv('WS_HOST') ?: '0.0.0.0');
define('WS_PORT', getenv('WS_PORT') ?: 8080);

// Alumni ID Format
define('ALUMNI_ID_PREFIX', 'ALM');
define('ALUMNI_ID_YEAR_FORMAT', 'Y');
define('ALUMNI_ID_SEQUENCE_LENGTH', 5);

// Status Constants
class Status {
    // User Status
    const USER_ACTIVE = 'active';
    const USER_INACTIVE = 'inactive';
    const USER_BLOCKED = 'blocked';
    
    // Event Status
    const EVENT_DRAFT = 'draft';
    const EVENT_UPCOMING = 'upcoming';
    const EVENT_ONGOING = 'ongoing';
    const EVENT_COMPLETED = 'completed';
    const EVENT_CANCELLED = 'cancelled';
    
    // RSVP Status
    const RSVP_GOING = 'going';
    const RSVP_MAYBE = 'maybe';
    const RSVP_NOT_GOING = 'not_going';
    
    // Announcement Status
    const ANNOUNCEMENT_DRAFT = 'draft';
    const ANNOUNCEMENT_PUBLISHED = 'published';
    const ANNOUNCEMENT_ARCHIVED = 'archived';
    
    // Redemption Status
    const REDEMPTION_PENDING = 'pending';
    const REDEMPTION_APPROVED = 'approved';
    const REDEMPTION_CLAIMED = 'claimed';
    const REDEMPTION_REJECTED = 'rejected';
    const REDEMPTION_EXPIRED = 'expired';
}

// Role Constants
class Role {
    const ALUMNI = 'alumni';
    const ADMIN = 'admin';
    const SYSTEM_ADMIN = 'system_admin';
    
    public static function isAdmin(string $role): bool {
        return in_array($role, [self::ADMIN, self::SYSTEM_ADMIN]);
    }
    
    public static function isSystemAdmin(string $role): bool {
        return $role === self::SYSTEM_ADMIN;
    }
}

// Badge Level Constants
class BadgeLevel {
    const BRONZE = 'bronze';
    const SILVER = 'silver';
    const GOLD = 'gold';
    const PLATINUM = 'platinum';
    const DIAMOND = 'diamond';
    
    public static function getForPoints(int $points): string {
        if ($points >= BADGE_DIAMOND_MIN) return self::DIAMOND;
        if ($points >= BADGE_PLATINUM_MIN) return self::PLATINUM;
        if ($points >= BADGE_GOLD_MIN) return self::GOLD;
        if ($points >= BADGE_SILVER_MIN) return self::SILVER;
        return self::BRONZE;
    }
    
    public static function getNextLevel(string $current): ?string {
        $levels = [self::BRONZE, self::SILVER, self::GOLD, self::PLATINUM, self::DIAMOND];
        $index = array_search($current, $levels);
        return $index !== false && $index < count($levels) - 1 ? $levels[$index + 1] : null;
    }
    
    public static function getPointsRequired(string $level): int {
        switch ($level) {
            case self::SILVER: return BADGE_SILVER_MIN;
            case self::GOLD: return BADGE_GOLD_MIN;
            case self::PLATINUM: return BADGE_PLATINUM_MIN;
            case self::DIAMOND: return BADGE_DIAMOND_MIN;
            default: return BADGE_BRONZE_MIN;
        }
    }
}
