import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // Configuración PWA
        VitePWA({
            registerType: 'autoUpdate',
            manifest: {
                name: 'Gestión Municipal Huamelula',
                short_name: 'Huamelula App',
                description: 'Sistema de Control de Asistencia',
                theme_color: '#1e293b',
                background_color: '#1e293b',
                display: 'standalone', // Esto hace que parezca App nativa (sin barra de url)
                orientation: 'portrait',
                icons: [
                    {
                        src: '/images/logo.png', // Usa tu logo
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/images/logo.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            },
            workbox: {
                // Qué archivos guardar en caché
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                navigateFallback: null, // Importante para SPA/Livewire
            }
        })
    ],
});
