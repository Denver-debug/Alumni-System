<?php
/**
 * Form Builder API - Get Form Fields
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/helpers.php';

function normalizeFormSectionFilter(?string $section): ?string {
    if ($section === null || $section === '') {
        return null;
    }

    $value = strtolower(trim((string)$section));
    $map = [
        'general' => 'personal',
        'other' => 'additional',
        'personal' => 'personal',
        'contact' => 'contact',
        'education' => 'education',
        'employment' => 'employment',
        'social' => 'social',
        'additional' => 'additional',
    ];

    return $map[$value] ?? null;
}

function toLegacyFormSection(string $section): string {
    $map = [
        'personal' => 'general',
        'additional' => 'other',
    ];

    return $map[$section] ?? $section;
}

function toLegacyColumnWidth(string $width): string {
    $map = [
        'full' => '100%',
        'half' => '50%',
        'third' => '33%',
    ];

    return $map[$width] ?? $width;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $isActive = isset($_GET['is_active']) ? $_GET['is_active'] === 'true' || $_GET['is_active'] === '1' : null;
    $formSection = normalizeFormSectionFilter($_GET['form_section'] ?? null);
    
    $sql = "SELECT * FROM form_fields WHERE 1=1";
    $params = [];
    
    if ($isActive !== null) {
        $sql .= " AND is_active = :is_active";
        $params['is_active'] = $isActive ? 1 : 0;
    }
    
    if ($formSection) {
        $sql .= " AND form_section = :form_section";
        $params['form_section'] = $formSection;
    }
    
    $sql .= " ORDER BY display_order ASC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    $fields = $stmt->fetchAll();
    
    // Parse JSON fields
    foreach ($fields as &$field) {
        if ($field['field_options']) {
            $field['field_options'] = json_decode($field['field_options'], true);
        }
        if ($field['validation_rules']) {
            $field['validation_rules'] = json_decode($field['validation_rules'], true);
        }

        // Preserve raw schema values and expose legacy-compatible labels for baseline pages.
        $field['form_section_key'] = $field['form_section'];
        $field['column_width_key'] = $field['column_width'];
        $field['form_section'] = toLegacyFormSection((string)$field['form_section']);
        $field['column_width'] = toLegacyColumnWidth((string)$field['column_width']);
    }
    
    respondSuccess($fields);
    
} catch (Exception $e) {
    respondError('Failed to load form fields: ' . $e->getMessage(), 500);
}
