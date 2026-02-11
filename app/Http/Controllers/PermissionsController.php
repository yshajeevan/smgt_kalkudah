<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use App\Models\User;


class PermissionsController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:permission-list', ['only' => ['index']]);
        $this->middleware('permission:permission-create', ['only' => ['create','store']]);
        $this->middleware('permission:permission-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
   }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Permission::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $edit = '<a href="'.route('permissions.edit', $row->id).'" class="btn btn-sm btn-primary mr-1"><i class="fas fa-edit"></i></a>';
                    $delete = '<form action="'.route('permissions.destroy', $row->id).'" method="POST" class="delete-form" style="display:inline;">'
                            . csrf_field()
                            . method_field('DELETE')
                            . '<button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>'
                            . '</form>';
                    return $edit.$delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('permissions.index');
    }

    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.createOrUpdate');
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
            'name'=>'required',
        ]);
        $input=$request->all();
        
        $status = Permission::create($input);
        if($status){
            request()->session()->flash('success','Successfully added');
            return redirect()->route('permissions.index');
        }
        else{
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
        $permissions = Permission::find($id);

        return view('permissions.createOrUpdate', compact('permissions'));
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
        $permissions = Permission::findOrFail($id);
        $this->validate($request,
        [
            'name'=>'required',
        ]);

        $status = Permission::where('id', $id)->update($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully updated');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        // If the request is AJAX, return JSON (helpful if you later want to do AJAX deletes)
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Permission deleted.']);
        }

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
