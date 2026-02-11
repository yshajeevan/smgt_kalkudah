<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklist;
use App\Models\Service;
use Illuminate\Support\Facades\File;

class ChecklistController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:service-show', ['only' => ['index']]);
        $this->middleware('permission:service-create', ['only' => ['create','store']]);
        $this->middleware('permission:service-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:service-delete', ['only' => ['destroy']]);
    }

    /**
     * Display checklist list by service
     */
    public function index($id)
    {
        $checklists = Checklist::where('service_id', $id)->get();
        $service = Service::findOrFail($id);

        return view('service_mgt.services.checklist_show',
            compact('checklists','id','service'));
    }

    /**
     * Show create form
     */
    public function create($id)
    {
        return view('service_mgt.services.checklist_createOrUpdate', compact('id'));
    }

    /**
     * Store new checklist
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required',
            'name' => 'required',
            'supportive_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'remarks' => 'nullable'
        ]);

        $input = $request->except('_token','supportive_doc');

        // Upload File (Optional)
        if ($request->hasFile('supportive_doc')) {

            $serviceId = $request->service_id;
            $folderPath = public_path('checklists/'.$serviceId);

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $file = $request->file('supportive_doc');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move($folderPath, $fileName);

            $input['supportive_doc'] = 'checklists/'.$serviceId.'/'.$fileName;
        }

        $status = Checklist::create($input);

        if($status){
            session()->flash('success','Checklist Successfully Added');
            return redirect()->route('checklist.index',$request->service_id);
        }

        session()->flash('error','Error occurred while inserting');
        return back();
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $checklist = Checklist::findOrFail($id);
        return view('service_mgt.services.checklist_createOrUpdate', compact('checklist'));
    }

    /**
     * Update checklist
     */
    public function update(Request $request, $id)
    {
        $checklist = Checklist::findOrFail($id);

        $request->validate([
            'name'=> 'required',
            'service_id' => 'required',
            'supportive_doc' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'remarks' => 'nullable'
        ]);

        $input = $request->except('_token','_method','supportive_doc');

        // If new file uploaded
        if ($request->hasFile('supportive_doc')) {

            // Delete old file
            if ($checklist->supportive_doc &&
                File::exists(public_path($checklist->supportive_doc))) {

                File::delete(public_path($checklist->supportive_doc));
            }

            $serviceId = $request->service_id;
            $folderPath = public_path('checklists/'.$serviceId);

            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $file = $request->file('supportive_doc');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move($folderPath, $fileName);

            $input['supportive_doc'] = 'checklists/'.$serviceId.'/'.$fileName;
        }

        $status = $checklist->update($input);

        if($status){
            session()->flash('success','Checklist Successfully Updated');
            return redirect()->route('checklist.index',$request->service_id);
        }

        session()->flash('error','Error occurred while updating');
        return back();
    }

    /**
     * Delete checklist
     */
    public function destroy($id)
    {
        $checklist = Checklist::findOrFail($id);

        // Delete file if exists
        if ($checklist->supportive_doc &&
            File::exists(public_path($checklist->supportive_doc))) {

            File::delete(public_path($checklist->supportive_doc));
        }

        $status = $checklist->delete();

        if($status){
            session()->flash('success','Checklist Successfully Deleted');
            return back();
        }

        session()->flash('error','Error while deleting checklist');
        return back();
    }
}
