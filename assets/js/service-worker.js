self.addEventListener('install', function(e) {
  // You can do offline caching here if you want
  self.skipWaiting();
});
self.addEventListener('fetch', function(event) {
  // Just let all requests pass through for now
});
