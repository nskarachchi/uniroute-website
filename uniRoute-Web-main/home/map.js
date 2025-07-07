function fetchBuses() {
    fetch("get_bus_location.php")
        .then(response => response.json())
        .then(data => {
            data.forEach(bus => {
                const { bus_id, latitude, longitude, contact_number, status } = bus;

                if (!latitude || !longitude) return;

                if (busMarkers[bus_id]) {
                    busMarkers[bus_id].setLatLng([latitude, longitude]);
                } else {
                    const marker = L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup(`
                            <b>Bus ID:</b> ${bus_id}<br>
                            <b>Status:</b> ${status}<br>
                            <b>Contact:</b> ${contact_number}
                        `);

                    busMarkers[bus_id] = marker;
                }
            });
        })
        .catch(error => console.error("Error fetching bus data:", error));
}
