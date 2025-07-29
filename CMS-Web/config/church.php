<?php
// config/database.php - Database Configuration for Church Management System

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'church');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() {}
}

// Database helper functions
function getDbConnection() {
    return Database::getInstance()->getConnection();
}

// Create tables if they don't exist
function initializeDatabase() {
    $pdo = getDbConnection();
    
    // Create sermons table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sermons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            speaker VARCHAR(100) NOT NULL,
            series VARCHAR(100) DEFAULT NULL,
            description TEXT,
            audio_url VARCHAR(255),
            video_url VARCHAR(255) DEFAULT NULL,
            thumbnail VARCHAR(255),
            duration VARCHAR(10),
            tags JSON,
            views INT DEFAULT 0,
            likes INT DEFAULT 0,
            date_preached DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status ENUM('draft', 'published', 'archived') DEFAULT 'published',
            INDEX idx_date_preached (date_preached),
            INDEX idx_speaker (speaker),
            INDEX idx_series (series),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Create testimonies table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS testimonies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(100) NOT NULL,
            email VARCHAR(255) DEFAULT NULL,
            description TEXT,
            audio_url VARCHAR(255) DEFAULT NULL,
            video_url VARCHAR(255) DEFAULT NULL,
            thumbnail VARCHAR(255),
            duration VARCHAR(10) DEFAULT NULL,
            tags JSON,
            views INT DEFAULT 0,
            likes INT DEFAULT 0,
            date_shared DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            featured BOOLEAN DEFAULT FALSE,
            INDEX idx_date_shared (date_shared),
            INDEX idx_author (author),
            INDEX idx_status (status),
            INDEX idx_featured (featured)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Create favorites table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            content_type ENUM('sermon', 'testimony') NOT NULL,
            content_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorite (user_id, content_type, content_id),
            INDEX idx_user_id (user_id),
            INDEX idx_content (content_type, content_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Create plays table for tracking
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS plays (
            id INT AUTO_INCREMENT PRIMARY KEY,
            content_type ENUM('sermon', 'testimony') NOT NULL,
            content_id INT NOT NULL,
            user_id INT DEFAULT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_content (content_type, content_id),
            INDEX idx_user_id (user_id),
            INDEX idx_played_at (played_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Create series table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS series (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            thumbnail VARCHAR(255),
            start_date DATE,
            end_date DATE DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('active', 'completed', 'archived') DEFAULT 'active',
            UNIQUE KEY unique_name (name),
            INDEX idx_status (status),
            INDEX idx_start_date (start_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    
    // Create tags table for better tag management
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            color VARCHAR(7) DEFAULT '#667eea',
            usage_count INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_name (name),
            INDEX idx_usage_count (usage_count)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
}

// Sermon-related database functions
function getAllSermons($limit = null, $offset = 0) {
    $pdo = getDbConnection();
    $sql = "SELECT * FROM sermons WHERE status = 'published' ORDER BY date_preached DESC";
    
    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($limit) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

function getSermonById($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM sermons WHERE id = :id AND status = 'published'");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

function searchSermons($query) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM sermons 
        WHERE status = 'published' 
        AND (title LIKE :query OR description LIKE :query OR speaker LIKE :query OR JSON_SEARCH(tags, 'one', :tag_query) IS NOT NULL)
        ORDER BY date_preached DESC
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->bindValue(':query', $searchTerm);
    $stmt->bindValue(':tag_query', $query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getSermonsBySeries($series) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM sermons WHERE series = :series AND status = 'published' ORDER BY date_preached ASC");
    $stmt->bindValue(':series', $series);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getMostViewedSermons($limit = 10) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM sermons WHERE status = 'published' ORDER BY views DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Testimony-related database functions
function getAllTestimonies($limit = null, $offset = 0) {
    $pdo = getDbConnection();
    $sql = "SELECT * FROM testimonies WHERE status = 'approved' ORDER BY date_shared DESC";
    
    if ($limit) {
        $sql .= " LIMIT :limit OFFSET :offset";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($limit) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

function getTestimonyById($id) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM testimonies WHERE id = :id AND status = 'approved'");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch();
}

function searchTestimonies($query) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM testimonies 
        WHERE status = 'approved' 
        AND (title LIKE :query OR description LIKE :query OR author LIKE :query OR JSON_SEARCH(tags, 'one', :tag_query) IS NOT NULL)
        ORDER BY date_shared DESC
    ");
    
    $searchTerm = '%' . $query . '%';
    $stmt->bindValue(':query', $searchTerm);
    $stmt->bindValue(':tag_query', $query);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getFeaturedTestimonies($limit = 5) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM testimonies WHERE status = 'approved' AND featured = TRUE ORDER BY date_shared DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Play tracking functions
function recordPlay($contentType, $contentId, $userId = null) {
    $pdo = getDbConnection();
    
    // Record the play
    $stmt = $pdo->prepare("
        INSERT INTO plays (content_type, content_id, user_id, ip_address, user_agent) 
        VALUES (:content_type, :content_id, :user_id, :ip_address, :user_agent)
    ");
    
    $stmt->execute([
        ':content_type' => $contentType,
        ':content_id' => $contentId,
        ':user_id' => $userId,
        ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    
    // Update view count
    $table = $contentType === 'sermon' ? 'sermons' : 'testimonies';
    $stmt = $pdo->prepare("UPDATE {$table} SET views = views + 1 WHERE id = :id");
    $stmt->execute([':id' => $contentId]);
    
    return true;
}

// Favorite functions
if (!function_exists('addToFavorites')) {
    function addToFavorites() {
        // Your existing function code goes here
    }
}


function removeFromFavorites($userId, $contentType, $contentId) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        DELETE FROM favorites 
        WHERE user_id = :user_id AND content_type = :content_type AND content_id = :content_id
    ");
    
    $stmt->execute([
        ':user_id' => $userId,
        ':content_type' => $contentType,
        ':content_id' => $contentId
    ]);
    
    return $stmt->rowCount() > 0;
}



// Analytics functions
function getContentStats($days = 30) {
    $pdo = getDbConnection();
    
    $stmt = $pdo->prepare("
        SELECT 
            content_type,
            COUNT(*) as play_count,
            COUNT(DISTINCT content_id) as unique_content,
            DATE(played_at) as play_date
        FROM plays 
        WHERE played_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        GROUP BY content_type, DATE(played_at)
        ORDER BY play_date DESC
    ");
    
    $stmt->execute([':days' => $days]);
    return $stmt->fetchAll();
}

function getPopularContent($contentType, $limit = 10, $days = 30) {
    $pdo = getDbConnection();
    
    $table = $contentType === 'sermon' ? 'sermons' : 'testimonies';
    
    $stmt = $pdo->prepare("
        SELECT c.*, COUNT(p.id) as recent_plays
        FROM {$table} c
        LEFT JOIN plays p ON p.content_type = :content_type 
            AND p.content_id = c.id 
            AND p.played_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        WHERE c.status = :status
        GROUP BY c.id
        ORDER BY recent_plays DESC, c.views DESC
        LIMIT :limit
    ");
    
    $status = $contentType === 'sermon' ? 'published' : 'approved';
    
    $stmt->execute([
        ':content_type' => $contentType,
        ':days' => $days,
        ':status' => $status,
        ':limit' => $limit
    ]);
    
    return $stmt->fetchAll();
}

// Initialize database tables
try {
    initializeDatabase();
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
}
?>