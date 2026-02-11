<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programme as Main;
use App\Models\Employee;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Str;
use DataTables;
use DB;

class ProgrammeController extends Controller
{

    public function path(){
        $path = 'programmes';
        return $path;
    }
    public function route(){
        return strtok(request()->path(), '/');
    }
    public function index(Request $request)
    {
         if ($request->ajax()) {
            $data = Main::where('is_website',1)->orderBy('id');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('name', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->editColumn('surname', function(Main $programme)
                        {
                            return $programme->coordinator->title.".".$programme->coordinator->initial.".".$programme->coordinator->surname;
                        })
                        ->editColumn('designation', function(Main $programme)
                        {
                            return $programme->coordinator->designation->designation;
                        })
                        ->editColumn('phone', function(Main $programme)
                        {
                            return $programme->coordinator->mobile;
                        })
                        ->editColumn('email', function(Main $programme)
                        {
                            return $programme->coordinator->email;
                        })
                    ->addColumn('action', function($row){
                        $edit =  route($this->route().'.edit',$row->id);
                        $delete =  route($this->route().'.destroy',$row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('settings-manage')) {
                            $btn = "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='show' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                            $btn = $btn."<button class='btn btn-xs btn-sm btn-danger btn-delete' data-remote='{$delete}'><i class='fas fa-trash'></i></button>";
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view($this->path().'.index');
    }

    /**
     * Show the form for creating a new Programme.
     *
     * @return Factory|View
     */
    public function create()
    {
        $coordinators= Employee::LastSync()->where('status','Active')->whereHas('designation', function($q){
            $q->where('designations.catg','=','OAC'); 
        })->get();
        
        return view($this->path().'.createOrUpdate')->with('coordinators', $coordinators);;
    }

    /**
     * Store a newly created Programme in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request, Main $programme)
    {
        $status = $programme->create($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully Added');
            return redirect()->route($this->route().'.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
        }
    }

    /**
     * Show the form for editing the specified Programme.
     *
     * @param Programme $programme
     * @return Factory|View
     */
    public function edit(Main $programme)
    {
       $coordinators= Employee::LastSync()->where('status','Active')->whereHas('designation', function($q){
            $q->where('designations.catg','=','OAC'); 
        })->get();

        return view($this->path().'.createOrUpdate')->with('item', $programme)->with('coordinators', $coordinators);
    }

    /**
     * Update the specified Programme in storage.
     *
     * @param Programme $programme
     * @return RedirectResponse|Redirector
     */
    public function update(Request $request, Main $programme)
    {
        $status = $programme->update($request->except('_token'));
        if($status){
            request()->session()->flash('success','Successfully Added');
            return redirect()->route($this->route().'.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
        }
    }

    /**
     * Remove the specified Programme from storage.
     *
     * @param Programme $programme
     * @return RedirectResponse|Redirector
     */
    public function destroy(string $id)
    {
        $status = Main::find($id)->delete();
        if($status){
            return response()->json([
                'success' => 'Record has been deleted successfully!'
            ]);
        }
        else{
            return response()->json([
                'error' => 'Error while deleting record'
            ]);
        }
    }
}
