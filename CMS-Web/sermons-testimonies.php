<?php
// index.php - Main Church Management System Page
session_start();

// Include database configuration
require_once 'config/church.php';

// Get sermons and testimonies data
$sermons = getSermons();
$testimonies = getTestimonies();

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'search':
            $query = $_GET['q'] ?? '';
            $type = $_GET['type'] ?? 'sermons';
            echo json_encode(searchContent($query, $type));
            exit;
            
        case 'filter':
            $filter = $_GET['filter'] ?? 'all';
            $type = $_GET['type'] ?? 'sermons';
            echo json_encode(filterContent($filter, $type));
            exit;
            
        case 'play':
            $id = $_GET['id'] ?? 0;
            $type = $_GET['type'] ?? 'sermons';
            echo json_encode(playContent($id, $type));
            exit;
            
        case 'favorite':
            $id = $_GET['id'] ?? 0;
            $type = $_GET['type'] ?? 'sermons';
            echo json_encode(addToFavorites($id, $type));
            exit;
    }
}

// Functions for data handling
function getSermons() {
    // In a real application, this would fetch from database
    return [
        [
            'id' => 1,
            'title' => 'Walking in Faith Through Uncertainty',
            'speaker' => 'Pastor John Smith',
            'date' => '2024-07-28',
            'series' => 'Faith Series',
            'description' => 'Discover how to maintain unwavering faith even when facing life\'s greatest uncertainties and challenges.',
            'duration' => '45:30',
            'tags' => ['Faith', 'Trust', 'Hope'],
            'views' => 1250,
            'likes' => 89,
            'audio_url' => 'audio/sermon1.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=200&fit=crop',
            'created_at' => '2024-07-28 10:00:00'
        ],
        [
            'id' => 2,
            'title' => 'The Power of Prayer',
            'speaker' => 'Pastor Sarah Johnson',
            'date' => '2024-07-21',
            'series' => 'Prayer Life',
            'description' => 'Understanding the transformative power of prayer and how it connects us deeper with God\'s heart.',
            'duration' => '38:45',
            'tags' => ['Prayer', 'Connection', 'Spiritual Growth'],
            'views' => 967,
            'likes' => 73,
            'audio_url' => 'audio/sermon2.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=400&h=200&fit=crop',
            'created_at' => '2024-07-21 10:00:00'
        ],
        [
            'id' => 3,
            'title' => 'Love in Action',
            'speaker' => 'Pastor Michael Brown',
            'date' => '2024-07-14',
            'series' => 'Love Series',
            'description' => 'Exploring practical ways to demonstrate Christ\'s love in our daily interactions and relationships.',
            'duration' => '42:15',
            'tags' => ['Love', 'Service', 'Community'],
            'views' => 1534,
            'likes' => 112,
            'audio_url' => 'audio/sermon3.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=400&h=200&fit=crop',
            'created_at' => '2024-07-14 10:00:00'
        ]
    ];
}

function getTestimonies() {
    return [
        [
            'id' => 1,
            'title' => 'From Addiction to Freedom',
            'author' => 'Mark Thompson',
            'date' => '2024-07-25',
            'description' => 'How God delivered me from years of addiction and gave me a new purpose in life.',
            'duration' => '12:30',
            'tags' => ['Deliverance', 'Freedom', 'Recovery'],
            'views' => 892,
            'likes' => 156,
            'audio_url' => 'audio/testimony1.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=400&h=200&fit=crop',
            'created_at' => '2024-07-25 14:00:00'
        ],
        [
            'id' => 2,
            'title' => 'Healing Through Faith',
            'author' => 'Lisa Rodriguez',
            'date' => '2024-07-20',
            'description' => 'My journey of physical and emotional healing through the power of faith and community support.',
            'duration' => '8:45',
            'tags' => ['Healing', 'Faith', 'Miracle'],
            'views' => 673,
            'likes' => 94,
            'audio_url' => 'audio/testimony2.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=200&fit=crop',
            'created_at' => '2024-07-20 16:00:00'
        ],
        [
            'id' => 3,
            'title' => 'Financial Breakthrough',
            'author' => 'David Wilson',
            'date' => '2024-07-18',
            'description' => 'How trusting God\'s provision led to unexpected financial breakthrough in my family\'s life.',
            'duration' => '10:20',
            'tags' => ['Provision', 'Trust', 'Breakthrough'],
            'views' => 445,
            'likes' => 67,
            'audio_url' => 'audio/testimony3.mp3',
            'thumbnail' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=400&h=200&fit=crop',
            'created_at' => '2024-07-18 15:00:00'
        ]
    ];
}

