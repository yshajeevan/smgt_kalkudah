<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use DataTables;

class UploadController extends Controller
{
 
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Upload::query();

            // 🔎 Global Search (DataTables default search)
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];

                $data->where(function ($query) use ($search) {
                    $query->where('description', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%")
                        ->orWhere('released_year', 'LIKE', "%{$search}%")
                        ->orWhere('releasedby', 'LIKE', "%{$search}%")
                        ->orWhere('type', 'LIKE', "%{$search}%");
                });
            }

            // 🔽 Dropdown Filters
            if ($request->filled('type')) {
                $data->where('type', $request->type);
            }

            if ($request->filled('released_year')) {
                $data->where('released_year', $request->released_year);
            }

            if ($request->filled('releasedby')) {
                $data->where('releasedby', $request->releasedby);
            }

            $data->orderBy('description');

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('view', function ($row) {
                    if ($row->file_url && file_exists(public_path('pdfs/'.$row->file_url))) {
                        return '<a href="' . asset('pdfs/'.$row->file_url) . '" 
                                target="_blank" 
                                class="btn btn-sm btn-info">
                                View PDF
                                </a>';
                    }
                    return '<span class="text-danger">No File</span>';
                })

                ->addColumn('is_website', function ($row) {
                    return $row->is_website 
                        ? '<span class="badge badge-success">Yes</span>' 
                        : '<span class="badge badge-danger">No</span>';
                })

                ->addColumn('action', function ($row) {

                    $edit = route('upload.edit', $row->id);
                    $delete = route('upload.destroy', $row->id);
                    $user = auth()->user();
                    $btn = '';

                    if ($user->can('settings-manage')) {

                        $btn .= "<a href='{$edit}' 
                                class='btn btn-primary btn-sm' 
                                title='Edit'>
                                <i class='fas fa-edit'></i>
                                </a>";

                        $btn .= "
                            <form action='{$delete}' 
                                method='POST' 
                                style='display:inline-block;' 
                                onsubmit='return confirm(\"Are you sure you want to delete this PDF?\")'>
                                " . csrf_field() . "
                                " . method_field('DELETE') . "
                                <button type='submit' class='btn btn-danger btn-sm'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </form>";
                    }

                    return $btn;
                })

                ->rawColumns(['view', 'is_website', 'action'])
                ->make(true);
        }

        return view('uploads.index');
    }

    public function create()
    {
        return view('uploads.createOrUpdate');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'description' => 'required',
            'released_year' => 'required|digits:4|integer|between:1950,' . date('Y'),
            'releasedby' => 'required',
            'type' => 'required',
            'fileToUpload' => 'required|mimes:pdf|max:51200', // 50MB,
            'is_website' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('fileToUpload');
        $name = $request->input('name');
        $filePath = $name . '.' . $file->getClientOriginalExtension();

        // Store file in the public directory
        $file->move(public_path('pdfs'), $filePath);

        $status = Upload::create([
            'name' => $request->name,
            'description' => $request->description,
            'file_url' => $filePath,
            'released_year' => $request->released_year,
            'releasedby' => $request->releasedby,
            'type' => $request->type,
            'is_website' => $request->has('is_website') ? 1 : 0, 
        ]);

        return $status
            ? redirect()->route('upload.index')->with('success', 'PDF uploaded successfully!')
            : redirect()->back()->with('error', 'Failed to upload PDF.');
    }

    public function edit(Upload $upload)
    {
        return view('uploads.createOrUpdate', ['item' => $upload]);
    }

    public function update(Request $request, Upload $upload)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'description' => 'required',
            'released_year' => 'required|digits:4|integer|between:1950,' . date('Y'),
            'releasedby' => 'required',
            'type' => 'required',
            'fileToUpload' => 'nullable|mimes:pdf|max:51200', // 50MB
            'is_website' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fileUrl = $upload->file_url;

        if ($request->hasFile('fileToUpload')) {
            $file = $request->file('fileToUpload');
            $name = $request->input('name');
            $filePath = $name . '.' . $file->getClientOriginalExtension();

            // Store new file and delete old file
            $file->move(public_path('pdfs'), $filePath);
            if (file_exists(public_path('pdfs/'.$upload->file_url))) {
                unlink(public_path('pdfs/'.$upload->file_url));
            }
            $fileUrl = $filePath;
        }

        $upload->update([
            'name' => $request->name,
            'description' => $request->description,
            'file_url' => $fileUrl,
            'released_year' => $request->released_year,
            'releasedby' => $request->releasedby,
            'type' => $request->type,
            'is_website' => $request->has('is_website') ? 1 : 0,
        ]);

        return redirect()->route('upload.index')->with('success', 'PDF updated successfully!');
    }

    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        if ($upload->file_url && file_exists(public_path('pdfs/' . $upload->file_url))) {
            unlink(public_path('pdfs/' . $upload->file_url));
        }

        $upload->delete();

        return redirect()->route('upload.index')
            ->with('success', 'PDF deleted successfully!');
    }
}
