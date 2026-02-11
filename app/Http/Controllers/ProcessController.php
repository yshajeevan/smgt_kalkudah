<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Process;
use App\Models\ServiceLog;
use App\Models\User;
use App\Models\Cfactivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Auth;
use Carbon\CarbonInterval;
use App\Models\Message;
use DateTime;
use Illuminate\Support\Str;
use App\Models\ZonalEvent;
use App\Models\Institute;
use App\Models\ServiceTransfer;
use Mail;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:process-list', ['only' => ['index']]);
         $this->middleware('permission:process-create', ['only' => ['create','store']]);
         $this->middleware('permission:process-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:process-delete', ['only' => ['destroy']]);
    }
    public function index($id)
    {
        return view('service_mgt.index')->with('pageid', $id);
    }
    public function getprocess(Request $request, $pageid)
    {

        $columns = array( 
                            0 =>'id', 
                            1 =>'surname',
                            2 =>'designation',
                            3=> 'service',
                            4=> 'cres',
                            5=> 'progress',
                            6=> 'id',
                        );
        $userid = Auth::user()->id;                
        if($pageid == '1'){
            $totalData = Process::where('user_id','=',$userid)->count();
        } else if($pageid == '2'){
            $totalData = Process::where('user_id','!=','0')->count();
        } else if($pageid == '3') {
            $totalData = Process::where('user_id','=','0')->count();
        } else if($pageid == '4') {
            $totalData = Process::where('processes.pendingchk',1)->count();
        }

        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {        
            $durations = ServiceLog::select(DB::raw('process_id,sum(time_allocated) as timeallocated,sum(time_taken) as timespent'))
                ->whereNull('on_hold')->groupBy('process_id')->orderBy('process_id');

            $query = Process::leftjoinSub($durations, 'durations', function ($join) {
                    $join->on('processes.id', '=', 'durations.process_id');
                    })->select('processes.id','processes.remarks','processes.employee_id','processes.service_id','processes.user_id','durations.timespent','durations.timeallocated',
                    DB::raw('last_updated_user / ((CASE WHEN smgt_services.user1_id != 0 THEN 1 ELSE 0 END) +  (CASE WHEN smgt_services.user2_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user3_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user4_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user5_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user6_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user7_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user8_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user9_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user10_id != 0 THEN 1 ELSE 0 END)) * 100 as progress'))
                    ->join('services', 'services.id', '=', 'processes.service_id')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir);

            if ($pageid == '1'){
                $processes = $query->where('processes.user_id',$userid)->get();
            } else if($pageid == '2'){
                $processes = $query->where('processes.user_id','!=','0')->get();
            } else if($pageid == '3') {
                $processes = $query->where('processes.user_id','0')->get();
            } else if($pageid == '4') {
                $processes = $query->where('processes.pendingchk',1)->get();
            }
                        
        }
        else {
            $search = $request->input('search.value'); 

            $durations = ServiceLog::select(DB::raw('process_id,sum(time_allocated) as timeallocated,sum(time_taken) as timespent'))
                ->whereNull('on_hold')->groupBy('process_id')->orderBy('process_id');

            $query = Process::leftjoinSub($durations, 'durations', function ($join) {
                    $join->on('processes.id', '=', 'durations.process_id');
                    })->select('processes.id','processes.employee_id','processes.service_id','processes.user_id','durations.timespent','durations.timeallocated',
                    DB::raw('((CASE WHEN processtime1 IS NOT NULL THEN 1 ELSE 0 END) +  (CASE WHEN processtime2 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime3 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime4 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime5 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime6 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime7 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime8 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime9 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime10 IS NOT NULL THEN 1 ELSE 0 END)) / ((CASE WHEN smgt_services.user1_id != 0 THEN 1 ELSE 0 END) +  (CASE WHEN smgt_services.user2_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user3_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user4_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user5_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user6_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user7_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user8_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user9_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN smgt_services.user10_id != 0 THEN 1 ELSE 0 END)) * 100 as progress'))
                    ->join('services', 'services.id', '=', 'processes.service_id')
                                ->where('processes.id','LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir);

            if ($pageid == '1'){
                $processes = $query->where('processes.user_id',$userid)->get();
            } else if($pageid == '2'){
                $processes = $query->where('processes.user_id','!=','0')->get();
            } else if($pageid == '3') {
                $processes = $query->where('processes.user_id','0')->get();
            } else if($pageid == '4') {
                $processes = $query->where('processes.pendingchk',1)->get();
            }

            if ($pageid == '1'){
                $totalFiltered = Process::where('processes.id','LIKE',"%{$search}%")->where('processes.user_id','=',$userid)->count();
            } else if($pageid == '2'){
                $totalFiltered = Process::where('processes.id','LIKE',"%{$search}%")->where('processes.user_id','!=','0')->count();
            } else if($pageid == '3') {
                $totalFiltered= Process::where('processes.id','LIKE',"%{$search}%")->where('processes.user_id','=','0')->count();
            } else if($pageid == '4') {
                $totalFiltered= Process::where('processes.id','LIKE',"%{$search}%")->where('processes.pendingchk',1)->count();
            }
          
        }

        $data = array();
        if(!empty($processes))
        {
            foreach ($processes as $process)
            {
                
                $edit =  route('process.edit',$process->id);
                $delete =  route('process.delete',$process->id);

                $title = $process->employee->title;
                $initial = $process->employee->initial;
                $surname = $process->employee->surname;
                $namewtinitial = $title.".".$initial.".".$surname;
                //Nested data
                $nestedData['id'] = $process->id;
                $nestedData['surname'] = $namewtinitial;
                $nestedData['designation'] = $process->employee->designation->designation;
                if(!empty($process->remarks)){
                    $nestedData['service'] = $process->service->service."-".$process->remarks;
                } else{
                    $nestedData['service'] = $process->service->service;
                }
                
                if($pageid != '3'){
                $nestedData['cres'] = $process->user->name;
                } else {
                $nestedData['cres'] = $process->employee->institute->institute;    
                }
                $nestedData['progress'] = $process->progress;
                if($process->timespent == 0) { 
                    $nestedData['emogi'] = "new.png";
                } else if($process->timespent > $process->timeallocated) {  
                    $nestedData['emogi'] = "emogi_sad.png";
                } else if($process->timespent < $process->timeallocated) {
                    $nestedData['emogi'] = "emogi_smilie.png";
                } 
                $nestedData['timeallocated'] = $process->timeallocated;
                // $nestedData['created_at'] = date('j M Y h:i a',strtotime($process->created_at));

                //Permissions for buttons
                $user = auth()->user();
                $btn = '';
                if ($user->can('process-edit')) {
                    $btn = "<a href='{$edit}' title='edit' class='btn btn-primary btn-sm float-left mr-1' style='height:30px; width:30px;border-radius:50%' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                }
                if ($user->can('process-delete')) {
                    $btn = $btn."<a href='{$delete}' onclick='return confirm(`Are you sure want to delete this record?`)' class='btn btn-danger btn-sm float-left mr-1' style='height:30px; width:30px;border-radius:50%' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                }
                $nestedData['options'] = $btn;

                $data[] = $nestedData;

            }
        }

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );

        echo json_encode($json_data); 

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $institutes = Institute::select('id','institute')->orderBy('institute')->get();
        $services = Service::all(); 
        $maxid = DB::table('processes')->max('id');
        $cf = Cfactivity::select('id','cfid','activity','estimated_cost')->where('expenditure', '=', 0)->get();
        return view('service_mgt.create',compact('services','maxid','cf','institutes'));
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
            'empid' => 'required',
            'service_id' => 'required',
        ]);
        $currentdatetime = Carbon::now();
        
        $process = New Process();
        $process->employee_id = $request->input('empid');
        $process->service_id = $request->input('service_id');
        if(!empty($request->input('remarks_mf'))){
            if(!empty($request->input('remarks_mt'))){
                $process->remarks = 'from '.$request->input('remarks_mf').' to '.$request->input('remarks_mt');
            } else {
                $process->remarks = 'for the month of '.$request->input('remarks_mf');
            }
        } 
        if(!empty($request->input('remarks_o'))){
            $process->remarks = 'for the date of '.$request->input('remarks_o');
        } 
        //Update next resposible officer's ID
        if(!empty($request->input('nres_id'))){
            $service = Service::where('id', $request->input('service_id'))->first();
            if($service->user2_id == 31){
                $nxtres = $process->employee->institute1->pfclerk_id; 
                $LUU = 1;
            }elseif($service->user2_id == 32){
                $nxtres = $process->employee->institute1->acctclerk_id;
                $LUU = 1;
            }else{
                if($service->user2->is_skipped && $service->user3->is_skipped){
                    if($service->user4_id == 31){
                        $nxtres = $process->employee->institute1->pfclerk_id; 
                        $LUU = 3;
                    }elseif($service->user4_id == 32){
                        $nxtres = $process->employee->institute1->acctclerk_id;
                        $LUU = 3;
                    }else{
                        $nxtres = $service->user4_id;
                        $LUU = 3;
                    }
                }elseif($service->user2->is_skipped){
                    if($service->user3_id == 31){
                        $nxtres = $process->employee->institute1->pfclerk_id; 
                        $LUU = 2;
                    }elseif($service->user3_id == 32){
                        $nxtres = $process->employee->institute1->acctclerk_id;
                        $LUU = 2;
                    }else{
                        $nxtres = $service->user3_id;
                        $LUU = 2;
                    }
                }else{
                    $nxtres = $service->user2_id;
                    $LUU = 1;
                }
            }
            $process->user_id = $nxtres;
            $process->last_updated_user= $LUU;
        }else {
            $process->user_id = 0;
        }
  
        //Update current user's processing time
        $process->user1_id = $request->input('user1_id');
        $process->processtime1 = $currentdatetime->toDateTimeString();
        
        //Insert unique key for feedback 
        $randomString = Str::random(30);
        $process->uniquekey = $randomString;
        
        $status=$process->save();

        if(!empty($request->input('msg'))){
        $message = New Message();
        $message->sender_id = Auth::id();
        $message->reciever_id = $request->input('nres_id');
        $maxid = DB::table('processes')->max('id');
        $message->subject = 'Starting of New Service. Process ID: '.$maxid;
        $message->message = $request->input('msg');
        $status2=$message->save();
        }
        
        if($status){
            $maxid = DB::table('processes')->max('id');
            $this->notify_nextUser($request, $maxid);
            $this->notify_client($request, $maxid);
        
            //get slug store
            if(!empty($request->input('slug'))){
                $func = $request->input('slug');
                $this->$func($request, $maxid);
            }
            request()->session()->flash('success','Successfully added process');
            return redirect()->back()->with('alert', 'Process ID: '.$maxid);
        } else {
            request()->session()->flash('error','Error occurred while adding process');
        }
    }
    public function notify_nextUser($request, $maxid){
        //Firebase Notification
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::where('id','=',$request->input('nres_id'))->pluck('device_key');

        $serverKey = 'AAAAPlyPkn4:APA91bHTfxIwVm6rquyBGGUcVkhsxKiylVfwYX1MIqDpZ_TiT0tJcj4eqOnn-xsJ36f7KRg0MXNm0_5k7uilalJtUTpJdl4A72hL4RaFRCfOmp6ALotbPABM81Ro5oPPCmfa-lt9ki95';
  
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'SMGT_Service',
                "body" => 'You have a new process with Ref.No: '.$maxid,   
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
        // FCM response
        // dd($result);
    }
    public function notify_client($request, $maxid){
        //Firebase Notification
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = Employee::where('id',$request->input('empid'))->pluck('device_key');

        $serverKey = 'AAAAPlyPkn4:APA91bHTfxIwVm6rquyBGGUcVkhsxKiylVfwYX1MIqDpZ_TiT0tJcj4eqOnn-xsJ36f7KRg0MXNm0_5k7uilalJtUTpJdl4A72hL4RaFRCfOmp6ALotbPABM81Ro5oPPCmfa-lt9ki95';
  
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'SMGT_Service',
                "body" => 'You have a new service with Ref.No: '.$maxid,   
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
        // FCM response
        // dd($result);
    }
    public function transfer($request, $maxid){
        if(empty($request->input('cntres_id'))){
            $input['process_id'] = $maxid;
            $input['employee_id'] = $request->input('empid');
            $input['transfer_from'] = $request->input('institute_id');
            $input['transfer_to'] = $request->input('transfer_to');
            $input['transfer_type'] = $request->input('transfer_type');
            $input['letter_date'] = $request->input('letter_date');
            $input['effect_from'] = $request->input('effect_from');
            $status = ServiceTransfer::create($input);
        } else {
            if($request->input('service_type') == 3){
                ServiceTransfer::where('id', $request->transfer_id)->update(['is_approved' => 1]);
            }elseif($request->input('service_category') == 'finishing'){
                ServiceTransfer::where('id', $request->transfer_id)->update(['is_printed' => 1]);
            }
        }
    }
    
    public function print_transfer($id){
        $transfer = ServiceTransfer::Find($id);
        return view('service_mgt.services.partials.transfer.print',compact('transfer'));
    }
    
    public function differ_transfer(){
        return $transfer = ServiceTransfer::Find(1);
    }
    
    public function cffund($request, $maxid){
        if(empty($request->input('cntres_id'))){
            Cfactivity::where('id', $request->cfund)->update(['is_done' => 1, 'process_id' => $maxid, 'expenditure' => $request->input('expenditure')]);
        } else {
          
        }
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
        $process = Process::find($id);
        $institutes = Institute::select('id','institute')->get();
        $cf = Cfactivity::select('id','cfid','activity','estimated_cost')->get();
        $query = Process::where('id',$id);
        $timetaken_data = array(
          "timetaken2" => $this->businessTime($query->value('processtime1'), $query->value('processtime2')),
          "timetaken3" => $this->businessTime($query->value('processtime2'), $query->value('processtime3')),
          "timetaken4" => $this->businessTime($query->value('processtime3'), $query->value('processtime4')),
          "timetaken5" => $this->businessTime($query->value('processtime4'), $query->value('processtime5')),
          "timetaken6" => $this->businessTime($query->value('processtime5'), $query->value('processtime6')),
          "timetaken7" => $this->businessTime($query->value('processtime6'), $query->value('processtime7')),
          "timetaken8" => $this->businessTime($query->value('processtime7'), $query->value('processtime8')),
          "timetaken9" => $this->businessTime($query->value('processtime8'), $query->value('processtime9')),
          "timetaken10" => $this->businessTime($query->value('processtime9'), $query->value('processtime10')),
        );

        $duration = ServiceLog::select(DB::raw('process_id,sum(time_allocated) as timeallocated,sum(time_taken) as timespent'))
                ->whereNull('on_hold')->where('process_id','=',$id)->first();

        $countres = Service::select(DB::raw('(CASE WHEN user1_id != 0 THEN 1 ELSE 0 END) +  (CASE WHEN user2_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user3_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user4_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user5_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user6_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user7_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user8_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user9_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user10_id != 0 THEN 1 ELSE 0 END) AS countres'))
        ->where('id', $process->service_id)->pluck('countres')->implode(', ');
        
        $countproc = Process::select(DB::raw('last_updated_user AS countproc'))->where('id', $process->id)->pluck('countproc')->implode(', ');

        $service = Service::where('id', $process->service_id)->first();

        // Get next responsible officer ID
        $nextRes = "user".($countproc + 2);
        if(!empty($service->$nextRes->id)){
            if($service->$nextRes->id == 31){
                $nxtres = $process->employee->institute1->pfclerk_id; 
            }elseif($service->$nextRes->id == 32){
                $nxtres = $process->employee->institute1->acctclerk_id;
            }else{
                $nxtres = $service->$nextRes->id;
            }
        }else{
           $nxtres = 0; 
        }
        
        // Get Current responsible officer 
        $cntres = "user".($countproc + 1);
        if(!empty($service->$cntres->id)){
            if($service->$cntres->id == 31){
                $cntres_id = $process->employee->institute1->pfclerk_id; 
            }elseif($service->$cntres->id == 32){
                $cntres_id = $process->employee->institute1->acctclerk_id;
            }else{
                $cntres_id = $service->$cntres->id;
            }
        } else {
            $cntres_id = 0;
        }
        $cntprocess = "processtime".($countproc + 1);
        $prevprocess = "processtime".($countproc);
        $cntrestime = "res".($countproc + 1)."time";
        $cntrestype = "servicetype".($countproc + 1)."_id";
        $cntrescategory = trim($cntrestype,'_id');
        $percentage = ($countproc/$countres)*100;

        return view('service_mgt.edit', compact('process','duration','cntres','cntrestype','nxtres','cntprocess','service','percentage','cntrestime','timetaken_data','institutes','prevprocess',
        'cntrescategory','cntres_id','cf'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
     public function isBusinessDay(DateTime $date)
        {
        //Weekends
        if ($date->format('N') > 5) {
            return false;
        }

        $getholidays = ZonalEvent::select('title','start')->where('isholiday',1)->get();      
        $array_holidays = [];
                    
        if(count($getholidays) > 0)
        {
            foreach ($getholidays as $holiday) {
                
                $array_holidays[$holiday->title] = new DateTime(date($holiday->start));     
            }
        }
        $holidays = $array_holidays;

        foreach ($holidays as $holiday) {
            if ($holiday->format('Y-m-d') === $date->format('Y-m-d')) {
                return false;
            }
        }

        //December company holidays
        if (new DateTime(date('Y') . '-12-15') <= $date && $date <= new DateTime((date('Y') + 1) . '-01-08')) {
            return false;
        }

        // Other checks can go here

        return true;
    }
    
    public function businessTime($start, $end)
    {
        $start = $start instanceof \DateTime ? $start : new DateTime($start);
        $end = $end instanceof \DateTime ? $end : new DateTime($end);
        $dates = [];
    
        $date = clone $start;
    
        while ($date <= $end) {
    
            $datesEnd = (clone $date)->setTime(23, 59, 59);
    
            if ($this->isBusinessDay($date)) {
                $dates[] = (object)[
                    'start' => clone $date,
                    'end'   => clone ($end < $datesEnd ? $end : $datesEnd),
                ];
            }
    
            $date->modify('+1 day')->setTime(0, 0, 0);
        }
    
        return array_reduce($dates, function ($carry, $item) {
    
            $businessStart = (clone $item->start)->setTime(8, 30, 0);
            $businessEnd = (clone $item->start)->setTime(16, 15, 0);
    
            $start = $item->start < $businessStart ? $businessStart : $item->start;
            $end = $item->end > $businessEnd ? $businessEnd : $item->end;
    
            //Diff in minutes
            return $carry += (max(0, $end->getTimestamp() - $start->getTimestamp())/60);
        }, 0);
    }
    
    public function update(Request $request, $id, $cntprocess, $cntres)
    {
        $process = Process::findOrFail($id);

        if($request->has('pendingchk')){
            $process->pendingchk = 1;
            $process->despending = $request->input('despending');
        } else {
            $currentdatetime = Carbon::now();
            $process->$cntprocess = $currentdatetime->toDateTimeString();
    
            $process->pendingchk = '0';
            $process->despending = '';

            //Update current responsible user
            $LUU = Process::where('id','=', $id)->value('last_updated_user');
            $curntres = "user".($LUU + 1)."_id";
            $process->$curntres = $request->input('cntres_id');
            
            //Update current user time
            $processtime = "processtime".($LUU + 1);
            $process->$processtime = $currentdatetime->toDateTimeString();
            
            //Undate next responsible officer
            
            $LUU = Process::where('id','=', $id)->value('last_updated_user');
            $n1user = "user".($LUU + 2);
            $n2user = "user".($LUU + 3);

            $nextres1 = "user".($LUU + 2)."_id";
            $nextres2 = "user".($LUU + 3)."_id";
            $nextres3 = "user".($LUU + 4)."_id";
            $service = Service::where('id', $process->service_id)->first();
            if(!empty($service->$nextres1)){    
                if($service->$nextres1 == 31){
                    $nxtres = $process->employee->institute1->pfclerk_id; 
                    $LUU = $LUU + 1;
                }elseif($service->$nextres1== 32){
                    $nxtres = $process->employee->institute1->acctclerk_id;
                    $LUU = $LUU + 1;
                }else{
                    if(!empty($service->$n1user) && !empty($service->$n2user)){
                        if($service->$n1user->is_skipped && $service->$n2user->is_skipped){
                            if($service->$nextres3 == 31){
                                $nxtres = $process->employee->institute1->pfclerk_id; 
                                $LUU = $LUU + 3;
                            }elseif($service->$nextres3 == 32){
                                $nxtres = $process->employee->institute1->acctclerk_id;
                                $LUU = $LUU + 3;
                            }else{
                                $nxtres = $service->$nextres3;
                                $LUU = $LUU + 3;
                            }
                        }elseif($service->$n1user->is_skipped){
                            if($service->$nextres2 == 31){
                                $nxtres = $process->employee->institute1->pfclerk_id; 
                                $LUU = $LUU + 2;
                            }elseif($service->$nextres2 == 32){
                                $nxtres = $process->employee->institute1->acctclerk_id;
                                $LUU = $LUU + 2;
                            }else{
                                $nxtres = $service->$nextres2;
                                $LUU = $LUU + 2;
                            }
                        } else{
                            $nxtres = $service->$nextres1;
                            $LUU = $LUU + 1;
                        }
                    }else{
                        $nxtres = $service->$nextres1;
                        $LUU = $LUU + 1;
                    }
                }
            }else {
                $nxtres = 0;
            }
            $process->user_id = $nxtres;
            $process->last_updated_user = $LUU;
            
            //Insert unique key for feedback 
            $randomString = Str::random(30);
            if(empty($process->uniquekey)){
                $process->uniquekey = $randomString;
            }
        }
            
        //Start Save in to Service_log(for user time spent calculation)
            $servicelog = New ServiceLog();
            $servicelog->user_id = Auth::user()->id;
            $servicelog->process_id = $id;
            $servicelog->time_allocated = $request->input('cntrestime');
            $servicelog->time_taken =  $this->businessTime($request->input('prevproctime'), Carbon::now());
            $servicelog->previous_time =  $request->input('prevproctime');
            $servicelog->actual_time =  Carbon::now();
            if($process->pendingchk == 1){
                $servicelog->on_hold = $process->updated_at;
            }
        $servicelog->save();  
        
        $status=$process->save();
            
        if($status){
            $maxid = $process->id;
            if($request->input('nres_id') != 0){
                $this->notify_nextUser($request, $maxid);
            }
            
            if(!empty($request->input('slug'))){
                $func = $request->input('slug');
                $this->$func($request,$maxid);
            }
            request()->session()->flash('success','Successfully updated');
            return redirect()->route('process.index', 1);
        }
        else{
            request()->session()->flash('error','Error occured while updating');
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
        $process = Process::findorFail($id);
        $status = $process->delete();
        
        if($status){
            request()->session()->flash('success','Process successfully deleted');
            return redirect()->back();
        }
        else{
            request()->session()->flash('error','There is an error while deleting process');
        }
        
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
                                        'empno'=>$data->empno,
                                        'fullname'=>$data->namewithinitial,
                                        'address'=>$data->peraddress,
                                        'designation'=>$data->designation->designation,
                                        'insid'=>$data->institute_id,
                                        'institute'=>$data->institute->institute,
                                        'pfclerk' => (!empty($data->institute1->pfclerk->name) ? $data->institute1->pfclerk->name : 'undefined'),
                                        'acctclerk' => (!empty($data->institute1->acctclerk->name) ? $data->institute1->acctclerk->name : 'undefined'),
                                        'pfclerk_id' => (!empty($data->institute1->pfclerk_id) ? $data->institute1->pfclerk_id : 'undefined'),
                                        'acctclerk_id' => (!empty($data->institute1->acctclerk_id) ? $data->institute1->acctclerk_id : 'undefined'),
                                        'cadresubject' => (!empty($data->cadresubject->cadre) ? $data->cadresubject->cadre : 'undefined'),
                                        'cadrecode' => (!empty($data->cadresubject->cadre_code) ? $data->cadresubject->cadre_code : 'undefined'),
                                        'gndivision' => (!empty($data->gndivision->gn) ? $data->gndivision->gn : 'undefined'),
                                        'gpslocation' => (!empty($data->gndivision->gpslocation) ? $data->gndivision->gpslocation : 'undefined'),
                                        'mobile' => $data->mobile,
                                    );
            
            }
        return response()->json($dataModified);
    }

    public function getService(Request $reqest)
    {
        $serviceid = $reqest->serviceid;

        $data = Service::with('user1','user2','user3')->Find($serviceid);

        $emplid = $reqest->empid;
        $employee = Employee::LastSync()->findOrFail($emplid);
        // Get photo of PF clerk
 
        return response()->json(['res1' => $data->user1_id,
                        'res1time' => $data->res1time,
                        'res1name' => $data->user1->name,
                        'res1photo' => $data->user1->photo,
                        'res2' => $data->user2_id,
                        'res2time' => $data->res2time,
                        'res2name' => $data->user2->name,
                        'res2photo' => $data->user2->photo,
                        'pfname' => $employee->institute1->pfclerk->name,
                        'pfclkid' => $employee->institute1->pfclerk_id,
                        'pfphoto' => $employee->institute1->pfclerk->photo,
                        'acctname' => $employee->institute1->acctclerk->name,
                        'acctclkid' => $employee->institute1->acctclerk_id,
                        'acctphoto' => $employee->institute1->acctclerk->photo,
                        'servicename' => $data->service,
                        'remarks' => $data->remarks,
                        'slug' => $data->slug,
                        ]);
    }
    
    public function feedback($scale){
        return view('feedback.feedback',compact('scale'));
    }

    public function updatefeedback(Request $request){
        $scale = $request->scale;
        $uniqkey = $request->uniqkey;

        $process = Process::where('uniquekey', '=', $uniqkey)->first();
        
        if(!empty($process->feedbackscale)){
            $lastupdate = $process->updated_at;
            if($lastupdate->diffInMinutes(Carbon::now()) < 30){
                $process->feedbackscale = $scale;
                $status = $process->save();
                if($status){
                    return response()->json(['status' => 'success']);
                } else {
                    return response()->json(['status' => 'error']);
                }
            }
        } else {
            $process->feedbackscale = $scale;
            if($status = $process->save()){
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error']);
            }
        }      
    }
    
    public function send_mail(Request $request)
    {
        $data = array('name'=>"Virat Gandhi");
   
      Mail::send(['text'=>'mail.test_mail'], $data, function($message) {
         $message->to('yshajeevan@gmail.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('bwddeplan@gmail.com','Virat Gandhi');
      });
      echo "Basic Email Sent. Check your inbox.";
    }
    
    public function bulkedit(){
        $items = Process::select('*',DB::raw('(CASE WHEN processtime1 IS NOT NULL THEN 1 ELSE 0 END) +  (CASE WHEN processtime2 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime3 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime4 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime5 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime6 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime7 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime8 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime9 IS NOT NULL THEN 1 ELSE 0 END) + (CASE WHEN processtime10 IS NOT NULL THEN 1 ELSE 0 END) AS countproc'))
        ->where('user_id',Auth::user()->id)->where('user_id','!=','0')->with('service')->orderBy('service_id')->orderBy('created_at','asc')->get();
        $services = Process::where('user_id',Auth::user()->id)->where('user_id','!=','0')->select('service_id')->distinct()->get();
        $count = $items->count();
        return view('service_mgt.bulkedit', compact('items','services','count'));
    }
    
    public function bulkupdate(Request $request){
        $selected = $request->input('selected');
        foreach ($selected as $row) {
            if(!empty($row['id'])){
                $cuser = 'user'.($row['countproc'] + 1).'_id';
                $ptime = 'processtime'.($row['countproc'] + 1);
                
                $process = Process::find($row['id']); 
                $service = Service::where('id', $row['serviceid'])->first();
                $nextRes = "user".($row['countproc'] + 2);
                if(!empty($service->$nextRes->id)){
                    if($service->$nextRes->id == 31){
                        $nxtres = $process->employee->institute1->pfclerk_id; 
                    }elseif($service->$nextRes->id == 32){
                        $nxtres = $process->employee->institute1->acctclerk_id;
                    }else{
                       $nxtres = $service->$nextRes->id;
                    }
                }else{
                  $nxtres = 0; 
                }
                $process->user_id = $nxtres; 
                $process->$cuser = Auth::user()->id; 
                $process->$ptime = Carbon::now();
                $process->pendingchk = 0; 
                $process->save();
                
            }
        }
        
        request()->session()->flash('success','Processes successfully updated');
        return redirect()->back();
       
        
    }
}
