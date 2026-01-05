/**
 * Product Forms Module
 * Handles review, question, and answer form submissions
 */
(function() {
    'use strict';

    function initForms() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Show notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `cart-notification cart-notification--${type}`;
            notification.textContent = message;
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px 20px; background: ' + (type === 'success' ? '#10b981' : '#ef4444') + '; color: white; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.3s';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Review Form Submission
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            const reviewUrl = reviewForm.getAttribute('data-review-url') || reviewForm.action;
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                fetch(reviewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        reviewForm.reset();
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification(data.message || 'Failed to submit review.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            });
        }

        // Question Form Submission
        const questionForm = document.getElementById('questionForm');
        if (questionForm) {
            const questionUrl = questionForm.getAttribute('data-question-url') || questionForm.action;
            questionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                fetch(questionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        questionForm.reset();
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification(data.message || 'Failed to submit question.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            });
        }

        // Answer Form Submissions
        document.querySelectorAll('.answer-form-inline').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const questionId = this.getAttribute('data-question-id');
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                fetch(`/question/${questionId}/answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        this.reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Failed to submit answer.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                });
            });
        });

        // Helpful Button
        document.querySelectorAll('.helpful-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const answerId = this.getAttribute('data-answer-id');
                fetch(`/answer/${answerId}/helpful`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.innerHTML = `<i class="fas fa-thumbs-up"></i> Helpful (${data.helpful_count})`;
                        this.disabled = true;
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForms);
    } else {
        initForms();
    }
})();

