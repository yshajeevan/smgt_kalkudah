<?php

namespace App\Http\Controllers;

use App\Models\BuildingRepairCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BuildingRepairCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BuildingRepairCategory::orderBy('name', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('building-repair-categories.edit', $row->id);
                    $delete = route('building-repair-categories.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('building_repair_categories.index');
    }

    public function create()
    {
        return view('building_repair_categories.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:building_repair_categories,name',
        ]);

        BuildingRepairCategory::create($request->all());
        return redirect()->route('building-repair-categories.index')->with('success', 'Repair Category added successfully.');
    }

    public function edit(BuildingRepairCategory $buildingRepairCategory)
    {
        return view('building_repair_categories.create_or_edit', compact('buildingRepairCategory'));
    }

    public function update(Request $request, BuildingRepairCategory $buildingRepairCategory)
    {
        $request->validate([
            'name' => 'required|unique:building_repair_categories,name,' . $buildingRepairCategory->id,
        ]);

        $buildingRepairCategory->update($request->all());
        return redirect()->route('building-repair-categories.index')->with('success', 'Repair Category updated successfully.');
    }

    public function destroy(BuildingRepairCategory $buildingRepairCategory)
    {
        $buildingRepairCategory->delete();
        return redirect()->route('building-repair-categories.index')->with('success', 'Repair Category deleted successfully.');
    }
}

