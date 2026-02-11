<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Room::with('building', 'roomType')
            ->join('buildings', 'rooms.building_id', '=', 'buildings.id') // Join buildings
            ->orderBy('buildings.id', 'asc') // Order by building ID
            ->select('rooms.*'); // Select only rooms fields to avoid conflicts

            // Apply building_id filter if provided
            if ($request->has('building_id') && $request->building_id) {
                $query->where('rooms.building_id', $request->building_id);
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('building', function ($row) {
                    return $row->building->name ?? '-';
                })
                ->addColumn('room_type', function ($row) {
                    return $row->roomType->name ?? '-';
                })
                ->addColumn('is_available', function ($row) {
                    return $row->is_available ? 'Active' : 'In-active';
                })
                ->addColumn('action', function ($row) {
                    $edit = route('rooms.edit', $row->id);
                    $delete = route('rooms.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $buildingName = null;
        $instituteName = null;

        if ($request->has('building_id') && $request->building_id) {
            $building = Building::find($request->building_id);

            if ($building) {
                $buildingName = $building->name;
                $instituteName = $building->institute->institute ?? 'Unknown'; // 
            }
        }

        return view('rooms.index', [
            'buildingName' => $buildingName,
            'instituteName' => $instituteName,
        ]);
    }

    public function create()
    {
        $buildings = Building::all();
        $roomTypes = RoomType::all();
        return view('rooms.create_or_edit', compact('buildings', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_type_id' => 'required|exists:room_types,id',
            'name' => 'required|unique:rooms,name,NULL,id,building_id,' . $request->building_id,
            'size' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room added successfully.');
    }

    public function edit(Room $room)
    {
        $buildings = Building::all();
        $roomTypes = RoomType::all();
        return view('rooms.create_or_edit', compact('room', 'buildings', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'building_id' => 'required|exists:buildings,id',
            'room_type_id' => 'required|exists:room_types,id',
            'name' => 'required|unique:rooms,name,' . $room->id . ',id,building_id,' . $request->building_id,
            'size' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        $room->update($request->all());
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
