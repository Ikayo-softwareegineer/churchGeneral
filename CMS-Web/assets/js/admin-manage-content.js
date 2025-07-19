// Admin Manage Content JavaScript Functions

function viewContent(contentId) {
    // Open content in new tab
    window.open('sermons&testimonies.php?view=' + contentId, '_blank');
}

function editContent(contentId) {
    // Redirect to edit page (you can create this later)
    alert('Edit functionality coming soon!');
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function confirmDelete() {
    // This would be handled by the form submission
    closeModal();
}

function showDeleteConfirmation(contentId, contentTitle) {
    const modal = document.getElementById('deleteModal');
    const modalContent = modal.querySelector('.modal-content p');
    modalContent.textContent = `Are you sure you want to delete "${contentTitle}"? This action cannot be undone.`;
    
    // Update the delete form action
    const deleteForm = modal.querySelector('form');
    const deleteInput = deleteForm.querySelector('input[name="delete_id"]');
    deleteInput.value = contentId;
    
    modal.style.display = 'block';
}

function closeModalOnOutsideClick(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeModal();
    }
}

function initializeAdminPage() {
    // Show modal when delete button is clicked
    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this content?')) {
                e.preventDefault();
            }
        });
    });

    // Close modal when clicking outside
    window.addEventListener('click', closeModalOnOutsideClick);

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Add smooth scrolling to pagination
    document.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!this.classList.contains('active')) {
                // Smooth scroll to top of content table
                document.querySelector('.content-table').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add search functionality with debounce
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Auto-submit search form after 500ms of no typing
                this.closest('form').submit();
            }, 500);
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + N for new content
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            window.location.href = 'admin-upload.php';
        }
        
        // Ctrl/Cmd + S for search focus
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });

    // Add tooltips for action buttons
    const actionButtons = document.querySelectorAll('.action-buttons .btn');
    actionButtons.forEach(btn => {
        const icon = btn.querySelector('i');
        if (icon) {
            const action = icon.className.includes('eye') ? 'View' :
                          icon.className.includes('edit') ? 'Edit' :
                          icon.className.includes('trash') ? 'Delete' :
                          icon.className.includes('pause') ? 'Deactivate' :
                          icon.className.includes('play') ? 'Activate' : '';
            
            if (action) {
                btn.title = action + ' Content';
            }
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeAdminPage);

// Export functions for potential use in other scripts
window.AdminContentManager = {
    viewContent,
    editContent,
    closeModal,
    confirmDelete,
    showDeleteConfirmation,
    initializeAdminPage
}; 