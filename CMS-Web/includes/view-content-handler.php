<?php
/**
 * View Content Handler
 * Handles all business logic for viewing individual content items
 */

require_once 'config/database.php';

class ViewContentHandler {
    private $pdo;
    private $content = null;
    private $relatedContent = [];
    private $contentId;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Process the content view request
     */
    public function processView() {
        $this->contentId = intval($_GET['id'] ?? 0);
        
        if (!$this->contentId) {
            $this->redirectToHome();
        }
        
        $this->loadContent();
        
        if (!$this->content) {
            $this->redirectToHome();
        }
        
        $this->incrementViews();
        $this->loadRelatedContent();
    }
    
    /**
     * Load content details
     */
    private function loadContent() {
        $stmt = $this->pdo->prepare("SELECT * FROM content WHERE id = ? AND status = 'active'");
        $stmt->execute([$this->contentId]);
        $this->content = $stmt->fetch();
    }
    
    /**
     * Increment view count
     */
    private function incrementViews() {
        $stmt = $this->pdo->prepare("UPDATE content SET views = views + 1 WHERE id = ?");
        $stmt->execute([$this->contentId]);
    }
    
    /**
     * Load related content
     */
    private function loadRelatedContent() {
        $stmt = $this->pdo->prepare("SELECT * FROM content WHERE type = ? AND id != ? AND status = 'active' ORDER BY upload_date DESC LIMIT 6");
        $stmt->execute([$this->content['type'], $this->contentId]);
        $this->relatedContent = $stmt->fetchAll();
    }
    
    /**
     * Redirect to home page
     */
    private function redirectToHome() {
        header('Location: sermons-testimonies.php');
        exit;
    }
    
    /**
     * Get content
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Get related content
     */
    public function getRelatedContent() {
        return $this->relatedContent;
    }
    
    /**
     * Get file type icon
     */
    public static function getFileTypeIcon($fileType) {
        switch ($fileType) {
            case 'video':
            case 'mp4':
            case 'avi':
            case 'mov':
                return 'fas fa-video';
            case 'audio':
            case 'mp3':
            case 'wav':
                return 'fas fa-music';
            case 'pdf':
                return 'fas fa-file-pdf';
            case 'text':
            case 'txt':
            case 'doc':
            case 'docx':
                return 'fas fa-file-alt';
            default:
                return 'fas fa-file';
        }
    }
    
    /**
     * Format file size
     */
    public static function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + ' GB';
        } else if ($bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + ' MB';
        } else if ($bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + ' KB';
        } else {
            return $bytes + ' bytes';
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
        return in_array($fileType, ['audio', 'mp3', 'wav']);
    }
    
    /**
     * Get content type display name
     */
    public static function getContentTypeDisplay($type) {
        return ucfirst($type === 'sermon' ? 'Sermons' : 'Testimonies');
    }
    
    /**
     * Get back URL
     */
    public function getBackUrl() {
        $type = $this->content['type'] === 'sermon' ? 'sermons' : 'testimonies';
        return "sermons-testimonies.php?type=$type";
    }
}

// Initialize and process
$handler = new ViewContentHandler();
$handler->processView();

// Make variables available to template
$content = $handler->getContent();
$relatedContent = $handler->getRelatedContent();
?> 