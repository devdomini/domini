@foreach($commandes as $commande)
<div id="affecterModal{{ $commande->id }}" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 2rem; width: 90%; max-width: 500px;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: #000000;">Affecter un livreur</h3>
        <form method="POST" action="{{ route('admin.commandes.affecter-livreur', $commande->id) }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #000000;">Sélectionner un livreur</label>
                <select name="livreur_id" required style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 6px;">
                    <option value="">-- Choisir --</option>
                    @foreach($livreurs as $livreur)
                    <option value="{{ $livreur->id }}">{{ $livreur->name }} - {{ $livreur->telephone }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="hideAffecterModal({{ $commande->id }})" style="padding: 0.5rem 1rem; background: #F5F5F5; color: #666; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Annuler
                </button>
                <button type="submit" style="padding: 0.5rem 1rem; background: #FF0000; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Affecter
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach
