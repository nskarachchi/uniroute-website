document.addEventListener("DOMContentLoaded", function () {
    fetch("load_bus.php")
        .then(response => response.json())
        .then(data => {
            const busList = document.getElementById("busList");
            busList.innerHTML = ""; 

            if (data.length === 0) {
                busList.innerHTML = "<tr><td colspan='6'>No buses available</td></tr>";
                return;
            }

            data.forEach(bus => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td class="p-2">${bus.bus_id}</td>
                    <td class="p-2">${bus.latitude}</td>
                    <td class="p-2">${bus.longitude}</td>
                    <td class="p-2">${bus.contact_number}</td>
                    <td class="p-2">${bus.driver_email}</td>
                    <td class="p-2">${bus.status}</td>
                `;
                busList.appendChild(row);
            });
        })
        .catch(error => {
            console.error("Error fetching buses:", error);
        });
});


// Add New Bus
document.getElementById("addBusForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData();
    formData.append("bus_id", document.getElementById("busId").value);
    formData.append("latitude", document.getElementById("latitude").value);
    formData.append("longitude", document.getElementById("longitude").value);
    formData.append("contact_number", document.getElementById("contact_number").value);
    formData.append("driver_email", document.getElementById("email").value);
    formData.append("status", document.getElementById("status").value);

    fetch("add_bus.php", { method: "POST", body: formData })
        .then(response => response.text())
        .then(data => {
            alert(data);
            loadBuses(); // Reload buses after adding
        });
});

// Update Bus
document.getElementById("updateBusForm").addEventListener("submit", function (e) {
    e.preventDefault();

    let formData = new FormData();
    formData.append("bus_id", document.getElementById("updateBusId").value);
    formData.append("latitude", document.getElementById("updateLatitude").value);
    formData.append("longitude", document.getElementById("updateLongitude").value);
    formData.append("contact_number", document.getElementById("updateContactNumber").value);
    formData.append("driver_email", document.getElementById("Updateemail").value);
    formData.append("status", document.getElementById("updateStatus").value);
    

    fetch("update_bus.php", { method: "POST", body: formData })
        .then(response => response.text())
        .then(data => {
            alert(data);
            loadBuses(); 
        });
});


loadBuses();


