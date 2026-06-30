self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open("worldison-cache-v1").then((cache) => {
      return cache.addAll([
        "/",
        "/login.php",
        "/favicon.ico",
        "/manifest.json",
      ]);
    })
  );
});

self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
