// Share content function
function shareContent() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            text: getContentDescription(),
            url: window.location.href
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        copyToClipboard(window.location.href);
    }
}

// Add to favorites function
function addToFavorites() {
    // This would typically make an AJAX call to save to favorites
    showNotification('Added to favorites! (Feature coming soon)', 'success');
}

// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Link copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

// Fallback copy to clipboard for older browsers
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showNotification('Link copied to clipboard!', 'success');
    } catch (err) {
        showNotification('Failed to copy link', 'error');
    }
    
    document.body.removeChild(textArea);
}

// Get content description for sharing
function getContentDescription() {
    const descriptionElement = document.querySelector('.content-description');
    if (descriptionElement) {
        const text = descriptionElement.textContent || descriptionElement.innerText;
        return text.length > 100 ? text.substring(0, 100) + '...' : text;
    }
    return 'Check out this content from Church CMS';
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Auto-play video when it's loaded (optional)
document.addEventListener('DOMContentLoaded', function() {
    const video = document.querySelector('video');
    if (video) {
        video.addEventListener('loadedmetadata', function() {
            // Video is ready to play
            console.log('Video loaded successfully');
        });
        
        // Add custom video controls if needed
        video.addEventListener('play', function() {
            console.log('Video started playing');
        });
        
        video.addEventListener('pause', function() {
            console.log('Video paused');
        });
    }
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Space bar to play/pause video
        if (e.code === 'Space' && video) {
            e.preventDefault();
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        }
        
        // Escape to go back
        if (e.code === 'Escape') {
            const backButton = document.querySelector('.back-button');
            if (backButton) {
                backButton.click();
            }
        }
    });
    
    // Add download tracking
    const downloadButtons = document.querySelectorAll('a[download]');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Track download (you could send this to your server)
            console.log('Content downloaded:', this.href);
        });
    });
    
    // Add view tracking for related content
    const relatedLinks = document.querySelectorAll('.related-card');
    relatedLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Track related content clicks
            console.log('Related content clicked:', this.href);
        });
    });
});

// Add smooth scrolling for better UX
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Add loading states for better UX
function showLoadingState() {
    const mediaPlayer = document.querySelector('.media-player');
    if (mediaPlayer) {
        mediaPlayer.innerHTML = `
            <div style="text-align: center; padding: 3rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-color);"></i>
                <p>Loading content...</p>
            </div>
        `;
    }
}

// Add error handling for media
function handleMediaError(element) {
    element.innerHTML = `
        <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 15px;">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
            <h3>Media Error</h3>
            <p>Unable to load the media file. Please try again later.</p>
        </div>
    `;
} 