-- Waste Collection Web Application Database Schema
-- Import this file into your MySQL database

CREATE DATABASE IF NOT EXISTS waste_collection_db;
USE waste_collection_db;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: staff
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: pickup_requests
CREATE TABLE IF NOT EXISTS pickup_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tracking_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    waste_type VARCHAR(50) NOT NULL,
    pickup_datetime DATETIME NOT NULL,
    notes TEXT,
    image_path VARCHAR(255),
    status ENUM('Pending', 'Assigned', 'In-Progress', 'Completed', 'Cancelled') DEFAULT 'Pending',
    staff_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin account
-- Username: admin
-- Password: admin123
-- 
-- IMPORTANT: The hash below is a placeholder. After importing this SQL file,
-- you MUST run setup_admin.php in your browser to generate a proper password hash.
-- 
-- Steps:
-- 1. Import this SQL file into your database
-- 2. Configure config.php with your database credentials
-- 3. Open http://localhost/your-project/setup_admin.php in your browser
-- 4. This will generate and set the correct password hash for 'admin123'
-- 5. Delete setup_admin.php after use for security
-- 
-- Alternative: If you want to manually set the hash, run this PHP command:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
-- Then update the admin table with the generated hash.
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- IMPORTANT: Change the default admin password after first login for security!

