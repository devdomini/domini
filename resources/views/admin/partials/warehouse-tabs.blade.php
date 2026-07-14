{{-- Onglets : fiche entrepôt (informations vs trajet) --}}
@php
    $isInfo = request()->routeIs('admin.warehouses.edit');
    $isTrajet = request()->routeIs('admin.trajets.edit');
    $isCarte = request()->routeIs('admin.trajets.map');
@endphp
<div style="display: flex; gap: 0.25rem; margin-bottom: 1.75rem; border-bottom: 2px solid #E5E5E5; flex-wrap: wrap;">
    <a href="{{ route('admin.warehouses.edit', $warehouse) }}"
       style="padding: 0.75rem 1.25rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; border-bottom: 3px solid {{ $isInfo ? '#FF0000' : 'transparent' }}; color: {{ $isInfo ? '#FF0000' : '#6B7280' }}; margin-bottom: -2px;">
        Informations
    </a>
    <a href="{{ route('admin.trajets.edit', $warehouse) }}"
       style="padding: 0.75rem 1.25rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; border-bottom: 3px solid {{ $isTrajet ? '#FF0000' : 'transparent' }}; color: {{ $isTrajet ? '#FF0000' : '#6B7280' }}; margin-bottom: -2px;">
        Trajet de livraison
    </a>
    <a href="{{ route('admin.trajets.map', $warehouse) }}"
       style="padding: 0.75rem 1.25rem; text-decoration: none; font-weight: 600; font-size: 0.95rem; border-bottom: 3px solid {{ $isCarte ? '#FF0000' : 'transparent' }}; color: {{ $isCarte ? '#FF0000' : '#6B7280' }}; margin-bottom: -2px;">
        Carte du trajet
    </a>
</div>
