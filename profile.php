<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT name, email, phone, profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update Profile Logic
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        if (empty($name) || empty($email) || empty($phone)) {
            $error = "All fields are required.";
        } else {
            try {
                // Handle Image Upload
                $profile_image = $user['profile_image'] ?? null;
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/profiles/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                    $fileName = $_FILES['profile_image']['name'];
                    $fileSize = $_FILES['profile_image']['size'];
                    $fileType = $_FILES['profile_image']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                    
                    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $dest_path = $uploadDir . $newFileName;
                        
                        if(move_uploaded_file($fileTmpPath, $dest_path)) {
                            $profile_image = $newFileName;
                            $_SESSION['user_image'] = $profile_image;
                        }
                    }
                }

                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, profile_image = ? WHERE id = ?");
                $stmt->execute([$name, $email, $phone, $profile_image, $user_id]);
                
                // Update session name if changed
                $_SESSION['user_name'] = $name;
                
                // Refresh user data
                $user['name'] = $name;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['profile_image'] = $profile_image;
                
                $success = "Profile updated successfully!";
            } catch (PDOException $e) {
                $error = "Failed to update profile. Email might already be in use.";
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Change Password Logic
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error = "All password fields are required.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $stored_password = $stmt->fetchColumn();

            if (password_verify($current_password, $stored_password)) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                $success = "Password changed successfully!";
            } else {
                $error = "Incorrect current password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Settings - Waste Collection Service</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light modern-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold brand-logo" href="index.php">
                <span class="brand-icon">♻️</span> Waste Collection
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="book_pickup.php">Book Pickup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="track.php">Track Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">My History</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-4">
                        <a class="nav-link active" href="profile.php">
                            <?php 
                            $nav_img = !empty($_SESSION['user_image']) ? 'assets/images/uploads/profiles/' . $_SESSION['user_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name']) . '&background=random';
                            ?>
                            <img src="<?php echo htmlspecialchars($nav_img); ?>" alt="Profile" class="rounded-circle me-1 object-fit-cover" width="30" height="30">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-nav" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 fw-bold">Profile & Settings</h1>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success modern-alert mb-4"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger modern-alert mb-4"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Profile Section -->
                <div class="card shadow-sm mb-4 border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 rounded-top-4">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-person-vcard me-2"></i> Personal Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="row g-3">
                                <div class="col-12 text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        <?php 
                                        $img_src = !empty($user['profile_image']) ? 'assets/images/uploads/profiles/' . $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random';
                                        ?>
                                        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Profile" class="rounded-circle object-fit-cover" width="120" height="120" style="border: 4px solid var(--card-bg); box-shadow: var(--shadow);">
                                        <label for="profile_image" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow-sm" style="cursor: pointer;">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*" onchange="document.getElementById('save-btn').click()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" id="save-btn" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="card shadow-sm mb-4 border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 rounded-top-4">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-shield-lock me-2"></i> Security Settings
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <input type="hidden" name="change_password" value="1">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <div class="col-12 text-end mt-3">
                                    <button type="submit" class="btn btn-outline-primary rounded-pill px-4">Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Preferences Section -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 rounded-top-4">
                        <h5 class="card-title mb-0 fw-bold text-primary">
                            <i class="bi bi-gear me-2"></i> Preferences
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Dark Mode</h6>
                                <p class="text-muted small mb-0">Switch between light and dark themes</p>
                            </div>
                            <button class="theme-toggle-modern theme-toggle" id="themeToggle">
                                <span class="theme-icon">☀️</span>
                                <span class="theme-text">Light Mode</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include 'includes/chatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>
