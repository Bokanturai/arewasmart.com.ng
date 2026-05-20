const STATIC_CACHE = 'arewa-smart-static-v3';
const DYNAMIC_CACHE = 'arewa-smart-dynamic-v3';
const MAX_DYNAMIC_ITEMS = 50;

// Core shell assets to pre-cache immediately
const urlsToCache = [
  '/',
  '/assets/css/style.css',
  '/assets/js/script.js',
  '/ofline/index.html',
  '/assets/img/bg/tom.png',
  '/manifest.json'
];

// Helper to limit cache items (preventing storage bloat)
const trimCache = (cacheName, maxItems) => {
  caches.open(cacheName).then(cache => {
    cache.keys().then(keys => {
      if (keys.length > maxItems) {
        cache.delete(keys[0]).then(() => trimCache(cacheName, maxItems));
      }
    });
  });
};

// 1. Install Event - Pre-cache UI shell
self.addEventListener('install', event => {
  self.skipWaiting(); // Force activation of the new Service Worker immediately
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// 2. Activate Event - Clean up stale caches from previous updates
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== STATIC_CACHE && key !== DYNAMIC_CACHE) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// 3. Fetch Event Interceptor
self.addEventListener('fetch', event => {
  const request = event.request;
  const url = new URL(request.url);

  // SECURITY: Avoid caching POST/PUT/DELETE, external CDNs, or sensitive routes
  if (
    request.method !== 'GET' ||
    url.origin !== self.location.origin ||
    url.pathname.includes('/logout') ||
    url.pathname.includes('/login') ||
    url.pathname.startsWith('/admin') ||
    url.pathname.startsWith('/api')
  ) {
    return; // Pass through to network natively
  }

  // STRATEGY A: Network-First (for HTML / Document Navigation Pages)
  // Ensures fresh live data, but falls back to previously visited cached pages, then to the offline screen
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then(response => {
          // If valid response, clone and cache it dynamically
          const responseClone = response.clone();
          caches.open(DYNAMIC_CACHE).then(cache => {
            cache.put(request, responseClone);
            trimCache(DYNAMIC_CACHE, MAX_DYNAMIC_ITEMS);
          });
          return response;
        })
        .catch(() => {
          // Offline fallback
          return caches.match(request) // Try serving the visited cached version of this page
            .then(cachedPage => {
              if (cachedPage) return cachedPage;
              return caches.match('/ofline/index.html'); // Ultimate fallback to beautiful offline card
            });
        })
    );
    return;
  }

  // STRATEGY B: Cache-First (for static assets: css, js, images, fonts)
  // Speeds up loading times drastically
  event.respondWith(
    caches.match(request).then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(request).then(response => {
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }
        const responseClone = response.clone();
        caches.open(DYNAMIC_CACHE).then(cache => {
          cache.put(request, responseClone);
        });
        return response;
      });
    })
  );
});
