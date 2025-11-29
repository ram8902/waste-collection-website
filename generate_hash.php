<?php
/**
 * Quick Hash Generator
 * 
 * This file generates a password hash for 'admin123'
 * Run this file in your browser or via command line: php generate_hash.php
 * 
 * Copy the generated hash and update the database.sql file or admin table directly.
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n\n";
echo "SQL Update Command:\n";
echo "UPDATE admin SET password = '$hash' WHERE username = 'admin';\n\n";
echo "Or update database.sql INSERT statement with this hash.\n";
?>

