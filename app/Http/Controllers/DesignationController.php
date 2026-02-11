<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use DataTables;

class DesignationController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:settings-manage', ['only' => ['index','profile','edit','create','store','update','destroy']]);
    }
    public function index(Request $request)
    {     
        if ($request->ajax()) {
            $data = Designation::orderBy('designation', 'asc');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $edit =  route('designation.edit', $row->id);
                        $delete =  route('designation.destroy', $row->id);
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
        return view('designation.index');
    }

    public function create()
    {   
        return view('designation.createOrUpdate');
    }

    public function store(Request $request)
    {
        $this->validate($request,
        [
            'designation' =>'string | required | max:30',
            'app_cadre' =>'nullable | numeric | max:5',
            'catg' =>'required | max:5',

        ]);

        $status = Designation::create($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully Added');
            return redirect()->route('designation.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
        }
    }

    public function edit($id)
    { 
       $designation = Designation::find($id);

       return view('designation.createOrUpdate', compact('designation'));
    }

     
    public function update(Request $request, $id)
    {
        $this->validate($request,
        [
            'designation' =>'string | required | max:30',
            'app_cadre' =>'nullable | numeric | max:5',
            'catg' =>'required | max:5',

        ]);

        $status = Designation::where('id', $id)->update($request->except('_token','_method'));
        if($status){
            request()->session()->flash('success','Successfully updated');
            return redirect()->route('designation.index');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        
    }

    public function destroy($id)
    {
        $designation = Designation::find($id);
        $status = $designation->delete();
        
        if($status){
            request()->session()->flash('success','Circular successfully deleted');
            return back();
        }
        else{
            request()->session()->flash('error','Error occurred please try again');
        }
        
    }
}
