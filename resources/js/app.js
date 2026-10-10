import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('quickview', {
    open: false,
    loading: false,
    content: '',
    title: '',

    async show(url, title = '') {
        this.title = title;
        this.content = '';
        this.loading = true;
        this.open = true;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
            });

            if (! response.ok) {
                throw new Error('No se pudo cargar la información.');
            }

            this.content = await response.text();
        } catch (error) {
            this.content = '<p class="p-6 text-sm text-red-600">' + error.message + '</p>';
        } finally {
            this.loading = false;
        }
    },

    close() {
        this.open = false;
        this.loading = false;
        this.content = '';
        this.title = '';
    },
});

Alpine.start();
