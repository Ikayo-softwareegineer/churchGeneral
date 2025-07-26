<?php
require_once(__DIR__ . '/../../includes/php/database.php');

$handler = new SermonsTestimoniesHandler();

// Get filter values from query string
$type = $_GET['type'] ?? 'sermons';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Fetch stats, categories, and content
$stats = $handler->getStats();
$categories = $handler->getCategories($type);
$contentData = $handler->getContent($type, $search, $category, $page);
$content = $contentData['items'];


// Prepare dynamic content for placeholders
$page_title = 'Sermons & Testimonies';

// Stats block
$stats_html = '<div class="row mb-4">'
    .'<div class="col-md-3 col-sm-6 mb-3"><div class="stat-card"><div class="stat-number">'. $stats['sermons'] .'</div><div class="stat-label">Total Sermons</div></div></div>'
    .'<div class="col-md-3 col-sm-6 mb-3"><div class="stat-card"><div class="stat-number">'. $stats['testimonies'] .'</div><div class="stat-label">Testimonies</div></div></div>'
    .'<div class="col-md-3 col-sm-6 mb-3"><div class="stat-card"><div class="stat-number">'. $stats['this_month'] .'</div><div class="stat-label">This Month</div></div></div>'
    .'<div class="col-md-3 col-sm-6 mb-3"><div class="stat-card"><div class="stat-number">'. number_format($stats['total_views']) .'</div><div class="stat-label">Total Views</div></div></div>'
    .'</div>';

// Tabs block
$tabs = '<div class="custom-tabs">'
    .'<a href="?type=sermons'. ($search ? '&search='.urlencode($search) : '') . ($category ? '&category='.urlencode($category) : '') .'" class="custom-tab-btn '.($type==='sermons'?'active':'').'">'
    .'<i class="fas fa-church"></i> Sermons</a>'
    .'<a href="?type=testimonies'. ($search ? '&search='.urlencode($search) : '') . ($category ? '&category='.urlencode($category) : '') .'" class="custom-tab-btn '.($type==='testimonies'?'active':'').'">'
    .'<i class="fas fa-heart"></i> Testimonies</a>'
    .'</div>';

// Search form
$search_form = '<form method="GET" class="search-bar">'
    .'<i class="fas fa-search"></i>'
    .'<input type="text" name="search" class="search-input" placeholder="Search..." value="'.htmlspecialchars($search).'">'
    .'<input type="hidden" name="type" value="'.htmlspecialchars($type).'">'
    .'<button type="submit" class="search-btn">Search</button>'
    .'</form>';

// Filter tabs
$filter_tabs = '<div class="filter-tabs">'
    .'<a href="?type='.$type.($search?'&search='.urlencode($search):'').'" class="filter-btn '.($category==''?'active':'').'">All</a>';
foreach ($categories as $cat) {
    $filter_tabs .= '<a href="?type='.$type.'&category='.urlencode($cat['category']).($search?'&search='.urlencode($search):'').'" class="filter-btn '.($category===$cat['category']?'active':'').'">'.ucfirst(htmlspecialchars($cat['category'])).'</a>';
}
$filter_tabs .= '</div>';

