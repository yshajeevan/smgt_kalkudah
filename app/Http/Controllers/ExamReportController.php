<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Models\OlResult;
use App\Models\OlSubjectResult;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Cadresubject;
use App\Models\Institute; 
use App\Models\StudentAttendance;
use App\Models\Competency;
use App\Models\SyllabusUnit;
use App\Models\Question;
use App\Models\StudentResponse;
use Auth;

class ExamReportController extends Controller
{
    public function showOlFinalResult(){
        return view('exam.reports.exam_ol_final');
    }

    public function getOlFinalResultData(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        
        return OlResult::where('institute_id', $instituteId)
            ->select('year', DB::raw('ROUND(pass_percentage, 2) as pass_percentage'))
            ->orderBy('year')
            ->get();
    }

    // .....................................................................................................

    public function olFinalsubjectresult()
    {
        $schoolId = Auth::user()->institute_id;

        $subjects = OlSubjectResult::where('institute_id', $schoolId) 
                    ->select('subject')
                    ->distinct()
                    ->pluck('subject');
        return view('exam.reports.exam_ol_final_subject', compact('subjects'));
    }

    public function getOlFinalsubjectResultsData(Request $request)
    {
        $schoolId = Auth::user()->institute_id;
        $subject = $request->input('subject');

        $results = OlSubjectResult::where('institute_id', $schoolId)
            ->where('subject', $subject)
            ->select('year', 'pass_percent', 'pi')
            ->orderBy('year')
            ->get();

        return response()->json($results);
    }


    // ........................................................................................................