function searchContent($query, $type) {
    $data = $type === 'sermons' ? getSermons() : getTestimonies();
    $results = [];
    
    foreach ($data as $item) {
        if (stripos($item['title'], $query) !== false || 
            stripos($item['description'], $query) !== false ||
            array_filter($item['tags'], function($tag) use ($query) {
                return stripos($tag, $query) !== false;
            })) {
            $results[] = $item;
        }
    }
    
    return $results;
}

function filterContent($filter, $type) {
    $data = $type === 'sermons' ? getSermons() : getTestimonies();
    
    switch ($filter) {
        case 'recent':
            usort($data, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            break;
            
        case 'popular':
            usort($data, function($a, $b) {
                return $b['views'] - $a['views'];
            });
            break;
            
        case 'series':
            $data = array_filter($data, function($item) {
                return isset($item['series']) && !empty($item['series']);
            });
            break;
            
        case 'healing':
            $data = array_filter($data, function($item) {
                return in_array('Healing', $item['tags']) || in_array('Miracle', $item['tags']);
            });
            break;
    }
    
    return $data;
}

function playContent($id, $type) {
    // Log play event, update view count, etc.
    return ['success' => true, 'message' => 'Playing content'];
}

function addToFavorites($id, $type) {
    // Add to user favorites in database
    return ['success' => true, 'message' => 'Added to favorites'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sermons & Testimonies - Grace Community Church</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/sermons-testimonies.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row">
            <div class="col-12">
                <div class="header text-center mb-4">
                    <h1><i class="fas fa-church"></i> Sermons & Testimonies</h1>
                    <p class="lead">Discover God's word through powerful messages and inspiring testimonies</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center custom-nav" id="contentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sermons-tab" data-bs-toggle="pill" data-bs-target="#sermons" 
                                type="button" role="tab" aria-controls="sermons" aria-selected="true">
                            <i class="fas fa-microphone me-2"></i>Sermons
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="testimonies-tab" data-bs-toggle="pill" data-bs-target="#testimonies" 
                                type="button" role="tab" aria-controls="testimonies" aria-selected="false">
                            <i class="fas fa-heart me-2"></i>Testimonies
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Search and Filter Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="controls-card">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Search sermons and testimonies...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="filter-buttons" id="filterButtons">
                                <button class="btn btn-outline-primary active" data-filter="all">All</button>
                                <button class="btn btn-outline-primary" data-filter="recent">Recent</button>
                                <button class="btn btn-outline-primary" data-filter="popular">Popular</button>
                                <button class="btn btn-outline-primary" data-filter="series">Series</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Tabs -->
        <div class="tab-content" id="contentTabsContent">
            <!-- Sermons Tab -->
            <div class="tab-pane fade show active" id="sermons" role="tabpanel" aria-labelledby="sermons-tab">
                <div class="row" id="sermonsGrid">
                    <?php foreach ($sermons as $sermon): ?>
                        <div class="col-lg-4 col-md-6 mb-4 content-item" data-tags="<?php echo implode(',', $sermon['tags']); ?>">
                            <div class="card content-card h-100">
                                <div class="card-img-container">
                                    <img src="<?php echo $sermon['thumbnail']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($sermon['title']); ?>">
                                    <div class="play-overlay">
                                        <button class="btn btn-play" onclick="playContent(<?php echo $sermon['id']; ?>, 'sermons')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($sermon['title']); ?></h5>
                                    <div class="card-meta mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($sermon['speaker']); ?>
                                            <i class="fas fa-calendar ms-3 me-1"></i><?php echo date('M d, Y', strtotime($sermon['date'])); ?>
                                        </small>
                                    </div>
                                    <div class="tags mb-2">
                                        <?php foreach ($sermon['tags'] as $tag): ?>
                                            <span class="badge tag-badge"><?php echo htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($sermon['description']); ?></p>
                                    <div class="stats mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?php echo $sermon['duration']; ?>
                                            <i class="fas fa-eye ms-3 me-1"></i><?php echo number_format($sermon['views']); ?>
                                            <i class="fas fa-heart ms-3 me-1"></i><?php echo $sermon['likes']; ?>
                                        </small>
                                    </div>
                                    <div class="card-actions d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" onclick="playContent(<?php echo $sermon['id']; ?>, 'sermons')">
                                            <i class="fas fa-play me-1"></i>Play
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="shareContent(<?php echo $sermon['id']; ?>, 'sermons')">
                                            <i class="fas fa-share"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="addToFavorites(<?php echo $sermon['id']; ?>, 'sermons')">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Testimonies Tab -->
            <div class="tab-pane fade" id="testimonies" role="tabpanel" aria-labelledby="testimonies-tab">
                <div class="row" id="testimoniesGrid">
                    <?php foreach ($testimonies as $testimony): ?>
                        <div class="col-lg-4 col-md-6 mb-4 content-item" data-tags="<?php echo implode(',', $testimony['tags']); ?>">
                            <div class="card content-card h-100">
                                <div class="card-img-container">
                                    <img src="<?php echo $testimony['thumbnail']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($testimony['title']); ?>">
                                    <div class="play-overlay">
                                        <button class="btn btn-play" onclick="playContent(<?php echo $testimony['id']; ?>, 'testimonies')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($testimony['title']); ?></h5>
                                    <div class="card-meta mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($testimony['author']); ?>
                                            <i class="fas fa-calendar ms-3 me-1"></i><?php echo date('M d, Y', strtotime($testimony['date'])); ?>
                                        </small>
                                    </div>
                                    <div class="tags mb-2">
                                        <?php foreach ($testimony['tags'] as $tag): ?>
                                            <span class="badge tag-badge"><?php echo htmlspecialchars($tag); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="card-text flex-grow-1"><?php echo htmlspecialchars($testimony['description']); ?></p>
                                    <div class="stats mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i><?php echo $testimony['duration']; ?>
                                            <i class="fas fa-eye ms-3 me-1"></i><?php echo number_format($testimony['views']); ?>
                                            <i class="fas fa-heart ms-3 me-1"></i><?php echo $testimony['likes']; ?>
                                        </small>
                                    </div>
                                    <div class="card-actions d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" onclick="playContent(<?php echo $testimony['id']; ?>, 'testimonies')">
                                            <i class="fas fa-play me-1"></i>Play
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="shareContent(<?php echo $testimony['id']; ?>, 'testimonies')">
                                            <i class="fas fa-share"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" onclick="addToFavorites(<?php echo $testimony['id']; ?>, 'testimonies')">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Player Modal -->
    <div class="modal fade" id="audioPlayerModal" tabindex="-1" aria-labelledby="audioPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="audioPlayerModalLabel">Now Playing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="audioPlayerContent">
                        <!-- Audio player content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/sermos-testimonies.js"></script>
    
    <script>
        // Pass PHP data to JavaScript
        window.churchData = {
            sermons: <?php echo json_encode($sermons); ?>,
            testimonies: <?php echo json_encode($testimonies); ?>
        };
    </script>
</body>
</html>