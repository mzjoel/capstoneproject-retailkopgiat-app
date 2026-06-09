import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VitePWA({
            registerType: 'autoUpdate',
            outDir: 'public',
            buildBase: '/',
            scope: '/',
            manifest: {
                name: 'GIAT Express',
                short_name: 'GIAT Express',
                description: 'Self Service Order',
                theme_color: '#800000',
                background_color: '#800000',
                display: 'standalone',
                orientation: 'portrait',
                start_url: '/',
                icons: [
                    {
                        src: '/assets/icons/giat-express-icon.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/assets/icons/giat-express-icon.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                ],
                screenshots: [
                    {
                        src: '/assets/screenshots/mobile.png',
                        sizes: '576x1024',
                        type: 'image/png',
                    },
                    {
                        src: '/assets/screenshots/desktop.png',
                        sizes: '1024x576',
                        type: 'image/png',
                        form_factor: 'wide',
                    }
                ]
            },
            workbox: {
                navigateFallback: null,
                cleanupOutdatedCaches: true,
                dontCacheBustURLsMatching: /\.[0-8a-f]{8}\./,
                globPatterns: [
                    'assets/icons/*.png',
                    'assets/screenshots/*.png'
                ],
                runtimeCaching: [
                    {
                        urlPattern: ({ request }) => request.mode === 'navigate',
                        handler: 'NetworkFirst', 
                        options: {
                            cacheName: 'laravel-dynamic-pages',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60 * 24 * 7, // 7 Days
                            },
                        },
                    },
                    {
                        urlPattern: ({ request }) => request.headers.get('X-Inertia') === 'true',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'inertia-json-cache',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 60 * 60 * 24 * 7, // 7 Days
                            }
                        },
                    },
                    {
                        urlPattern: ({ request }) => request.destination === 'style' || request.destination === 'script',
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'static-assets-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30, // 30 Days
                            }
                        }
                    },
                    {
                        urlPattern: ({ request }) => request.destination === 'image',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'images-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 60 * 60 * 24 * 30, // 30 Days
                            }
                        }
                    }
                ]
            },
            devOptions: {
                enabled: true,
                type: 'module',
            }
        })
    ],
    resolve: {
        alias: {
            'ziggy-js': path.resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
});
