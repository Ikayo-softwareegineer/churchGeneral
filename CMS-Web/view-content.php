<?php
include 'includes/view-content-handler.php';

// Prepare dynamic content for placeholders
$page_title = htmlspecialchars($content['title']) . ' - Church CMS';
$back_button = '<a href="' . $handler->getBackUrl() . '" class="back-button"><i class="fas fa-arrow-left"></i> Back to ' . ViewContentHandler::getContentTypeDisplay($content['type']) . '</a>';

// Content header
$content_header = '<div class="content-header">'
    .'<div class="content-icon ' . ($content['type'] === 'sermon' ? 'sermon-icon' : 'testimony-icon') . '"><i class="' . ViewContentHandler::getFileTypeIcon($content['file_type']) . '"></i></div>'
    .'<div><h1 class="content-title">' . htmlspecialchars($content['title']) . '</h1>'
    .'<div class="content-meta"><i class="fas fa-user"></i> ' . htmlspecialchars($content['author']) . ' • <i class="fas fa-calendar"></i> ' . date('F j, Y', strtotime($content['upload_date'])) . '</div></div>'
    .'</div>';

// Category
$category_html = '';
if (!empty($content['category'])) {
    $category_html = '<div class="content-category"><i class="fas fa-tag"></i> ' . ucfirst(htmlspecialchars($content['category'])) . '</div>';
}

// Media player
if (ViewContentHandler::isVideo($content['file_type'])) {
    $media_player = '<div class="media-player"><video controls><source src="' . htmlspecialchars($content['file_path']) . '" type="video/mp4">Your browser does not support the video tag.</video></div>';
} elseif (ViewContentHandler::isAudio($content['file_type'])) {
    $media_player = '<div class="media-player"><audio controls><source src="' . htmlspecialchars($content['file_path']) . '" type="audio/mpeg">Your browser does not support the audio tag.</audio></div>';
} else {
    $media_player = '<div class="media-player"><div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 15px;"><i class="fas fa-file-alt" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i><h3>Text Content</h3><p>This content is available for download.</p></div></div>';
}

// Description
$description_html = '<div class="content-description">' . nl2br(htmlspecialchars($content['description'])) . '</div>';

// Actions
$actions_html = '<div class="action-buttons">'
    .'<a href="' . htmlspecialchars($content['file_path']) . '" class="btn btn-custom-primary" download><i class="fas fa-download"></i> Download</a>'
    .'<a href="#" class="btn btn-custom-secondary" onclick="shareContent()"><i class="fas fa-share"></i> Share</a>'
    .'<a href="#" class="btn btn-custom-secondary" onclick="addToFavorites()"><i class="fas fa-heart"></i> Add to Favorites</a>'
    .'</div>';

// Stats
$stats_html = '<div class="content-stats">'
    .'<div class="stat-item"><i class="fas fa-eye"></i><span>' . number_format($content['views']) . ' views</span></div>'
    .'<div class="stat-item"><i class="fas fa-download"></i><span>' . number_format($content['downloads']) . ' downloads</span></div>';
if ($content['file_size']) {
    $stats_html .= '<div class="stat-item"><i class="fas fa-file"></i><span>' . ViewContentHandler::formatFileSize($content['file_size']) . '</span></div>';
}
$stats_html .= '<div class="stat-item"><i class="fas fa-clock"></i><span>Uploaded ' . date('M j, Y', strtotime($content['upload_date'])) . '</span></div></div>';

// Related content
$related_html = '';
if (!empty($relatedContent)) {
    $related_html .= '<div class="related-content"><h2 class="related-title">Related ' . ViewContentHandler::getContentTypeDisplay($content['type']) . '</h2><div class="row">';
    foreach ($relatedContent as $related) {
        $related_html .= '<div class="col-lg-4 col-md-6 mb-3">'
            .'<a href="view-content.php?id=' . $related['id'] . '" class="related-card">'
            .'<div class="related-card-title">' . htmlspecialchars($related['title']) . '</div>'
            .'<div class="related-card-meta">' . htmlspecialchars($related['author']) . ' • ' . date('M j, Y', strtotime($related['upload_date'])) . '</div>'
            .'</a></div>';
    }
    $related_html .= '</div></div>';
}

// Load the HTML template
$template = file_get_contents('templates/view-content.html');

// Replace placeholders
$replacements = [
    '{{page_title}}' => $page_title,
    '{{back_button}}' => $back_button,
    '{{content_header}}' => $content_header,
    '{{category}}' => $category_html,
    '{{media_player}}' => $media_player,
    '{{description}}' => $description_html,
    '{{actions}}' => $actions_html,
    '{{stats}}' => $stats_html,
    '{{related_content}}' => $related_html
];

echo strtr($template, $replacements);
?> 