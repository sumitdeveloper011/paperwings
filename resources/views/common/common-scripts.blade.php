<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<!-- Alpine.js -->
<script src="{{ asset('assets/js/alpine.min.js') }}"></script>

<!-- Toast Function -->
<script>
    function toast() {
        return {
            visible: false,
            title: '',
            message: '',
            type: 'info',
            timeout: null,
            
            show(title, message, type = 'info', duration = 5000) {
                this.title = title;
                this.message = message;
                this.type = type;
                this.visible = true;
                
                // Clear existing timeout
                if (this.timeout) {
                    clearTimeout(this.timeout);
                }
                
                // Auto hide after duration
                if (duration > 0) {
                    this.timeout = setTimeout(() => {
                        this.hide();
                    }, duration);
                }
            },
            
            hide() {
                this.visible = false;
                if (this.timeout) {
                    clearTimeout(this.timeout);
                    this.timeout = null;
                }
            },
            
            success(title, message, duration = 5000) {
                this.show(title, message, 'success', duration);
            },
            
            error(title, message, duration = 5000) {
                this.show(title, message, 'error', duration);
            },
            
            warning(title, message, duration = 5000) {
                this.show(title, message, 'warning', duration);
            },
            
            info(title, message, duration = 5000) {
                this.show(title, message, 'info', duration);
            }
        }
    }
    
    // Global toast function
    window.showToast = function(title, message, type = 'info', duration = 5000) {
        // Dispatch custom event to trigger toast
        window.dispatchEvent(new CustomEvent('show-toast', {
            detail: { title, message, type, duration }
        }));
    }
</script>
<script src="{{ asset('assets/js/admin-script.js') }}"></script>