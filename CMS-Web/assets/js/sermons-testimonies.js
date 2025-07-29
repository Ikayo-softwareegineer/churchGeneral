// assets/js/app.js - Church Management System JavaScript

class ChurchMediaSystem {
    constructor() {
        this.currentData = [];
        this.currentType = 'sermons';
        this.currentFilter = 'all';
        this.audioPlayer = null;
        this.currentAudio = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.setupAudioPlayer();
        this.loadInitialData();
    }
    
    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.handleSearch();
            }, 300));
        }
        
        // Filter buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('.filter-buttons .btn')) {
                this.handleFilter(e.target);
            }
        });
        
        // Tab switching
        const tabButtons = document.querySelectorAll('[data-bs-toggle="pill"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', (e) => {
                this.handleTabSwitch(e.target);
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardShortcuts(e);
        });
    }
    
    loadInitialData() {
        if (window.churchData) {
            this.currentData = window.churchData.sermons;
            this.currentType = 'sermons';
        }
    }
    
    handleTabSwitch(tab) {
        const targetId = tab.getAttribute('data-bs-target').replace('#', '');
        this.currentType = targetId;
        this.currentData = window.churchData[targetId] || [];
        
        // Update filter buttons for the new tab
        this.updateFilterButtons(targetId);
        
        // Reset search and filter
        this.resetFilters();
        
        // Add animation to new content
        this.animateContent(targetId);
    }
    
    updateFilterButtons(type) {
        const filterContainer = document.getElementById('filterButtons');
        let filterHTML = '';
        
        if (type === 'sermons') {
            filterHTML = `
                <button class="btn btn-outline-primary active" data-filter="all">All</button>
                <button class="btn btn-outline-primary" data-filter="recent">Recent</button>
                <button class="btn btn-outline-primary" data-filter="popular">Popular</button>
                <button class="btn btn-outline-primary" data-filter="series">Series</button>
            `;
        } else {
            filterHTML = `
                <button class="btn btn-outline-primary active" data-filter="all">All</button>
                <button class="btn btn-outline-primary" data-filter="recent">Recent</button>
                <button class="btn btn-outline-primary" data-filter="popular">Popular</button>
                <button class="btn btn-outline-primary" data-filter="healing">Healing</button>
            `;
        }
        
        filterContainer.innerHTML = filterHTML;
    }
    
    handleSearch() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        
        if (query === '') {
            this.showAllContent();
            return;
        }
        
        // Show loading state
        this.showLoadingState();
        
        // Simulate API call delay
        setTimeout(() => {
            const filteredData = this.searchContent(query);
            this.updateContentDisplay(filteredData);
            this.hideLoadingState();
        }, 300);
    }
    
    searchContent(query) {
        return this.currentData.filter(item => {
            return (
                item.title.toLowerCase().includes(query) ||
                item.description.toLowerCase().includes(query) ||
                (item.speaker && item.speaker.toLowerCase().includes(query)) ||
                (item.author && item.author.toLowerCase().includes(query)) ||
                item.tags.some(tag => tag.toLowerCase().includes(query))
            );
        });
    }
    
    handleFilter(button) {
        // Update active filter button
        document.querySelectorAll('.filter-buttons .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');
        
        this.currentFilter = button.dataset.filter;
        
        // Show loading state
        this.showLoadingState();
        
        // Apply filter
        setTimeout(() => {
            const filteredData = this.filterContent(this.currentFilter);
            this.updateContentDisplay(filteredData);
            this.hideLoadingState();
        }, 200);
    }
    
    filterContent(filter) {
        let filteredData = [...this.currentData];
        
        switch (filter) {
            case 'recent':
                filteredData.sort((a, b) => new Date(b.created_at || b.date) - new Date(a.created_at || a.date));
                break;
                
            case 'popular':
                filteredData.sort((a, b) => b.views - a.views);
                break;
                
            case 'series':
                filteredData = filteredData.filter(item => item.series);
                break;
                
            case 'healing':
                filteredData = filteredData.filter(item => 
                    item.tags.some(tag => 
                        ['Healing', 'Miracle', 'Recovery', 'Deliverance'].includes(tag)
                    )
                );
                break;
                
            default:
                // 'all' - no filtering needed
                break;
        }
        
        return filteredData;
    }
    
    updateContentDisplay(data) {
        const gridId = this.currentType + 'Grid';
        const grid = document.getElementById(gridId);
        
        if (!grid) return;
        
        if (data.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="no-results">
                        <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                        <h4>No results found</h4>
                        <p class="text-muted">Try adjusting your search or filter criteria</p>
                    </div>
                </div>
            `;
            return;
        }
        
        grid.innerHTML = '';
        
        data.forEach((item, index) => {
            const card = this.createContentCard(item, this.currentType === 'sermons');
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
            grid.appendChild(card);
        });
    }
    
    createContentCard(item, isSermon) {
        const col = document.createElement('div');
        col.className = 'col-lg-4 col-md-6 mb-4 content-item';
        col.setAttribute('data-tags', item.tags.join(','));
        
        const speakerOrAuthor = isSermon ? item.speaker : item.author;
        const typeLabel = isSermon ? 'Speaker' : 'Shared by';
        
        col.innerHTML = `
            <div class="card content-card h-100">
                <div class="card-img-container">
                    <img src="${item.thumbnail}" class="card-img-top" alt="${this.escapeHtml(item.title)}" 
                         onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="play-overlay">
                        <button class="btn btn-play" onclick="churchMedia.playContent(${item.id}, '${this.currentType}')">
                            <i class="fas fa-play"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">${this.escapeHtml(item.title)}</h5>
                    <div class="card-meta mb-2">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>${this.escapeHtml(speakerOrAuthor)}
                            <i class="fas fa-calendar ms-3 me-1"></i>${this.formatDate(item.date)}
                        </small>
                    </div>
                    <div class="tags mb-2">
                        ${item.tags.map(tag => `<span class="badge tag-badge">${this.escapeHtml(tag)}</span>`).join('')}
                    </div>
                    <p class="card-text flex-grow-1">${this.escapeHtml(item.description)}</p>
                    <div class="stats mb-3">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>${item.duration}
                            <i class="fas fa-eye ms-3 me-1"></i>${this.formatNumber(item.views)}
                            <i class="fas fa-heart ms-3 me-1"></i>${item.likes}
                        </small>
                    </div>
                    <div class="card-actions d-flex gap-2">
                        <button class="btn btn-primary flex-fill" onclick="churchMedia.playContent(${item.id}, '${this.currentType}')">
                            <i class="fas fa-play me-1"></i>Play
                        </button>
                        <button class="btn btn-outline-secondary" onclick="churchMedia.shareContent(${item.id}, '${this.currentType}')">
                            <i class="fas fa-share"></i>
                        </button>
                        <button class="btn btn-outline-secondary" onclick="churchMedia.addToFavorites(${item.id}, '${this.currentType}')">
                            <i class="fas fa-bookmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        return col;
    }
    
    playContent(id, type) {
        const data = window.churchData[type];
        const item = data.find(item => item.id === id);
        
        if (!item) {
            this.showNotification('Content not found', 'error');
            return;
        }
        
        this.openAudioPlayer(item, type);
        this.updatePlayCount(id, type);
    }
    
    openAudioPlayer(item, type) {
        const modal = new bootstrap.Modal(document.getElementById('audioPlayerModal'));
        const modalContent = document.getElementById('audioPlayerContent');
        
        const speakerOrAuthor = type === 'sermons' ? item.speaker : item.author;
        
        modalContent.innerHTML = `
            <div class="audio-player">
                <div class="audio-info">
                    <div class="audio-title">${this.escapeHtml(item.title)}</div>
                    <div class="audio-meta">by ${this.escapeHtml(speakerOrAuthor)} • ${item.duration}</div>
                </div>
                
                <div class="audio-controls">
                    <button class="btn btn-outline-secondary" onclick="churchMedia.previousTrack()">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button class="btn btn-primary" id="playPauseBtn" onclick="churchMedia.togglePlayPause()">
                        <i class="fas fa-play"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="churchMedia.nextTrack()">
                        <i class="fas fa-step-forward"></i>
                    </button>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar" onclick="churchMedia.seekAudio(event)">
                        <div class="progress-fill" id="audioProgress"></div>
                    </div>
                    <div class="time-info">
                        <span id="currentTime">0:00</span>
                        <span id="totalTime">${item.duration}</span>
                    </div>
                </div>
                
                <div class="volume-control">
                    <i class="fas fa-volume-down"></i>
                    <input type="range" class="volume-slider" min="0" max="100" value="80" 
                           onchange="churchMedia.setVolume(this.value)">
                    <i class="fas fa-volume-up"></i>
                </div>
            </div>
        `;
        
        modal.show();
        this.initializeAudioPlayer(item);
    }
    
    initializeAudioPlayer(item) {
        // In a real application, you would load the actual audio file
        // For demonstration, we'll simulate audio playback
        this.currentAudio = {
            item: item,
            isPlaying: false,
            currentTime: 0,
            duration: this.parseTimeToSeconds(item.duration)
        };
        
        this.showNotification(`Now playing: ${item.title}`, 'success');
    }
    
    togglePlayPause() {
        const playPauseBtn = document.getElementById('playPauseBtn');
        const icon = playPauseBtn.querySelector('i');
        
        if (this.currentAudio.isPlaying) {
            // Pause
            icon.className = 'fas fa-play';
            this.currentAudio.isPlaying = false;
            this.showNotification('Paused', 'info');
        } else {
            // Play
            icon.className = 'fas fa-pause';
            this.currentAudio.isPlaying = true;
            this.startAudioProgress();
            this.showNotification('Playing', 'success');
        }
    }
    
    startAudioProgress() {
        if (!this.currentAudio.isPlaying) return;
        
        const progressFill = document.getElementById('audioProgress');
        const currentTimeSpan = document.getElementById('currentTime');
        
        if (progressFill && currentTimeSpan) {
            this.currentAudio.currentTime += 1;
            const percentage = (this.currentAudio.currentTime / this.currentAudio.duration) * 100;
            
            progressFill.style.width = percentage + '%';
            currentTimeSpan.textContent = this.formatTime(this.currentAudio.currentTime);
            
            if (this.currentAudio.currentTime < this.currentAudio.duration) {
                setTimeout(() => this.startAudioProgress(), 1000);
            } else {
                this.currentAudio.isPlaying = false;
                document.getElementById('playPauseBtn').querySelector('i').className = 'fas fa-play';
            }
        }
    }
    
    seekAudio(event) {
        if (!this.currentAudio) return;
        
        const progressBar = event.currentTarget;
        const rect = progressBar.getBoundingClientRect();
        const percentage = (event.clientX - rect.left) / rect.width;
        
        this.currentAudio.currentTime = Math.floor(percentage * this.currentAudio.duration);
        
        const progressFill = document.getElementById('audioProgress');
        const currentTimeSpan = document.getElementById('currentTime');
        
        if (progressFill && currentTimeSpan) {
            progressFill.style.width = (percentage * 100) + '%';
            currentTimeSpan.textContent = this.formatTime(this.currentAudio.currentTime);
        }
    }
    
    setVolume(volume) {
        // In a real application, you would set the actual audio volume
        this.showNotification(`Volume set to ${volume}%`, 'info');
    }
    
    previousTrack() {
        this.showNotification('Previous track functionality', 'info');
    }
    
    nextTrack() {
        this.showNotification('Next track functionality', 'info');
    }
    
    shareContent(id, type) {
        const data = window.churchData[type];
        const item = data.find(item => item.id === id);
        
        if (!item) return;
        
        if (navigator.share) {
            navigator.share({
                title: item.title,
                text: item.description,
                url: window.location.href + `#${type}-${id}`
            }).then(() => {
                this.showNotification('Content shared successfully!', 'success');
            }).catch((error) => {
                this.fallbackShare(item);
            });
        } else {
            this.fallbackShare(item);
        }
    }
    
    fallbackShare(item) {
        const shareUrl = window.location.href + `#${item.id}`;
        navigator.clipboard.writeText(shareUrl).then(() => {
            this.showNotification('Link copied to clipboard!', 'success');
        }).catch(() => {
            this.showNotification('Unable to share. Please copy the URL manually.', 'error');
        });
    }
    
    addToFavorites(id, type) {
        // Make AJAX call to PHP backend
        fetch(`?action=favorite&id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification('Added to favorites!', 'success');
                    this.updateFavoriteButton(id, type);
                } else {
                    this.showNotification('Failed to add to favorites', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Network error', 'error');
            });
    }
    
    updatePlayCount(id, type) {
        // Update play count via AJAX
        fetch(`?action=play&id=${id}&type=${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Play count updated');
                }
            })
            .catch(error => {
                console.error('Error updating play count:', error);
            });
    }
    
    updateFavoriteButton(id, type) {
        const button = document.querySelector(`button[onclick="churchMedia.addToFavorites(${id}, '${type}')"]`);
        if (button) {
            button.innerHTML = '<i class="fas fa-bookmark text-warning"></i>';
            button.classList.add('favorited');
        }
    }
    
    showLoadingState() {
        const grids = document.querySelectorAll('[id$="Grid"]');
        grids.forEach(grid => {
            if (grid.closest('.tab-pane.active')) {
                grid.innerHTML = `
                    <div class="col-12">
                        <div class="loading-spinner active">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading content...</p>
                        </div>
                    </div>
                `;
            }
        });
    }
    
    hideLoadingState() {
        const spinners = document.querySelectorAll('.loading-spinner');
        spinners.forEach(spinner => {
            spinner.classList.remove('active');
        });
    }
    
    showAllContent() {
        this.updateContentDisplay(this.currentData);
    }
    
    resetFilters() {
        document.getElementById('searchInput').value = '';
        this.currentFilter = 'all';
        
        document.querySelectorAll('.filter-buttons .btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'all') {
                btn.classList.add('active');
            }
        });
    }
    
    animateContent(tabId) {
        const tabPane = document.getElementById(tabId);
        if (tabPane) {
            tabPane.classList.add('slide-up');
            setTimeout(() => {
                tabPane.classList.remove('slide-up');
            }, 500);
        }
    }
    
    handleKeyboardShortcuts(e) {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'f':
                case 'F':
                    e.preventDefault();
                    document.getElementById('searchInput').focus();
                    break;
                case ' ':
                    e.preventDefault();
                    if (this.currentAudio && this.currentAudio.isPlaying !== undefined) {
                        this.togglePlayPause();
                    }
                    break;
            }
        }
        
        if (e.key === 'Escape') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('audioPlayerModal'));
            if (modal) {
                modal.hide();
            }
        }
    }
    
    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
    
    // Utility functions
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num);
    }
    
    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    parseTimeToSeconds(timeString) {
        const parts = timeString.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }
    
  }