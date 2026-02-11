<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SyllabusUnit;
use App\Models\Competency;
use App\Models\Cadresubject;

class SyllabusUnitController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:results-manage', ['only' => ['store','edit','update','destroy']]);
    }
    // Show page
    public function index()
    {
        $subjects = Cadresubject::whereIn('id', [7,17,23,20,11,50,36])
        ->orderBy('cadre')
        ->get();

        return view('exam.syllabus_units.index', compact('subjects'));
    }

    public function getCompetencies($subjectId)
    {
        return Competency::where('subject_id', $subjectId)->orderBy('name')->get();
    }

    public function getUnits($competencyId)
    {
        return SyllabusUnit::with('competency')->where('competency_id', $competencyId)->get();
    }

    // Get units by competency
    public function getByCompetency($competencyId)
    {
        $units = SyllabusUnit::where('competency_id', $competencyId)
            ->with('competency')
            ->orderByDesc('id')
            ->get();

        return response()->json($units);
    }

    // Store
    public function store(Request $request)
{
    $request->validate([
        'competency_id' => 'required|exists:competencies,id',
        'name'          => 'required|string|max:255',
        'code'          => 'nullable|string|max:50',
        'worksheet'     => 'nullable|file|mimes:pdf|max:5120', // 5MB
    ]);

    $data = $request->all();

    if ($request->hasFile('worksheet')) {
        $file = $request->file('worksheet');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'uploads/units/';
        $file->move(public_path($path), $filename);
        $data['worksheet'] = $path . $filename;
    }

    $unit = SyllabusUnit::create($data);

    return response()->json([
        'success' => true,
        'unit' => $unit->load('competency')
    ]);
}

    // Edit
    public function edit($id)
    {
        $unit = SyllabusUnit::findOrFail($id);
        return response()->json($unit);
    }

    // Update
    public function update(Request $request, $id)
{
    $unit = SyllabusUnit::findOrFail($id);

    $request->validate([
        'competency_id' => 'required|exists:competencies,id',
        'name'          => 'required|string|max:255',
        'code'          => 'nullable|string|max:50',
        'worksheet'     => 'nullable|file|mimes:pdf|max:5120',
    ]);

    $data = $request->all();

    if ($request->hasFile('worksheet')) {
        // Delete old file
        if ($unit->worksheet && file_exists(public_path($unit->worksheet))) {
            unlink(public_path($unit->worksheet));
        }

        $file = $request->file('worksheet');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = 'uploads/units/';
        $file->move(public_path($path), $filename);
        $data['worksheet'] = $path . $filename;
    }

    $unit->update($data);

    return response()->json([
        'success' => true,
        'unit' => $unit->load('competency')
    ]);
}


    // Delete
    public function destroy($id)
    {
        $unit = SyllabusUnit::findOrFail($id);
        $unit->delete();

        return response()->json(['success' => true]);
    }
}
