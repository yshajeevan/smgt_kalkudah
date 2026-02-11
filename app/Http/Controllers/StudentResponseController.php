<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Question;
use App\Models\StudentResponse;
use Illuminate\Http\Request;
use Auth;

class StudentResponseController extends Controller
{
    public function create()
    {
        $exams = Exam::all();

        return view('exam.student_responses.create', compact('exams'));
    }

    // Fetch subjects by exam
    public function loadSubjects(Request $request)
    {
        $subjects = Mark::where('exam_id', $request->exam_id)
                        ->with('subject')
                        ->get()
                        ->pluck('subject')
                        ->filter()        // remove null subjects
                        ->unique('id')
                        ->values();

        return response()->json($subjects);
    }

    // Fetch students by exam
    public function loadStudents(Request $request)
    {
        $examId = $request->exam_id;
        $subjectId = $request->subject_id;
        $instituteId = Auth()->user()->institute_id;

        // fetch unique students who have marks for this exam + subject + institute
        $students = Mark::where('exam_id', $examId)
                        ->where('subject_id', $subjectId)
                        ->whereHas('student', function($q) use ($instituteId) {
                            $q->where('institute_id', $instituteId);
                        })
                        ->with('student')
                        ->get()
                        ->pluck('student')   // get student model
                        ->filter()           // remove nulls just in case
                        ->unique('id')       // remove duplicates
                        ->values();

        return response()->json($students);
    }


    // Fetch questions + responses
    public function loadQuestions(Request $request)
    {
        $studentId  = $request->student_id;
        $examId     = $request->exam_id;
        $subjectId  = $request->subject_id;

        $questions = Question::where('exam_id', $examId)
                             ->where('subject_id', $subjectId)
                             ->get();

        $responses = StudentResponse::where('student_id', $studentId)
                                    ->whereIn('question_id', $questions->pluck('id'))
                                    ->get()
                                    ->keyBy('question_id');

        return view('exam.student_responses.partials.questions_table', compact('questions', 'responses', 'studentId'));
    }

    // Save responses
    public function store(Request $request)
    {
        $studentId = $request->student_id;

        foreach ($request->responses as $questionId => $data) {
            $question = Question::find($questionId);

            if ($question->type == 'MCQ') {
                $isCorrect = isset($data['is_correct']) ? (bool)$data['is_correct'] : null;
                StudentResponse::updateOrCreate(
                    ['student_id' => $studentId, 'question_id' => $questionId],
                    ['is_correct' => $isCorrect, 'obtained_marks' => null]
                );
            } else { // SAQ
                $marks = $data['obtained_marks'] ?? null;
                if ($marks !== null && $marks > $question->max_marks) {
                    return response()->json([
                        'success' => false,
                        'errors' => ["Marks for Question {$question->id} cannot exceed {$question->max_marks}"]
                    ]);
                }
                StudentResponse::updateOrCreate(
                    ['student_id' => $studentId, 'question_id' => $questionId],
                    ['is_correct' => null, 'obtained_marks' => $marks]
                );
            }
        }

        // Return success flag, no page reload
        return response()->json(['success' => true]);
    }


    public function status(Request $request)
    {
        $examId = $request->exam_id;
        $subjectId = $request->subject_id;
        $instituteId = Auth()->user()->institute_id;

        // All students for this exam+subject
        $students = Mark::where('exam_id', $examId)
                        ->where('subject_id', $subjectId)
                        ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
                        ->with('student')
                        ->get()
                        ->pluck('student')
                        ->unique('id')
                        ->values();

        // Check who has responses
        $responses = StudentResponse::whereIn('student_id', $students->pluck('id'))
                                    ->whereHas('question', function($q) use ($examId, $subjectId){
                                        $q->where('exam_id', $examId)->where('subject_id', $subjectId);
                                    })
                                    ->get()
                                    ->groupBy('student_id');

        return view('exam.student_responses.partials.status_table', [
            'students' => $students,
            'responses' => $responses,
            'examId' => $examId,
            'subjectId' => $subjectId
        ]);
    }

    public function delete(Request $request)
    {
        $studentId = $request->student_id;
        $examId    = $request->exam_id;
        $subjectId = $request->subject_id;

        StudentResponse::where('student_id', $studentId)
            ->whereHas('question', function($q) use ($examId, $subjectId){
                $q->where('exam_id', $examId)->where('subject_id', $subjectId);
            })
            ->delete();

        return response()->json(['message' => 'Responses deleted successfully']);
}

}
