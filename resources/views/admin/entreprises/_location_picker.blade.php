@if(!empty($googleMapsApiKey))
<div style="margin-top: 1rem;">
    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
        Position sur la carte
    </label>
    <p style="color: #666; font-size: 0.85rem; margin: 0 0 0.5rem;">
        Déplacez le marqueur sur l’établissement, ou cliquez sur la carte. Les champs latitude / longitude se mettent à jour automatiquement.
    </p>
    <div id="entreprise-location-map" style="width: 100%; height: 300px; border-radius: 8px; border: 1px solid #E5E5E5; background: #eee;"></div>
</div>
@endif
