<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GnDivision;
use App\Models\DsDivision;
use DataTables;

class GnController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:settings-manage', ['only' => ['index','profile','edit','create','store','update','destroy']]);
    }
    public function index(Request $request)
    {     
        if ($request->ajax()) {
            $data = GnDivision::with('dsdivision')->orderBy('gn', 'asc'); 
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('dsdivision', function(GnDivision $gndivision)
                    {
                        return $gndivision->dsdivision->ds;
                    })
                    ->addColumn('action', function($row){
                        $edit =  route('gndivision.edit', $row->id);
                        $delete =  route('gndivision.delete', $row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('settings-manage')) {
                            $btn = "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('settings-manage')) {
                            $btn = $btn."<a href='{$delete}' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                        }
                         return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('gndivision.index');
    }

    public function create()
    {   
        $dsdivisions = DsDivision::get();
        return view('gndivision.createOrUpdate', compact('dsdivisions'));
    }

    public function store(Request $request)
    {
        $this->validate($request,
        [
            'gn' =>'required',
            'dsdivision_id' =>'required',
            'gpslocation' =>'nullable',

        ]);

        $status = GnDivision::create($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully Added');
            return redirect()->route('gndivision.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
        }
    }

    public function edit($id)
    { 
        $gndivision = GnDivision::find($id);
        $dsdivisions = DsDivision::get();
        return view('gndivision.createOrUpdate', compact('gndivision','dsdivisions'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
        [
            'gn' =>'required',
            'dsdivision_id' =>'required',
            'gpslocation' =>'required',
        ]);

        $status = GnDivision::where('id', $id)->update($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully updated');
            return redirect()->route('gndivision.index');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        
    }

    public function destroy($id)
    {
        $gndivision = GnDivision::find($id);
        $status = $gndivision->delete();
        
        if($status){
            request()->session()->flash('success','Circular successfully deleted');
            return back();
        }
        else{
            request()->session()->flash('error','Error occurred please try again');
        }
        
    }
    
    public function getgn($id)
     {
        $data = GnDivision::where('dsdivision_id','=',$id)->get();
        return response()->json(['data' => $data]);
     }
    public function testgn(){
       return $data = GnDivision::get();
    }
}