    public function pass()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.subject_pass_percentage', compact('exams'));
    }

    public function getData(Request $request)
    {
        $examId = $request->exam_id;
        if(!$examId) return response()->json(['school'=>[], 'compare'=>[]]);

        $currentSchoolId = Auth()->user()->institute_id;

        // Subjects available in current school
        $subjects = Mark::where('exam_id', $examId)
            ->whereHas('student', fn($q) => $q->where('institute_id', $currentSchoolId))
            ->with('subject')
            ->get()
            ->pluck('subject.cadre','subject_id')
            ->unique();

        $schoolName = Institute::find($currentSchoolId)->institute ?? "Your School";
        $schoolData = [];

        foreach($subjects as $subId => $subName){
            $total = Mark::where('exam_id',$examId)
                ->where('subject_id',$subId)
                ->whereHas('student', fn($q)=>$q->where('institute_id',$currentSchoolId))
                ->count();

            $passed = Mark::where('exam_id',$examId)
                ->where('subject_id',$subId)
                ->where('mark','>=',35)
                ->whereHas('student', fn($q)=>$q->where('institute_id',$currentSchoolId))
                ->count();

            $schoolData[$subName] = $total>0 ? round(($passed/$total)*100,2) : 0;
        }

        // zonal average for same subjects
        $zonalData = [];
        foreach($subjects as $subId => $subName){
            $total = Mark::where('exam_id',$examId)
                ->where('subject_id',$subId)
                ->count();

            $passed = Mark::where('exam_id',$examId)
                ->where('subject_id',$subId)
                ->where('mark','>=',35)
                ->count();

            $zonalData[$subName] = $total>0 ? round(($passed/$total)*100,2) : 0;
        }

        return response()->json([
            'school' => [$schoolName => $schoolData],
            'compare'=> ['Zonal Avg' => $zonalData]
        ]);
    }


    // .....................................................................................................

    public function average()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.subject_average', compact('exams'));
    }


    public function getAverageData(Request $request)
{
    $examId = $request->exam_id;
    if (!$examId) return response()->json(['zonal'=>[], 'compare'=>[]]);

    // 1️⃣ Current school subjects and average
    $currentSchoolId = Auth()->user()->institute_id;

    $schoolMarks = Mark::where('exam_id', $examId)
        ->whereHas('student', fn($q) => $q->where('institute_id', $currentSchoolId))
        ->with('subject')
        ->get();

    $compareData = [];
    foreach ($schoolMarks as $mark) {
        $subName = $mark->subject->cadre ?? 'Unknown';
        if (!isset($compareData[$subName])) $compareData[$subName] = [];
        $compareData[$subName][] = $mark->mark;
    }

    // Calculate average per subject
    foreach ($compareData as $sub => $marks) {
        $compareData[$sub] = round(array_sum($marks)/count($marks), 2);
    }

    // 2️⃣ Zonal averages for only these subjects
    $subjects = array_keys($compareData);
    $allSchoolIds = Mark::where('exam_id', $examId)
        ->with('student')
        ->get()
        ->pluck('student.institute_id')
        ->unique()
        ->values();

    $zonalData = [];
    foreach ($allSchoolIds as $sid) {
        $schoolName = Institute::find($sid)->institute ?? "School $sid";
        foreach ($subjects as $subName) {
            // Find subject_id by name (assuming unique names)
            $subjectId = Mark::where('exam_id', $examId)
                ->whereHas('subject', fn($q)=> $q->where('cadre', $subName))
                ->value('subject_id');

            if (!$subjectId) continue;

            $avg = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->whereHas('student', fn($q)=>$q->where('institute_id', $sid))
                ->avg('mark');

            $zonalData[$schoolName][$subName] = $avg ? round($avg,2) : 0;
        }
    }

    $currentSchool = Institute::find($currentSchoolId);
    $schoolName = $currentSchool->institute ?? "Your School";

    return response()->json([
        'compare' => [$schoolName => $compareData], // school only
        'zonal' => $zonalData // zonal averages for same subjects
    ]);
}


    // Graph of all the subjects marks of each student.....................................................................................................

    public function studentSubjectMarksReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();

        // Default: most recent exam
        $recentExamId = $exams->first()?->id ?? null;

        $subjects = [];
        $instituteId = Auth::user()->institute_id;
        $instituteName = Institute::where('id', $instituteId)->value('institute');

        if($recentExamId){
            $subjects = Mark::where('exam_id', $recentExamId)
                ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
                ->with('subject')
                ->get()
                ->pluck('subject.cadre', 'subject_id')
                ->unique();
        }

        return view('exam.reports.subjects_wise_students_marks', compact('exams', 'subjects', 'recentExamId', 'instituteName'));
    }
    public function getStudentSubjectMarks(Request $request)
    {
        $examId = $request->exam_id;
        $subjectId = $request->subject_id;
        $instituteId = Auth::user()->institute_id;

        // Fetch marks for students of the school
        $marks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
            ->with('student')
            ->get();

        $data = [];
        foreach($marks as $mark){
            $name = $mark->student->name ?? "Student {$mark->student_id}";
            $data[$name] = $mark->mark;
        }

        return response()->json($data);
    }

    public function getSubjectAwards(Request $request)
    {
        $examId = (int)$request->exam_id;
        $studentId = (int)$request->student_id;
        $instituteId = Auth::user()->institute_id;

        // find previous exam (largest id less than current)
        $previousExam = \App\Models\Exam::where('id', '<', $examId)->orderBy('id', 'desc')->first();

        // Get student's marks for current exam
        $studentMarks = Mark::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with('subject')
            ->get()
            ->keyBy('subject_id'); // key by subject for quick lookup

        // If previous exam exists, get student's marks for previous exam (keyed)
        $prevMarks = collect();
        if ($previousExam) {
            $prevMarks = Mark::where('exam_id', $previousExam->id)
                ->where('student_id', $studentId)
                ->with('subject')
                ->get()
                ->keyBy('subject_id');
        }

        $awards = [];
        $improvements = [];

        foreach ($studentMarks as $mark) {
            $subjectId = $mark->subject_id;
            $subjectName = $mark->subject->cadre ?? 'Unknown';
            $score = $mark->mark;
            $isAbsent = (bool) $mark->is_absent; // adjust if your column diff

            // All marks in school for this subject (current exam)
            $allMarks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
                ->get()
                ->filter(fn($m) => $m->mark !== null && $m->mark !== '' && $m->mark >= 35)
                ->sortBy('mark')
                ->values();

            $award = "Keep Trying";

            if (!$isAbsent && $score !== null && $score !== '' && $allMarks->count() > 0 && $score >= 35) {
                $N = $allMarks->count();

                $getPercentile = function($p) use ($allMarks, $N) {
                    $rank = $p * ($N + 1);
                    $k = floor($rank) - 1;
                    $k = max(0, min($k, $N - 1));
                    return $allMarks[$k]->mark;
                };

                $P50 = $getPercentile(0.5);
                $P75 = $getPercentile(0.75);
                $P90 = $getPercentile(0.9);

                if ($score >= $P90 && $score >= 80) $award = "Gold";
                elseif ($score >= $P75 && $score >= 70) $award = "Silver";
                elseif ($score >= $P50 && $score >= 60) $award = "Bronze";
            }

            $awards[] = [
                'subject' => $subjectName,
                'mark'    => $score,
                'award'   => $award,
                'is_absent' => $isAbsent,
            ];

            // ----- improvement calculation -----
            $imprAward = "Keep Trying";
            $prevMarkObj = $prevMarks->get($subjectId);
            if ($prevMarkObj && $score !== null && $score !== '' && !$isAbsent && $prevMarkObj->mark !== null && $prevMarkObj->mark !== '' && !$prevMarkObj->is_absent) {
                $prevScore = $prevMarkObj->mark;
                $delta = floatval($score) - floatval($prevScore);

                // Improvement thresholds (points)
                if ($delta >= 20) $imprAward = "Gold";
                elseif ($delta >= 10) $imprAward = "Silver";
                elseif ($delta >= 5)  $imprAward = "Bronze";
                else $imprAward = "Keep Trying";

                $improvements[] = [
                    'subject' => $subjectName,
                    'previous_mark' => $prevScore,
                    'current_mark'  => $score,
                    'improvement'   => $delta,
                    'award'         => $imprAward,
                ];
            } else {
                // previous missing or absent or current absent -> Keep Trying (still include row)
                $improvements[] = [
                    'subject' => $subjectName,
                    'previous_mark' => $prevMarkObj->mark ?? null,
                    'current_mark'  => $score,
                    'improvement'   => null,
                    'award'         => "Keep Trying",
                ];
            }
        }

        return response()->json([
            'studentAwards' => $awards,
            'improvementAwards' => $improvements,
            'previous_exam' => $previousExam ? ['id'=>$previousExam->id, 'name'=>($previousExam->name ?? ''), 'year'=>($previousExam->year ?? '')] : null
        ]);
    }
    

    // Subject wise award winners...........................................................................................................

    public function subjectMedalWinnersReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();

        return view('exam.awardings.subject_medal_winners', compact('exams'));
    }

    public function getSubjectMedalWinnersData(Request $request)
    {
        $examId = $request->exam_id;
        $instituteId = Auth::user()->institute_id;

        if (!$examId) {
            return response()->json(['error' => 'exam_id is required'], 400);
        }

        $subjects = Cadresubject::all();
        $winners = [];

        foreach ($subjects as $subject) {
            $marks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subject->id)
                ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
                ->with('student')
                ->get()
                ->filter(fn($m) => $m->mark >= 35)
                ->sortBy('mark')
                ->values();

            if ($marks->isEmpty()) continue;

            $N = $marks->count();
            $getPercentile = function($p) use ($marks, $N) {
                $rank = $p * ($N + 1);
                $k = floor($rank) - 1;
                $k = max(0, min($k, $N - 1));
                return $marks[$k]->mark;
            };

            $P50 = $getPercentile(0.5);
            $P75 = $getPercentile(0.75);
            $P90 = $getPercentile(0.9);

            $subjectWinners = [];

            foreach ($marks as $m) {
                $stuAward = "Keep Trying";
                if ($m->mark >= $P90 && $m->mark >= 80) $stuAward = "Gold";
                elseif ($m->mark >= $P75 && $m->mark >= 70) $stuAward = "Silver";
                elseif ($m->mark >= $P50 && $m->mark >= 60) $stuAward = "Bronze";

                if ($stuAward !== "Keep Trying") {
                    $subjectWinners[] = [
                        'student' => $m->student->name ?? "Student {$m->student_id}",
                        'mark'    => $m->mark,
                        'medal'   => $stuAward
                    ];
                }
            }

            if (!empty($subjectWinners)) {
                $winners[] = [
                    'subject' => $subject->cadre ?? 'Unknown',
                    'winners' => $subjectWinners
                ];
            }
        }

        return response()->json(['winners' => $winners]);
    }
    //......................................................................................................

    public function subjectImprovementAwards()
    {
        $exams = Exam::orderBy('year', 'desc')->get();

        return view('exam.awardings.subject_improvement_awards', compact('exams'));
    }

    public function getSubjectImprovementData(Request $request)
    {
        $examId = (int) $request->query('exam_id');
        $instituteId = Auth::user()->institute_id;

        if (!$examId) {
            return response()->json(['error' => 'exam_id is required'], 400);
        }

        // find previous exam (largest id < current)
        $previousExam = Exam::where('id', '<', $examId)->orderBy('id', 'desc')->first();

        if (!$previousExam) {
            // No previous exam — return empty winners but include previous_exam null
            return response()->json([
                'winners' => [],
                'previous_exam' => null
            ]);
        }

        // thresholds (points)
        $thresholds = [
            'gold'   => 20,
            'silver' => 10,
            'bronze' => 5
        ];

        $subjects = Cadresubject::all();
        $result = [];

        foreach ($subjects as $subject) {
            $subjectId = $subject->id;

            // Get current exam marks for this subject in this institute (only students who have a mark)
            $currentMarks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->whereHas('student', fn($q) => $q->where('institute_id', $instituteId))
                ->with('student')
                ->get();

            if ($currentMarks->isEmpty()) {
                continue;
            }

            $winners = [];

            foreach ($currentMarks as $cm) {
                // skip if current is absent or null
                $currentIsAbsent = isset($cm->is_absent) ? (bool) $cm->is_absent : false;
                if ($currentIsAbsent) {
                    continue;
                }

                $currentMark = $cm->mark;
                if ($currentMark === null || $currentMark === '') continue;

                // find previous mark for same student+subject
                $prev = Mark::where('exam_id', $previousExam->id)
                    ->where('subject_id', $subjectId)
                    ->where('student_id', $cm->student_id)
                    ->first();

                if (!$prev) {
                    // no previous mark — cannot compute improvement
                    continue;
                }

                $prevIsAbsent = isset($prev->is_absent) ? (bool) $prev->is_absent : false;
                if ($prevIsAbsent) {
                    // treat as no previous numeric value — skip
                    continue;
                }

                if ($prev->mark === null || $prev->mark === '') continue;

                // compute improvement (current - previous)
                $delta = floatval($currentMark) - floatval($prev->mark);

                // determine award by thresholds (absolute point difference)
                $award = 'Keep Trying';
                if ($delta >= $thresholds['gold']) {
                    $award = 'Gold';
                } elseif ($delta >= $thresholds['silver']) {
                    $award = 'Silver';
                } elseif ($delta >= $thresholds['bronze']) {
                    $award = 'Bronze';
                }

                // only include those who achieved at least Bronze? (we'll include all with award != Keep Trying)
                if ($award !== 'Keep Trying') {
                    $winners[] = [
                        'student'       => $cm->student->name ?? "Student {$cm->student_id}",
                        'previous_mark' => is_numeric($prev->mark) ? floatval($prev->mark) : $prev->mark,
                        'current_mark'  => is_numeric($currentMark) ? floatval($currentMark) : $currentMark,
                        'improvement'   => round($delta, 2),
                        'award'         => $award,
                    ];
                }
            }

            // sort winners: Gold -> Silver -> Bronze, within same award by improvement desc
            $order = ['Gold' => 1, 'Silver' => 2, 'Bronze' => 3];
            usort($winners, function($a, $b) use ($order) {
                $oa = $order[$a['award']] ?? 99;
                $ob = $order[$b['award']] ?? 99;
                if ($oa !== $ob) return $oa <=> $ob;
                return $b['improvement'] <=> $a['improvement'];
            });

            if (!empty($winners)) {
                $result[] = [
                    'subject' => $subject->cadre ?? ($subject->name ?? 'Unknown'),
                    'winners' => $winners
                ];
            }
        }

        return response()->json([
            'winners' => $result,
            'previous_exam' => [
                'id' => $previousExam->id,
                'name' => $previousExam->name ?? null,
                'year' => $previousExam->year ?? null
            ]
        ]);
    }

    // .....................................................................................................


    // Show report page
    public function studentAverageAllSubject()
    {
        $schoolId = Auth::user()->institute_id;

        // Get all exams
        $exams = Exam::orderBy('year', 'desc')->get();

        return view('exam.reports.student_average_all_subjects', compact('exams'));
    }

    // Fetch students associated with selected exam
    public function getStudentsByExam(Request $request)
    {
        $examId = $request->exam_id;
        $schoolId = Auth::user()->institute_id;

        // Get student IDs who have marks for this exam
        $studentIds = Mark::where('exam_id', $examId)
            ->whereHas('student', fn($q) => $q->where('institute_id', $schoolId))
            ->pluck('student_id')
            ->unique()
            ->toArray();

        $students = Student::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($students);
    }

    // Fetch student-wise subject data
    public function getstudentAverageAllSubject(Request $request)
    {
        $studentId = $request->student_id;
        $examId = $request->exam_id ?? null;

        $query = Mark::where('student_id', $studentId);

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        $marks = $query->with('subject', 'exam')->get();

        $data = [];
        foreach ($marks as $mark) {
            $examName = $mark->exam->name . ' (' . $mark->exam->year . ')';
            $subjectName = $mark->subject->cadre ?? "Subject {$mark->subject_id}";

            $data[$examName][$subjectName] = $mark->mark;
        }

        return response()->json($data);
    }

    // .....................................................................................................


    public function studentMarksTableReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.student_marks_table', compact('exams'));
    }

    public function getStudentMarksTableData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) return response()->json([]);

        $schoolId = Auth::user()->institute_id;

        // ✅ Get exam with total_days
        $exam = Exam::findOrFail($examId);
        $totalDays = $exam->total_days ?? 0;

        // ✅ Get students who have marks
        $students = Student::where('institute_id', $schoolId)
            ->whereHas('marks', fn($q) => $q->where('exam_id', $examId))
            ->with([
                'marks' => fn($q) => $q->where('exam_id', $examId)->with('subject'),
                'attendance' => fn($q) => $q->where('exam_id', $examId) // assuming relation
            ])
            ->get();

        // ✅ Collect subjects (ordered)
        $subjects = $students->flatMap(fn($stu) =>
            $stu->marks->map(fn($mark) => [
                'id' => $mark->subject->id,
                'name' => $mark->subject->cadre
            ])
        )->unique('id')->sortBy('id')->values();

        $data = [];
        foreach ($students as $stu) {
            $row = [
                'id' => $stu->id,
                'name' => $stu->name,
                'subjects' => [],
                'total' => 0,
                'average' => 0,
                'attendance' => 0,
            ];

            $total = 0;
            $count = 0;

            foreach ($subjects as $subj) {
                $mark = $stu->marks->firstWhere('subject_id', $subj['id']);

                if (!$mark) {
                    $row['subjects'][$subj['id']] = 'NaN';
                } elseif ($mark->is_absent || is_null($mark->mark)) {
                    $row['subjects'][$subj['id']] = 'AB';
                } else {
                    $row['subjects'][$subj['id']] = round($mark->mark);
                }

                if (is_numeric($row['subjects'][$subj['id']])) {
                    $total += $mark->mark;
                    $count++;
                }
            }

            // ✅ Attendance %
            $attDays = $stu->attendance->first()->attendance ?? 0;
            $row['attendance'] = $totalDays > 0 ? round(($attDays / $totalDays) * 100, 2) : 0;

            $row['total'] = $total;
            $row['average'] = $count ? round($total / $count, 2) : 0;
            $data[] = $row;
        }

        // ✅ Ranking
        $data = collect($data)->sortByDesc('average')->values()->map(function($row, $index) {
            $row['rank'] = $index + 1;
            return $row;
        });

        return response()->json([
            'subjects' => $subjects,
            'students' => $data
        ]);
    }

    // .....................................................................................................

    public function studentRanksTableReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.student_ranking_table', compact('exams'));
    }

    public function getStudentRanksTableData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) return response()->json([]);

        $schoolId = Auth::user()->institute_id;

        $exam = Exam::findOrFail($examId);
        $totalDays = $exam->total_days ?? 0;

        // Fetch students with marks + attendance
        $students = Student::where('institute_id', $schoolId)
            ->whereHas('marks', fn($q) => $q->where('exam_id', $examId))
            ->with([
                'marks' => fn($q) => $q->where('exam_id', $examId)->with('subject'),
                'attendance' => fn($q) => $q->where('exam_id', $examId)
            ])
            ->get();

        // Get unique subjects (ordered)
        $subjects = collect();
        foreach ($students as $stu) {
            foreach ($stu->marks as $mark) {
                if ($mark->subject && !$subjects->contains('id', $mark->subject->id)) {
                    $subjects->push([
                        'id' => $mark->subject->id,
                        'name' => $mark->subject->cadre
                    ]);
                }
            }
        }

        $data = [];
        foreach ($students as $stu) {
            $row = [
                'id' => $stu->id,
                'name' => $stu->name,
                'subjects' => [],
                'pass_fail' => 'Fail',
                'attendance' => 0
            ];

            $cAboveCount = 0;

            foreach ($subjects as $subj) {
                $mark = $stu->marks->firstWhere('subject_id', $subj['id']);

                if (!$mark) {
                    $row['subjects'][$subj['id']] = 'NaN';
                } elseif ($mark->is_absent || is_null($mark->mark)) {
                    $row['subjects'][$subj['id']] = 'AB';
                } else {
                    $rank = $this->getRankLetter($mark->mark);
                    $row['subjects'][$subj['id']] = $rank;
                    if (in_array($rank, ['A','B','C'])) $cAboveCount++;
                }
            }

            // Check Math (36) and Tamil (17)
            $mathRank = $row['subjects'][36] ?? 'NaN';
            $tamilRank = $row['subjects'][17] ?? 'NaN';
            $mathPass = in_array($mathRank, ['A','B','C','S']);
            $tamilPass = in_array($tamilRank, ['A','B','C','S']);

            if ($cAboveCount >= 3 && $mathPass && $tamilPass) {
                $row['pass_fail'] = 'Pass';
            }

            // ✅ Attendance %
            $attDays = $stu->attendance->first()->attendance ?? 0;
            $row['attendance'] = $totalDays > 0 ? round(($attDays / $totalDays) * 100, 2) : 0;

            $data[] = $row;
        }

        return response()->json([
            'subjects' => $subjects,
            'students' => $data
        ]);
    }

    private function getRankLetter($mark)
    {
        if ($mark === null) return 'AB';
        if ($mark < 35) return 'W';
        if ($mark < 50) return 'S';
        if ($mark < 65) return 'C';
        if ($mark < 75) return 'B';
        return 'A';
    }

    // .....................................................................................................

    public function studentMarksPrintReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.student_marks_print', compact('exams'));
    }

    public function getStudentMarksPrintData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) {
            return response()->json(['error' => 'No exam selected'], 400);
        }

        $exam = Exam::findOrFail($examId);

        // Get all students with marks for this exam
        $students = Student::where('institute_id', Auth::user()->institute_id)
            ->whereHas('marks', fn($q) => $q->where('exam_id', $examId))
            ->with([
                'marks' => fn($q) => $q->where('exam_id', $examId)->with('subject'),
                'institute'
            ])
            ->get();

        // Collect unique subjects for exam (for all students)
        $subjects = collect();
        foreach ($students as $stu) {
            foreach ($stu->marks as $mark) {
                if ($mark->subject && !$subjects->contains('id', $mark->subject->id)) {
                    $subjects->push([
                        'id' => $mark->subject->id,
                        'name' => $mark->subject->cadre,
                    ]);
                }
            }
        }

        // Prepare student data
        $dataStudents = [];
        foreach ($students as $stu) {
            $row = [
                'id' => $stu->id,
                'name' => $stu->name,
                'school' => $stu->institute->institute ?? 'N/A',
                'subjects' => [],
                'attendance' => $stu->attendance ?? null,
            ];

            foreach ($stu->marks as $mark) {
                if ($mark->subject) {
                    $row['subjects'][] = [
                        'id' => $mark->subject->id,
                        'name' => $mark->subject->cadre,
                        'value' => $mark->is_absent ? 'AB' : ($mark->grade ?? round($mark->mark)),
                    ];
                }
            }

            $dataStudents[] = $row;
        }

        return response()->json([
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'year' => $exam->year,
            ],
            'schoolName' => optional($students->first()->institute)->institute ?? 'N/A',
            'subjects' => $subjects->values(),
            'students' => $dataStudents,
        ]);
    }

    // .....................................................................................................

    public function examAttendanceReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.exam_attendance', compact('exams'));
    }

    public function getExamAttendanceData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) {
            return response()->json(['error' => 'No exam selected'], 400);
        }

        $exam = Exam::findOrFail($examId);

        // Fetch students with marks for this exam
        $students = Student::where('institute_id', Auth::user()->institute_id)
            ->whereHas('marks', function ($q) use ($examId) {
                $q->where('exam_id', $examId);
            })
            ->with([
                'marks' => function ($q) use ($examId) {
                    $q->where('exam_id', $examId)->with('subject');
                },
            ])
            ->get();
        $counts = [];

        foreach ($students as $stu) {
            // Count number of subjects this student actually sat for
            $numSubjects = $stu->marks->where('is_absent', 0)->count();

            if ($numSubjects > 0) {
                $counts[$numSubjects] = ($counts[$numSubjects] ?? 0) + 1;
            }
        }

        $total = array_sum($counts);

        // Convert counts to objects with percent and count
        $result = [];
        foreach ($counts as $subjects => $count) {
            $result[$subjects . ' Subjects'] = [
                'percent' => round(($count / $total) * 100, 2),
                'count' => $count
            ];
        }

        return response()->json($result);

    }

    // .....................................................................................................

    public function attendanceScatterReport()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        return view('exam.reports.student_attendance_scatter', compact('exams'));
    }

    // .............................................................................................................
    public function getAttendanceScatterData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) {
            return response()->json([
                'data' => [], 
                'summary' => [
                    'above_75' => 0,
                    'between_50_75' => 0,
                    'between_25_49' => 0,
                    'below_25' => 0
                ]
            ]);
        }

        $exam = Exam::findOrFail($examId);
        $instituteId = Auth()->user()->institute_id;

        $attendances = DB::table('student_attendances')
            ->join('students', 'student_attendances.student_id', '=', 'students.id')
            ->where('student_attendances.exam_id', $examId)
            ->where('students.institute_id', $instituteId) // ✅ filter by institute
            ->select(
                'students.id as student_id',
                'students.name as student_name',
                'student_attendances.attendance'
            )
            ->get();

        $data = $attendances->map(fn($s) => [
            'student_id'   => $s->student_id,
            'student_name' => $s->student_name,
            'percentage'   => round(($s->attendance / $exam->total_days) * 100, 2)
        ]);

        // Compute summary
        $summary = [
            'above_75'      => $data->where('percentage','>',75)->count(),
            'between_50_75' => $data->whereBetween('percentage',[50,75])->count(),
            'between_25_49' => $data->whereBetween('percentage',[25,49])->count(),
            'below_25'      => $data->where('percentage','<',25)->count(),
        ];

        return response()->json([
            'data'    => $data,
            'summary' => $summary
        ]);
    }


