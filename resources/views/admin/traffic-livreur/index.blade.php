@extends('admin.layout')

@section('title', 'Traffic livreur')
@section('page-title', 'Traffic livreur')

@section('styles')
<style>
    .traffic-wrap {
        display: grid;
        grid-template-columns: 360px 1fr;
        gap: 1.25rem;
        align-items: stretch;
    }

    /* Mode plein écran */
    .traffic-wrap.only-map { grid-template-columns: 1fr; }
    .traffic-wrap.only-map .side-card { display: none; }
    .traffic-wrap.only-side { grid-template-columns: 1fr; }
    .traffic-wrap.only-side .map-card { display: none; }

    .map-card {
        background: white;
        border: 1px solid #E5E5E5;
        border-radius: 12px;
        overflow: hidden;
        height: calc(100vh - 140px);
        min-height: 520px;
        position: relative;
    }

    #trafficMap {
        width: 100%;
        height: 100%;
        min-height: 520px;
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 20;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        pointer-events: none;
    }
    .map-controls .btn {
        pointer-events: auto;
        box-shadow: 0 10px 18px rgba(0,0,0,0.12);
    }

    .side-card {
        background: white;
        border: 1px solid #E5E5E5;
        border-radius: 12px;
        overflow: hidden;
    }

    .side-head {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #E5E5E5;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .side-body {
        padding: 1rem 1.25rem;
        height: calc(100vh - 140px);
        min-height: 520px;
        overflow: auto;
    }

    .filters {
        display: grid;
        gap: 0.65rem;
        padding: 0.9rem;
        border: 1px solid #F0F0F0;
        border-radius: 12px;
        background: #FDFBF8;
        margin-bottom: 1rem;
    }

    .filters .rowline {
        display: flex;
        gap: 0.6rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filters input[type="date"] {
        padding: 0.55rem 0.7rem;
        border: 1px solid #E5E5E5;
        border-radius: 10px;
        background: white;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .kpi {
        border: 1px solid #F0F0F0;
        border-radius: 12px;
        padding: 0.85rem;
        background: #FDFBF8;
    }

    .kpi .label {
        font-size: 0.75rem;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
    }

    .kpi .value {
        font-size: 1.5rem;
        font-weight: 900;
        color: #000000;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .pill.green { background: #E8F5E9; color: #2d9248; }
    .pill.red { background: #FFEBEE; color: #CC0000; }
    .pill.yellow { background: #FFF3E0; color: #E65100; }
    .pill.gray { background: #F5F5F5; color: #666; }

    .livreur-pin {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        border: 2px solid #fff;
        box-shadow: 0 10px 18px rgba(0,0,0,0.18);
    }
    .livreur-pin.online { background: #10B981; }
    .livreur-pin.busy { background: #EF4444; }
    .livreur-pin.offline { background: #9CA3AF; }
    .livreur-pin.inactive { background: #6B7280; }
    .livreur-pin svg { width: 18px; height: 18px; display:block; color: #fff; }

    .list {
        display: grid;
        gap: 0.5rem;
        max-height: 38vh;
        overflow: auto;
        padding-right: 0.25rem;
    }

    .row {
        border: 1px solid #F0F0F0;
        border-radius: 12px;
        padding: 0.75rem 0.85rem;
        display: grid;
        gap: 0.25rem;
        cursor: pointer;
        transition: background 0.15s ease;
    }
    .row:hover { background: #FAFAFA; }

    .row .title {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: center;
    }
    .row .name { font-weight: 800; color: #000000; }
    .row .sub { font-size: 0.85rem; color: #666; }

    @media (max-width: 1100px) {
        .traffic-wrap { grid-template-columns: 1fr; }
        .map-card { height: 65vh; min-height: 480px; }
        #trafficMap { height: 100%; min-height: 480px; }
        .side-body { height: auto; min-height: 0; overflow: visible; }
    }
</style>
@endsection

@section('content')
<div class="traffic-wrap">
    <div class="side-card">
        <div class="side-head">
            <div>
                <div style="font-size: 1.15rem; font-weight: 900;">Flux en temps réel</div>
                <div id="serverTime" style="font-size: 0.85rem; color: #666;">—</div>
            </div>
            <div style="display:flex; gap: 0.5rem; align-items:center; flex-wrap: wrap; justify-content: flex-end;">
                <span id="followBadge" class="pill gray" style="display:none;"></span>
                <button class="btn btn-secondary" type="button" onclick="toggleOnlyMap()" title="Masquer le panneau">
                    Plein écran carte
                </button>
                <button class="btn btn-secondary" type="button" onclick="toggleOnlySide()" title="Masquer la carte">
                    Plein écran panneau
                </button>
                <button class="btn btn-secondary" type="button" onclick="stopFollow()">Arrêter suivi</button>
                <button class="btn btn-secondary" type="button" onclick="refreshTraffic()">Actualiser</button>
            </div>
        </div>
        <div class="side-body">
            <div class="filters">
                <div style="font-weight: 900;">Filtrer par période</div>
                <div class="rowline">
                    <button class="btn btn-secondary" type="button" onclick="setPreset('today')">Aujourd’hui</button>
                    <button class="btn btn-secondary" type="button" onclick="setPreset('yesterday')">Hier</button>
                    <button class="btn btn-secondary" type="button" onclick="setPreset('7d')">7 jours</button>
                    <button class="btn btn-secondary" type="button" onclick="clearPeriod()">Actives</button>
                </div>
                <div class="rowline">
                    <div style="font-size: 0.85rem; color: #666; min-width: 120px;">Jour précis</div>
                    <input id="filterDate" type="date" onchange="applyPeriod()">
                    <button class="btn btn-primary" type="button" onclick="applyPeriod()">Appliquer</button>
                </div>
                <div class="rowline">
                    <div style="font-size: 0.85rem; color: #666; min-width: 120px;">Plage</div>
                    <input id="filterFrom" type="date" onchange="applyPeriodRange()">
                    <span style="color:#666;">→</span>
                    <input id="filterTo" type="date" onchange="applyPeriodRange()">
                    <button class="btn btn-primary" type="button" onclick="applyPeriodRange()">Appliquer</button>
                </div>
                <div id="periodLabel" style="font-size: 0.85rem; color:#666;">Mode: livraisons actives (temps réel)</div>
            </div>

            <div class="kpi-grid">
                <div class="kpi">
                    <div class="label">Livreurs (total)</div>
                    <div class="value" id="kpiLivreursTotal">—</div>
                </div>
                <div class="kpi">
                    <div class="label">Livreurs dispo</div>
                    <div class="value" id="kpiLivreursDispo">—</div>
                </div>
                <div class="kpi">
                    <div class="label">Livraisons actives</div>
                    <div class="value" id="kpiLivActives">—</div>
                </div>
                <div class="kpi">
                    <div class="label">En cours</div>
                    <div class="value" id="kpiLivEnCours">—</div>
                </div>
            </div>

            <div style="display:flex; gap: 0.75rem; align-items:center; justify-content: space-between; margin-top: 0.5rem;">
                <div style="font-weight: 800;">Livreurs</div>
                <div style="display:flex; gap: 0.5rem; align-items:center;">
                    <span class="pill green">● Dispo</span>
                    <span class="pill red">● Occupé</span>
                    <span class="pill gray">● Inactif</span>
                </div>
            </div>
            <div class="list" id="livreursList" style="margin-top: 0.75rem;"></div>

            <div style="display:flex; gap: 0.75rem; align-items:center; justify-content: space-between; margin-top: 1.25rem;">
                <div style="font-weight: 800;">Livraisons actives</div>
                <div style="display:flex; gap: 0.5rem; align-items:center;">
                    <span class="pill yellow">● Assignée</span>
                    <span class="pill red">● En cours</span>
                </div>
            </div>
            <div class="list" id="livraisonsList" style="margin-top: 0.75rem;"></div>
        </div>
    </div>

    <div class="map-card">
        <div class="map-controls">
            <button class="btn btn-secondary" type="button" onclick="resetLayout()" title="Réduire">
                Réduire
            </button>
            <button class="btn btn-secondary" type="button" onclick="toggleOnlyMap()" title="Plein écran carte">
                Carte
            </button>
            <button class="btn btn-secondary" type="button" onclick="toggleOnlySide()" title="Plein écran panneau">
                Panneau
            </button>
        </div>
        <div id="trafficMap"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ urlencode($apiKey ?? '') }}&callback=initTrafficMap"></script>
<script>
    const DATA_URL = @json(route('admin.traffic-livreur.data'));
    const REFRESH_MS = 12000;
    const PUSHER_KEY = @json(env('PUSHER_APP_KEY'));
    const PUSHER_CLUSTER = @json(env('PUSHER_APP_CLUSTER', 'mt1'));

    let map; // google.maps.Map
    let directionsService = null; // google.maps.DirectionsService
    /** Incrémenté à chaque refresh pour ignorer les callbacks Directions obsolètes */
    let trafficRenderGeneration = 0;

    const gLayers = {
        livreurs: new Map(), // id -> google.maps.Marker
        destinations: [],    // google.maps.Marker[]
        flux: [],            // google.maps.Polyline[]
    };

    let didInitialFit = false;
    let refreshTimer = null;
    let followed = { type: null, id: null }; // {type: 'livreur'|'livraison', id}
    let lastDataCache = null;

    // Période (filtre livraisons)
    let period = { date: null, from: null, to: null };

    function initTrafficMap() {
        if (!window.google || !google.maps) return;
        map = new google.maps.Map(document.getElementById('trafficMap'), {
            center: { lat: 5.35, lng: -4.03 },
            zoom: 12,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
        });
        directionsService = new google.maps.DirectionsService();
    }

    function ymd(d) {
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${mm}-${dd}`;
    }

    function setPreset(p) {
        const now = new Date();
        const dateEl = document.getElementById('filterDate');
        const fromEl = document.getElementById('filterFrom');
        const toEl = document.getElementById('filterTo');

        dateEl.value = '';
        fromEl.value = '';
        toEl.value = '';

        if (p === 'today') {
            period = { date: ymd(now), from: null, to: null };
            dateEl.value = period.date;
        } else if (p === 'yesterday') {
            const y = new Date(now);
            y.setDate(now.getDate() - 1);
            period = { date: ymd(y), from: null, to: null };
            dateEl.value = period.date;
        } else if (p === '7d') {
            const a = new Date(now);
            a.setDate(now.getDate() - 6);
            period = { date: null, from: ymd(a), to: ymd(now) };
            fromEl.value = period.from;
            toEl.value = period.to;
        }
        updatePeriodLabel();
        didInitialFit = false;
        refreshTraffic();
    }

    function clearPeriod() {
        period = { date: null, from: null, to: null };
        document.getElementById('filterDate').value = '';
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterTo').value = '';
        updatePeriodLabel();
        didInitialFit = false;
        refreshTraffic();
    }

    function applyPeriod() {
        const v = document.getElementById('filterDate').value || null;
        period = { date: v, from: null, to: null };
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterTo').value = '';
        updatePeriodLabel();
        didInitialFit = false;
        refreshTraffic();
    }

    function applyPeriodRange() {
        const from = document.getElementById('filterFrom').value || null;
        const to = document.getElementById('filterTo').value || null;
        period = { date: null, from: from, to: to };
        document.getElementById('filterDate').value = '';
        updatePeriodLabel();
        didInitialFit = false;
        refreshTraffic();
    }

    function updatePeriodLabel() {
        const el = document.getElementById('periodLabel');
        if (!el) return;
        if (period.date) {
            el.textContent = `Mode: livraisons du ${period.date}`;
            return;
        }
        if (period.from || period.to) {
            el.textContent = `Mode: livraisons ${period.from ?? '…'} → ${period.to ?? '…'}`;
            return;
        }
        el.textContent = 'Mode: livraisons actives (temps réel)';
    }

    function pillForLivreur(l) {
        if (!l.is_active) return '<span class="pill gray">Inactif</span>';
        if (l.is_online) {
            return l.is_dispo
                ? '<span class="pill green">En ligne · Dispo</span>'
                : '<span class="pill red">En ligne · Occupé</span>';
        }
        return '<span class="pill gray">Hors ligne</span>';
    }

    function livreurIconHtml(l) {
        const cls = !l.is_active
            ? 'inactive'
            : (l.is_online ? (l.is_dispo ? 'online' : 'busy') : 'offline');

        const svg = `
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M7 17a2 2 0 1 0 0.001 0z"></path>
            <path d="M17 17a2 2 0 1 0 0.001 0z"></path>
            <path d="M7 17h6l3-7h-4l-2 4H8l-2-4H4"></path>
            <path d="M14 7a2 2 0 1 0 0.001 0z"></path>
          </svg>
        `;

        return `<div class="livreur-pin ${cls}" title="${escapeHtml(l.name)}">${svg}</div>`;
    }

    function pillForLivraison(statut) {
        if (statut === 'assignee') return '<span class=\"pill yellow\">Assignée</span>';
        if (statut === 'en_cours') return '<span class=\"pill red\">En cours</span>';
        return '<span class=\"pill gray\">' + escapeHtml(statut) + '</span>';
    }

    function escapeHtml(s) {
        return String(s ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function clearLayers() {
        for (const [, m] of gLayers.livreurs) {
            try { m.setMap(null); } catch (_) {}
        }
        gLayers.livreurs.clear();
        gLayers.destinations.forEach(m => { try { m.setMap(null); } catch (_) {} });
        gLayers.destinations = [];
        gLayers.flux.forEach(p => { try { p.setMap(null); } catch (_) {} });
        gLayers.flux = [];
    }

    function toLatLng(lat, lng) {
        return new google.maps.LatLng(Number(lat), Number(lng));
    }

    function livreurMarkerIcon(l) {
        const cls = !l.is_active
            ? 'inactive'
            : (l.is_online ? (l.is_dispo ? 'online' : 'busy') : 'offline');
        const bg = cls === 'online' ? '#10B981' : (cls === 'busy' ? '#EF4444' : (cls === 'inactive' ? '#6B7280' : '#9CA3AF'));

        const svg = `
          <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34">
            <circle cx="17" cy="17" r="15" fill="${bg}" stroke="#ffffff" stroke-width="2"/>
            <path d="M11 20.5a2 2 0 1 0 0.001 0z" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M23 20.5a2 2 0 1 0 0.001 0z" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11 20.5h7l3.3-8h-4.2l-2.2 4.4h-4l-2.2-4.4H7" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M19 10.5a2 2 0 1 0 0.001 0z" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        `.trim();

        return {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
            scaledSize: new google.maps.Size(34, 34),
            anchor: new google.maps.Point(17, 17),
        };
    }

    function addLivreurMarker(l) {
        if (l.lat == null || l.lng == null) return;
        const marker = new google.maps.Marker({
            position: { lat: Number(l.lat), lng: Number(l.lng) },
            map,
            title: l.name ?? '',
            icon: livreurMarkerIcon(l),
        });

        const info = new google.maps.InfoWindow({
            content:
                `<div style="font-weight:800;">${escapeHtml(l.name)}</div>` +
                `<div style="color:#666; font-size:12px;">${escapeHtml(l.telephone ?? '')}</div>` +
                `<div style="margin-top:6px;">${pillForLivreur(l)}</div>`,
        });
        marker.addListener('click', () => info.open({ anchor: marker, map }));

        gLayers.livreurs.set(l.id, marker);
    }

    function addStraightFluxPolyline(liv, dest, color, gen) {
        const poly = new google.maps.Polyline({
            path: [
                { lat: Number(liv.lat), lng: Number(liv.lng) },
                { lat: Number(dest.lat), lng: Number(dest.lng) },
            ],
            strokeColor: color,
            strokeOpacity: 0.85,
            strokeWeight: 4,
            geodesic: true,
            map,
        });
        if (gen === trafficRenderGeneration) {
            gLayers.flux.push(poly);
        } else {
            poly.setMap(null);
        }
    }

    /**
     * Tracé livreur → destination le long des routes (Google Directions), avec repli ligne droite si échec.
     */
    function addLivraisonFlux(li, gen) {
        const liv = li.livreur;
        const dest = li.destination;
        if (!liv || liv.lat == null || liv.lng == null) return;
        if (!dest || dest.lat == null || dest.lng == null) return;

        const color = li.statut === 'assignee' ? '#CC0000' : '#EF4444';

        const destMarker = new google.maps.Marker({
            position: { lat: Number(dest.lat), lng: Number(dest.lng) },
            map,
            title: dest.label ?? 'Destination',
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 6,
                fillColor: '#FFFFFF',
                fillOpacity: 1,
                strokeColor: '#000000',
                strokeWeight: 2,
            }
        });
        const info = new google.maps.InfoWindow({
            content:
                `<div style="font-weight:800;">Destination</div>` +
                `<div style="color:#666; font-size:12px;">${escapeHtml(dest.label ?? '')}</div>` +
                `<div style="margin-top:6px;">${pillForLivraison(li.statut)}</div>`,
        });
        destMarker.addListener('click', () => info.open({ anchor: destMarker, map }));
        gLayers.destinations.push(destMarker);

        if (!directionsService) {
            addStraightFluxPolyline(liv, dest, color, gen);
            return;
        }

        directionsService.route(
            {
                origin: new google.maps.LatLng(Number(liv.lat), Number(liv.lng)),
                destination: new google.maps.LatLng(Number(dest.lat), Number(dest.lng)),
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                region: 'ci',
            },
            function (response, status) {
                if (gen !== trafficRenderGeneration) {
                    return;
                }
                if (status === 'OK' && response.routes && response.routes[0] && response.routes[0].overview_path && response.routes[0].overview_path.length) {
                    const path = response.routes[0].overview_path.map(function (ll) {
                        return { lat: ll.lat(), lng: ll.lng() };
                    });
                    const poly = new google.maps.Polyline({
                        path: path,
                        strokeColor: color,
                        strokeOpacity: 0.85,
                        strokeWeight: 4,
                        geodesic: false,
                        map: map,
                    });
                    gLayers.flux.push(poly);
                } else {
                    console.warn('Traffic Directions (livraison ' + (li.id ?? '?') + ') : ' + status + ' — ligne directe.');
                    addStraightFluxPolyline(liv, dest, color, gen);
                }
            }
        );
    }

    function renderLists(viewData, fullData) {
        const livreurs = viewData.livreurs ?? [];
        const livraisons = viewData.livraisons ?? [];

        // KPIs restent globaux, même en mode focus
        document.getElementById('kpiLivreursTotal').textContent = fullData.stats?.livreurs_total ?? (fullData.livreurs ?? []).length;
        document.getElementById('kpiLivreursDispo').textContent = fullData.stats?.livreurs_dispo ?? '—';
        document.getElementById('kpiLivActives').textContent = fullData.stats?.livraisons_actives ?? (fullData.livraisons ?? []).length;
        document.getElementById('kpiLivEnCours').textContent = fullData.stats?.livraisons_en_cours ?? '—';
        document.getElementById('serverTime').textContent = fullData.server_time ? `Serveur: ${fullData.server_time}` : '—';

        const livreursList = document.getElementById('livreursList');
        livreursList.innerHTML = livreurs.map(l => {
            const sub = [
                l.type_livreur ? `Type: ${escapeHtml(l.type_livreur)}` : null,
                l.last_location_at ? `GPS: ${escapeHtml(l.last_location_at)}` : null,
            ].filter(Boolean).join(' · ');
            return `
                <div class=\"row\" onclick=\"followLivreur(${l.id}, ${l.lat ?? 'null'}, ${l.lng ?? 'null'})\">
                    <div class=\"title\">
                        <div class=\"name\">${escapeHtml(l.name)}</div>
                        <div>${pillForLivreur(l)}</div>
                    </div>
                    <div class=\"sub\">${escapeHtml(l.telephone ?? '')}</div>
                    ${sub ? `<div class=\"sub\">${sub}</div>` : ''}
                </div>
            `;
        }).join('') || `<div style=\"color:#666;\">Aucun livreur.</div>`;

        const livList = document.getElementById('livraisonsList');
        livList.innerHTML = livraisons.map(li => {
            const liv = li.livreur;
            const dest = li.destination;
            const cmd = li.commande;
            const title = cmd?.ref ? escapeHtml(cmd.ref) : `Livraison #${li.id}`;
            const sub = [
                liv?.name ? `Livreur: ${escapeHtml(liv.name)}` : null,
                dest?.label ? `→ ${escapeHtml(dest.label)}` : null,
            ].filter(Boolean).join(' ');
            return `
                <div class=\"row\" onclick=\"followLivraison(${li.id}, ${liv?.lat ?? 'null'}, ${liv?.lng ?? 'null'}, ${dest?.lat ?? 'null'}, ${dest?.lng ?? 'null'})\">
                    <div class=\"title\">
                        <div class=\"name\">${title}</div>
                        <div>${pillForLivraison(li.statut)}</div>
                    </div>
                    <div class=\"sub\">${sub}</div>
                </div>
            `;
        }).join('') || `<div style=\"color:#666;\">Aucune livraison active.</div>`;
    }

    function followLivreur(id, lat, lng) {
        followed = { type: 'livreur', id: id };
        updateFollowBadge();
        didInitialFit = true; // on ne refit pas automatiquement en focus
        refreshTraffic(); // applique le masquage immédiatement
        if (lat == null || lng == null) return;
        map.setZoom(15);
        map.panTo({ lat: Number(lat), lng: Number(lng) });
    }

    function followLivraison(id, lLat, lLng, dLat, dLng) {
        followed = { type: 'livraison', id: id };
        updateFollowBadge();
        didInitialFit = true;
        refreshTraffic();
        if (lLat == null || lLng == null || dLat == null || dLng == null) return;
        const bounds = new google.maps.LatLngBounds();
        bounds.extend(toLatLng(lLat, lLng));
        bounds.extend(toLatLng(dLat, dLng));
        map.fitBounds(bounds, 60);
    }

    function stopFollow() {
        followed = { type: null, id: null };
        updateFollowBadge();
        didInitialFit = false; // re-permet le fit global
        refreshTraffic();
    }

    function updateFollowBadge() {
        const el = document.getElementById('followBadge');
        if (!el) return;
        if (!followed.type) {
            el.style.display = 'none';
            el.textContent = '';
            el.className = 'pill gray';
            return;
        }
        el.style.display = 'inline-flex';
        el.className = 'pill yellow';
        el.textContent = followed.type === 'livreur'
            ? `Suivi livreur #${followed.id}`
            : `Suivi livraison #${followed.id}`;
    }

    function applyFollowFromData(data) {
        if (!followed.type || !followed.id) return;
        if (followed.type === 'livreur') {
            const l = (data.livreurs ?? []).find(x => x.id === followed.id);
            if (l && l.lat != null && l.lng != null) {
                map.setZoom(15);
                map.panTo({ lat: Number(l.lat), lng: Number(l.lng) });
            }
            return;
        }
        if (followed.type === 'livraison') {
            const li = (data.livraisons ?? []).find(x => x.id === followed.id);
            const liv = li?.livreur;
            const dest = li?.destination;
            if (liv?.lat != null && liv?.lng != null && dest?.lat != null && dest?.lng != null) {
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(toLatLng(liv.lat, liv.lng));
                bounds.extend(toLatLng(dest.lat, dest.lng));
                map.fitBounds(bounds, 60);
            }
        }
    }

    function applyFocusFilter(fullData) {
        const livreurs = fullData.livreurs ?? [];
        const livraisons = fullData.livraisons ?? [];

        if (!followed.type || !followed.id) {
            return { livreurs, livraisons };
        }

        if (followed.type === 'livreur') {
            const l = livreurs.find(x => x.id === followed.id);
            const filteredLivraisons = livraisons.filter(li => (li.livreur?.id ?? null) === followed.id);
            return {
                livreurs: l ? [l] : [],
                livraisons: filteredLivraisons,
            };
        }

        if (followed.type === 'livraison') {
            const li = livraisons.find(x => x.id === followed.id);
            const livreurId = li?.livreur?.id ?? null;
            const l = livreurId ? livreurs.find(x => x.id === livreurId) : null;
            return {
                livreurs: l ? [l] : [],
                livraisons: li ? [li] : [],
            };
        }

        return { livreurs, livraisons };
    }

    function _wrapEl() {
        // Le conteneur direct de la page
        return document.querySelector('.traffic-wrap');
    }

    function _afterLayoutChange() {
        try {
            if (map && window.google && google.maps) {
                // Force un redraw Google Maps après changement de layout
                google.maps.event.trigger(map, 'resize');
                // Si on est en suivi, on recentre ; sinon on garde la position actuelle
                if (lastDataCache) applyFollowFromData(lastDataCache);
            }
        } catch (_) {}
    }

    function toggleOnlyMap() {
        const el = _wrapEl();
        if (!el) return;
        const on = el.classList.toggle('only-map');
        if (on) el.classList.remove('only-side');
        setTimeout(_afterLayoutChange, 80);
    }

    function toggleOnlySide() {
        const el = _wrapEl();
        if (!el) return;
        const on = el.classList.toggle('only-side');
        if (on) el.classList.remove('only-map');
        setTimeout(_afterLayoutChange, 80);
    }

    function resetLayout() {
        const el = _wrapEl();
        if (!el) return;
        el.classList.remove('only-map');
        el.classList.remove('only-side');
        setTimeout(_afterLayoutChange, 80);
    }

    async function refreshTraffic() {
        try {
            if (!map || !window.google || !google.maps) {
                // Map pas encore initialisée (callback Google Maps non déclenché)
                return;
            }
            const url = new URL(DATA_URL, window.location.origin);
            if (period.date) url.searchParams.set('date', period.date);
            if (period.from) url.searchParams.set('from', period.from);
            if (period.to) url.searchParams.set('to', period.to);

            const resp = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
            const json = await resp.json();
            const data = json?.data ?? {};
            lastDataCache = data;
            const view = applyFocusFilter(data);

            trafficRenderGeneration += 1;
            const gen = trafficRenderGeneration;
            clearLayers();
            renderLists(view, data);

            const bounds = new google.maps.LatLngBounds();
            let pointsCount = 0;
            (view.livreurs ?? []).forEach(l => {
                addLivreurMarker(l);
                if (l.lat != null && l.lng != null) {
                    bounds.extend(toLatLng(l.lat, l.lng));
                    pointsCount++;
                }
            });
            (view.livraisons ?? []).forEach(li => {
                addLivraisonFlux(li, gen);
                const liv = li.livreur;
                const dest = li.destination;
                if (liv?.lat != null && liv?.lng != null) {
                    bounds.extend(toLatLng(liv.lat, liv.lng));
                    pointsCount++;
                }
                if (dest?.lat != null && dest?.lng != null) {
                    bounds.extend(toLatLng(dest.lat, dest.lng));
                    pointsCount++;
                }
            });

            if (!didInitialFit && pointsCount >= 2) {
                map.fitBounds(bounds, 60);
                didInitialFit = true;
            }

            // Si on suit un livreur ou une livraison, on recentre à chaque refresh
            applyFollowFromData(data);
        } catch (e) {
            console.error('Traffic refresh error', e);
        }
    }

    // initTrafficMap est appelé par le callback Google Maps
    updatePeriodLabel();
    // On attend que la map soit prête, puis on démarre.
    (function waitMapReady() {
        const tick = () => {
            if (map && window.google && google.maps) {
                refreshTraffic();
                refreshTimer = setInterval(refreshTraffic, REFRESH_MS);
                return;
            }
            setTimeout(tick, 120);
        };
        tick();
    })();

    // Temps réel via Pusher (public pour démarrer)
    // On déclenche un refresh léger dès qu’un event arrive (debounce).
    (function initRealtime() {
        if (!PUSHER_KEY) return;
        try {
            const pusher = new Pusher(PUSHER_KEY, {
                cluster: PUSHER_CLUSTER,
                forceTLS: true,
            });
            const channel = pusher.subscribe('traffic-livreur');

            let t = null;
            const kick = () => {
                if (t) return;
                t = setTimeout(() => {
                    t = null;
                    refreshTraffic();
                }, 350);
            };

            channel.bind('livreur.location.updated', kick);
            channel.bind('livreur.status.updated', kick);
            channel.bind('livraison.status.updated', kick);
        } catch (e) {
            console.error('Pusher init error', e);
        }
    })();
</script>
@endsection

