@extends('admin.layout')

@section('title', 'Trajet — ' . $warehouse->name)
@section('page-title', 'Trajet de livraison')

@section('content')
    <div style="max-width: 960px; margin: 0 auto;">
        <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('admin.trajets.index') }}" style="color: #3B82F6; text-decoration: none; font-weight: 600;">← Tous les trajets</a>
            <a href="{{ route('admin.trajets.map', $warehouse) }}" style="color: #3B82F6; text-decoration: none; font-weight: 600;">Voir la carte du trajet →</a>
        </div>

        @include('admin.partials.warehouse-tabs', ['warehouse' => $warehouse])

        @if(session('success'))
            <div style="background: #D4EDDA; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background: #F8D7DA; color: #721C24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">{{ session('error') }}</div>
        @endif

        <div style="background: white; padding: 1.75rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.5rem;">{{ $warehouse->name }}</h2>
                    <p style="color: #666; font-size: 0.95rem; margin: 0;">
                        Glissez les étapes pour changer l’ordre, ou utilisez les flèches. Les entreprises « disponibles » peuvent être ajoutées au trajet.
                    </p>
                </div>
                @if(!$eligible->isEmpty())
                    <form action="{{ route('admin.trajets.auto-proximite', $warehouse) }}" method="POST" style="flex-shrink: 0;"
                          onsubmit="return confirm('Générer l’ordre automatiquement ? Toutes les entreprises éligibles seront ajoutées au trajet, classées du plus proche au plus loin de l’entrepôt (distance à vol d’oiseau). Les entreprises sans GPS seront à la fin.');">
                        @csrf
                        <button type="submit" style="background: #3B82F6; color: white; padding: 0.65rem 1.1rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; white-space: nowrap;">
                            Générer auto (plus proche → plus loin)
                        </button>
                    </form>
                @endif
            </div>
            <p style="color: #9CA3AF; font-size: 0.8rem; margin: 0.75rem 0 0;">
                Le calcul utilise les coordonnées de l’entrepôt et, pour chaque entreprise, le GPS de l’entreprise ou à défaut celui de sa commune.
            </p>
        </div>

        @if($eligible->isEmpty())
            <div style="background: #FFFBEB; border: 1px solid #FCD34D; color: #92400E; padding: 1.25rem; border-radius: 8px;">
                Aucune entreprise éligible : il faut au moins une entreprise <strong>active</strong> avec une <strong>commune</strong> liée à cet entrepôt.
            </div>
        @else
            <form action="{{ route('admin.trajets.update', $warehouse) }}" method="POST" id="trajet-form">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
                    {{-- Ordre du trajet --}}
                    <div style="background: white; padding: 1.25rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #000000;">Ordre de livraison</h3>
                        <ol id="trajet-list" style="list-style: none; padding: 0; margin: 0; min-height: 120px;">
                            @foreach($orderedItems as $item)
                                <li draggable="true" data-id="{{ $item->entreprise_id }}" class="trajet-row" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.75rem; margin-bottom: 0.5rem; background: #FDFBF8; border: 1px solid #E5E5E5; border-radius: 8px; cursor: grab;">
                                    <span style="color: #FF0000; font-weight: 800; min-width: 1.5rem;" class="step-num">{{ $loop->iteration }}</span>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; color: #1A1A1A;">{{ $item->entreprise->nom }}</div>
                                        <div style="font-size: 0.8rem; color: #6B7280;">{{ $item->entreprise->commune?->nom ?? '—' }}</div>
                                    </div>
                                    <input type="hidden" name="entreprise_ids[]" value="{{ $item->entreprise_id }}">
                                    <button type="button" class="btn-up" style="border: none; background: #E5E5E5; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;" title="Monter">↑</button>
                                    <button type="button" class="btn-down" style="border: none; background: #E5E5E5; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;" title="Descendre">↓</button>
                                    <button type="button" class="btn-remove" style="border: none; background: #FEE2E2; color: #B91C1C; width: 32px; height: 32px; border-radius: 6px; cursor: pointer;" title="Retirer">×</button>
                                </li>
                            @endforeach
                        </ol>
                        @if($orderedItems->isEmpty())
                            <p id="trajet-empty" style="color: #9CA3AF; font-size: 0.9rem; padding: 1rem 0;">Ajoutez des entreprises depuis la liste de droite.</p>
                        @endif
                    </div>

                    {{-- Disponibles --}}
                    <div style="background: white; padding: 1.25rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #000000;">Disponibles</h3>
                        <ul id="disponibles-list" style="list-style: none; padding: 0; margin: 0;">
                            @foreach($disponibles as $e)
                                <li class="disp-row" data-id="{{ $e->id }}" style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; padding: 0.65rem 0.75rem; margin-bottom: 0.5rem; background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 8px;">
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 600;">{{ $e->nom }}</div>
                                        <div style="font-size: 0.8rem; color: #6B7280;">{{ $e->commune?->nom ?? '—' }}</div>
                                    </div>
                                    <button type="button" class="btn-add" data-nom="{{ $e->nom }}" data-commune="{{ $e->commune?->nom ?? '—' }}" style="background: #10B981; color: white; border: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 600; cursor: pointer; white-space: nowrap;">+ Ajouter</button>
                                </li>
                            @endforeach
                        </ul>
                        @if($disponibles->isEmpty())
                            <p style="color: #9CA3AF; font-size: 0.9rem;">Toutes les entreprises éligibles sont dans le trajet.</p>
                        @endif
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <a href="{{ route('admin.trajets.index') }}" style="padding: 0.75rem 1.5rem; border: 1px solid #D1D5DB; border-radius: 8px; text-decoration: none; color: #374151; font-weight: 600;">Annuler</a>
                    <button type="submit" style="background: #FF0000; color: white; padding: 0.75rem 2rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Enregistrer le trajet</button>
                </div>
            </form>
        @endif
    </div>
