<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\User;
use App\Models\Process;
use App\Models\Service;
use App\Models\Institute;
use App\Models\Employee;
use App\Models\ServiceLog;
use App\Rules\MatchOldPassword;
use Hash;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;
use DB;
use DataTables;
use Excel;
use TeamPickr\DistanceMatrix\DistanceMatrix;
use TeamPickr\DistanceMatrix\Licenses\StandardLicense;
use App\Models\Testprocess;
use App\Models\TestserviceOrder;
use App\Models\TestprocessOrder;
use Auth;
use DateTime;
use App\Models\ZonalEvent;

class HomeController extends Controller
{
    public function index(){
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
        $array[] = ['Name', 'Number'];
        foreach($data as $key => $value)
        {
        $array[++$key] = [$value->day_name, $value->count];
        }
        $countservices = Service::count('id');
        $activeusers = User::where('status','active')->count('id');
        $countstaff = Employee::where('status','active')->count('id');
        $process = Process::all();
        $staffsummary = Process::where('pendingchk','=',0)->select(DB::raw("count('id') as countid, user_id"))->with('user')->groupBy('user_id')->get()->where('user_id','!=',0);
        $feedbacks =  Process::where('user_id','=',0)->select(DB::raw("count('id') as totaldone,sum(if((feedbackscale = '5'),1,0)) AS scale5,sum(if((feedbackscale = '4'),1,0)) AS scale4,sum(if((feedbackscale = '3'),1,0)) AS scale3,sum(if((feedbackscale = '2'),1,0)) AS scale2,sum(if((feedbackscale = '1'),1,0)) AS scale1, service_id"))->groupBy('service_id')->with('service')->orderby('service_id')->get();
     
                
        //Time Analysis of Services 
        $proc = Process::where('user_id', '=', 0)->join('services', 'services.id', '=', 'service_id')->selectRaw('smgt_processes.id, smgt_services.service');
        $servicesummary = ServiceLog::joinSub($proc, 'process', function ($join) {
                    $join->on('process_id', '=', 'process.id');
        })->groupBy('service')->select('process.service', DB::raw('sum(time_taken) as timetaken'), DB::raw('sum(time_allocated) as timeallocated'))->get();
        
        $countprocess = Process::select('id', 'created_at')
                ->get()
                ->groupBy(function($date) {
                    //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
                    return Carbon::parse($date->created_at)->format('m'); // grouping by months
                });

        $month = [];
        $processcount = [];

        foreach ($countprocess as $key => $value) {
            $month[(int)$key] = count($value);
        }

        for($i = 1; $i <= 12; $i++){
            if(!empty($month[$i])){
                $processcount[] = $month[$i];    
            }else{
                $processcount[] = 0;    
            }
        }

        return view('home',compact('countservices','activeusers','countstaff','process','feedbacks','staffsummary','servicesummary'))
        ->with('users', json_encode($array))
        ->with('processcount', json_encode($processcount,JSON_NUMERIC_CHECK));
    }

    public function staffperf(Request $request){
        if ($request->ajax()) {
            $data = ServiceLog::where('on_hold','=', null)->groupBy('user_id')->selectRaw('round(sum(time_taken)/sum(time_allocated),2) as kpi, 
            (CASE WHEN (sum(time_taken)/sum(time_allocated)) < "0.85" THEN "8" 
            WHEN (sum(time_taken)/sum(time_allocated)) >= "0.85" and (sum(time_taken)/sum(time_allocated)) < "0.90" THEN "7"
            WHEN (sum(time_taken)/sum(time_allocated)) >= "0.90" and (sum(time_taken)/sum(time_allocated)) < "0.95" THEN "6"
            WHEN (sum(time_taken)/sum(time_allocated)) >= "0.95" and (sum(time_taken)/sum(time_allocated)) < "1" THEN "5"
            WHEN (sum(time_taken)/sum(time_allocated)) >= "1" and (sum(time_taken)/sum(time_allocated)) < "1.20" THEN "4" 
            WHEN (sum(time_taken)/sum(time_allocated)) >= "1.20" and (sum(time_taken)/sum(time_allocated)) < "1.40" THEN "3" 
            WHEN (sum(time_taken)/sum(time_allocated)) >= "1.40" and (sum(time_taken)/sum(time_allocated)) < "1.60" THEN "2"
            WHEN (sum(time_taken)/sum(time_allocated)) >= "1.60" and (sum(time_taken)/sum(time_allocated)) < "1.80" THEN "1"
            ELSE "0" END) as scale, user_id')->with('user')->orderBy('kpi', 'ASC');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('user', function(ServiceLog $servicelog)
                    {
                        if(!empty($servicelog->user->name)){
                            return $servicelog->user->name;
                        } else{
                            return 'Unknwon';
                        }
                    })
                    ->make(true);
        }
    }

