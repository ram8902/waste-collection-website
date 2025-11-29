<?php
/**
 * Setup Script - Generate Admin Password Hash
 * 
 * Run this file once to generate a proper password hash for the admin account.
 * This ensures the default admin password works correctly.
 * 
 * Usage: Open this file in your browser or run: php setup_admin.php
 */

require_once 'config.php';

$admin_username = 'admin';
$admin_password = 'admin123';

try {
    // Generate password hash
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt->execute([$admin_username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE admin SET password = ? WHERE username = ?");
        $stmt->execute([$hashed_password, $admin_username]);
        echo "<h2>Admin password updated successfully!</h2>";
        echo "<p>Username: <strong>$admin_username</strong></p>";
        echo "<p>Password: <strong>$admin_password</strong></p>";
        echo "<p>Hash: <code>$hashed_password</code></p>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->execute([$admin_username, $hashed_password]);
        echo "<h2>Admin account created successfully!</h2>";
        echo "<p>Username: <strong>$admin_username</strong></p>";
        echo "<p>Password: <strong>$admin_password</strong></p>";
        echo "<p>Hash: <code>$hashed_password</code></p>";
    }
    
    echo "<hr>";
    echo "<p><strong>You can now login at:</strong> <a href='admin/login.php'>admin/login.php</a></p>";
    echo "<p><em>Please delete this file (setup_admin.php) after setup for security!</em></p>";
    
} catch (PDOException $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure your database is set up correctly in config.php</p>";
}
?>

