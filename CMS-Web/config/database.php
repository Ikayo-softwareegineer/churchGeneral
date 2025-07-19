<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'church_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Helper function to get content statistics
function getContentStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Get sermon count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'sermon' AND status = 'active'");
    $stmt->execute();
    $stats['sermons'] = $stmt->fetch()['count'];
    
    // Get testimony count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'testimony' AND status = 'active'");
    $stmt->execute();
    $stats['testimonies'] = $stmt->fetch()['count'];
    
    // Get this month's count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM content WHERE MONTH(upload_date) = MONTH(CURRENT_DATE()) AND YEAR(upload_date) = YEAR(CURRENT_DATE()) AND status = 'active'");
    $stmt->execute();
    $stats['this_month'] = $stmt->fetch()['count'];
    
    // Get total views
    $stmt = $pdo->prepare("SELECT SUM(views) as total FROM content WHERE status = 'active'");
    $stmt->execute();
    $stats['total_views'] = $stmt->fetch()['total'] ?? 0;
    
    return $stats;
}

// Helper function to get content by type
function getContentByType($type, $limit = 12, $offset = 0, $search = '', $category = '') {
    $pdo = getDBConnection();
    
    $whereConditions = ["type = ?", "status = 'active'"];
    $params = [$type];
    
    if (!empty($search)) {
        $whereConditions[] = "(title LIKE ? OR author LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($category)) {
        $whereConditions[] = "category = ?";
        $params[] = $category;
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    
    $sql = "SELECT * FROM content WHERE $whereClause ORDER BY upload_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

// Helper function to get categories
function getCategories($type) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT DISTINCT category FROM content WHERE type = ? AND status = 'active' AND category IS NOT NULL AND category != ''");
    $stmt->execute([$type]);
    
    return $stmt->fetchAll();
}

// Helper function to increment views
function incrementViews($contentId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("UPDATE content SET views = views + 1 WHERE id = ?");
    $stmt->execute([$contentId]);
}

// Helper function to get file type icon
function getFileTypeIcon($fileType) {
    switch (strtolower($fileType)) {
        case 'video':
        case 'mp4':
        case 'avi':
        case 'mov':
            return 'fas fa-play';
        case 'audio':
        case 'mp3':
        case 'wav':
            return 'fas fa-volume-up';
        case 'pdf':
            return 'fas fa-file-pdf';
        case 'text':
        case 'txt':
            return 'fas fa-file-alt';
        default:
            return 'fas fa-file';
    }
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?> 