<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    public function index()
    {
        try {
            $competences = Competence::all();
            return response()->json($competences, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'label_comp'       => 'required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

        try {
            $competence = Competence::create([
                'label_comp'       => $request->label_comp,
                'description_comp' => $request->description_comp,
            ]);
            return response()->json($competence, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $code_comp)
    {
        try {
            $competence = Competence::findOrFail($code_comp);
            return response()->json($competence, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $code_comp)
    {
        $request->validate([
            'label_comp'       => 'sometimes|required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

        try {
            $competence = Competence::findOrFail($code_comp);

            // only() : ne met à jour que les champs envoyés dans la requête
            // Evite d'écraser avec null les champs non envoyés
            $competence->update($request->only(['label_comp', 'description_comp']));

            return response()->json($competence, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $code_comp)
    {
        try {
            $competence = Competence::findOrFail($code_comp);
            $competence->delete();
            return response()->json(['message' => 'competence deleted successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
