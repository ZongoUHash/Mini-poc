const cacheName = 'attendance-poc-static-v3';
const cacheablePaths = ['/manifest.webmanifest', '/icons/icon-192.svg', '/icons/icon-512.svg'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(cacheName)
            .then((cache) => cache.addAll(cacheablePaths))
            .then(() => self.skipWaiting()),
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((key) => key.startsWith('attendance-poc-') && key !== cacheName).map((key) => caches.delete(key))))
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const requestUrl = new URL(event.request.url);
    const isStaticAsset = requestUrl.origin === self.location.origin
        && (requestUrl.pathname.startsWith('/build/') || cacheablePaths.includes(requestUrl.pathname));

    if (! isStaticAsset) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            return fetch(event.request).then((response) => {
                if (response.ok) {
                    caches.open(cacheName).then((cache) => cache.put(event.request, response.clone()));
                }

                return response;
            });
        }),
    );
});