    public function profile(){
        $profile=Auth()->user();
        // return $profile;
        return view('users.profile')->with('profile',$profile);
    }

    public function profileUpdate(Request $request,$id){
        $this->validate($request,[
            'photo'=>'dimensions:max_width=256'
        ]);
        
        // return $request->all();
        $user=User::findOrFail($id);
        $data=$request->all();
        $status=$user->fill($data)->save();
        if($status){
            request()->session()->flash('success','Successfully updated your profile');
        }
        else{
            request()->session()->flash('error','Please try again!');
        }
        return redirect()->back();
    }

    public function settings(){
        $data=Settings::first();
        return view('backend.setting')->with('data',$data);
    }

    public function settingsUpdate(Request $request){
        // return $request->all();
        $this->validate($request,[
            'short_des'=>'required|string',
            'description'=>'required|string',
            'photo'=>'required',
            'logo'=>'required',
            'address'=>'required|string',
            'email'=>'required|email',
            'phone'=>'required|string',
        ]);
        $data=$request->all();
        // return $data;
        $settings=Settings::first();
        // return $settings;
        $status=$settings->fill($data)->save();
        if($status){
            request()->session()->flash('success','Setting successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again');
        }
        return redirect()->route('admin');
    }

    public function changePassword(){
        return view('layouts.changePassword');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->route('/')->with('success','Password successfully changed');
    }

    // Pie chart
    public function userPieChart(Request $request){
        // dd($request->all());
        $data = User::select(\DB::raw("COUNT(*) as count"), \DB::raw("DAYNAME(created_at) as day_name"), \DB::raw("DAY(created_at) as day"))
        ->where('created_at', '>', Carbon::today()->subDay(6))
        ->groupBy('day_name','day')
        ->orderBy('day')
        ->get();
     $array[] = ['Name', 'Number'];
     foreach($data as $key => $value)
     {
       $array[++$key] = [$value->day_name, $value->count];
     }
    //  return $data;
     return view('backend.index')->with('course', json_encode($array));
    }

public function test(){
    $license = new StandardLicense('AIzaSyAHZUq9uPXq56Baa1yGZyUlmZ6WxinyFGQ');

    $origin = '7.7865426,81.6614256'; 
            $destination = '7.6766122,81.6614305';
            
    $dict = Institute::where('id','<',50)->select('id',DB::Raw("DistanceMatrix::license($license)->addOrigin('7.7865426,81.6614256')->addDestination('7.6766122,81.6614305')->request()->rows() as sum "))->get();
    return $dict;
    $rows = DistanceMatrix::license($license)->addOrigin($origin)->addDestination('7.6766122,81.6614305')->request()->rows();
    $elements = $rows[0]->elements();
    return $element = $elements[0]->distanceText();
   
    $dict = Institute::where('id','<',50)->pluck('gpslocation','id')->toarray();
    
    foreach($dict as $key => $val)
    {
        $origin = $val; 
        $destination = '7.6766122,81.6614305';

        $response = DistanceMatrix::license($license)
        ->addOrigin($origin)
        ->addDestination($destination)
        ->request();
       
        $rows = $response->rows();
        $elements = $rows[0]->elements();
        $element = $elements[0];
    
        $distance[$key] =  $element->distanceText();
    }  
    return $distance;
     
}
    
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

    public function sendsms($mobile, $email){
        $message = "Your service is completed";

        error_reporting(E_ALL);
        date_default_timezone_set('Asia/Colombo');
        $now = date("Y-m-d\TH:i:s");
        $username = "zeo_batti";
        $password = "@q123456";
        $digest=md5($password);

        $body = '{

        "messages": [
        {
        "clientRef": "",
        "number": "'.$mobile.'",
        "mask": "BATWESTZEO",
        "text": "'.$message.'",
        "campaignName":"TestPromo"
        }
        ]
        }';
        // return $body;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://richcommunication.dialog.lk/api/sms/send");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$body); //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = [
        'Content-Type: application/json',
        'USER: '.$username,
        'DIGEST: '.$digest,
        'CREATED: '.$now
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec ($ch);

        curl_close ($ch);
    }
}
