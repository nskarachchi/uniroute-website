document.addEventListener("DOMContentLoaded", function () {
    var map = L.map("map").setView([6.9271, 79.8612], 12);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

    let markers = {};

    function loadBusLocations() {
        fetch("get_buses.php")
            .then(response => response.json())
            .then(data => {
                data.forEach(bus => {
                    if (markers[bus.bus_id]) {
                        markers[bus.bus_id].setLatLng([bus.latitude, bus.longitude]);
                    } else {
                        markers[bus.bus_id] = L.marker([bus.latitude, bus.longitude])
                            .addTo(map)
                            .bindPopup(`<b>Bus ID:</b> ${bus.bus_id}<br><b>Status:</b> ${bus.status}<br><b>Last Updated:</b> ${bus.last_updated}`);
                    }
                });
            });
    }

    loadBusLocations();
    setInterval(loadBusLocations, 3000);
});

