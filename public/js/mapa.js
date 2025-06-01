const map = L.map('map').setView([20, 0], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker;

map.on('click', function (e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map);

    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    // Obtener pais y ciudad
    fetch(`index.php?controller=ubicacion&method=getUbicacion&lat=${lat}&lng=${lng}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById("pais").textContent = data.pais;
            document.getElementById("ciudad").textContent = data.provincia;

            // Guardar pais y ciudad en sesion
            fetch('index.php?controller=registro&method=guardarUbicacion', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    pais: data.pais,
                    provincia: data.provincia
                })
            })
        });
});