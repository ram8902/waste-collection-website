<?php
ob_start();
require_once 'config.php';
requireLogin();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $ward = $_POST['ward'] ?? '';
    $waste_type = $_POST['waste_type'] ?? '';
    $pickup_datetime = $_POST['pickup_datetime'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    // Validation
    if (empty($name) || empty($phone) || empty($email) || empty($address) || empty($ward) || empty($waste_type) || empty($pickup_datetime)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header("Location: book_pickup.php");
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address.';
        header("Location: book_pickup.php");
        exit();
    } else {
        // Generate unique tracking ID
        $tracking_id = 'WC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        // Handle file upload
        $image_path = null;
        if (isset($_FILES['waste_image']) && $_FILES['waste_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'assets/images/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['waste_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $file_name = $tracking_id . '_' . time() . '.' . $file_extension;
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['waste_image']['tmp_name'], $target_path)) {
                    $image_path = $target_path;
                }
            }
        }

        // Insert pickup request
        try {
            $stmt = $pdo->prepare("INSERT INTO pickup_requests (user_id, tracking_id, name, phone, email, address, ward, waste_type, pickup_datetime, notes, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
            $stmt->execute([$user_id, $tracking_id, $name, $phone, $email, $address, $ward, $waste_type, $pickup_datetime, $notes, $image_path]);
            
            $_SESSION['success_message'] = "Pickup request submitted successfully! Your tracking ID is: <strong>$tracking_id</strong>";
            ob_end_clean();
            header("Location: track.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to submit pickup request. Please try again.';
            header("Location: book_pickup.php");
            exit();
        }
    }
} else {
    // If accessed directly without POST, redirect back
    header("Location: book_pickup.php");
    exit();
}
?>
