<?php
require_once '../config.php';
requireAdmin();

// Get statistics
try {
    $stats = [];
    
    // Total pickup requests
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pickup_requests");
    $stats['total_pickups'] = $stmt->fetch()['count'];
    
    // Pending pickups
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pickup_requests WHERE status = 'Pending'");
    $stats['pending_pickups'] = $stmt->fetch()['count'];
    
    // Completed pickups
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM pickup_requests WHERE status = 'Completed'");
    $stats['completed_pickups'] = $stmt->fetch()['count'];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['total_users'] = $stmt->fetch()['count'];
    
    // Recent pickup requests
    $stmt = $pdo->query("SELECT pr.*, u.name as user_name FROM pickup_requests pr LEFT JOIN users u ON pr.user_id = u.id ORDER BY pr.created_at DESC LIMIT 10");
    $recent_pickups = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error loading statistics.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Waste Collection Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light modern-navbar">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold brand-logo" href="dashboard.php">
                <span class="brand-icon">🔧</span> Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pickups.php">Pickups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="monthly_report.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="staff.php">Staff</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn btn-outline-primary btn-sm px-3" href="../index.php" target="_blank">View Site</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn btn-danger btn-sm px-3 text-white" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container admin-dashboard-container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="page-header-title">Dashboard Overview</h2>
                <p class="page-header-subtitle">Monitor pickups, track performance, and manage users at a glance.</p>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <!-- Total Pickups -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card primary">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-recycle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['total_pickups']); ?></div>
                        <div class="stat-label">Total Pickups</div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Pickups -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card warning">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['pending_pickups']); ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
            </div>
            
            <!-- Completed Pickups -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card success">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['completed_pickups']); ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                </div>
            </div>
            
            <!-- Total Users -->
            <div class="col-xl-3 col-md-6">
                <div class="stat-card info">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="quick-actions-card">
                    <h5 class="section-title"><i class="bi bi-lightning-charge-fill text-warning"></i> Quick Actions</h5>
                    <div class="d-flex flex-wrap gap-4">
                        <a href="pickups.php" class="btn btn-primary action-btn">
                            <i class="bi bi-list-check"></i> Manage Pickups
                        </a>
                        <a href="monthly_report.php" class="btn btn-info text-white action-btn">
                            <i class="bi bi-bar-chart-fill"></i> Monthly Report
                        </a>
                        <a href="staff.php" class="btn btn-secondary action-btn">
                            <i class="bi bi-person-badge-fill"></i> Manage Staff
                        </a>
                        <a href="manage_wards.php" class="btn btn-success text-white action-btn">
                            <i class="bi bi-map-fill"></i> Manage Wards
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Pickup Requests -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="recent-pickups-card">
                    <div class="table-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">Recent Pickup Requests</h5>
                            <small class="text-muted">Latest 10 pickup records</small>
                        </div>
                        <a href="pickups.php" class="btn btn-outline-primary btn-sm btn-action-pill">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>User</th>
                                    <th>Waste Type</th>
                                    <th>Pickup Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_pickups)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No pickup requests found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_pickups as $pickup): ?>
                                        <tr>
                                            <td>
                                                <span class="font-monospace fw-bold text-primary">
                                                    #<?php echo htmlspecialchars($pickup['tracking_id']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2 bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                    <?php echo htmlspecialchars($pickup['user_name'] ?? 'Guest'); ?>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($pickup['waste_type']); ?></td>
                                            <td>
                                                <i class="bi bi-calendar-event me-1 text-muted"></i>
                                                <?php echo date('M j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    $statusClass = 'pending';
                                                    switch($pickup['status']) {
                                                        case 'Assigned': $statusClass = 'assigned'; break;
                                                        case 'In-Progress': $statusClass = 'in-progress'; break;
                                                        case 'Completed': $statusClass = 'completed'; break;
                                                        case 'Cancelled': $statusClass = 'cancelled'; break;
                                                    }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($pickup['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_pickup.php?id=<?php echo $pickup['id']; ?>" class="btn btn-primary btn-sm btn-action-pill">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>

