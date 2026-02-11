<?php

namespace App\Http\Controllers;

use App\Models\BuildingType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BuildingTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BuildingType::orderBy('name', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('building-types.edit', $row->id);
                    $delete = route('building-types.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('building_types.index');
    }

    public function create()
    {
        return view('building_types.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:building_types,name',
        ]);

        BuildingType::create($request->all());
        return redirect()->route('building-types.index')->with('success', 'Building Type added successfully.');
    }

    public function edit(BuildingType $buildingType)
    {
        return view('building_types.create_or_edit', compact('buildingType'));
    }

    public function update(Request $request, BuildingType $buildingType)
    {
        $request->validate([
            'name' => 'required|unique:building_types,name,' . $buildingType->id,
        ]);

        $buildingType->update($request->all());
        return redirect()->route('building-types.index')->with('success', 'Building Type updated successfully.');
    }

    public function destroy(BuildingType $buildingType)
    {
        $buildingType->delete();
        return redirect()->route('building-types.index')->with('success', 'Building Type deleted successfully.');
    }
}