@endsection

@section('scripts')
<script>
(function() {
    const list = document.getElementById('trajet-list');
    const emptyMsg = document.getElementById('trajet-empty');
    if (!list) return;

    function renumber() {
        list.querySelectorAll('.step-num').forEach((el, i) => { el.textContent = i + 1; });
        if (emptyMsg) emptyMsg.style.display = list.children.length ? 'none' : 'block';
    }

    function moveLi(li, dir) {
        if (dir < 0 && li.previousElementSibling) list.insertBefore(li, li.previousElementSibling);
        if (dir > 0 && li.nextElementSibling) list.insertBefore(li.nextElementSibling, li);
        renumber();
    }

    list.addEventListener('click', function(e) {
        const li = e.target.closest('.trajet-row');
        if (!li) return;
        if (e.target.closest('.btn-up')) moveLi(li, -1);
        if (e.target.closest('.btn-down')) moveLi(li, 1);
        if (e.target.closest('.btn-remove')) {
            const id = li.dataset.id;
            li.remove();
            const disp = document.querySelector('.disp-row[data-id="' + id + '"]');
            if (disp) disp.style.display = '';
            renumber();
        }
    });

    document.querySelectorAll('.btn-add').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const row = btn.closest('.disp-row');
            const id = row.dataset.id;
            const nom = btn.dataset.nom;
            const commune = btn.dataset.commune;
            row.style.display = 'none';
            const li = document.createElement('li');
            li.className = 'trajet-row';
            li.draggable = true;
            li.dataset.id = id;
            li.style.cssText = 'display:flex;align-items:center;gap:0.5rem;padding:0.65rem 0.75rem;margin-bottom:0.5rem;background:#FDFBF8;border:1px solid #E5E5E5;border-radius:8px;cursor:grab;';
            li.innerHTML =
                '<span style="color:#FF0000;font-weight:800;min-width:1.5rem;" class="step-num">0</span>' +
                '<div style="flex:1;min-width:0;"><div style="font-weight:600;color:#1A1A1A;">' + escapeHtml(nom) + '</div>' +
                '<div style="font-size:0.8rem;color:#6B7280;">' + escapeHtml(commune) + '</div></div>' +
                '<input type="hidden" name="entreprise_ids[]" value="' + id + '">' +
                '<button type="button" class="btn-up" style="border:none;background:#E5E5E5;width:32px;height:32px;border-radius:6px;cursor:pointer;">↑</button>' +
                '<button type="button" class="btn-down" style="border:none;background:#E5E5E5;width:32px;height:32px;border-radius:6px;cursor:pointer;">↓</button>' +
                '<button type="button" class="btn-remove" style="border:none;background:#FEE2E2;color:#B91C1C;width:32px;height:32px;border-radius:6px;cursor:pointer;">×</button>';
            list.appendChild(li);
            renumber();
        });
    });

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    let dragEl = null;
    list.addEventListener('dragstart', function(e) {
        if (!e.target.classList.contains('trajet-row')) return;
        dragEl = e.target;
        e.target.style.opacity = '0.5';
    });
    list.addEventListener('dragend', function(e) {
        if (dragEl) dragEl.style.opacity = '1';
        dragEl = null;
        renumber();
    });
    list.addEventListener('dragover', function(e) { e.preventDefault(); });
    list.addEventListener('drop', function(e) {
        e.preventDefault();
        if (!dragEl) return;
        const target = e.target.closest('.trajet-row');
        if (!target || target === dragEl) return;
        const rect = target.getBoundingClientRect();
        const after = (e.clientY - rect.top) > rect.height / 2;
        if (after) {
            list.insertBefore(dragEl, target.nextSibling);
        } else {
            list.insertBefore(dragEl, target);
        }
        renumber();
    });
})();
</script>
@endsection
