// Preloader
document.addEventListener('DOMContentLoaded', function() {
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(() => {
            preloader.classList.add('preloader--hidden');
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }, 2000);
    }
});

// Sidebar Toggle and Collapse Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const adminMain = document.querySelector('.admin-main');
    const adminFooter = document.querySelector('.admin-footer');
    const topbar = document.querySelector('.topbar');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');

    // Check localStorage for saved state
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const isMobile = window.innerWidth <= 1024;

    // Initialize sidebar state
    if (!isMobile) {
        sidebar.classList.add('show');
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            adminMain.classList.add('sidebar-collapsed');
            adminFooter.classList.add('sidebar-collapsed');
            topbar.classList.add('sidebar-collapsed');
        }
    }

    // Sidebar toggle functionality
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (isMobile) {
                // Mobile behavior: show/hide sidebar
                sidebar.classList.toggle('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.toggle('show');
                }
            } else {
                // Desktop behavior: collapse/expand sidebar
                sidebar.classList.toggle('collapsed');
                adminMain.classList.toggle('sidebar-collapsed');
                adminFooter.classList.toggle('sidebar-collapsed');
                topbar.classList.toggle('sidebar-collapsed');
                
                // Save state to localStorage
                const isNowCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isNowCollapsed);
            }
        });
    }

    // Close sidebar on mobile
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.remove('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        });
    }

    // Close sidebar when clicking overlay on mobile
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        const newIsMobile = window.innerWidth <= 1024;
        
        if (newIsMobile !== isMobile) {
            // Mobile/desktop breakpoint crossed
            if (newIsMobile) {
                // Switched to mobile
                sidebar.classList.remove('collapsed');
                adminMain.classList.remove('sidebar-collapsed');
                adminFooter.classList.remove('sidebar-collapsed');
                topbar.classList.remove('sidebar-collapsed');
                sidebar.classList.remove('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
            } else {
                // Switched to desktop
                sidebar.classList.add('show');
                const savedCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (savedCollapsed) {
                    sidebar.classList.add('collapsed');
                    adminMain.classList.add('sidebar-collapsed');
                    adminFooter.classList.add('sidebar-collapsed');
                    topbar.classList.add('sidebar-collapsed');
                }
            }
        }
    });
});

// User Dropdown Functionality
document.addEventListener('DOMContentLoaded', function() {
    const userDropdown = document.getElementById('userDropdown');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userDropdown && userDropdownMenu) {
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.style.opacity = '0';
                userDropdownMenu.style.visibility = 'hidden';
                userDropdownMenu.style.transform = 'translateY(-10px)';
            }
        });

        // Toggle dropdown on click
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = userDropdownMenu.style.opacity === '1';
            
            if (isVisible) {
                userDropdownMenu.style.opacity = '0';
                userDropdownMenu.style.visibility = 'hidden';
                userDropdownMenu.style.transform = 'translateY(-10px)';
            } else {
                userDropdownMenu.style.opacity = '1';
                userDropdownMenu.style.visibility = 'visible';
                userDropdownMenu.style.transform = 'translateY(0)';
            }
        });
    }
});

// Notification Functionality
document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    let notificationPollInterval;
    let isDropdownOpen = false;

    // Fetch notifications
    function fetchNotifications() {
        const notificationsUrl = document.getElementById('notificationBtn')?.dataset.url || '/admin/notifications';
        fetch(notificationsUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateNotificationBadge(data.unread_count);
                if (isDropdownOpen) {
                    renderNotifications(data.notifications);
                }
            }
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });
    }

    // Update notification badge
    function updateNotificationBadge(count) {
        if (count > 0) {
            notificationBadge.textContent = count > 99 ? '99+' : count;
            notificationBadge.style.display = 'block';
            markAllReadBtn.style.display = count > 0 ? 'block' : 'none';
        } else {
            notificationBadge.style.display = 'none';
            markAllReadBtn.style.display = 'none';
        }
    }

    // Render notifications list
    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            notificationList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications</p>
                </div>
            `;
            return;
        }

        const notificationsHtml = notifications.map(notif => {
            const statusClass = notif.status === 'pending' ? 'warning' : 
                               notif.status === 'delivered' ? 'success' : 'info';
            return `
                <div class="notification-item" data-order-id="${notif.id}">
                    <div class="notification-item__icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="notification-item__content">
                        <div class="notification-item__header">
                            <strong>New Order: ${notif.order_number}</strong>
                            <span class="notification-time">${notif.time_ago}</span>
                        </div>
                        <div class="notification-item__body">
                            <p>Customer: ${notif.customer_name}</p>
                            <p>Total: $${notif.total}</p>
                            <span class="status-badge status-badge--${statusClass}">${notif.status}</span>
                        </div>
                    </div>
                    <div class="notification-item__action">
                        <a href="${notif.url}" class="notification-view-btn" onclick="markNotificationAsRead(${notif.id})">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');

        notificationList.innerHTML = notificationsHtml;
    }

    // Mark notification as read
    window.markNotificationAsRead = function(orderId) {
        const markReadUrl = document.getElementById('notificationBtn')?.dataset.markReadUrl || '/admin/notifications';
        const url = markReadUrl.replace(':id', orderId);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    };

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const markAllReadUrl = document.getElementById('notificationBtn')?.dataset.markAllReadUrl || '/admin/notifications/read-all';
            fetch(markAllReadUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchNotifications();
                }
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }

    // Toggle notification dropdown
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            
            if (isDropdownOpen) {
                notificationDropdown.classList.add('show');
                fetchNotifications();
            } else {
                notificationDropdown.classList.remove('show');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
                isDropdownOpen = false;
            }
        });
    }

    // Initial fetch
    fetchNotifications();

    // Poll for new notifications every 30 seconds
    notificationPollInterval = setInterval(fetchNotifications, 30000);

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (notificationPollInterval) {
            clearInterval(notificationPollInterval);
        }
    });
});

// Active Navigation Highlighting
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href.replace('/admin', ''))) {
            link.classList.add('active');
        }
    });
});

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Toast notifications (if needed)
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <span class="toast-message">${message}</span>
            <button class="toast-close">&times;</button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        hideToast(toast);
    }, 5000);
    
    // Close button functionality
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
        hideToast(toast);
    });
}

function hideToast(toast) {
    toast.classList.remove('show');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

// Form validation helpers
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            isValid = false;
        } else {
            input.classList.remove('error');
        }
    });
    
    return isValid;
}

// AJAX helper function
function makeRequest(url, method = 'GET', data = null) {
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Request failed:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export functions for global use
window.AdminScripts = {
    showToast,
    validateForm,
    makeRequest,
    debounce,
    throttle
};

// ============================================
// Product Show Page - Image Modal Functions
// ============================================

function openImageModal(imageUrl) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    if (modal && modalImage) {
        modalImage.src = imageUrl;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Export image modal functions for global use
window.AdminScripts.openImageModal = openImageModal;
window.AdminScripts.closeImageModal = closeImageModal;
