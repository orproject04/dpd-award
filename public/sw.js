const CACHE_NAME = 'dpd-media-cache-v1';

// File extensions and MIME types to cache persistently
const MEDIA_REGEX = /\.(png|jpg|jpeg|webp|gif|svg|ico|mp4|webm|m3u8|ts|woff2|woff|ttf|css|js)($|\?)/i;

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const url = event.request.url;

    // Only intercept GET requests for static media, fonts, videos, and images
    if (event.request.method !== 'GET') return;
    if (!MEDIA_REGEX.test(url)) return;

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                // Return cached version immediately (0ms)
                return cachedResponse;
            }

            // Fetch from network and put in cache
            return fetch(event.request).then((response) => {
                // Ensure valid response before caching
                if (!response || response.status !== 200 || (response.type !== 'basic' && response.type !== 'cors')) {
                    return response;
                }

                const responseToCache = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseToCache);
                });

                return response;
            }).catch(() => {
                // Fallback gracefully if network fails
                return cachedResponse;
            });
        })
    );
});
