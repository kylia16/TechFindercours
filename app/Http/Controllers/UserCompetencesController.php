<?php

namespace App\Http\Controllers;

use App\Models\User_Competence;
use Illuminate\Http\Request;

class UserCompetencesController extends Controller
{
    public function index()
    {
        $userCompetences = User_Competence::all();
        return response()->json($userCompetences, 200);
    }

    public function store(Request $request)
    {
        $validate = request()->validate([
            'code_user' => 'required|string|exists:utilisateur,code_user',
            'code_comp' => 'required|integer|exists:competences,code_comp',
        ]);

        $exists = User_Competence::where('code_user', $validate['code_user'])
            ->where('code_comp', $validate['code_comp'])
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Competence already assigned to user'], 409);
        }

        try {
            $userCompetence = User_Competence::create($validate);
            return response()->json($userCompetence, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function showByUser($code_user)
    {
        $userCompetences = User_Competence::where('code_user', $code_user)->get();

        if ($userCompetences->isEmpty()) {
            return response()->json(['message' => 'No competences found for this user'], 404);
        }

        return response()->json($userCompetences, 200);
    }

    public function destroy(Request $request)
{
    try {
        // Récupérer directement depuis le request sans validate
        $code_user = $request->input('code_user');
        $code_comp = $request->input('code_comp');

        if (!$code_user || !$code_comp) {
            return response()->json(['message' => 'code_user et code_comp sont requis'], 422);
        }

        $userCompetence = User_Competence::where('code_user', $code_user)
            ->where('code_comp', $code_comp)
            ->first();

        if (!$userCompetence) {
            return response()->json(['message' => 'User competence not found'], 404);
        }

        $userCompetence->delete();
        return response()->json(null, 204);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
}
