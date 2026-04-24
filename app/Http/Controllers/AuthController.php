<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'code_user'                  => 'required|string|max:15|unique:utilisateur,code_user',
            'nom_user'                   => 'required|string|max:255',
            'prenom_user'                => 'required|string|max:255',
            'login_user'                 => 'required|string|unique:utilisateur,login_user',
            'password_user'              => 'required|string|min:6|confirmed',
            'tel_user'                   => 'nullable|string',
            'sexe_user'                  => 'nullable|in:M,F',
            'role_user'                  => 'required|in:client,technicien,admin',
        ]);

        try {
            $validated['password_user'] = bcrypt($validated['password_user']);
            $validated['etat_user']     = 'inactif';
            $utilisateur = Utilisateur::create($validated);
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'     => 'Compte créé avec succès.',
                'utilisateur' => $utilisateur,
                'token'       => $token,
                'token_type'  => 'Bearer',
            ], 201);
        } catch (Throwable $th) {
            Log::error('AUTH REGISTER ERROR: ' . $th->getMessage() . ' | ' . $th->getFile() . ':' . $th->getLine());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_user'    => 'required|string',
            'password_user' => 'required|string',
        ]);

        try {
            $utilisateur = Utilisateur::where('login_user', $request->login_user)->first();

            if (! $utilisateur || ! Hash::check($request->password_user, $utilisateur->password_user)) {
                return response()->json(['message' => 'Identifiants incorrects.'], 401);
            }

            if ($utilisateur->etat_user !== 'actif') {
                return response()->json([
                    'message' => 'Votre compte est ' . $utilisateur->etat_user . '. Contactez un administrateur.',
                ], 403);
            }

            $utilisateur->tokens()->delete();
            $token = $utilisateur->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message'     => 'Connexion réussie.',
                'utilisateur' => $utilisateur,
                'token'       => $token,
                'token_type'  => 'Bearer',
            ], 200);
        } catch (Throwable $th) {
            Log::error('AUTH LOGIN ERROR: ' . $th->getMessage() . ' | ' . $th->getFile() . ':' . $th->getLine());
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function logout(Request $request)
{
    try {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        } else {
            // actingAs() dans les tests ne crée pas de token réel
            $request->user()->tokens()->delete();
        }
        return response()->json(['message' => 'Déconnexion réussie.'], 200);
    } catch (Throwable $th) {
        \Log::error('AUTH LOGOUT ERROR: ' . $th->getMessage());
        return response()->json(['message' => $th->getMessage()], 500);
    }
}
    public function profil(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function updateProfil(Request $request)
    {
        $utilisateur = $request->user();

        $validated = $request->validate([
            'nom_user'      => 'sometimes|string|max:255',
            'prenom_user'   => 'sometimes|string|max:255',
            'tel_user'      => 'nullable|string',
            'sexe_user'     => 'nullable|in:M,F',
            'password_user' => 'sometimes|string|min:6|confirmed',
        ]);

        try {
            if (isset($validated['password_user'])) {
                $validated['password_user'] = bcrypt($validated['password_user']);
            }
            $utilisateur->update($validated);
            return response()->json([
                'message'     => 'Profil mis à jour avec succès.',
                'utilisateur' => $utilisateur->fresh(),
            ], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
