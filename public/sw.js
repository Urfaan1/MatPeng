// MatPeng PWA Service Worker
const CACHE_NAME = 'matpeng-v1-static';

const PRECACHE_ASSETS = [
    '/',
    '/manifest.webmanifest',
    '/icons/icon-192.svg',
    '/icons/icon-512.svg'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Hanya tangani GET request
    if (event.request.method !== 'GET') return;

    // JANGAN cache URL transaksi, API, atau data finansial privat
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/transactions') || url.pathname.startsWith('/api') || url.pathname.startsWith('/settings')) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
