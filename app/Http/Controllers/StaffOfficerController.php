<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DataTables;
use DB;

class StaffOfficerController extends Controller
{
    public function index(Request $request)
    {     
        if ($request->ajax()) {
            $data = Staff::all();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('image', function ($row) {
                        $imagePath = asset('/images/staff_officers/' . $row->image);
                        return "<img src='{$imagePath}' alt='Staff Image' class='img-circle' style='height:50px; width:50px; object-fit:cover;'>";
                    })
                    ->addColumn('action', function($row){
                        $edit =  route('staff.edit', $row->id);
                        $delete =  route('staff.destroy', $row->id);
                        $user = auth()->user();
                        $btn = '';
                        if ($user->can('settings-manage')) {
                            $btn = "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='edit' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        }
                        if ($user->can('settings-manage')) {
                            $btn = $btn."<a href='{$delete}' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                        }
                         return $btn;
                    })
                    ->rawColumns(['image', 'action'])
                    ->make(true);
        }
        return view('staff_officers.index');
    }


    public function create()
    {
        return view('staff_officers.createOrUpdate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string',
            'branch' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'whatsapp' => 'nullable|string|max:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_website' => 'boolean',
            'list_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/staff_officers'), $imageName);
            $validated['image'] = $imageName;
        }

        Staff::create($validated);
        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }


    public function edit(Staff $staff)
    {
        return view('staff_officers.createOrUpdate', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string',
            'branch' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'whatsapp' => 'nullable|string|max:15',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_website' => 'boolean',
            'list_order' => 'integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($staff->image && file_exists(public_path('images/staff_officers/' . $staff->image))) {
                unlink(public_path('images/staff_officers/' . $staff->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/staff_officers'), $imageName);
            $validated['image'] = $imageName;
        }

        $staff->update($validated);
        return redirect()->route('staff.index')->with('success', 'Staff updated successfully.');
    }

    public function show()
    {
        $staff = Staff::orderBy('list_order')->get();
        return view('staff_officers.list_order', compact('staff'));
    }

    public function updateListOrder(Request $request)
    {
        $order = $request->order;

        foreach ($order as $item) {
            Staff::where('id', $item['id'])->update(['list_order' => $item['position']]);
        }

        return response()->json(['success' => true, 'message' => 'List order updated successfully!']);
    }


    public function destroy(Staff $staff)
    {
        if ($staff->image) {
            Storage::disk('public')->delete($staff->image);
        }
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully.');
    }
}

