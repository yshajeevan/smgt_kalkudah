<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VirtualFile;

class VirtualFileController extends Controller
{
    public function index($id){
        $vfiles = VirtualFIle::where('employee_id','=',$id)->get();

        return view('vfiles.index',compact('vfiles','id'));
    }

    public function create($id){
        return view('vfiles.createOrUpdate',compact('id'));
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $id){
        $this->validate($request,
        [
            'file' => 'required|mimes:jpeg,jpg,png|max:10000',
        ]);

    
        // Update vfile
        $vfiles = VirtualFile::where('employee_id','=',$id)->first();

         // delete if file exist
        if(\File::exists(base_path('vfiles/').$id."_".$request->curfield.".jpg")){
             unlink(base_path('vfiles/').$id."_".$request->curfield.".jpg");
        }
        
        if($request->file()) {
            $fileName = $id."_".$request->curfield.".jpg";
            $filePath = $request->file('file')->storeAs('vfiles', $fileName, 'base');
            $vfiles->hiqualif = $fileName;
        }
        $vfiles->save();

        $status = $vfiles->save();
        if($status){
            request()->session()->flash('success','Successfully updated');
            return redirect()->back();
        }
        else{
            request()->session()->flash('error','Error occured while updating');
        }
        
    }

}
