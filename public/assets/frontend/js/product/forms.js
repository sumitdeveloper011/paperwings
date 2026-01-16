/**
 * Product Forms Module
 * Handles review, question, and answer form submissions
 */
(function() {
    'use strict';

    function initForms() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Show notification function - use toast if available
        function showNotification(message, type = 'success') {
            if (typeof showToast !== 'undefined') {
                showToast(message, type, 5000);
            } else {
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
        }

        // Native Form Validation Helper
        function validateReviewForm(form) {
            const errors = {};
            const isGuest = !document.querySelector('meta[name="user-authenticated"]') || 
                            document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'false';

            // Rating validation
            const rating = form.querySelector('[name="rating"]:checked');
            if (!rating) {
                errors.rating = 'Please select a rating.';
            } else {
                const ratingValue = parseInt(rating.value);
                if (ratingValue < 1 || ratingValue > 5) {
                    errors.rating = 'Rating must be between 1 and 5 stars.';
                }
            }

            // Review validation
            const review = form.querySelector('[name="review"]');
            if (review) {
                const reviewValue = review.value.trim();
                if (!reviewValue) {
                    errors.review = 'Please write a review.';
                } else if (reviewValue.length < 10) {
                    errors.review = 'Review must be at least 10 characters.';
                } else if (reviewValue.length > 1000) {
                    errors.review = 'Review cannot exceed 1000 characters.';
                }
            }

            // Name validation (for guests)
            if (isGuest) {
                const name = form.querySelector('[name="name"]');
                if (name) {
                    const nameValue = name.value.trim();
                    if (!nameValue) {
                        errors.name = 'Please enter your name.';
                    } else if (nameValue.length < 2) {
                        errors.name = 'Name must be at least 2 characters.';
                    } else if (nameValue.length > 255) {
                        errors.name = 'Name cannot exceed 255 characters.';
                    } else if (!/^[a-zA-Z\s\-\'\.]+$/.test(nameValue)) {
                        errors.name = 'Name can only contain letters, spaces, hyphens, apostrophes, and periods.';
                    }
                }
            }

            // Email validation (for guests)
            if (isGuest) {
                const email = form.querySelector('[name="email"]');
                if (email) {
                    const emailValue = email.value.trim();
                    if (!emailValue) {
                        errors.email = 'Please enter your email.';
                    } else if (emailValue.length > 255) {
                        errors.email = 'Email cannot exceed 255 characters.';
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                        errors.email = 'Please enter a valid email address.';
                    }
                }
            }

            return errors;
        }

        function displayValidationErrors(form, errors) {
            // Clear previous errors
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            form.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });

            // Display new errors
            Object.keys(errors).forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const formGroup = input.closest('.review-form__field, .review-form__col, .form-group');
                    if (formGroup) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = errors[fieldName];
                        formGroup.appendChild(errorDiv);
                    }
                }
            });
        }

        // Review Form Submission
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const errors = validateReviewForm(this);
                if (Object.keys(errors).length > 0) {
                    displayValidationErrors(this, errors);
                    const firstErrorField = Object.keys(errors)[0];
                    const firstErrorInput = this.querySelector(`[name="${firstErrorField}"]`);
                    if (firstErrorInput) {
                        firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorInput.focus();
                    }
                    return;
                }

                submitReviewForm(this);
            });

            // Submit function
            function submitReviewForm(form) {
                const reviewUrl = form.getAttribute('data-review-url') || form.action;
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                fetch(reviewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    return response.json().then(data => ({
                        status: response.status,
                        data: data
                    }));
                })
                .then(({status, data}) => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;

                    if (status === 422) {
                        // Validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = form.querySelector(`[name="${field}"]`);
                                if (input) {
                                    const errorMsg = data.errors[field][0];
                                    const formGroup = input.closest('.review-form__field, .review-form__col');
                                    if (formGroup) {
                                        let errorDiv = formGroup.querySelector('.invalid-feedback');
                                        if (!errorDiv) {
                                            errorDiv = document.createElement('div');
                                            errorDiv.className = 'invalid-feedback';
                                            formGroup.appendChild(errorDiv);
                                        }
                                        errorDiv.textContent = errorMsg;
                                        errorDiv.style.display = 'block';
                                        input.classList.add('is-invalid');
                                    }
                                }
                            });
                        }
                        showNotification(data.message || 'Please correct the errors and try again.', 'error');
                    } else if (data.success) {
                        showNotification(data.message, 'success');
                        form.reset();
                        // Clear validation errors
                        form.querySelectorAll('.is-invalid').forEach(el => {
                            el.classList.remove('is-invalid');
                        });
                        form.querySelectorAll('.invalid-feedback').forEach(el => {
                            el.style.display = 'none';
                        });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification(data.message || 'Failed to submit review.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    showNotification('An error occurred. Please try again.', 'error');
                });
            }

        }

        // Question Form Validation Helper
        function validateQuestionForm(form) {
            const errors = {};
            const isGuest = !document.querySelector('meta[name="user-authenticated"]') || 
                            document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'false';

            // Question validation
            const question = form.querySelector('[name="question"]');
            if (question) {
                const questionValue = question.value.trim();
                if (!questionValue) {
                    errors.question = 'Please enter your question.';
                } else if (questionValue.length < 10) {
                    errors.question = 'Question must be at least 10 characters.';
                } else if (questionValue.length > 500) {
                    errors.question = 'Question cannot exceed 500 characters.';
                }
            }

            // Name validation (for guests)
            if (isGuest) {
                const name = form.querySelector('[name="name"]');
                if (name) {
                    const nameValue = name.value.trim();
                    if (!nameValue) {
                        errors.name = 'Please enter your name.';
                    } else if (nameValue.length < 2) {
                        errors.name = 'Name must be at least 2 characters.';
                    } else if (nameValue.length > 255) {
                        errors.name = 'Name cannot exceed 255 characters.';
                    }
                }
            }

            // Email validation (for guests)
            if (isGuest) {
                const email = form.querySelector('[name="email"]');
                if (email) {
                    const emailValue = email.value.trim();
                    if (!emailValue) {
                        errors.email = 'Please enter your email.';
                    } else if (emailValue.length > 255) {
                        errors.email = 'Email cannot exceed 255 characters.';
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                        errors.email = 'Please enter a valid email address.';
                    }
                }
            }

            return errors;
        }

        // Question Form Submission
        const questionForm = document.getElementById('questionForm');
        if (questionForm) {
            // Get URL from data attribute first, then fallback to action attribute
            let questionUrl = questionForm.getAttribute('data-question-url');
            if (!questionUrl) {
                questionUrl = questionForm.action || questionForm.getAttribute('action');
            }
            
            // Validate URL is set
            if (!questionUrl) {
                console.error('Question form URL is not set');
                return;
            }
            
            questionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const errors = validateQuestionForm(this);
                if (Object.keys(errors).length > 0) {
                    displayValidationErrors(this, errors);
                    const firstErrorField = Object.keys(errors)[0];
                    const firstErrorInput = this.querySelector(`[name="${firstErrorField}"]`);
                    if (firstErrorInput) {
                        firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorInput.focus();
                    }
                    return;
                }

                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';

                fetch(questionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    return response.json().then(data => ({
                        status: response.status,
                        data: data
                    }));
                })
                .then(({status, data}) => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;

                    if (status === 422) {
                        // Validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const input = questionForm.querySelector(`[name="${field}"]`);
                                if (input) {
                                    const errorMsg = data.errors[field][0];
                                    const formGroup = input.closest('.mb-3, .form-group');
                                    if (formGroup) {
                                        let errorDiv = formGroup.querySelector('.invalid-feedback');
                                        if (!errorDiv) {
                                            errorDiv = document.createElement('div');
                                            errorDiv.className = 'invalid-feedback';
                                            formGroup.appendChild(errorDiv);
                                        }
                                        errorDiv.textContent = errorMsg;
                                        errorDiv.style.display = 'block';
                                        input.classList.add('is-invalid');
                                    }
                                }
                            });
                        }
                        showNotification(data.message || 'Please correct the errors and try again.', 'error');
                    } else if (data.success) {
                        showNotification(data.message, 'success');
                        questionForm.reset();
                        // Clear validation errors
                        questionForm.querySelectorAll('.is-invalid').forEach(el => {
                            el.classList.remove('is-invalid');
                        });
                        questionForm.querySelectorAll('.invalid-feedback').forEach(el => {
                            el.style.display = 'none';
                        });
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification(data.message || 'Failed to submit question.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
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

