<?php
/**
 * Email Configuration
 * Alumni Management System
 * Uses PHPMailer for sending emails
 */

require_once __DIR__ . '/database.php';

// Check if PHPMailer is available via Composer
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        break;
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service Class
 */
class EmailService {
    private $mailer;
    private $db;
    private $settings = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadSettings();
        $this->initMailer();
    }
    
    /**
     * Load email settings from database
     */
    private function loadSettings(): void {
        $results = $this->db->fetchAll("SELECT setting_key, setting_value FROM email_settings");
        foreach ($results as $row) {
            $this->settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    /**
     * Initialize PHPMailer
     */
    private function initMailer(): void {
        $this->mailer = new PHPMailer(true);
        
        // SMTP configuration
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->settings['smtp_host'] ?? (getenv('MAIL_HOST') ?: (getenv('SMTP_HOST') ?: 'smtp.gmail.com'));
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->settings['smtp_username'] ?? (getenv('MAIL_USERNAME') ?: (getenv('SMTP_USERNAME') ?: ''));
        $this->mailer->Password = $this->settings['smtp_password'] ?? (getenv('MAIL_PASSWORD') ?: (getenv('SMTP_PASSWORD') ?: ''));
        $this->mailer->SMTPSecure = ($this->settings['smtp_encryption'] ?? 'tls') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = (int) ($this->settings['smtp_port'] ?? (getenv('MAIL_PORT') ?: (getenv('SMTP_PORT') ?: 587)));
        
        // Default sender
        $fromEmail = $this->settings['from_email'] ?? getenv('MAIL_FROM_ADDRESS') ?: 'noreply@alumni.edu';
        $fromName = $this->settings['from_name'] ?? getenv('MAIL_FROM_NAME') ?: 'Alumni System';
        $this->mailer->setFrom($fromEmail, $fromName);
        
        // Content type
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }
    
    /**
     * Get email template from database
     */
    public function getTemplate(string $templateKey): ?array {
        return $this->db->fetchOne(
            "SELECT * FROM email_templates WHERE template_key = ? AND is_active = TRUE",
            [$templateKey]
        );
    }
    
    /**
     * Parse template with variables
     */
    private function parseTemplate(string $content, array $variables): string {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }
    
    /**
     * Send email using template
     */
    public function sendTemplateEmail(string $to, string $templateKey, array $variables = []): bool {
        $template = $this->getTemplate($templateKey);
        
        if (!$template) {
            error_log("Email template not found: $templateKey");
            return false;
        }
        
        $subject = $this->parseTemplate($template['subject'], $variables);
        $body = $this->parseTemplate($template['body'], $variables);
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send raw email
     */
    public function send(string $to, string $subject, string $body): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->wrapInTemplate($body);
            $this->mailer->AltBody = strip_tags($body);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Wrap content in email template
     */
    private function wrapInTemplate(string $content): string {
        $institutionName = $this->settings['from_name'] ?? 'Alumni System';
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .footer { background: #1f2937; color: #9ca3af; padding: 20px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        h2 { color: #1e40af; margin-top: 0; }
        .code { background: #1e40af; color: white; padding: 15px 30px; font-size: 24px; letter-spacing: 5px; border-radius: 8px; display: inline-block; margin: 20px 0; }
        a { color: #1e40af; }
        .btn { display: inline-block; background: #1e40af; color: white !important; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$institutionName}</h1>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>&copy; {$institutionName} | All rights reserved</p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Send verification email
     */
    public function sendVerificationEmail(string $to, string $name, string $code): bool {
        return $this->sendTemplateEmail($to, 'verification', [
            'name' => $name,
            'code' => $code,
            'expiration_minutes' => '10'
        ]);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $to, string $name, string $code): bool {
        return $this->sendTemplateEmail($to, 'password_reset', [
            'name' => $name,
            'code' => $code,
            'expiration_minutes' => '10'
        ]);
    }
    
    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(string $to, string $name, string $alumniId): bool {
        return $this->sendTemplateEmail($to, 'welcome', [
            'name' => $name,
            'alumni_id' => $alumniId
        ]);
    }
    
    /**
     * Send event reminder
     */
    public function sendEventReminder(string $to, string $name, array $event): bool {
        return $this->sendTemplateEmail($to, 'event_reminder', [
            'name' => $name,
            'event_title' => $event['title'],
            'event_date' => date('F j, Y', strtotime($event['event_date'])),
            'location' => $event['location'] ?? 'TBA'
        ]);
    }
    
    /**
     * Send points earned notification
     */
    public function sendPointsEarned(string $to, string $name, int $points, string $reason, int $totalPoints): bool {
        return $this->sendTemplateEmail($to, 'points_earned', [
            'name' => $name,
            'points' => $points,
            'reason' => $reason,
            'total_points' => $totalPoints
        ]);
    }
}

/**
 * Get email service instance
 */
function email(): EmailService {
    static $instance = null;
    if ($instance === null) {
        $instance = new EmailService();
    }
    return $instance;
}
