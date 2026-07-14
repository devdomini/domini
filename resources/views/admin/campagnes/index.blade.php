@extends('admin.layout')

@section('title', 'Campagnes push')
@section('page-title', 'Campagnes — notifications push')

@section('styles')
<style>
    .camp-wrap { max-width: 720px; margin: 0 auto; }
    .camp-label { display: block; font-weight: 600; margin-bottom: 0.45rem; color: #000000; font-size: 0.9rem; }
    .camp-label .req { color: #FF0000; }
    .camp-input, .camp-select, .camp-textarea {
        width: 100%; padding: 0.75rem 0.9rem; border: 2px solid #E5E5E5; border-radius: 10px;
        font-size: 1rem; font-family: inherit; background: #fff; box-sizing: border-box;
    }
    .camp-textarea { min-height: 140px; resize: vertical; }
    .camp-input:focus, .camp-select:focus, .camp-textarea:focus {
        outline: none; border-color: #FF0000; box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.15);
    }
    .camp-hint { font-size: 0.8rem; color: #666; margin-top: 0.4rem; line-height: 1.45; }
    .camp-warn {
        background: #FFF8E1; color: #856404; padding: 1rem 1.15rem; border-radius: 10px;
        margin-bottom: 1.25rem; font-size: 0.9rem; border: 1px solid #FFE082;
    }
    .camp-section { margin-bottom: 1.25rem; }
</style>
@endsection

@section('content')
<div class="camp-wrap">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Envoyer une notification push</h2>
            <p style="margin: 0.5rem 0 0; color: #666; font-size: 0.9rem;">
                Message promotionnel, fidélisation ou information. Seuls les utilisateurs ayant ouvert l’app mobile
                et un jeton FCM enregistré recevront la notification (clients <strong>employe</strong>, <strong>livreur</strong>).
            </p>
        </div>
        <div class="card-body" style="padding-top: 1rem;">

            @if(!$fcmConfigured)
                <div class="camp-warn">
                    <strong>Firebase non configuré.</strong> Définissez <code>FIREBASE_CREDENTIALS</code> dans <code>.env</code>
                    (chemin vers le JSON compte de service) puis <code>php artisan config:clear</code>.
                </div>
            @endif

            @isset($stats)
                <div class="card" style="margin-bottom: 1.5rem; background: #fafafa; border: 1px solid #E8E8E8;">
                    <div class="card-header" style="border-bottom: 1px solid #E5E5E5; padding-bottom: 0.75rem;">
                        <h3 class="card-title" style="font-size: 1.05rem;">Jetons FCM sur cette base de données</h3>
                    </div>
                    <div style="padding: 1rem 1.25rem; font-size: 0.9rem; color: #444; line-height: 1.6;">
                        <p style="margin: 0 0 0.75rem;"><strong>Clients (employe)</strong> : {{ $stats['employe_fcm'] }} avec jeton / {{ $stats['employe_total'] }} au total</p>
                        <p style="margin: 0 0 0.75rem;"><strong>Livreurs</strong> : {{ $stats['livreur_fcm'] }} avec jeton / {{ $stats['livreur_total'] }} au total</p>
                        <p style="margin: 0 0 0.75rem;"><strong>Total campagnes (employe + livreur avec jeton)</strong> : <strong>{{ $stats['all_fcm'] }}</strong></p>
                        @if($stats['all_fcm'] === 0)
                            <p class="camp-warn" style="margin-top: 1rem; margin-bottom: 0;">
                                Tant que ce total est 0, aucune campagne ne part. L’app mobile doit appeler
                                <code>POST /api/device/fcm-token</code> sur <strong>ce même serveur</strong> (même <code>APP_URL</code> / base que l’admin).
                                Vérifiez <code>api_config.dart</code> (URL) et reconnectez-vous sur l’app.
                            </p>
                        @endif
                    </div>
                </div>
            @endisset

            <form method="POST" action="{{ route('admin.campagnes.send') }}"
                  onsubmit="return confirm('Confirmer l\u2019envoi de cette campagne aux destinataires sélectionnés ?');">
                @csrf

                <div class="camp-section">
                    <label class="camp-label" for="kind">Type de campagne <span class="req">*</span></label>
                    <select class="camp-select" id="kind" name="kind" required>
                        <option value="promo" {{ old('kind') === 'promo' ? 'selected' : '' }}>Promo / offre</option>
                        <option value="fidelisation" {{ old('kind') === 'fidelisation' ? 'selected' : '' }}>Fidélisation</option>
                        <option value="message" {{ old('kind', 'message') === 'message' ? 'selected' : '' }}>Message / annonce</option>
                    </select>
                </div>

                <div class="camp-section">
                    <label class="camp-label" for="audience">Destinataires <span class="req">*</span></label>
                    <select class="camp-select" id="audience" name="audience" required>
                        <option value="all" {{ old('audience', 'all') === 'all' ? 'selected' : '' }}>Tous (clients + livreurs)</option>
                        <option value="employe" {{ old('audience') === 'employe' ? 'selected' : '' }}>Clients uniquement (employés / app commande)</option>
                        <option value="livreur" {{ old('audience') === 'livreur' ? 'selected' : '' }}>Livreurs uniquement</option>
                    </select>
                    <span class="camp-hint">Comptes avec jeton FCM enregistré depuis l’app mobile (connexion à ce serveur).</span>
                </div>

                <div class="camp-section">
                    <label class="camp-label" for="title">Titre (bannière) <span class="req">*</span></label>
                    <input class="camp-input" type="text" id="title" name="title" maxlength="120"
                           value="{{ old('title') }}" required placeholder="Ex. -20 % ce week-end">
                    @error('title')<span style="color:#CC0000;font-size:0.85rem;">{{ $message }}</span>@enderror
                </div>

                <div class="camp-section">
                    <label class="camp-label" for="body">Message <span class="req">*</span></label>
                    <textarea class="camp-textarea" id="body" name="body" maxlength="2000" required
                              placeholder="Texte affiché sous le titre.">{{ old('body') }}</textarea>
                    @error('body')<span style="color:#CC0000;font-size:0.85rem;">{{ $message }}</span>@enderror
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary" {{ $fcmConfigured ? '' : 'disabled' }}>
                        Envoyer la campagne
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
