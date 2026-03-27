<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use Illuminate\Http\Request;

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       try {
            $competences = Competence::all();
            return response()->json($competences, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve competences', 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label_comp' => 'required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

       try {
            $competence = Competence::create([
            'label_comp' => $request->label_comp,
            'description_comp' => $request->description_comp,
            ]);
            return response()->json($competence, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create competence', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $code_comp)
    {
       try {
            $competence = Competence::findOrFail($code_comp);
            return response()->json($competence, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve competence', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $code_comp)
    {
        $request->validate([
            'label_comp' => 'sometimes|required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

        try {
            $competence = Competence::findOrFail($code_comp);
            $competence->update([
                'label_comp' => $request->label_comp,
                'description_comp' => $request->description_comp
            ]);
            return response()->json($competence, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update competence', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $code_comp)
    {
        try {
            $competence = Competence::findOrFail($code_comp);
            $competence->delete();
            return response()->json(['message' => 'competence deleted successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete competence', 'message' => $e->getMessage()], 500);
        }
    }
}
