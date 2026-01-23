<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger vers le dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);

        $remember = $request->filled('remember');

        // Tenter la connexion
        if (Auth::attempt($credentials, $remember)) {
            // Vérifier que l'utilisateur est un admin
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();
                
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Bienvenue ' . Auth::user()->name . ' !');
            } else {
                Auth::logout();
                return back()->with('error', 'Vous n\'avez pas les permissions d\'accès à cette zone.');
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Les identifiants sont incorrects.');
    }

    /**
     * Afficher le dashboard
     */
    public function dashboard()
    {
        // Vérifier que l'utilisateur est admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login');
        }

        return view('admin.dashboard');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
