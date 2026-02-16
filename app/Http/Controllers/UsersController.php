<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Rules\MatchOldPassword;
use DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:user-list|role-create|role-edit|role-delete', ['only' => ['index']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = User::with('employee:id,name_with_initial_e,photo')->with('roles:id,name')->select('employee_id','users.email','users.id','users.is_active');

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('photo', function (User $user) { 
                        if (!empty($user->employee_id)) {
                            $photo = $user->employee_id . '.jpg';
                            $path = public_path("images/employees/$photo");
                            if (!file_exists($path)) {
                                $photo = 'avatar.jpg';
                            }
                        } else {
                            $photo = 'avatar.jpg';
                        }

                        // Final fallback if avatar.jpg not found
                        $fallbackPath = public_path("images/employees/$photo");
                        if (!file_exists($fallbackPath)) {
                            $url = asset("backend/img/avatar.png");
                        } else {
                            $url = asset("images/employees/$photo");
                        }

                        return '<img src="'.$url.'" border="0" width="40" class="img-rounded" align="center" />';
                    })
                    ->addColumn('name_with_initial_e', function(User $user){
                        return $user->employee->name_with_initial_e ?? 'N/A';
                    })
                    ->addColumn('roles', function (User $user) {
                        return implode(', ', $user->roles->pluck('name')->toArray());
                    })
                    ->addColumn('action', function($row){
                        $edit =  route('user.edit',$row->id);
                        $delete =  route('user.destroy',$row->id);
                        $btn = '';
                        $btn = "<a href='{$edit}' class='btn btn-xs btn-primary btn-sm' style='height:30px; width:30px;' title='show' data-placement='bottom'><i class='fas fa-edit'></i></a>";
                        $btn = $btn."<a href='{$delete}' onclick='return confirm(`Are you sure want to delete this record?`)' class='btn btn-xs btn-danger btn-sm' style='height:30px; width:30px;' title='delete' data-placement='bottom'><i class='fas fa-trash'></i></a>";
                        return $btn;
                    })
                    ->rawColumns(['photo','action'])
                    ->make(true);
        }
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
        $roles = Role::get();
        return view('users.createOrUpdate',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(Request $request)
    {
        $this->validate($request,
        [
            'employee_id' => 'required',
            'institute_id' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles'=>'required',
            'is_active'=>'required|in:1,0',
        ]);
        
        $user = User::create([
            'employee_id' => $request->employee_id,
            'institute_id' => $request->institute_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => $request->is_active,
        ]);
    
        $status = $user->assignRole($request->input('roles'));

        if($status){
            request()->session()->flash('success','Successfully added user');
            return redirect()->route('user.index');
        }
        else{
            request()->session()->flash('error','Error occurred while adding user');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::findOrFail($id);
        $userrole = $item->roles->pluck('name')->toArray();
        $roles = Role::get();
    
        return view('users.createOrUpdate',compact('item','userrole','roles'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'institute_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required',
            'is_active' => 'required|in:1,0',
            'password' => 'nullable|min:6|same:confirm-password',
        ]);

        $user = User::find($id);
        $user->email = $request->email;
        $user->name = $request->name;
        $user->institute_id = $request->institute_id; 
        $user->is_active = $request->is_active;

         // 🔥 Password update only if BOTH fields are entered + matched
        if ($request->filled('password') && $request->filled('confirm-password')) {
            if ($request->password === $request->{'confirm-password'}) {
                $user->password = Hash::make($request->password);
            }
        }

        $user->save();

        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $status = $user->assignRole($request->input('roles'));

        if ($status) {
            session()->flash('success', 'Successfully updated');
            return redirect()->route('user.index');
        } else {
            session()->flash('error', 'Error occurred while updating');
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        $user = User::findorFail($id);
        $status = $user->delete();
        if($status){
            request()->session()->flash('success','User Successfully deleted');
            return redirect()->route('user.index');
        }
        else{
            request()->session()->flash('error','There is an error while deleting users');
            return redirect()->back();
        }
        
    }
    public function changePassword(){
        return view('users.changePassword');
    }
    public function changPasswordStore(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => [
                'required',
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        return redirect()->route('dashboard')->with('success','Password successfully changed');
    }
}
