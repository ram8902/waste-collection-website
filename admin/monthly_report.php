<?php
require_once '../config.php';
requireAdmin();
require_once '../includes/report_helpers.php';

// Get month and year from GET parameters, default to current month
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$status_filter = $_GET['status_filter'] ?? '';
$waste_type_filter = $_GET['waste_type_filter'] ?? '';

// Validate month and year
$month = max(1, min(12, $month));
$year = max(2020, min(2100, $year));

// Get report data
$summary = getMonthlySummary($pdo, $month, $year);
$status_breakdown = getMonthlyStatusBreakdown($pdo, $month, $year);
$waste_type_breakdown = getMonthlyWasteTypeBreakdown($pdo, $month, $year);
$staff_breakdown = getMonthlyStaffBreakdown($pdo, $month, $year);
$pickups = getMonthlyPickups($pdo, $month, $year, $status_filter, $waste_type_filter);

// Handle CSV export (must be before any HTML output)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="monthly_report_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.csv"');
    
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

$month_name = date('F', mktime(0, 0, 0, $month, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - Admin Panel</title>
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
                        <a class="nav-link" href="pickups.php">Pickups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="monthly_report.php">Reports</a>
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
                <h2 class="page-header-title">Monthly Report</h2>
                <p class="page-header-subtitle mb-0">Analytics and performance metrics for <?php echo $month_name . ' ' . $year; ?></p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0 no-print">
                <a href="monthly_report.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>&export=csv" class="btn btn-success btn-filter d-inline-flex w-auto px-4">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Month/Year Filter Form -->
        <div class="filter-card no-print">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="month" class="form-label">Select Month</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-calendar-month"></i></span>
                        <select class="form-select form-select-filter border-start-0 ps-0" id="month" name="month" required>
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
                </div>
                <div class="col-md-4">
                    <label for="year" class="form-label">Select Year</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-calendar3"></i></span>
                        <select class="form-select form-select-filter border-start-0 ps-0" id="year" name="year" required>
                            <?php
                            $current_year = date('Y');
                            for ($y = $current_year; $y >= $current_year - 5; $y--) {
                                $selected = $year == $y ? 'selected' : '';
                                echo "<option value=\"$y\" $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-filter w-100">
                        <i class="bi bi-bar-chart-fill"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($pickups) && !isset($_GET['month'])): ?>
            <div class="alert alert-info d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                <div>Please select a month and year to generate the report.</div>
            </div>
        <?php elseif (empty($pickups)): ?>
            <div class="alert alert-warning d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>No records found for <?php echo $month_name . ' ' . $year; ?>.</div>
            </div>
        <?php else: ?>
            
            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="report-summary-card report-card-primary">
                        <div class="report-summary-icon">
                            <i class="bi bi-collection"></i>
                        </div>
                        <div class="report-summary-value"><?php echo number_format($summary['total_pickups']); ?></div>
                        <div class="report-summary-label">Total Pickups</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="report-summary-card report-card-success">
                        <div class="report-summary-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="report-summary-value"><?php echo number_format($summary['completed']); ?></div>
                        <div class="report-summary-label">Completed</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="report-summary-card report-card-warning">
                        <div class="report-summary-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="report-summary-value"><?php echo number_format($summary['pending']); ?></div>
                        <div class="report-summary-label">Pending</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="report-summary-card report-card-danger">
                        <div class="report-summary-icon">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="report-summary-value"><?php echo number_format($summary['cancelled']); ?></div>
                        <div class="report-summary-label">Cancelled</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="report-summary-card report-card-info">
                        <div class="report-summary-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="report-summary-value"><?php echo number_format($summary['unique_users']); ?></div>
                        <div class="report-summary-label">Unique Users</div>
                    </div>
                </div>
            </div>

            <!-- Breakdown Tables -->
            <div class="row g-4 mb-4">
                <!-- Status Breakdown -->
                <div class="col-md-6">
                    <div class="table-card h-100">
                        <div class="table-card-header">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart-fill me-2 text-primary"></i> Status Breakdown</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_for_calc = $summary['total_pickups'] > 0 ? $summary['total_pickups'] : 1;
                                    foreach ($status_breakdown as $status => $count): 
                                        $percentage = ($count / $total_for_calc) * 100;
                                    ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                    $statusClass = 'pending';
                                                    switch($status) {
                                                        case 'Assigned': $statusClass = 'assigned'; break;
                                                        case 'In-Progress': $statusClass = 'in-progress'; break;
                                                        case 'Completed': $statusClass = 'completed'; break;
                                                        case 'Cancelled': $statusClass = 'cancelled'; break;
                                                    }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($status); ?>
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold"><?php echo $count; ?></td>
                                            <td class="text-end text-muted"><?php echo number_format($percentage, 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($status_breakdown)): ?>
                                        <tr><td colspan="3" class="text-center text-muted">No data available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Waste Type Breakdown -->
                <div class="col-md-6">
                    <div class="table-card h-100">
                        <div class="table-card-header">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-recycle me-2 text-success"></i> Waste Type Breakdown</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Waste Type</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($waste_type_breakdown as $type => $count): 
                                        $percentage = ($count / $total_for_calc) * 100;
                                    ?>
                                        <tr>
                                            <td class="fw-medium"><?php echo htmlspecialchars($type); ?></td>
                                            <td class="text-end fw-bold"><?php echo $count; ?></td>
                                            <td class="text-end text-muted"><?php echo number_format($percentage, 1); ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($waste_type_breakdown)): ?>
                                        <tr><td colspan="3" class="text-center text-muted">No data available</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Staff Performance -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="table-card">
                        <div class="table-card-header">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge-fill me-2 text-info"></i> Staff Performance (Completed Pickups)</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th class="text-end">Completed Pickups</th>
                                        <th class="text-end">Contribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_completed = $summary['completed'] > 0 ? $summary['completed'] : 1;
                                    foreach ($staff_breakdown as $staff_name => $count): 
                                        $contribution = ($count / $total_completed) * 100;
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="staff-avatar primary me-2" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                                        <?php echo strtoupper(substr($staff_name, 0, 1)); ?>
                                                    </div>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($staff_name); ?></span>
                                                </div>
                                            </td>
                                            <td class="text-end text-success fw-bold"><?php echo $count; ?></td>
                                            <td class="text-end">
                                                <span class="badge bg-info rounded-pill">
                                                    <?php echo number_format($contribution, 1); ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($staff_breakdown)): ?>
                                        <tr><td colspan="3" class="text-center text-muted">No completed pickups assigned to staff this month.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <button onclick="window.print()" class="btn btn-primary btn-print" title="Print Report">
                <i class="bi bi-printer-fill"></i> Print Report
            </button>

        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
