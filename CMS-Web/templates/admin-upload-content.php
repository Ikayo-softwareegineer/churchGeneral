<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Content - Church CMS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin-upload-content.css">
</head>
<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Church CMS Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="sermons-testimonies.php"><i class="fas fa-home"></i> View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-users"></i> Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-cog"></i> Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h1 class="page-title">Upload Content</h1>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="form-container">
            <h2 class="form-title">Upload New Content</h2>
            
            <!-- File Type Information -->
            <div class="file-type-info">
                <h6><i class="fas fa-info-circle"></i> File Organization</h6>
                <ul>
                    <li><strong>Videos:</strong> MP4, AVI, MOV files → stored in <code>uploads/videos/</code></li>
                    <li><strong>Audio:</strong> MP3, WAV files → stored in <code>uploads/audio/</code></li>
                    <li><strong>Documents:</strong> PDF, TXT, DOC, DOCX files → stored in <code>uploads/documents/</code></li>
                </ul>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Content Type *</label>
                        <select name="type" id="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="sermon">Sermon</option>
                            <option value="testimony">Testimony</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" name="category" id="category" class="form-control" placeholder="e.g., faith, prayer, healing">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter content title" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="author" class="form-label">Author/Speaker *</label>
                        <input type="text" name="author" id="author" class="form-control" placeholder="Enter author name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter content description" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label">File Upload *</label>
                    <div class="file-upload" onclick="document.getElementById('file').click()">
                        <div class="file-upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h5>Click to upload or drag and drop</h5>
                        <p class="text-muted">Supported formats: MP4, AVI, MOV, MP3, WAV, PDF, TXT, DOC, DOCX (Max 100MB)</p>
                        <input type="file" name="file" id="file" class="d-none" accept=".mp4,.avi,.mov,.mp3,.wav,.pdf,.txt,.doc,.docx" required>
                    </div>
                    <div id="file-info" class="mt-2"></div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-custom-primary">
                        <i class="fas fa-upload"></i> Upload Content
                    </button>
                    <a href="sermons-testimonies.php" class="btn btn-custom-secondary ms-2">
                        <i class="fas fa-eye"></i> View Site
                    </a>
                </div>
            </form>
        </div>

        <!-- Content Management -->
        <div class="content-management">
            <h2 class="form-title">Recent Content</h2>
            <div class="table-responsive">
                <table class="table content-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>File Location</th>
                            <th>Upload Date</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($existingContent as $item): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo getFileTypeIcon($item['file_type']); ?> <?php echo ucfirst($item['file_type']); ?></small>
                                </td>
                                <td>
                                    <span class="badge <?php echo $item['type'] === 'sermon' ? 'bg-primary' : 'bg-success'; ?>">
                                        <?php echo ucfirst($item['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($item['author']); ?></td>
                                <td><?php echo htmlspecialchars($item['category'] ?: '-'); ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($item['file_path']); ?>
                                    </small>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($item['upload_date'])); ?></td>
                                <td><?php echo number_format($item['views']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $item['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view-content.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-content.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this content?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin-upload-content.js"></script>
</body>
</html> 