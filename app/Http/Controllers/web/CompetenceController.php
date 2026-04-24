<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Competence;

class CompetenceController extends Controller
{
    public function index()
    {
        $competences_list = Competence::paginate(10);
        return view('competences', compact('competences_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label_comp'       => 'required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

        Competence::create([
            'label_comp'       => $request->label_comp,
            'description_comp' => $request->description_comp,
        ]);

        return redirect('/web/competences')
               ->with('success', 'Compétence ajoutée avec succès.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'label_comp'       => 'sometimes|required|string|max:255',
            'description_comp' => 'nullable|string',
        ]);

        $competence = Competence::findOrFail($id);
        $competence->update($request->only(['label_comp', 'description_comp']));

        return redirect('/web/competences')
               ->with('success', 'Compétence modifiée avec succès.');
    }

    public function destroy(string $id)
    {
        $competence = Competence::findOrFail($id);
        $competence->delete();

        return redirect('/web/competences')
               ->with('success', 'Compétence supprimée avec succès.');
    }
}
