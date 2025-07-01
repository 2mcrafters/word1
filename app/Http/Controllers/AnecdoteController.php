<?php
namespace App\Http\Controllers;

use App\Models\Anecdote;
use Illuminate\Http\Request;

class AnecdoteController extends Controller
{
    // Ajouter une anecdote
    public function store(Request $request, $id)
{
    $request->validate([
        'type' => 'required|in:Bof,Excellent,Technique,Wow!!',
    ]);

    $user = $request->user();

    // Empêche de voter deux fois pour le même type sur la même anecdote
    $exists = Vote::where('user_id', $user->id)
        ->where('anecdote_id', $id)
        ->where('type', $request->type)
        ->exists();

    if ($exists) {
        return response()->json([
            'message' => 'Vous avez déjà voté ce type pour cette anecdote.'
        ], 409);
    }

    // Création du vote
    $vote = Vote::create([
        'user_id' => $user->id,
        'anecdote_id' => $id,
        'type' => $request->type,
    ]);

    return response()->json([
        'message' => 'Vote enregistré avec succès.',
        'vote' => $vote
    ], 201);
}


    //  Afficher toutes les anecdotes avec compteur par type de vote
    public function index()
    {
        $anecdotes = Anecdote::with('user', 'votes')->get();

        $data = $anecdotes->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'author' => $a->user->name,
                'category' => $a->category,
                'content' => $a->content,
                'votes' => [
                    'Bof' => $a->votes->where('type', 'Bof')->count(),
                    'Excellent' => $a->votes->where('type', 'Excellent')->count(),
                    'Technique' => $a->votes->where('type', 'Technique')->count(),
                    'Wow!!' => $a->votes->where('type', 'Wow!!')->count(),
                ],
            ];
        });

        return response()->json($data);
    }
    //  supprimer une anecdote

    public function destroy($id)
{
    $anecdote = Anecdote::find($id);

    if (!$anecdote) {
        return response()->json(['message' => 'Anecdote non trouvée.'], 404);
    }

    $anecdote->delete();

    return response()->json(['message' => 'Anecdote supprimée.']);
}

}
