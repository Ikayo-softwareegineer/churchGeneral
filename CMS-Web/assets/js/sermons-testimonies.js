// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    const searchForm = document.querySelector('.search-bar');
    
    // Auto-submit search after typing stops
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                searchForm.submit();
            }
        }, 500);
    });
    
    // Clear search functionality
    const clearSearchBtn = document.createElement('button');
    clearSearchBtn.type = 'button';
    clearSearchBtn.className = 'clear-search-btn';
    clearSearchBtn.innerHTML = '<i class="fas fa-times"></i>';
    clearSearchBtn.style.cssText = `
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        display: none;
    `;
    
    searchInput.parentNode.style.position = 'relative';
    searchInput.parentNode.appendChild(clearSearchBtn);
    
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchForm.submit();
    });
    
    searchInput.addEventListener('input', function() {
        clearSearchBtn.style.display = this.value ? 'block' : 'none';
    });
});

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            document.body.style.cursor = 'wait';
            
            // Track filter clicks
            console.log('Filter clicked:', this.textContent.trim());
        });
    });
});

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.custom-tab-btn');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add loading state
            document.body.style.cursor = 'wait';
            
            // Track tab clicks
            console.log('Tab clicked:', this.textContent.trim());
        });
    });
});

// Card interactions
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.custom-card');
    
    cards.forEach(card => {
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Click tracking
        const viewButton = card.querySelector('.btn-custom-primary');
        if (viewButton) {
            viewButton.addEventListener('click', function() {
                console.log('Content viewed:', this.href);
            });
        }
        
        const downloadButton = card.querySelector('.btn-custom-secondary');
        if (downloadButton) {
            downloadButton.addEventListener('click', function() {
                console.log('Content downloaded:', this.href);
            });
        }
    });
});

// Enhanced media preview functionality
document.addEventListener('DOMContentLoaded', function() {
    const mediaPreviews = document.querySelectorAll('.media-preview');
    
    mediaPreviews.forEach(media => {
        const overlay = media.parentNode.querySelector('.media-overlay');
        
        // Pause all other media when one starts playing
        media.addEventListener('play', function() {
            mediaPreviews.forEach(otherMedia => {
                if (otherMedia !== media && !otherMedia.paused) {
                    otherMedia.pause();
                }
            });
            
            // Hide overlay when playing
            if (overlay) {
                overlay.classList.add('hidden');
            }
            
            // Add playing class
            this.classList.add('playing');
        });
        
        // Show overlay when paused
        media.addEventListener('pause', function() {
            if (overlay) {
                overlay.classList.remove('hidden');
            }
            this.classList.remove('playing');
        });
        
        // Add loading state
        media.addEventListener('loadstart', function() {
            this.style.opacity = '0.7';
        });
        
        media.addEventListener('canplay', function() {
            this.style.opacity = '1';
        });
        
        // Error handling
        media.addEventListener('error', function() {
            this.innerHTML = `
                <div style="text-align: center; padding: 2rem; background: #f8f9fa; border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle" style="color: #dc3545; font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Media could not be loaded</p>
                </div>
            `;
        });
    });
});

// Pagination functionality
document.addEventListener('DOMContentLoaded', function() {
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state
            document.body.style.cursor = 'wait';
            
            // Track pagination clicks
            console.log('Page clicked:', this.href);
        });
    });
});

// Statistics animation
document.addEventListener('DOMContentLoaded', function() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    const animateNumber = (element, target) => {
        let current = 0;
        const increment = target / 50; // Animate over 50 steps
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current).toLocaleString();
        }, 20);
    };
    
    // Animate stats when they come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.textContent.replace(/,/g, ''));
                animateNumber(entry.target, target);
                observer.unobserve(entry.target);
            }
        });
    });
    
    statNumbers.forEach(stat => {
        observer.observe(stat);
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
    
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.querySelector('.search-input');
        if (searchInput && searchInput.value) {
            searchInput.value = '';
            searchInput.form.submit();
        }
    }
});

