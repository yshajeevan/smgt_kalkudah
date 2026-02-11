<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Competency;
use App\Models\Cadresubject;

class CompetencyController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:results-manage', ['only' => ['store','edit','update','destroy']]);
    }
    // Show page with form + table
    public function index()
    {
        $subjects = Cadresubject::whereIn('id', [7,17,23,20,11,50,36])
        ->orderBy('cadre')
        ->get();
        $competencies = Competency::with('subject')->orderBy('id', 'desc')->get();

        return view('exam.competencies.index', compact('subjects', 'competencies'));
    }

    public function getBySubject($subjectId)
    {
        $competencies = Competency::where('subject_id', $subjectId)
            ->with('subject')
            ->orderByDesc('id')
            ->get();

        return response()->json($competencies);
    }

    // Store new competency
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'subject_id' => 'required|exists:cadresubjects,id',
        ]);

        $competency = Competency::create($request->all());

        return response()->json([
            'success' => true,
            'competency' => $competency->load('subject')
        ]);
    }

    // Get data for editing
    public function edit($id)
    {
        $competency = Competency::findOrFail($id);
        return response()->json($competency);
    }

    // Update competency
    public function update(Request $request, $id)
    {
        $competency = Competency::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'subject_id' => 'required|exists:cadresubjects,id',
        ]);

        $competency->update($request->all());

        return response()->json([
            'success' => true,
            'competency' => $competency->load('subject')
        ]);
    }

    // Delete competency
    public function destroy($id)
    {
        $competency = Competency::findOrFail($id);
        $competency->delete();

        return response()->json(['success' => true]);
    }
}
