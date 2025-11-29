<?php
require_once '../config.php';
// requireStaff();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// Get assigned pickups (not completed or cancelled)
try {
    $stmt = $pdo->prepare("
        SELECT pr.*, u.name as user_name 
        FROM pickup_requests pr 
        LEFT JOIN users u ON pr.user_id = u.id 
        WHERE pr.staff_id = ? AND pr.status NOT IN ('Completed', 'Cancelled')
        ORDER BY pr.pickup_datetime ASC
    ");
    $stmt->execute([$staff_id]);
    $assigned_pickups = $stmt->fetchAll();
} catch (PDOException $e) {
    $assigned_pickups = [];
    $error = 'Error loading assigned pickups.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Waste Collection Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Staff Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">👷 Staff Panel</a>
            <div class="navbar-nav ms-auto">

                <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['staff_name']); ?></span>
                <a class="nav-link" href="../index.php">View Site</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid my-4">
        <h2 class="mb-4">My Assigned Pickups</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Pickup Requests Assigned to Me</h5>
            </div>
            <div class="card-body">
                <?php if (empty($assigned_pickups)): ?>
                    <div class="alert alert-info">No pickup requests assigned to you at the moment.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Tracking ID</th>
                                    <th>Customer Name</th>
                                    <th>Address</th>
                                    <th>Waste Type</th>
                                    <th>Pickup Date/Time</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assigned_pickups as $pickup): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($pickup['tracking_id']); ?></code></td>
                                        <td><?php echo htmlspecialchars($pickup['user_name'] ?? $pickup['name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($pickup['address'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo htmlspecialchars($pickup['waste_type']); ?></td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?></td>
                                        <td>
                                            <span class="badge status-<?php echo strtolower(str_replace(' ', '-', $pickup['status'])); ?>">
                                                <?php echo htmlspecialchars($pickup['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($pickup['notes'] ?? '', 0, 30)) . (strlen($pickup['notes'] ?? '') > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <a href="update_status.php?id=<?php echo $pickup['id']; ?>" class="btn btn-sm btn-primary">Update Status</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>

