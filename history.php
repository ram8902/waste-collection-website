<?php
require_once 'config.php';
requireLogin();
require_once 'includes/report_helpers.php';

$user_id = $_SESSION['user_id'];

// Get month and year from GET parameters, default to current month
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Validate month and year
$month = max(1, min(12, $month));
$year = max(2020, min(2100, $year));

// Get user history data
$summary = getUserMonthlySummary($pdo, $user_id, $month, $year);
$history = getUserMonthlyHistory($pdo, $user_id, $month, $year);

$month_name = date('F', mktime(0, 0, 0, $month, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pickup History - Waste Collection Service</title>
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
            </div>
        </div>
    </nav>

    <div class="history-page-container">
        <div class="container">
            <!-- Page Header -->
            <div class="history-page-header">
                <h1 class="history-page-title">My Pickup History</h1>
                <p class="history-page-subtitle">View your past pickup requests and status at a glance.</p>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar-modern">
                <form method="GET" action="" class="filter-form">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label for="month" class="filter-label">
                                <i class="bi bi-calendar3 me-2"></i>Month
                            </label>
                            <select class="form-select-modern" id="month" name="month" required>
                                <?php
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                ];
                                foreach ($months as $m => $name) {
                                    $selected = $month == $m ? 'selected' : '';
                                    echo "<option value=\"$m\" $selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label for="year" class="filter-label">
                                <i class="bi bi-calendar-year me-2"></i>Year
                            </label>
                            <select class="form-select-modern" id="year" name="year" required>
                                <?php
                                $current_year = date('Y');
                                for ($y = $current_year; $y >= $current_year - 5; $y--) {
                                    $selected = $year == $y ? 'selected' : '';
                                    echo "<option value=\"$y\" $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <button type="submit" class="btn-filter-submit">
                                <i class="bi bi-search me-2"></i>Show History
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (empty($history) && !isset($_GET['month'])): ?>
                <!-- Initial State -->
                <div class="empty-state-modern">
                    <div class="empty-state-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3>Select a Month to View History</h3>
                    <p>Please select a month and year above to view your pickup history.</p>
                </div>
            <?php elseif (empty($history)): ?>
                <!-- Empty State -->
                <div class="empty-state-modern">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h3>No Pickups Found</h3>
                    <p>No pickup requests found for <?php echo $month_name . ' ' . $year; ?>. Try selecting a different month or <a href="book_pickup.php">book a new pickup</a>.</p>
                </div>
            <?php else: ?>
                <!-- Summary Stat Cards -->
                <div class="stats-grid">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-card-icon">
                            <i class="bi bi-list-ul"></i>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-card-number"><?php echo $summary['total']; ?></div>
                            <div class="stat-card-label">Total Pickups</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-success">
                        <div class="stat-card-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-card-number"><?php echo $summary['completed']; ?></div>
                            <div class="stat-card-label">Completed</div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-warning">
                        <div class="stat-card-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-card-content">
                            <div class="stat-card-number"><?php echo $summary['pending']; ?></div>
                            <div class="stat-card-label">Pending</div>
                        </div>
                    </div>
                </div>

                <!-- History Table Card -->
                <div class="table-card-modern">
                    <div class="table-card-header">
                        <h3 class="table-card-title">
                            <i class="bi bi-table me-2"></i>
                            Pickup Requests for <?php echo $month_name . ' ' . $year; ?>
                        </h3>
                    </div>
                    <div class="table-card-body">
                        <div class="table-responsive-modern">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Tracking ID</th>
                                        <th>Waste Type</th>
                                        <th>Pickup Date/Time</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Assigned Staff</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $pickup): ?>
                                        <tr>
                                            <td>
                                                <a href="track.php" class="tracking-id-link">
                                                    <?php echo htmlspecialchars($pickup['tracking_id']); ?>
                                                </a>
                                                <div class="track-link-small">
                                                    <a href="track.php" class="track-link">
                                                        <i class="bi bi-arrow-right-circle me-1"></i>Track
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="waste-type-badge"><?php echo htmlspecialchars($pickup['waste_type']); ?></span>
                                            </td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?></td>
                                            <td>
                                                <span class="address-text"><?php echo htmlspecialchars(substr($pickup['address'], 0, 50)); ?><?php echo strlen($pickup['address']) > 50 ? '...' : ''; ?></span>
                                            </td>
                                            <td>
                                                <span class="status-pill status-<?php echo strtolower(str_replace(' ', '-', $pickup['status'])); ?>">
                                                    <?php echo htmlspecialchars($pickup['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="staff-name"><?php echo htmlspecialchars($pickup['staff_name'] ?? 'Not Assigned'); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($pickup['image_path'] && file_exists($pickup['image_path'])): ?>
                                                    <a href="<?php echo htmlspecialchars($pickup['image_path']); ?>" target="_blank" class="image-thumbnail-link">
                                                        <img src="<?php echo htmlspecialchars($pickup['image_path']); ?>" alt="Waste Image" class="image-thumbnail">
                                                    </a>
                                                <?php else: ?>
                                                    <span class="no-image-text">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <?php include 'includes/chatbot.php'; ?>
</body>
</html>

