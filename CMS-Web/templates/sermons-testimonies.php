<?php
// Prevent undefined variable notices
$stats = isset($stats) ? $stats : ['sermons' => 0, 'testimonies' => 0, 'this_month' => 0, 'total_views' => 0];
$search = isset($search) ? $search : '';
$category = isset($category) ? $category : '';
$type = isset($type) ? $type : 'sermons';
$categories = isset($categories) ? $categories : [];
$content = isset($content) ? $content : [];
$totalPages = isset($totalPages) ? $totalPages : 1;
$page = isset($page) ? $page : 1;
$handler = isset($handler) ? $handler : null;
// Make sure SermonsTestimoniesHandler is included or autoloaded before using its static methods
// require_once 'path/to/SermonsTestimoniesHandler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church CMS - Sermons & Testimonies</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/sermons-testimonies.css">
</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Church CMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-calendar"></i> Programs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-users"></i> Departments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-calendar-check"></i> Book Appointment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-hand-holding-heart"></i> Offertory</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h1 class="page-title">Sermons & Testimonies</h1>

        <!-- Bootstrap Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['sermons']; ?></div>
                    <div class="stat-label">Total Sermons</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['testimonies']; ?></div>
                    <div class="stat-label">Testimonies</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['this_month']; ?></div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                    <div class="stat-label">Total Views</div>
                </div>
            </div>
        </div>

        <!-- Custom Tabs -->
        <div class="custom-tabs">
            <a href="?type=sermons<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
               class="custom-tab-btn <?php echo $type === 'sermons' ? 'active' : ''; ?>">
                <i class="fas fa-church"></i> Sermons
            </a>
            <a href="?type=testimonies<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?>" 
               class="custom-tab-btn <?php echo $type === 'testimonies' ? 'active' : ''; ?>">
                <i class="fas fa-heart"></i> Testimonies
            </a>
        </div>

        <!-- Content Section -->
        <div class="tab-content active">
            <form method="GET" class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="search-input" placeholder="Search <?php echo $type; ?> by title, author, or description..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <button type="submit" class="search-btn">Search</button>
            </form>

            <div class="filter-tabs">
                <a href="?type=<?php echo $type; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="filter-btn <?php echo empty($category) ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?type=<?php echo $type; ?>&category=<?php echo urlencode($cat['category']); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                       class="filter-btn <?php echo $category === $cat['category'] ? 'active' : ''; ?>">
                        <?php echo ucfirst(htmlspecialchars($cat['category'])); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($content)): ?>
                <div class="no-results">
                    <i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>No <?php echo $type; ?> found matching your search criteria.</p>
                    <a href="?type=<?php echo $type; ?>" class="btn btn-custom-primary">View All <?php echo ucfirst($type); ?></a>
                </div>
            <?php else: ?>
                <!-- Bootstrap Grid for Content -->
                <div class="row">
                    <?php foreach ($content as $item): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="custom-card">
                                <div class="card-header">
                                    <div class="card-icon <?php echo $item['type'] === 'sermon' ? 'sermon-icon' : 'testimony-icon'; ?>">
                                        <i class="<?php echo SermonsTestimoniesHandler::getFileTypeIcon($item['file_type']); ?>"></i>
                                    </div>
                                    <div>
                                        <div class="card-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                        <div class="card-date"><?php echo htmlspecialchars($item['author']); ?> • <?php echo date('F j, Y', strtotime($item['upload_date'])); ?></div>
                                    </div>
                                </div>
                                
                                <?php if (SermonsTestimoniesHandler::isVideo($item['file_type'])): ?>
                                    <div class="media-container">
                                        <video class="media-preview" controls preload="metadata">
                                            <source src="<?php echo htmlspecialchars($item['file_path']); ?>" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                        <div class="media-overlay">
                                            <button class="btn btn-play-overlay" onclick="playMedia(this)">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php elseif (SermonsTestimoniesHandler::isAudio($item['file_type'])): ?>
                                    <div class="media-container">
                                        <audio class="media-preview" controls preload="metadata">
                                            <source src="<?php echo htmlspecialchars($item['file_path']); ?>" type="audio/mpeg">
                                            Your browser does not support the audio tag.
                                        </audio>
                                        <div class="media-overlay">
                                            <button class="btn btn-play-overlay" onclick="playMedia(this)">
                                                <i class="fas fa-volume-up"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($item['type'] === 'testimony' && !empty($item['category'])): ?>
                                    <div class="testimony-category"><?php echo ucfirst(htmlspecialchars($item['category'])); ?></div>
                                <?php endif; ?>
                                
                                <div class="card-description">
                                    <?php echo htmlspecialchars(substr($item['description'], 0, 150)) . (strlen($item['description']) > 150 ? '...' : ''); ?>
                                </div>
                                
                                <div class="d-flex gap-2 mt-3">
                                    <?php if (SermonsTestimoniesHandler::isVideo($item['file_type']) || SermonsTestimoniesHandler::isAudio($item['file_type'])): ?>
                                        <button class="btn btn-watch-now" onclick="playMediaInCard(this, '<?php echo htmlspecialchars($item['file_path']); ?>', '<?php echo $item['file_type']; ?>', '<?php echo htmlspecialchars($item['title']); ?>')">
                                            <i class="fas fa-<?php echo SermonsTestimoniesHandler::isVideo($item['file_type']) ? 'play' : 'volume-up'; ?>"></i> 
                                            <?php echo SermonsTestimoniesHandler::isVideo($item['file_type']) ? 'Watch Now' : 'Listen Now'; ?>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-like" onclick="likeVideo(<?php echo $item['id']; ?>, this)">
                                        <i class="fas fa-heart"></i> <span class="like-count"><?php echo isset($item['likes']) ? $item['likes'] : 0; ?></span>
                                    </button>
                                    <a href="includes/sermons-testimonies-handler.php?action=download&id=<?php echo $item['id']; ?>" class="btn btn-custom-secondary download-btn">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-eye"></i> <?php echo number_format($item['views']); ?> views
                                    <?php if ($item['file_size']): ?>
                                        • <i class="fas fa-file"></i> <?php echo SermonsTestimoniesHandler::formatFileSize($item['file_size']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-container">
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo $handler->getPaginationUrlParams($page - 1); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo $handler->getPaginationUrlParams($i); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo $handler->getPaginationUrlParams($page + 1); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="preview-modal">
        <div class="preview-modal-content">
            <div class="preview-header">
                <h3 class="preview-title" id="previewTitle"></h3>
                <button class="preview-close" onclick="closePreview()">&times;</button>
            </div>
            <div class="preview-body">
                <!-- Media Player Section -->
                <div class="preview-media-section" id="previewMediaSection" style="display: none;">
                    <div class="preview-media-info">
                        <div class="preview-media-type">
                            <i id="previewMediaIcon"></i>
                            <span id="previewMediaType"></span>
                        </div>
                        <div class="preview-media-duration" id="previewMediaDuration"></div>
                    </div>
                    <div class="preview-media-player" id="previewMediaPlayer">
                        <div class="preview-media-loading" id="previewMediaLoading">
                            <i class="fas fa-spinner"></i>
                            <span>Loading media...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Thumbnail Section (for non-media files) -->
                <div class="preview-thumbnail" id="previewThumbnail">
                    <i id="previewIcon"></i>
                </div>
                
                <div class="preview-info">
                    <div class="preview-info-item">
                        <i class="fas fa-user"></i>
                        <span id="previewAuthor"></span>
                    </div>
                    <div class="preview-info-item">
                        <i class="fas fa-calendar"></i>
                        <span id="previewDate"></span>
                    </div>
                    <div class="preview-info-item">
                        <i class="fas fa-eye"></i>
                        <span id="previewViews"></span> views
                    </div>
                    <div class="preview-info-item">
                        <i class="fas fa-file"></i>
                        <span id="previewFileSize"></span>
                    </div>
                </div>
                
                <div class="preview-description" id="previewDescription"></div>
                
                <div class="preview-file-info">
                    <i class="fas fa-info-circle"></i>
                    <span id="previewFileType"></span> • <span id="previewCategory"></span>
                </div>
                
                <div class="preview-actions">
                    <a href="#" class="preview-btn preview-btn-primary" id="previewPlayBtn">
                        <i class="fas fa-play"></i> Play Now
                    </a>
                    <a href="#" class="preview-btn preview-btn-secondary" id="previewDownloadBtn" download>
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sermons-testimonies.js"></script>
    <script>
function likeVideo(id, btn) {
    fetch('includes/sermons-testimonies-handler.php?action=like&id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                let countSpan = btn.querySelector('.like-count');
                countSpan.textContent = data.likes;
                btn.classList.add('liked');
            }
        });
}
// Fallback stubs for playMedia and playMediaInCard if not defined in sermons-testimonies.js
if (typeof playMedia !== 'function') {
    function playMedia(btn) {
        alert('Media playback not implemented.');
    }
}
if (typeof playMediaInCard !== 'function') {
    function playMediaInCard(btn, filePath, fileType, title) {
        alert('Media playback not implemented.');
    }
}
</script>
</body>
</html> 