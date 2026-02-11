<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingCategory;
use App\Models\BuildingType;
use App\Models\Institute;
use App\Models\BuildingRepairCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Building::with('category', 'type', 'repairs');

            // Apply filter if institute_id is provided
            if ($request->institute_id) {
                $query->where('institute_id', $request->institute_id);
            }

            // Apply filter for type
            if ($request->type_id) {
                $query->where('building_type_id', $request->type_id);
            }

            // Apply filter for usage
            if ($request->usage !== null) { // Check explicitly for null to allow 0 (Inactive)
                $query->where('usage', $request->usage);
            }

             // Apply filter for buildings with or without repairs
            if ($request->has_repairs === "1") {
                $query->has('repairs'); // Only buildings with repairs
            } elseif ($request->has_repairs === "0") {
                $query->doesntHave('repairs'); // Only buildings without repairs
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('institute', function ($row) {
                    return $row->institute->institute ?? '-';
                })
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '-';
                })
                ->addColumn('type', function ($row) {
                    return $row->type->name ?? '-';
                })
                ->addColumn('usage', function ($row) {
                    return $row->usage ? 'Active' : 'In-active'; // Display usage as Active or In-active
                })
                ->addColumn('name_with_repairs', function ($row) {
                    $repairsCount = $row->repairs->count();
                    if ($repairsCount > 0) {
                        $badge = '<span class="badge badge-danger" style="font-size: 0.8rem;">Repair: ' . $repairsCount . '</span>';
                        return $row->name . ' ' . $badge;
                    }
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $edit = route('buildings.edit', $row->id);
                    $delete = route('buildings.destroy', $row->id);
                    $viewRoomsUrl = route('rooms.index', ['building_id' => $row->id]);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$viewRoomsUrl}' class='btn btn-xs btn-info btn-sm' title='View Rooms'>
                                <i class='fas fa-door-open'></i>
                            </a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['name_with_repairs', 'action'])
                ->make(true);
        }
        $institutes = Institute::all();
        $types = BuildingType::all(); 
        return view('buildings.index', compact('institutes', 'types'));
    }

    public function create()
    {
        $categories = BuildingCategory::all();
        $types = BuildingType::all();
        $institutes = Institute::all();
        $repairCategories = BuildingRepairCategory::all(); // Fetch repair categories
        return view('buildings.create_or_edit', compact('categories', 'types', 'institutes', 'repairCategories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'institute_id' => 'nullable|integer',
            'name' => 'required|unique:buildings,name,NULL,id,institute_id,' . $request->institute_id,
            'size' => 'required|string',
            'building_category_id' => 'required|exists:building_categories,id',
            'building_type_id' => 'required|exists:building_types,id',
            'usage' => 'required|boolean',
            'constructed_on' => 'nullable|date',
            'repairs.*.building_repair_category_id' => 'required|exists:building_repair_categories,id',
            'repairs.*.description' => 'nullable|string',
            'repairs.*.cost' => 'nullable|numeric',
        ]);
    
        // Create the building
        $building = Building::create($request->except('repairs'));
    
        // Add related repairs
        if ($request->has('repairs')) {
            foreach ($request->repairs as $repair) {
                $building->repairs()->create($repair);
            }
        }
    
        return redirect()->route('buildings.index')->with('success', 'Building added successfully.');
    }
    
    public function edit(Building $building)
    {
        $categories = BuildingCategory::all();
        $types = BuildingType::all();
        $institutes = Institute::all();
        $repairCategories = BuildingRepairCategory::all(); // Fetch repair categories
        return view('buildings.create_or_edit', compact('building', 'categories', 'types', 'institutes', 'repairCategories'));
    }
    
    public function update(Request $request, Building $building)
    {
        $request->validate([
            'institute_id' => 'nullable|integer',
            'name' => 'required|unique:buildings,name,' . $building->id . ',id,institute_id,' . $request->institute_id,
            'size' => 'required|string',
            'building_category_id' => 'required|exists:building_categories,id',
            'building_type_id' => 'required|exists:building_types,id',
            'usage' => 'required|boolean',
            'constructed_on' => 'nullable|date',
            'repairs.*.building_repair_category_id' => 'required|exists:building_repair_categories,id',
            'repairs.*.description' => 'nullable|string',
            'repairs.*.cost' => 'nullable|numeric',
        ]);

        // Update the building excluding repairs
        $building->update($request->except('repairs'));

        // If usage is set to 0, update related rooms to set is_available = 0
        if ($request->usage == 0) {
            $building->rooms()->update(['is_available' => 0]);
        }

        // Synchronize repairs
        $building->repairs()->delete();
        if ($request->has('repairs')) {
            foreach ($request->repairs as $repair) {
                $building->repairs()->create($repair);
            }
        }

        return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');
    }


    public function destroy(Building $building)
    {
        $building->delete();
        return redirect()->route('buildings.index')->with('success', 'Building deleted successfully.');
    }
}
