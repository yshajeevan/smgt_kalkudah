<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTransfer;
use DataTables;
use Illuminate\Support\Facades\DB;


class TransferController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:employee-list', ['only' => ['index','profile','edit']]);
    }
    
    public function index(Request $request)
    {
        // ================= AJAX (DATATABLE) =================
        if ($request->ajax()) {

            $data = ServiceTransfer::with([
                'employee.cadresubject',
                'employee.designation',
                'employee.institute1',   // for pfclerk
                'institute1',            // transfer_from
                'institute'              // transfer_to
            ]);

            return DataTables::of($data)

                // 🔍 SEARCH + FILTER
                ->filter(function ($query) use ($request) {

                    // 🔎 Global search (name + NIC)
                    if ($request->has('search') && $request->search['value']) {
                        $search = $request->search['value'];

                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('name_with_initial_e', 'like', "%{$search}%")
                              ->orWhere('nic', 'like', "%{$search}%");
                        });
                    }

                    // 🎯 PF Clerk filter
                    if ($request->pfclerk) {
                        $query->whereHas('institute1.pfclerk.employee', function ($q) use ($request) {
                            $q->where('employees.id', $request->pfclerk);
                        });
                    }

                    // 🎯 School filter (transfer_from)
                    if ($request->school) {
                        $query->where('transfer_from', $request->school);
                    }
                })

                // 👤 Employee Name
                ->editColumn('employee_id', function ($transfer) {
                    $emp = $transfer->employee;

                    if (!$emp) return '';

                    return $emp->title . "." . $emp->name_with_initial_e .
                        " (" . optional($emp->cadresubject)->cadre . "), " .
                        optional($emp->designation)->designation;
                })

                // 🏫 Transfer From
                ->editColumn('transfer_from', function ($transfer) {
                    return optional($transfer->institute1)->institute;
                })

                // 🏫 Transfer To
                ->editColumn('transfer_to', function ($transfer) {
                    return optional($transfer->institute)->institute;
                })

                // 👤 PF Clerk
                ->addColumn('pfclerk', function ($transfer) {
                    return $transfer->institute1?->pfclerk?->employee?->name_with_initial_e ?? 'N/A';
                })

                ->addIndexColumn()
                ->make(true);
        }

        // ================= DROPDOWNS =================

        // 🎯 Only used schools (transfer_from)
        $schools = DB::table('service_transfers')
            ->join('institutes', 'service_transfers.transfer_from', '=', 'institutes.id')
            ->select('institutes.id', 'institutes.institute')
            ->distinct()
            ->get();

        // 🎯 Only used PF Clerks
        $pfclerks = DB::table('service_transfers')
            ->join('institutes', 'service_transfers.transfer_from', '=', 'institutes.id')
            ->join('users', 'institutes.pfclerk_id', '=', 'users.id')
            ->join('employees', 'users.employee_id', '=', 'employees.id')
            ->select('employees.id', 'employees.name_with_initial_e')
            ->distinct()
            ->get();

        return view(
            'service_mgt.services.partials.transfer.transferList',
            compact('schools', 'pfclerks')
        );
    }
 }

