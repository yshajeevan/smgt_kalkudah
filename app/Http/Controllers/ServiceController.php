<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\User;
use DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class ServiceController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:service-show', ['only' => ['index','show']]);
         $this->middleware('permission:service-create', ['only' => ['create','store']]);
         $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:service-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::select('*', DB::raw('(CASE WHEN user1_id != 0 THEN 1 ELSE 0 END) +  (CASE WHEN user2_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user3_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user4_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user5_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user6_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user7_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user8_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user9_id != 0 THEN 1 ELSE 0 END) + (CASE WHEN user10_id != 0 THEN 1 ELSE 0 END) AS countres'),
                    DB::raw('(res1time) + (res2time) + (res3time) + (res4time) + (res5time)+ (res6time) + (res7time) + (res8time) + (res9time) + (res10time) as timeallocated'))
                    ->orderBy('service', 'asc');

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('timeallocated', function ($request) {
                        return CarbonInterval::minutes($request->timeallocated)->cascade()->forHumans(); // human readable format
                      })
                    ->addColumn('action', function($row){
                        $show =  route('service.show',$row->id);
                        $checklist =  route('checklist.index',$row->id);
                        $edit =  route('service.edit',$row->id);
                        $delete =  route('servicedel.destroy',$row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('service-show')) {
                            $btn = "<a href='{$show}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='show' data-placement='bottom'><i class='fas fa-eye'></i></a>";
                        }
                        if ($user->can('service-show')) {
                            $btn = $btn."<a href='{$checklist}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='checklist' data-placement='bottom'><i class='fas fa-list'></i></a>";
                        }
                        if ($user->can('service-edit')) {
                            $btn = $btn."<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('service-delete')) {
                            $btn = $btn."<a href='{$delete}' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                        }
                         return $btn;
                    })
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('service', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
      

        // $test =  CarbonInterval::minutes(1820)->cascade()->forHumans();
        // return $tduration;
        return view('service_mgt.services.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $servicetypes = ServiceType::orderBy('name')->get();
        $users = User::where('institute_id','=', 69)->orWhere('institute_id','=', 73)->orderBy('name')->get();

        return view('service_mgt.services.createOrUpdate', compact('users','servicetypes')); 
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
            'service'=> 'required',
            'smsdesc' => 'required',
            'remarks' => 'required',
            'user1_id'=> 'required',
            'res1time'=> 'required',
        ]);
        $input=$request->except('_token');
        
        $status = Service::create($input);
        if($status){
            request()->session()->flash('success','Successfully added');
                return redirect()->route('services.index');
        } else {
             request()->session()->flash('error','Error occured while inserting');
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
        $service = Service::find($id);
        $users = User::where('institute_id','=', 69)->orWhere('institute_id','=', 73)->get();
        return view('service_mgt.services.show', compact('service','users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $servicetypes = ServiceType::orderBy('name')->get();
        $service = Service::find($id);
        $users = User::where('institute_id','=', 69)->orWhere('institute_id','=', 73)->get();
        return view('service_mgt.services.createOrUpdate', compact('service','users','servicetypes'));
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
        $employee=Service::findOrFail($id);
        $this->validate($request,
        [
            'service'=> 'required',
            'smsdesc' => 'required',
            'user1_id'=> 'required',
            'res1time'=> 'required',
            'servicetype1_id' => 'required',
        ]);

        $status = Service::where('id', $id)->update($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully updated');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        return redirect()->route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete=Service::findorFail($id);
        $status=$delete->delete();
        if($status){
            request()->session()->flash('success','Service Successfully deleted');
            return redirect()->route('services.index');
        }
        else{
            request()->session()->flash('error','There is an error while deleting Service');
        }

    }
}
