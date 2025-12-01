<?php
require_once 'config.php';
requireLogin();

$error = '';
$success = '';

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Check for session error
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Pickup - Waste Collection Service</title>
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
                        <a class="nav-link active" href="book_pickup.php">Book Pickup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="track.php">Track Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">My History</a>
            </div>
        </div>
    </nav>

    <div class="container form-page-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Page Header -->
                <div class="form-page-header">
                    <h1 class="form-page-title">Book Waste Pickup</h1>
                    <p class="form-page-subtitle">Fill out the form below to schedule a pickup</p>
                </div>

                <!-- Form Card -->
                <div class="form-card-modern">
                    <?php if ($error): ?>
                        <div class="alert alert-danger modern-alert"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success modern-alert"><?php echo $success; ?></div>
                        <div class="text-center mt-4">
                            <a href="track.php" class="btn btn-primary">Track Status</a>
                            <a href="book_pickup.php" class="btn btn-outline-primary ms-2">Book Another</a>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="process_pickup.php" enctype="multipart/form-data">
                            <!-- Personal Information Section -->
                            <div class="form-section">
                                <h3 class="form-section-title">
                                    <i class="bi bi-person-circle me-2"></i>
                                    Personal Information
                                </h3>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating-modern">
                                            <input type="text" class="form-control-modern" id="name" name="name" placeholder="Full Name" required 
                                                   value="<?php echo htmlspecialchars($user['name'] ?? ($_POST['name'] ?? '')); ?>">
                                            <label for="name">
                                                <i class="bi bi-person"></i>
                                                <span>Full Name <span class="text-danger">*</span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating-modern">
                                            <input type="tel" class="form-control-modern" id="phone" name="phone" placeholder="Phone Number" required 
                                                   value="<?php echo htmlspecialchars($user['phone'] ?? ($_POST['phone'] ?? '')); ?>">
                                            <label for="phone">
                                                <i class="bi bi-telephone"></i>
                                                <span>Phone Number <span class="text-danger">*</span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-floating-modern mt-4">
                                    <input type="email" class="form-control-modern" id="email" name="email" placeholder="Email Address" required 
                                           value="<?php echo htmlspecialchars($user['email'] ?? ($_POST['email'] ?? '')); ?>">
                                    <label for="email">
                                        <i class="bi bi-envelope"></i>
                                        <span>Email Address <span class="text-danger">*</span></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Pickup Details Section -->
                            <div class="form-section">
                                <h3 class="form-section-title">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    Pickup Details
                                </h3>
                                
                                <div class="form-floating-modern">
                                    <textarea class="form-control-modern" id="address" name="address" rows="3" placeholder="Pickup Address" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                    <label for="address">
                                        <i class="bi bi-house-door"></i>
                                        <span>Pickup Address <span class="text-danger">*</span></span>
                                    </label>
                                    <button type="button" onclick="getPickupLocation()" class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 m-2" style="z-index: 5;">
                                        📍 Use Current Location
                                    </button>
                                </div>
                                

                                <div class="form-floating-modern mt-4">
                                    <select class="form-control-modern" id="ward" name="ward" required>
                                        <option value="">Select Your Ward</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT name FROM wards ORDER BY name ASC");
                                            while ($row = $stmt->fetch()) {
                                                $selected = (($_POST['ward'] ?? '') === $row['name']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row['name']) . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                                            }
                                        } catch (PDOException $e) {
                                            // Fallback or error handling
                                        }
                                        ?>
                                    </select>
                                    <label for="ward">
                                        <i class="bi bi-map"></i>
                                        <span>Select Your Ward <span class="text-danger">*</span></span>
                                    </label>
                                </div>

                                <div class="row g-4 mt-4">
                                    <div class="col-md-6">
                                        <div class="form-floating-modern">
                                            <select class="form-control-modern" id="waste_type" name="waste_type" required>
                                                <option value="">Select waste type</option>
                                                <option value="Plastic" <?php echo (($_POST['waste_type'] ?? '') === 'Plastic') ? 'selected' : ''; ?>>Plastic</option>
                                                <option value="E-waste" <?php echo (($_POST['waste_type'] ?? '') === 'E-waste') ? 'selected' : ''; ?>>E-waste</option>
                                                <option value="Metal" <?php echo (($_POST['waste_type'] ?? '') === 'Metal') ? 'selected' : ''; ?>>Metal</option>
                                                <option value="Household" <?php echo (($_POST['waste_type'] ?? '') === 'Household') ? 'selected' : ''; ?>>Household</option>
                                                <option value="Other" <?php echo (($_POST['waste_type'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                            <label for="waste_type">
                                                <i class="bi bi-trash"></i>
                                                <span>Waste Type <span class="text-danger">*</span></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating-modern">
                                            <input type="datetime-local" class="form-control-modern" id="pickup_datetime" name="pickup_datetime" required 
                                                   value="<?php echo htmlspecialchars($_POST['pickup_datetime'] ?? ''); ?>">
                                            <label for="pickup_datetime">
                                                <i class="bi bi-calendar-event"></i>
                                                <span>Preferred Pickup Date & Time <span class="text-danger">*</span></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information Section -->
                            <div class="form-section">
                                <h3 class="form-section-title">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Additional Information
                                </h3>
                                
                                <div class="form-floating-modern">
                                    <textarea class="form-control-modern" id="notes" name="notes" rows="3" placeholder="Additional Notes"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                                    <label for="notes">
                                        <i class="bi bi-sticky"></i>
                                        <span>Additional Notes (Optional)</span>
                                    </label>
                                </div>
                                
                                <div class="file-upload-modern mt-4">
                                    <label for="waste_image" class="file-upload-label">
                                        <div class="file-upload-content">
                                            <i class="bi bi-cloud-upload file-upload-icon"></i>
                                            <span class="file-upload-text">Upload Waste Image (Optional)</span>
                                            <span class="file-upload-hint">JPG, PNG (Max 5MB)</span>
                                        </div>
                                        <input type="file" class="file-upload-input" id="waste_image" name="waste_image" accept="image/jpeg,image/jpg,image/png">
                                    </label>
                                    <div class="file-name-display" id="file-name-display"></div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-submit-section">
                                <button type="submit" class="btn-submit-modern">
                                    <span>Submit Pickup Request</span>
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
<script>
        // Floating label functionality
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control-modern');
            inputs.forEach(input => {
                // Check if input has value on load
                if (input.value) {
                    input.classList.add('has-value');
                }
                
                input.addEventListener('focus', function() {
                    this.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.classList.remove('focused');
                    if (this.value) {
                        this.classList.add('has-value');
                    } else {
                        this.classList.remove('has-value');
                    }
                });
            });

            // File upload display
            const fileInput = document.getElementById('waste_image');
            const fileNameDisplay = document.getElementById('file-name-display');
            
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    if (fileName) {
                        fileNameDisplay.innerHTML = '<i class="bi bi-file-image me-2"></i>' + fileName;
                        fileNameDisplay.style.display = 'block';
                    } else {
                        fileNameDisplay.style.display = 'none';
                    }
                });
            }
        });

        function getPickupLocation() {
            if (navigator.geolocation) {
                const btn = document.querySelector('button[onclick="getPickupLocation()"]');
                const originalText = btn.innerHTML;
                btn.innerHTML = '⌛ Getting Link...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const link = `https://www.google.com/maps?q=${lat},${lng}`;
                    
                    const addressField = document.getElementById('address');
                    addressField.value = link;
                    addressField.classList.add('has-value'); // Ensure floating label stays up
                    
                    btn.innerHTML = '✅ Link Added!';
                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 2000);
                }, function(error) {
                    alert("Error getting location: " + error.message);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
    <?php include 'includes/chatbot.php'; ?>
</body>
</html>

