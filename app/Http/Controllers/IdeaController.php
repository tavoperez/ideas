<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IdeaController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:100',
        'description' => 'required|string|max:300',
    ];

    private array $errorMessages = [
        'title.required' => 'El campo título es obligarorio.',
        'description.required' => 'El campo descripción es obligarorio.',
        'string' => 'Este campo debe ser de tipo String.',
        'title.max' => 'El campo título no debe ser mayor a :max caracteres.',
        'description.max' => 'El campo descripción no debe ser mayor a :max caracteres.',
    ];

    public function index (Request $request): View
    {
        $ideas = Idea::myIdea($request->filtro)->theBest($request->filtro)->get();
        return view('ideas.index', ['ideas' => $ideas]);
    }

    public function create(): View
    {
        return view('ideas.create_edit');
    }

    public function store(Request $request): RedirectResponse
    {
        /* dd($request)->all(); */
        $validate = $request->validate($this->rules, $this->errorMessages);
        Idea::create([
            'user_id' => $request->user()->id,
            'title' => $validate['title'],
            'description' => $validate['description'],
        ]);
        session()->flash('message', 'Idea creada correctamente!');
        return redirect()->route('idea.index');/* ->with('success', 'Idea created successfully'); */
    }

    public function edit(Idea $idea_id): View
    {
        /* $idea = Idea::findOrFail($id); */
        $this->authorize('update', $idea_id);
        return view('ideas.create_edit')->with('idea', $idea_id);
    }
    
    public function update(Request $request,Idea $idea_id): RedirectResponse
    {
        $this->authorize('update', $idea_id);
        $validate = $request->validate($this->rules, $this->errorMessages);
        $idea_id->update($validate);
        session()->flash('message', 'Idea actualizada correctamente!');
        return redirect()->route('idea.index');
    }

    public function show(Idea $idea_id): View

    {
        return view('ideas.show')->with('idea', $idea_id);
    }
    
    public function delete(Idea $idea_id): RedirectResponse
    {
        $this->authorize($idea_id);
        $idea_id->delete();
        session()->flash('message', 'Idea eliminada correctamente!');
        return redirect()->route('idea.index');
    }

    public function synchronizeLikes(Request $request,Idea $idea_id): RedirectResponse
    {
        $this->authorize('updateLike', $idea_id);
        $request->user()->ideasLike()->toggle([$idea_id->id]);

        $idea_id->update(['likes' =>  $idea_id->users()->count()]);

        return redirect()->route('idea.show', $idea_id);
    }
}