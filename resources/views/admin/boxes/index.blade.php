@extends('admin.layout')

@section('title', 'Boxes & Casiers')
@section('page-title', 'Gestion des Boxes & Casiers')

@section('content')
    <!-- Actions Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #000000;">Toutes les boxes</h2>
            <p style="color: #666; margin-top: 0.25rem;">Gérez les points de livraison et leurs casiers intelligents</p>
        </div>
        <a href="{{ route('admin.boxes.create') }}" class="btn btn-primary">
            + Nouvelle Box
        </a>
    </div>

    <!-- Liste des boxes -->
    <div style="display: grid; gap: 1.5rem;">
        @forelse($boxes as $box)
        <div class="card">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr auto; gap: 2rem;">
                    <!-- Info Box -->
                    <div>
                        <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                            <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #FF0000, #CC0000); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">{{ $box->nom }}</h3>
                                    @if($box->est_actif)
                                        <span class="badge" style="background-color: #E8F5E9; color: #2d9248;">Actif</span>
                                    @else
                                        <span class="badge" style="background-color: #F5F5F5; color: #666;">Inactif</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.875rem; color: #666; margin-bottom: 0.25rem;">{{ $box->ref }}</div>
                                <div style="font-size: 0.875rem; color: #666;">
                                    <strong>Entreprise:</strong> {{ $box->entreprise->nom }}
                                </div>
                                @if($box->adresse)
                                <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $box->adresse }}
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Stats Casiers -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Total Casiers</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #000000;">{{ $box->capacite }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Libres</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #10B981;">{{ $box->casiersLibres() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Occupés</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #EF4444;">{{ $box->casiersOccupes() }}</div>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.25rem;">Taux d'occupation</div>
                                <div style="font-size: 1.5rem; font-weight: 900; color: #CC0000;">
                                    {{ $box->capacite > 0 ? round(($box->casiersOccupes() / $box->capacite) * 100) : 0 }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="{{ route('admin.boxes.show', $box->id) }}" class="btn btn-primary" style="text-align: center;">
                            Voir Détails
                        </a>
                        <button type="button" onclick="openPrintQrModal({{ $box->id }}, '{{ e($box->nom) }}', '{{ e($box->ref) }}')" class="btn" style="background-color: #000000; color: white; text-align: center;">
                            Imprimer QR
                        </button>
                        <a href="{{ route('admin.boxes.edit', $box->id) }}" class="btn btn-secondary" style="text-align: center;">
                            Modifier
                        </a>
                        <button onclick="showAjouterCasiersModal({{ $box->id }})" class="btn" style="background-color: #10B981; color: white; text-align: center;">
                            + Ajouter Casiers
                        </button>
                        <form action="{{ route('admin.boxes.toggle', $box->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn" style="width: 100%; background-color: {{ $box->est_actif ? '#CC0000' : '#10B981' }}; color: white;">
                                {{ $box->est_actif ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.boxes.destroy', $box->id) }}" method="POST" onsubmit="return confirm('Supprimer cette box et tous ses casiers ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Impression QR -->
        <div id="printQrModal{{ $box->id }}" style="display: none; position: fixed; inset: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.55); z-index: 1100; justify-content: center; align-items: center; padding: 1rem;">
            <div style="background: white; border-radius: 12px; max-width: 750px; width: 100%; overflow: hidden;">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #E5E5E5; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #000000;">Imprimer les QR codes</div>
                        <div id="printQrSubtitle{{ $box->id }}" style="font-size: 0.85rem; color: #666;"></div>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" class="btn btn-secondary" onclick="closePrintQrModal({{ $box->id }})">Fermer</button>
                        <button type="button" class="btn btn-primary" onclick="printSelectedQr({{ $box->id }})">Imprimer</button>
                    </div>
                </div>
                <div style="padding: 1rem 1.25rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                        <input id="printQrSearch{{ $box->id }}" type="text" placeholder="Rechercher casier (N°, ref, QR)..." style="flex: 1; min-width: 260px; padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 10px;">
                        <button class="btn btn-secondary" onclick="loadCasiersForPrint({{ $box->id }})">Rechercher</button>
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #000000;">
                            <input id="printQrSelectAll{{ $box->id }}" type="checkbox" onchange="toggleSelectAll({{ $box->id }})">
                            Tout sélectionner
                        </label>
                    </div>
                    <div id="printQrStatus{{ $box->id }}" style="margin-top: 0.75rem; font-size: 0.85rem; color: #666;"></div>
                    <div id="printQrList{{ $box->id }}" style="margin-top: 1rem; max-height: 55vh; overflow: auto; border: 1px solid #E5E5E5; border-radius: 12px;"></div>
                    <div style="margin-top: 0.75rem; font-size: 0.85rem; color: #666;">
                        Astuce : si rien n’est coché, on imprime <strong>tous</strong> les casiers de la box.
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Ajouter Casiers -->
        <div id="ajouterCasiersModal{{ $box->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
            <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 90%;">
                <h3 style="margin-top: 0;">Ajouter des casiers</h3>
                <form action="{{ route('admin.boxes.casiers.ajouter', $box->id) }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem;">Nombre de casiers à ajouter *</label>
                        <input type="number" name="nombre" min="1" max="50" required style="width: 100%; padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 8px;">
                        <small style="color: #666; font-size: 0.75rem;">Maximum: 50 casiers à la fois</small>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Ajouter</button>
                        <button type="button" onclick="hideAjouterCasiersModal({{ $box->id }})" class="btn btn-secondary">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 4rem;">
                <svg style="width: 80px; height: 80px; margin: 0 auto 1.5rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Aucune box</h3>
                <p style="color: #666; margin-bottom: 1.5rem;">Commencez par créer votre première box</p>
                <a href="{{ route('admin.boxes.create') }}" class="btn btn-primary">
                    + Créer une Box
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    {{ $boxes->links('vendor.pagination.domini') }}
@endsection

@section('scripts')
<script>
function showAjouterCasiersModal(id) {
    document.getElementById('ajouterCasiersModal' + id).style.display = 'flex';
}

function hideAjouterCasiersModal(id) {
    document.getElementById('ajouterCasiersModal' + id).style.display = 'none';
}

// Close modal on outside click
document.querySelectorAll('[id^="ajouterCasiersModal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

// Impression QR
function openPrintQrModal(boxId, boxNom, boxRef) {
    const modal = document.getElementById('printQrModal' + boxId);
    document.getElementById('printQrSubtitle' + boxId).textContent = `${boxNom} · ${boxRef}`;
    document.getElementById('printQrSearch' + boxId).value = '';
    document.getElementById('printQrSelectAll' + boxId).checked = false;
    document.getElementById('printQrStatus' + boxId).textContent = 'Chargement des casiers…';
    document.getElementById('printQrList' + boxId).innerHTML = '';
    modal.style.display = 'flex';
    loadCasiersForPrint(boxId);
}

function closePrintQrModal(boxId) {
    document.getElementById('printQrModal' + boxId).style.display = 'none';
}

document.querySelectorAll('[id^="printQrModal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

async function loadCasiersForPrint(boxId) {
    const q = (document.getElementById('printQrSearch' + boxId).value || '').trim();
    const status = document.getElementById('printQrStatus' + boxId);
    const list = document.getElementById('printQrList' + boxId);
    list.innerHTML = '';
    status.textContent = 'Chargement…';

    try {
        const url = new URL(`/admin/boxes/${boxId}/casiers/list`, window.location.origin);
        if (q) url.searchParams.set('q', q);
        const resp = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        const items = data?.data ?? [];
        if (!Array.isArray(items) || items.length === 0) {
            status.textContent = 'Aucun casier trouvé.';
            return;
        }
        status.textContent = `${items.length} casier(s). Sélectionnez ceux à imprimer.`;

        list.innerHTML = `
            <div style="display: grid;">
                ${items.map(c => {
                    return `
                        <label style="display:flex; gap: 0.75rem; align-items:center; padding: 0.75rem 0.9rem; border-bottom: 1px solid #F0F0F0; cursor: pointer;">
                            <input class="qrCasierCheckbox" type="checkbox" data-box="${boxId}" value="${c.id}">
                            <div style="flex: 1;">
                                <div style="font-weight: 800; color: #000000;">Casier N° ${c.numero_casier} · ${escapeHtml(c.ref)}</div>
                                <div style="font-size: 0.85rem; color: #666; font-family: 'Courier New', monospace;">${escapeHtml(c.qr_code)}</div>
                            </div>
                            <span style="font-size: 0.75rem; color: #666; background: #F5F5F5; padding: 0.25rem 0.5rem; border-radius: 999px;">${escapeHtml(c.statut)}</span>
                        </label>
                    `;
                }).join('')}
            </div>
        `;
    } catch (e) {
        status.textContent = 'Erreur de chargement.';
    }
}

function toggleSelectAll(boxId) {
    const selectAll = document.getElementById('printQrSelectAll' + boxId).checked;
    document.querySelectorAll(`.qrCasierCheckbox[data-box="${boxId}"]`).forEach(cb => {
        cb.checked = selectAll;
    });
}

function printSelectedQr(boxId) {
    const selected = Array.from(document.querySelectorAll(`.qrCasierCheckbox[data-box="${boxId}"]:checked`)).map(cb => cb.value);
    const url = new URL(`/admin/boxes/${boxId}/casiers/print`, window.location.origin);
    if (selected.length > 0) {
        url.searchParams.set('casier_ids', selected.join(','));
    }
    // Certains navigateurs bloquent les popups : fallback en navigation directe
    const w = window.open(url.toString(), '_blank');
    if (!w) {
        window.location.href = url.toString();
    }
}

function escapeHtml(s) {
    return String(s ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>
@endsection
