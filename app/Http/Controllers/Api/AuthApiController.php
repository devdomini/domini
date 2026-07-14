<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Livraison;
use App\Models\User;
use App\Services\AccountVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthApiController extends Controller
{
    private function verification(): AccountVerificationService
    {
        return new AccountVerificationService();
    }

    /**
     * Inscription d'un nouvel employé
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|in:employe,livreur',
            'id_entreprise' => 'nullable|integer|exists:entreprises,id',
            'num_box' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'id_entreprise.integer' => 'L\'ID entreprise doit être un nombre entier.',
            'id_entreprise.exists' => 'L\'entreprise spécifiée n\'existe pas.',
            'num_box.max' => 'Le numéro de box ne peut pas dépasser 50 caractères.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Générer et envoyer le code (SMS ou e-mail selon le rôle)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'employe',
            'id_entreprise' => $request->id_entreprise,
            'num_box' => $request->num_box,
            'is_active' => true,
        ]);

        $sendResult = $this->verification()->issueAndSendCode($user, 'verification');

        // Créer un token avec expiration d'1 mois
        $expiresAt = Carbon::now()->addMonth();
        $tokenResult = $user->createToken('auth_token');
        $tokenResult->accessToken->expires_at = $expiresAt;
        $tokenResult->accessToken->save();
        
        $accessToken = $tokenResult->plainTextToken;

        // Préparer les données utilisateur sans le code (entreprise pour l’app mobile)
        $userData = $user->load('entreprise')->toArray();
        unset($userData['code_verification'], $userData['code_expires_at']);

        $channel = $sendResult['channel'];
        $channelLabel = $channel === 'email' ? 'e-mail' : 'SMS';

        return response()->json([
            'success' => true,
            'message' => $sendResult['success']
                ? "Inscription réussie. Un code de vérification a été envoyé par {$channelLabel}."
                : 'Inscription réussie, mais l\'envoi du code a échoué. Utilisez « Renvoyer le code ».',
            'verification_channel' => $sendResult['channel'],
            'verification_sent_to' => $sendResult['sent_to'],
            'available_channels' => $this->verification()->allowedChannels(),
            'code_sent' => $sendResult['success'],
            'send_message' => $sendResult['message'] ?? null,
            'data' => [
                'user' => $userData,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toISOString(),
                'verification_channel' => $sendResult['channel'],
                'verification_sent_to' => $sendResult['sent_to'],
            ]
        ], 201);
    }

    /**
     * Connexion d'un employé
     * Accepte soit un email soit un numéro de téléphone
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:telephone',
            'telephone' => 'required_without:email',
            'password' => 'required',
        ], [
            'email.required_without' => 'L\'email ou le téléphone est obligatoire.',
            'telephone.required_without' => 'L\'email ou le téléphone est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rechercher l'utilisateur par email ou téléphone
        $user = null;
        if ($request->has('email') && !empty($request->email)) {
            // Vérifier si c'est un email valide
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $request->email)->first();
            } else {
                // Si ce n'est pas un email valide, traiter comme un téléphone
                $user = User::where('telephone', $request->email)->first();
            }
        } elseif ($request->has('telephone') && !empty($request->telephone)) {
            $user = User::where('telephone', $request->telephone)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est désactivé'
            ], 403);
        }

        // Comptes back-office : pas d’accès à l’application mobile
        if (in_array($user->role, ['admin', 'entreprise'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Cet espace est réservé aux clients et aux livreurs. Connectez-vous au site web pour un compte administrateur ou entreprise.',
            ], 403);
        }

        // Créer un token avec expiration d'1 mois
        $expiresAt = Carbon::now()->addMonth();
        $tokenResult = $user->createToken('auth_token');
        $tokenResult->accessToken->expires_at = $expiresAt;
        $tokenResult->accessToken->save();
        
        $accessToken = $tokenResult->plainTextToken;

        // Préparer les données utilisateur (entreprise pour l’app mobile)
        $userData = $user->load('entreprise')->toArray();
        unset($userData['code_verification'], $userData['code_expires_at']);

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => $userData,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toISOString(),
                ...$this->verification()->verificationMeta($user),
            ]
        ], 200);
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('entreprise');
        $userData = $user->toArray();
        
        // Si c'est un livreur, ajouter les statistiques
        if ($user->role === 'livreur') {
            $coursesCount = \App\Models\Livraison::where('livreur_id', $user->id)
                ->where('statut', 'livree')
                ->count();
            
            $userData['stats'] = [
                'courses_count' => $coursesCount,
                'rating' => 5.0, // Valeur par défaut
                'vehicle' => 'Scooter', // Valeur par défaut
                'matricule' => 'AB-123-CD', // Valeur par défaut
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $userData
            ]
        ], 200);
    }

    /**
     * Mettre à jour les informations de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'sometimes|required|string|max:20|unique:users,telephone,' . $user->id,
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'telephone']));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'data' => [
                'user' => $user->fresh()->load('entreprise'),
            ]
        ], 200);
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès'
        ], 200);
    }

    /**
     * Vérifier le compte avec le code (SMS ou e-mail selon le rôle).
     */
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required_without:email|nullable|string',
            'email' => 'required_without:telephone|nullable|email',
            'code' => 'required|string|size:6',
            'channel' => 'nullable|in:sms,email',
        ], [
            'telephone.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'email.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'code.required' => 'Le code de vérification est obligatoire.',
            'code.size' => 'Le code doit contenir 6 chiffres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $this->verification();
        $user = $verification->findUser($request->email, $request->telephone);

        if (!$user) {
            $field = $request->filled('email') ? 'email' : 'telephone';
            $message = $field === 'email'
                ? 'Cette adresse e-mail n\'existe pas.'
                : 'Ce numéro de téléphone n\'existe pas.';

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => [$field => [$message]]
            ], 422);
        }

        $codeError = $verification->validateCode($user, $request->code);
        if ($codeError) {
            return response()->json([
                'success' => false,
                'message' => $codeError === 'Code incorrect' ? 'Code de vérification incorrect' : $codeError,
            ], 400);
        }

        $verification->markVerified($user, $request->channel);

        $channel = $verification->resolveChannel($user, $request->channel);
        $successMessage = $channel === 'email'
            ? 'Adresse e-mail vérifiée avec succès'
            : 'Numéro de téléphone vérifié avec succès';

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'data' => [
                'user' => $user->fresh(),
                'verification_channel' => $channel,
            ]
        ], 200);
    }

    /**
     * Renvoyer un code de vérification (SMS ou e-mail selon le rôle).
     */
    public function resendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required_without:email|nullable|string',
            'email' => 'required_without:telephone|nullable|email',
            'channel' => 'nullable|in:sms,email',
        ], [
            'telephone.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'email.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $this->verification();
        $user = $verification->findUser($request->email, $request->telephone);

        if (!$user) {
            $field = $request->filled('email') ? 'email' : 'telephone';
            $message = $field === 'email'
                ? 'Cette adresse e-mail n\'existe pas.'
                : 'Ce numéro de téléphone n\'existe pas.';

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => [$field => [$message]]
            ], 422);
        }

        $sendResult = $verification->issueAndSendCode($user, 'verification', $request->channel);

        if ($sendResult['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Code de vérification renvoyé avec succès',
                'verification_channel' => $sendResult['channel'],
                'verification_sent_to' => $sendResult['sent_to'],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du code. Veuillez réessayer plus tard.',
            'verification_channel' => $sendResult['channel'],
            'error' => $sendResult['message'] ?? 'Erreur inconnue',
        ], 500);
    }

    /**
     * Demander la réinitialisation du mot de passe (SMS ou e-mail selon le rôle).
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required_without:email|nullable|string',
            'email' => 'required_without:telephone|nullable|email',
            'channel' => 'nullable|in:sms,email',
        ], [
            'telephone.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'email.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $this->verification();
        $user = $verification->findUser($request->email, $request->telephone);

        if (!$user) {
            $field = $request->filled('email') ? 'email' : 'telephone';
            $message = $field === 'email'
                ? 'Cette adresse e-mail n\'existe pas dans notre système.'
                : 'Ce numéro de téléphone n\'existe pas dans notre système.';

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => [$field => [$message]]
            ], 422);
        }

        $sendResult = $verification->issueAndSendCode($user, 'reset', $request->channel);

        if ($sendResult['success']) {
            $channelLabel = $sendResult['channel'] === 'email' ? 'e-mail' : 'téléphone';

            return response()->json([
                'success' => true,
                'message' => "Un code de réinitialisation a été envoyé à votre {$channelLabel}",
                'verification_channel' => $sendResult['channel'],
                'verification_sent_to' => $sendResult['sent_to'],
                'code_sent' => true,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'envoi du code. Veuillez réessayer plus tard.',
            'verification_channel' => $sendResult['channel'],
            'code_sent' => false,
            'error' => $sendResult['message'] ?? 'Erreur inconnue',
        ], 500);
    }

    /**
     * Réinitialiser le mot de passe avec le code reçu.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'telephone' => 'required_without:email|nullable|string',
            'email' => 'required_without:telephone|nullable|email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'telephone.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'email.required_without' => 'Le téléphone ou l\'e-mail est obligatoire.',
            'code.required' => 'Le code de réinitialisation est obligatoire.',
            'code.size' => 'Le code doit contenir 6 chiffres.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $verification = $this->verification();
        $user = $verification->findUser($request->email, $request->telephone);

        if (!$user) {
            $field = $request->filled('email') ? 'email' : 'telephone';
            $message = $field === 'email'
                ? 'Cette adresse e-mail n\'existe pas.'
                : 'Ce numéro de téléphone n\'existe pas.';

            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => [$field => [$message]]
            ], 422);
        }

        $codeError = $verification->validateCode($user, $request->code);
        if ($codeError) {
            $message = match ($codeError) {
                'Code incorrect' => 'Code de réinitialisation incorrect',
                default => $codeError,
            };

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'code_verification' => null,
            'code_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès'
        ], 200);
    }

    /**
     * Rafraîchir le token d'authentification
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'entreprise'], true)) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Cet espace est réservé aux clients et aux livreurs. Connectez-vous au site web pour un compte administrateur ou entreprise.',
            ], 403);
        }

        // Supprimer l'ancien token
        $request->user()->currentAccessToken()->delete();

        // Créer un nouveau token avec expiration d'1 mois
        $expiresAt = Carbon::now()->addMonth();
        $tokenResult = $user->createToken('auth_token');
        $tokenResult->accessToken->expires_at = $expiresAt;
        $tokenResult->accessToken->save();
        
        $accessToken = $tokenResult->plainTextToken;

        // Préparer les données utilisateur
        $userData = $user->toArray();
        unset($userData['code_verification'], $userData['code_expires_at']);

        return response()->json([
            'success' => true,
            'message' => 'Token rafraîchi avec succès',
            'data' => [
                'user' => $userData,
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->toISOString(),
            ]
        ], 200);
    }

    /**
     * Suppression définitive du compte (exigence App Store 5.1.1v).
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ], [
            'password.required' => 'Le mot de passe est obligatoire pour confirmer la suppression.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (in_array($user->role, ['admin', 'commercial'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type de compte ne peut pas être supprimé depuis l’application mobile. Contactez l’administrateur.',
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mot de passe incorrect',
            ], 401);
        }

        if ($user->role === 'livreur') {
            $hasActiveDelivery = Livraison::query()
                ->where('livreur_id', $user->id)
                ->whereIn('statut', ['assignee', 'en_cours'])
                ->exists();

            if ($hasActiveDelivery) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le compte : vous avez une livraison en cours. Terminez-la ou contactez le support.',
                ], 422);
            }
        }

        DB::transaction(function () use ($user, $request) {
            $user->tokens()->delete();
            $user->entreprises()->detach();
            $user->notifications()->delete();
            $user->adresses()->delete();
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Votre compte a été supprimé définitivement.',
        ], 200);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ], 200);
    }
}
