@extends('admin.layout')

@section('title', 'Modifier le mot de passe')
@section('page-title', 'Modifier le mot de passe')

@section('content')
    <div style="max-width: 600px;">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;">
            <a href="{{ route('admin.users.index') }}" style="color: #FF0000; text-decoration: none; font-weight: 600;">
                ← Retour à la liste
            </a>
        </div>

        <!-- User Info Card -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div style="padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #FF0000, #CC0000); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 900;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #000000; margin-bottom: 0.25rem;">
                        {{ $user->name ?? 'Utilisateur' }}
                    </h3>
                    <p style="color: #666; margin: 0;">{{ $user->email ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Nouveau mot de passe</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update-password', $user->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div style="display: grid; gap: 1.5rem;">
                        <!-- Nouveau mot de passe -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Nouveau mot de passe <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Min. 8 caractères"
                                required
                                autofocus
                            >
                            <small style="color: #666; font-size: 0.75rem;">Le mot de passe doit contenir au moins 8 caractères</small>
                            @error('password')
                                <span style="color: #CC0000; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirmation -->
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #000000;">
                                Confirmer le mot de passe <span style="color: #FF0000;">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                style="width: 100%; padding: 0.75rem; border: 2px solid #E5E5E5; border-radius: 8px; font-size: 1rem;"
                                placeholder="Retapez le mot de passe"
                                required
                            >
                        </div>

                        <!-- Warning -->
                        <div style="background-color: #FFEBEE; border-left: 4px solid #FF0000; padding: 1rem; border-radius: 4px;">
                            <p style="color: #000000; font-size: 0.875rem; margin: 0; display: flex; align-items: flex-start; gap: 0.5rem;">
                                @include('admin.partials.icon', ['name' => 'exclamation', 'size' => 18])
                                <span><strong>Attention :</strong> L'utilisateur devra utiliser ce nouveau mot de passe lors de sa prochaine connexion.</span>
                            </p>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #E5E5E5;">
                        <button type="submit" class="btn btn-primary">
                            Modifier le mot de passe
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Tips -->
        <div class="card" style="margin-top: 1.5rem;">
            <div style="padding: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #000000; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    @include('admin.partials.icon', ['name' => 'light-bulb', 'size' => 18])
                    Conseils pour un mot de passe sécurisé
                </h3>
                <ul style="margin: 0; padding-left: 1.5rem; color: #666; font-size: 0.875rem; line-height: 1.8;">
                    <li>Utilisez au moins 8 caractères</li>
                    <li>Mélangez majuscules et minuscules</li>
                    <li>Ajoutez des chiffres et symboles</li>
                    <li>Évitez les mots du dictionnaire</li>
                    <li>N'utilisez pas d'informations personnelles</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const eyeIcon = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
    const eyeOffIcon = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858 3.03a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`;

    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        const wrapper = input.parentElement;
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.innerHTML = eyeIcon;
        toggleBtn.style.cssText = 'position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #666; display: flex; align-items: center;';
        
        wrapper.style.position = 'relative';
        wrapper.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.innerHTML = eyeOffIcon;
            } else {
                input.type = 'password';
                toggleBtn.innerHTML = eyeIcon;
            }
        });
    });
</script>
@endsection
