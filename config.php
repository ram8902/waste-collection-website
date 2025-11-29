<?php
/**
 * Database Configuration File
 * 
 * Instructions:
 * 1. Update the database credentials below to match your MySQL setup
 * 2. Make sure you have created the database and imported database.sql
 * 3. Default admin login: username: admin, password: admin123
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'waste_collection_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    die("Database connection failed: " . $e->getMessage());
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

