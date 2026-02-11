<?php

namespace App\Http\Controllers;

use App\Models\Cadresubject;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CadreSubjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cadresubject::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('cadre-subject.edit', $row->id);
                    $delete = route('cadre-subject.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('cadre_subject.index');
    }

    public function create()
    {
        return view('cadre_subject.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cadre' => 'required',
            'cadre_code' => 'required',
            'category' => 'required|in:primary,secondary_b1,secondary_b2,secondary_b3,advanced_level,school_non_academic,school_administration,office_academic,office_non_academic,13_years_education,others',
            'subject_number' => 'required|integer',
            'category2' => 'nullable',
            'app_cadre' => 'required|integer',
        ]);

        Cadresubject::create($request->all());
        return redirect()->route('cadre-subject.index')->with('success', 'CadreSubject added successfully.');
    }

    public function edit(Cadresubject $cadreSubject)
    {
        return view('cadre_subject.create_or_edit', compact('cadreSubject'));
    }

    public function update(Request $request, Cadresubject $cadreSubject)
    {
        $request->validate([
            'cadre' => 'required',
            'cadre_code' => 'required',
            'category' => 'required|in:primary,secondary_b1,secondary_b2,secondary_b3,advanced_level,school_non_academic,school_administration,office_academic,office_non_academic,13_years_education,others',
            'subject_number' => 'required|integer',
            'category2' => 'nullable',
            'app_cadre' => 'required|integer',
        ]);

        $cadreSubject->update($request->all());
        return redirect()->route('cadre-subject.index')->with('success', 'CadreSubject updated successfully.');
    }

    public function destroy(Cadresubject $cadreSubject)
    {
        $cadreSubject->delete();
        return redirect()->route('cadre-subject.index')->with('success', 'CadreSubject deleted successfully.');
    }
}