// Infinite scroll (optional)
let isLoading = false;
let currentPage = 1;

function loadMoreContent() {
    if (isLoading) return;
    
    isLoading = true;
    currentPage++;
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'loading-indicator';
    loadingIndicator.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
            <p>Loading more content...</p>
        </div>
    `;
    
    const contentRow = document.querySelector('.row');
    if (contentRow) {
        contentRow.appendChild(loadingIndicator);
    }
    
    // Simulate loading (replace with actual AJAX call)
    setTimeout(() => {
        if (loadingIndicator.parentNode) {
            loadingIndicator.parentNode.removeChild(loadingIndicator);
        }
        isLoading = false;
    }, 1000);
}

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add scroll to top button
document.addEventListener('DOMContentLoaded', function() {
    const scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollToTopBtn.className = 'scroll-to-top-btn';
    scrollToTopBtn.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        border: none;
        cursor: pointer;
        display: none;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.3s;
    `;
    
    document.body.appendChild(scrollToTopBtn);
    
    scrollToTopBtn.addEventListener('click', scrollToTop);
    
    // Show/hide scroll to top button
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.style.display = 'block';
        } else {
            scrollToTopBtn.style.display = 'none';
        }
    });
});

// Performance optimization
document.addEventListener('DOMContentLoaded', function() {
    // Lazy load images and videos
    const mediaElements = document.querySelectorAll('img, video');
    
    const mediaObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                if (element.dataset.src) {
                    element.src = element.dataset.src;
                    element.removeAttribute('data-src');
                }
                mediaObserver.unobserve(element);
            }
        });
    });
    
    mediaElements.forEach(element => {
        if (element.dataset.src) {
            mediaObserver.observe(element);
        }
    });
}); 

// Sermons and Testimonies JavaScript with Media Preview

// Preview Modal Functions with Media Playback
function showPreview(id, title, author, description, filePath, fileType, type, category, date, views, fileSize) {
    console.log('showPreview called with:', { id, title, author, fileType, type, filePath });
    
    const modal = document.getElementById('previewModal');
    if (!modal) {
        console.error('Preview modal not found!');
        return;
    }
    
    const thumbnail = document.getElementById('previewThumbnail');
    const icon = document.getElementById('previewIcon');
    const mediaSection = document.getElementById('previewMediaSection');
    const mediaPlayer = document.getElementById('previewMediaPlayer');
    const mediaLoading = document.getElementById('previewMediaLoading');
    
    // Set modal content
    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewAuthor').textContent = author;
    document.getElementById('previewDescription').textContent = description;
    document.getElementById('previewDate').textContent = date;
    document.getElementById('previewViews').textContent = views;
    document.getElementById('previewFileSize').textContent = fileSize;
    document.getElementById('previewFileType').textContent = getFileTypeDisplay(fileType);
    document.getElementById('previewCategory').textContent = category || 'General';
    
    // Set action buttons
    const playBtn = document.getElementById('previewPlayBtn');
    const downloadBtn = document.getElementById('previewDownloadBtn');
    
    playBtn.href = `view-content.php?id=${id}`;
    downloadBtn.href = filePath;
    
    // Check if it's a media file (video or audio)
    if (isVideo(fileType) || isAudio(fileType)) {
        // Show media player
        mediaSection.style.display = 'block';
        thumbnail.style.display = 'none';
        
        // Set media type info
        const mediaIcon = document.getElementById('previewMediaIcon');
        const mediaType = document.getElementById('previewMediaType');
        
        if (isVideo(fileType)) {
            mediaIcon.className = 'fas fa-play';
            mediaType.textContent = 'Video';
            playBtn.innerHTML = '<i class="fas fa-play"></i> Watch Full Video';
        } else if (isAudio(fileType)) {
            mediaIcon.className = 'fas fa-volume-up';
            mediaType.textContent = 'Audio';
            playBtn.innerHTML = '<i class="fas fa-volume-up"></i> Listen Full Audio';
        }
        
        // Create media element
        createMediaPlayer(filePath, fileType, mediaPlayer, mediaLoading);
        
    } else {
        // Show thumbnail for non-media files
        mediaSection.style.display = 'none';
        thumbnail.style.display = 'flex';
        setPreviewThumbnail(fileType, type);
        playBtn.innerHTML = '<i class="fas fa-eye"></i> View Full Content';
    }
    
    // Show modal
    modal.style.display = 'block';
    console.log('Modal should now be visible');
    
    // Add backdrop click to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closePreview();
        }
    });
    
    // Add escape key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closePreview();
        }
    });
}

