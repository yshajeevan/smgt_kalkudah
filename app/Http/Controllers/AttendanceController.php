<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentTotal;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Auth;
use DataTables;
use Session;
use DB;
use Illuminate\Support\Facades\Route;
use Excel;
use Mail;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:attendance-list', ['only' => ['index']]);
         $this->middleware('permission:attendance-create', ['only' => ['create','store']]);
         $this->middleware('permission:attendance-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:attendance-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    { 
        $institute = $request->institute_search;
        $insid = $request->txtid;
        $currentPath = Route::getFacadeRoot()->current()->uri();
        $uname = Auth::user()->roles->pluck('name')->implode(', ');
        $schools = DB::table('institutes as i')
            ->join('attendances as a', 'a.institute_id', '=', 'i.id')
            ->whereDate('a.created_at', Carbon::today())
            ->select('i.id', 'i.institute')
            ->distinct()
            ->orderBy('i.institute')
            ->get();
        $countatten = Attendance::whereDate('created_at', Carbon::today())->count();

        if ($request->ajax()) {
            $class = $request->class_filter;

            $totExpr = '';
            $prExpr  = '';

            switch ($class) {

                // ✅ Grade groups
                case '1_5':
                    $totExpr = 'COALESCE(tot_1_5,0)';
                    $prExpr  = 'COALESCE(pr_1_5,0)';
                    break;

                case '6_9':
                    $totExpr = 'COALESCE(tot_6_9,0)';
                    $prExpr  = 'COALESCE(pr_6_9,0)';
                    break;

                case '10_11':
                    $totExpr = 'COALESCE(tot_10_11,0)';
                    $prExpr  = 'COALESCE(pr_10_11,0)';
                    break;

                case 'secondary': // ✅ 6–11 combined
                    $totExpr = '
                        COALESCE(tot_6_9,0) +
                        COALESCE(tot_10_11,0)
                    ';
                    $prExpr = '
                        COALESCE(pr_6_9,0) +
                        COALESCE(pr_10_11,0)
                    ';
                    break;

                // ✅ Individual A/L 1st Year
                case 'arts_1st':
                case 'com_1st':
                case 'physc_1st':
                case 'biosc_1st':
                case 'etech_1st':
                case 'btech_1st':
                    $totExpr = "COALESCE(tot_{$class},0)";
                    $prExpr  = "COALESCE(pr_{$class},0)";
                    break;

                // ✅ Individual A/L 2nd Year
                case 'arts_2nd':
                case 'com_2nd':
                case 'physc_2nd':
                case 'biosc_2nd':
                case 'etech_2nd':
                case 'btech_2nd':
                    $totExpr = "COALESCE(tot_{$class},0)";
                    $prExpr  = "COALESCE(pr_{$class},0)";
                    break;

                // ✅ All A/L 1st Year combined
                case 'al_1':
                    $totExpr = '
                        COALESCE(tot_arts_1st,0) +
                        COALESCE(tot_com_1st,0) +
                        COALESCE(tot_physc_1st,0) +
                        COALESCE(tot_biosc_1st,0) +
                        COALESCE(tot_etech_1st,0) +
                        COALESCE(tot_btech_1st,0)
                    ';
                    $prExpr = '
                        COALESCE(pr_arts_1st,0) +
                        COALESCE(pr_com_1st,0) +
                        COALESCE(pr_physc_1st,0) +
                        COALESCE(pr_biosc_1st,0) +
                        COALESCE(pr_etech_1st,0) +
                        COALESCE(pr_btech_1st,0)
                    ';
                    break;

                // ✅ All A/L 2nd Year combined
                case 'al_2':
                    $totExpr = '
                        COALESCE(tot_arts_2nd,0) +
                        COALESCE(tot_com_2nd,0) +
                        COALESCE(tot_physc_2nd,0) +
                        COALESCE(tot_biosc_2nd,0) +
                        COALESCE(tot_etech_2nd,0) +
                        COALESCE(tot_btech_2nd,0)
                    ';
                    $prExpr = '
                        COALESCE(pr_arts_2nd,0) +
                        COALESCE(pr_com_2nd,0) +
                        COALESCE(pr_physc_2nd,0) +
                        COALESCE(pr_biosc_2nd,0) +
                        COALESCE(pr_etech_2nd,0) +
                        COALESCE(pr_btech_2nd,0)
                    ';
                    break;

                // ✅ Default (ALL students)
                default:
                    $totExpr = '(
                        COALESCE(tot_1_5,0) +
                        COALESCE(tot_6_9,0) +
                        COALESCE(tot_10_11,0) +
                        COALESCE(tot_arts_1st,0) +
                        COALESCE(tot_com_1st,0) +
                        COALESCE(tot_physc_1st,0) +
                        COALESCE(tot_biosc_1st,0) +
                        COALESCE(tot_etech_1st,0) +
                        COALESCE(tot_btech_1st,0) +
                        COALESCE(tot_arts_2nd,0) +
                        COALESCE(tot_com_2nd,0) +
                        COALESCE(tot_physc_2nd,0) +
                        COALESCE(tot_biosc_2nd,0) +
                        COALESCE(tot_etech_2nd,0) +
                        COALESCE(tot_btech_2nd,0)
                    )';

                    $prExpr = '(
                        COALESCE(pr_1_5,0) +
                        COALESCE(pr_6_9,0) +
                        COALESCE(pr_10_11,0) +
                        COALESCE(pr_arts_1st,0) +
                        COALESCE(pr_com_1st,0) +
                        COALESCE(pr_physc_1st,0) +
                        COALESCE(pr_biosc_1st,0) +
                        COALESCE(pr_etech_1st,0) +
                        COALESCE(pr_btech_1st,0) +
                        COALESCE(pr_arts_2nd,0) +
                        COALESCE(pr_com_2nd,0) +
                        COALESCE(pr_physc_2nd,0) +
                        COALESCE(pr_biosc_2nd,0) +
                        COALESCE(pr_etech_2nd,0) +
                        COALESCE(pr_btech_2nd,0)
                    )';
            }

            // ✅ BASE QUERY (FIXED)
            $baseQuery = Attendance::query()
            ->leftJoin('institutes as i', 'i.id', '=', 'attendances.institute_id')
            ->select([
                'attendances.*',
                'i.institute as institute_name',
                DB::raw('DATE(smgt_attendances.created_at) as adate'),
                DB::raw("$totExpr as totstu"),
                DB::raw("$prExpr as prstu"),
                DB::raw("($prExpr / NULLIF($totExpr,0)) as perc")
            ]);
            // ✅ CONDITIONS
            $currentPath = $request->currentPath ?? '';

            if (!empty($request->insid)) {

                $data = $baseQuery
                    ->where('institute_id', $request->insid)
                    ->orderBy('adate', 'desc');

            } elseif ($currentPath == 'attendance-schools') {

                $data = $baseQuery->orderByDesc('perc');

            } elseif ($uname == 'Sch_Admin' && $currentPath == 'schoolatten') {

                $data = $baseQuery
                    ->where('institute_id', Auth::user()->institute_id)
                    ->orderBy('adate', 'desc');

            } elseif ($currentPath == 'zonalattendance') {

                // ✅ FIXED ZONAL QUERY
                $data = Attendance::selectRaw("
                    DATE(created_at) as adate,

                    COUNT(DISTINCT institute_id) as total_schools,

                    SUM(CASE WHEN principal = 1 THEN 1 ELSE 0 END) as principal_present,

                    (SUM(CASE WHEN principal = 1 THEN 1 ELSE 0 END) 
                        / NULLIF(COUNT(DISTINCT institute_id),0)) * 100 as principal_perc,

                    SUM(tottea) as tottea,
                    SUM(prtea) as prtea,
                    (SUM(prtea) / NULLIF(SUM(tottea),0)) * 100 as perctea,

                    SUM($totExpr) as totstu,
                    SUM($prExpr) as prstu,
                    (SUM($prExpr) / NULLIF(SUM($totExpr),0)) as perc
                ")
                ->groupBy('adate')
                ->orderBy('adate', 'desc');

            } else {

                // ✅ fallback (VERY IMPORTANT)
                $data = $baseQuery->orderBy('adate', 'desc');
            }

        
            return Datatables::of($data)
                ->addIndexColumn() //

                ->addColumn('institute_name', function ($row) {
                    return $row->institute_name ?? 'NULL';
                })

                ->addColumn('rank', function () {
                    static $rank = 0;
                    $rank++;

                    if ($rank == 1) return '<span style="font-size:18px;">🥇</span> 1';
                    if ($rank == 2) return '<span style="font-size:18px;">🥈</span> 2';
                    if ($rank == 3) return '<span style="font-size:18px;">🥉</span> 3';

                    return $rank;
                })

                ->addColumn('total_schools', function ($row) {
                    return $row->total_schools ?? 0;
                })

                ->addColumn('principal_present', function ($row) {
                    return $row->principal_present ?? 0;
                })

                ->addColumn('principal_perc', function ($row) {
                    if ($row->total_schools > 0) {
                        $perc = round($row->principal_perc, 2);
                        return '<div class="progress">
                            <div class="progress-bar bg-info" style="width:' . $perc . '%">' . $perc . '%</div>
                        </div>';
                    }
                    return 0;
                })

                ->editColumn('percstu', function ($row) {
                    if ($row->totstu > 0) {
                        $perc = round(($row->prstu / $row->totstu) * 100, 2);
                        return '<div class="progress">
                            <div class="progress-bar bg-danger" style="width:' . $perc . '%">' . $perc . '%</div>
                        </div>';
                    }
                    return 0;
                })

                ->editColumn('perctea', function ($row) {
                    if ($row->tottea > 0) {
                        $perc = round(($row->prtea / $row->tottea) * 100, 2);

                        return '<div class="progress">
                            <div class="progress-bar bg-success" style="width:' . $perc . '%">' . $perc . '%</div>
                        </div>';
                    }
                    return 0;
                })

                ->editColumn('principal', function ($row) {
                    if ($row->principal == 1) {
                            return '<span class="badge badge-success">On-duty</span>';
                        } elseif ($row->principal == 2) {
                            return '<span class="badge badge-warning">Duty Leave</span>';
                        } elseif ($row->principal == 3) {
                            return '<span class="badge badge-danger">Personal Leave</span>';
                        }

                        return '-';
                })

                ->editColumn('created_at', function ($row) {
                    return date('d F Y', strtotime($row->adate));
                })

                ->filter(function ($instance) use ($request, $currentPath) {

                    if (!empty($request->from_date)) {
                        $instance->whereDate('attendances.created_at', $request->from_date);
                    } elseif ($request->currentPath == 'attendance-schools') {
                        $instance->whereDate('attendances.created_at', Carbon::today()); // default
                    }

                    if (!empty($request->search['value']) && $request->currentPath == 'attendance-schools') {
                        $search = $request->search['value'];

                        $instance->where(function ($q) use ($search) {
                            $q->orWhere('attendances.institute_id', 'LIKE', "%{$search}%")
                            ->orWhere('i.institute', 'LIKE', "%{$search}%");
                        });
                    }

                    if (!empty($request->school_id)) {
                        $instance->where('attendances.institute_id', $request->school_id);
                    }
                })

                ->rawColumns(['percstu','institute_name', 'perctea', 'principal','rank','principal_perc'])
                ->make(true);
        }

        return view('attendance.index', compact( 
            'countatten',
            'uname',
            'currentPath',
            'insid',
            'institute',
            'schools'
        ));
    }

    public function attendanceGraph(Request $request)
    {
        $currentPath = $request->currentPath ?? '';
        $insid = $request->insid;
        $fromDate = $request->from_date;
        $uname = Auth::user()->roles->pluck('name')->implode(', ');
        $class = $request->class_filter;

        // =========================
        // ✅ CLASS FILTER (SAME AS TABLE)
        // =========================
        $totExpr = '';
        $prExpr  = '';

        switch ($class) {

            case '1_5':
                $totExpr = 'COALESCE(tot_1_5,0)';
                $prExpr  = 'COALESCE(pr_1_5,0)';
                break;

            case '6_9':
                $totExpr = 'COALESCE(tot_6_9,0)';
                $prExpr  = 'COALESCE(pr_6_9,0)';
                break;

            case '10_11':
                $totExpr = 'COALESCE(tot_10_11,0)';
                $prExpr  = 'COALESCE(pr_10_11,0)';
                break;

            case 'secondary':
                $totExpr = 'COALESCE(tot_6_9,0)+COALESCE(tot_10_11,0)';
                $prExpr  = 'COALESCE(pr_6_9,0)+COALESCE(pr_10_11,0)';
                break;

            case 'arts_1st':
            case 'com_1st':
            case 'physc_1st':
            case 'biosc_1st':
            case 'etech_1st':
            case 'btech_1st':
                $totExpr = "COALESCE(tot_{$class},0)";
                $prExpr  = "COALESCE(pr_{$class},0)";
                break;

            case 'arts_2nd':
            case 'com_2nd':
            case 'physc_2nd':
            case 'biosc_2nd':
            case 'etech_2nd':
            case 'btech_2nd':
                $totExpr = "COALESCE(tot_{$class},0)";
                $prExpr  = "COALESCE(pr_{$class},0)";
                break;

            case 'al_1':
                $totExpr = '
                    COALESCE(tot_arts_1st,0)+COALESCE(tot_com_1st,0)+
                    COALESCE(tot_physc_1st,0)+COALESCE(tot_biosc_1st,0)+
                    COALESCE(tot_etech_1st,0)+COALESCE(tot_btech_1st,0)
                ';
                $prExpr = '
                    COALESCE(pr_arts_1st,0)+COALESCE(pr_com_1st,0)+
                    COALESCE(pr_physc_1st,0)+COALESCE(pr_biosc_1st,0)+
                    COALESCE(pr_etech_1st,0)+COALESCE(pr_btech_1st,0)
                ';
                break;

            case 'al_2':
                $totExpr = '
                    COALESCE(tot_arts_2nd,0)+COALESCE(tot_com_2nd,0)+
                    COALESCE(tot_physc_2nd,0)+COALESCE(tot_biosc_2nd,0)+
                    COALESCE(tot_etech_2nd,0)+COALESCE(tot_btech_2nd,0)
                ';
                $prExpr = '
                    COALESCE(pr_arts_2nd,0)+COALESCE(pr_com_2nd,0)+
                    COALESCE(pr_physc_2nd,0)+COALESCE(pr_biosc_2nd,0)+
                    COALESCE(pr_etech_2nd,0)+COALESCE(pr_btech_2nd,0)
                ';
                break;

            default:
                $totExpr = '(
                    COALESCE(tot_1_5,0)+COALESCE(tot_6_9,0)+COALESCE(tot_10_11,0)+
                    COALESCE(tot_arts_1st,0)+COALESCE(tot_com_1st,0)+
                    COALESCE(tot_physc_1st,0)+COALESCE(tot_biosc_1st,0)+
                    COALESCE(tot_etech_1st,0)+COALESCE(tot_btech_1st,0)+
                    COALESCE(tot_arts_2nd,0)+COALESCE(tot_com_2nd,0)+
                    COALESCE(tot_physc_2nd,0)+COALESCE(tot_biosc_2nd,0)+
                    COALESCE(tot_etech_2nd,0)+COALESCE(tot_btech_2nd,0)
                )';

                $prExpr = '(
                    COALESCE(pr_1_5,0)+COALESCE(pr_6_9,0)+COALESCE(pr_10_11,0)+
                    COALESCE(pr_arts_1st,0)+COALESCE(pr_com_1st,0)+
                    COALESCE(pr_physc_1st,0)+COALESCE(pr_biosc_1st,0)+
                    COALESCE(pr_etech_1st,0)+COALESCE(pr_btech_1st,0)+
                    COALESCE(pr_arts_2nd,0)+COALESCE(pr_com_2nd,0)+
                    COALESCE(pr_physc_2nd,0)+COALESCE(pr_biosc_2nd,0)+
                    COALESCE(pr_etech_2nd,0)+COALESCE(pr_btech_2nd,0)
                )';
        }

        // =========================
        // ✅ BASE QUERY
        // =========================
        $query = Attendance::query()
            ->selectRaw("
                DATE(created_at) as adate,
                SUM($prExpr) as prstu,
                SUM($totExpr) as totstu
            ");

        // =========================
        // ✅ SAME CONDITIONS AS TABLE
        // =========================
        if (!empty($insid)) {

            $query->where('institute_id', $insid);

        } elseif ($currentPath == 'attendance-schools') {

            // zonal → no filter

        } elseif ($uname == 'Sch_Admin' && $currentPath == 'schoolatten') {

            $query->where('institute_id', Auth::user()->institute_id);

        } elseif ($currentPath == 'zonalattendance') {

            // zonal → no filter
        }

        // =========================
        // ✅ DATE FILTER (TREND)
        // =========================
        if (!empty($fromDate)) {
            $query->whereDate('created_at', '<=', $fromDate);
        }

        $data = $query
            ->groupBy('adate')
            ->orderBy('adate')
            ->get();

        // =========================
        // ✅ FORMAT OUTPUT
        // =========================
        $labels = [];
        $studentPerc = [];

        foreach ($data as $row) {
            $labels[] = $row->adate;

            $studentPerc[] = $row->totstu > 0
                ? round(($row->prstu / $row->totstu) * 100, 2)
                : 0;
        }

        return response()->json([
            'labels' => $labels,
            'student' => $studentPerc
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
 
    public function create(Request $request)
    {
        $uname = Auth::user()->roles->pluck('name')->implode(', ');
        if($uname != 'Sch_Admin'){
            $instid = $request->txtid;
        } else {
            $instid = Auth::user()->institute_id;
        }
        
        $stupop = StudentTotal::groupBy('institute_id')
                                    ->where('institute_id', $instid)
                                    ->first();
        $teachers = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->whereIn('designation_id', array('8','13'))
                                    ->first();
        $trainees = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->where('designation_id', 22)
                                    ->first();
        $nonacademic = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->whereIn('designation_id', array('26','19','18','17','16'))
                                    ->first();
        
        return view('attendance.create',compact('stupop','teachers','trainees','nonacademic','instid')); 
    }

    //Store attendance from school list (from zone)
    public function createlink($instid)
    {
        $instid = $instid;
    
        $stupop = StudentTotal::where('institute_id', $instid)->first();

        $teachers = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->whereIn('designation_id', array('8','13'))
                                    ->first();
        $trainees = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->where('designation_id', 22)
                                    ->first();
        $nonacademic = Employee::selectRaw('count(id) as count')
                                    ->where('status', 'Active')
                                    ->where('institute_id', $instid)
                                    ->whereIn('designation_id', array('26','19','18','17','16'))
                                    ->first();
        
        return view('attendance.create',compact('stupop','teachers','trainees','nonacademic','instid')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        $request->validate([
            'tottea' => 'required|integer|min:0',
            'prtea'  => 'required|integer|min:0|lte:tottea',
        ]);

        // ⏰ Time restriction
        $ctime = date("H:i:s");
        if ($ctime >= "00:00:00" && $ctime <= "07:30:00") {
            return redirect()->back()
                ->with(Session::flash('alert-info', 'You can not update attendance between 12.00am to 7.30am'));
        }

        // 🚫 جلوگیری duplicate entry
        if (Attendance::where('institute_id', $request->institute_id)
            ->whereDate('created_at', Carbon::today())
            ->exists()) {
            return redirect()->back()
                ->with(Session::flash('alert-danger', 'You have already entered the data'));
        }

        // 🔁 Map Blade → DB fields
        $attendance = Attendance::create([

            'institute_id' => $request->institute_id,
            'principal' => $request->principal,

            // Grade 1-5
            'tot_1_5' => $request->input('1_5_tot'),
            'pr_1_5'  => $request->input('1_5_pr'),

            // Grade 6-9
            'tot_6_9' => $request->input('6_9_tot'),
            'pr_6_9'  => $request->input('6_9_pr'),

            // Grade 10-11
            'tot_10_11' => $request->input('10_11_tot'),
            'pr_10_11'  => $request->input('10_11_pr'),


            //1st Year A/L
            // 🎓 A/L Arts
            'tot_arts_1st' => $request->input('al_arts_1st_tot'),
            'pr_arts_1st'  => $request->input('al_arts_1st_pr'),

            // 💼 A/L Commerce
            'tot_com_1st' => $request->input('al_com_1st_tot'),
            'pr_com_1st'  => $request->input('al_com_1st_pr'),

            // 🔬 A/L Physical Science
            'tot_physc_1st' => $request->input('al_physc_1st_tot'),
            'pr_physc_1st'  => $request->input('al_physc_1st_pr'),

            // 🧬 A/L Bio Science
            'tot_biosc_1st' => $request->input('al_biosc_1st_tot'),
            'pr_biosc_1st'  => $request->input('al_biosc_1st_pr'),

            // ⚙️ A/L Engineering Tech
            'tot_etech_1st' => $request->input('al_etech_1st_tot'),
            'pr_etech_1st'  => $request->input('al_etech_1st_pr'),

            // 🌱 A/L Bio Systems Tech
            'tot_btech_1st' => $request->input('al_btech_1st_tot'),
            'pr_btech_1st'  => $request->input('al_btech_1st_pr'),


            //2nd Year A/L
            // 🎓 A/L Arts
            'tot_arts_2nd' => $request->input('al_arts_2nd_tot'),
            'pr_arts_2nd'  => $request->input('al_arts_2nd_pr'),

            // 💼 A/L Commerce
            'tot_com_2nd' => $request->input('al_com_2nd_tot'),
            'pr_com_2nd'  => $request->input('al_com_2nd_pr'),

            // 🔬 A/L Physical Science
            'tot_physc_2nd' => $request->input('al_physc_2nd_tot'),
            'pr_physc_2nd'  => $request->input('al_physc_2nd_pr'),

            // 🧬 A/L Bio Science
            'tot_biosc_2nd' => $request->input('al_biosc_2nd_tot'),
            'pr_biosc_2nd'  => $request->input('al_biosc_2nd_pr'),

            // ⚙️ A/L Engineering Tech
            'tot_etech_2nd' => $request->input('al_etech_2nd_tot'),
            'pr_etech_2nd'  => $request->input('al_etech_2nd_pr'),

            // 🌱 A/L Bio Systems Tech
            'tot_btech_2nd' => $request->input('al_btech_2nd_tot'),
            'pr_btech_2nd'  => $request->input('al_btech_2nd_pr'), 

            // Teachers
            'tottea' => $request->input('tottea'),
            'prtea'  => $request->input('prtea'),

            'updated_by' => Auth::id(),
        ]);

        // 📊 Email trigger
        $countatten = Attendance::whereDate('created_at', Carbon::today())->count();

        // if ($countatten == 68) {
        //     Excel::store(new AttendanceExport, 'attendance.xlsx', 'excel_uploads');

        //     Mail::send(['text' => 'mail.attendance'], [], function ($message) {
        //         $message->to([
        //             'mineducationep@gmail.com',
        //             'plan@edudept.ep.gov.lk',
        //             'yshajeevan@gmail.com'
        //         ])
        //         ->subject('Attendance Batticaloa West-' . now()->format('Y-m-d'))
        //         ->from('baw@edudept.ep.gov.lk', 'Batticaloa West')
        //         ->attach(storage_path('app/excel_uploads/attendance.xlsx'));
        //     });
        // }

        // 🔁 Redirect
        if (Auth::user()->hasRole('Sch_Admin')) {
            return redirect()->route('attendance.submitrespose');
        }

        return redirect()->route('attendance.list')
            ->with('success', 'Attendance Successfully Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSchoolsByDate(Request $request)
    {
        $date = $request->date ?? Carbon::today();

        $schools = DB::table('institutes as i')
            ->join('attendances as a', 'a.institute_id', '=', 'i.id')
            ->whereDate('a.created_at', $date)
            ->select('i.id', 'i.institute')
            ->distinct()
            ->orderBy('i.institute')
            ->get();

        $count = Attendance::whereDate('created_at', $date)->count();

        return response()->json([
            'schools' => $schools,
            'count' => $count
        ]);
    }
}
