<?php
require_once '../config.php';
// requireStaff();
if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];
$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

// Get pickup request
try {
    $stmt = $pdo->prepare("SELECT * FROM pickup_requests WHERE id = ? AND staff_id = ?");
    $stmt->execute([$id, $staff_id]);
    $pickup = $stmt->fetch();
    
    if (!$pickup) {
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';

    if (empty($status) || !in_array($status, ['In-Progress', 'Completed'])) {
        $error = 'Invalid status selected.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pickup_requests SET status = ?, updated_at = NOW() WHERE id = ? AND staff_id = ?");
            $stmt->execute([$status, $id, $staff_id]);
            $success = 'Status updated successfully.';
            
            // Refresh pickup data
            $stmt = $pdo->prepare("SELECT * FROM pickup_requests WHERE id = ? AND staff_id = ?");
            $stmt->execute([$id, $staff_id]);
            $pickup = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Error updating status.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - Staff Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Staff Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">👷 Staff Panel</a>
            <div class="navbar-nav ms-auto">

                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update Pickup Status</h4>
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
                                    <th>Customer Name:</th>
                                    <td><?php echo htmlspecialchars($pickup['name']); ?></td>
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
                                <tr>
                                    <th>Current Status:</th>
                                    <td>
                                        <span class="badge status-<?php echo strtolower(str_replace(' ', '-', $pickup['status'])); ?>">
                                            <?php echo htmlspecialchars($pickup['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($pickup['notes']): ?>
                                    <tr>
                                        <th>Notes:</th>
                                        <td><?php echo nl2br(htmlspecialchars($pickup['notes'])); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <!-- Update Status Form -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="status" class="form-label">Update Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php if ($pickup['status'] === 'Assigned'): ?>
                                        <option value="In-Progress">In-Progress</option>
                                        <option value="Completed">Completed</option>
                                    <?php elseif ($pickup['status'] === 'In-Progress'): ?>
                                        <option value="Completed">Completed</option>
                                    <?php else: ?>
                                        <option value="">-- Select Status --</option>
                                        <option value="In-Progress">In-Progress</option>
                                        <option value="Completed">Completed</option>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">You can mark this pickup as In-Progress or Completed.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
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

