<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institute; 
use Auth;
use DB;

class SchoolController extends Controller
{
    public function home(){

        $schoolId = Auth::user()->institute_id;

        // Get institute name
        $institute = Institute::where('id', $schoolId)->value('institute');
        return view('school', compact('institute'));
    }
    public function index()
    {
        $schoolId = Auth::user()->institute_id;

        // Get institute name
        $institute = Institute::where('id', $schoolId)->value('institute');

        $grades = [10, 11]; // explicitly Grade 10 & 11

        $summary = collect($grades)->map(function ($grade) use ($schoolId) {
            // Total students exist in that grade
            $stuExist = \DB::table('students')
                ->where('institute_id', $schoolId)
                ->where('grade_id', $grade)
                ->count();

            // Students entered in marks table
            $stuEntere = \DB::table('marks')
                ->join('students', 'marks.student_id', '=', 'students.id')
                ->where('students.institute_id', $schoolId)
                ->where('students.grade_id', $grade)
                ->distinct('marks.student_id')
                ->count('marks.student_id');

            // Latest exam name (instead of count)
            $lastExam = \DB::table('marks')
                ->join('students', 'marks.student_id', '=', 'students.id')
                ->join('exams', 'marks.exam_id', '=', 'exams.id')
                ->where('students.institute_id', $schoolId)
                ->where('students.grade_id', $grade)
                ->orderBy('exams.year', 'desc') // or use exams.date if available
                ->orderBy('exams.id', 'desc') // optional if you have term column
                ->value('exams.name'); // exam name

            return [
                'grade'     => 'G' . $grade,
                'stuExist'  => $stuExist,
                'stuEntere' => $stuEntere,
                'lastTerm'  => $lastExam ?? '-', // fallback if no exam
            ];
        });

        return view('exam.dashboard', compact('institute', 'summary'));
    }


}