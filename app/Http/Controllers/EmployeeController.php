<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeDummy;
use App\Models\User;
use App\Models\Designation;
use App\Models\Cadresubject;
use App\Models\DsDivision;
use App\Models\GnDivision;
use App\Models\EmpService;
use App\Models\Zone;
use App\Models\Degree;
use App\Models\DegSubject;
use App\Models\EmpDegreeSubject;
use App\Models\AppCategory;
use App\Models\Institute;
use App\Models\HighEduQualification;
use App\Models\TransMode;
use App\Exports\EmployeeExport;
use App\Models\ServiceHistory;
use App\Models\SalaryTeacher;
use App\Models\ProfQualification;
use App\Models\ProfQualificationInstitute;
use App\Models\EmpQualification;
use App\Models\EmpTeachSubject;
use Illuminate\Support\Facades\DB;
use Auth;
use DataTables;
use Session;
use Excel;
use App\Imports\SalaryImport;
use Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;



use Pnlinh\GoogleDistance\Facades\GoogleDistance;
// Google distance matrix
use TeamPickr\DistanceMatrix\DistanceMatrix;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:employee-list', ['only' => ['index','profile','edit']]);
         $this->middleware('permission:employee-create', ['only' => ['create','store']]);
         $this->middleware('permission:employee-edit', ['only' => ['update','dummy_store']]);
         $this->middleware('permission:employee-delete', ['only' => ['destroy']]);
    }
    

    public function index(Request $request)
    {
        $cadresubs = Cadresubject::orderBy('cadre')->get();
        $designations = Designation::orderBy('designation')->get();

        if (auth()->user()->hasRole('Sch_Admin')) {
            $userInstitute = auth()->user()->institute_id;

            $institutes = Institute::where(function($q) use ($userInstitute) {
                $q->where('id', $userInstitute);
            })
            ->orderBy('institute')
            ->get();
        } else {
            $institutes = Institute::orderBy('institute')->get();
        }

        if ($request->ajax()) {
            // get actual table name to avoid prefix problems (e.g. smgt_employees)
            $empTable = (new \App\Models\Employee)->getTable();

            // Build base query (do NOT force global orderBy here)
            $data = Employee::query()
                ->with(['empdummy', 'servicetransfer.institute', 'institute', 'cadresubject', 'designation']);

            /*
            * If DataTables asked to order, detect if the ordered column is 'updated_at'.
            * DataTables sends: order[0][column] and columns[index][data]
            */
            if ($request->has('order') && is_array($request->get('order'))) {
                $order = $request->get('order')[0] ?? null;
                $columns = $request->get('columns', []);
                if ($order && isset($columns[$order['column']])) {
                    $colData = $columns[$order['column']]['data'] ?? null;
                    $dir = strtolower($order['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

                    // If the client requested sorting by updated_at, apply real-table orderBy
                    if ($colData === 'updated_at' || $colData === "{$empTable}.updated_at" || $colData === 'employees.updated_at') {
                        $data->orderBy("{$empTable}.updated_at", $dir);
                    }
                }
            }

            return DataTables::eloquent($data)
                ->addIndexColumn()

                ->filter(function ($instance) use ($request, $empTable) {

                    // 1) School Admin auto-filter (applies first)
                    if (auth()->user()->hasRole('Sch_Admin')) {
                        $userInstitute = auth()->user()->institute_id;

                        $instance->where(function($q) use ($userInstitute) {
                            $q->where('institute_id', $userInstitute)
                            ->orWhere('current_working_station', $userInstitute);
                        });
                    }

                    // 2) Institute filter (applies as AND)
                    if ($request->filled('institute')) {
                        $instance->where('institute_id', $request->get('institute'));
                    }

                    // 3) Designation filter
                    if ($request->filled('designation')) {
                        $instance->where('designation_id', $request->get('designation'));
                    }

                    // 4) Cadre filter
                    if ($request->filled('cadre')) {
                        $instance->where('cadresubject_id', $request->get('cadre'));
                    }

                    // 5) Transfer validation
                    if ($request->filled('transferValidate')) {
                        $instance->whereHas('servicetransfer', function($w) {
                            $w->whereColumn('service_transfers.institute_id', '!=', 'service_transfers.transfer_to');
                        });
                    }

                    // 6) Status filter
                    if ($request->filled('status')) {
                        $instance->where('status', $request->get('status'));
                    }

                    // 7) Search filter (keeps ORs inside a group)
                    if ($request->filled('search')) {
                        $search = $request->get('search');
                        $instance->where(function($w) use ($search) {
                            $w->where('name_with_initial_e', 'LIKE', "%{$search}%")
                            ->orWhere('name_denoted_by_initial_e', 'LIKE', "%{$search}%")
                            ->orWhere('nic', 'LIKE', "%{$search}%")
                            ->orWhere('empno', 'LIKE', "%{$search}%")
                            ->orWhere('id', 'LIKE', "%{$search}%");
                        });
                    }

                    // 8) Attachment filter 
                    if ($request->attachment == 1) {
                        $instance->whereColumn('institute_id', '!=', 'current_working_station');
                    }
                })

                ->editColumn('namewithinitial', function(Employee $employee) {
                    if (!empty($employee->empdummy) && $employee->empdummy->name_with_initial_e != $employee->name_with_initial_e) {
                        $name = $employee->title . '.' . $employee->name_with_initial_e
                            . ' (' . $employee->empdummy->name_with_initial_e . ')'; 
                    } else {
                        $name = $employee->title . '.' . $employee->name_with_initial_e
                            . ' (' . $employee->name_denoted_by_initial_e . ')';
                    }

                    if (!empty($employee->empdummy)) {
                        $icon = " <i class='fas fa-exclamation-circle text-warning' title='Pending Dummy Record'></i>";
                    } else {
                        $icon = "";
                    }

                    return $name . $icon;
                })

                ->addColumn('institute', function(Employee $employee) {
                    $instName = optional($employee->institute)->institute ?? '-';
                    if (!empty($employee->servicetransfer) && optional($employee->servicetransfer->institute)->institute != $instName) {
                        return $instName . " (Transfered to: " . optional($employee->servicetransfer->institute)->institute
                            . "," . $employee->servicetransfer->process_id . ")";
                    }
                    return $instName;
                })

                ->addColumn('working_station', function(Employee $employee) {
                    return optional($employee->workingStation)->institute ?? '-';
                })

                ->addColumn('cadresubject', function(Employee $employee) {
                    $cadre = optional($employee->cadresubject)->cadre ?? '-';
                    if (!empty($employee->empdummy) && optional($employee->empdummy->cadresubject)->cadre != $cadre) {
                        return $cadre . ' (' . optional($employee->empdummy->cadresubject)->cadre . ')';
                    }
                    return $cadre;
                })

                ->addColumn('designation', function(Employee $employee) {
                    $desig = optional($employee->designation)->designation ?? '-';
                    if (!empty($employee->empdummy) && optional($employee->empdummy->designation)->designation != $desig) {
                        return $desig . ' (' . optional($employee->empdummy->designation)->designation . ')';
                    }
                    return $desig;
                })

                ->addColumn('status', function(Employee $employee) {
                    if (!empty($employee->empdummy) && $employee->empdummy->status != $employee->status) {
                        return $employee->status . ' (' . $employee->empdummy->status . ')';
                    }
                    return $employee->status;
                })

                ->addColumn('updated_at', function(Employee $employee) {
                    return $employee->updated_at ? $employee->updated_at->format('Y-m-d H:i') : '-';
                })

                ->addColumn('action', function($row) {
                    $show = route('employee.show', $row->id);
                    $edit = route('employee.edit', $row->id);
                    $delete = route('employee.destroy', $row->id);
                    $user = auth()->user();
                    $btn = '';

                    if ($user->can('employee-list')) {
                        $btn .= "<a href='{$show}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='print' data-placement='bottom'><i class='fa fa-print'></i></a>";
                        $btn .= "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='view' data-placement='bottom'><i class='fas fa-eye'></i></a>";
                    }
                    if ($user->can('employee-delete')) {
                        $btn .= "<a href='{$delete}' onclick='return confirm(`Are you sure want to delete this record?`)' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                    }

                    return $btn;
                })

                ->rawColumns(['action','institute','namewithinitial'])
                ->make(true);
        }

        return view('human_resource.index', compact('cadresubs','institutes','designations'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function transfer(){
        return view('human_resource.transfer');
    }
    
    public function create()
    {
        $employee = null;
        $employeeDummy = null;
        $ds = DsDivision::get();
        $gn = GnDivision::get();
        $zones = Zone::get();
        $transmodes = TransMode::get();
        $services = EmpService::get();   
        $designations = Designation::orderBy('designation')->get(); 
        $institutes = Institute::orderBy('institute')->get();  
        $highqualifs = HighEduQualification::get(); 
        $degrees = Degree::get();
        $degreesubs = DegSubject::get();
        $appcats = AppCategory::get();
        $cadresubs = Cadresubject::orderBy('cadre')->get(); 

        return view('human_resource.createOrUpdate', compact('employee','employeeDummy','ds','gn','zones','transmodes','services','designations','institutes',
        'highqualifs','degrees','degreesubs','appcats','cadresubs'));
   
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // your validations
        ]);

        if ($request->input('desigcatg') != 'SAC') {
            $insid = Institute::where('institute', $request->input('desigcatg'))
                        ->value('id');
            $institute1id = $insid;
        } else {
            $institute1id = $request->input('institute_id');
        }

        // ✅ If current_working_station is empty
        $currentWorkingStation = $request->input('current_working_station') 
                                    ?: $request->input('institute_id');

        if (!Employee::where('nic', $request->input('nic'))->exists()) {

            $status = Employee::create(
                $request->all() + [
                    'institute1_id' => $institute1id,
                    'current_working_station' => $currentWorkingStation
                ]
            );

            if ($status) {
                session()->flash('success', 'Successfully added');
            } else {
                session()->flash('error', 'Error occurred while inserting');
            }

            return redirect()->route('employee.index');
        } else {
            session()->flash('error', 'Employee already exist!');
            return redirect()->back();
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $employees = Employee::where('institute_id', '=', $request->txtid)->whereIn('designation_id', $request->des)->where('status','Active')->with('servicehistory')->get();
        return view('human_resource.show', compact('employees'));
    }

    public function show(Request $request, $id)
    {
        $employees = Employee::where('id',$id)->with('servicehistory')->get();
        return view('human_resource.show', compact('employees'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $ds = DsDivision::get();
        $gn = GnDivision::get();
        $zones = Zone::get();
        $transmodes = TransMode::get();
        $services = EmpService::get();   
        $designations = Designation::orderBy('designation')->get(); 
        $institutes = Institute::orderBy('institute')->get();  
        $highqualifs = HighEduQualification::get(); 
        $degrees = Degree::get();
        $degreesubs = DegSubject::get();
        $appcats = AppCategory::get();
        $cadresubs = Cadresubject::orderBy('cadre')->get(); 
        $teachsubs = Cadresubject::whereNotIn('category', [
                        'office_academic',
                        'school_administration',
                        'others',
                        'school_non_academic',
                        'office_non_academic'
                    ])->orderBy('cadre')->get();
        $employee = Employee::find($id);
        $employeeDummy = EmployeeDummy::where('employee_id', $id)->first(); 
        $empDegreeSubjects = EmpDegreeSubject::where('employee_id',$id)->get();
        $qualifications = EmpQualification::where('employee_id',$id)->get();
        $qualifData = ProfQualification::query()->orderBy('name')->get();
        $instituteData = ProfQualificationInstitute::query()->orderBy('name')->get();
        $teachsubjects = EmpTeachSubject::where('employee_id',$id)->get();
  
    return view('human_resource.createOrUpdate', compact('employee','employeeDummy','ds','gn','zones','transmodes','services','designations','institutes',
    'highqualifs','degrees','degreesubs','empDegreeSubjects','appcats','cadresubs','teachsubs','qualifications','qualifData','instituteData','teachsubjects'));
    }

    public function update(Request $request, $id)
    {
        // mapping dummy => employee attributes
        $updatable = [
            'dummy_nicnew' => 'nicnew',
            'dummy_title' => 'title',
            'dummy_surname' => 'name_with_initial_e', 
            'dummy_fullname' => 'name_denoted_by_initial_e',
            'dummy_surname' => 'name_with_initial_t', 
            'dummy_fullname' => 'name_denoted_by_initial_t',
            'dummy_dob' => 'dob',
            'dummy_gender' => 'gender',
            'dummy_civilstatus' => 'civilstatus',
            'dummy_ethinicity' => 'ethinicity',
            'dummy_religion' => 'religion',
            'dummy_peraddress' => 'peraddress',
            'dummy_tmpaddress' => 'tmpaddress',
            'dummy_dsdivision_id' => 'dsdivision_id',
            'dummy_gndivision_id' => 'gndivision_id',
            'dummy_zone_id' => 'zone_id',
            'dummy_transmode_id' => 'transmode_id',
            'dummy_distores' => 'distores',
            'dummy_mobile' => 'mobile',
            'dummy_whatsapp' => 'whatsapp',
            'dummy_fixedphone' => 'fixedphone',
            'dummy_email' => 'email',
            'dummy_empservice_id' => 'empservice_id',
            'dummy_grade' => 'grade',
            'dummy_dtyasmfapp' => 'dtyasmfapp',
            'dummy_dtyasmcser' => 'dtyasmcser',
            'dummy_designation_id' => 'designation_id',
            'dummy_institute_id' => 'institute_id',
            'dummy_current_working_station' => 'current_working_station',
            'dummy_dtyasmprins' => 'dtyasmprins',
            'dummy_highqualification_id' => 'highqualification_id',
            'dummy_degree_id' => 'degree_id',
            'dummy_degtype' => 'degtype',
            'dummy_degsubject1_id' => 'degsubject1_id',
            'dummy_degsubject2_id' => 'degsubject2_id',
            'dummy_degsubject3_id' => 'degsubject3_id',
            'dummy_appsubject' => 'appsubject',
            'dummy_appcategory_id' => 'appcategory_id',
            'dummy_cadresubject_id' => 'cadresubject_id',
            'dummy_trained' => 'trained',
            'dummy_remark' => 'remark',
        ];

        // Detect presence of employee_dummy for this employee and whether modal update keys present
        $employeeDummy = EmployeeDummy::where('employee_id', $id)->first();
        $isDummySubmission = $employeeDummy && $request->has('update');

        // Conditional validation:
        // If it's not a dummy modal submission, perform full validation. If it is dummy or combined,
        // validate only fields present (so modal-only doesn't fail).
        if (!$isDummySubmission) {
            $this->validate($request, [
                'empno' => 'required',
                'nic' => 'required|unique:employees,nic,'.$id,
                'name_with_initial_e' => 'string|required|max:30',
                'cadresubject_id' => 'required',
                'status' => 'required',
                'email' => 'nullable|email',
            ]);
        } else {
            // optional: add minimal validation for present dummy fields if desired
        }

        DB::beginTransaction();

        try {
            $employee = Employee::findOrFail($id);

            // We'll collect whether any changes applied
            $applied = false;

            // ---------- 1) If there is a dummy and user provided 'update' (modal) => apply checked dummy fields first ----------
            if ($employeeDummy && $request->has('update')) {
                $checked = $request->input('update', []); // associative array like ['dummy_surname' => '1']

                foreach ($updatable as $dummyKey => $empField) {
                    if (array_key_exists($dummyKey, $checked)) {
                        // take the pending value from request
                        $value = $request->input($dummyKey, null);

                        // convert numeric IDs to int if needed (example)
                        if (in_array($dummyKey, [
                            'dummy_dsdivision_id','dummy_gndivision_id','dummy_institute_id',
                            'dummy_designation_id','dummy_empservice_id','dummy_degree_id',
                            'dummy_degsubject1_id','dummy_degsubject2_id','dummy_degsubject3_id'
                        ])) {
                            $value = ($value === null || $value === '') ? null : (int) $value;
                        }

                        $employee->{$empField} = $value;
                        $applied = true;

                        Log::info("DBG: applied dummy field", ['dummyKey' => $dummyKey, 'empField' => $empField, 'value' => $value]);
                    }
                }

                // after applying dummy values, delete the dummy record(s) (your existing behavior)
                if ($applied) {
                    $employee->save();
                    EmployeeDummy::where('employee_id', $id)->delete();
                }
            }

            // ---------- 2) Now apply any other normal edit-form inputs present in the request ----------
            // Build exclude list so we do not accidentally apply temporary/form-only fields
            $exclude = array_merge(
                ['_token','_method','desigcatg','qualifications','course_name','degree_subjects','subject_name','institution','duration','periods','teachsubject_id','teachsubjects','update'],
                array_keys($updatable)
            );

            // $other contains all non-dummy inputs submitted
            $other = $request->except($exclude);

            // Optional behaviour: give priority to dummy-applied fields (i.e. do not overwrite them
            // with form inputs). If you want form inputs to override dummy values, comment out the block below.
            if (!empty($request->input('update', []))) {
                foreach ($request->input('update', []) as $dummyKey => $v) {
                    if (isset($updatable[$dummyKey])) {
                        $fieldName = $updatable[$dummyKey];
                        // remove from $other to preserve dummy value
                        if (array_key_exists($fieldName, $other)) {
                            unset($other[$fieldName]);
                        }
                    }
                }
            }

            // Validate only fields present in $other (so combined submissions validate required fields if present)
            $dynamicRules = [];
            if (array_key_exists('empno', $other)) $dynamicRules['empno'] = 'required';
            if (array_key_exists('nic', $other)) $dynamicRules['nic'] = 'required|unique:employees,nic,'.$id;
            if (array_key_exists('name_with_initial_e', $other)) $dynamicRules['name_with_initial_e'] = 'string|required|max:30';
            if (array_key_exists('cadresubject_id', $other)) $dynamicRules['cadresubject_id'] = 'required';
            if (array_key_exists('status', $other)) $dynamicRules['status'] = 'required';
            if (array_key_exists('email', $other)) $dynamicRules['email'] = 'nullable|email';

            if (!empty($dynamicRules)) {
                $request->validate($dynamicRules);
            }

            // Apply remaining $other fields to model
            if (!empty($other)) {
                foreach ($other as $k => $v) {
                    // If input names are same as DB attr, set directly
                    $employee->{$k} = $v;
                    $applied = true;
                }
                $employee->save();
            }

            // ---------- 3) Update institute1_id logic (unchanged) ----------
            if ($request->filled('desigcatg') && $request->input('desigcatg') != 'SAC') {
                $insid = Institute::where('institute', $request->input('desigcatg'))->value('id');
                $employee->institute1_id = $insid;
            } elseif ($request->has('institute_id')) {
                $employee->institute1_id = $request->input('institute_id');
            }

            // ✅ If current_working_station empty → use institute_id
            if ($request->has('current_working_station')) {
                $employee->current_working_station =
                    $request->input('current_working_station')
                    ?: $request->input('institute_id');
            }

            $employee->save();

            // ================= DEGREE SUBJECTS =================
            // Update existing
            if ($request->input('degree_subjects')) {
                foreach ($request->input('degree_subjects') as $subject) {

                    if (!isset($subject['id'])) continue;

                    $record = EmpDegreeSubject::find($subject['id']);

                    if ($record) {
                        $record->subject_name = $subject['subject_name'] ?? null;
                        $record->save();
                    }
                }
            }



            // Insert new
            if ($request->get('subject_name')) {
                foreach ($request->subject_name as $val) {
                    if (trim($val) == "") continue;
                    EmpDegreeSubject::create([
                        'subject_name' => $val,
                        'employee_id' => $id,
                    ]);
                }
            }


            // ---------- 4) Qualifications handling (same as your original logic) ----------
            // Update existing
            if ($request->input('qualifications')) {
                foreach ($request->input('qualifications') as $q) {
                    $record = EmpQualification::find($q['id']);
                    if ($record) {
                        $record->course_name = $q['course_name'];
                        $record->institution = $q['institution'];
                        $record->duration = $q['duration'];
                        $record->save();
                    }
                }
            }
            // Insert New
            if ($request->get('course_name')) {
                foreach ($request->get('course_name') as $i => $val) {
                    if (trim($val) == "") continue;
                    EmpQualification::create([
                        'course_name' => $request->course_name[$i],
                        'institution' => $request->institution[$i],
                        'duration' => $request->duration[$i],
                        'employee_id' => $id,
                    ]);
                }
            }

            // ---------- 5) Teaching subjects handling (same as original) ----------
            // Update existing
            if ($request->input('teachsubjects')) {
                foreach ($request->input('teachsubjects') as $ts) {
                    $rec = EmpTeachSubject::find($ts['id']);
                    if ($rec) {
                        $rec->cadresubject_id = $ts['teachsubject_id'];
                        $rec->periods = $ts['periods'];
                        $rec->save();
                    }
                }
            }

            // Insert New
            if ($request->get('teachsubject_id')) {
                foreach ($request->teachsubject_id as $i => $val) {
                    if (!$val) continue;
                    EmpTeachSubject::create([
                        'cadresubject_id' => $val,
                        'periods' => $request->periods[$i] ?? null,
                        'employee_id' => $id,
                    ]);
                }
            }

            DB::commit();

            // ---------- 6) Flash message ----------
            if ($applied) {
                session()->flash('success', 'Successfully updated');
            } else {
                // No changes applied (e.g., user opened modal and didn't check anything)
                session()->flash('error', $isDummySubmission ? 'No dummy fields selected' : 'No changes to update');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DBG update exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Error: '.$e->getMessage());
        }

        if ($isDummySubmission) {
            return redirect()->route('employee.dummy_index');
        } else {
            return redirect()->route('employee.index');
        }
    }

    public function ignoreDummy($id)
    {
        try {
            $deleted = EmployeeDummy::where('employee_id', $id)->delete();

            if ($deleted) {
                // return JSON for AJAX; front-end will close modal and update UI
                return response()->json(['success' => 'Pending changes ignored and removed.']);
            }

            // nothing to delete
            return response()->json(['warning' => 'No pending changes found.'], 404);
        } catch (\Exception $e) {
            \Log::error("Error ignoring dummy for employee {$id}: " . $e->getMessage());
            return response()->json(['error' => 'Unable to ignore pending changes.'], 500);
        }
    }

    public function photoupdate(Request $request, $id){
        
        $this->validate($request,
        [
            'file' => 'required|mimes:jpeg,jpg,png|max:10000',
        ]);
        
        // Update profile photo
        $employee = Employee::findOrFail($id);

        // delete if file exist
        if(\File::exists(base_path('vfiles/profileimg/').$id."jpg")){
             unlink(base_path('vfiles/profileimg/').$id."jpg");
        }
        
        if($request->file()) {
            $fileName = $id.".jpg";
            $filePath = $request->file('file')->storeAs('vfiles/profileimg/', $fileName, 'base');
            $employee->photo = $fileName;
        }

        $status = $employee->save();
        if($status){
            request()->session()->flash('success','Successfully updated');
            return redirect()->back();
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        
    }
    public function appendview(){
        return view('human_resource.appendclk');
    }

    public function updateinstitute1(){
        $employees = Employee::with('designation')->get();
        foreach($employees as $employee){
            if($employee->designation->catg == 'SNAC'){
                $employee->institute1_id = 74;
            } else if($employee->designation->catg == 'ONAC'){
                $employee->institute1_id = 73;
            } else if($employee->designation->catg == 'ONACM'){
                $employee->institute1_id = 72;
            } else if($employee->designation->catg == 'SPC'){
                $employee->institute1_id = 71;
            } else if($employee->designation->catg == 'TC'){
                $employee->institute1_id = 70;
            } else if($employee->designation->catg == 'OAC'){
                $employee->institute1_id = 69;
            } else {
                $employee->institute1_id = $employee->institute_id;
            }
            $status = $employee->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete=Employee::findorFail($id);
        $status=$delete->delete();
        if($status){
            request()->session()->flash('success','Employee Successfully deleted');
        }
        else{
            request()->session()->flash('error','There is an error while deleting employee');
        }
        return redirect()->route('employee.index');
        // return $id;
    }
    
    
    public function getdesigcatg($id)
     {
        $data = Designation::where('id','=',$id)->first();
        
        return response()->json(['catg' => $data->catg ]);
     }
     
    public function getMobile(Request $req){
        $eid = $req->empid;
        $data = User::all()->whereIn('id',$eid)->pluck('phone');
        return $data;
      }
    
    public function autocompleteSearch(Request $request)
    {
        $search = $request->search;

        $datas = Employee::LastSync()->where('nic', 'like', '%' .$search . '%')->with('institute1')->get();
        
        $dataModified = array();
        foreach ($datas as $data)
            {
                $dataModified[] = array('value'=>$data->id,
                                        'label'=>$data->nic,
                                        'fullname'=>$data->namewithinitial,
                                        'designation'=>$data->designation->designation,
                                        'insid'=>$data->institute_id,
                                        'institute'=>$data->institute->institute,
                                        'pfclerk' => (!empty($data->institute1->pfclerk->name) ? $data->institute1->pfclerk->name : 'undefined'),
                                        'pfclerk_id' => (!empty($data->institute1->pfclerk_id) ? $data->institute1->pfclerk_id : 'undefined'),
                                        'mobile' => $data->mobile
                                    );
            
            }
        return response()->json($dataModified);
    }
    
    public function getdistrance(Request $reqest)
    {
        $gnid = $reqest->gnid;
        $insid = $reqest->insid;
        
        $gnloc = GnDivision::where('id','=', $gnid)->pluck('gpslocation');
        $insloc = Institute::where('id','=', $insid)->pluck('gpslocation');
        
        $origin = $gnloc; 
        $destination = $insloc;

        $license = new StandardLicense('AIzaSyAHZUq9uPXq56Baa1yGZyUlmZ6WxinyFGQ');
    
        $response = DistanceMatrix::license($license)
        ->addOrigin($origin)
        ->addDestination($destination)
        ->request();
       
        $rows = $response->rows();
        $elements = $rows[0]->elements();
        $element = $elements[0];
    
        $distance = $element->distance();
        $distanceText = $element->distanceText();
        $duration = $element->duration();
        $durationText = $element->durationText();
        $durationInTraffic = $element->durationInTraffic();
        $durationInTrafficText = $element->durationInTrafficText();
    
        
        return response()->json(['distance' => $distanceText, 'duration' =>$durationText]);
    }
    
    public function getservicehistory(Request $request)
    {
        $empnic = $request->empnic;
        
        $srhistories = ServiceHistory::where('nic','=',$empnic)->get();
        
        return response()->json($srhistories);
    }
    
    public function export()
      {
        $prx = DB::getTablePrefix();
        $employees = DB::table("employees as e")
                    ->leftJoin('gn_divisions', function($join) {
                      $join->on('e.gndivision_id', '=', 'gn_divisions.id');
                    })->leftJoin('ds_divisions', function($join) {
                      $join->on('e.dsdivision_id', '=', 'ds_divisions.id');
                    })->leftJoin('zones', function($join) {
                      $join->on('e.zone_id', '=', 'zones.id');
                    })->leftJoin('institutes', function($join) {
                      $join->on('e.institute_id', '=', 'institutes.id');
                    })->leftJoin('emp_services', function($join) {
                      $join->on('e.empservice_id', '=', 'emp_services.id');
                    })->leftJoin('designations', function($join) {
                      $join->on('e.designation_id', '=', 'designations.id');
                    })->leftJoin('cadresubjects', function($join) {
                      $join->on('e.cadresubject_id', '=', 'cadresubjects.id');
                    })->leftJoin('cadresubjects as teasub1', function($join) {
                      $join->on('e.cadresubject1_id', '=', 'teasub1.id');
                    })->leftJoin('cadresubjects as teasub2', function($join) {
                      $join->on('e.cadresubject2_id', '=', 'teasub2.id');
                    })->leftJoin('cadresubjects as teasub3', function($join) {
                      $join->on('e.cadresubject3_id', '=', 'teasub3.id');
                    })->leftJoin('high_qualifications', function($join) {
                      $join->on('e.highqualification_id', '=', 'high_qualifications.id');
                    })->leftJoin('deg_institutes', function($join) {
                      $join->on('e.deginstitute_id', '=', 'deg_institutes.id');
                    })->leftJoin('degrees', function($join) {
                      $join->on('e.degree_id', '=', 'degrees.id');
                    })->leftJoin('deg_subjects as degsub1', function($join) {
                      $join->on('e.degsubject1_id', '=', 'degsub1.id');
                    })->leftJoin('deg_subjects as degsub2', function($join) {
                      $join->on('e.degsubject2_id', '=', 'degsub2.id');
                    })->leftJoin('deg_subjects as degsub3', function($join) {
                      $join->on('e.degsubject3_id', '=', 'degsub3.id');
                    })->leftJoin('app_categories', function($join) {
                      $join->on('e.appcategory_id', '=', 'app_categories.id');
                    })->select('e.id','e.empno','e.nic','e.nicnew','e.nic','e.title','e.name_with_initial_e','e.name_denoted_by_initial_e','e.name_with_initial_t','e.name_denoted_by_initial_t','e.dob','e.ethinicity','e.religion','e.civilstatus',
                    'e.gender','gn_divisions.gn', 'ds_divisions.ds','zones.zone','e.peraddress','e.tmpaddress','e.fixedphone','e.mobile','e.distores','institutes.institute',
		            'institutes.census','e.dtyasmprins','e.dtyasmfapp as duty assumption date','emp_services.service','e.dtyasmcser as duty_assump_pre_service','e.grade',
                    'app_categories.appcat','appsubject','designations.designation','cadresubjects.cadre as Cadre Subject','teasub1.cadre as Teaching Subject1',
		            'teasub2.cadre as Teaching Subject2','teasub3.cadre as Teaching Subject3','high_qualifications.qualif','deg_institutes.eduinsti',
		            'degrees.degree','degsub1.degreesub','degsub2.degreesub','e.trained','degtype','degsub3.degreesub','e.status','e.remark')->get();
                    
                
                 
                
          foreach($employees as $object)
          {
             $list[] =  (array) $object;
          }
          $data = collect($list);
          
         
          $excel = new EmployeeExport($data);

          return Excel::download($excel,'employees.xls');
        
      }
      
    public function salaryimportview(){
        return view('human_resource.salaryImport');
    }
    public function salaryimport(Request $request){
        if($request->hasFile('document')){
            $files = $request->file('document');
            if (preg_match_all("/teacher/i",$files[0]->getClientOriginalName())){
                 $importdata = $files[0];
            } elseif (preg_match_all("/teacher/i",$files[1]->getClientOriginalName())){
                $importdata = $files[1];  
            } else {
                request()->session()->flash('error','Could not found excel file with name teachers. Please check your file name');
                return redirect()->back();
            }
            $delete = SalaryTeacher::truncate();
            if($delete){
                $data = Excel::import(new SalaryImport, $importdata);
            } else {
               request()->session()->flash('error','error deleting data insalary table. Contact your system admin.');
                return redirect()->back(); 
            }
            if(!empty($data)){
                if(!empty($data)){ //to add confirm send mail variable * pending *
                    $data = array('name'=>"BWZEO");
                    $status = Mail::send(['text'=>'mail.test_mail'], $data, function($message) use($files ){
                        $message->to('yshajeevan@gmail.com', 'Tutorials Point')->subject('Salary File');
                        $message->from('baw@edudept.ep.gov.lk','BWZEO');
                        foreach($files as $file){
                            $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(), 
                                'mime' => $file->getMimeType()
                            ]);
                        }
                     });
                    request()->session()->flash('success','File successfully sent!');
                    return redirect()->route('crosscheckcheck.salary'); 
                } else{
                    request()->session()->flash('success','Salary table updated without sending mail to PD office.');
                    return redirect()->route('crosscheckcheck.salary');
                }
                 
            } else {
                request()->session()->flash('error','error importing salary file');
                return redirect()->back();
            }
        } 
    }
    
    public function checkwithsalary(){
        $items = DB::table('employees')->select('nic')->where('status','Active')->get();
        if(sizeof($items)){
            foreach($items as $item) {
                $smgt[] = $item->nic;
            }
        } else{
            $smgt = [];
        }
        $notinsmgt = DB::table('salary_teachers')->select('name','nic','institutes.institute')->join('institutes', 'institutes.salaryinst_id', '=', 'salary_teachers.institute')->where('status',1)->whereNotIn('nic', $smgt)->get();
        
        $notinsalary = DB::table('employees As e')->where('e.status','=','Active')->whereIn('e.designation_id',[7,8,9,13,16,17,18,19])->select(DB::raw('CONCAT(title,".", name_with_initial_e) AS name'),'e.nic', 'i.institute As institute')
        ->leftJoin('salary_teachers As s', function($join) {
            $join->on('e.nic', '=', 's.nic');
        })->whereNull('s.nic')->join('institutes As i', function($join) {
            $join->on('i.id', '=', 'e.institute_id');
        })->get();
        
        // return $notinsalary = DB::table('employees')->where('employees.status','=','Active')->whereIn('employees.designation_id',[7,8,9,13,16,17,18,19])->select(DB::raw('CONCAT(title,".", initial,".", surname) AS name'),'employees.nic', 'institutes.institute As institute')
        //         ->leftJoin('salary_teachers', 'salary_teachers.nic', '=', 'employees.nic')
        //         ->join('institutes', 'institutes.salaryinst_id', '=', 'salary_teachers.institute')
        //         ->get();
        
        return view('human_resource.checkSalary', compact('notinsmgt','notinsalary'));
    }
    
    public function checkwithnemis(){
        $items = DB::table('employees')->select('nic')->where('status','Active')->get();
        if(sizeof($items)){
            foreach($items as $item) {
                $smgt[] = $item->nic;
            }
        } else{
            $smgt = [];
        }
        $institute = ['BATTICALOA WEST DUMMY SCHOOL','BATTICALOA WEST ZONAL EDUCATION OFFICE'];
        $notinsmgt = DB::table('nemis')->select('name','nic','institute')->whereNotIn('institute',$institute)->whereNotIn('nic', $smgt)->orderBy('institute')->get();
        
        $notinnemis = DB::table('employees As e')->where('e.status','=','Active')->whereIn('e.designation_id',[7,8,9,13])->select(DB::raw('CONCAT(title,".", name_with_initial_e) AS name'),'e.nic', 'e.institute_id As institute')
       ->leftJoin('nemis As n', function($join) {
            $join->on('e.nic', '=', 'n.nic');
        })->whereNull('n.nic')->get();
        
        
        return view('human_resource.checkNemis', compact('notinsmgt','notinnemis'));
    }
    
    public function destroy_qualification($id){

        EmpQualification::find($id)->delete();
        
        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }
    public function dummy_index(Request $request)
    {
        $cadresubs = Cadresubject::orderBy('cadre')->get(); 
        $designations = Designation::orderBy('designation')->get();
        $uname = Auth::user()->roles->pluck('name')->implode(', ');
        $institutes = Institute::orderBy('institute')->get();

        if ($request->ajax()) {
            $data = EmployeeDummy::orderBy('created_at', 'desc');

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('institute'))) {
                            $instance->where(function($w) use($request){
                                $institute = $request->get('institute'); 
                                $w->orWhere('institute_id', '=', $institute);
                            });   
                        }
                        if (!empty($request->get('designation'))) {
                            $instance->where(function($w) use($request){
                                $designation = $request->get('designation'); 
                                $w->orWhere('designation_id', '=', $designation);
                            });   
                        }
                         if (!empty($request->get('cadre'))) {
                            $instance->where(function($w) use($request){
                                $cadre = $request->get('cadre'); 
                                $w->orWhere('cadresubject_id', '=', $cadre);
                            });  
                        }
        
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('name_with_initial_e', 'LIKE', "%$search%")
                                ->orWhere('name_denoted_by_initial_e', 'LIKE', "%$search%")
                                ->orWhere('nic', 'LIKE', "%$search%")
                                ->orWhere('empno', 'LIKE', "%$search%")
                                ->orWhere('id', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->editColumn('namewithinitial', function(EmployeeDummy $employee) {
                            return $employee->title.'.'.$employee->name_with_initial_e;
                    })
                    ->addColumn('institute', function(EmployeeDummy $employee)
                    {
                            return $employee->institute->institute;
                    })
                    ->addColumn('cadresubject', function(EmployeeDummy $employee)
                    {
                        return $employee->cadresubject->cadre;
                    })
                    ->addColumn('designation', function(EmployeeDummy $employee)
                    {
                        return $employee->designation->designation;
                    }) 
                    ->addColumn('action', function($row){
                        $edit =  route('employee.edit',$row->employee_id);
                        $delete =  route('employee.dummy_destroy',$row->employee_id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('employee-list')) {
                            $btn = $btn."<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('employee-delete')) {
                            $btn = $btn."<button class='btn btn-xs btn-sm btn-danger btn-delete' data-remote='{$delete}'><i class='fas fa-trash'></i></button>";
                        }
                        return $btn;
                    })
                    ->rawColumns(['action','institute'])
                    ->make(true);
        }
        return view('human_resource.dummy_index', compact('cadresubs','institutes','designations'));
        
    }
    
    public function dummy_store(Request $request, $id)
{
    $this->validate($request, [
        // add rules you need
    ]);

    // Resolve institute1_id
    if ($request->input('desigcatg') != 'SAC') {
        $insid = Institute::select('id')
            ->where('institute', '=', $request->input('desigcatg'))
            ->value('id');
        $institute1id = $insid;
    } else {
        $institute1id = $request->input('institute_id');
    }

    $excluded = [
        '_token','desigcatg','qualifications','course_name','institution','duration',
        'periods','teachsubject_id','teachsubjects'
    ];

    DB::beginTransaction();

    try {
        $employee = Employee::findOrFail($id);

        // Create if not exists
        if (! EmployeeDummy::where('employee_id', $id)->exists()) {
            $input = $request->except($excluded);

            $mapped = [];
            foreach ($input as $key => $value) {
                if (strpos($key, 'dummy_') === 0) {
                    $mapped[substr($key, 6)] = $value;
                } else {
                    $mapped[$key] = $value;
                }
            }

            $mapped['institute1_id'] = $institute1id;
            $mapped['employee_id'] = $id;

            $fillable = (new EmployeeDummy)->getFillable();
            $data = array_intersect_key($mapped, array_flip($fillable));

            $status = EmployeeDummy::create($data);
        } else {
            // Update: ONLY update fields that the USER actually changed (relative to Employee table)
            $empDummy = EmployeeDummy::firstOrNew(['employee_id' => $id]);

            $empDummy->employee_id = $id;
            if (isset($institute1id)) {
                $empDummy->institute1_id = $institute1id;
            }

            $trackable = (new EmployeeDummy)->getFillable();

            foreach ($trackable as $field) {
                if (in_array($field, ['id'])) continue;

                // original value from Employee table (source-of-truth for "unchanged")
                $employeeVal = $employee->$field ?? null;
                if ($employeeVal === '') $employeeVal = null;

                // If the request submitted a visible field (present in form),
                // treat it as *user edit* only when it differs from the Employee value.
                if ($request->has($field)) {
                    $submitted = $request->input($field);
                    if ($submitted === '') $submitted = null;

                    // If submitted equals Employee value -> user didn't change this field (leave dummy as-is)
                    if ($submitted === $employeeVal) {
                        // do nothing: preserve existing empDummy value (if any)
                        continue;
                    } else {
                        // user changed relative to Employee -> store that change in empDummy
                        $empDummy->$field = $submitted;
                        continue;
                    }
                }

                // If visible field not present, check hidden dummy_ inputs (carry-over of prior dummy)
                $dummyKey = 'dummy_' . $field;
                if ($request->has($dummyKey)) {
                    $dummyVal = $request->input($dummyKey);
                    if ($dummyVal === '') $dummyVal = null;

                    // Only set if dummyVal differs from what is currently stored in empDummy or employee
                    // Current stored could be in DB empDummy or fallback to employee value
                    $currentStored = $empDummy->exists ? ($empDummy->$field ?? null) : null;
                    if ($currentStored === null) {
                        // no dummy stored yet, but we have employeeVal; we want to preserve prior dummyVal from form
                        if ($dummyVal !== $employeeVal) {
                            $empDummy->$field = $dummyVal;
                        }
                    } else {
                        // a dummy exists in DB; if dummyVal differs from it, update
                        if ($dummyVal !== $currentStored) {
                            $empDummy->$field = $dummyVal;
                        }
                    }
                }

                // else: neither submitted nor dummy_ present -> leave as is
            }

            if ($empDummy->isDirty()) {
                $status = $empDummy->save();
            } else {
                $status = true;
            }
        }

        // Teaching subjects update/insert (as before)
        $teachsubjects = $request->input('teachsubjects');
        if ($teachsubjects && is_array($teachsubjects)) {
            foreach ($teachsubjects as $teachsubject) {
                if (!empty($teachsubject['id'])) {
                    $query = EmpTeachSubject::find($teachsubject['id']);
                    if ($query) {
                        $query->cadresubject_id = $teachsubject['teachsubject_id'] ?? $query->cadresubject_id;
                        $query->periods = $teachsubject['periods'] ?? $query->periods;
                        $query->save();
                    }
                }
            }
        }

        if ($request->get('teachsubject_id')) {
            $cadresubject_id = $request->get('teachsubject_id');
            $periods = $request->get('periods');
            foreach ($cadresubject_id as $key => $value) {
                if (empty($value) && empty($periods[$key])) continue;
                $table = new EmpTeachSubject;
                $table->cadresubject_id = $cadresubject_id[$key];
                $table->periods = $periods[$key] ?? null;
                $table->employee_id  = $id;
                $table->save();
            }
        }

        if ($request->get('course_name')) {
            $courseNames  = $request->input('course_name', []);
            $institutions = $request->input('institution', []);
            $durations    = $request->input('duration', []);
            DB::transaction(function() use ($id, $courseNames, $institutions, $durations) {
                // Option A: append new rows (don't touch existing)
                foreach ($courseNames as $index => $course) {
                    $inst = $institutions[$index] ?? null;
                    $dur  = $durations[$index] ?? null;

                    // skip empty rows
                    if (empty($course) && empty($inst) && empty($dur)) {
                        continue;
                    }

                    EmpQualification::create([
                        'employee_id' => $id,
                        'course_name' => $course,
                        'institution' => $inst,
                        'duration'    => $dur,
                    ]);
                }
            });
        }

        DB::commit();

        if (!empty($status)) {
            request()->session()->flash('success', 'Successfully saved for approval');
        } else {
            request()->session()->flash('error', 'Error occurred while saving');
        }

        return redirect()->route('employee.index');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('dummy_store error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        request()->session()->flash('error', 'Unexpected error: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}




    public function dummy_destroy(string $id)
    {
        $status = EmployeeDummy::where('employee_id',$id)->delete();
        if($status){
            return response()->json([
                'success' => 'Record has been deleted successfully!'
            ]);
        }
        else{
            return response()->json([
                'error' => 'Error while deleting record'
            ]);
        }
    }

    public function destroy_emp_degsubject($id)
    {
        $record = EmpDegreeSubject::find($id);

        if (!$record) {
            return response()->json([
                'error' => 'Record not found'
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    
    public function destroy_teachsubject($id){
        $record = EmpTeachSubject::find($id);
        if(!$record){
            return response()->json(['error' => 'Record not found'], 404);
        }

        $record->delete();

        return response()->json([
            'success' => 'Record deleted successfully!'
        ]);
    }

    public function userSearch(Request $request)
    {
        $search = $request->search;
        $employees = Employee::where('nic', 'like', '%' .$search . '%')->get();
        $dataModified = array();
        foreach ($employees as $data){
            $dataModified[] = array('value'      => $data->id,
                                    'label'      => $data->nic,
                                    'fullname'   => $data->name_with_initial_e,
                                    'designation'=> $data->designation->designation,
                                    'institute'  => $data->institute->institute,
                                    'institute_id'  => $data->institute->id,
                                    'mobile'     => $data->mobile,
                                    'email'      => $data->email,
                                    );
            
        }
        return response()->json($dataModified);
    }

    public function showChart()
    {
        // Filter employees based on designation_id and status
        $employees = Employee::whereIn('designation_id', [8, 22])
            ->where('status', 'Active')
            ->get();

        // Gender distribution
        $genderData = $employees->groupBy('gender')->map->count();
        $genderData['Empty'] = $employees->whereNull('gender')->count();

        // Age distribution (calculated from dob)
        $ageGroups = $employees->map(function ($employee) {
            if (empty($employee->dob)) {
                return 'Empty';
            }
            $age = \Carbon\Carbon::parse($employee->dob)->age;
            if ($age < 20) return 'Below 20';
            elseif ($age <= 30) return '20-30';
            elseif ($age <= 40) return '30-40';
            elseif ($age <= 50) return '40-50';
            else return 'Above 50';
        })->countBy();

        // Religion distribution
        $religionData = $employees->groupBy('religion')->map->count();
        $religionData['Empty'] = $employees->whereNull('religion')->count();

        // Civil status distribution
        $civilStatusData = $employees->groupBy('civilstatus')->map->count();
        $civilStatusData['Empty'] = $employees->whereNull('civilstatus')->count();

        // Distance distribution (group distances)
        $distanceGroups = $employees->map(function ($employee) {
            if (empty($employee->distores)) {
                return 'Empty';
            }
            $distance = $employee->distores;
            if ($distance < 5) return 'Below 5 km';
            elseif ($distance <= 10) return '5-10 km';
            elseif ($distance <= 20) return '10-20 km';
            else return 'Above 20 km';
        })->countBy();

        // DS Division distribution
        $dsDivisionData = $employees->groupBy(fn($employee) => $employee->dsdivision->ds ?? 'Unknown')->map->count();
        $dsDivisionData['Empty'] = $employees->whereNull('dsdivision_id')->count();

        // GN Division distribution
        $gnDivisionData = $employees->groupBy(fn($employee) => $employee->gndivision->gn ?? 'Unknown')->map->count();
        $gnDivisionData['Empty'] = $employees->whereNull('gndivision_id')->count();

        // Zone distribution
        $zoneData = $employees->groupBy(fn($employee) => $employee->zone->zone ?? 'Unknown')->map->count();
        $zoneData['Empty'] = $employees->whereNull('zone_id')->count();

        // Transport Mode distribution
        $transmodeData = $employees->groupBy(fn($employee) => $employee->transmode->tranmode ?? 'Unknown')->map->count();
        $transmodeData['Empty'] = $employees->whereNull('transmode_id')->count();

        // Grade distribution
        $gradeData = $employees->groupBy('grade')->map->count();
        $gradeData['Empty'] = $employees->whereNull('grade')->count();

        // Designation distribution
        $designationData = $employees->groupBy(fn($employee) => $employee->designation->designation ?? 'Unknown')->map->count();
        $designationData['Empty'] = $employees->whereNull('designation_id')->count();

        // Trained distribution
        $trainedData = $employees->groupBy('trained')->map->count();
        $trainedData['Empty'] = $employees->whereNull('trained')->count();

        // Appointment Category distribution
        $appcategoryData = $employees->groupBy(fn($employee) => $employee->appcategory->appcat ?? 'Unknown')->map->count();
        $appcategoryData['Empty'] = $employees->whereNull('appcategory_id')->count();

        // Highest Qualification distribution
        $highqualificationData = $employees->groupBy(fn($employee) => $employee->highqualification->qualif ?? 'Unknown')->map->count();
        $highqualificationData['Empty'] = $employees->whereNull('highqualification_id')->count();

        return view('human_resource.analysis', compact(
            'genderData',
            'ageGroups',
            'religionData',
            'civilStatusData',
            'distanceGroups',
            'dsDivisionData',
            'gnDivisionData',
            'zoneData',
            'transmodeData',
            'gradeData',
            'designationData',
            'trainedData',
            'appcategoryData',
            'highqualificationData'
        ));
    }



}
