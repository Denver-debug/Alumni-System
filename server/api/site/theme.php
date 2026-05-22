<?php
/**
 * Public Site Theme API
 * GET /api/site/theme
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

try {
    $db = Database::getInstance()->getConnection();

    $defaults = [
        'primary_color' => '#059669',
        'secondary_color' => '#0ea5e9',
        'accent_color' => '#f59e0b',
        'background_color' => '#f8fafc',
        'text_color' => '#1f2937',
        'heading_font' => 'Inter',
        'body_font' => 'Inter',
        'font_family' => 'Inter',
        'logo_url' => 'assets/images/logo.svg',
        'auth_background_image_url' => '',
        'favicon_url' => '',
        'sidebar_style' => 'dark',
        'border_radius' => 'md',
        'custom_css' => '',
    ];

    $stmt = $db->query("SELECT setting_key, setting_value FROM theme_settings");
    $rows = $stmt->fetchAll();

    $settings = $defaults;
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    if (($settings['body_font'] ?? '') === '' && ($settings['font_family'] ?? '') !== '') {
        $settings['body_font'] = $settings['font_family'];
    }

    if (($settings['heading_font'] ?? '') === '' && ($settings['font_family'] ?? '') !== '') {
        $settings['heading_font'] = $settings['font_family'];
    }

    if (($settings['font_family'] ?? '') === '' && ($settings['body_font'] ?? '') !== '') {
        $settings['font_family'] = $settings['body_font'];
    }

    respondSuccess($settings);
} catch (Exception $e) {
    respondError('Failed to load theme settings: ' . $e->getMessage(), 500);
}
