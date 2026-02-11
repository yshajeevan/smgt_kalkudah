<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DegreeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Degree::orderBy('degree', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('degrees.edit', $row->id);
                    $delete = route('degrees.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('degrees.index');
    }

    public function create()
    {
        return view('degrees.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'degree' => 'required|unique:degrees,degree',
        ]);

        Degree::create($request->all());
        return redirect()->route('degrees.index')->with('success', 'Degree added successfully.');
    }

    public function edit(Degree $degree)
    {
        return view('degrees.create_or_edit', compact('degree'));
    }

    public function update(Request $request, Degree $degree)
    {
        $request->validate([
            'degree' => 'required|unique:degrees,degree,' . $degree->id,
        ]);

        $degree->update($request->all());
        return redirect()->route('degrees.index')->with('success', 'Degree updated successfully.');
    }

    public function destroy(Degree $degree)
    {
        $degree->delete();
        return redirect()->route('degrees.index')->with('success', 'Degree deleted successfully.');
    }
}
