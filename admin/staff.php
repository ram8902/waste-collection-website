<?php
require_once '../config.php';
requireAdmin();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'list';
$edit_id = $_GET['id'] ?? 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($username) || empty($password)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            try {
                // Check if email or username already exists
                $stmt = $pdo->prepare("SELECT id FROM staff WHERE email = ? OR username = ?");
                $stmt->execute([$email, $username]);
                if ($stmt->fetch()) {
                    $error = 'Email or username already exists.';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO staff (name, email, phone, username, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $phone, $username, $hashed_password]);
                    $success = 'Staff member added successfully.';
                    $action = 'list';
                }
            } catch (PDOException $e) {
                $error = 'Error adding staff member.';
            }
        }
    } elseif (isset($_POST['edit_staff'])) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $id = (int)$_POST['id'];

        if (empty($name) || empty($email) || empty($phone) || empty($username)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            try {
                // Check if email or username already exists for other staff
                $stmt = $pdo->prepare("SELECT id FROM staff WHERE (email = ? OR username = ?) AND id != ?");
                $stmt->execute([$email, $username, $id]);
                if ($stmt->fetch()) {
                    $error = 'Email or username already exists.';
                } else {
                    if (!empty($password)) {
                        if (strlen($password) < 6) {
                            $error = 'Password must be at least 6 characters long.';
                        } else {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE staff SET name = ?, email = ?, phone = ?, username = ?, password = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $phone, $username, $hashed_password, $id]);
                            $success = 'Staff member updated successfully.';
                            $action = 'list';
                        }
                    } else {
                        $stmt = $pdo->prepare("UPDATE staff SET name = ?, email = ?, phone = ?, username = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $phone, $username, $id]);
                        $success = 'Staff member updated successfully.';
                        $action = 'list';
                    }
                }
            } catch (PDOException $e) {
                $error = 'Error updating staff member.';
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM staff WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = 'Staff member deleted successfully.';
    } catch (PDOException $e) {
        $error = 'Error deleting staff member.';
    }
}

// Get staff list
try {
    $stmt = $pdo->query("SELECT * FROM staff ORDER BY created_at DESC");
    $staff_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $staff_list = [];
}

// Get staff for editing
$edit_staff = null;
if ($action === 'edit' && $edit_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_staff = $stmt->fetch();
        if (!$edit_staff) {
            $action = 'list';
        }
    } catch (PDOException $e) {
        $action = 'list';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Admin Panel</title>
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
                        <a class="nav-link" href="monthly_report.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="staff.php">Staff</a>
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
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h2 class="page-header-title">Manage Staff</h2>
                <p class="page-header-subtitle mb-0">Add, edit, and manage your waste collection team.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <?php if ($action === 'list'): ?>
                    <a href="staff.php?action=add" class="btn btn-primary btn-filter d-inline-flex w-auto px-4">
                        <i class="bi bi-person-plus-fill"></i> Add New Staff
                    </a>
                <?php else: ?>
                    <a href="staff.php" class="btn btn-secondary btn-filter d-inline-flex w-auto px-4">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Staff List -->
            <div class="table-card">
                <div class="table-card-header">
                    <h5 class="mb-0 fw-bold">Staff Members</h5>
                    <span class="badge bg-primary rounded-pill"><?php echo count($staff_list); ?> Active</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Staff Member</th>
                                <th>Contact Info</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Joined Date</th>
                                <th style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($staff_list)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-3"></i>
                                            No staff members found.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($staff_list as $staff): ?>
                                    <tr>
                                        <td><span class="text-muted">#<?php echo $staff['id']; ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="staff-avatar <?php echo $staff['id'] % 2 == 0 ? 'info' : 'primary'; ?>">
                                                    <?php echo strtoupper(substr($staff['name'], 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?php echo htmlspecialchars($staff['name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <small class="text-muted mb-1"><i class="bi bi-envelope me-1"></i> <?php echo htmlspecialchars($staff['email']); ?></small>
                                                <small class="text-muted"><i class="bi bi-telephone me-1"></i> <?php echo htmlspecialchars($staff['phone']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="font-monospace bg-light px-2 py-1 rounded border">
                                                @<?php echo htmlspecialchars($staff['username']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="role-badge">Staff</span>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                                            <?php echo date('M j, Y', strtotime($staff['created_at'])); ?>
                                        </td>
                                        <td>
                                            <div class="action-btn-group">
                                                <a href="staff.php?action=edit&id=<?php echo $staff['id']; ?>" class="btn btn-primary btn-icon" title="Edit">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.8rem;"></i>
                                                </a>
                                                <a href="staff.php?delete=<?php echo $staff['id']; ?>" 
                                                   class="btn btn-danger btn-icon" 
                                                   title="Delete"
                                                   onclick="return confirm('Are you sure you want to delete this staff member?')">
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
        <?php elseif ($action === 'add' || $action === 'edit'): ?>
            <!-- Add/Edit Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-card">
                        <div class="form-card-header">
                            <h4 class="mb-0 fw-bold">
                                <?php if($action === 'edit'): ?>
                                    <i class="bi bi-pencil-square me-2 text-primary"></i> Edit Staff Member
                                <?php else: ?>
                                    <i class="bi bi-person-plus-fill me-2 text-primary"></i> Add New Staff
                                <?php endif; ?>
                            </h4>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="id" value="<?php echo $edit_staff['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-modern" id="name" name="name" required 
                                               placeholder="e.g. John Doe"
                                               value="<?php echo htmlspecialchars($edit_staff['name'] ?? ($_POST['name'] ?? '')); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-modern" id="username" name="username" required 
                                               placeholder="e.g. johndoe"
                                               value="<?php echo htmlspecialchars($edit_staff['username'] ?? ($_POST['username'] ?? '')); ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control form-control-modern" id="email" name="email" required 
                                               placeholder="e.g. john@example.com"
                                               value="<?php echo htmlspecialchars($edit_staff['email'] ?? ($_POST['email'] ?? '')); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control form-control-modern" id="phone" name="phone" required 
                                               placeholder="e.g. +1 234 567 890"
                                               value="<?php echo htmlspecialchars($edit_staff['phone'] ?? ($_POST['phone'] ?? '')); ?>">
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr class="my-2 border-secondary-subtle">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label">
                                            Password <?php echo $action === 'edit' ? '(leave blank to keep current)' : ''; ?> 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" class="form-control form-control-modern" id="password" name="password" 
                                               <?php echo $action === 'add' ? 'required' : ''; ?> minlength="6">
                                    </div>
                                    <?php if ($action === 'add'): ?>
                                        <div class="col-md-6">
                                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control form-control-modern" id="confirm_password" name="confirm_password" required minlength="6">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="col-12 mt-4 d-flex gap-3">
                                        <button type="submit" name="<?php echo $action === 'edit' ? 'edit_staff' : 'add_staff'; ?>" 
                                                class="btn btn-primary btn-filter px-5">
                                            <?php echo $action === 'edit' ? 'Update Staff' : 'Add Staff'; ?>
                                        </button>
                                        <a href="staff.php" class="btn btn-secondary btn-filter px-4">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>

