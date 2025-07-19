// File upload handling
const fileInput = document.getElementById('file');
const fileUpload = document.querySelector('.file-upload');
const fileInfo = document.getElementById('file-info');

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        fileInfo.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-file"></i> ${file.name} (${formatFileSize(file.size)})
                <br><small>Will be stored in: ${getStorageLocation(file.name)}</small>
            </div>
        `;
    }
});

// Drag and drop functionality
fileUpload.addEventListener('dragover', function(e) {
    e.preventDefault();
    fileUpload.classList.add('dragover');
});

fileUpload.addEventListener('dragleave', function(e) {
    e.preventDefault();
    fileUpload.classList.remove('dragover');
});

fileUpload.addEventListener('drop', function(e) {
    e.preventDefault();
    fileUpload.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }
});

// File size formatter
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// Get storage location based on file extension
function getStorageLocation(fileName) {
    const extension = fileName.split('.').pop().toLowerCase();
    if (['mp4', 'avi', 'mov'].includes(extension)) {
        return 'uploads/videos/';
    } else if (['mp3', 'wav'].includes(extension)) {
        return 'uploads/audio/';
    } else {
        return 'uploads/documents/';
    }
}

// Delete content function
function deleteContent(contentId) {
    if (confirm('Are you sure you want to delete this content? This action cannot be undone.')) {
        // This would typically make an AJAX call to delete the content
        alert('Delete functionality would be implemented here');
    }
}

// Auto-resize textarea
const description = document.getElementById('description');
description.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
}); 