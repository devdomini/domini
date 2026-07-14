@extends('admin.layout')

@section('title', 'Impression QR')
@section('page-title', 'Impression QR Codes')

@section('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .topbar, .sidebar { display: none !important; }
        .print-page { padding: 0 !important; }
    }

    .print-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .qr-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .qr-card {
        border: 1px solid #E5E5E5;
        border-radius: 12px;
        padding: 12px;
        background: white;
        break-inside: avoid;
    }

    .qr-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
        font-size: 12px;
        color: #444;
        font-family: 'Inter', sans-serif;
    }

    .qr-ref {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        color: #111;
    }

    .qr-box {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 6px 0;
    }

    @media (max-width: 900px) {
        .qr-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
</style>
@endsection

@section('content')
<div class="print-page">
    <div class="card no-print">
        <div class="card-body" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <div>
                <div style="font-size: 1.25rem; font-weight: 800;">QR Codes des casiers</div>
                <div style="color: #666; margin-top: 0.25rem;">
                    Box: <strong>{{ $box->nom }}</strong> · {{ $box->ref }} · Entreprise: {{ $box->entreprise?->nom ?? '—' }}
                    @if($selectedCount)
                        · Sélection: {{ $selectedCount }} casier(s)
                    @endif
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('admin.boxes.show', $box->id) }}" class="btn btn-secondary">Retour</a>
                <button class="btn btn-primary" onclick="window.print()">Imprimer</button>
            </div>
        </div>
    </div>

    <div class="print-header">
        <div>
            <div style="font-size: 16px; font-weight: 900;">{{ $box->nom }}</div>
            <div style="font-size: 12px; color: #666;">{{ $box->ref }} · {{ $box->entreprise?->nom ?? '—' }}</div>
        </div>
        <div style="font-size: 12px; color: #666;">
            Généré le {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    @if($casiers->isEmpty())
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 3rem; color: #666;">
                Aucun casier à imprimer.
            </div>
        </div>
    @else
        <div class="qr-grid" id="qrGrid">
            @foreach($casiers as $c)
                <div class="qr-card">
                    <div class="qr-box" id="qr_{{ $c->id }}" data-qr="{{ $c->qr_code }}"></div>
                    <div class="qr-meta">
                        <div>
                            <div style="font-size: 11px; color: #666;">Casier</div>
                            <div style="font-weight: 800;">N° {{ $c->numero_casier }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; color: #666;">Réf</div>
                            <div class="qr-ref">{{ $c->ref }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 8px; font-size: 11px; color: #888; text-align: center; font-family: 'Courier New', monospace;">
                        {{ $c->qr_code }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    function renderQRCodes() {
        if (typeof QRCode === 'undefined') {
            console.error('QRCode.js non chargé');
            return;
        }
        document.querySelectorAll('[id^="qr_"][data-qr]').forEach((el) => {
            const value = el.getAttribute('data-qr');
            if (!value) return;
            // Nettoyer un éventuel rendu précédent
            el.innerHTML = '';
            new QRCode(el, {
                text: value,
                width: 170,
                height: 170,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderQRCodes);
    } else {
        renderQRCodes();
    }
</script>
@endsection

