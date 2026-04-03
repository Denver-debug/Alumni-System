<?php
/**
 * Form Builder API - Manage Form Fields (Admin)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../middleware/auth.php';

requireAdmin();

try {
    $db = Database::getInstance()->getConnection();
    $user = getCurrentUser();
    $method = $_SERVER['REQUEST_METHOD'];
    $data = getRequestBody();
    
    switch ($method) {
        case 'POST':
            // Create new field
            $fieldName = sanitize($data['field_name'] ?? '');
            $fieldLabel = sanitize($data['field_label'] ?? '');
            $fieldType = $data['field_type'] ?? 'text';
            
            if (!$fieldName || !$fieldLabel) {
                respondError('Field name and label are required', 400);
            }
            
            // Validate field name (alphanumeric and underscores only)
            if (!preg_match('/^[a-z][a-z0-9_]*$/', $fieldName)) {
                respondError('Field name must start with a letter and contain only lowercase letters, numbers, and underscores', 400);
            }
            
            // Check if field name exists
            $stmt = $db->prepare("SELECT id FROM form_fields WHERE field_name = :field_name");
            $stmt->execute(['field_name' => $fieldName]);
            if ($stmt->fetch()) {
                respondError('Field name already exists', 400);
            }
            
            // Get max display order
            $stmt = $db->query("SELECT MAX(display_order) FROM form_fields");
            $maxOrder = $stmt->fetchColumn() ?: 0;
            
            $stmt = $db->prepare("
                INSERT INTO form_fields (
                    field_name, field_label, field_type, field_options, validation_rules,
                    form_section, display_order, is_required, is_builtin, is_active, column_width,
                    created_at, updated_at
                ) VALUES (
                    :field_name, :field_label, :field_type, :field_options, :validation_rules,
                    :form_section, :display_order, :is_required, 0, :is_active, :column_width,
                    NOW(), NOW()
                )
            ");
            
            $stmt->execute([
                'field_name' => $fieldName,
                'field_label' => $fieldLabel,
                'field_type' => $fieldType,
                'field_options' => json_encode($data['field_options'] ?? []),
                'validation_rules' => json_encode($data['validation_rules'] ?? []),
                'form_section' => $data['form_section'] ?? 'general',
                'display_order' => $maxOrder + 1,
                'is_required' => $data['is_required'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'column_width' => $data['column_width'] ?? '100%'
            ]);
            
            $fieldId = $db->lastInsertId();
            
            // Log action
            logAdminActivity($user['id'], 'create', 'form_fields', $fieldId, "Created form field: $fieldLabel");
            
            respondSuccess(['id' => $fieldId, 'message' => 'Field created successfully'], 201);
            break;
            
        case 'PUT':
            // Update field
            $fieldId = $data['id'] ?? $_GET['id'] ?? null;
            
            if (!$fieldId) {
                respondError('Field ID required', 400);
            }
            
            // Check if field exists and is not builtin
            $stmt = $db->prepare("SELECT * FROM form_fields WHERE id = :id");
            $stmt->execute(['id' => $fieldId]);
            $field = $stmt->fetch();
            
            if (!$field) {
                respondError('Field not found', 404);
            }
            
            // Only allow updating certain fields for builtin fields
            $updates = [];
            $params = ['id' => $fieldId];
            
            if (!$field['is_builtin']) {
                if (isset($data['field_label'])) {
                    $updates[] = 'field_label = :field_label';
                    $params['field_label'] = sanitize($data['field_label']);
                }
                if (isset($data['field_type'])) {
                    $updates[] = 'field_type = :field_type';
                    $params['field_type'] = $data['field_type'];
                }
                if (isset($data['field_options'])) {
                    $updates[] = 'field_options = :field_options';
                    $params['field_options'] = json_encode($data['field_options']);
                }
                if (isset($data['validation_rules'])) {
                    $updates[] = 'validation_rules = :validation_rules';
                    $params['validation_rules'] = json_encode($data['validation_rules']);
                }
            }
            
            // These can be updated for all fields
            if (isset($data['display_order'])) {
                $updates[] = 'display_order = :display_order';
                $params['display_order'] = (int)$data['display_order'];
            }
            if (isset($data['is_required'])) {
                $updates[] = 'is_required = :is_required';
                $params['is_required'] = $data['is_required'] ? 1 : 0;
            }
            if (isset($data['is_active'])) {
                $updates[] = 'is_active = :is_active';
                $params['is_active'] = $data['is_active'] ? 1 : 0;
            }
            if (isset($data['column_width'])) {
                $updates[] = 'column_width = :column_width';
                $params['column_width'] = $data['column_width'];
            }
            if (isset($data['form_section'])) {
                $updates[] = 'form_section = :form_section';
                $params['form_section'] = $data['form_section'];
            }
            
            if (empty($updates)) {
                respondError('No fields to update', 400);
            }
            
            $updates[] = 'updated_at = NOW()';
            $sql = "UPDATE form_fields SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            logAdminActivity($user['id'], 'update', 'form_fields', $fieldId, "Updated form field");
            
            respondSuccess(['message' => 'Field updated successfully']);
            break;
            
        case 'DELETE':
            $fieldId = $_GET['id'] ?? null;
            
            if (!$fieldId) {
                respondError('Field ID required', 400);
            }
            
            // Check if field exists and is not builtin
            $stmt = $db->prepare("SELECT * FROM form_fields WHERE id = :id");
            $stmt->execute(['id' => $fieldId]);
            $field = $stmt->fetch();
            
            if (!$field) {
                respondError('Field not found', 404);
            }
            
            if ($field['is_builtin']) {
                respondError('Cannot delete builtin fields', 400);
            }
            
            $stmt = $db->prepare("DELETE FROM form_fields WHERE id = :id");
            $stmt->execute(['id' => $fieldId]);
            
            logAdminActivity($user['id'], 'delete', 'form_fields', $fieldId, "Deleted form field: " . $field['field_label']);
            
            respondSuccess(['message' => 'Field deleted successfully']);
            break;
            
        default:
            respondError('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    respondError('Operation failed: ' . $e->getMessage(), 500);
}

function logAdminActivity($adminId, $action, $targetType, $targetId, $description) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        INSERT INTO admin_activities (admin_id, action, target_type, target_id, description, created_at)
        VALUES (:admin_id, :action, :target_type, :target_id, :description, NOW())
    ");
    $stmt->execute([
        'admin_id' => $adminId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'description' => $description
    ]);
}
