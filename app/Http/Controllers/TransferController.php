<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceTransfer;
use DataTables;


class TransferController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:employee-list', ['only' => ['index','profile','edit']]);
    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ServiceTransfer::all();
            return Datatables::of($data)
                    ->editColumn('employee_id', function(ServiceTransfer $transfer) {
                        $emp = $transfer->employee;

                        if (!$emp) return '';

                        return $emp->title . "." . $emp->name_with_initial_e .
                            " (" . optional($emp->cadresubject)->cadre . "), " .
                            optional($emp->designation)->designation;
                    })
                    ->editColumn('transfer_from', function(ServiceTransfer $transfer) {
                            return $transfer->institute1->institute;
                    })
                    ->editColumn('transfer_to', function(ServiceTransfer $transfer) {
                            return $transfer->institute->institute;
                    })
                    ->addColumn('pfclerk', function(ServiceTransfer $transfer)
                    {
                        return $transfer->employee->institute1?->pfclerk?->name ?? 'N/A';
                    })
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('service_mgt.services.partials.transfer.transferList');
    }
 }

