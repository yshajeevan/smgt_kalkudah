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
        $countatten = Attendance::wheredate('created_at',  Carbon::now()->format('Y-m-d'))->count();
        if($request->ajax()) {
            //Zonal access single school every day attendance 
            if(isset($request->insid)) {
                $instid = $request->insid;
                $data = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', $instid)->orderBy('adate','desc'); 
            //Zonal access school wise today attendance   
            }else if ($request->currentPath == 'attendance-schools') {
                $data = Attendance::selectRaw('Date(created_at) as adate,principal,totstu,prstu,tottea,prtea,tottrainee,prtrainee,totnonacademic,prnonacademic,institute_id')
                ->orderBy('adate','desc')->with('institute');
            //Direct school access daily attendance   
            } else if($uname == 'Sch_Admin' && $request->currentPath == 'schoolatten') {
                $instid = Auth::user()->institute_id;
                $data = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', $instid)->orderBy('adate','desc');   
            //Every day zonal summary   
            }else if ($request->currentPath == 'zonalattendance') {
                $data = Attendance::selectRaw('Date(created_at) as adate,sum(principal) as principal, sum(totstu) as totstu,sum(prstu) as prstu,sum(tottea) as tottea,sum(prtea) as prtea,sum(tottrainee) as tottrainee,sum(prtrainee) as prtrainee,sum(totnonacademic) as totnonacademic,sum(prnonacademic) as prnonacademic')
                        ->groupBy('adate')->orderBy('adate','desc');
            }
            return Datatables::of($data) 
                    
                    ->addColumn('institute', function(Attendance $attendance) use($request)
                    {
                        if($request->currentPath != 'zonalattendance'){
                            return $attendance->institute->institute; 
                        }
                    })
                    ->editColumn('percstu', function($data_rem) {
                        if($data_rem->totstu != 0){
                            return '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:'.round(($data_rem->prstu / $data_rem->totstu) * 100,2).'%">'.round(($data_rem->prstu / $data_rem->totstu) * 100,2)."%".'</div></div>';
                        }else{
                            return 0;
                        }
                    })
                    ->editColumn('perctea', function($data_rem) {
                        if($data_rem->tottea != 0){
                            return '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:'.round(($data_rem->prtea / $data_rem->tottea) * 100,2).'%">'.round(($data_rem->prtea / $data_rem->tottea) * 100,2)."%".'</div></div>';
                        }else{
                            return 0;
                        }
                    })
                    ->editColumn('perctrainee', function($data_rem) {
                        if($data_rem->tottrainee != 0){
                            return '<div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:'.round(($data_rem->prtrainee / $data_rem->tottrainee) * 100,2).'%">'.round(($data_rem->prtrainee / $data_rem->tottrainee) * 100,2)."%".'</div></div>';
                        }else{
                            return 0;
                        }
                    })
                    ->editColumn('percnonacademic', function($data_rem) {
                        
                        if($data_rem->totnonacademic != 0){
                            return '<div class="progress"> <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:'.round(($data_rem->prnonacademic / $data_rem->totnonacademic) * 100,2).'%">'.round(($data_rem->prnonacademic / $data_rem->totnonacademic) * 100,2)."%".'</div></div>';
                        }else{
                            return 0;
                        }
                    })
                    ->editColumn('created_at', function($data_rem) {
                        return date('d F Y', strtotime($data_rem->adate));
                    })
                    ->filter(function ($instance) use ($request) {
                        if(!empty($request->get('from_date'))) {
                        // $instance->whereBetween('created_at', array($request->from_date, $request->to_date));
                        $instance->wheredate('created_at', $request->from_date);
                        } else if($request->currentPath != 'zonalattendance') {
                        $instance->wheredate('created_at',  Carbon::now()->format('Y-m-d'));
                        }
                        
                        if (!empty($request->get('search'))) {
                             $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('institute_id', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['percstu','perctea','perctrainee','percnonacademic'])
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('attendance.index', compact('countatten','uname','currentPath','insid','institute'));
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
    public function createlink($instid)
    {
        $instid = $instid;
    
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
        [
            'prboys'=>'lte:'.(int)$request->totboys,
            'prgirls'=>'lte:'.(int)$request->totgirls,
            'prtea'=>'lte:'.(int)$request->tottea,
            'prtrainee'=>'lte:'.(int)$request->tottrainee,
            'prnonacademic'=>'lte:'.(int)$request->totnonacademic,
            'prstu'=> 'required',
            'totstu'=> 'required',
        ],
        [   
            'prboys.lte'         => 'Presented boys must be equal or less than total boys.',
            'prgirls.lte'        => 'Presented girls must be equal or less than total girls.',            
            'prtea.lte'          => 'Presented teachers must be equal or less than total teachers.',            
            'prtrainee.lte'      => 'Presented DOs must be equal or less than total DOs.',   
            'prnonacademic.lte'  => 'Presented NonAc staff must be equal or less than total NonAc staff.'            
        ]);
        $attendance = new Attendance();

        //Prevent insertion in mid 12.00 to 7.30am
        $time_start = "24:00:00";
        $time_end = "07:30:00";
        $ctime = date("H:i:s");
        if($ctime < $time_start && $ctime > $time_end) {
            if(!Attendance::where('institute_id', $request->institute_id)->whereDate('created_at', Carbon::now()->format('Y-m-d'))->exists()){
                $status = $attendance->create($request->except('_token') + ['updated_by' => Auth::user()->institute_id]);
                if($request->principal == 0)
                    $attendance->principal = 0;
                else{
                     $attendance->principal = 1;
                }
                    if($status){
                        $countatten = Attendance::wheredate('created_at',  Carbon::now()->format('Y-m-d'))->count();
                        if($countatten == 68) {
                            Excel::store(new AttendanceExport, 'attendance.xlsx', 'excel_uploads'); //excel_uploads => filesystem path
                            $files = [
                                base_path('backend/attendance.xlsx'),
                            ];
                            $data = array('name'=>"BWZEO");
                            $to = ['mineducationep@gmail.com','plan@edudept.ep.gov.lk','yshajeevan@gmail.com'];
                            $subject = 'Attendance Batticaloa West-'.Carbon::now()->format("Y-m-d");
                            Mail::send(['text'=>'mail.attendance'], $data, function($message) use($files,$subject,$to) {
                                $message->to($to, 'Attendance')->subject($subject);
                                $message->from('baw@edudept.ep.gov.lk','Batticaloa West');
                                foreach ($files as $file){
                                    $message->attach($file);
                                }
                            });
                            $today = Carbon::now()->format('Y-m-d');
                            // Add activity logs
                            $user = Auth::user();
                            activity()
                            ->performedOn($attendance)
                            ->causedBy($user)
                            ->log('Attendance: Attendance of '.$today.' is completed');
                            
                        }
                        if(Auth::user()->hasRole('Sch_Admin')){
                            return redirect()->route('attendance.submitrespose');
                        } else{
                            request()->session()->flash('success','Attendance Successfully Created!');
                            return redirect()->route('attendance.list');
                        }
                    }else{

                    }
            }  else {
                return redirect()->back()->with(Session::flash('alert-danger', 'You have already entered the data'));
            }
        }  else {
             return redirect()->back()->with(Session::flash('alert-info', 'You can not update attendance between 12.00am to 7.30am'));
        }
    }
    public function submitrespose(){
        $prstu = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('prstu');
        $totstu = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('totstu');
        if($totstu > 0){
            $percstu = round(($prstu / $totstu) * 100,2);
        } else {
            $percstu = 0;
        }
        
        $prtea = Attendance::selectRaw('*,sum(prtea + prtrainee) as prteachers,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('prteachers');
        $tottea = Attendance::selectRaw('*,sum(tottea + tottrainee) as totalteachers,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('totalteachers');
        if($tottea > 0){
            $perctea = round(($prtea / $tottea) * 100,2);
        } else {
            $perctea = 0;
        }
        
        $prnonac = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('prnonacademic');
        $totnonac = Attendance::selectRaw('*,Date(created_at) as adate')->where('institute_id', Auth::user()->institute_id)->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->value('totnonacademic');
        if($totnonac > 0){
            $percnonac = round(($prnonac / $totnonac) * 100,2);
        } else {
            $percnonac = 0;
        }
        
        return view('attendance.submitrespose',compact('percstu','perctea','percnonac','tottea','totnonac'));
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
}
