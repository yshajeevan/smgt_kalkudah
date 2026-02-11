<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Zonalappcadre;
use App\Exports\CadreDataExport;
use App\Models\Appcadre;
use App\Models\Institute;
use App\Models\StuBasket;
use Auth;
use Excel;
Use Helper;
use Illuminate\Support\Arr;
use Pnlinh\GoogleDistance\Facades\GoogleDistance;
use DataTables;
use App\Models\Cadresubject;


use TeamPickr\DistanceMatrix\DistanceMatrix;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;

class CadreController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:cadre-view', ['only' => ['index','detailedCadre']]);
         $this->middleware('permission:cadre-export', ['only' => ['cadreexport']]);
    }

    public function index(Request $request)
    {
            $arr1 = ["7","10","11","12"]; //Add principal catogary
      
            $instid = $request->txtid;

            if ($instid > 0) { 
                $institute = DB::table('institutes')->where('id', $instid)->first(); // get institute details
                $institute_array = DB::table('institutes')->where('id',$instid)->pluck('id');
                $avicadre = Helper::avicdare($institute_array)->whereIn('designation_id', $request->des)->orwhereIn('designation_id', $arr1)->where('status','=','Active')->groupBy('institute_id');
                $cad = Appcadre::joinSub($avicadre, 'avicadre', function ($join) {
                    $join->on('appcadres.institute_id', '=', 'avicadre.institute_id');
                })
                ->where('appcadres.institute_id', '=', $instid)
                ->first();
                $cadres[] = collect($cad);

            } else {
                $institute = null;
                if(!empty($request->division)){
                    $institute_array = DB::table('institutes')->where('division',$request->division)->pluck('id');
                } else{
                    $institute_array = DB::table('institutes')->where('id','<',69)->pluck('id');
                }
                $avicadre = Helper::avicdare($institute_array)->whereIn('designation_id', $request->des)->orwhereIn('designation_id', $arr1)->where('status','=','Active');
                
                if($request->division == 'MW'){
                    $cad = $avicadre->crossJoin('mw_appcadres')->selectRaw('smgt_mw_appcadres.*')->get()->first();
                }elseif($request->division == 'MSW'){
                    $cad = $avicadre->crossJoin('msw_appcadres')->selectRaw('smgt_msw_appcadres.*')->get()->first();
                }elseif($request->division == 'EP'){
                    $cad = $avicadre->crossJoin('ep_appcadres')->selectRaw('smgt_ep_appcadres.*')->get()->first();
                } else{
                    $cad = $avicadre->crossJoin('appcadrezone')->selectRaw('smgt_appcadrezone.*')->get()->first();                   
                }
                $cadres[] = collect($cad);
            }

            $processedData = [];

            // Assuming data is a list of arrays, we'll process the first item
            $data = $cadres[0];

            // Extracting available cadre and approved cadre
            foreach ($data as $key => $value) {
                if (strpos($key, 'app_') === 0) {
                    // Extract the subject from the 'app_' prefix
                    $subject_code = str_replace('app_', '', $key);
                    $approvedCadre = $value;

                    // Construct the key for available cadre
                    $availableCadreKey = 'avi_' . $subject_code;

                    // Get the available cadre value, defaulting to 0 if not set
                    $availableCadre = isset($data[$availableCadreKey]) ? $data[$availableCadreKey] : 0;

                    // Calculate the difference
                    $difference = $availableCadre - $approvedCadre;

                    // Find the corresponding name from the CadreSubject table
                    $cadreSubject = CadreSubject::where('cadre_code', $subject_code)->first();
                    $subjectName = $cadreSubject ? $cadreSubject->cadre : null;
                    $subjectNumber = $cadreSubject ? $cadreSubject->subject_number : null;
                    $subjectCategory = $cadreSubject ? $cadreSubject->category : null;

                    // Append the processed data
                    $processedData[] = [
                        'subject_name' => $subjectName,
                        'subject_code' => $subject_code,
                        'subject_number' => $subjectNumber,
                        'subject_category' => $subjectCategory,
                        'approved' => $approvedCadre,
                        'available' => $availableCadre,
                        'difference' => $difference
                    ];
                }
            }

            // Sort the processed data by subject_number
            usort($processedData, function ($a, $b) {
                return $a['subject_number'] <=> $b['subject_number'];
            });

            // Check if designation_id values 9 (performing principals) and 22 (development officers) are in $request->des
            $hasNine = in_array(9, $request->des);
            $hasTwentyTwo = in_array(22, $request->des);

            // Initialize flags
            $isPerform = false;
            $isDevOfficer = false;

            // Check if designation_id values 9 and 22 are in $request->des
            if (in_array(9, $request->des)) {
                $isPerform = true;
            }

            if (in_array(22, $request->des)) {
                $isDevOfficer = true;
            }

            // Initialize count variables
            $countNine = 0;
            $countTwentyTwo = 0; 

            if (!$hasNine || !$hasTwentyTwo) {
                // Count occurrences in the database
                if (!$hasNine) {
                    $countNine = Helper::avicdare($institute_array)
                        ->where('designation_id', 9)
                        ->where('status', '=', 'Active')
                        ->count();
                }

                if (!$hasTwentyTwo) {
                    $countTwentyTwo = Helper::avicdare($institute_array)
                        ->where('designation_id', 22)
                        ->where('status', '=', 'Active')
                        ->count();
                }

                return view('human_resource.cadrerpt', [
                    'subjects' => $processedData, 
                    'performing_principals' => $countNine, 
                    'development_officers' => $countTwentyTwo,
                    'isPerform' => $isPerform,
                    'isDevOfficer' => $isDevOfficer,
                    'institute' => $institute
                ]); 
            } else {
                return view('human_resource.cadrerpt', [
                    'subjects' => $processedData, 
                    'performing_principals' => $countNine, 
                    'development_officers' => $countTwentyTwo,
                    'isPerform' => $isPerform,
                    'isDevOfficer' => $isDevOfficer,
                    'institute' => $institute
                ]);
            }

            
             
    }  

    public function detailedCadre(Request $request)
    {
        $cols = $request->col;

        $col0_app = "app_" . $cols[0];
        $col0_avi = "avi_" . $cols[0];
        
        if (array_key_exists(1, $cols)) {
            $col1_app = "app_" . $cols[1];
            $col1_avi = "avi_" . $cols[1];
        } else {
            $col1_app = '';
            $col1_avi = '';
        }
        
        $institute_array = DB::table('institutes')->where('id', '<', 69)->pluck('id');
        $avicadre = Helper::avicdare($institute_array)->whereIn('designation_id', $request->des)->groupBy('institute_id');
        $baskets = StuBasket::whereHas('cadresubject', function($q) use($col0_avi) {
                $q->where('cadre_code', $col0_avi);
            })->select('institute_id', 'cadresubject_id', DB::raw("SUM(students) as count"))->groupBy('institute_id');
        
        $cadres = Appcadre::joinSub($avicadre, 'avicadre', function ($join) {
                $join->on('appcadres.institute_id', '=', 'avicadre.institute_id');
            })->leftjoinSub($baskets, 'baskets', function ($join) {
                $join->on('appcadres.institute_id', '=', 'baskets.institute_id');
            })->select(
                'avicadre.institute_id', 
                'baskets.count', 
                DB::raw($col0_app), 
                DB::raw($col0_avi)
            )->when(array_key_exists(1, $cols), function ($cadres) use ($col0_app, $col0_avi, $col1_app, $col1_avi) {
                return $cadres->select(
                    'avicadre.institute_id', 
                    DB::raw($col0_app), 
                    DB::raw($col0_avi), 
                    DB::raw($col1_app), 
                    DB::raw($col1_avi)
                );
            })
            // ->having($col0_app, '>', 0)
            // ->having($col0_avi, '>', 0)
            ->get();
                
           
            return view('human_resource.cadrerptHorizontal', compact('cadres','col0_app','col0_avi','col1_app','col1_avi','cols'));     
    }

    public function cadrexport(Request $request)
    {
        // Fetch all institutes
        $institutes = Institute::with('employee.cadreSubject')->where('id', '<', '69')->get();

        // Fetch all cadre subjects
        $excludedcategories = ['office_academic', 'office_non_academic'];
        $cadres = CadreSubject::whereNotIn('category', $excludedcategories)->orderBy('subject_number')->get();

        // Fetch all approved cadres
        $approvedCadres = AppCadre::all();

        // Structure the data
        $structuredData = [];

        foreach ($institutes as $institute) {
            $row = [
                'census' => $institute->census,
                'institute_name' => $institute->institute,
                'cadres' => [],
            ];
        
            $designations = $request->input('des',["8","13"]); // Default to an empty array if 'des' is not set

            foreach ($cadres as $cadre) {
                // Extract the 'app_' prefixed column dynamically
                $approvedCadreColumn = 'app_' . $cadre->cadre_code;
        
                // Find the corresponding approved cadre for the institute
                $approved = $approvedCadres
                    ->where('institute_id', $institute->id)
                    ->pluck($approvedCadreColumn)
                    ->first() ?? 0;
        
                // Count available employees for this cadre in the institute
                $available = $institute->employee
                    ->where('cadresubject_id', $cadre->id)
                    ->filter(function ($employee) use ($designations) {
                        return in_array($employee->designation_id, $designations) && $employee->status === 'Active';
                    })
                    ->count();
        
                // Calculate Ex/De (available - approved)
                $ex_de = $available - $approved;
        
                // Populate the row data
                $row['cadres'][] = [
                    'approved' => $approved,
                    'available' => $available,
                    'ex_de' => $ex_de,
                ];
            }
        
            $structuredData[] = $row;
        }

        if ($request->has('export')) {
            return $this->exportExcel($structuredData, $cadres);
        }
    
        return view('human_resource.cadrerpt_horizontal', compact('structuredData', 'cadres'));
    }

    public function exportExcel($structuredData, $cadres) 
    {
        return Excel::download(new CadreDataExport($structuredData, $cadres), 'cadre_data.xlsx');
    }
    
    public function transcadre(Request $request){
        $license = new StandardLicense('AIzaSyAHZUq9uPXq56Baa1yGZyUlmZ6WxinyFGQ');
        
        $origin = $request->gpsloc;
        $appsub = 'app_'.$request->cadrecode;
        $avisub = $request->cadrecode;
        
        $institute_array = DB::table('institutes')->where('id','<',69)->pluck('id');
        $avicadre = Helper::avicdare($institute_array)->groupBy('institute_id');
        $inst = Institute::select('id','institute','gpslocation');
        
        $cadres = Appcadre::joinSub($avicadre, 'avicadre', function ($join) {
                $join->on('appcadres.institute_id', '=', 'avicadre.institute_id');
                })->joinSub($inst, 'inst', function ($join) {
                $join->on('appcadres.institute_id', '=', 'inst.id');
                })->where($appsub,'!=',0)->where($avisub,'!=',0)
                ->select('appcadres.institute_id','inst.institute','inst.gpslocation',$appsub.' as app',$avisub.' as avi', DB::raw("$avisub-$appsub as exd"))
                ->orderBy('exd','asc')->get();
                
                // return response()->json($cadres);
                
        $getloc  = Appcadre::joinSub($avicadre, 'avicadre', function ($join) {
                $join->on('appcadres.institute_id', '=', 'avicadre.institute_id');
                })->joinSub($inst, 'inst', function ($join) {
                $join->on('appcadres.institute_id', '=', 'inst.id');
                })->where($appsub,'!=',0)->where($avisub,'!=',0)
                ->orderBy('inst.institute','asc')
                ->pluck('inst.gpslocation','inst.institute')->toarray();
                
                
                
        foreach($getloc as $key => $val)
        {
            $destination = $val;
    
            $rows = DistanceMatrix::license($license)->addOrigin($origin)->addDestination($destination)->request()->rows();
            $elements = $rows[0]->elements();
        
            $distance[$key] =  $elements[0]->distanceText();
    
        }
         
        
        return response()->json([$cadres,$distance]);
    }
    
    public function index_tmp(Request $request){
        if($request->ajax()) {
            if ($request->get("instid") > 0) { 
                $data =  Employee::whereIn('designation_id', $request->get("designation"))->where('status','Active')->where('institute_id',$request->get("instid"))->select('cadresubject_id', DB::raw('count(*) as avi_cadre'))
                        ->groupBy('cadresubject_id')->with('cadresubject');
            } else {
                $data =  Employee::whereIn('designation_id', $request->get("designation"))->where('status','Active')->select('cadresubject_id', DB::raw('count(*) as avi_cadre'))
                        ->groupBy('cadresubject_id')->with('cadresubject');
            }

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('cadresubject', function(Employee $employee){
                return $employee->cadresubject->cadre;
            })
            ->addColumn('app_cadre', function(Employee $employee){
                return $employee->cadresubject->app_cadre;
            })
            ->addColumn('exd_cadre', function(Employee $employee){
                return $employee->avi_cadre - $employee->cadresubject->app_cadre;
            })
            ->filterColumn('name', function($query, $keyword) {
                    $sql = "name like ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
            ->rawColumns(['action'])
            ->make(true);
        }
        if(!empty($request->txtid)) {
            $instid = $request->txtid;
        } else {
            $instid = 0;
        }
       $designation = $request->des;
        return view('human_resource.cadrelist',compact('instid','designation'));
    }
}
