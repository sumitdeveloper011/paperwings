<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initialize Bootstrap Datepicker
    $('#start_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true
    });

    $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true
    });

    // Set datepicker values if input already has values (for old values from validation errors)
    const startDateValue = $('#start_date').val();
    if (startDateValue) {
        const convertedStart = convertISOToDisplay(startDateValue);
        if (convertedStart && convertedStart !== startDateValue) {
            $('#start_date').val(convertedStart);
            // Try to parse and set in datepicker
            const parts = convertedStart.split('-');
            if (parts.length === 3 && parts[0].length === 2) {
                const dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(dateObj.getTime())) {
                    $('#start_date').datepicker('setDate', dateObj);
                }
            }
        } else if (convertedStart && convertedStart === startDateValue) {
            // Already in dd-mm-yyyy format, set in datepicker
            const parts = convertedStart.split('-');
            if (parts.length === 3 && parts[0].length === 2) {
                const dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(dateObj.getTime())) {
                    $('#start_date').datepicker('setDate', dateObj);
                }
            }
        }
    }

    const endDateValue = $('#end_date').val();
    if (endDateValue) {
        const convertedEnd = convertISOToDisplay(endDateValue);
        if (convertedEnd && convertedEnd !== endDateValue) {
            $('#end_date').val(convertedEnd);
            // Try to parse and set in datepicker
            const parts = convertedEnd.split('-');
            if (parts.length === 3 && parts[0].length === 2) {
                const dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(dateObj.getTime())) {
                    $('#end_date').datepicker('setDate', dateObj);
                }
            }
        } else if (convertedEnd && convertedEnd === endDateValue) {
            // Already in dd-mm-yyyy format, set in datepicker
            const parts = convertedEnd.split('-');
            if (parts.length === 3 && parts[0].length === 2) {
                const dateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!isNaN(dateObj.getTime())) {
                    $('#end_date').datepicker('setDate', dateObj);
                }
            }
        }
    }

    // Update end date min date when start date changes
    $('#start_date').on('changeDate', function(e) {
        const startDate = e.date;
        if (startDate) {
            const minDate = new Date(startDate);
            minDate.setDate(minDate.getDate() + 1);
            $('#end_date').datepicker('setStartDate', minDate);
            // If end date is before new min date, clear it
            const endDate = $('#end_date').datepicker('getDate');
            if (endDate && endDate <= startDate) {
                $('#end_date').datepicker('setDate', null);
            }
        }
    });

    // Trigger datepicker on icon click
    $('.datepicker-trigger').on('click', function() {
        const target = $(this).data('target');
        $(target).datepicker('show');
    });

    // Convert date formats
    function convertDateToISO(dateString) {
        if (!dateString) return '';
        const parts = dateString.split('-');
        if (parts.length === 3) {
            // Check if it's already in yyyy-mm-dd format (year is 4 digits)
            if (parts[0].length === 4) {
                return dateString; // Already in ISO format
            }
            // Convert dd-mm-yyyy to yyyy-mm-dd
            const day = parts[0].padStart(2, '0');
            const month = parts[1].padStart(2, '0');
            const year = parts[2];
            return `${year}-${month}-${day}`;
        }
        return dateString;
    }

    function convertISOToDisplay(dateString) {
        if (!dateString) return '';
        const parts = dateString.split('-');
        if (parts.length === 3) {
            // Check if it's in yyyy-mm-dd format (year is 4 digits at start)
            if (parts[0].length === 4) {
                return `${parts[2]}-${parts[1]}-${parts[0]}`; // Convert to dd-mm-yyyy
            }
            // Already in dd-mm-yyyy format
            return dateString;
        }
        return dateString;
    }

    // Form validation
    const form = document.getElementById('couponForm');
    if (form) {
        // Validate numeric fields
        function validateNumericField(input, min, max, fieldName, isInteger = false) {
            const value = input.value.trim();
            if (value === '') {
                return true; // Allow empty for optional fields
            }

            // Check if it's a valid number
            const numValue = isInteger ? parseInt(value, 10) : parseFloat(value);
            if (isNaN(numValue) || (isInteger && (value.includes('.') || value.includes(',') || !Number.isInteger(numValue)))) {
                // Only add client-side error if there's no server-side error already
                const serverError = input.parentElement.querySelector('.invalid-feedback:not(.field-error)');
                if (!serverError) {
                    input.classList.add('is-invalid');
                    let errorDiv = input.parentElement.querySelector('.field-error');
                    if (!errorDiv) {
                        errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback field-error';
                        input.parentElement.appendChild(errorDiv);
                    }
                    errorDiv.textContent = fieldName + ' must be a valid ' + (isInteger ? 'whole number' : 'number') + '.';
                }
                return false;
            }

            // Remove client-side error if validation passes (but keep server-side errors)
            const clientError = input.parentElement.querySelector('.field-error');
            if (clientError) {
                clientError.remove();
            }
            // Only remove is-invalid if there's no server-side error
            const serverError = input.parentElement.querySelector('.invalid-feedback:not(.field-error)');
            if (!serverError) {
                input.classList.remove('is-invalid');
            }

            if (min !== undefined && numValue < min) {
                input.classList.add('is-invalid');
                let errorDiv = input.parentElement.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback field-error';
                    input.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = fieldName + ' must be at least ' + min + '.';
                return false;
            }

            if (max !== undefined && numValue > max) {
                input.classList.add('is-invalid');
                let errorDiv = input.parentElement.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback field-error';
                    input.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = fieldName + ' cannot exceed ' + max + '.';
                return false;
            }

            input.classList.remove('is-invalid');
            const errorDiv = input.parentElement.querySelector('.field-error');
            if (errorDiv) {
                errorDiv.remove();
            }
            return true;
        }

        // Validate percentage value
        function validateDiscountValue() {
            const type = document.getElementById('type').value;
            const valueInput = document.getElementById('value');
            const value = parseFloat(valueInput.value);

            if (type === 'percentage' && value > 100) {
                valueInput.classList.add('is-invalid');
                let errorDiv = valueInput.parentElement.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback field-error';
                    valueInput.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Percentage discount cannot exceed 100%.';
                return false;
            }

            if (type === 'fixed' && value > 100000) {
                valueInput.classList.add('is-invalid');
                let errorDiv = valueInput.parentElement.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback field-error';
                    valueInput.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Fixed discount amount cannot exceed $100,000.';
                return false;
            }

            return true;
        }

        // Validate maximum discount (only for percentage)
        function validateMaximumDiscount() {
            const type = document.getElementById('type').value;
            const maxDiscountInput = document.getElementById('maximum_discount');
            const value = maxDiscountInput.value.trim();

            if (value === '') {
                maxDiscountInput.classList.remove('is-invalid');
                const errorDiv = maxDiscountInput.parentElement.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
                return true;
            }

            if (type !== 'percentage') {
                maxDiscountInput.classList.add('is-invalid');
                let errorDiv = maxDiscountInput.parentElement.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback field-error';
                    maxDiscountInput.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Maximum discount can only be set for percentage type coupons.';
                return false;
            }

            return true;
        }

        // Show temporary error message
        function showTemporaryError(input, message) {
            // Remove existing temporary error
            const existingError = input.parentElement.querySelector('.temp-error');
            if (existingError) {
                existingError.remove();
            }

            // Add temporary error
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback temp-error';
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            input.parentElement.appendChild(errorDiv);

            // Remove error after 2 seconds
            setTimeout(() => {
                if (errorDiv.parentElement) {
                    errorDiv.remove();
                }
                // Only remove is-invalid if there's no server-side error
                const serverError = input.parentElement.querySelector('.invalid-feedback:not(.temp-error):not(.field-error)');
                if (!serverError) {
                    input.classList.remove('is-invalid');
                }
            }, 2000);
        }

        // Restrict invalid characters while typing
        function restrictInvalidInput(input, isInteger = false, fieldName = '') {
            input.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);

                // Allow: backspace, delete, tab, escape, enter, decimal point (for non-integer)
                if ([8, 9, 27, 13, 46, 110, 190].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }

                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                    const message = isInteger
                        ? fieldName + ' must be a whole number (0-9 only).'
                        : fieldName + ' must be a number (0-9 and decimal point only).';
                    showTemporaryError(input, message);
                    return false;
                }

                // For integer fields, don't allow decimal point
                if (isInteger && (char === '.' || char === ',')) {
                    e.preventDefault();
                    showTemporaryError(input, fieldName + ' must be a whole number (no decimal point allowed).');
                    return false;
                }

                // For decimal fields, allow only one decimal point
                if (!isInteger && (char === '.' || char === ',')) {
                    const currentValue = input.value;
                    if (currentValue.indexOf('.') !== -1 || currentValue.indexOf(',') !== -1) {
                        e.preventDefault();
                        showTemporaryError(input, fieldName + ' can only have one decimal point.');
                        return false;
                    }
                }
            });

            // Handle paste events
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const originalPaste = paste;

                // Remove invalid characters
                let cleaned = paste.replace(/[^0-9.]/g, '');

                // For integer fields, remove decimal point
                if (isInteger) {
                    cleaned = cleaned.replace(/[.,]/g, '');
                } else {
                    // For decimal fields, allow only one decimal point
                    const parts = cleaned.split('.');
                    if (parts.length > 2) {
                        cleaned = parts[0] + '.' + parts.slice(1).join('');
                    }
                }

                // Show error if invalid characters were removed
                if (cleaned !== originalPaste) {
                    const message = isInteger
                        ? fieldName + ' must be a whole number. Invalid characters removed.'
                        : fieldName + ' must be a number. Invalid characters removed.';
                    showTemporaryError(input, message);
                }

                // Insert cleaned value at cursor position
                const start = input.selectionStart;
                const end = input.selectionEnd;
                const currentValue = input.value;
                const newValue = currentValue.substring(0, start) + cleaned + currentValue.substring(end);
                input.value = newValue;

                // Set cursor position
                const newCursorPos = start + cleaned.length;
                input.setSelectionRange(newCursorPos, newCursorPos);

                // Trigger validation
                input.dispatchEvent(new Event('input'));
            });

            // Handle input event to remove invalid characters (separate handler)
            input.addEventListener('input', function(e) {
                let value = input.value;
                const originalValue = value;
                const cursorPos = input.selectionStart;

                // Remove invalid characters
                if (isInteger) {
                    // For integer: only allow digits
                    value = value.replace(/[^0-9]/g, '');
                } else {
                    // For decimal: allow digits and one decimal point
                    value = value.replace(/[^0-9.]/g, '');
                    // Ensure only one decimal point
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                }

                // Update value if changed
                if (value !== originalValue) {
                    input.value = value;
                    // Restore cursor position (adjust for removed characters)
                    const removedChars = originalValue.length - value.length;
                    const newCursorPos = Math.max(0, cursorPos - removedChars);
                    input.setSelectionRange(newCursorPos, newCursorPos);

                    // Show error if characters were removed
                    if (removedChars > 0) {
                        const message = isInteger
                            ? fieldName + ' must be a whole number. Invalid characters removed.'
                            : fieldName + ' must be a number. Invalid characters removed.';
                        showTemporaryError(input, message);
                    }
                }
            }, { capture: true });
        }

        // Add validation listeners
        const numericFields = [
            { id: 'value', min: 0, max: null, name: 'Discount value', isInteger: false },
            { id: 'minimum_amount', min: 0, max: 1000000, name: 'Minimum amount', isInteger: false },
            { id: 'maximum_discount', min: 0, max: 100000, name: 'Maximum discount', isInteger: false },
            { id: 'usage_limit', min: 1, max: 999999, name: 'Usage limit', isInteger: true },
            { id: 'usage_limit_per_user', min: 1, max: 999999, name: 'Usage limit per user', isInteger: true }
        ];

        numericFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (input) {
                // Restrict invalid input while typing (pass field name for error messages)
                restrictInvalidInput(input, field.isInteger, field.name);

                // Validate on blur (after input restriction)
                input.addEventListener('blur', function() {
                    validateNumericField(input, field.min, field.max, field.name, field.isInteger);
                });

                // Validate after input restriction (use setTimeout to run after input handler)
                input.addEventListener('input', function() {
                    setTimeout(() => {
                        validateNumericField(input, field.min, field.max, field.name, field.isInteger);
                    }, 0);
                });
            }
        });

        // Validate discount value based on type
        const typeSelect = document.getElementById('type');
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                validateDiscountValue();
                validateMaximumDiscount();
            });
        }

        const valueInput = document.getElementById('value');
        if (valueInput) {
            valueInput.addEventListener('input', validateDiscountValue);
            valueInput.addEventListener('blur', validateDiscountValue);
        }

        const maxDiscountInput = document.getElementById('maximum_discount');
        if (maxDiscountInput) {
            maxDiscountInput.addEventListener('input', validateMaximumDiscount);
            maxDiscountInput.addEventListener('blur', validateMaximumDiscount);
        }

        const allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(input => {
            input.addEventListener('invalid', function(ev) {
                ev.preventDefault();
                ev.stopPropagation();
            }, true);
        });

        form.addEventListener('submit', function(e) {
            // Convert dates before submission
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');

            if (startDateInput && startDateInput.value) {
                startDateInput.value = convertDateToISO(startDateInput.value);
            }
            if (endDateInput && endDateInput.value) {
                endDateInput.value = convertDateToISO(endDateInput.value);
            }

            // Validate all fields for user feedback, but don't prevent submission
            // Server-side validation is the source of truth
            numericFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input) {
                    validateNumericField(input, field.min, field.max, field.name, field.isInteger);
                }
            });

            validateDiscountValue();
            validateMaximumDiscount();
        });
    }
});
</script>
