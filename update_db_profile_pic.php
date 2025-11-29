<?php
require_once 'config.php';

try {
    // Add profile_image column if it doesn't exist
    $sql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL";
    $pdo->exec($sql);
    echo "Successfully added profile_image column to users table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column profile_image already exists.\n";
    } else {
        echo "Error updating database: " . $e->getMessage() . "\n";
    }
}
?>
