<?php
require_once '../config.php';
requireAdmin();

$success_msg = '';
$error_msg = '';

// Handle Add Ward
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_ward'])) {
    $ward_name = trim($_POST['ward_name']);
    if (!empty($ward_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO wards (name) VALUES (?)");
            $stmt->execute([$ward_name]);
            $success_msg = "Ward added successfully!";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $error_msg = "Ward already exists!";
            } else {
                $error_msg = "Error adding ward: " . $e->getMessage();
            }
        }
    } else {
        $error_msg = "Ward name cannot be empty.";
    }
}

// Handle Delete Ward
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM wards WHERE id = ?");
        $stmt->execute([$id]);
        $success_msg = "Ward deleted successfully!";
    } catch (PDOException $e) {
        $error_msg = "Error deleting ward: " . $e->getMessage();
    }
}

// Fetch Wards
$wards = $pdo->query("SELECT * FROM wards ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Wards - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
                        <a class="nav-link active" href="manage_wards.php">Wards</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pickups.php">Pickups</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="nav-link btn btn-danger btn-sm px-3 text-white" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container admin-dashboard-container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="page-header-title">Manage Wards</h2>
                <p class="page-header-subtitle">Add or remove wards for pickup requests.</p>
            </div>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success modern-alert"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger modern-alert"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- Add Ward Form -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Add New Ward</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="ward_name" class="form-label">Ward Name</label>
                                <input type="text" class="form-control" id="ward_name" name="ward_name" placeholder="e.g. Ward 4 - South Zone" required>
                            </div>
                            <button type="submit" name="add_ward" class="btn btn-primary w-100">Add Ward</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Wards List -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="p-3">Ward Name</th>
                                        <th class="p-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($wards)): ?>
                                        <tr>
                                            <td colspan="2" class="text-center p-4 text-muted">No wards found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($wards as $ward): ?>
                                            <tr>
                                                <td class="p-3 align-middle"><?php echo htmlspecialchars($ward['name']); ?></td>
                                                <td class="p-3 text-end">
                                                    <a href="manage_wards.php?delete=<?php echo $ward['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ward?');">
                                                        <i class="bi bi-trash"></i> Delete
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
