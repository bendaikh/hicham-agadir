export function reportsManager() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    return {
        selectedPeriod: 'month',
        startDate: firstDay.toISOString().split('T')[0],
        endDate: today.toISOString().split('T')[0],
        isLoading: false,

        init() {
            // Get current period from URL if exists
            const params = new URLSearchParams(window.location.search);
            const period = params.get('period');
            if (period) {
                this.selectedPeriod = period;
                // Restore custom dates if provided
                if (period === 'custom') {
                    const startDate = params.get('start_date');
                    const endDate = params.get('end_date');
                    if (startDate) this.startDate = startDate;
                    if (endDate) this.endDate = endDate;
                }
            }
        },

        updatePeriod() {
            if (this.isLoading) return; // Prevent multiple clicks
            // Validate custom dates
            if (this.selectedPeriod === 'custom') {
                if (!this.startDate || !this.endDate) {
                    alert('Veuillez sélectionner une date de début et de fin');
                    return;
                }
                if (new Date(this.startDate) > new Date(this.endDate)) {
                    alert('La date de début doit être avant la date de fin');
                    return;
                }
            }
            // Show loading state
            this.isLoading = true;
            // Add slight delay to show transition
            setTimeout(() => {
                const params = new URLSearchParams();
                params.set('period', this.selectedPeriod);
                if (this.selectedPeriod === 'custom') {
                    params.set('start_date', this.startDate);
                    params.set('end_date', this.endDate);
                }
                window.location.href = `/reports?${params.toString()}`;
            }, 200);
        },

        exportReport() {
            console.log('Export report triggered');
            if (this.isLoading) return; // Prevent multiple clicks
            // Validate custom dates
            if (this.selectedPeriod === 'custom') {
                if (!this.startDate || !this.endDate) {
                    alert('Veuillez sélectionner une date de début et de fin');
                    return;
                }
                if (new Date(this.startDate) > new Date(this.endDate)) {
                    alert('La date de début doit être avant la date de fin');
                    return;
                }
            }
            // Show loading state
            this.isLoading = true;
            const params = new URLSearchParams();
            params.set('period', this.selectedPeriod);
            if (this.selectedPeriod === 'custom') {
                params.set('start_date', this.startDate);
                params.set('end_date', this.endDate);
            }
            const url = `/reports/export?${params.toString()}`;

            // Use fetch to download the file as a blob so we can reset loading state
            fetch(url, { credentials: 'same-origin' })
                .then(async response => {
                    if (!response.ok) {
                        const text = await response.text().catch(() => '');
                        console.error('Export failed, status:', response.status, 'body:', text);
                        throw new Error(`Export failed with status ${response.status}`);
                    }
                    return response.blob().then(blob => ({ blob, response }));
                })
                .then(({ blob, response }) => {
                    // Try to extract filename from content-disposition header
                    let filename = 'rapport.html';
                    const cd = response.headers.get('content-disposition');
                    if (cd) {
                        const match = cd.match(/filename\*=UTF-8''(.+)|filename="?([^;\"]+)"?/);
                        if (match) filename = decodeURIComponent(match[1] || match[2]);
                    }
                    const link = document.createElement('a');
                    const blobUrl = URL.createObjectURL(blob);
                    link.href = blobUrl;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    setTimeout(() => URL.revokeObjectURL(blobUrl), 1000);
                })
                .catch(err => {
                    console.error('Export failed', err);
                    alert('Échec de l\'export. Veuillez réessayer.');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        // Fallback for button if template calls downloadFullReport
        downloadFullReport() {
            this.exportReport();
        }
    };
}
