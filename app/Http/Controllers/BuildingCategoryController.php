<?php

namespace App\Http\Controllers;

use App\Models\BuildingCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BuildingCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BuildingCategory::orderBy('name', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('building-categories.edit', $row->id);
                    $delete = route('building-categories.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('building_categories.index');
    }

    public function create()
    {
        return view('building_categories.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:building_categories,name',
        ]);

        BuildingCategory::create($request->all());
        return redirect()->route('building-categories.index')->with('success', 'Building Category added successfully.');
    }

    public function edit(BuildingCategory $buildingCategory)
    {
        return view('building_categories.create_or_edit', compact('buildingCategory'));
    }

    public function update(Request $request, BuildingCategory $buildingCategory)
    {
        $request->validate([
            'name' => 'required|unique:building_categories,name,' . $buildingCategory->id,
        ]);

        $buildingCategory->update($request->all());
        return redirect()->route('building-categories.index')->with('success', 'Building Category updated successfully.');
    }

    public function destroy(BuildingCategory $buildingCategory)
    {
        $buildingCategory->delete();
        return redirect()->route('building-categories.index')->with('success', 'Building Category deleted successfully.');
    }
}
