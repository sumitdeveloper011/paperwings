/**
 * Admin Gallery Management Module
 * Handles gallery item operations: drag-drop reordering, set featured, etc.
 */

(function(window) {
    'use strict';

    const AdminGallery = {
        /**
         * Get CSRF token from meta tag
         */
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        /**
         * Make authenticated AJAX request
         */
        makeRequest: function(url, method = 'POST', data = null) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: data ? JSON.stringify(data) : null
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Request failed');
                    }).catch(() => {
                        throw new Error(`Request failed with status ${response.status}`);
                    });
                }
                return response.json();
            })
            .catch(error => {
                console.error('Gallery request error:', error);
                if (typeof showToast !== 'undefined') {
                    showToast(error.message || 'An error occurred. Please try again.', 'error');
                }
                throw error;
            });
        },

        /**
         * Initialize drag and drop reordering
         */
        initDragDrop: function(containerId, reorderUrl) {
            const itemGrid = document.getElementById(containerId);
            if (!itemGrid) return;

            let draggedElement = null;

            // Set up drag handlers on each card individually
            const cards = itemGrid.querySelectorAll('.gallery-item-card');
            cards.forEach((card) => {
                // Make sure card is draggable
                card.setAttribute('draggable', 'true');
                
                // Prevent buttons and forms from starting drag
                const buttons = card.querySelectorAll('button, form');
                buttons.forEach(btn => {
                    btn.setAttribute('draggable', 'false');
                    btn.addEventListener('mousedown', function(e) {
                        e.stopPropagation();
                    });
                    btn.addEventListener('dragstart', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }, true);
                });

                // Handle drag start - allow drag from anywhere except buttons/forms
                card.addEventListener('dragstart', function(e) {
                    // Don't start drag if clicking on button or form
                    const target = e.target;
                    if (target.closest('button, form, .gallery-item-card__actions')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                    
                    draggedElement = this;
                    this.style.opacity = '0.5';
                    this.classList.add('dragging');
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', this.dataset.itemId || '');
                }, false);

                card.addEventListener('dragend', function(e) {
                    if (draggedElement) {
                        draggedElement.style.opacity = '1';
                        draggedElement.classList.remove('dragging');
                        draggedElement = null;
                    }
                });
            });

            itemGrid.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'move';
                
                if (!draggedElement) return;

                const afterElement = AdminGallery.getDragAfterElement(itemGrid, e.clientY);
                
                if (afterElement == null) {
                    itemGrid.appendChild(draggedElement);
                } else {
                    itemGrid.insertBefore(draggedElement, afterElement);
                }
            });

            itemGrid.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!draggedElement) return;

                const items = Array.from(itemGrid.querySelectorAll('.gallery-item-card')).map((item, index) => ({
                    id: parseInt(item.dataset.itemId),
                    order: index + 1
                }));

                AdminGallery.makeRequest(reorderUrl, 'POST', { items: items })
                    .then(data => {
                        if (data.success) {
                            if (typeof showToast !== 'undefined') {
                                showToast('Items reordered successfully!', 'success');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Reorder error:', error);
                        if (typeof showToast !== 'undefined') {
                            showToast('Failed to reorder items. Please try again.', 'error');
                        }
                    });
            });
        },

        /**
         * Get element after which to insert dragged element
         */
        getDragAfterElement: function(container, y) {
            const draggableElements = [...container.querySelectorAll('.gallery-item-card:not(.dragging)')];
            
            if (draggableElements.length === 0) {
                return null;
            }

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        },

        /**
         * Initialize set featured button handlers
         */
        initSetFeatured: function(setFeaturedUrlTemplate) {
            document.querySelectorAll('.set-featured-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemUuid = this.dataset.itemUuid;
                    const url = setFeaturedUrlTemplate.replace('ITEM_UUID', itemUuid);
                    
                    AdminGallery.makeRequest(url, 'POST')
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Set featured error:', error);
                        });
                });
            });
        }
    };

    window.AdminGallery = AdminGallery;
})(window);
