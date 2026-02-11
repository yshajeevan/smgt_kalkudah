<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\User;
use App\Models\GnDivision;
use App\Models\Employee;
use App\Models\Stupopulation;
use App\Models\Attendance;
use App\Models\Studropout;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use App\Exports\InstituteExport;
use Helper;
use Excel;
use DataTables;

class InstituteController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:institute-list', ['only' => ['index','export','list','show']]);
        $this->middleware('permission:institute-create', ['only' => ['create','store']]);
        $this->middleware('permission:institute-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Institute::query();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->filter(function ($instance) use ($request) {
                        if (!empty($request->get('search'))) {
                            $instance->where(function($w) use($request){
                                $search = $request->get('search');
                                $w->orWhere('institute', 'LIKE', "%$search%")
                                ->orWhere('census', 'LIKE', "%$search%");
                            });
                        }
                    })
                    ->addColumn('action', function($row){
                        $show =  route('institute.show',$row->id);
                        $edit =  route('institute.edit',$row->id);
                        $delete =  route('institute.destroy',$row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('institute-list')) {
                            $btn = "<a href='{$show}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit'><i class='fas fa-eye'></i></a>";
                        }
                        if ($user->can('institute-edit')) {
                            $btn .= "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('institute-delete')) {
                            $btn .= "<a href='{$delete}' onclick='return confirm(`Are you sure want to delete this record?`)' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete'><i class='fas fa-trash'></i></a>";
                        }
                         return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('institutes.index');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $status = Institute::create($request->except('_token', 'document'));

        if ($status) {
            request()->session()->flash('success', 'Successfully Added');
        } else {
            request()->session()->flash('error', 'Error occurred while inserting');
        }
        return redirect()->back();
    }

    public function show($id)
    {
        $instit = Institute::find($id);
        $institutes = Helper::getinstitutes()->where('i.id', '=' ,$id)->first();
        return view('institutes.show', compact('institutes','instit'));
    }

    public function edit($id)
    { 
        $institutes = Institute::find($id);
        $gns = GnDivision::getAllList();

        $epsi = Employee::LastSync()->whereHas('designation', function($q){
            $q->where('designations.catg','=','OAC');
        })->pluck('namewithinitial', 'id')->toArray();

        return view('institutes.createOrUpdate', compact('institutes', 'gns', 'epsi'));
    }


    public function update(Request $request, $id)
    {
        $status = Institute::where('id', $id)->update($request->except('_token', 'document'));

        if ($status) {
            request()->session()->flash('success', 'Institute successfully updated');
            return redirect()->route('institute.index');
        } else {
            request()->session()->flash('error', 'Error occurred while updating institute');
        }
    }

    public function view_clerk(){
        $institutes = Institute::where('id','<',75)->get();
        $users = User::select('id','name')->get();
        return view('institutes.updateclerks',compact('institutes','users'));
    }

    public function clerks(){
        $datas = User::where('institute_id','>',68)->orderby("name","asc")->select('id','name')->get();

        foreach ($datas as $data) {
            $dataModified[] = ['value' => $data->id, 'text' => $data->name];
        }

        print_r(json_encode($dataModified));
    }

    public function updatepk(Request $request)
    {
        if ($request->ajax()) {
            Institute::find($request->input('pk'))->update([$request->input('name') => $request->input('value')]);
            return response()->json(['success' => true]);
        }
    }

    public function destroy($id)
    {
        //
    }

    public function export()
    {
        $institutes = Helper::getinstitutes()->get();

        foreach($institutes as $object) {
            $list[] = (array) $object;
        }

        $data = collect($list);
        $excel = new InstituteExport($data);

        return Excel::download($excel,'institutes.xlsx');
    }

    public function prlclass(){
        $prlclasses = SchoolClass::groupBy('institute_id')->selectRaw('institute_id, count(id) as countprlclass')->with('institute')->get();
        return view('institutes.prlclass',compact('prlclasses'));
    }

    public function students(){
        $students = Student::groupBy('institute_id')->selectRaw('institute_id, count(id) as totstudent')->with('institute')->get();
        return view('institutes.students',compact('students'));
    }
}
