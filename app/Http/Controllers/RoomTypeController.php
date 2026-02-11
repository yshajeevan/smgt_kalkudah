<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = RoomType::orderBy('name', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = route('room-types.edit', $row->id);
                    $delete = route('room-types.destroy', $row->id);
                    return "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' title='Edit'><i class='fas fa-edit'></i></a>
                            <a href='{$delete}' class='btn btn-xs btn-danger btn-sm' title='Delete' onclick='return confirm(\"Are you sure?\")'><i class='fas fa-trash'></i></a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('room_types.index');
    }

    public function create()
    {
        return view('room_types.create_or_edit');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:room_types,name',
        ]);

        RoomType::create($request->all());
        return redirect()->route('room-types.index')->with('success', 'Room Type added successfully.');
    }

    public function edit(RoomType $roomType)
    {
        return view('room_types.create_or_edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|unique:room_types,name,' . $roomType->id,
        ]);

        $roomType->update($request->all());
        return redirect()->route('room-types.index')->with('success', 'Room Type updated successfully.');
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->delete();
        return redirect()->route('room-types.index')->with('success', 'Room Type deleted successfully.');
    }
}