function createMediaPlayer(filePath, fileType, mediaPlayer, mediaLoading) {
    // Clear existing content
    mediaPlayer.innerHTML = '';
    
    // Show loading
    mediaLoading.style.display = 'flex';
    
    if (isVideo(fileType)) {
        // Create video element
        const video = document.createElement('video');
        video.controls = true;
        video.preload = 'metadata';
        video.style.width = '100%';
        video.style.height = '100%';
        video.style.borderRadius = '10px';
        
        // Add video source
        const source = document.createElement('source');
        source.src = filePath;
        source.type = getVideoMimeType(fileType);
        video.appendChild(source);
        
        // Add error handling
        video.addEventListener('error', function() {
            showMediaError(mediaPlayer, 'Video could not be loaded');
        });
        
        // Add loaded metadata
        video.addEventListener('loadedmetadata', function() {
            mediaLoading.style.display = 'none';
            mediaPlayer.appendChild(video);
            
            // Set duration
            const duration = document.getElementById('previewMediaDuration');
            if (duration) {
                duration.textContent = formatTime(video.duration);
            }
        });
        
        // Add loading error
        video.addEventListener('error', function() {
            mediaLoading.style.display = 'none';
            showMediaError(mediaPlayer, 'Video could not be loaded');
        });
        
    } else if (isAudio(fileType)) {
        // Create audio element
        const audio = document.createElement('audio');
        audio.controls = true;
        audio.preload = 'metadata';
        audio.style.width = '100%';
        audio.style.height = '60px';
        audio.style.borderRadius = '10px';
        
        // Add audio source
        const source = document.createElement('source');
        source.src = filePath;
        source.type = getAudioMimeType(fileType);
        audio.appendChild(source);
        
        // Add error handling
        audio.addEventListener('error', function() {
            showMediaError(mediaPlayer, 'Audio could not be loaded');
        });
        
        // Add loaded metadata
        audio.addEventListener('loadedmetadata', function() {
            mediaLoading.style.display = 'none';
            mediaPlayer.appendChild(audio);
            
            // Set duration
            const duration = document.getElementById('previewMediaDuration');
            if (duration) {
                duration.textContent = formatTime(audio.duration);
            }
        });
        
        // Add loading error
        audio.addEventListener('error', function() {
            mediaLoading.style.display = 'none';
            showMediaError(mediaPlayer, 'Audio could not be loaded');
        });
    }
}

function showMediaError(mediaPlayer, message) {
    mediaPlayer.innerHTML = `
        <div class="preview-media-error">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Media Error</strong><br>
                ${message}
            </div>
        </div>
    `;
}

function getVideoMimeType(fileType) {
    const mimeTypes = {
        'mp4': 'video/mp4',
        'avi': 'video/avi',
        'mov': 'video/quicktime',
        'webm': 'video/webm'
    };
    return mimeTypes[fileType.toLowerCase()] || 'video/mp4';
}

function getAudioMimeType(fileType) {
    const mimeTypes = {
        'mp3': 'audio/mpeg',
        'wav': 'audio/wav',
        'ogg': 'audio/ogg',
        'aac': 'audio/aac'
    };
    return mimeTypes[fileType.toLowerCase()] || 'audio/mpeg';
}

