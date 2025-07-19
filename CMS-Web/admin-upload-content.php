<?php
include 'includes/admin-upload-content-handler.php';

// Prepare dynamic content for placeholders
$page_title = 'Upload Content';
$message_html = $message ? '<div class="alert alert-success"><i class="fas fa-check-circle"></i> '.htmlspecialchars($message).'</div>' : '';
$error_html = $error ? '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> '.htmlspecialchars($error).'</div>' : '';

// Upload form HTML
$upload_form = '<div class="form-container">'
    .'<h2 class="form-title">Upload New Content</h2>'
    .'<div class="file-type-info"><h6><i class="fas fa-info-circle"></i> File Organization</h6><ul>'
    .'<li><strong>Videos:</strong> MP4, AVI, MOV files → stored in <code>uploads/videos/</code></li>'
    .'<li><strong>Audio:</strong> MP3, WAV files → stored in <code>uploads/audio/</code></li>'
    .'<li><strong>Documents:</strong> PDF, TXT, DOC, DOCX files → stored in <code>uploads/documents/</code></li>'
    .'</ul></div>'
    .'<form method="POST" enctype="multipart/form-data">'
    .'<div class="row">'
    .'<div class="col-md-6 mb-3"><label for="type" class="form-label">Content Type *</label>'
    .'<select name="type" id="type" class="form-select" required>'
    .'<option value="">Select Type</option>'
    .'<option value="sermon">Sermon</option>'
    .'<option value="testimony">Testimony</option>'
    .'</select></div>'
    .'<div class="col-md-6 mb-3"><label for="category" class="form-label">Category</label>'
    .'<input type="text" name="category" id="category" class="form-control" placeholder="e.g., faith, prayer, healing"></div>'
    .'</div>'
    .'<div class="row">'
    .'<div class="col-md-6 mb-3"><label for="title" class="form-label">Title *</label>'
    .'<input type="text" name="title" id="title" class="form-control" placeholder="Enter content title" required></div>'
    .'<div class="col-md-6 mb-3"><label for="author" class="form-label">Author/Speaker *</label>'
    .'<input type="text" name="author" id="author" class="form-control" placeholder="Enter author name" required></div>'
    .'</div>'
    .'<div class="mb-3"><label for="description" class="form-label">Description *</label>'
    .'<textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter content description" required></textarea></div>'
    .'<div class="mb-3"><label for="file" class="form-label">File Upload *</label>'
    .'<div class="file-upload" onclick="document.getElementById(\'file\').click()">'
    .'<div class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>'
    .'<h5>Click to upload or drag and drop</h5>'
    .'<p class="text-muted">Supported formats: MP4, AVI, MOV, MP3, WAV, PDF, TXT, DOC, DOCX (Max 100MB)</p>'
    .'<input type="file" name="file" id="file" class="d-none" accept=".mp4,.avi,.mov,.mp3,.wav,.pdf,.txt,.doc,.docx" required>'
    .'</div><div id="file-info" class="mt-2"></div></div>'
    .'<div class="text-center">'
    .'<button type="submit" class="btn btn-custom-primary"><i class="fas fa-upload"></i> Upload Content</button>'
    .'<a href="sermons-testimonies.php" class="btn btn-custom-secondary ms-2"><i class="fas fa-eye"></i> View Site</a>'
    .'</div>'
    .'</form>'
    .'</div>';

// Content table HTML
$content_table = '<div class="content-management"><h2 class="form-title">Recent Content</h2><div class="table-responsive"><table class="table content-table"><thead><tr>'
    .'<th>Title</th><th>Type</th><th>Author</th><th>Category</th><th>File Location</th><th>Upload Date</th><th>Views</th><th>Status</th><th>Actions</th>'
    .'</tr></thead><tbody>';
foreach ($existingContent as $item) {
    $content_table .= '<tr>';
    $content_table .= '<td><strong>'.htmlspecialchars($item['title']).'</strong><br><small class="text-muted">'.AdminUploadContentHandler::getFileTypeIcon($item['file_type']).' '.ucfirst($item['file_type']).'</small></td>';
    $content_table .= '<td><span class="badge '.($item['type']==='sermon'?'bg-primary':'bg-success').'">'.ucfirst($item['type']).'</span></td>';
    $content_table .= '<td>'.htmlspecialchars($item['author']).'</td>';
    $content_table .= '<td>'.htmlspecialchars($item['category'] ?: '-').'</td>';
    $content_table .= '<td><small class="text-muted">'.htmlspecialchars($item['file_path']).'</small></td>';
    $content_table .= '<td>'.date('M j, Y', strtotime($item['upload_date'])).'</td>';
    $content_table .= '<td>'.number_format($item['views']).'</td>';
    $content_table .= '<td><span class="status-badge '.($item['status']==='active'?'status-active':'status-inactive').'">'.ucfirst($item['status']).'</span></td>';
    $content_table .= '<td><div class="action-buttons">'
        .'<a href="view-content.php?id='.$item['id'].'" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>'
        .'<a href="edit-content.php?id='.$item['id'].'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>'
        .'<form method="POST" action="" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this content?\');">'
        .'<input type="hidden" name="delete_id" value="'.$item['id'].'">'
        .'<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>'
        .'</form></div></td>';
    $content_table .= '</tr>';
}
$content_table .= '</tbody></table></div></div>';

// Load the HTML template
$template = file_get_contents('templates/admin-upload-content.html');

// Replace placeholders
$replacements = [
    '{{page_title}}' => $page_title,
    '{{message}}' => $message_html,
    '{{error}}' => $error_html,
    '{{upload_form}}' => $upload_form,
    '{{content_table}}' => $content_table
];

echo strtr($template, $replacements);
?> 