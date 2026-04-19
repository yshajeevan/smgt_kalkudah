<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\LeaveRecord;
use DB;
use DataTables;

class LeaveController extends Controller
{
    public function index()
    {
        return view('leave.index');
    }

    public function getData(Request $request)
    {
        $year = date('Y');

        $employees = Employee::leftJoin('designations as d', 'd.id', '=', 'employees.designation_id')
        ->select(
            'employees.id',
            'employees.name_with_initial_e',
            'd.designation'
        );

        return Datatables::of($employees)

        ->addColumn('casual_taken', function($row) use ($year){
            return LeaveRecord::where('employee_id',$row->id)
            ->where('leave_type','Casual')
            ->where('year',$year)
            ->sum('days');
        })

        ->addColumn('medical_taken', function($row) use ($year){
            return LeaveRecord::where('employee_id',$row->id)
            ->where('leave_type','Medical')
            ->where('year',$year)
            ->sum('days');
        })

        ->addColumn('casual_balance', function($row) use ($year){

            $limit = 21;
            $taken = LeaveRecord::where('employee_id',$row->id)
            ->where('leave_type','Casual')
            ->where('year',$year)
            ->sum('days');

            return $limit - $taken;
        })

        ->addColumn('medical_balance', function($row) use ($year){

            $limit = ($row->designation == 'SLTES') ? 22 : 24;

            $taken = LeaveRecord::where('employee_id',$row->id)
            ->where('leave_type','Medical')
            ->where('year',$year)
            ->sum('days');

            return $limit - $taken;
        })

        ->addColumn('note_pending', function($row){
            return LeaveRecord::where('employee_id',$row->id)
            ->whereNull('leave_note')
            ->count();
        })

        ->addColumn('action', function($row){
            return '
                <button class="btn btn-sm btn-success addLeave" data-id="'.$row->id.'" data-name="'.$row->name_with_initial_e.'">
                Add Leave
                </button>

                <button class="btn btn-sm btn-primary historyBtn" data-id="'.$row->id.'">
                History
                </button>
            ';
        })

        ->rawColumns(['action', 'casual_balance','medical_balance'])
        ->make(true);
    }

    public function history($id)
    {
        $data = LeaveRecord::where('employee_id',$id)
        ->orderBy('from_date','desc')
        ->get();

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'=>'required',
            'leave_type'=>'required',
            'from_date'=>'required',
            'to_date'=>'required',
            'days'=>'required'
        ]);

        LeaveRecord::create([
            'employee_id'=>$request->employee_id,
            'leave_type'=>$request->leave_type,
            'from_date'=>$request->from_date,
            'to_date'=>$request->to_date,
            'days'=>$request->days,
            'leave_note'=>$request->leave_note,
            'year'=>date('Y')
        ]);

        return response()->json(['success'=>true]);
    }

    public function pendingNotes()
    {
        $data = DB::table('employees as e')
            ->join('leave_records as lr', 'e.id', '=', 'lr.employee_id')
            ->select(
                'e.id',
                'e.name_with_initial_e',
                DB::raw('COUNT(*) as pending')
            )
            ->whereNull('lr.leave_note')
            ->groupBy(
                'e.id',
                'e.name_with_initial_e'
            )
            ->orderByDesc('pending')
            ->get();

        return view('leave.pending_notes', compact('data'));
    }

    public function pendingDates($id)
    {
        $data = DB::table('leave_records')
            ->where('employee_id', $id)
            ->whereNull('leave_note')
            ->orderBy('from_date', 'desc')
            ->get();

        return response()->json($data);
    }

    public function edit($id)
    {
        $leave = LeaveRecord::find($id);

        return response()->json($leave);
    }


    public function update(Request $request, $id)
    {
        $leave = LeaveRecord::find($id);

        $leave->update([
            'leave_type' => $request->leave_type,
            'from_date'  => $request->from_date,
            'to_date'    => $request->to_date,
            'days'       => $request->days,
            'leave_note' => $request->leave_note
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Leave updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        $leave = LeaveRecord::find($id);

        if (!$leave) {
            return response()->json([
                'status' => false,
                'message' => 'Leave record not found.'
            ]);
        }

        $leave->delete();

        return response()->json([
            'status' => true,
            'message' => 'Leave deleted successfully.'
        ]);
    }
}