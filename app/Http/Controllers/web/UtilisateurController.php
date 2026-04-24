<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $query = Utilisateur::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code_user',    'LIKE', "%$search%")
                  ->orWhere('nom_user',   'LIKE', "%$search%")
                  ->orWhere('prenom_user','LIKE', "%$search%")
                  ->orWhere('login_user', 'LIKE', "%$search%")
                  ->orWhere('role_user',  'LIKE', "%$search%")
                  ->orWhere('etat_user',  'LIKE', "%$search%");
            });
        }

        $utilisateurs_list = $query->paginate(10)->withQueryString();
        return view('utilisateurs', compact('utilisateurs_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_user'     => 'required|string|max:255',
            'prenom_user'  => 'required|string|max:255',
            'login_user'   => 'required|string|unique:utilisateur,login_user',
            'password_user'=> 'required|string|min:6',
            'tel_user'     => 'nullable|string',
            'sexe_user'    => 'nullable|in:M,F',
            'role_user'    => 'required|in:client,technicien,admin',
            'etat_user'    => 'required|in:actif,inactif,bloque',
        ]);

        try {
            do {
                $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4));
                $numbers = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $code    = 'USR-' . $letters . $numbers;
            } while (Utilisateur::where('code_user', $code)->exists());

            Utilisateur::create([
                'code_user'    => $code,
                'nom_user'     => $request->nom_user,
                'prenom_user'  => $request->prenom_user,
                'login_user'   => $request->login_user,
                'password_user'=> bcrypt($request->password_user),
                'tel_user'     => $request->tel_user,
                'sexe_user'    => $request->sexe_user,
                'role_user'    => $request->role_user,
                'etat_user'    => $request->etat_user,
            ]);

            return redirect('/web/users')
                   ->with('success', 'Utilisateur ajouté avec succès.');

        } catch (\Exception $e) {
            return redirect('/web/users')
                   ->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nom_user'    => 'required|string|max:255',
            'prenom_user' => 'required|string|max:255',
            'tel_user'    => 'nullable|string',
            'sexe_user'   => 'nullable|in:M,F',
            'role_user'   => 'required|in:client,technicien,admin',
            'etat_user'   => 'required|in:actif,inactif,bloque',
        ]);

        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update($request->only([
            'nom_user', 'prenom_user', 'tel_user',
            'sexe_user', 'role_user', 'etat_user'
        ]));

        return redirect('/web/users')
               ->with('success', 'Utilisateur modifié avec succès.');
    }

    public function destroy(string $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->delete();

        return redirect('/web/users')
               ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
