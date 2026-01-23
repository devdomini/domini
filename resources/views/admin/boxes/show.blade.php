@extends('admin.layout')

@section('title', 'Détails Box')
@section('page-title', $box->nom)

@section('content')
    <!-- En-tête -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('admin.boxes.index') }}" style="color: #666; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-weight: 500;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Retour aux boxes
            </a>
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $box->nom }}</h2>
            <div style="font-size: 0.875rem; color: #666;">{{ $box->ref }}</div>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            @if($box->est_actif)
                <span class="badge" style="background-color: #E8F5E9; color: #2d9248; font-size: 1rem; padding: 0.5rem 1rem;">Actif</span>
            @else
                <span class="badge" style="background-color: #F5F5F5; color: #666; font-size: 1rem; padding: 0.5rem 1rem;">Inactif</span>
            @endif
        </div>
    </div>

    <!-- Informations de la Box -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Informations de la Box</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div>
                    <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.5rem;">Entreprise</div>
                    <div style="font-weight: 600; font-size: 1.125rem;">{{ $box->entreprise->nom }}</div>
                    <div style="font-size: 0.875rem; color: #666; margin-top: 0.25rem;">{{ $box->entreprise->ville }}, {{ $box->entreprise->pays }}</div>
                </div>

                @if($box->adresse)
                <div>
                    <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.5rem;">Adresse</div>
                    <div style="font-weight: 600; display: flex; align-items: start; gap: 0.5rem;">
                        <svg style="width: 16px; height: 16px; flex-shrink: 0; margin-top: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $box->adresse }}
                    </div>
                </div>
                @endif

                @if($box->lat && $box->long)
                <div>
                    <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.5rem;">Coordonnées GPS</div>
                    <div style="font-weight: 600;">{{ $box->lat }}, {{ $box->long }}</div>
                    <a href="https://www.google.com/maps?q={{ $box->lat }},{{ $box->long }}" target="_blank" style="color: #D9542A; text-decoration: none; font-size: 0.875rem; margin-top: 0.25rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                        Voir sur Google Maps
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                @endif

                <div>
                    <div style="font-size: 0.75rem; color: #666; text-transform: uppercase; margin-bottom: 0.5rem;">Date de création</div>
                    <div style="font-weight: 600;">{{ $box->created_at->format('d/m/Y') }}</div>
                    <div style="font-size: 0.875rem; color: #666;">{{ $box->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des Casiers -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #3A3A3A, #2A2A2A); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Total Casiers</div>
            <div style="font-size: 2.5rem; font-weight: 900;">{{ $box->capacite }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #10B981, #059669); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Casiers Libres</div>
            <div style="font-size: 2.5rem; font-weight: 900;">{{ $box->casiersLibres() }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #EF4444, #DC2626); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Casiers Occupés</div>
            <div style="font-size: 2.5rem; font-weight: 900;">{{ $box->casiersOccupes() }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #F7B801, #e5a900); border-radius: 12px; padding: 1.5rem; color: white;">
            <div style="font-size: 0.875rem; opacity: 0.9;">Taux d'Occupation</div>
            <div style="font-size: 2.5rem; font-weight: 900;">
                {{ $box->capacite > 0 ? round(($box->casiersOccupes() / $box->capacite) * 100) : 0 }}%
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card" style="margin-bottom: 1rem;">
        <div class="card-body" style="padding: 1rem;">
            <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; position: relative;">
                    <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #999;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="searchCasier" placeholder="Rechercher par numéro, référence ou QR code..." style="width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.5rem; border: 1px solid #E5E5E5; border-radius: 8px;">
                </div>
                <select id="filterStatut" style="padding: 0.75rem; border: 1px solid #E5E5E5; border-radius: 8px;">
                    <option value="">Tous les statuts</option>
                    <option value="libre">Libre</option>
                    <option value="occupe">Occupé</option>
                    <option value="reserve">Réservé</option>
                    <option value="hors_service">Hors service</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Liste des Casiers -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des Casiers ({{ $box->casiers->count() }})</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 80px;">N°</th>
                            <th>Référence</th>
                            <th>QR Code</th>
                            <th>Employé</th>
                            <th>Statut</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="casiersTableBody">
                        @foreach($box->casiers->sortBy('numero_casier') as $casier)
                        <tr class="casier-row" data-numero="{{ $casier->numero_casier }}" data-ref="{{ $casier->ref }}" data-qr="{{ $casier->qr_code }}" data-statut="{{ $casier->statut }}">
                            <td style="text-align: center;">
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #D9542A, #c13d18); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-size: 1.125rem;">
                                    {{ $casier->numero_casier }}
                                </div>
                            </td>
                            <td>
                                <div style="font-family: 'Courier New', monospace; font-weight: 600; color: #3A3A3A;">{{ $casier->ref }}</div>
                            </td>
                            <td>
                                <div style="font-family: 'Courier New', monospace; font-size: 0.875rem; color: #666; background: #F5F5F5; padding: 0.25rem 0.5rem; border-radius: 4px; display: inline-block;">
                                    {{ $casier->qr_code }}
                                </div>
                            </td>
                            <td>
                                @if($casier->employe)
                                    <div style="font-weight: 600;">{{ $casier->employe->name }}</div>
                                    <div style="font-size: 0.75rem; color: #666;">{{ $casier->employe->email }}</div>
                                @else
                                    <span style="color: #999;">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statutColors = [
                                        'libre' => 'background-color: #E8F5E9; color: #2d9248;',
                                        'occupe' => 'background-color: #FFEBEE; color: #C62828;',
                                        'reserve' => 'background-color: #FFF3E0; color: #E65100;',
                                        'hors_service' => 'background-color: #F5F5F5; color: #666;'
                                    ];
                                    $statutLabels = [
                                        'libre' => 'Libre',
                                        'occupe' => 'Occupé',
                                        'reserve' => 'Réservé',
                                        'hors_service' => 'Hors service'
                                    ];
                                @endphp
                                <span class="badge" style="{{ $statutColors[$casier->statut] ?? '' }}">
                                    {{ $statutLabels[$casier->statut] ?? $casier->statut }}
                                </span>
                            </td>
                            <td>
                                <button onclick="showQRCode('{{ $casier->qr_code }}', '{{ $casier->ref }}')" class="btn-icon" style="background-color: #3A3A3A;" title="Voir QR Code">
                                    <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($box->casiers->isEmpty())
            <div style="text-align: center; padding: 3rem; color: #666;">
                <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Aucun casier</h3>
                <p>Cette box n'a pas encore de casiers générés.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal QR Code -->
    <div id="qrCodeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 400px; width: 90%; text-align: center;">
            <h3 style="margin-top: 0; margin-bottom: 1rem;">QR Code du Casier</h3>
            <div id="qrCodeRef" style="font-family: 'Courier New', monospace; font-weight: 600; color: #666; margin-bottom: 1rem;"></div>
            <div id="qrCodeImage" style="background: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: center;"></div>
            <div id="qrCodeValue" style="font-family: 'Courier New', monospace; font-size: 0.875rem; color: #999; margin-bottom: 1.5rem;"></div>
            <button onclick="hideQRCodeModal()" class="btn btn-secondary" style="width: 100%;">Fermer</button>
        </div>
    </div>
@endsection

@section('scripts')
<!-- QRCode.js Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
// Recherche et filtrage
const searchInput = document.getElementById('searchCasier');
const filterStatut = document.getElementById('filterStatut');

function filterCasiers() {
    const searchTerm = searchInput.value.toLowerCase();
    const statutFilter = filterStatut.value;
    const rows = document.querySelectorAll('.casier-row');

    rows.forEach(row => {
        const numero = row.dataset.numero.toLowerCase();
        const ref = row.dataset.ref.toLowerCase();
        const qr = row.dataset.qr.toLowerCase();
        const statut = row.dataset.statut;

        const matchSearch = numero.includes(searchTerm) || ref.includes(searchTerm) || qr.includes(searchTerm);
        const matchStatut = !statutFilter || statut === statutFilter;

        if (matchSearch && matchStatut) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', filterCasiers);
filterStatut.addEventListener('change', filterCasiers);

// Modal QR Code
let currentQRCode = null;

function showQRCode(qrCode, ref) {
    document.getElementById('qrCodeRef').textContent = ref;
    document.getElementById('qrCodeValue').textContent = qrCode;
    
    // Clear previous QR code
    const qrContainer = document.getElementById('qrCodeImage');
    qrContainer.innerHTML = '';
    
    // Generate new QR code
    currentQRCode = new QRCode(qrContainer, {
        text: qrCode,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    document.getElementById('qrCodeModal').style.display = 'flex';
}

function hideQRCodeModal() {
    document.getElementById('qrCodeModal').style.display = 'none';
}

// Close modal on outside click
document.getElementById('qrCodeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideQRCodeModal();
    }
});

// Close modal on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideQRCodeModal();
    }
});
</script>
@endsection
