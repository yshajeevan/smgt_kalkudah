<?php

namespace App\Http\Controllers;

use App\Models\DegSubject;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DegreeSubjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DegSubject::orderBy('degreesub', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('deg-subjects.edit', $row->id);
                    $delete = route('deg-subjects.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('degree_subjects.index');
    }

    public function create()
    {
        return view('degree_subjects.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'degreesub' => 'required|unique:deg_subjects,degreesub',
        ]);

        DegSubject::create($request->all());
        return redirect()->route('deg-subjects.index')->with('success', 'Subject added successfully.');
    }

    public function edit(DegSubject $degSubject)
    {
        return view('degree_subjects.create_or_edit', compact('degSubject'));
    }

    public function update(Request $request, DegSubject $degSubject)
    {
        $request->validate([
            'degreesub' => 'required|unique:deg_subjects,degreesub,' . $degSubject->id,
        ]);

        $degSubject->update($request->all());
        return redirect()->route('deg-subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(DegSubject $degSubject)
    {
        $degSubject->delete();
        return redirect()->route('deg-subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
