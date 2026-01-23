@extends('admin.layout')

@section('title', 'Changer le mot de passe')
@section('page-title', 'Changer le mot de passe')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="padding: 2rem;">
            <div style="margin-bottom: 2rem;">
                <a href="{{ route('admin.livreurs.index') }}" style="color: #D9542A; text-decoration: none; font-weight: 600;">
                    ← Retour à la liste
                </a>
            </div>

            <div style="text-align: center; margin-bottom: 2rem; padding: 1.5rem; background: #F5F5F5; border-radius: 12px;">
                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔑</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #3A3A3A; margin-bottom: 0.5rem;">
                    {{ $livreur->name }}
                </h3>
                <p style="color: #666; font-size: 0.875rem;">{{ $livreur->email }}</p>
            </div>

            <form action="{{ route('admin.livreurs.update-password', $livreur->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div style="display: grid; gap: 1.5rem;">
                    <!-- Nouveau mot de passe -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Nouveau mot de passe <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            minlength="6"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Minimum 6 caractères"
                        >
                        @error('password')
                            <small style="color: #EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Confirmer le mot de passe -->
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #3A3A3A;">
                            Confirmer le mot de passe <span style="color: #D9542A;">*</span>
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required
                            minlength="6"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                            placeholder="Retapez le mot de passe"
                        >
                    </div>

                    <!-- Info -->
                    <div style="background: #DBEAFE; padding: 1rem; border-radius: 8px; border-left: 4px solid #3B82F6;">
                        <div style="font-weight: 600; color: #1E40AF; margin-bottom: 0.5rem;">ℹ️ Information</div>
                        <ul style="margin: 0; padding-left: 1.5rem; color: #1E3A8A; font-size: 0.875rem;">
                            <li>Le mot de passe doit contenir au moins 6 caractères</li>
                            <li>Le livreur devra utiliser ce nouveau mot de passe pour se connecter</li>
                            <li>Assurez-vous de communiquer le nouveau mot de passe au livreur de manière sécurisée</li>
                        </ul>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #E5E5E5;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        Changer le mot de passe
                    </button>
                    <a href="{{ route('admin.livreurs.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
