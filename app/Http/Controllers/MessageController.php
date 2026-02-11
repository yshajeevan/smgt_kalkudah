<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if ($request->ajax()) {
            $userid = Auth::id();
            $data = Message::where('reciever_id', $userid)->orderBy('created_at', 'desc');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('F d, Y h:i A'); // human readable format
                      })
                      ->addColumn('action', function($row){
                        $show = route('message.show', $row->id);
                        $delete = route('message.destroy', $row->id);
                    
                        $btn = '';
                        $btn .= "<a href='{$show}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='view' data-placement='bottom'><i class='fas fa-eye'></i></a>";
                        
                        $btn .= "
                            <form action='{$delete}' method='POST' style='display:inline-block;'>
                                " . csrf_field() . "
                                " . method_field('DELETE') . "
                                <button type='submit' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom' onclick='return confirm(\"Are you sure?\")'>
                                    <i class='fas fa-trash'></i>
                                </button>
                            </form>
                        ";
                    
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('message.index');
    }

    public function messageFive()
    {
        $userid = Auth::id();
        $message=Message::where('reciever_id', $userid)
                        ->whereNull('read_at')->limit(5)->get();
        return response()->json($message);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $username = Auth::user()->name;
        $users = User::all();
        return view('message.create',compact('username','users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'reciever' => 'required|array',
            'msg' => 'required',
            'subject' => 'required',
            'file' => 'nullable|mimes:csv,txt,xlx,xls,pdf|max:20480'
        ]);

        $senderid = Auth::id();
        $tags = $request->reciever;

        foreach ($tags as $value) {
            $message = new Message();
            $message->sender_id = $senderid;
            $message->reciever_id = $value;     
            $message->subject = $request->input('subject');
            $message->message = $request->input('msg');

            if ($request->hasFile('file')) {
                // Generate a random file name
                $fileName = Str::random(40) . '.' . $request->file('file')->getClientOriginalExtension();
                // Store the file in the private storage (storage/app/private/attachments)
                $filePath = $request->file('file')->storeAs('private/attachments', $fileName);
                $message->file = $fileName; // Save the file name
            }

            $message->save();
        }

        session()->flash('success', 'Message Successfully Sent');
        return redirect()->back();
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $message = Message::find($id);
        
        if($message){
            $message->read_at=\Carbon\Carbon::now();
            $message->save();
        
            return view('message.show')->with('message',$message);
        } else {
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        // Delete file if it exists
        if ($message->file) {
            Storage::disk('public')->delete('uploads/' . $message->file);
        }

        $message->delete();

        session()->flash('success', 'Message Successfully Deleted');
        return redirect()->back();
    }
}
