<?php

namespace App\Http\Controllers;

use App\Models\DegInstitute;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DegreeInstituteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DegInstitute::orderBy('eduinsti', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('deg-institutes.edit', $row->id);
                    $delete = route('deg-institutes.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('degree_institutes.index');
    }

    public function create()
    {
        return view('degree_institutes.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'eduinsti' => 'required|unique:deg_institutes,eduinsti',
        ]);

        DegInstitute::create($request->all());
        return redirect()->route('deg-institutes.index')->with('success', 'Institute added successfully.');
    }

    public function edit(DegInstitute $degInstitute)
    {
        return view('degree_institutes.create_or_edit', compact('degInstitute'));
    }

    public function update(Request $request, DegInstitute $degInstitute)
    {
        $request->validate([
            'eduinsti' => 'required|unique:deg_institutes,eduinsti,' . $degInstitute->id,
        ]);

        $degInstitute->update($request->all());
        return redirect()->route('deg-institutes.index')->with('success', 'Institute updated successfully.');
    }

    public function destroy(DegInstitute $degInstitute)
    {
        $degInstitute->delete();
        return redirect()->route('deg-institutes.index')->with('success', 'Institute deleted successfully.');
    }
}

