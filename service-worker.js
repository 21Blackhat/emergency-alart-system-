self.addEventListener("push", function(event) {
    const notificationData = event.data ? event.data.json() : {};
    const title = notificationData.title || "🚨 Emergency Alert!";
    const options = {
        body: notificationData.body || "A new emergency has been reported!",
        icon: "icons/icon-192x192.png",
        badge: "icons/icon-192x192.png",
        data: notificationData.url || "/admin_dashboard_mobile.html"
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener("notificationclick", function(event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data)
    );
});
