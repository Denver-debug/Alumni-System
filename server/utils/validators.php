<?php
/**
 * Validation Utilities
 * Alumni Management System
 */

/**
 * Validator Class
 */
class Validator {
    private $data = [];
    private $errors = [];
    private $rules = [];
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    /**
     * Create new validator
     */
    public static function make(array $data): self {
        return new self($data);
    }
    
    /**
     * Add validation rule
     */
    public function rule(string $field, string $rule, ...$params): self {
        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }
        $this->rules[$field][] = ['rule' => $rule, 'params' => $params];
        return $this;
    }
    
    /**
     * Add multiple rules for a field
     */
    public function rules(string $field, array $rules): self {
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->rule($field, $rule);
            } elseif (is_array($rule)) {
                $this->rule($field, $rule[0], ...array_slice($rule, 1));
            }
        }
        return $this;
    }
    
    /**
     * Validate and return result
     */
    public function validate(): bool {
        $this->errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            
            foreach ($rules as $ruleData) {
                $rule = $ruleData['rule'];
                $params = $ruleData['params'];
                
                $error = $this->checkRule($field, $value, $rule, $params);
                if ($error) {
                    $this->errors[$field] = $error;
                    break; // Stop at first error for this field
                }
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Check a single rule
     */
    private function checkRule(string $field, $value, string $rule, array $params): ?string {
        $label = ucfirst(str_replace('_', ' ', $field));
        
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    return "$label is required";
                }
                break;
                
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "$label must be a valid email address";
                }
                break;
                
            case 'min':
                $min = $params[0] ?? 0;
                if (is_string($value) && strlen($value) < $min) {
                    return "$label must be at least $min characters";
                }
                if (is_numeric($value) && $value < $min) {
                    return "$label must be at least $min";
                }
                break;
                
            case 'max':
                $max = $params[0] ?? 255;
                if (is_string($value) && strlen($value) > $max) {
                    return "$label must not exceed $max characters";
                }
                if (is_numeric($value) && $value > $max) {
                    return "$label must not exceed $max";
                }
                break;
                
            case 'numeric':
                if ($value && !is_numeric($value)) {
                    return "$label must be a number";
                }
                break;
                
            case 'integer':
                if ($value && !filter_var($value, FILTER_VALIDATE_INT)) {
                    return "$label must be an integer";
                }
                break;
                
            case 'alpha':
                if ($value && !preg_match('/^[a-zA-Z]+$/', $value)) {
                    return "$label must only contain letters";
                }
                break;
                
            case 'alphanumeric':
                if ($value && !preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    return "$label must only contain letters and numbers";
                }
                break;
                
            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    return "$label must be a valid URL";
                }
                break;
                
            case 'date':
                if ($value && !strtotime($value)) {
                    return "$label must be a valid date";
                }
                break;
                
            case 'in':
                if ($value && !in_array($value, $params)) {
                    return "$label must be one of: " . implode(', ', $params);
                }
                break;
                
            case 'regex':
                $pattern = $params[0] ?? '';
                if ($value && !preg_match($pattern, $value)) {
                    return "$label format is invalid";
                }
                break;
                
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($this->data[$confirmField] ?? null)) {
                    return "$label confirmation does not match";
                }
                break;
                
            case 'unique':
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;
                
                if ($value && $table) {
                    if (!validateSqlIdentifier($table) || !validateSqlIdentifier($column)) {
                        return "$label validation configuration is invalid";
                    }

                    $db = Database::getInstance();
                    $sql = "SELECT COUNT(*) as count FROM `{$table}` WHERE `{$column}` = ?";
                    $sqlParams = [$value];
                    
                    if ($exceptId !== null && $exceptId !== '') {
                        $sql .= " AND id != ?";
                        $sqlParams[] = $exceptId;
                    }
                    
                    $result = $db->fetchOne($sql, $sqlParams);
                    if ($result['count'] > 0) {
                        return "$label already exists";
                    }
                }
                break;
                
            case 'exists':
                $table = $params[0] ?? '';
                $column = $params[1] ?? 'id';
                
                if ($value && $table) {
                    if (!validateSqlIdentifier($table) || !validateSqlIdentifier($column)) {
                        return "$label validation configuration is invalid";
                    }

                    $db = Database::getInstance();
                    $result = $db->fetchOne("SELECT COUNT(*) as count FROM `{$table}` WHERE `{$column}` = ?", [$value]);
                    if ($result['count'] === 0) {
                        return "$label does not exist";
                    }
                }
                break;
                
            case 'phone':
                if ($value && !preg_match('/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,4}[-\s\.]?[0-9]{1,9}$/', $value)) {
                    return "$label must be a valid phone number";
                }
                break;
                
            case 'password':
                if ($value) {
                    $errors = Password::validate($value);
                    if (!empty($errors)) {
                        return $errors[0];
                    }
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Get validation errors
     */
    public function errors(): array {
        return $this->errors;
    }
    
    /**
     * Check if validation failed
     */
    public function fails(): bool {
        return !empty($this->errors);
    }
    
    /**
     * Get validated data (only fields that were validated)
     */
    public function validated(): array {
        $validated = [];
        foreach (array_keys($this->rules) as $field) {
            if (isset($this->data[$field])) {
                $validated[$field] = $this->data[$field];
            }
        }
        return $validated;
    }
}

/**
 * Quick validation helper
 */
function validate(array $data, array $rules): array {
    $validator = Validator::make($data);
    
    foreach ($rules as $field => $fieldRules) {
        if (is_string($fieldRules)) {
            $fieldRules = explode('|', $fieldRules);
        }
        
        foreach ($fieldRules as $rule) {
            if (is_string($rule)) {
                if (strpos($rule, ':') !== false) {
                    list($ruleName, $params) = explode(':', $rule, 2);
                    $params = explode(',', $params);
                    $validator->rule($field, $ruleName, ...$params);
                } else {
                    $validator->rule($field, $rule);
                }
            }
        }
    }
    
    if (!$validator->validate()) {
        return $validator->errors();
    }
    
    return [];
}

/**
 * SQL Identifier validation (for form builder)
 */
function validateSqlIdentifier(string $name): bool {
    // Must start with letter, contain only alphanumeric and underscore, max 64 chars
    if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/i', $name)) {
        return false;
    }
    
    // Check against reserved keywords
    $reserved = [
        'select', 'insert', 'update', 'delete', 'drop', 'create', 'alter', 
        'table', 'database', 'index', 'from', 'where', 'and', 'or', 'not',
        'null', 'true', 'false', 'like', 'in', 'between', 'join', 'on',
        'group', 'order', 'by', 'having', 'limit', 'offset', 'union',
        'all', 'distinct', 'as', 'case', 'when', 'then', 'else', 'end'
    ];
    
    return !in_array(strtolower($name), $reserved);
}
