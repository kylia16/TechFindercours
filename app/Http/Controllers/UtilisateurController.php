<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Throwable;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try{
            // Validation des données entrantes
            $validatedData = $request->validate([
                'code_user'     => 'required|string|unique:utilisateurs,code_user',
                'nom_user'      => 'required|string|max:255',
                'prenom_user'   => 'required|string|max:255',
                'login_user'    => 'required|string|unique:utilisateurs,login_user',
                'password_user' => 'required|string|min:6',
                'tel_user'      => 'nullable|string',
                'sexe_user'     => 'nullable|string|in:M,F',
                'role_user'     => 'required|string',
                'etat_user'     => 'required|boolean',
            ]);

            $utilisateur = Utilisateur::create($validatedData);

            return response()->json(["message" => "Utilisateur créé avec succès"], 201);

        }catch(Throwable $th){
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $codeUser)
    {
        //
        try {
            $utilisateur = Utilisateur::findOrFail($codeUser);
            return response()->json($utilisateur, 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $codeUser)
    {
        //
        try{
            $utilisateur = Utilisateur::findOrFail($codeUser);
            // Validation des données entrantes
            $validatedData = $request->validate([
                'code_user'     => 'required|string|unique:utilisateurs,code_user',
                'nom_user'      => 'required|string|max:255',
                'prenom_user'   => 'required|string|max:255',
                'login_user'    => 'required|string|unique:utilisateurs,login_user',
                'password_user' => 'required|string|min:6',
                'tel_user'      => 'nullable|string',
                'sexe_user'     => 'nullable|string|in:M,F',
                'role_user'     => 'required|string',
                'etat_user'     => 'required|boolean',
            ]);

            $utilisateur = Utilisateur::update($validatedData);

            return response()->json(["message" => "Utilisateur mis a jour avec succès"], 200);

        }catch(Throwable $th){
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $codeUser)
    {
        //
        try {
            $utilisateur = Utilisateur::findOrFail($codeUser);
            $utilisateur->delete();

            return response()->json(['message' => 'Utilisateur supprimé'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
