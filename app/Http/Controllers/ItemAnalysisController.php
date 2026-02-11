<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\StudentResponse;
use App\Models\Cadresubject;
use App\Models\Student;
use App\Models\Question;
use Auth;

class ItemAnalysisController extends Controller
{
public function itemAnalysis()
{
    $exams = Exam::orderBy('year','desc')->get();
    $subjects = []; // initially empty

    return view('exam.reports.item_analysis', compact('exams','subjects'));
}

public function getSubjects(Request $request)
{
    $examId = $request->exam_id;
    $instituteId = Auth()->user()->institute_id;

    $subjects = Cadresubject::whereHas('questions.responses', function($q) use ($examId, $instituteId) {
            $q->where('exam_id', $examId)
              ->whereHas('student', function($sq) use ($instituteId) {
                  $sq->where('institute_id', $instituteId);
              });
        })
        ->distinct()
        ->get(['id','cadre']);

    return response()->json($subjects);
}


// Fetch data
public function getItemAnalysisData(Request $request)
{
    $examId = $request->exam_id;
    $subjectId = $request->subject_id;
    $instituteId = Auth()->user()->institute_id; // ✅ fixed

    $questions = Question::where('exam_id', $examId)
        ->where('subject_id', $subjectId)
        ->with('syllabusUnit.competency')
        ->get();

    $data = [];

    foreach ($questions as $q) {
        $responses = StudentResponse::where('question_id', $q->id)
            ->join('students', 'student_responses.student_id', '=', 'students.id')
            ->where('students.institute_id', $instituteId)
            ->get(['student_responses.*','students.id as student_id']);

        $total_students = $responses->count();

        $correct=0; $wrong=0; $not_attempted=0; $partial=0; $obtained_total=0;

        foreach ($responses as $res) {
            if ($q->type == 'MCQ') {
                if ($res->is_correct == 1) $correct++;
                elseif ($res->is_correct == 0) $wrong++;
                else $not_attempted++;
            } else { // SAQ
                if ($res->obtained_marks !== null) {
                    $obtained_total += $res->obtained_marks;
                    if ($res->obtained_marks == $q->max_marks) $correct++;
                    elseif ($res->obtained_marks > 0) $partial++;
                    else $wrong++;
                } else {
                    $not_attempted++;
                }
            }
        }

        if ($q->type == 'SAQ') {
            $DI = $total_students > 0
                ? round(($obtained_total / ($total_students * $q->max_marks)) * 100, 2)
                : 0;
            $Disc = null;
        } else {
            $DI = $total_students > 0 ? round(($correct / $total_students) * 100, 2) : 0;
            $Disc = $total_students > 0 ? round((($correct - $wrong) / $total_students) * 100, 2) : 0;
        }

        $data[] = [
            'question_no' => $q->question_no,
            'type' => $q->type,
            'competency' => $q->syllabusUnit->competency->name ?? '',
            'syllabus' => $q->syllabusUnit->name ?? '',
            'total' => $total_students,
            'correct' => $correct,
            'partial' => $partial,
            'wrong' => $wrong,
            'not_attempted' => $not_attempted,
            'correct_percent' => $total_students > 0 ? round(($correct / $total_students) * 100, 2) : 0,
            'avg_obtained' => $q->type == 'SAQ' && $total_students > 0 ? round($obtained_total / $total_students, 2) : null,
            'max_marks' => $q->type == 'SAQ' ? $q->max_marks : null,
            'difficulty_index' => $DI,
            'discrimination_index' => $Disc,
        ];
    }

    return response()->json($data);
}

public function studentUnitAnalysis()
{
    $exams = Exam::orderBy('year','desc')->get();
    $instituteId = Auth()->user()->institute_id; // ✅ fixed

    $subjects = Cadresubject::whereHas('questions.responses.student', function($q) use ($instituteId) {
            $q->where('institute_id', $instituteId);
        })
        ->distinct()
        ->get();
    return view('exam.reports.item_analysis_student_unit', compact('exams','subjects'));
}

public function getStudentUnitData(Request $request)
{
    $examId = $request->exam_id;
    $subjectId = $request->subject_id;
    $instituteId = Auth()->user()->institute_id;

    $students = Student::where('institute_id', $instituteId)->get();
    $questions = Question::where('exam_id', $examId)
        ->where('subject_id', $subjectId)
        ->with('syllabusUnit.competency')
        ->get();

    $data = [
        'students' => $students->pluck('name'),
        'units' => []
    ];

    $units = [];

    foreach ($questions as $q) {
        $unitName = $q->syllabusUnit->name ?? 'Unknown Unit';
        $compName = $q->syllabusUnit->competency->name ?? '';

        if (!isset($units[$unitName])) {
            $units[$unitName] = [
                'syllabus' => $unitName,
                'competency' => $compName,
                'student_totals' => array_fill(0, count($students), 0),
                'student_counts' => array_fill(0, count($students), 0),
            ];
        }

        foreach ($students as $idx => $stu) {
            $res = StudentResponse::where('student_id', $stu->id)
                ->where('question_id', $q->id)
                ->first();

            $percent = 0;
            if ($q->type == 'MCQ') {
                $percent = $res ? ($res->is_correct ? 100 : 0) : 0;
            } else {
                if ($res && $res->obtained_marks !== null) {
                    $percent = round(($res->obtained_marks / $q->max_marks) * 100, 2);
                }
            }

            $units[$unitName]['student_totals'][$idx] += $percent;
            $units[$unitName]['student_counts'][$idx] += 1;
        }
    }

    // compute averages per student per unit
    foreach ($units as $unit) {
        $averages = [];
        foreach ($unit['student_totals'] as $i => $total) {
            $count = $unit['student_counts'][$i];
            $averages[] = $count ? round($total / $count, 2) : 0;
        }
        $data['units'][] = [
            'syllabus' => $unit['syllabus'],
            'competency' => $unit['competency'],
            'student_percents' => $averages
        ];
    }

    return response()->json($data);
}



}