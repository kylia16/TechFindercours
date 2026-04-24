<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterventionController extends Controller
{
    public function index()
    {
        try {
            $interventions = Intervention::all();
            return response()->json($interventions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'note_int'         => 'required|integer|min:0|max:20',
            'commentaire_int'  => 'nullable|string',
            'code_user_client' => 'required|string',
            'code_user_techn'  => 'required|string',
            'code_comp'        => 'required|integer',
        ]);

        try {
            $intervention = Intervention::create([
                'date_int'         => Carbon::now(),
                'note_int'         => $request->note_int,
                'commentaire_int'  => $request->commentaire_int,
                'code_user_client' => $request->code_user_client,
                'code_user_techn'  => $request->code_user_techn,
                'code_comp'        => $request->code_comp,
            ]);
            return response()->json($intervention, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $code_int)
    {
        try {
            $intervention = Intervention::findOrFail($code_int);
            return response()->json($intervention, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $code_int)
    {
        $request->validate([
            'note_int'         => 'sometimes|integer|min:0|max:20',
            'commentaire_int'  => 'nullable|string',
            'code_user_client' => 'sometimes|string',
            'code_user_techn'  => 'sometimes|string',
            'code_comp'        => 'sometimes|integer',
        ]);

        try {
            $intervention = Intervention::findOrFail($code_int);

            // only() : ne met à jour que les champs envoyés
            $data = $request->only([
                'note_int', 'commentaire_int',
                'code_user_client', 'code_user_techn', 'code_comp'
            ]);

            $intervention->update($data);
            return response()->json($intervention->fresh(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $code_int)
    {
        try {
            $intervention = Intervention::findOrFail($code_int);
            $intervention->delete();
            return response()->json(['message' => 'Intervention supprimée avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
