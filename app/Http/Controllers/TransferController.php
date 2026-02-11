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
                            return $transfer->employee->title.".".$transfer->employee->initial.".".$transfer->employee->surname." (".$transfer->employee->cadresubject->cadre."), ".$transfer->employee->designation->designation;
                    })
                    ->editColumn('transfer_from', function(ServiceTransfer $transfer) {
                            return $transfer->institute1->institute;
                    })
                    ->editColumn('transfer_to', function(ServiceTransfer $transfer) {
                            return $transfer->institute->institute;
                    })
                    ->addColumn('pfclerk', function(ServiceTransfer $transfer)
                    {
                        return $transfer->employee->institute1->pfclerk->name;
                    })
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('service_mgt.services.partials.transfer.transferList');
    }
 }

