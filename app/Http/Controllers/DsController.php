<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DsDivision;
use DataTables;

class DsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:settings-manage', ['only' => ['index','profile','edit','create','store','update','destroy']]);
    }
    public function index(Request $request)
    {     
        if ($request->ajax()) {
            $data = DsDivision::orderBy('ds', 'asc');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $edit =  route('dsdivision.edit', $row->id);
                        $delete =  route('dsdivision.delete', $row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('esettings-manage')) {
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
        return view('dsdivision.index');
    }

    public function create()
    {   
        return view('dsdivision.createOrUpdate');
    }

    public function store(Request $request)
    {
        $this->validate($request,
        [
            'ds' =>'required',

        ]);

        $status = DsDivision::create($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully Added');
            return redirect()->route('dsdivision.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
        }
    }

    public function edit($id)
    { 
       $dsdivision = DsDivision::find($id);

       return view('dsdivision.createOrUpdate', compact('dsdivision'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,
        [
            'ds' =>'required',

        ]);

        $status = DsDivision::where('id', $id)->update($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully updated');
            return redirect()->route('dsdivision.index');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        
    }

    public function destroy($id)
    {
        $dsdivision = DsDivision::find($id);
        $status = $dsdivision->delete();
        
        if($status){
            request()->session()->flash('success','Circular successfully deleted');
            return back();
        }
        else{
            request()->session()->flash('error','Error occurred please try again');
        }
        
    }
}
