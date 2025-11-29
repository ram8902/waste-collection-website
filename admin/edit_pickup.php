<?php
require_once '../config.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get pickup request
try {
    $stmt = $pdo->prepare("SELECT * FROM pickup_requests WHERE id = ?");
    $stmt->execute([$id]);
    $pickup = $stmt->fetch();
    
    if (!$pickup) {
        header('Location: pickups.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: pickups.php');
    exit();
}

// Get staff list
try {
    $stmt = $pdo->query("SELECT id, name FROM staff ORDER BY name");
    $staff_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $staff_list = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $staff_id = !empty($_POST['staff_id']) ? (int)$_POST['staff_id'] : null;
    $notes = trim($_POST['notes'] ?? '');

    if (empty($status)) {
        $error = 'Status is required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pickup_requests SET status = ?, staff_id = ?, notes = ? WHERE id = ?");
            $stmt->execute([$status, $staff_id, $notes, $id]);
            $success = 'Pickup request updated successfully.';
            
            // Refresh pickup data
            $stmt = $pdo->prepare("SELECT * FROM pickup_requests WHERE id = ?");
            $stmt->execute([$id]);
            $pickup = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Error updating pickup request.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pickup - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">🔧 Admin Panel</a>
            <div class="navbar-nav ms-auto">

                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="pickups.php">Pickups</a>
                <a class="nav-link" href="monthly_report.php">Monthly Report</a>
                <a class="nav-link" href="staff.php">Staff</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Pickup Request</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <!-- Display Pickup Details -->
                        <div class="mb-4">
                            <h5>Pickup Details</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Tracking ID:</th>
                                    <td><code><?php echo htmlspecialchars($pickup['tracking_id']); ?></code></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><?php echo htmlspecialchars($pickup['name']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($pickup['email']); ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?php echo htmlspecialchars($pickup['phone']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td><?php echo nl2br(htmlspecialchars($pickup['address'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Waste Type:</th>
                                    <td><?php echo htmlspecialchars($pickup['waste_type']); ?></td>
                                </tr>
                                <tr>
                                    <th>Pickup Date/Time:</th>
                                    <td><?php echo date('F j, Y g:i A', strtotime($pickup['pickup_datetime'])); ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Edit Form -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Pending" <?php echo $pickup['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Assigned" <?php echo $pickup['status'] === 'Assigned' ? 'selected' : ''; ?>>Assigned</option>
                                    <option value="In-Progress" <?php echo $pickup['status'] === 'In-Progress' ? 'selected' : ''; ?>>In-Progress</option>
                                    <option value="Completed" <?php echo $pickup['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="Cancelled" <?php echo $pickup['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Assign Staff</label>
                                <select class="form-select" id="staff_id" name="staff_id">
                                    <option value="">-- Not Assigned --</option>
                                    <?php foreach ($staff_list as $staff): ?>
                                        <option value="<?php echo $staff['id']; ?>" 
                                                <?php echo $pickup['staff_id'] == $staff['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($staff['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo htmlspecialchars($pickup['notes'] ?? ''); ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Pickup</button>
                                <a href="pickups.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>

