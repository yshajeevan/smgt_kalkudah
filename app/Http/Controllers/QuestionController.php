<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Cadresubject;
use App\Models\SyllabusUnit;
use Illuminate\Http\Request;
use Auth;

class QuestionController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:results-manage', ['only' => ['store','destroy']]);
    }
    public function manage()
    {
        $exams = Exam::all();

        return view('exam.questions.create_edit', compact('exams'));
    }

    // Return subjects for a given exam
    public function getSubjects(Request $request)
    {
        $examId = $request->exam_id;
        $instituteId = auth()->user()->institute_id;

        $allowedIds = [7, 17, 23, 20, 11, 50, 36];

        $subjects = Cadresubject::whereIn('id', $allowedIds)
            ->whereIn('id', function ($q) use ($examId, $instituteId) {
                $q->select('subject_id')
                    ->from('marks')
                    ->where('exam_id', $examId)
                    ->whereIn('student_id', function ($q2) use ($instituteId) {
                        $q2->select('id')->from('students')->where('institute_id', $instituteId);
                    });
            })
            ->get();

        return response()->json($subjects);
    }

    // Return units for a given subject
    public function getUnits($subjectId)
    {
        $units = SyllabusUnit::whereHas('competency', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        })->get();

        return response()->json($units);
    }


    // Return questions for a subject (ordered by question_no)
    public function getQuestionsBySubject($subjectId)
    {
        $questions = Question::where('subject_id', $subjectId)
        ->with(['exam','subject','syllabusUnit'])
        ->orderByDesc('question_no')
        ->get();

    // Debug check
    foreach ($questions as $q) {
        \Log::info("QID: {$q->id}, SyllabusUnit: " . ($q->syllabusUnit?->name ?? 'NULL'));
    }

    return response()->json($questions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|numeric',
            'subject_id' => 'required|numeric',
            'syllabus_unit_id' => 'required|exists:syllabus_units,id',
            'question_no' => 'required|numeric',
            'type' => 'required',
            'max_marks' => 'required|numeric'
        ]);

        if ($request->id) {
            // Update existing question
            $question = Question::findOrFail($request->id);
            $question->update($validated);
            return response()->json(['success' => true, 'message' => 'Question updated successfully']);
        } else {
            // Create new question
            $question = Question::create($validated);
            return response()->json(['success' => true, 'message' => 'Question added successfully', 'data' => $question]);
        }
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(['success' => true]);
    }

    
}
