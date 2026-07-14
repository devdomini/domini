<script>
    function formatCoordInput(n) { var s = Number(n).toFixed(7); return s.replace(/\.?0+$/, ''); }
    function initEntrepriseLocationPicker() {
        var mapEl = document.getElementById('entreprise-location-map');
        var latInput = document.getElementById('entreprise-lat');
        var lngInput = document.getElementById('entreprise-long');
        if (!mapEl || !latInput || !lngInput) return;
        var lat = parseFloat(String(latInput.value).replace(',', '.')) || 5.354;
        var lng = parseFloat(String(lngInput.value).replace(',', '.')) || -3.981;
        var map = new google.maps.Map(mapEl, { center: { lat: lat, lng: lng }, zoom: 16 });
        var marker = new google.maps.Marker({ position: { lat: lat, lng: lng }, map: map, draggable: true });
        function sync(ll) { latInput.value = formatCoordInput(ll.lat()); lngInput.value = formatCoordInput(ll.lng()); }
        marker.addListener('dragend', function() { sync(marker.getPosition()); });
        map.addListener('click', function(e) { marker.setPosition(e.latLng); sync(e.latLng); });
    }
    window.initEntrepriseLocationPickerCallback = function() { initEntrepriseLocationPicker(); };
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($googleMapsApiKey) }}&callback=initEntrepriseLocationPickerCallback"></script>
