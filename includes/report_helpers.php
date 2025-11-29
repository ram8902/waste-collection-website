<?php
/**
 * Report Helper Functions
 * Reusable functions for generating monthly reports and history
 */

/**
 * Get monthly summary statistics
 * @param PDO $pdo Database connection
 * @param int $month Month (1-12)
 * @param int $year Year (e.g., 2024)
 * @return array Summary statistics
 */
function getMonthlySummary($pdo, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date)); // Last day of month
    
    $summary = [
        'total_pickups' => 0,
        'completed' => 0,
        'pending' => 0,
        'cancelled' => 0,
        'unique_users' => 0
    ];
    
    try {
        // Total pickups
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        $summary['total_pickups'] = $stmt->fetch()['count'];
        
        // Completed
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'Completed'");
        $stmt->execute([$start_date, $end_date]);
        $summary['completed'] = $stmt->fetch()['count'];
        
        // Pending
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'Pending'");
        $stmt->execute([$start_date, $end_date]);
        $summary['pending'] = $stmt->fetch()['count'];
        
        // Cancelled
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'Cancelled'");
        $stmt->execute([$start_date, $end_date]);
        $summary['cancelled'] = $stmt->fetch()['count'];
        
        // Unique users
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        $summary['unique_users'] = $stmt->fetch()['count'];
        
    } catch (PDOException $e) {
        error_log("Error in getMonthlySummary: " . $e->getMessage());
    }
    
    return $summary;
}

/**
 * Get monthly status breakdown
 * @param PDO $pdo Database connection
 * @param int $month Month (1-12)
 * @param int $year Year
 * @return array Status breakdown
 */
function getMonthlyStatusBreakdown($pdo, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $breakdown = [];
    
    try {
        $stmt = $pdo->prepare("SELECT status, COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY status ORDER BY count DESC");
        $stmt->execute([$start_date, $end_date]);
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $breakdown[$row['status']] = $row['count'];
        }
    } catch (PDOException $e) {
        error_log("Error in getMonthlyStatusBreakdown: " . $e->getMessage());
    }
    
    return $breakdown;
}

/**
 * Get monthly waste type breakdown
 * @param PDO $pdo Database connection
 * @param int $month Month (1-12)
 * @param int $year Year
 * @return array Waste type breakdown
 */
function getMonthlyWasteTypeBreakdown($pdo, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $breakdown = [];
    
    try {
        $stmt = $pdo->prepare("SELECT waste_type, COUNT(*) as count FROM pickup_requests WHERE DATE(created_at) BETWEEN ? AND ? GROUP BY waste_type ORDER BY count DESC");
        $stmt->execute([$start_date, $end_date]);
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            $breakdown[$row['waste_type']] = $row['count'];
        }
    } catch (PDOException $e) {
        error_log("Error in getMonthlyWasteTypeBreakdown: " . $e->getMessage());
    }
    
    return $breakdown;
}

/**
 * Get monthly staff breakdown
 * @param PDO $pdo Database connection
 * @param int $month Month (1-12)
 * @param int $year Year
 * @return array Staff breakdown
 */
function getMonthlyStaffBreakdown($pdo, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $breakdown = [];
    
    try {
        $stmt = $pdo->prepare("
            SELECT s.name as staff_name, COUNT(pr.id) as count 
            FROM pickup_requests pr 
            LEFT JOIN staff s ON pr.staff_id = s.id 
            WHERE DATE(pr.created_at) BETWEEN ? AND ? AND pr.status = 'Completed'
            GROUP BY s.id, s.name 
            ORDER BY count DESC
        ");
        $stmt->execute([$start_date, $end_date]);
        $results = $stmt->fetchAll();
        
        foreach ($results as $row) {
            if ($row['staff_name']) {
                $breakdown[$row['staff_name']] = $row['count'];
            }
        }
    } catch (PDOException $e) {
        error_log("Error in getMonthlyStaffBreakdown: " . $e->getMessage());
    }
    
    return $breakdown;
}

/**
 * Get monthly pickup requests for admin
 * @param PDO $pdo Database connection
 * @param int $month Month (1-12)
 * @param int $year Year
 * @param string $status_filter Optional status filter
 * @param string $waste_type_filter Optional waste type filter
 * @return array Pickup requests
 */
function getMonthlyPickups($pdo, $month, $year, $status_filter = '', $waste_type_filter = '') {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $where = ["DATE(pr.created_at) BETWEEN ? AND ?"];
    $params = [$start_date, $end_date];
    
    if (!empty($status_filter)) {
        $where[] = "pr.status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($waste_type_filter)) {
        $where[] = "pr.waste_type = ?";
        $params[] = $waste_type_filter;
    }
    
    $where_sql = implode(' AND ', $where);
    
    try {
        $sql = "SELECT pr.*, u.name as user_name, s.name as staff_name 
                FROM pickup_requests pr 
                LEFT JOIN users u ON pr.user_id = u.id 
                LEFT JOIN staff s ON pr.staff_id = s.id 
                WHERE $where_sql
                ORDER BY pr.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getMonthlyPickups: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user monthly history
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @param int $month Month (1-12)
 * @param int $year Year
 * @return array User pickup history
 */
function getUserMonthlyHistory($pdo, $user_id, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    try {
        $stmt = $pdo->prepare("
            SELECT pr.*, s.name as staff_name 
            FROM pickup_requests pr 
            LEFT JOIN staff s ON pr.staff_id = s.id 
            WHERE pr.user_id = ? AND DATE(pr.created_at) BETWEEN ? AND ?
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute([$user_id, $start_date, $end_date]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error in getUserMonthlyHistory: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user monthly summary
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @param int $month Month (1-12)
 * @param int $year Year
 * @return array Summary statistics
 */
function getUserMonthlySummary($pdo, $user_id, $month, $year) {
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    
    $summary = [
        'total' => 0,
        'completed' => 0,
        'pending' => 0
    ];
    
    try {
        // Total
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ?");
        $stmt->execute([$user_id, $start_date, $end_date]);
        $summary['total'] = $stmt->fetch()['count'];
        
        // Completed
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ? AND status = 'Completed'");
        $stmt->execute([$user_id, $start_date, $end_date]);
        $summary['completed'] = $stmt->fetch()['count'];
        
        // Pending
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM pickup_requests WHERE user_id = ? AND DATE(created_at) BETWEEN ? AND ? AND status = 'Pending'");
        $stmt->execute([$user_id, $start_date, $end_date]);
        $summary['pending'] = $stmt->fetch()['count'];
        
    } catch (PDOException $e) {
        error_log("Error in getUserMonthlySummary: " . $e->getMessage());
    }
    
    return $summary;
}

?>

