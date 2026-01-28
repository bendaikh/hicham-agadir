// Global Search Component
document.addEventListener('alpine:init', () => {
    Alpine.data('globalSearch', () => ({
        query: '',
        open: false,
        loading: false,
        results: {
            clients: [],
            articles: [],
            quotes: [],
            invoices: [],
            purchases: [],
            total: 0
        },
        searchTimeout: null,

        async search() {
            clearTimeout(this.searchTimeout);
            
            if (this.query.length < 2) {
                this.open = false;
                this.results = {
                    clients: [],
                    articles: [],
                    quotes: [],
                    invoices: [],
                    purchases: [],
                    total: 0
                };
                return;
            }

            this.open = true;
            this.loading = true;

            this.searchTimeout = setTimeout(async () => {
                try {
                    const response = await fetch(`/search?q=${encodeURIComponent(this.query)}`);
                    const data = await response.json();
                    
                    this.$nextTick(() => {
                        this.results = data;
                        this.loading = false;
                    });
                } catch (error) {
                    console.error('Search error:', error);
                    this.loading = false;
                }
            }, 300); // Debounce search
        },

        focusNext() {
            if (!this.open) {
                this.open = true;
            }
        },

        focusPrev() {
            if (!this.open) {
                this.open = true;
            }
        },

        selectFocused() {
            if (this.open && this.results.total > 0) {
                // Auto-navigate to first result
                const allResults = [
                    ...this.results.clients,
                    ...this.results.articles,
                    ...this.results.quotes,
                    ...this.results.invoices,
                    ...this.results.purchases
                ];
                
                if (allResults.length > 0) {
                    window.location.href = allResults[0].url;
                }
            }
        }
    }));
});