function formatTime(seconds) {
    if (isNaN(seconds)) return '--:--';
    
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function closePreview() {
    console.log('closePreview called');
    const modal = document.getElementById('previewModal');
    if (modal) {
        // Stop any playing media
        const mediaPlayer = document.getElementById('previewMediaPlayer');
        if (mediaPlayer) {
            const video = mediaPlayer.querySelector('video');
            const audio = mediaPlayer.querySelector('audio');
            
            if (video) {
                video.pause();
                video.currentTime = 0;
            }
            
            if (audio) {
                audio.pause();
                audio.currentTime = 0;
            }
        }
        
        modal.style.display = 'none';
        console.log('Modal closed');
    }
}

function setPreviewThumbnail(fileType, contentType) {
    console.log('setPreviewThumbnail called with:', { fileType, contentType });
    
    const thumbnail = document.getElementById('previewThumbnail');
    const icon = document.getElementById('previewIcon');
    
    if (!thumbnail || !icon) {
        console.error('Thumbnail or icon elements not found!');
        return;
    }
    
    // Set background based on content type
    if (contentType === 'sermon') {
        thumbnail.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    } else {
        thumbnail.style.background = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
    }
    
    // Set icon based on file type
    if (isVideo(fileType)) {
        icon.className = 'fas fa-play';
    } else if (isAudio(fileType)) {
        icon.className = 'fas fa-volume-up';
    } else if (isPDF(fileType)) {
        icon.className = 'fas fa-file-pdf';
    } else if (isText(fileType)) {
        icon.className = 'fas fa-file-alt';
    } else {
        icon.className = 'fas fa-file';
    }
    
    console.log('Thumbnail set with icon:', icon.className);
}

function getFileTypeDisplay(fileType) {
    const fileTypeMap = {
        'video': 'Video',
        'mp4': 'Video',
        'avi': 'Video',
        'mov': 'Video',
        'audio': 'Audio',
        'mp3': 'Audio',
        'wav': 'Audio',
        'pdf': 'PDF Document',
        'text': 'Text Document',
        'txt': 'Text Document'
    };
    
    return fileTypeMap[fileType.toLowerCase()] || 'File';
}

function isVideo(fileType) {
    return ['video', 'mp4', 'avi', 'mov'].includes(fileType.toLowerCase());
}

function isAudio(fileType) {
    return ['audio', 'mp3', 'wav'].includes(fileType.toLowerCase());
}

function isPDF(fileType) {
    return fileType.toLowerCase() === 'pdf';
}

function isText(fileType) {
    return ['text', 'txt'].includes(fileType.toLowerCase());
}

// Enhanced card interactions
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing preview functionality...');
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.custom-card');
    console.log('Found', cards.length, 'content cards');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add click tracking for preview buttons
    const previewButtons = document.querySelectorAll('.btn-preview');
    console.log('Found', previewButtons.length, 'preview buttons');
    
    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Preview button clicked!');
            // Track preview interaction (you could send this to your server)
            console.log('Content previewed:', this.getAttribute('onclick'));
        });
    });
    
    // Add smooth scrolling for search results
    const searchForm = document.querySelector('.search-bar');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 100);
        });
    }
    
    // Add loading states for preview modal
    const previewModal = document.getElementById('previewModal');
    if (previewModal) {
        console.log('Preview modal found in DOM');
        previewModal.addEventListener('show', function() {
            // Add loading animation
            const thumbnail = document.getElementById('previewThumbnail');
            thumbnail.style.opacity = '0.7';
        });
        
        previewModal.addEventListener('shown', function() {
            // Remove loading animation
            const thumbnail = document.getElementById('previewThumbnail');
            thumbnail.style.opacity = '1';
        });
    } else {
        console.error('Preview modal not found in DOM!');
    }
    
    console.log('Preview functionality initialized successfully');
});

// Export functions for potential use in other scripts
window.PreviewManager = {
    showPreview,
    closePreview,
    setPreviewThumbnail,
    getFileTypeDisplay,
    isVideo,
    isAudio,
    isPDF,
    isText
};

