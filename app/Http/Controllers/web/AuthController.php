<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Session::has('user')) {
            return redirect('/web/competences');
        }
        return view('connexion');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_user'    => 'required|string',
            'password_user' => 'required|string',
        ]);

        $utilisateur = Utilisateur::where('login_user', $request->login_user)->first();

        if (!$utilisateur || !Hash::check($request->password_user, $utilisateur->password_user)) {
            return redirect('/web/connexion')
                   ->with('error', 'Identifiants incorrects.')
                   ->withInput();
        }

        if ($utilisateur->etat_user === 'bloque') {
            return redirect('/web/connexion')
                   ->with('error', 'Votre compte est bloqué. Contactez un administrateur.');
        }

        if ($utilisateur->etat_user === 'inactif') {
            return redirect('/web/connexion')
                   ->with('error', 'Votre compte est inactif. Contactez un administrateur.');
        }

        Session::put('user', [
            'code_user'   => $utilisateur->code_user,
            'nom_user'    => $utilisateur->nom_user,
            'prenom_user' => $utilisateur->prenom_user,
            'login_user'  => $utilisateur->login_user,
            'role_user'   => $utilisateur->role_user,
        ]);

        return redirect('/web/competences')
               ->with('success', 'Bienvenue ' . $utilisateur->prenom_user . ' !');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect('/web/connexion')
               ->with('success', 'Déconnexion réussie.');
    }
}
