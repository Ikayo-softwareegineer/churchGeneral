<?php
require_once '../config/database.php';

// Handle delete action
function handleDeleteContent($contentId) {
    $pdo = getDBConnection();
    
    // Get file path before deleting
    $stmt = $pdo->prepare("SELECT file_path FROM content WHERE id = ?");
    $stmt->execute([$contentId]);
    $content = $stmt->fetch();
    
    if ($content) {
        // Delete from database
        $deleteStmt = $pdo->prepare("DELETE FROM content WHERE id = ?");
        if ($deleteStmt->execute([$contentId])) {
            // Delete physical file
            if (file_exists($content['file_path'])) {
                unlink($content['file_path']);
            }
            return ['success' => true, 'message' => 'Content deleted successfully!'];
        } else {
            return ['success' => false, 'message' => 'Error deleting content from database'];
        }
    }
    
    return ['success' => false, 'message' => 'Content not found'];
}

// Handle status toggle
function handleToggleStatus($contentId, $newStatus) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("UPDATE content SET status = ? WHERE id = ?");
    if ($stmt->execute([$newStatus, $contentId])) {
        return ['success' => true, 'message' => 'Status updated successfully!'];
    } else {
        return ['success' => false, 'message' => 'Error updating status'];
    }
}

// Get content with pagination and filters
function getContentWithPagination($page = 1, $limit = 10, $search = '', $typeFilter = '') {
    $pdo = getDBConnection();
    
    $whereConditions = [];
    $params = [];
    
    if ($search) {
        $whereConditions[] = "(title LIKE ? OR author LIKE ? OR description LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    if ($typeFilter) {
        $whereConditions[] = "type = ?";
        $params[] = $typeFilter;
    }
    
    $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM content $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch()['total'];
    
    $totalPages = ceil($totalRecords / $limit);
    $offset = ($page - 1) * $limit;
    
    // Get content
    $sql = "SELECT * FROM content $whereClause ORDER BY upload_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $content = $stmt->fetchAll();
    
    return [
        'content' => $content,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalRecords' => $totalRecords
    ];
}

// Process form submissions
function processFormSubmissions() {
    $result = ['success' => false, 'message' => ''];
    
    if (isset($_POST['delete_id'])) {
        $result = handleDeleteContent($_POST['delete_id']);
    } elseif (isset($_POST['toggle_status'])) {
        $contentId = $_POST['content_id'];
        $newStatus = $_POST['new_status'];
        $result = handleToggleStatus($contentId, $newStatus);
    }
    
    return $result;
}

// Get content statistics for admin dashboard
function getAdminContentStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total content
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content");
    $stmt->execute();
    $stats['total'] = $stmt->fetch()['count'];
    
    // Active content
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE status = 'active'");
    $stmt->execute();
    $stats['active'] = $stmt->fetch()['count'];
    
    // Inactive content
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE status = 'inactive'");
    $stmt->execute();
    $stats['inactive'] = $stmt->fetch()['count'];
    
    // Sermons
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'sermon'");
    $stmt->execute();
    $stats['sermons'] = $stmt->fetch()['count'];
    
    // Testimonies
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'testimony'");
    $stmt->execute();
    $stats['testimonies'] = $stmt->fetch()['count'];
    
    // Total views
    $stmt = $pdo->prepare("SELECT SUM(views) as total FROM content");
    $stmt->execute();
    $stats['total_views'] = $stmt->fetch()['total'] ?? 0;
    
    // This month's uploads
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE MONTH(upload_date) = MONTH(CURRENT_DATE()) AND YEAR(upload_date) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $stats['this_month'] = $stmt->fetch()['count'];
    
    return $stats;
}
?> 