// Content block
$content_html = '';
if (empty($content)) {
    $content_html = '<div class="no-results">'
        .'<i class="fas fa-search" style="font-size: 3rem; margin-bottom: 1rem;"></i>'
        .'<p>No content found.</p>'
        .'<a href="?type='.$type.'" class="btn btn-custom-primary">View All</a>'
        .'</div>';
} else {
    $content_html = '<div class="row">';
    foreach ($content as $item) {
        $isVideo = $handler::isVideo($item['file_type']);
        $isAudio = $handler::isAudio($item['file_type']);

        $content_html .= '<div class="col-lg-4 col-md-6 mb-4"><div class="custom-card">';
        $content_html .= '<div class="card-header"><div class="card-icon '.($item['type']==='sermon'?'sermon-icon':'testimony-icon').'">'
            .'<i class="'. $handler::getFileTypeIcon($item['file_type']) .'"></i></div>';
        $content_html .= '<div><div class="card-title">'.htmlspecialchars($item['title']).'</div>';
        $content_html .= '<div class="card-date">'.htmlspecialchars($item['author']).' • '.date('F j, Y', strtotime($item['upload_date'])).'</div></div></div>';

        if ($isVideo) {
            $content_html .= '<div class="media-container"><video class="media-preview" controls preload="metadata">'
                .'<source src="'.htmlspecialchars($item['file_path']).'" type="video/mp4">Your browser does not support the video tag.</video></div>';
        } elseif ($isAudio) {
            $content_html .= '<div class="media-container"><audio class="media-preview" controls preload="metadata">'
                .'<source src="'.htmlspecialchars($item['file_path']).'" type="audio/mpeg">Your browser does not support the audio tag.</audio></div>';
        }

        if ($item['type'] === 'testimony' && !empty($item['category'])) {
            $content_html .= '<div class="testimony-category">'.ucfirst(htmlspecialchars($item['category'])).'</div>';
        }

        $content_html .= '<div class="card-description">'.htmlspecialchars(substr($item['description'], 0, 150)).(strlen($item['description'])>150?'...':'').'</div>';
        $content_html .= '<div class="d-flex gap-2 mt-3">';
        if ($isVideo || $isAudio) {
            $label = $isVideo ? 'Watch Now' : 'Listen Now';
            $icon = $isVideo ? 'play' : 'volume-up';
            $content_html .= '<button class="btn btn-watch-now" onclick="playMediaInCard(this, \''.htmlspecialchars($item['file_path']).'\', \''.$item['file_type'].'\', \''.htmlspecialchars($item['title']).'\')">'
                .'<i class="fas fa-'.$icon.'"></i> '.$label.'</button>';
        }

        $content_html .= '<button class="btn btn-like" onclick="likeVideo('.$item['id'].', this)"><i class="fas fa-heart"></i> <span class="like-count">'.($item['likes'] ?? 0).'</span></button>';
        $content_html .= '<a href="includes/sermons-testimonies-handler.php?action=download&id='.$item['id'].'" class="btn btn-custom-secondary download-btn"><i class="fas fa-download"></i> Download</a>';
        $content_html .= '</div>';

        $content_html .= '<div class="mt-2 text-muted small"><i class="fas fa-eye"></i> '.number_format($item['views']).' views';
        if ($item['file_size']) {
            $content_html .= ' • <i class="fas fa-file"></i> '.$handler::formatFileSize($item['file_size']);
        }
        $content_html .= '</div></div></div>';
    }
    $content_html .= '</div>';
}

// Pagination block
$pagination = '';
if ($totalPages > 1) {
    $pagination .= '<div class="pagination-container"><nav><ul class="pagination">';
    if ($page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?'.$handler->getPaginationUrlParams($page-1).'"><i class="fas fa-chevron-left"></i></a></li>';
    }
    for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++) {
        $pagination .= '<li class="page-item '.($i===$page?'active':'').'"><a class="page-link" href="?'.$handler->getPaginationUrlParams($i).'">'.$i.'</a></li>';
    }
    if ($page < $totalPages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="?'.$handler->getPaginationUrlParams($page+1).'"><i class="fas fa-chevron-right"></i></a></li>';
    }
    $pagination .= '</ul></nav></div>';
}

// Load the template and inject
$template = file_get_contents('templates/sermons-testimonies.html');
$replacements = [
    '{{page_title}}' => $page_title,
    '{{stats}}' => $stats_html,
    '{{tabs}}' => $tabs,
    '{{search_form}}' => $search_form,
    '{{filter_tabs}}' => $filter_tabs,
    '{{content}}' => $content_html,
    '{{pagination}}' => $pagination
];

echo strtr($template, $replacements);
?>
