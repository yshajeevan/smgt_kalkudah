<?php

namespace App\Http\Controllers;

use App\Models\ProfQualification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProfQualificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = ProfQualification::orderBy('name', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $edit = route('prof-qualifications.edit', $row->id);
                    $delete = route('prof-qualifications.destroy', $row->id);

                    return "
                        <a href='{$edit}' class='btn btn-primary btn-sm'>
                            <i class='fas fa-edit'></i>
                        </a>
                        <form action='{$delete}' method='POST' style='display:inline'>
                            " . csrf_field() . "
                            " . method_field('DELETE') . "
                            <button type='submit' class='btn btn-danger btn-sm'
                                onclick='return confirm(\"Are you sure?\")'>
                                <i class='fas fa-trash'></i>
                            </button>
                        </form>
                    ";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('prof_qualifications.index');
    }

    public function create()
    {
        return view('prof_qualifications.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:prof_qualifications,name'
        ]);

        ProfQualification::create([
            'name' => $request->name
        ]);

        return redirect()->route('prof-qualifications.index')
            ->with('success', 'Qualification added successfully.');
    }

    public function edit(ProfQualification $prof_qualification)
    {
        return view('prof_qualifications.create_or_edit',
            compact('prof_qualification'));
    }

    public function update(Request $request, ProfQualification $prof_qualification)
    {
        $request->validate([
            'name' => 'required|unique:prof_qualifications,name,' . $prof_qualification->id
        ]);

        $prof_qualification->update([
            'name' => $request->name
        ]);

        return redirect()->route('prof-qualifications.index')
            ->with('success', 'Qualification updated successfully.');
    }

    public function destroy(ProfQualification $prof_qualification)
    {
        $prof_qualification->delete();

        return redirect()->route('prof-qualifications.index')
            ->with('success', 'Qualification deleted successfully.');
    }
}
