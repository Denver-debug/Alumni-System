<?php
/**
 * Generate admin password hash
 * Run: php generate-admin-hash.php
 */

$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";
echo "Verification: " . (password_verify($password, $hash) ? 'PASS' : 'FAIL') . "\n";
echo "\n";
echo "SQL to insert admin:\n";
echo "INSERT INTO users (email, password, name, role, auth_provider, email_verified, status) VALUES\n";
echo "('admin@minsu.edu.ph', '$hash', 'System Administrator', 'system_admin', 'email', TRUE, 'active');\n";
