<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News as Main;
use App\Models\NewsCategory;
use App\Models\NewsPhoto;
use DataTables;
use Carbon\Carbon;
use Intervention\Image\ImageManager; 
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NewsController extends Controller
{

    public function path(){
        $path = 'news';
        return $path;
    }
    public function route(){
        return request()->path();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Main::query()->latest();;

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $edit =  route($this->route().'.edit',$row->id);
                        $delete =  route($this->route().'.destroy',$row->id);
                        $btn = '';
                        $btn = "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='show' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        $btn = $btn."<button class='btn btn-xs btn-sm btn-danger btn-delete' data-remote='{$delete}'><i class='fas fa-trash'></i></button>";
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view($this->path().'.index');
    }


    public function create()
    {
        $categories = NewsCategory::select('id','name')->get();
        return view($this->path().'.createOrUpdate',compact('categories'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
        [
            'name'=>'required',
            'summary'=>'required',
            'content' => 'required',
            'venue' => 'required',
            'category_id' => 'required',
        ]);
        

        $content = $request->content;
        $dom = new \DomDocument();
        $dom->loadHtml('<?xml encoding="UTF-8">'.$content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $imageFile = $dom->getElementsByTagName('img');
 
        foreach($imageFile as $item => $image){
           $data = $image->getAttribute('src');
           list($type, $data) = explode(';', $data);
           list(, $data)      = explode(',', $data);
           $imgeData = base64_decode($data);
           $image_name= Carbon::now()->getPreciseTimestamp(3) . '.jpg';
           $path = public_path('images/news/').$image_name;
           file_put_contents($path, $imgeData);
           
           $image->removeAttribute('src');
           $image->setAttribute('src', asset('images/news/' . $image_name));
        }
        $content = $dom->saveHTML();
        
        
        $news = New Main();
        $news->name = $request->name;
        $news->summary = $request->summary;
        $news->content = $request->content;
        $news->venue = $request->venue;
        $news->category_id = $request->category_id;
        $news->slug = str_replace(' ', '-', trim($request->name));
        $result = $news->save();
        
        
        if($request->hasFile('file')){
            $names = [];
            foreach($request->file('file') as $key=>$image){
                // $image->move($destinationPath, $fileName);
                
                //Image Intervention
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $image = $manager->read($image);
                $image->cover(900, 540 , 'center');
                $image->text('www.battiwestzeo.lk', 750 /* x */, 520 /* y */, function($font) {
                    $font->size(70);
                });
                // encode edited image
                $encoded = $image->toJpg();
                
                // save encoded image
                $fileName = Carbon::now()->getPreciseTimestamp(3).'.jpg';
                $encoded->save(public_path().'/images/news/'.$fileName);
 
                //Save to database         
                $table = new NewsPhoto;
                $table->name = $fileName;
                
                //Validate radio button
                $options = is_array($request->sample_model) ? implode($request->sample_model) : '';
                $value = intval($options);
                if($key == $value){
                    $table->is_cover = 1;
                }else{
                    $table->is_cover = 0;
                }
                $names[] = $table;
            }  
            $result = $news->photo()->saveMany($names);
        }
   
        if($result){
            request()->session()->flash('success','Successfully added');
            return redirect()->route($this->route().'.index');
        }
        else{
            request()->session()->flash('error','Error occured while inserting');
            return redirect()->back();
        }
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        $item = Main::find($id);
        $categories = NewsCategory::select('id','name')->get();

        return view($this->path().'.createOrUpdate', compact('item','categories'));
    }


    public function update(Request $request, string $id)
    {
        $this->validate($request,
        [
            'name'=>'required',
            'summary'=>'required',
            'content' => 'required',
            'venue' => 'required',
            'category_id' => 'required',
        ]);
        
        //Update content summernote
        $content = $request->content;
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHtml('<?xml encoding="UTF-8">'.$content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | libxml_use_internal_errors(true));
        $imageFile = $dom->getElementsByTagName('img');
    
        // Ensure the 'images/news' directory exists
        $newsImagePath = public_path('images/news');
        if (!File::exists($newsImagePath)) {
            File::makeDirectory($newsImagePath, 0755, true);
        }
        
        $imageFile = $dom->getElementsByTagName('img');
        
        foreach ($imageFile as $item => $image) {
            $data = $image->getAttribute('src');
            if (strpos($data, ';') === false) {
                continue; // Skip if the image src is not base64 encoded
            }
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $imageData = base64_decode($data);
            $imageName = Carbon::now()->getPreciseTimestamp(3) . '.jpg';
            $imagePath = $newsImagePath . '/' . $imageName;
        
            // Write the image data to the specified path
            file_put_contents($imagePath, $imageData);
        
            // Update the src attribute to point to the saved image
            $image->removeAttribute('src');
            $image->setAttribute('src', asset('images/news/' . $imageName));
        }
        $content = $dom->saveHTML();
        
        $query = Main::find($id);
        

        if($request->hasFile('file')){
            $names = [];
            foreach ($request->file('file') as $key=>$image) {
                // delete existing (if set)
                $old_image_name = NewsPhoto::where('id',$key)->value('name');
                if(!empty($old_image_name)){
                    $old_image_path = 'images/news/'.$old_image_name;
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                //Image Intervention
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $image = $manager->read($image);
                $image->cover(900, 540 , 'center');
                $image->text('www.battiwestzeo.lk', 750 /* x */, 520 /* y */, function($font) {
                    $font->size(70);
                });
                // encode edited image
                $encoded = $image->toJpg();
                
                // save encoded image
                $fileName = Carbon::now()->getPreciseTimestamp(3).'.jpg';
                $encoded->save(public_path().'/images/news/'.$fileName);

                //Update or store database
                $table = NewsPhoto::find($key); 
                if(!empty($table)){
                    $table->name = $fileName;
                }else{
                    $table = new NewsPhoto;
                    $table->name = $fileName;
                }

                //Validate radio button
                $options = is_array($request->sample_model) ? implode($request->sample_model) : '';
                $value = intval($options);
                if($key == $value){
                    $table->is_cover = 1;
                }else{
                    $table->is_cover = 0;
                }
                $names[] = $table;
            }
            $result = $query->photo()->saveMany($names);
        }
        
        
        // Reset is_cover for all related photos
        NewsPhoto::where('news_id', $id)->update(['is_cover' => 0]);
    
        // Get the selected cover photo ID
        $coverPhotoId = intval($request->input('sample_model', 0));
    
        // Set the selected photo's is_cover to 1
        if ($coverPhotoId) {
            $coverPhoto = NewsPhoto::find($coverPhotoId);
            if ($coverPhoto) {
                $coverPhoto->is_cover = 1;
                $coverPhoto->save();
            }
        }

        $query->name = $request->name;
        $query->summary = $request->summary;
        $query->content = $content;
        $query->venue = $request->venue;
        $query->category_id = $request->category_id;
        $query->slug = str_replace(' ', '-', trim($request->name));
        $result = $query->save();  
        
        if($result){
            request()->session()->flash('success','Successfully updated');
            return redirect()->route($this->path().'.index');
        }
        else{
            request()->session()->flash('error','Error occured while updating');
            return redirect()->back();
        }
    }

    public function destroy(string $id)
    {
        $status = Main::find($id)->delete();
        if($status){
            return response()->json([
                'success' => 'Record has been deleted successfully!'
            ]);
        }
        else{
            return response()->json([
                'error' => 'Error while deleting record'
            ]);
        }
    }
    
    public function destroyPhoto($id)
    {
       
        // Find the photo to be deleted
        $photo = NewsPhoto::find($id);
    
        if (!$photo) {
            return response()->json(['error' => 'Photo not found.'], 404);
        }
    
        // Check if this is the only photo for the associated item
        $totalPhotos = NewsPhoto::where('news_id', $photo->news_id)->count();
        if ($totalPhotos <= 1) {
            return response()->json(['error' => 'Cannot delete the only photo.'], 400);
        }
    
        // Store whether the photo to delete is the cover photo
        $wasCover = $photo->is_cover;
    
        // Delete the file from the server
        $filePath = public_path('images/news/' . $photo->name);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    
        // Delete the photo from the database
        $photo->delete();
    
        // If the deleted photo was the cover, assign a new cover photo
        if ($wasCover) {
            $newCoverPhoto = NewsPhoto::where('news_id', $photo->news_id)->first();
            if ($newCoverPhoto) {
                $newCoverPhoto->is_cover = 1;
                $newCoverPhoto->save();
            }
        }
    
        return response()->json(['success' => 'Photo deleted successfully.']);
    }

}
