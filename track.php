<?php
require_once 'config.php';

$error = '';
$pickup = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tracking_id = trim($_POST['tracking_id'] ?? '');
    
    if (empty($tracking_id)) {
        $error = 'Please enter a tracking ID.';
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT pr.*, u.name as user_name, s.name as staff_name 
                FROM pickup_requests pr 
                LEFT JOIN users u ON pr.user_id = u.id 
                LEFT JOIN staff s ON pr.staff_id = s.id 
                WHERE pr.tracking_id = ?
            ");
            $stmt->execute([$tracking_id]);
            $pickup = $stmt->fetch();
            
            if (!$pickup) {
                $error = 'Tracking ID not found. Please check and try again.';
            }
        } catch (PDOException $e) {
            $error = 'Error retrieving pickup information. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Pickup - Waste Collection Service</title>
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
                        <a class="nav-link active" href="track.php">Track Status</a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="history.php">My History</a>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="track-page-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <!-- Page Header -->
                    <div class="track-page-header">
                        <h1 class="track-page-title">Track Pickup Status</h1>
                        <p class="track-page-subtitle">Enter your tracking ID to view live pickup status.</p>
                    </div>

                    <!-- Search Bar Card -->
                    <div class="track-search-card">
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success modern-alert mb-4">
                                <?php 
                                echo $_SESSION['success_message']; 
                                unset($_SESSION['success_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($error && !$pickup): ?>
                            <div class="track-error-alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="track-search-form">
                            <div class="track-search-input-wrapper">
                                <i class="bi bi-hash track-search-icon"></i>
                                <input type="text" class="track-search-input" name="tracking_id" 
                                       placeholder="Enter your tracking ID (e.g., WC-20241122-ABC123)" 
                                       value="<?php echo htmlspecialchars($_POST['tracking_id'] ?? ''); ?>" required>
                                <button class="btn-track-submit" type="submit">
                                    <i class="bi bi-search me-2"></i>Track
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Helper Card (Initial State) -->
                    <?php if (!$pickup && !$error): ?>
                        <div class="track-helper-card">
                            <div class="track-helper-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <p class="track-helper-text">
                                <strong>Tip:</strong> You can find your tracking ID in your booking confirmation.
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Result Card -->
                    <?php if ($pickup): ?>
                        <div class="track-result-card">
                            <!-- Status Timeline -->
                            <div class="track-timeline"  data-progress="<?php 
                                $statuses = ['Pending', 'Assigned', 'In-Progress', 'Completed', 'Cancelled'];
                                $current_status = $pickup['status'];
                                $current_index = array_search($current_status, $statuses);
                                echo $current_index !== false ? $current_index : 0;
                            ?>">
                                <?php
                                $statuses = ['Pending', 'Assigned', 'In-Progress', 'Completed', 'Cancelled'];
                                $current_status = $pickup['status'];
                                $current_index = array_search($current_status, $statuses);
                                ?>
                                <?php foreach ($statuses as $index => $status): ?>
                                    <div class="timeline-step <?php echo $index <= $current_index ? 'active' : ''; ?> <?php echo $index == $current_index ? 'current' : ''; ?>">
                                        <div class="timeline-step-icon"style="border-radius: 40px;">
                                            <?php if ($index < $current_index): ?>
                                                <i class="bi bi-check-circle-fill"></i>
                                            <?php elseif ($index == $current_index): ?>
                                                <i class="bi bi-circle-fill"></i>
                                            <?php else: ?>
                                                <i class="bi bi-circle"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-step-label"><?php echo $status; ?></div>
                                        <?php if ($index < count($statuses) - 1): ?>
                                            <div class="timeline-step-connector"></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Status Badge -->
                            <div class="track-status-badge-wrapper">
                                <span class="status-pill status-<?php echo strtolower(str_replace(' ', '-', $pickup['status'])); ?>">
                                    <i class="bi bi-<?php 
                                        echo $pickup['status'] === 'Completed' ? 'check-circle' : 
                                            ($pickup['status'] === 'Cancelled' ? 'x-circle' : 
                                            ($pickup['status'] === 'In-Progress' ? 'arrow-repeat' : 'clock')); 
                                    ?> me-2"></i>
                                    <?php echo htmlspecialchars($pickup['status']); ?>
                                </span>
                            </div>

                            <!-- Details Section -->
                            <div class="track-details-section">
                                <h3 class="track-details-title">
                                    <i class="bi bi-clipboard-data me-2"></i>
                                    Pickup Request Details
                                </h3>

                                <div class="track-details-grid">
                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-hash"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Tracking ID</div>
                                            <div class="track-detail-value tracking-id-value"><?php echo htmlspecialchars($pickup['tracking_id']); ?></div>
                                        </div>
                                    </div>

                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-trash"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Waste Type</div>
                                            <div class="track-detail-value"><?php echo htmlspecialchars($pickup['waste_type']); ?></div>
                                        </div>
                                    </div>

                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Pickup Date & Time</div>
                                            <div class="track-detail-value"><?php echo date('F j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?></div>
                                        </div>
                                    </div>

                                    <div class="track-detail-item track-detail-item-full">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Address</div>
                                            <div class="track-detail-value"><?php echo nl2br(htmlspecialchars($pickup['address'])); ?></div>
                                        </div>
                                    </div>

                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Assigned Staff</div>
                                            <div class="track-detail-value"><?php echo htmlspecialchars($pickup['staff_name'] ?? 'Not assigned yet'); ?></div>
                                        </div>
                                    </div>

                                    <?php if ($pickup['notes']): ?>
                                        <div class="track-detail-item track-detail-item-full">
                                            <div class="track-detail-icon">
                                                <i class="bi bi-sticky"></i>
                                            </div>
                                            <div class="track-detail-content">
                                                <div class="track-detail-label">Notes</div>
                                                <div class="track-detail-value"><?php echo nl2br(htmlspecialchars($pickup['notes'])); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($pickup['image_path'] && file_exists($pickup['image_path'])): ?>
                                        <div class="track-detail-item track-detail-item-full">
                                            <div class="track-detail-icon">
                                                <i class="bi bi-image"></i>
                                            </div>
                                            <div class="track-detail-content">
                                                <div class="track-detail-label">Waste Image</div>
                                                <div class="track-detail-value">
                                                    <a href="<?php echo htmlspecialchars($pickup['image_path']); ?>" target="_blank" class="track-image-link">
                                                        <img src="<?php echo htmlspecialchars($pickup['image_path']); ?>" 
                                                             alt="Waste Image" class="track-image-thumbnail">
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Request Created</div>
                                            <div class="track-detail-value track-detail-value-small"><?php echo date('F j, Y g:i A', strtotime($pickup['created_at'])); ?></div>
                                        </div>
                                    </div>

                                    <div class="track-detail-item">
                                        <div class="track-detail-icon">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </div>
                                        <div class="track-detail-content">
                                            <div class="track-detail-label">Last Updated</div>
                                            <div class="track-detail-value track-detail-value-small"><?php echo date('F j, Y g:i A', strtotime($pickup['updated_at'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <?php include 'includes/chatbot.php'; ?>
</body>
</html>

