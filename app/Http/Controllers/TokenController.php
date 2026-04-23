<?php
namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\Branch;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('tokens.index', compact('branches'));
    }

    // 🔍 NIC auto-search
    public function findEmployee($nic)
    {
        $emp = DB::table('employees')->where('nic',$nic)->first();

        if ($emp) {
            return response()->json([
                'found'=>true,
                'name'=>$emp->name_with_initial_e,
                'mobile'=>$emp->mobile ?? ''
            ]);
        }

        return response()->json(['found'=>false]);
    }

    // ➕ Create Token
    public function store(Request $request)
    {
        // Find or create visitor
        $visitor = Visitor::where('nic',$request->nic)->first();

        if (!$visitor) {

            $emp = DB::table('employees')->where('nic',$request->nic)->first();

            if ($emp) {
                $visitor = Visitor::create([
                    'nic'=>$request->nic,
                    'name'=>$emp->name_with_initial_e,
                    'mobile'=>$emp->mobile,
                    'is_internal'=>true
                ]);
            } else {
                $visitor = Visitor::create([
                    'nic'=>$request->nic,
                    'mobile'=>$request->mobile,
                    'is_internal'=>false
                ]);
            }
        }

        // Token number
        $last = Token::latest()->first();
        $num = $last ? $last->id + 1 : 1;

        $token = Token::create([
            'token_number'=>'T'.str_pad($num,3,'0',STR_PAD_LEFT),
            'purpose'=>$request->purpose,
            'branch_id'=>$request->branch_id,
            'visitor_id'=>$visitor->id
        ]);

        return back()->with('success','Token '.$token->token_number);
    }

    // ▶ Next Token
    public function next($branch_id)
    {
        $token = Token::where('branch_id',$branch_id)
            ->where('status','waiting')
            ->orderBy('id')
            ->first();

        if ($token) {
            $token->update(['status'=>'serving']);
        }

        return back();
    }

    // ✅ Complete
    public function complete(Request $request, $id)
    {
        $token = Token::findOrFail($id);

        $token->update([
            'status'=>'completed',
            'served_at'=>now(),
            'satisfaction'=>$request->satisfaction
        ]);

        return response()->json(['success'=>true]);
    }

    // 📊 Dashboard
    public function dashboard()
{
    $date = now()->toDateString();

    // Stats (today)
    $total = Token::whereDate('created_at', $date)->count();
    $completed = Token::whereDate('created_at', $date)
        ->where('status','completed')->count();

    $pending = Token::whereDate('created_at', $date)
        ->where('status','waiting')->count();

    $avgSatisfaction = Token::whereDate('created_at', $date)
        ->whereNotNull('satisfaction')
        ->avg('satisfaction') ?? 0;

    $internal = Token::whereDate('created_at', $date)
        ->whereHas('visitor', fn($q)=>$q->where('is_internal',1))->count();

    $external = Token::whereDate('created_at', $date)
        ->whereHas('visitor', fn($q)=>$q->where('is_internal',0))->count();

    // Charts
    $byBranch = Token::selectRaw('branch_id, count(*) as total')
        ->whereDate('created_at', $date)
        ->groupBy('branch_id')->with('branch')->get();

    $byPurpose = Token::selectRaw('purpose, count(*) as total')
        ->whereDate('created_at', $date)
        ->groupBy('purpose')->get();

    $byHour = Token::selectRaw('HOUR(created_at) hour, count(*) total')
        ->whereDate('created_at', $date)
        ->groupBy('hour')->orderBy('hour')->get();

    return view('tokens.dashboard', compact(
        'date','total','completed','pending',
        'avgSatisfaction','internal','external',
        'byBranch','byPurpose','byHour'
    ));
}

public function dashboardData(Request $request)
{
    $date = $request->date ?? now()->toDateString();
    $status = $request->status;

    $query = Token::with(['branch','visitor'])
        ->whereDate('created_at', $date);

    if ($status == 'waiting') {
        $query->where('status','waiting');
    }

    return response()->json($query->orderBy('created_at')->get());
}
}