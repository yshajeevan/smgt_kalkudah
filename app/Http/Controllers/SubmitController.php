<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\Employee;
use App\Models\Attendance;
use DataTables;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SubmitController extends Controller
{
    public function index(Request $request, $id)
    {
        if($id == '1'){
            $action = 'cadrerpt';
        } elseif($id == '2') {
            $action = 'cadrexport';
        } elseif($id == '3') {
            $action = 'cadredetailed';
        } elseif($id == '4') {
            $action = 'attencreate';
        } elseif($id == '5') {
            $action = 'schoolatten';
        } elseif($id == '6') {
            $action = 'teacherprofile';
        } elseif($id == '7') {
            $action = 'room_list';
        }
        $institutes = Institute::all();
        $uname = Auth::user()->roles->pluck('name')->implode(', ');

        // $cols = Schema::getColumnListing('institutes');
        $cols = Employee::where('designation_id', 8)
                        ->orWhere('designation_id', 13)
                        ->orWhere('designation_id', 22)
                        ->groupBy('cadresubject_id')->leftJoin('cadresubjects', 'cadresubjects.id', '=', 'cadresubject_id')
                        ->orderBy('cadresubjects.cadre')              
                        ->get();
        $items = DB::table('attendances')->wheredate('created_at',  Carbon::now()->format('Y-m-d'))->get();
        if(sizeof($items)){
            foreach($items as $item) {
                $listcompleted[]=$item->institute_id;
            }
        } else{
            $listcompleted=[];
        }
       
        $attendances = DB::table('institutes')->select('institute','id')->where('id', '<',69)->whereNotIn('id', $listcompleted)->get();

        return view('submitforms.submitform', compact('uname','institutes','action','cols','attendances'));
    }

    public function instituteSearch(Request $request)
    {
        // $input = $request->all();
        $search = $request->search;

        $datas = Institute::where('institute', 'like', '%' .$search . '%')->get();

        $dataModified = array();
        foreach ($datas as $data)
            {
                $dataModified[] = array("value"=>$data->id,"label"=>$data->institute);
            
            }
        return response()->json($dataModified);

    }
    
}
