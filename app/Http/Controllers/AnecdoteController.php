<?php
namespace App\Http\Controllers;

use App\Models\Anecdote;
use Illuminate\Http\Request;

class AnecdoteController extends Controller
{


public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'category' => 'required|in:histoire,humour,vie quotidienne,echec,succes',
        'content' => 'required|string|max:500',
    ]);

    $anecdote = Anecdote::create([
        'user_id' => $request->user()->id,
        'title' => $request->title,
        'category' => $request->category,
        'content' => $request->content,
    ]);

    return response()->json([
        'message' => 'Anecdote créée avec succès.',
        'anecdote' => $anecdote
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
