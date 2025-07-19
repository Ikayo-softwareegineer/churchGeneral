<?php
/**
 * Sermons & Testimonies Handler
 * Handles all business logic for displaying sermons and testimonies
 */

require_once 'config/database.php';

class SermonsTestimoniesHandler {
    private $pdo;
    private $search = '';
    private $category = '';
    private $type = 'sermons';
    private $page = 1;
    private $limit = 12;
    private $content = [];
    private $categories = [];
    private $stats = [];
    private $totalCount = 0;
    private $totalPages = 0;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->processRequest();
    }
    
    /**
     * Process the request and set up data
     */
    private function processRequest() {
        // Get search and filter parameters
        $this->search = $_GET['search'] ?? '';
        $this->category = $_GET['category'] ?? '';
        $this->type = $_GET['type'] ?? 'sermons';
        $this->page = max(1, intval($_GET['page'] ?? 1));
        $this->offset = ($this->page - 1) * $this->limit;
        
        // Get content statistics
        $this->stats = $this->getContentStats();
        
        // Get content based on type
        $contentType = ($this->type === 'testimonies') ? 'testimony' : 'sermon';
        $this->content = $this->getContentByType($contentType, $this->limit, $this->offset, $this->search, $this->category);
        
        // Get categories for filtering
        $this->categories = $this->fetchCategoriesByType($contentType);
        
        // Get total count for pagination
        $this->calculatePagination($contentType);
    }
    
    /**
     * Get content statistics
     */
    private function getContentStats() {
        $stats = [];
        
        // Total sermons
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'sermon' AND status = 'active'");
        $stmt->execute();
        $stats['sermons'] = $stmt->fetch()['count'];
        
        // Total testimonies
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM content WHERE type = 'testimony' AND status = 'active'");
        $stmt->execute();
        $stats['testimonies'] = $stmt->fetch()['count'];
        
        // This month's content
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM content WHERE status = 'active' AND upload_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
        $stmt->execute();
        $stats['this_month'] = $stmt->fetch()['count'];
        
        // Total views
        $stmt = $this->pdo->prepare("SELECT SUM(views) as total FROM content WHERE status = 'active'");
        $stmt->execute();
        $stats['total_views'] = $stmt->fetch()['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Get content by type with filters
     */
    private function getContentByType($type, $limit, $offset, $search = '', $category = '') {
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
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get categories for filtering
     */
    private function fetchCategoriesByType($type) {
        $stmt = $this->pdo->prepare("SELECT DISTINCT category FROM content WHERE type = ? AND status = 'active' AND category IS NOT NULL AND category != '' ORDER BY category");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    /**
     * Calculate pagination
     */
    private function calculatePagination($contentType) {
        $whereConditions = ["type = ?", "status = 'active'"];
        $params = [$contentType];
        
        if (!empty($this->search)) {
            $whereConditions[] = "(title LIKE ? OR author LIKE ? OR description LIKE ?)";
            $searchTerm = "%$this->search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($this->category)) {
            $whereConditions[] = "category = ?";
            $params[] = $this->category;
        }
        
        $whereClause = implode(" AND ", $whereConditions);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM content WHERE $whereClause");
        $stmt->execute($params);
        $this->totalCount = $stmt->fetch()['count'];
        $this->totalPages = ceil($this->totalCount / $this->limit);
    }
    
    /**
     * Get file type icon
     */
    public static function getFileTypeIcon($fileType) {
        $icons = [
            'video' => 'fas fa-video',
            'mp4' => 'fas fa-video',
            'avi' => 'fas fa-video',
            'mov' => 'fas fa-video',
            'webm' => 'fas fa-video',
            'audio' => 'fas fa-music',
            'mp3' => 'fas fa-music',
            'wav' => 'fas fa-music',
            'ogg' => 'fas fa-music',
            'aac' => 'fas fa-music',
            'm4a' => 'fas fa-music',
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'default' => 'fas fa-file'
        ];
        
        return $icons[$fileType] ?? $icons['default'];
    }
    
    /**
     * Format file size
     */
    public static function formatFileSize($bytes) {
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
    
    /**
     * Check if content is video
     */
    public static function isVideo($fileType) {
        return in_array($fileType, ['video', 'mp4', 'avi', 'mov']);
    }
    
    /**
     * Check if content is audio
     */
    public static function isAudio($fileType) {
        return in_array($fileType, ['audio', 'mp3', 'wav', 'ogg', 'aac', 'm4a']);
    }
    
    /**
     * Get content action text
     */
    public static function getActionText($fileType) {
        if (self::isVideo($fileType)) {
            return 'Watch Now';
        } elseif (self::isAudio($fileType)) {
            return 'Listen Now';
        } else {
            return 'View';
        }
    }
    
    /**
     * Get search URL parameters
     */
    public function getSearchUrlParams() {
        $params = [];
        if (!empty($this->search)) {
            $params[] = 'search=' . urlencode($this->search);
        }
        if (!empty($this->category)) {
            $params[] = 'category=' . urlencode($this->category);
        }
        return implode('&', $params);
    }
    
    /**
     * Get pagination URL parameters
     */
    public function getPaginationUrlParams($page) {
        $params = ['type=' . $this->type, 'page=' . $page];
        if (!empty($this->search)) {
            $params[] = 'search=' . urlencode($this->search);
        }
        if (!empty($this->category)) {
            $params[] = 'category=' . urlencode($this->category);
        }
        return implode('&', $params);
    }
    
    // Getters
    public function getSearch() { return $this->search; }
    public function getCategory() { return $this->category; }
    public function getType() { return $this->type; }
    public function getPage() { return $this->page; }
    public function getContent() { return $this->content; }
    public function getCategories() { return $this->categories; }
    public function getStats() { return $this->stats; }
    public function getTotalCount() { return $this->totalCount; }
    public function getTotalPages() { return $this->totalPages; }
}

// Initialize and process
$handler = new SermonsTestimoniesHandler();

// Add AJAX endpoints for like and download
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'like' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('UPDATE content SET likes = likes + 1 WHERE id = ?');
        $stmt->execute([$id]);
        $stmt = $pdo->prepare('SELECT likes FROM content WHERE id = ?');
        $stmt->execute([$id]);
        $likes = $stmt->fetchColumn();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'likes' => $likes]);
        exit;
    }
    if ($_GET['action'] === 'download' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT file_path, title FROM content WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && file_exists($row['file_path'])) {
            // Increment download count
            $stmt = $pdo->prepare('UPDATE content SET downloads = downloads + 1 WHERE id = ?');
            $stmt->execute([$id]);
            // Serve file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($row['file_path']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($row['file_path']));
            readfile($row['file_path']);
            exit;
        } else {
            http_response_code(404);
            echo 'File not found.';
            exit;
        }
    }
}

// Make variables available to template
$search = $handler->getSearch();
$category = $handler->getCategory();
$type = $handler->getType();
$page = $handler->getPage();
$content = $handler->getContent();
$categories = $handler->getCategories();
$stats = $handler->getStats();
$totalCount = $handler->getTotalCount();
$totalPages = $handler->getTotalPages();
?> 