// .....................................................................................................

    public function schoolSubjectAnalysis()
    {
        $exams = Exam::orderBy('year', 'desc')->get();

        // Pick latest exam
        $latestExam = $exams->first();

        // Get subjects only for latest exam
        $subjects = [];
        if ($latestExam) {
            $subjects = Mark::where('exam_id', $latestExam->id)
                ->with('subject')
                ->get()
                ->pluck('subject')
                ->unique('id')
                ->values();
        }

        $currentSchool = Institute::find(Auth()->user()->institute_id);
        $currentSchoolName = $currentSchool->institute ?? "Your School";

        // Select first subject as default (if available)
        $defaultSubject = $subjects->first();

        return view('exam.reports.school_subject_analysis', compact(
            'exams', 'subjects', 'latestExam', 'defaultSubject', 'currentSchoolName'
        ));
    }


    // public function getSchoolSubjectAnalysisData(Request $request)
    // {
    //     $examId = $request->exam_id;
    //     $subjectId = $request->subject_id;

    //     // Get all schools in this exam
    //     $allSchoolIds = Mark::where('exam_id', $examId)
    //     ->where('subject_id', $subjectId)
    //     ->with('student.class.institute')
    //     ->get()
    //     ->pluck('student.class.institute.id')
    //     ->unique()
    //     ->values();

    //     $data = [];
    //     foreach ($allSchoolIds as $sid) {
    //         $school = Institute::find($sid);
    //         $schoolName = $school->institute ?? "School $sid";

    //         // Marks for this subject + school
    //         $marks = Mark::where('exam_id', $examId)
    //             ->where('subject_id', $subjectId)
    //             ->whereHas('student.class', fn($q) => $q->where('institute_id', $sid))
    //             ->pluck('mark');

    //         $sat = $marks->count();

    //         // Grade counts
    //         $grades = [
    //             'A' => $marks->whereBetween(null, [75, 100])->count(),
    //             'B' => $marks->whereBetween(null, [65, 74])->count(),
    //             'C' => $marks->whereBetween(null, [55, 64])->count(),
    //             'S' => $marks->whereBetween(null, [35, 54])->count(),
    //             'W' => $marks->whereBetween(null, [0, 34])->count(),
    //         ];

    //         $pass = $grades['A'] + $grades['B'] + $grades['C'] + $grades['S'];
    //         $percentage = $sat > 0 ? round(($pass / $sat) * 100, 2) : 0;
    //         $average = $sat > 0 ? round($marks->avg(), 2) : 0;

    //         $data[] = [
    //             'school' => $schoolName,
    //             'A' => $grades['A'],
    //             'B' => $grades['B'],
    //             'C' => $grades['C'],
    //             'S' => $grades['S'],
    //             'W' => $grades['W'],
    //             'sat' => $sat,
    //             'pass' => $pass,
    //             'percentage' => $percentage,
    //             'average' => $average,
    //         ];
    //     }

    //     // Rank by pass %
    //     usort($data, fn($a, $b) => $b['percentage'] <=> $a['percentage']);
    //     $rank = 1;
    //     foreach ($data as &$row) {
    //         $row['rank'] = $rank++;
    //     }

    //     return response()->json($data);
    // }

    public function getSchoolSubjectAnalysisData(Request $request)
    {
        $examId = $request->exam_id;
        $subjectId = $request->subject_id;

        // Global average pass % across all grade 11 students
        $allMarks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->whereHas('student', fn($q) => $q->where('grade_id', 11))
            ->pluck('mark');

        $allSat = $allMarks->count();
        $allGrades = [
            'A' => $allMarks->filter(fn($m) => $m >= 75)->count(),
            'B' => $allMarks->filter(fn($m) => $m >= 65 && $m <= 74)->count(),
            'C' => $allMarks->filter(fn($m) => $m >= 55 && $m <= 64)->count(),
            'S' => $allMarks->filter(fn($m) => $m >= 35 && $m <= 54)->count(),
        ];
        $allPass = array_sum($allGrades);
        $globalPassPercent = $allSat > 0 ? ($allPass / $allSat) * 100 : 0;

        $minStudents = 20;

        // Get all school IDs via student's class relationship
        $allSchoolIds = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->whereHas('student', fn($q) => $q->where('grade_id', 11))
            ->with('student.institute')
            ->get()
            ->pluck('student.institute.id')
            ->unique()
            ->values();

        $data = [];

        foreach ($allSchoolIds as $sid) {
            $school = Institute::find($sid);
            $schoolName = $school->institute ?? "School $sid";

            $marks = Mark::where('exam_id', $examId)
                ->where('subject_id', $subjectId)
                ->whereHas('student', fn($q) => $q->where('grade_id', 11)->where('institute_id', $sid))
                ->pluck('mark');

            $sat = $marks->count();

            $grades = [
                'A' => $marks->filter(fn($m) => $m >= 75)->count(),
                'B' => $marks->filter(fn($m) => $m >= 65 && $m <= 74)->count(),
                'C' => $marks->filter(fn($m) => $m >= 55 && $m <= 64)->count(),
                'S' => $marks->filter(fn($m) => $m >= 35 && $m <= 54)->count(),
                'W' => $marks->filter(fn($m) => $m < 35)->count(),
            ];

            $pass = $grades['A'] + $grades['B'] + $grades['C'] + $grades['S'];
            $rawPercent = $sat > 0 ? ($pass / $sat) * 100 : 0;

            $adjustedPercent = ($pass + $globalPassPercent * $minStudents / 100) / ($sat + $minStudents) * 100;
            $average = $sat > 0 ? round($marks->avg(), 2) : 0;

            $data[] = [
                'school' => $schoolName,
                'A' => $grades['A'],
                'B' => $grades['B'],
                'C' => $grades['C'],
                'S' => $grades['S'],
                'W' => $grades['W'],
                'sat' => $sat,
                'pass' => $pass,
                'percentage' => round($rawPercent, 2),
                'adjusted_percentage' => round($adjustedPercent, 2),
                'average' => $average,
            ];
        }

        usort($data, fn($a, $b) => $b['adjusted_percentage'] <=> $a['adjusted_percentage']);
        $rank = 1;
        foreach ($data as &$row) {
            $row['rank'] = $rank++;
        }

        return response()->json($data);
    }

    public function getExamSubjects(Request $request)
    {
        $examId = $request->exam_id;

        $subjects = Mark::where('exam_id', $examId)
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->unique('id')
            ->values();

        return response()->json($subjects);
    }

    // .................................................................................................

    public function schoolOverallAnalysis()
    {
        $exams = Exam::orderBy('year', 'desc')->get();
        $latestExam = $exams->first(); // pick the most recent exam

        $currentSchool = Institute::find(Auth()->user()->institute_id);
        $currentSchoolName = $currentSchool->institute ?? "Your School";

        return view('exam.reports.school_overall_analysis', compact('exams', 'latestExam', 'currentSchoolName'));

    }


    // public function getSchoolOverallAnalysisData(Request $request)
    // {
    //     $examId = $request->exam_id;
    //     if (!$examId) return response()->json([]);

    //     // Get all schools that have students in this exam
    //     $schoolIds = Student::whereHas('marks', fn($q) => $q->where('exam_id', $examId))
    //         ->pluck('institute_id')
    //         ->unique();

    //     $data = [];

    //     foreach ($schoolIds as $sid) {
    //         $school = Institute::find($sid);
    //         $schoolName = $school->institute ?? "School $sid";

    //         // Students of this school with marks for this exam
    //         $students = Student::where('institute_id', $sid)
    //             ->whereHas('marks', fn($q) => $q->where('exam_id', $examId))
    //             ->with(['marks' => fn($q) => $q->where('exam_id', $examId)->with('subject')])
    //             ->get();

    //         $total = $students->count();
    //         $passCount = 0;

    //         foreach ($students as $stu) {
    //             $cAboveCount = 0;
    //             $subjects = $stu->marks->pluck('subject_id')->unique();

    //             foreach ($stu->marks as $mark) {
    //                 $grade = $this->getRankLetter($mark->mark);
    //                 if (in_array($grade, ['A','B','C'])) $cAboveCount++;
    //             }

    //             $math = $stu->marks->firstWhere('subject_id', 36);
    //             $tamil = $stu->marks->firstWhere('subject_id', 17);

    //             $mathPass = $math && in_array($this->getRankLetter($math->mark), ['A','B','C','S']);
    //             $tamilPass = $tamil && in_array($this->getRankLetter($tamil->mark), ['A','B','C','S']);

    //             if ($cAboveCount >= 3 && $mathPass && $tamilPass) {
    //                 $passCount++;
    //             }
    //         }

    //         $failCount = $total - $passCount;
    //         $percentage = $total > 0 ? round(($passCount / $total) * 100, 2) : 0;

    //         $data[] = [
    //             'school' => $schoolName,
    //             'total' => $total,
    //             'pass' => $passCount,
    //             'fail' => $failCount,
    //             'percentage' => $percentage,
    //         ];
    //     }

    //     // Sort and assign rank
    //     usort($data, fn($a, $b) => $b['percentage'] <=> $a['percentage']);
    //     $rank = 1;
    //     foreach ($data as &$row) {
    //         $row['rank'] = $rank++;
    //     }

    //     return response()->json($data);
    // }

    public function getSchoolOverallAnalysisData(Request $request)
    {
        $examId = $request->exam_id;
        if (!$examId) return response()->json([]);

        // Get all schools that have students in this exam
        $schoolIds = Student::whereHas('marks', fn($q) => $q->where('exam_id', $examId))
            ->pluck('institute_id')
            ->unique();

        $data = [];

        foreach ($schoolIds as $sid) {
            $school = Institute::find($sid);
            $schoolName = $school->institute ?? "School $sid";

            // Students of this school with marks for this exam
            $students = Student::where('institute_id', $sid)
                ->whereHas('marks', fn($q) => $q->where('exam_id', $examId))
                ->with(['marks' => fn($q) => $q->where('exam_id', $examId)->with('subject')])
                ->get();

            $total = $students->count();
            $passCount = 0;

            foreach ($students as $stu) {
                $cAboveCount = 0;

                foreach ($stu->marks as $mark) {
                    $grade = $this->getRankLetter($mark->mark);
                    if (in_array($grade, ['A','B','C'])) $cAboveCount++;
                }

                $math = $stu->marks->firstWhere('subject_id', 36);
                $tamil = $stu->marks->firstWhere('subject_id', 17);

                $mathPass = $math && in_array($this->getRankLetter($math->mark), ['A','B','C','S']);
                $tamilPass = $tamil && in_array($this->getRankLetter($tamil->mark), ['A','B','C','S']);

                if ($cAboveCount >= 3 && $mathPass && $tamilPass) {
                    $passCount++;
                }
            }

            $failCount = $total - $passCount;
            $percentage = $total > 0 ? round(($passCount / $total) * 100, 2) : 0;

            // Adjusted pass %: penalize schools with very few students (<20)
            $adjustedPercentage = $total < 20 ? round($percentage * ($total / 20), 2) : $percentage;

            $data[] = [
                'school' => $schoolName,
                'total' => $total,
                'pass' => $passCount,
                'fail' => $failCount,
                'percentage' => $percentage,
                'adjusted_percentage' => $adjustedPercentage, // ← make sure this is here
            ];
        }

        // Sort by adjusted pass %
        usort($data, fn($a, $b) => $b['adjusted_percentage'] <=> $a['adjusted_percentage']);
        $rank = 1;
        foreach ($data as &$row) {
            $row['rank'] = $rank++;
        }

        return response()->json($data);
    }

    // .....................................................................................................

}
