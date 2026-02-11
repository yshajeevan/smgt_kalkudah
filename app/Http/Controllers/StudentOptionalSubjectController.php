<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Cadresubject;
use Illuminate\Support\Facades\Auth;

class StudentOptionalSubjectController extends Controller
{
    public function index(Request $request)
    {
        // Fetch subjects per category2 bucket
        $basket1Subjects = Cadresubject::where('category2', 'Basket 1')->orderBy('cadre')->get(['id','cadre']);
        $basket2Subjects = Cadresubject::where('category2', 'Basket 2')->orderBy('cadre')->get(['id','cadre']);
        $basket3Subjects = Cadresubject::where('category2', 'Basket 3')->orderBy('cadre')->get(['id','cadre']);
        $religionSubjects = Cadresubject::where('category2', 'Religion')->orderBy('cadre')->get(['id','cadre']);

        // students filtered by institute
        $query = Student::where('institute_id', Auth::user()->institute_id);

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qb) use ($q){
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('id', 'like', "%{$q}%");
            });
        }

        $students = $query->orderBy('id')->paginate(70)->appends($request->query());

        return view('students.optional_subjects.index', compact(
            'students',
            'basket1Subjects',
            'basket2Subjects',
            'basket3Subjects',
            'religionSubjects'
        ));
    }

    public function update(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)
                          ->where('institute_id', Auth::user()->institute_id)
                          ->firstOrFail();

        $data = $request->validate([
            'cadresubject1_id' => 'nullable|exists:cadresubjects,id',
            'cadresubject2_id' => 'nullable|exists:cadresubjects,id',
            'cadresubject3_id' => 'nullable|exists:cadresubjects,id',
            'cadresubject4_id' => 'nullable|exists:cadresubjects,id',
        ]);

        $selected = array_filter([
            $data['cadresubject1_id'] ?? null,
            $data['cadresubject2_id'] ?? null,
            $data['cadresubject3_id'] ?? null,
            $data['cadresubject4_id'] ?? null,
        ]);

        if (count($selected) !== count(array_unique($selected))) {
            return back()->with('error', 'Please choose different subjects for each optional slot.');
        }

        // Optional: ensure selected subject belongs to correct category2 for each slot
        if (!empty($data['cadresubject1_id'])) {
            $s = Cadresubject::find($data['cadresubject1_id']);
            if (!$s || $s->category2 !== 'Basket 1') {
                return back()->with('error', 'Selected subject for Optional 1 must be from Basket 1.');
            }
        }
        if (!empty($data['cadresubject2_id'])) {
            $s = Cadresubject::find($data['cadresubject2_id']);
            if (!$s || $s->category2 !== 'Basket 2') {
                return back()->with('error', 'Selected subject for Optional 2 must be from Basket 2.');
            }
        }
        if (!empty($data['cadresubject3_id'])) {
            $s = Cadresubject::find($data['cadresubject3_id']);
            if (!$s || $s->category2 !== 'Basket 3') {
                return back()->with('error', 'Selected subject for Optional 3 must be from Basket 3.');
            }
        }
        if (!empty($data['cadresubject4_id'])) {
            $s = Cadresubject::find($data['cadresubject4_id']);
            if (!$s || $s->category2 !== 'Religion') {
                return back()->with('error', 'Selected subject for Optional 4 must be from Religion.');
            }
        }

        $student->update([
            'cadresubject1_id' => $data['cadresubject1_id'] ?? null,
            'cadresubject2_id' => $data['cadresubject2_id'] ?? null,
            'cadresubject3_id' => $data['cadresubject3_id'] ?? null,
            'cadresubject4_id' => $data['cadresubject4_id'] ?? null,
        ]);

        return back()->with('success', 'Optional subjects updated.');
    }
}
