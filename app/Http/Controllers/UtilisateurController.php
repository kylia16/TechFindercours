<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Throwable;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code_user'     => 'required|string|max:15|unique:utilisateur,code_user',
            'nom_user'      => 'required|string|max:255',
            'prenom_user'   => 'required|string|max:255',
            'login_user'    => 'required|string|unique:utilisateur,login_user',
            'password_user' => 'required|string|min:6',
            'tel_user'      => 'nullable|string',
            'sexe_user'     => 'nullable|in:M,F',
            'role_user'     => 'required|in:client,admin,technicien',
            'etat_user'     => 'required|boolean',
        ]);

        try {
            $utilisateur = Utilisateur::create($validatedData);
            return response()->json(['message' => 'Utilisateur créé avec succès'], 201);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function show(string $codeUser)
    {
        try {
            $utilisateur = Utilisateur::findOrFail($codeUser);
            return response()->json($utilisateur, 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

    public function update(Request $request, string $codeUser)
    {
        $utilisateur = Utilisateur::findOrFail($codeUser);

        $validatedData = $request->validate([
            'code_user'     => 'sometimes|string|max:15|unique:utilisateur,code_user,' . $codeUser . ',code_user',
            'nom_user'      => 'sometimes|string|max:255',
            'prenom_user'   => 'sometimes|string|max:255',
            'login_user'    => 'sometimes|string|unique:utilisateur,login_user,' . $codeUser . ',code_user',
            'password_user' => 'sometimes|string|min:6',
            'tel_user'      => 'nullable|string',
            'sexe_user'     => 'nullable|in:M,F',
            'role_user'     => 'sometimes|in:client,admin,technicien',
            'etat_user'     => 'sometimes|boolean',
        ]);

        try {
            $utilisateur->update($validatedData);
            return response()->json(['message' => 'Utilisateur mis à jour avec succès'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy(string $codeUser)
    {
        try {
            $utilisateur = Utilisateur::findOrFail($codeUser);
            $utilisateur->delete();
            return response()->json(['message' => 'Utilisateur supprimé'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
