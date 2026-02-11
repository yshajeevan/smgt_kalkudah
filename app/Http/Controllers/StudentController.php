<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Cadresubject;
use App\Models\Institute;
use App\Models\Grade;
use App\Models\Dsdivision;
use App\Models\Gndivision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // index - main page
    public function index(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $institute = Institute::find($instituteId);
        $instType = $institute ? $institute->type : null;

        // grades for rows = all grades matching institute type
        $gradesForRows = Grade::when($instType, function($q) use ($instType){
            $q->where('type','like', "%{$instType}%");
        })->orderBy('name')->get();

        // available grades for filters = distinct grade_ids in students
        $gradeIds = Student::where('institute_id', $instituteId)
                    ->whereNotNull('grade_id')
                    ->distinct()
                    ->pluck('grade_id')
                    ->filter()
                    ->toArray();

        $availableGrades = Grade::whereIn('id', $gradeIds ?: [0])->orderBy('name')->get();

        // available EWS and Status from students table
        $availableEws = Student::where('institute_id', $instituteId)->distinct()->pluck('ews_color')->filter()->unique()->values();
        $availableStatus = Student::where('institute_id', $instituteId)->distinct()->pluck('status')->filter()->unique()->values();

        // institutes for inline select: id < 69
        $institutesForSelect = Institute::where('id','<',69)->orderBy('institute')->get();

        // filters
        $filterGrade = $request->get('grade');
        $filterEws = $request->get('ews_color');
        $filterStatus = $request->get('status');
        $q = $request->get('q');

        $query = Student::with(['institute','gradeRelation'])
            ->where('institute_id', $instituteId)
            ->orderBy('admission_number');

        if($filterGrade)   $query->where('grade_id', $filterGrade);
        if($filterEws)     $query->where('ews_color', $filterEws);
        if($filterStatus)  $query->where('status', $filterStatus);
        if($q) $query->where(function($qb) use ($q) {
            $qb->where('name','like', "%{$q}%")
               ->orWhere('admission_number','like', "%{$q}%");
        });

        $totalCount = Student::where('institute_id', $instituteId)->count();
        $students = $query->paginate(25)->appends($request->except('page'));
        $filteredCount = $students->total();

        return view('students.index', compact(
            'students',
            'gradesForRows',
            'availableGrades',
            'availableEws',
            'availableStatus',
            'institutesForSelect',
            'totalCount','filteredCount',
            'filterGrade','filterEws','filterStatus','q'
        ));
    }

    // AJAX list (live search & filters) - returns HTML rows + counts
    public function listAjax(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $institute = Institute::find($instituteId);
        $instType = $institute ? $institute->type : null;

        $gradesForRows = Grade::when($instType, function($q) use ($instType){
            $q->where('type','like', "%{$instType}%");
        })->orderBy('name')->get();

        $gradeIds = Student::where('institute_id', $instituteId)
                    ->whereNotNull('grade_id')
                    ->distinct()
                    ->pluck('grade_id')
                    ->filter()
                    ->toArray();

        $availableGrades = Grade::whereIn('id', $gradeIds ?: [0])->orderBy('name')->get();

        $institutesForSelect = Institute::where('id','<',69)->orderBy('institute')->get();

        $filterGrade = $request->get('grade');
        $filterEws = $request->get('ews_color');
        $filterStatus = $request->get('status');
        $q = $request->get('q');

        $query = Student::with(['institute','gradeRelation'])
            ->where('institute_id', $instituteId)
            ->orderBy('name');

        if($filterGrade)   $query->where('grade_id', $filterGrade);
        if($filterEws)     $query->where('ews_color', $filterEws);
        if($filterStatus)  $query->where('status', $filterStatus);
        if($q) $query->where(function($qb) use ($q) {
            $qb->where('name','like', "%{$q}%")
               ->orWhere('admission_number','like', "%{$q}%");
        });

        $students = $query->limit(200)->get();
        $filteredCount = $query->count();
        $totalCount = Student::where('institute_id', $instituteId)->count();

        // render partial rows view (we embed rows server side here)
        $html = view('students._rows', compact('students','gradesForRows','availableGrades','institutesForSelect'))->render();

        return response()->json([
            'html' => $html,
            'filteredCount' => $filteredCount,
            'totalCount' => $totalCount,
        ]);
    }
    

    // Quick add (AJAX) with census checks (as implemented earlier)
    public function quickStore(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $institute = Institute::find($instituteId);
        $currentCensus = $institute ? (string)$institute->census : null;

        $payload = $request->only(['admission_number','name','grade_id','ews_color','status','force']);

        $validator = \Validator::make($payload, [
            'admission_number' => ['required','string','max:100'],
            'name' => ['required','string','max:255'],
            'grade_id' => ['nullable','integer','exists:grades,id'],
            'ews_color' => ['nullable','integer', Rule::in([1,2,3])],
            'status' => ['required', Rule::in(['Active','Droped out'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['success'=>false, 'message' => $validator->errors()->first()], 422);
        }

        $admission = trim($payload['admission_number']);

        // existence check: full admission number in same institute
        $exists = Student::where('institute_id', $instituteId)
                         ->where('admission_number', $admission)
                         ->exists();
        if ($exists) {
            return response()->json(['success'=>false, 'message' => 'Admission number already exists'], 422);
        }

        $prefix = (strpos($admission, '_') !== false) ? explode('_', $admission, 2)[0] : $admission;
        $prefix = trim($prefix);

        if ($currentCensus && $prefix === (string)$currentCensus) {
            // proceed
        } else {
            $otherInstitute = Institute::where('census', $prefix)->where('id','!=',$instituteId)->first();
            if ($otherInstitute) {
                if (!$request->boolean('force')) {
                    return response()->json([
                        'success'=>false,
                        'confirm_needed'=>true,
                        'message'=>'Are you sure that the student comes from another school (' . ($otherInstitute->institute ?? $otherInstitute->id) . ')?'
                    ], 200);
                }
                // force true -> proceed
            } else {
                return response()->json(['success'=>false, 'message' => 'Wrong census number!. Please check'], 422);
            }
        }

        $student = Student::create([
            'admission_number' => $admission,
            'name' => $payload['name'],
            'grade_id' => $payload['grade_id'] ?? null,
            'ews_color' => $payload['ews_color'] ?? null,
            'status' => $payload['status'],
            'institute_id' => $instituteId,
            'ews_updated_by' => $payload['ews_color'] ? $instituteId : null,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['success'=>true, 'student_id' => $student->id]);
    }

    public function checkAdmission(Request $request)
    {
        $user = Auth::user();
        $instituteId = $user->institute_id;
        $institute = Institute::find($instituteId);
        $currentCensus = $institute ? (string)$institute->census : null;

        $data = $request->validate([
            'admission_number' => ['required','string','max:100'],
            'force' => ['nullable','boolean'],
        ]);

        $admission = trim($data['admission_number']);

        // 1) full admission uniqueness check (same institute)
        $exists = Student::where('institute_id', $instituteId)
                        ->where('admission_number', $admission)
                        ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Admission number already exists in this school.'
            ], 422);
        }

        // 2) prefix check
        $prefix = (strpos($admission, '_') !== false) ? explode('_', $admission, 2)[0] : $admission;
        $prefix = trim((string)$prefix);

        if ($currentCensus && $prefix === (string)$currentCensus) {
            // all good — same school prefix
            return response()->json(['success' => true, 'message' => 'OK']);
        }

        // check other institutes for census match
        $otherInstitute = Institute::where('census', $prefix)
                            ->where('id', '!=', $instituteId)
                            ->first();

        if ($otherInstitute) {
            // found other school — ask for confirmation unless force true
            if (empty($data['force'])) {
                return response()->json([
                    'success' => false,
                    'confirm_needed' => true,
                    'message' => 'Are you sure that the student comes from another school?',
                    'other_institute_id' => $otherInstitute->id,
                    'other_institute_name' => $otherInstitute->institute ?? null
                ], 200);
            } else {
                // force provided — allow (note: we don't save here; we only tell client it's allowable)
                return response()->json(['success' => true, 'message' => 'OK (forced)']);
            }
        }

        // prefix not found in any institute -> invalid census
        return response()->json([
            'success' => false,
            'message' => 'Wrong census number!. Please check'
        ], 422);
    }

    public function cadresByGrade($gradeId)
    {
        // Normalize
        $gradeId = (int)$gradeId;

        // Always religion for cadre4
        $religion = Cadresubject::where('category2', 'Religion')
                        ->orderBy('cadre')
                        ->get(['id','cadre']);

        // Default empty sets
        $basket1 = collect();
        $basket2 = collect();
        $basket3 = collect();
        $al = collect();
        $yrs13 = collect();

        // NEW: grades 6-9 -> use Basket 2 for 1/2/3
        if (in_array($gradeId, [6,7,8,9])) {
            $basket2 = Cadresubject::where('category2', 'Basket 2')->orderBy('cadre')->get(['id','cadre']);
        }

        // Case 1: grade 10,11,12,13 -> Basket 1/2/3
        if (in_array($gradeId, [10,11,12,13])) {
            $basket1 = Cadresubject::where('category2', 'Basket 1')->orderBy('cadre')->get(['id','cadre']);
            $basket2 = Cadresubject::where('category2', 'Basket 2')->orderBy('cadre')->get(['id','cadre']);
            $basket3 = Cadresubject::where('category2', 'Basket 3')->orderBy('cadre')->get(['id','cadre']);
        }

        // Case 2: grade between 14 and 37 inclusive -> A/L
        if ($gradeId >= 14 && $gradeId <= 37) {
            $al = Cadresubject::where('category2', 'A/L')->orderBy('cadre')->get(['id','cadre']);
        }

        // Case 4: grade 38 or 39 -> 13_years_education
        if (in_array($gradeId, [38,39])) {
            $yrs13 = Cadresubject::where('category', '13_years_education')->orderBy('cadre')->get(['id','cadre']);
        }

        return response()->json([
            'success' => true,
            'basket1' => $basket1,
            'basket2' => $basket2,
            'basket3' => $basket3,
            'al'      => $al,
            'yrs13'   => $yrs13,
            'religion'=> $religion,
        ]);
    }



    // edit page (full fields)
    public function edit(Student $student)
    {
        // ensure institute user can't edit other institutes
        if ($student->institute_id != Auth::user()->institute_id) {
            abort(403);
        }

        $cadres = Cadresubject::orderBy('cadre')->get();

        // DS & GN (DS list all; GN filtered by student's ds)
        $dsdivisions = Dsdivision::orderBy('ds')->get();

        $gndivisions = Gndivision::where('dsdivision_id', $student->dsdivision_id ?? 0)
                        ->orderBy('gn')
                        ->get();

        // grades for rows using institute type
        $institute = Institute::find(Auth::user()->institute_id);
        $instType = $institute ? $institute->type : null;
        $gradesForRows = Grade::when($instType, function($q) use ($instType){
                $q->where('type','like', "%{$instType}%");
            })->orderBy('name')->get();

        $institutesForSelect = Institute::where('id','<',69)->orderBy('institute')->get();

        // --- NEW: prepare initial cadre option sets according to current student grade ---
        $gradeId = (int)($student->grade_id ?? 0);

        // always religion for cadre4
        $initialCadres4 = Cadresubject::where('category2', 'Religion')->orderBy('cadre')->get(['id','cadre']);

        $initialCadres1 = collect();
        $initialCadres2 = collect();
        $initialCadres3 = collect();

        if (in_array($gradeId, [10,11,12,13])) {
            $initialCadres1 = Cadresubject::where('category2', 'Basket 1')->orderBy('cadre')->get(['id','cadre']);
            $initialCadres2 = Cadresubject::where('category2', 'Basket 2')->orderBy('cadre')->get(['id','cadre']);
            $initialCadres3 = Cadresubject::where('category2', 'Basket 3')->orderBy('cadre')->get(['id','cadre']);
        } elseif ($gradeId >= 14 && $gradeId <= 37) {
            $initialCadres1 = $initialCadres2 = $initialCadres3 = Cadresubject::where('category2', 'A/L')->orderBy('cadre')->get(['id','cadre']);
        } elseif (in_array($gradeId, [38,39])) {
            // category is '13_years_education'
            $initialCadres1 = $initialCadres2 = $initialCadres3 = Cadresubject::where('category', '13_years_education')->orderBy('cadre')->get(['id','cadre']);
        } else {
            // For grades <6 or other grades where these do not apply, keep empty collections
            $initialCadres1 = collect();
            $initialCadres2 = collect();
            $initialCadres3 = collect();
        }

        return view('students.edit', compact(
            'student','cadres','dsdivisions','gndivisions','gradesForRows','institutesForSelect',
            'initialCadres1','initialCadres2','initialCadres3','initialCadres4'
        ));
    }



    public function gndsByDs($dsId)
    {
        $gnds = Gndivision::where('dsdivision_id', $dsId)->orderBy('gn')->get(['id','gn']);
        return response()->json(['success'=>true,'gnds'=>$gnds]);
    }

    // update full student
    public function update(Request $request, Student $student)
    {
        // ensure user is from same institute (or super_admin if you change policy)
        if ($student->institute_id != Auth::user()->institute_id) {
            abort(403);
        }

        $instituteId = Auth::user()->institute_id;

        // Basic field rules (we'll add admission/institute specific checks below)
        $rules = [
            'admission_number' => ['required','string','max:100'],
            'name' => 'required|string|max:255',
            'grade_id' => ['required','integer', Rule::exists('grades','id')],
            'status' => ['required', Rule::in(['Active', 'Droped out'])],
            'ews_color' => ['nullable', 'integer', Rule::in([1,2,3])],
            'cadresubject1_id' => 'nullable|integer|exists:cadresubjects,id',
            'cadresubject2_id' => 'nullable|integer|exists:cadresubjects,id',
            'cadresubject3_id' => 'nullable|integer|exists:cadresubjects,id',
            'cadresubject4_id' => 'nullable|integer|exists:cadresubjects,id',
            'dsdivision_id' => 'nullable|integer|exists:ds_divisions,id',
            'gndivision_id' => 'nullable|integer|exists:gn_divisions,id',
            'mobile' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'father_nic' => 'nullable|string|max:12',
            'mother_name' => 'nullable|string|max:255',
            // allow institute_id in edit form
            'institute_id' => ['nullable','integer','exists:institutes,id'],
            // force flags from client
            'force_admission' => ['nullable','boolean'],
            'force_institute' => ['nullable','boolean'],
        ];

        $data = $request->validate($rules);

        $targetInstituteId = $data['institute_id'] ?? $student->institute_id;

        // --- 1) Uniqueness check: admission_number must be unique within the target institute ---
        $admission = trim($data['admission_number']);
        $exists = Student::where('institute_id', $targetInstituteId)
                        ->where('admission_number', $admission)
                        ->where('id','!=',$student->id)
                        ->exists();
        if ($exists) {
            return redirect()->back()->withInput()->withErrors(['admission_number' => 'Admission number already exists in the selected institute.']);
        }

        // DS/GN consistency check
        if (!empty($data['gndivision_id']) && !empty($data['dsdivision_id'])) {
            $gn = Gndivision::find($data['gndivision_id']);
            if (!$gn || $gn->dsdivision_id != $data['dsdivision_id']) {
                return redirect()->back()->withInput()->withErrors(['gndivision_id' => 'Selected GN division does not belong to selected DS division.']);
            }
        }

        // --- 2) Census / prefix logic ---
        // get census of target institute and current institute
        $targetInstitute = Institute::find($targetInstituteId);
        $targetCensus = $targetInstitute ? (string)$targetInstitute->census : null;

        // extract prefix (before first underscore) or entire string if none
        $prefix = (strpos($admission, '_') !== false) ? explode('_', $admission, 2)[0] : $admission;
        $prefix = trim((string)$prefix);

        // if prefix matches the target institute census -> OK
        if ($targetCensus && $prefix === (string)$targetCensus) {
            // allowed
        } else {
            // check if prefix belongs to some other institute
            $otherInstitute = Institute::where('census', $prefix)->where('id', '!=', $targetInstituteId)->first();

            if ($otherInstitute) {
                // prefix indicates another school
                $forceAdmission = $request->boolean('force_admission');
                $forceInstitute = $request->boolean('force_institute');

                // If user is transferring the student to that same other institute (targetInstitute == otherInstitute),
                // allow only if force_institute true or user is super_admin
                if ($otherInstitute->id == $targetInstituteId) {
                    if (!$forceInstitute && Auth::user()->role !== 'super_admin') {
                        return redirect()->back()->withInput()->withErrors([
                            'institute_id' => 'You selected a different school (' . ($otherInstitute->institute ?? $otherInstitute->id) . '). Please confirm the transfer in the form (click OK when prompted) or contact admin.'
                        ]);
                    }
                    // allowed if force_institute true
                } else {
                    // prefix belongs to some other institute than the selected target
                    // If user provided force_admission, allow; else ask to confirm
                    if (!$forceAdmission && Auth::user()->role !== 'super_admin') {
                        return redirect()->back()->withInput()->withErrors([
                            'admission_number' => 'The census prefix indicates the student belongs to another school (' . ($otherInstitute->institute ?? $otherInstitute->id) . '). Please confirm if this is correct.'
                        ]);
                    }
                }
            } else {
                // prefix not found anywhere -> invalid census
                return redirect()->back()->withInput()->withErrors(['admission_number' => 'Wrong census number!. Please check']);
            }
        }

        // --- 3) Cadre-subjects business rules (server-side enforcement) ---
        $gradeId = (int) ($data['grade_id'] ?? 0);

        // helper to fetch subject and check category/category2
        $invalidCadreError = function($field, $msg){
            return redirect()->back()->withInput()->withErrors([$field => $msg]);
        };

        // ensure no duplication among 1/2/3
        $sub1 = $data['cadresubject1_id'] ?? null;
        $sub2 = $data['cadresubject2_id'] ?? null;
        $sub3 = $data['cadresubject3_id'] ?? null;

        $selected = array_filter([$sub1, $sub2, $sub3]);
        if (count($selected) !== count(array_unique($selected))) {
            return $invalidCadreError('cadresubject1_id', 'Cadre subjects 1, 2 and 3 must be different.');
        }

        // helper for checking DB subject category fields
        $checkCategory2 = function($subjectId, $expected) {
            if (!$subjectId) return true; // empty is allowed in some grades (but will be checked elsewhere)
            $s = Cadresubject::find($subjectId);
            if (!$s) return false;
            return isset($s->category2) && $s->category2 === $expected;
        };

        $checkCategory = function($subjectId, $expected) {
            if (!$subjectId) return true;
            $s = Cadresubject::find($subjectId);
            if (!$s) return false;
            return isset($s->category) && $s->category === $expected;
        };

        // RULE 3: if grade_id < 6 hide cadresubject1/2/3 -> they must be empty
        if ($gradeId < 6) {
            if (!empty($sub1) || !empty($sub2) || !empty($sub3)) {
                return $invalidCadreError('cadresubject1_id', 'Cadre subjects 1/2/3 are not applicable for this grade. Please remove them.');
            }
        }

        // NEW RULE: grades 6,7,8,9 -> all three use category2 'Basket 2'
        if (in_array($gradeId, [6,7,8,9])) {
            if ($sub1 && ! $checkCategory2($sub1, 'Basket 2')) {
                return redirect()->back()->withInput()->withErrors(['cadresubject1_id' => 'Cadre subject 1 must belong to Basket 2 for the selected grade.']);
            }
            if ($sub2 && ! $checkCategory2($sub2, 'Basket 2')) {
                return redirect()->back()->withInput()->withErrors(['cadresubject2_id' => 'Cadre subject 2 must belong to Basket 2 for the selected grade.']);
            }
            if ($sub3 && ! $checkCategory2($sub3, 'Basket 2')) {
                return redirect()->back()->withInput()->withErrors(['cadresubject3_id' => 'Cadre subject 3 must belong to Basket 2 for the selected grade.']);
            }
        }

        // RULE 1: if grade_id = 10,11,12,13 -> basket mapping
        if (in_array($gradeId, [10,11,12,13])) {
            if ($sub1 && ! $checkCategory2($sub1, 'Basket 1')) {
                return $invalidCadreError('cadresubject1_id', 'Cadre subject 1 must belong to Basket 1 for the selected grade.');
            }
            if ($sub2 && ! $checkCategory2($sub2, 'Basket 2')) {
                return $invalidCadreError('cadresubject2_id', 'Cadre subject 2 must belong to Basket 2 for the selected grade.');
            }
            if ($sub3 && ! $checkCategory2($sub3, 'Basket 3')) {
                return $invalidCadreError('cadresubject3_id', 'Cadre subject 3 must belong to Basket 3 for the selected grade.');
            }
        }

        // RULE 2: if grade between 14 and 37 inclusive -> all 1/2/3 must be category2 = 'A/L'
        if ($gradeId >= 14 && $gradeId <= 37) {
            foreach (['cadresubject1_id','cadresubject2_id','cadresubject3_id'] as $f) {
                $val = $data[$f] ?? null;
                if ($val && ! $checkCategory2($val, 'A/L')) {
                    return $invalidCadreError($f, 'For this grade the subject must be an A/L subject.');
                }
            }
        }

        // RULE 4: grade 38 or 39 -> category = '13_years_education'
        if (in_array($gradeId, [38,39])) {
            foreach (['cadresubject1_id','cadresubject2_id','cadresubject3_id'] as $f) {
                $val = $data[$f] ?? null;
                if ($val && ! $checkCategory($val, '13_years_education')) {
                    return $invalidCadreError($f, 'For this grade the subject must be from 13_years_education category.');
                }
            }
        }

        // RULE 5: cadresubject4_id always category2 -> 'Religion'
        $sub4 = $data['cadresubject4_id'] ?? null;
        if ($sub4 && ! $checkCategory2($sub4, 'Religion')) {
            return $invalidCadreError('cadresubject4_id', 'Cadre subject 4 must be a Religion subject.');
        }

        // --- All checks passed; build update payload ---
        $updateData = [
            'admission_number' => $admission,
            'name' => $data['name'],
            'grade_id' => $data['grade_id'],
            'status' => $data['status'],
            'ews_color' => $data['ews_color'] ?? null,
            'cadresubject1_id' => $data['cadresubject1_id'] ?? null,
            'cadresubject2_id' => $data['cadresubject2_id'] ?? null,
            'cadresubject3_id' => $data['cadresubject3_id'] ?? null,
            'cadresubject4_id' => $data['cadresubject4_id'] ?? null,
            'dsdivision_id' => $data['dsdivision_id'] ?? null,
            'gndivision_id' => $data['gndivision_id'] ?? null,
            'address' => $data['address'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'father_name' => $data['father_name'] ?? null,
            'father_nic' => $data['father_nic'] ?? null,
            'mother_name' => $data['mother_name'] ?? null,
            'updated_by' => Auth::id(),
        ];

        // ensure institute set to target (the form allowed institute_id)
        $updateData['institute_id'] = $targetInstituteId;

        // if ews_color present set ews_updated_by (use authenticated user's institute)
        if (isset($updateData['ews_color'])) {
            $updateData['ews_updated_by'] = Auth::user()->institute_id;
        }

        // perform update
        $student->update($updateData);

        return redirect()->route('students.index')->with('success','Student updated successfully');
    }


    // update only EWS via separate endpoint
    public function updateEwsColor(Request $request, Student $student)
    {
        if ($student->institute_id != Auth::user()->institute_id && Auth::user()->role !== 'super_admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate(['ews_color' => ['required','integer', Rule::in([1,2,3])]]);

        $student->ews_color = $request->ews_color;
        $student->ews_updated_by = Auth::user()->institute_id;
        $student->updated_by = Auth::id();
        $student->save();

        return response()->json(['success' => true, 'ews_color' => $student->ews_color]);
    }

    public function destroy(Student $student)
    {
        // only super_admin can delete
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Only super_admin can delete');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success','Student deleted');
    }

    // other controller methods (create, store, edit, update, destroy) remain as you already have them...
}
