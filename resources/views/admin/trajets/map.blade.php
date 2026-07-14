@extends('admin.layout')

@section('title', 'Carte du trajet — ' . $warehouse->name)
@section('page-title', 'Carte du trajet')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto;">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('admin.trajets.index') }}" style="color: #3B82F6; text-decoration: none; font-weight: 600;">← Tous les trajets</a>
            <a href="{{ route('admin.trajets.edit', $warehouse) }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">Modifier l’ordre du trajet</a>
        </div>

        @include('admin.partials.warehouse-tabs', ['warehouse' => $warehouse])

        <div style="background: white; padding: 1.25rem 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1rem;">
            <h2 style="font-size: 1.2rem; font-weight: 700; color: #000000; margin-bottom: 0.35rem;">{{ $warehouse->name }}</h2>
            <p style="color: #666; font-size: 0.95rem; margin: 0;">
                Parcours dans l’ordre : <strong>entrepôt (D)</strong> puis les étapes numérotées. Le tracé rouge suit les routes (itinéraire véhicule via Google Directions).
                <span style="display:block;margin-top:0.35rem;font-size:0.85rem;">Marqueurs <span style="color:#FF0000;">●</span> rouge = GPS entreprise, <span style="color:#D97706;">●</span> ambre = aligné Google (nom + adresse), <span style="color:#EA580C;">●</span> orange = commune.</span>
            </p>
        </div>

        @if(empty($apiKey))
            <div style="background: #FFFBEB; border: 1px solid #FCD34D; color: #92400E; padding: 1.25rem; border-radius: 8px; margin-bottom: 1rem;">
                <strong>Clé Google Maps manquante.</strong> Ajoutez dans votre fichier <code>.env</code> :<br>
                <code style="display: inline-block; margin-top: 0.5rem; padding: 0.35rem 0.6rem; background: #fff; border-radius: 4px;">GOOGLE_MAPS_API_KEY=votre_cle</code>
                <p style="margin: 0.75rem 0 0; font-size: 0.9rem;">Puis exécutez <code>php artisan config:clear</code>. Activez « Maps JavaScript API » et « Directions API » dans Google Cloud, puis restreignez la clé par domaine (référent HTTP).</p>
            </div>
        @endif

        @if(count($sansCoordonnees) > 0)
            <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem;">
                <strong>Étapes sans coordonnées GPS</strong> — géocodage automatique par adresse si possible ; sinon renseignez le GPS :
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach($sansCoordonnees as $s)
                        <li>Étape {{ $s['position'] }} — {{ $s['nom'] }}@if(!empty($s['adresse'])) ({{ $s['adresse'] }})@endif : {{ $s['raison'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!empty($apiKey) && count($waypoints) === 0 && count($sansCoordonnees) === 0)
            <div style="background: #F3F4F6; padding: 2rem; border-radius: 8px; text-align: center; color: #6B7280;">
                Aucun point à afficher : renseignez les coordonnées de l’entrepôt et des entreprises (ou des communes), et définissez un trajet.
            </div>
        @elseif(!empty($apiKey))
            <div id="trajet-map" style="width: 100%; height: 520px; border-radius: 12px; overflow: hidden; border: 1px solid #E5E5E5; background: #E5E7EB;"></div>

            <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                @foreach($waypoints as $w)
                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; background: {{ $w['kind'] === 'warehouse' ? '#ECFDF5' : '#FFF7ED' }}; border-radius: 20px; font-size: 0.85rem; border: 1px solid {{ $w['kind'] === 'warehouse' ? '#A7F3D0' : '#FDBA74' }};">
                        <strong style="color: #FF0000;">{{ $w['label'] }}</strong>
                        {{ $w['title'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('scripts')
@if(!empty($apiKey) && (count($waypoints) > 0 || count($sansCoordonnees) > 0))
<script>
    const TRAJET_WAYPOINTS = @json($waypoints);
    const TRAJET_SANS_COORD = @json($sansCoordonnees);

    function markerFillColor(w) {
        if (w.kind === 'warehouse') return '#059669';
        if (w.geocoded || w.coords_source === 'geocoded_map') return '#D97706';
        if (w.coords_source === 'commune' || w.coords_overlapped) return '#EA580C';
        return '#FF0000';
    }

    const MAP_BOUNDS_PADDING = { top: 72, right: 72, bottom: 72, left: 72 };

    function fitMapToTrajet(map, bounds) {
        if (bounds.isEmpty()) return;
        map.fitBounds(bounds, MAP_BOUNDS_PADDING);
        google.maps.event.addListenerOnce(map, 'idle', function() {
            const z = map.getZoom();
            if (z !== undefined && z > 17) {
                map.setZoom(17);
            }
        });
    }

    function addTrajetMarker(map, bounds, w, zIndex) {
        const pos = { lat: w.lat, lng: w.lng };
        bounds.extend(pos);
        const marker = new google.maps.Marker({
            position: pos,
            map: map,
            label: {
                text: w.label,
                color: '#FFFFFF',
                fontWeight: 'bold',
                fontSize: '12px',
            },
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 14,
                fillColor: markerFillColor(w),
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 2,
            },
            title: w.title,
            zIndex: zIndex,
        });
        const info = new google.maps.InfoWindow({
            content: '<div style="padding:4px 8px;max-width:240px;"><strong>' + escapeHtml(w.title) + '</strong><br><span style="color:#666;font-size:12px;">' + escapeHtml(w.subtitle || '') + '</span></div>',
        });
        marker.addListener('click', function() {
            info.open(map, marker);
        });
        return marker;
    }

    function sortWaypoints(waypoints) {
        return waypoints.slice().sort(function(a, b) {
            if (a.kind === 'warehouse') return -1;
            if (b.kind === 'warehouse') return 1;
            return (a.order || 0) - (b.order || 0);
        });
    }

    function geocodeMissingStops(geocoder) {
        const tasks = (TRAJET_SANS_COORD || []).map(function(s) {
            const query = [s.adresse, s.commune, 'Abidjan', 'Côte d\'Ivoire'].filter(Boolean).join(', ');
            if (!query.trim()) {
                return Promise.resolve(null);
            }
            return new Promise(function(resolve) {
                geocoder.geocode({ address: query, region: 'ci' }, function(results, status) {
                    if (status !== 'OK' || !results || !results[0]) {
                        console.warn('Géocodage étape ' + s.position + ' : ' + status);
                        resolve(null);
                        return;
                    }
                    const loc = results[0].geometry.location;
                    resolve({
                        kind: 'stop',
                        title: s.nom,
                        subtitle: (s.commune || '') + ' — Position estimée (géocodage adresse)',
                        lat: loc.lat(),
                        lng: loc.lng(),
                        order: s.position,
                        label: String(s.position),
                        geocoded: true,
                    });
                });
            });
        });
        return Promise.all(tasks).then(function(items) {
            return items.filter(Boolean);
        });
    }

    function initTrajetMap() {
        const mapEl = document.getElementById('trajet-map');
        const bounds = new google.maps.LatLngBounds();
        const map = new google.maps.Map(mapEl, {});
        const geocoder = new google.maps.Geocoder();

        geocodeMissingStops(geocoder).then(function(geocoded) {
            const all = sortWaypoints(TRAJET_WAYPOINTS.concat(geocoded));
            if (!all.length) return;

            all.forEach(function(w, i) {
                addTrajetMarker(map, bounds, w, i);
            });

            drawRoadPolyline(all, map, bounds);
        });
    }

    /**
     * Assemble un chemin suivant le réseau routier via DirectionsService.
     * Au-delà de 27 points, enchaîne plusieurs requêtes (limite Google : 25 waypoints intermédiaires).
     */
    function drawRoadPolyline(waypoints, map, bounds) {
        if (waypoints.length < 2) {
            fitMapToTrajet(map, bounds);
            return;
        }

        const directionsService = new google.maps.DirectionsService();
        const segments = [];
        for (let start = 0; start < waypoints.length - 1; ) {
            const end = Math.min(start + 26, waypoints.length - 1);
            segments.push({ start: start, end: end });
            start = end;
        }

        const mergedPath = [];
        let segIdx = 0;

        function requestSegment() {
            if (segIdx >= segments.length) {
                new google.maps.Polyline({
                    path: mergedPath,
                    geodesic: false,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.95,
                    strokeWeight: 4,
                    map: map,
                });
                fitMapToTrajet(map, bounds);
                return;
            }

            const seg = segments[segIdx];
            const slice = waypoints.slice(seg.start, seg.end + 1);
            const origin = slice[0];
            const destination = slice[slice.length - 1];
            const middle = slice.slice(1, -1).map(function(p) {
                return { location: new google.maps.LatLng(p.lat, p.lng), stopover: true };
            });

            directionsService.route(
                {
                    origin: new google.maps.LatLng(origin.lat, origin.lng),
                    destination: new google.maps.LatLng(destination.lat, destination.lng),
                    waypoints: middle,
                    travelMode: google.maps.TravelMode.DRIVING,
                    unitSystem: google.maps.UnitSystem.METRIC,
                    region: 'ci',
                },
                function(response, status) {
                    if (status === 'OK' && response.routes && response.routes[0] && response.routes[0].overview_path) {
                        response.routes[0].overview_path.forEach(function(ll, i) {
                            if (segIdx > 0 && i === 0 && mergedPath.length) {
                                const last = mergedPath[mergedPath.length - 1];
                                if (Math.abs(last.lat - ll.lat()) < 1e-7 && Math.abs(last.lng - ll.lng()) < 1e-7) {
                                    return;
                                }
                            }
                            mergedPath.push({ lat: ll.lat(), lng: ll.lng() });
                        });
                    } else {
                        console.warn('Directions segment ' + seg.start + '→' + seg.end + ' : ' + status + ' — ligne directe de secours.');
                        slice.forEach(function(p, i) {
                            if (segIdx > 0 && i === 0 && mergedPath.length) return;
                            mergedPath.push({ lat: p.lat, lng: p.lng });
                        });
                    }
                    segIdx++;
                    requestSegment();
                }
            );
        }

        requestSegment();
    }

    function escapeHtml(text) {
        const tag = 'd' + 'iv';
        const d = document.createElement(tag);
        d.textContent = text;
        return d.innerHTML;
    }

    window.initTrajetMapCallback = function() {
        initTrajetMap();
    };
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($apiKey) }}&callback=initTrajetMapCallback"></script>
@endif
@endsection
