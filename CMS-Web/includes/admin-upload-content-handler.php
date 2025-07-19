<?php
/**
 * Admin Upload Content Handler
 * Handles all business logic for content upload and management
 */

require_once 'config/database.php';

class AdminUploadContentHandler {
    private $pdo;
    private $message = '';
    private $error = '';
    private $existingContent = [];
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Process the form submission
     */
    public function processForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->validateInput();
                $this->handleFileUpload();
                $this->saveToDatabase();
                $this->message = 'Content uploaded successfully! File stored in: ' . $this->uploadDir;
            } catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }
        
        $this->loadExistingContent();
    }
    
    /**
     * Validate form input
     */
    private function validateInput() {
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $_POST['type'] ?? '';
        $category = trim($_POST['category'] ?? '');
        
        if (empty($title) || empty($author) || empty($description) || empty($type)) {
            throw new Exception('All required fields must be filled.');
        }
        
        // Store validated data
        $this->formData = [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'type' => $type,
            'category' => $category
        ];
    }
    
    /**
     * Handle file upload
     */
    private function handleFileUpload() {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Please select a file to upload.');
        }
        
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];
        
        // Validate file
        $this->validateFile($fileName, $fileSize);
        
        // Determine file type and upload directory
        $this->determineFileType($fileName);
        
        // Always enforce videos in uploads/videos/
        if ($this->fileType === 'video') {
            $this->uploadDir = 'uploads/videos/';
        }
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Generate unique filename and move file
        $uniqueFileName = uniqid() . '_' . $fileName;
        $this->filePath = $this->uploadDir . $uniqueFileName;
        
        if (!move_uploaded_file($fileTmpName, $this->filePath)) {
            throw new Exception('Failed to upload file.');
        }
        
        $this->fileSize = $fileSize;
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($fileName, $fileSize) {
        $allowedTypes = ['mp4', 'avi', 'mov', 'mp3', 'wav', 'pdf', 'txt', 'doc', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedTypes)) {
            throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
        }
        
        if ($fileSize > 100 * 1024 * 1024) { // 100MB limit
            throw new Exception('File size too large. Maximum size is 100MB.');
        }
    }
    
    /**
     * Determine file type and upload directory
     */
    private function determineFileType($fileName) {
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (in_array($fileExtension, ['mp4', 'avi', 'mov'])) {
            $this->fileType = 'video';
            $this->uploadDir = 'uploads/videos/';
        } elseif (in_array($fileExtension, ['mp3', 'wav'])) {
            $this->fileType = 'audio';
            $this->uploadDir = 'uploads/audio/';
        } elseif (in_array($fileExtension, ['pdf'])) {
            $this->fileType = 'pdf';
            $this->uploadDir = 'uploads/documents/';
        } elseif (in_array($fileExtension, ['txt', 'doc', 'docx'])) {
            $this->fileType = 'text';
            $this->uploadDir = 'uploads/documents/';
        }
    }
    
    /**
     * Save content to database
     */
    private function saveToDatabase() {
        $stmt = $this->pdo->prepare("
            INSERT INTO content (type, title, author, description, category, file_path, file_type, file_size, upload_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $this->formData['type'],
            $this->formData['title'],
            $this->formData['author'],
            $this->formData['description'],
            $this->formData['category'],
            $this->filePath,
            $this->fileType,
            $this->fileSize
        ]);
    }
    
    /**
     * Load existing content for display
     */
    private function loadExistingContent() {
        $stmt = $this->pdo->prepare("SELECT * FROM content ORDER BY upload_date DESC LIMIT 20");
        $stmt->execute();
        $this->existingContent = $stmt->fetchAll();
    }
    
    /**
     * Get message
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * Get error
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * Get existing content
     */
    public function getExistingContent() {
        return $this->existingContent;
    }
    
    /**
     * Get file type icon
     */
    public static function getFileTypeIcon($fileType) {
        switch ($fileType) {
            case 'video':
                return '<i class="fas fa-video"></i>';
            case 'audio':
                return '<i class="fas fa-music"></i>';
            case 'pdf':
                return '<i class="fas fa-file-pdf"></i>';
            case 'text':
                return '<i class="fas fa-file-alt"></i>';
            default:
                return '<i class="fas fa-file"></i>';
        }
    }
    
    /**
     * Delete content by ID
     */
    public function deleteContent($contentId) {
        try {
            // Get file path before deletion
            $stmt = $this->pdo->prepare("SELECT file_path FROM content WHERE id = ?");
            $stmt->execute([$contentId]);
            $content = $stmt->fetch();
            
            if ($content && file_exists($content['file_path'])) {
                unlink($content['file_path']);
            }
            
            // Delete from database
            $stmt = $this->pdo->prepare("DELETE FROM content WHERE id = ?");
            $stmt->execute([$contentId]);
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get content by ID
     */
    public function getContentById($contentId) {
        $stmt = $this->pdo->prepare("SELECT * FROM content WHERE id = ?");
        $stmt->execute([$contentId]);
        return $stmt->fetch();
    }
    
    /**
     * Update content views
     */
    public function incrementViews($contentId) {
        $stmt = $this->pdo->prepare("UPDATE content SET views = views + 1 WHERE id = ?");
        $stmt->execute([$contentId]);
    }
    
    /**
     * Get content statistics
     */
    public function getContentStats() {
        $stats = [];
        
        // Total content count
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM content");
        $stmt->execute();
        $stats['total'] = $stmt->fetch()['total'];
        
        // Content by type
        $stmt = $this->pdo->prepare("SELECT type, COUNT(*) as count FROM content GROUP BY type");
        $stmt->execute();
        $stats['by_type'] = $stmt->fetchAll();
        
        // Total views
        $stmt = $this->pdo->prepare("SELECT SUM(views) as total_views FROM content");
        $stmt->execute();
        $stats['total_views'] = $stmt->fetch()['total_views'] ?? 0;
        
        return $stats;
    }
}

// Initialize and process
$handler = new AdminUploadContentHandler();
$handler->processForm();

// Make variables available to template
$message = $handler->getMessage();
$error = $handler->getError();
$existingContent = $handler->getExistingContent();

// --- SCRIPT: Fix existing video file paths in the database ---
if (isset($_GET['fix_video_paths']) && $_GET['fix_video_paths'] === '1') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, file_path FROM content WHERE (file_type = 'video' OR file_path LIKE '%.mp4' OR file_path LIKE '%.avi' OR file_path LIKE '%.mov') AND file_path NOT LIKE 'uploads/videos/%'");
    $stmt->execute();
    $videos = $stmt->fetchAll();
    $moved = 0;
    $updated = 0;
    foreach ($videos as $video) {
        $oldPath = $video['file_path'];
        $fileName = basename($oldPath);
        $newPath = 'uploads/videos/' . $fileName;
        if (file_exists($oldPath)) {
            if (!is_dir('uploads/videos/')) {
                mkdir('uploads/videos/', 0755, true);
            }
            if (rename($oldPath, $newPath)) {
                $moved++;
                $updateStmt = $pdo->prepare("UPDATE content SET file_path = ? WHERE id = ?");
                $updateStmt->execute([$newPath, $video['id']]);
                $updated++;
            }
        }
    }
    echo "<pre>Moved $moved files and updated $updated database entries. Done.</pre>";
    exit;
}
?> 