// Global error handler for debugging
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
    console.error('Error details:', e);
});

// Enhanced media functionality
function playMedia(button) {
    const mediaContainer = button.closest('.media-container');
    const media = mediaContainer.querySelector('.media-preview');
    const overlay = mediaContainer.querySelector('.media-overlay');
    
    if (media) {
        // Pause all other media first
        const allMedia = document.querySelectorAll('.media-preview');
        allMedia.forEach(otherMedia => {
            if (otherMedia !== media && !otherMedia.paused) {
                otherMedia.pause();
            }
        });
        
        // Play the selected media
        media.play().then(() => {
            if (overlay) {
                overlay.classList.add('hidden');
            }
            media.classList.add('playing');
        }).catch(error => {
            console.error('Error playing media:', error);
            showMediaError(media, 'Failed to play media');
        });
    }
}

function playMediaInCard(button, filePath, fileType, title) {
    // Create a temporary modal for immediate playback
    const modal = document.createElement('div');
    modal.className = 'preview-modal';
    modal.style.display = 'block';
    modal.style.zIndex = '9999';
    
    const isVideo = fileType.toLowerCase().includes('video') || ['mp4', 'avi', 'mov'].includes(fileType.toLowerCase());
    const isAudio = fileType.toLowerCase().includes('audio') || ['mp3', 'wav', 'ogg'].includes(fileType.toLowerCase());
    
    if (isVideo || isAudio) {
        modal.innerHTML = `
            <div class="preview-modal-content" style="max-width: 800px; margin: 5% auto;">
                <div class="preview-header">
                    <h3 class="preview-title">${title}</h3>
                    <button class="preview-close" onclick="this.closest('.preview-modal').remove()">&times;</button>
                </div>
                <div class="preview-body">
                    <div class="preview-media-section">
                        <div class="preview-media-info">
                            <div class="preview-media-type">
                                <i class="fas fa-${isVideo ? 'play' : 'volume-up'}"></i>
                                <span>${isVideo ? 'Video' : 'Audio'}</span>
                            </div>
                        </div>
                        <div class="preview-media-player">
                            <${isVideo ? 'video' : 'audio'} controls style="width: 100%; ${isVideo ? 'max-height: 400px;' : 'height: 60px;'} border-radius: 10px;">
                                <source src="${filePath}" type="${isVideo ? 'video/mp4' : 'audio/mpeg'}">
                                Your browser does not support the ${isVideo ? 'video' : 'audio'} tag.
                            </${isVideo ? 'video' : 'audio'}>
                        </div>
                    </div>
                    <div class="preview-actions">
                        <a href="${filePath}" class="preview-btn preview-btn-secondary" download>
                            <i class="fas fa-download"></i> Download
                        </a>
                        <button class="preview-btn preview-btn-primary" onclick="this.closest('.preview-modal').remove()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Add backdrop click to close
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        // Add escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                modal.remove();
            }
        });
    }
}

// Track media interactions
function trackMediaInteraction(action, mediaType, title) {
    console.log(`Media ${action}:`, { mediaType, title, timestamp: new Date().toISOString() });
    // Here you could send analytics data to your server
} 

// Enable download button after media is viewed
    document.querySelectorAll('.media-preview').forEach(function(media) {
        // Get the card's download button ID from the parent card
        const card = media.closest('.custom-card');
        if (card) {
            const downloadBtn = card.querySelector('.download-btn');
            if (downloadBtn) {
                // Initially disable (redundant if already set in HTML, but safe)
                downloadBtn.setAttribute('disabled', 'disabled');
                // Enable after 50% of media is played
                let enabled = false;
                media.addEventListener('timeupdate', function() {
                    if (!enabled && media.duration && media.currentTime >= media.duration / 2) {
                        downloadBtn.removeAttribute('disabled');
                        enabled = true;
                    }
                });
            }
        }
    }); 