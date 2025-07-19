<?php
require_once 'includes/admin-content-handler.php';

// Process form submissions
$formResult = processFormSubmissions();
$success_message = $formResult['success'] ? $formResult['message'] : null;
$error_message = !$formResult['success'] && $formResult['message'] ? $formResult['message'] : null;

// Get pagination parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

// Get content with pagination
$contentData = getContentWithPagination($page, 10, $search, $type_filter);
$content_result = $contentData['content'];
$total_pages = $contentData['totalPages'];
$total_records = $contentData['totalRecords'];

// Get admin statistics
$stats = getAdminContentStats();

// Set page variables for header
$page_title = 'Manage Content';
$additional_css = ['assets/css/admin-manage-content.css'];
$additional_js = ['assets/js/admin-manage-content.js'];

// Include header
include 'includes/admin-header.php';
?>

<!-- Statistics Cards -->
<div class="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div style="background: rgba(255,255,255,0.95); padding: 1rem; border-radius: 15px; text-align: center;">
        <h3 style="color: #667eea; margin-bottom: 0.5rem;">Total Content</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #333;"><?php echo $stats['total']; ?></p>
    </div>
    <div style="background: rgba(255,255,255,0.95); padding: 1rem; border-radius: 15px; text-align: center;">
        <h3 style="color: #28a745; margin-bottom: 0.5rem;">Active</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #333;"><?php echo $stats['active']; ?></p>
    </div>
    <div style="background: rgba(255,255,255,0.95); padding: 1rem; border-radius: 15px; text-align: center;">
        <h3 style="color: #dc3545; margin-bottom: 0.5rem;">Inactive</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #333;"><?php echo $stats['inactive']; ?></p>
    </div>
    <div style="background: rgba(255,255,255,0.95); padding: 1rem; border-radius: 15px; text-align: center;">
        <h3 style="color: #ffc107; margin-bottom: 0.5rem;">Total Views</h3>
        <p style="font-size: 2rem; font-weight: bold; color: #333;"><?php echo number_format($stats['total_views']); ?></p>
    </div>
</div>

<div class="admin-actions">
    <a href="admin-upload.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Upload New Content
    </a>
    <a href="sermons&testimonies.php" class="btn btn-success">
        <i class="fas fa-eye"></i> View Public Site
    </a>
</div>

<div class="search-filters">
    <form method="GET" class="filter-row">
        <div class="filter-group">
            <label class="filter-label">Search</label>
            <input type="text" name="search" class="filter-input" placeholder="Search by title, author..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="filter-group">
            <label class="filter-label">Type</label>
            <select name="type" class="filter-input">
                <option value="">All Types</option>
                <option value="sermon" <?php echo $type_filter === 'sermon' ? 'selected' : ''; ?>>Sermons</option>
                <option value="testimony" <?php echo $type_filter === 'testimony' ? 'selected' : ''; ?>>Testimonies</option>
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">&nbsp;</label>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>
</div>

<div class="content-table">
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Author</th>
                <th>Category</th>
                <th>Upload Date</th>
                <th>Views</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($content_result)): ?>
                <?php foreach($content_result as $content): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($content['title']); ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo substr(htmlspecialchars($content['description']), 0, 50) . '...'; ?></small>
                        </td>
                        <td>
                            <span class="type-badge type-<?php echo $content['type']; ?>">
                                <?php echo ucfirst($content['type']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($content['author']); ?></td>
                        <td><?php echo htmlspecialchars($content['category']); ?></td>
                        <td><?php echo date('M j, Y', strtotime($content['upload_date'])); ?></td>
                        <td><?php echo number_format($content['views']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $content['status']; ?>">
                                <?php echo ucfirst($content['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-success btn-sm" onclick="viewContent(<?php echo $content['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editContent(<?php echo $content['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this content?')">
                                    <input type="hidden" name="delete_id" value="<?php echo $content['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="content_id" value="<?php echo $content['id']; ?>">
                                    <input type="hidden" name="new_status" value="<?php echo $content['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                    <input type="hidden" name="toggle_status" value="1">
                                    <button type="submit" class="btn btn-<?php echo $content['status'] === 'active' ? 'warning' : 'success'; ?> btn-sm">
                                        <i class="fas fa-<?php echo $content['status'] === 'active' ? 'pause' : 'play'; ?>"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem;">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                        <p>No content found.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type_filter); ?>" 
                   class="page-btn <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
include 'includes/admin-footer.php';
?> 