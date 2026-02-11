<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Cadresubject;
use App\Models\StudentAttendance;
use Auth;

class MarksController extends Controller
{
    // Show create marks page
    public function createMarks(Request $request)
    {
        $exams = Exam::orderBy('year','desc')->get();
        $students = Student::with('marks')
            ->where('grade_id', 11)
            ->where('institute_id', Auth::user()->institute_id)
            ->get();

        $selectedExamId = $request->exam_id ?? session('selected_exam_id') ?? null;

        return view('exam.data_entry.create', compact('exams', 'students', 'selectedExamId'));
    }

    // AJAX: return subjects/marks/attendance for a student & exam
    // AJAX: return subjects/marks/attendance for a student & exam
    public function getStudentSubjects(Request $request, $studentId)
    {
        $examId = $request->exam_id;
        $student = Student::findOrFail($studentId);

        $existingMarks = Mark::where('student_id', $studentId)
                            ->where('exam_id', $examId)
                            ->pluck('mark','subject_id')
                            ->toArray();

        $existingAbsent = Mark::where('student_id', $studentId)
                            ->where('exam_id', $examId)
                            ->pluck('is_absent','subject_id')
                            ->toArray();

        $attendance = StudentAttendance::where('student_id', $studentId)
                                    ->where('exam_id', $examId)
                                    ->value('attendance');

        // base/main subjects (hard-coded as before)
        $subjects = [
            ['id' => 36, 'name' => 'Maths'],
            ['id' => 7,  'name' => 'Science'],
            ['id' => 17, 'name' => 'Tamil'],
            ['id' => 18, 'name' => 'English'],
            ['id' => 23, 'name' => 'History'],
        ];

        // collect current optional ids from student (filter nulls, unique)
        $optionalIds = collect([
            $student->cadresubject4_id,
            $student->cadresubject1_id,
            $student->cadresubject2_id,
            $student->cadresubject3_id,
        ])->filter()->unique()->values();

        if ($optionalIds->isNotEmpty()) {
            $optionalSubjects = Cadresubject::whereIn('id', $optionalIds)
                ->get(['id','cadre'])
                ->sortBy('cadre')
                ->map(function ($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->cadre,
                    ];
                })
                ->toArray();

            // keep preferred order: if cadresubject4_id exists push it first among optionals
            if ($student->cadresubject4_id) {
                $first = array_filter($optionalSubjects, fn($s) => $s['id'] == $student->cadresubject4_id);
                $rest = array_filter($optionalSubjects, fn($s) => $s['id'] != $student->cadresubject4_id);
                $optionalSubjects = array_merge($first, $rest);
            }

            $subjects = array_merge($subjects, $optionalSubjects);
        }

        // If there are existing marks for some subject ids not yet in $subjects (edge cases),
        // fetch those subject names and append so they are editable too.
        $existingIds = array_map('intval', array_keys($existingMarks));
        $currentSubjectIds = array_map('intval', array_column($subjects, 'id'));
        $extraIds = array_values(array_diff($existingIds, $currentSubjectIds));

        if (!empty($extraIds)) {
            $extraSubjects = Cadresubject::whereIn('id', $extraIds)
                ->get(['id','cadre'])
                ->map(function($sub){
                    return ['id' => $sub->id, 'name' => $sub->cadre];
                })->toArray();

            // append extras
            $subjects = array_merge($subjects, $extraSubjects);
        }

        // finally return subjects plus marks/absent/attendance (existing arrays already built)
        return response()->json([
            'subjects'   => $subjects,
            'marks'      => $existingMarks,
            'absent'     => $existingAbsent,
            'attendance' => $attendance,
        ]);
    }


    // AJAX: return students table rows filtered by exam id
    public function studentsSummary($examId)
    {
        $students = Student::where('grade_id', 11)
                    ->where('institute_id', Auth::user()->institute_id)
                    ->get();

        $rows = '';
        foreach($students as $i => $stu){
            $marksCount = Mark::where('student_id', $stu->id)
                              ->where('exam_id', $examId)
                              ->count();

            $attendance = StudentAttendance::where('student_id', $stu->id)
                                           ->where('exam_id', $examId)
                                           ->value('attendance');

            $completed = $marksCount > 0 || !is_null($attendance);

            $rows .= '<tr>';
            $rows .= '<td>'.($i+1).'</td>';
            $rows .= '<td>'.e($stu->name).' ('.e($stu->id).')</td>';
            $rows .= '<td>'.$marksCount.($completed ? ' <span class="text-success ms-1">✔</span>' : '').'</td>';
            $rows .= '<td>'.($attendance ?? '-').'</td>';
            $rows .= '<td><button class="btn btn-sm btn-info edit-btn" data-student="'.e($stu->id).'">Edit</button></td>';
            $rows .= '</tr>';
        }

        return response()->json(['html' => $rows]);
    }

    public function storeMarks(Request $request)
    {
        foreach ($request->marks as $subjectId => $mark) {
            Mark::updateOrCreate(
                [
                    'student_id' => $request->student_id,
                    'exam_id'    => $request->exam_id,
                    'subject_id' => $subjectId,
                ],
                [
                    'mark'      => $mark,
                    'is_absent' => isset($request->is_absent[$subjectId]) ? 1 : 0,
                ]
            );
        }

        // Attendance
        StudentAttendance::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'exam_id'    => $request->exam_id,
            ],
            [
                'attendance' => $request->attendance,
            ]
        );
        

        session(['selected_exam_id' => $request->exam_id]); // remember selected exam

        return back()->with('success', 'Marks & attendance saved successfully');
    }
}