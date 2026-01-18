/**
 * Address Autocomplete Module
 * Handles NZ Post address autocomplete functionality
 */
class AddressAutocomplete {
    constructor(inputElement, suggestionsContainer, prefix) {
        this.input = inputElement;
        this.container = suggestionsContainer;
        this.prefix = prefix;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        this.searchUrl = '/checkout/search-address';
        this.searchTimeout = null;
        this.init();
    }

    init() {
        if (!this.input || !this.container) return;

        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('blur', () => this.hideSuggestions());
        this.input.addEventListener('focus', () => {
            if (this.container.children.length > 0) {
                this.container.style.display = 'block';
            }
        });

        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.container.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    handleInput(e) {
        const query = e.target.value.trim();
        clearTimeout(this.searchTimeout);

        if (query.length < 3) {
            this.hideSuggestions();
            return;
        }

        this.searchTimeout = setTimeout(() => {
            this.searchAddress(query);
        }, 300);
    }

    /**
     * Search for address suggestions
     * @param {string} query - Search query
     */
    async searchAddress(query) {
        try {
            const data = window.AjaxUtils
                ? await window.AjaxUtils.post(this.searchUrl, { query }, { showMessage: false, silentAuth: true })
                : await fetch(this.searchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ query })
                }).then(response => response.json());

            const results = data.data?.results ?? data.results ?? [];
            if (data.success && results && results.length > 0) {
                this.displaySuggestions(results);
            } else {
                this.hideSuggestions();
            }
        } catch (error) {
            this.hideSuggestions();
        }
    }

    displaySuggestions(results) {
        this.container.innerHTML = '';

        results.forEach(result => {
            const item = document.createElement('div');
            item.className = 'address-suggestion-item';
            item.textContent = result.display;
            item.addEventListener('click', () => this.selectAddress(result));
            this.container.appendChild(item);
        });

        this.container.style.display = 'block';
    }

    selectAddress(result) {
        this.input.value = result.display;
        this.hideSuggestions();

        const cityField = document.getElementById(this.prefix + 'City');
        const suburbField = document.getElementById(this.prefix + 'Suburb');
        const regionField = document.getElementById(this.prefix + 'Region');
        const zipField = document.getElementById(this.prefix + 'Zip');

        if (cityField && result.city) {
            cityField.value = result.city;
        }

        if (suburbField && result.suburb) {
            suburbField.value = result.suburb;
        }

        if (regionField && result.region_id) {
            regionField.value = result.region_id;
            regionField.dispatchEvent(new Event('change'));
        }

        if (zipField && result.postcode) {
            zipField.value = result.postcode;
        }

        if (this.prefix === 'shipping' && typeof window.updateShipping === 'function') {
            window.updateShipping(result.region_id);
        }
    }

    hideSuggestions() {
        this.container.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const shippingInput = document.getElementById('shippingAddress');
    const shippingContainer = document.getElementById('shippingAddressSuggestions');
    if (shippingInput && shippingContainer) {
        new AddressAutocomplete(shippingInput, shippingContainer, 'shipping');
    }

    const billingInput = document.getElementById('billingAddress');
    const billingContainer = document.getElementById('billingAddressSuggestions');
    if (billingInput && billingContainer) {
        new AddressAutocomplete(billingInput, billingContainer, 'billing');
    }
});
