import './bootstrap';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob<{ default: React.ComponentType }>('./Pages/**/*.tsx', { eager: true });
        const page = pages[`./Pages/${name}.tsx`];
        if (!page) {
            throw new Error(`Halaman Inertia tidak ditemukan: ./Pages/${name}.tsx`);
        }
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
