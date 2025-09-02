var cacheName = 'its-ready-admin';
var filesToCache = [
    // '/nl/admin/login'
    // '/assets/fontawesome/css/font-awesome.min.css',
    // '/assets/bootstrap/dist/css/bootstrap.min.css',
    // '/assets/css/custom.css',
    // '/themes/dashboard/css/style.css',
    // '/assets/pwa/admin/main.js',
    // '/assets/jquery/dist/jquery.min.js',
    // '/assets/bootstrap/dist/js/bootstrap.min.js',
    // '/assets/lang-messages/langs.js',
    // '/assets/jquery-validation/dist/jquery.validate.min.js',
    // '/assets/jquery-validation/dist/additional-methods.min.js',
    // '/assets/js/default.js'
];

/* Start the service worker and cache all of the app's content */
self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open(cacheName).then(function(cache) {
            return cache.addAll(filesToCache);
        })
    );
});

/* Serve cached content when offline */
self.addEventListener('fetch', function(e) {
    e.respondWith(
        caches.match(e.request).then(function(response) {
            return response || fetch(e.request);
        })
    );
});
