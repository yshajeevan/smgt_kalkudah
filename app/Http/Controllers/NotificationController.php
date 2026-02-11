<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activitylog;
use App\Models\Activityreadlog;
use Helper;
use DataTables;
use Auth;

class NotificationController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:employee-list', ['only' => ['index']]);
         $this->middleware('permission:employee-create', ['only' => ['create','store']]);
         $this->middleware('permission:employee-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:employee-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) 
        {
            $data = Helper::activitylog();
            return Datatables::of($data)
                    ->editColumn('updated_at', function($data_rem) {
                        return date('d F Y', strtotime($data_rem->updated_at));
                    })
                    ->addColumn('action', function($row){
                        $show =  route('notif.show',$row->id);
                        $delete =  route('cffund.destroy',$row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('employee-edit')) {
                            $btn = "<a href='{$show}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('employee-delete')) {
                            $btn = $btn."<a href='{$delete}' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                        }
                         return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        // return $data;
        return view('notification.index');
        
    }
    public function show(Request $request, $id)
    {
        $notification = Helper::activitylog()->find($id);

        if($notification){
            $read = New Activityreadlog();

            $read->activitylog_id = $id;
            $read->user_id = Auth::user()->id;
            $read->save();
        
            return view('notification.show')->with('notification',$notification);
        }
        else{
            return back();
        }
    }
    public function delete($id)
    {
       //
    }
}
