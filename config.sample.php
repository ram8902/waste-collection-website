<?php
/**
 * Database Configuration File (Sample)
 * 
 * Instructions:
 * 1. Rename this file to config.php
 * 2. Update the database credentials below to match your MySQL setup
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'waste_collection_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Enter your password here

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ... rest of the helper functions would go here or be included ...
// For a sample file, usually just the config constants are enough, 
// but since the original config.php has functions, we should include them 
// or refactor. For now, I will copy the structure but keep credentials empty.

// PDO Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // In production/sample, maybe don't die with error details
    die("Database connection failed.");
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Helper function to check if staff is logged in
function isStaffLoggedIn() {
    return isset($_SESSION['staff_id']);
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Helper function to redirect if not admin
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit();
    }
}

// Helper function to redirect if not staff
function requireStaff() {
    if (!isStaffLoggedIn()) {
        header('Location: /staff/login.php');
        exit();
    }
}
?>
