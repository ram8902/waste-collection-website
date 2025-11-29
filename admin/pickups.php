<?php
require_once '../config.php';
requireAdmin();

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(pr.tracking_id LIKE ? OR pr.name LIKE ? OR pr.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($status_filter)) {
    $where[] = "pr.status = ?";
    $params[] = $status_filter;
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get pickup requests
try {
    $sql = "SELECT pr.*, u.name as user_name, s.name as staff_name 
            FROM pickup_requests pr 
            LEFT JOIN users u ON pr.user_id = u.id 
            LEFT JOIN staff s ON pr.staff_id = s.id 
            $where_sql
            ORDER BY pr.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pickups = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Error loading pickup requests.';
    $pickups = [];
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM pickup_requests WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        header('Location: pickups.php?msg=deleted');
        exit();
    } catch (PDOException $e) {
        $error = 'Error deleting pickup request.';
    }
}

// Handle CSV export (must be before any HTML output)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pickup_requests_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Tracking ID', 'User Name', 'Email', 'Phone', 'Address', 'Waste Type', 'Pickup Date/Time', 'Status', 'Staff', 'Created At']);
    
    foreach ($pickups as $pickup) {
        fputcsv($output, [
            $pickup['id'],
            $pickup['tracking_id'],
            $pickup['user_name'] ?? 'N/A',
            $pickup['email'],
            $pickup['phone'],
            $pickup['address'],
            $pickup['waste_type'],
            $pickup['pickup_datetime'],
            $pickup['status'],
            $pickup['staff_name'] ?? 'Not Assigned',
            $pickup['created_at']
        ]);
    }
    
    fclose($output);
    exit();
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Pickups - Admin Panel</title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pickups.php">Pickups</a>
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
        <div class="row mb-4 align-items-end">
            <div class="col-md-8">
                <h2 class="page-header-title">Manage Pickup Requests</h2>
                <p class="page-header-subtitle mb-0">Search, filter, and manage all waste collection requests.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="pickups.php?export=csv" class="btn btn-success btn-filter d-inline-flex w-auto px-4">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </a>
            </div>
        </div>

        <?php if ($msg === 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Pickup request deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="" class="row g-3">
                <div class="col-lg-5">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control form-control-search" name="search" 
                               placeholder="Search by tracking ID, name, or email" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select class="form-select form-select-filter" name="status">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Assigned" <?php echo $status_filter === 'Assigned' ? 'selected' : ''; ?>>Assigned</option>
                        <option value="In-Progress" <?php echo $status_filter === 'In-Progress' ? 'selected' : ''; ?>>In-Progress</option>
                        <option value="Completed" <?php echo $status_filter === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary btn-filter w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
                <div class="col-lg-2">
                    <a href="pickups.php" class="btn btn-secondary btn-filter w-100">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Pickup Requests Table -->
        <div class="table-card">
            <div class="table-card-header">
                <h5 class="mb-0 fw-bold">All Requests</h5>
                <span class="badge bg-primary rounded-pill"><?php echo count($pickups); ?> Records</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Tracking ID</th>
                            <th>User Details</th>
                            <th>Waste Type</th>
                            <th>Pickup Date</th>
                            <th>Status</th>
                            <th>Assigned Staff</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pickups)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        No pickup requests found matching your criteria.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pickups as $pickup): ?>
                                <tr>
                                    <td><span class="text-muted">#<?php echo $pickup['id']; ?></span></td>
                                    <td>
                                        <a href="edit_pickup.php?id=<?php echo $pickup['id']; ?>" class="tracking-link">
                                            <?php echo htmlspecialchars($pickup['tracking_id']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold"><?php echo htmlspecialchars($pickup['user_name'] ?? 'N/A'); ?></span>
                                            <small class="text-muted"><?php echo htmlspecialchars($pickup['email']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?php echo htmlspecialchars($pickup['waste_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-nowrap">
                                            <i class="bi bi-calendar-event me-2 text-muted"></i>
                                            <?php echo date('M j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?>
                                        </div>
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
                                        <?php if (!empty($pickup['staff_name'])): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 12px;">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                <?php echo htmlspecialchars($pickup['staff_name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Not Assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="edit_pickup.php?id=<?php echo $pickup['id']; ?>" class="btn btn-primary btn-icon" title="Edit">
                                                <i class="bi bi-pencil-fill" style="font-size: 0.8rem;"></i>
                                            </a>
                                            <a href="pickups.php?delete=<?php echo $pickup['id']; ?>" 
                                               class="btn btn-danger btn-icon" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this pickup request?')">
                                                <i class="bi bi-trash-fill" style="font-size: 0.8rem;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>

