<?php
namespace App\Http;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Institute;
use App\Models\Stupopulation;
use App\Models\Employee;
use App\Models\Cadresubject;
use App\Models\User;
use App\Models\Todo;
use App\Models\Process;
use App\Models\Cfactivity;
use App\Models\Activityreadlog;
use Illuminate\Support\Facades\DB;
use Auth;

// use Auth;
class Helper{
    public static function messageList()
    {
        $userid = Auth::id();
        return Message::where('reciever_id', $userid)
                        ->whereNull('read_at')
                        ->orderBy('created_at', 'desc')
                        ->get();
    } 

    public static function todoList()
    {
        $userid = Auth::id();
        return Todo::where('user_id', $userid)
                        ->orderBy('created_at', 'desc')
                        ->get();
    } 

    public static function pendingprocess()
    {
        $userid = Auth::id();
        return Process::where('user_id', $userid)
                        ->orderBy('created_at', 'desc')->get();
    }

    public static function activitylog()
    {
        $userid = Auth::id();
        
        return Activityreadlog::orderBy('activity_log.id', 'desc')->rightJoin('activity_log', function ($join) {
            $userid = Auth::id();
            $join->on('activity_read_log.activitylog_id', '=', 'activity_log.id');
        })->get()->where('user_id','!=','2');
    }

    public static function getAllCategory()
    {
        $category=new Category();
        $menu=$category->getAllParentWithChild();
        return $menu;
    } 
    public static function getinstitutes()
    {
            $principal = Employee::select(DB::raw("institute_id,concat(title,'. ',initial,'. ',surname) as principal,mobile"))
                ->where('status', '=', 'Active')
                ->whereIn('designation_id', array(7,9));

            $teachermale = Employee::select(DB::raw("institute_id,count(id) as teachermale"))
                ->where('status', '=', 'Active')
                ->where('gender', '=', 'male')
                ->whereIn('designation_id', array(8,13))
                ->groupBy('institute_id');

            $teacherfemale = Employee::select(DB::raw("institute_id,count(id) as teacherfemale"))
                ->where('status', '=', 'Active')
                ->where('gender', '=', 'female')
                ->whereIn('designation_id', array(8,13))
                ->groupBy('institute_id');

            $grtrainee = Employee::select(DB::raw("institute_id,count(id) as grtrainee"))
                ->where('status', '=', 'Active')
                ->whereIn('designation_id', array(22,27))
                ->groupBy('institute_id');

            $labassistant = Employee::select(DB::raw("institute_id,count(id) as labassistant"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '16')
                ->groupBy('institute_id');

            $libryassistant = Employee::select(DB::raw("institute_id,count(id) as libryassistant"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '19')
                ->groupBy('institute_id');

            $perprincipal = Employee::select(DB::raw("institute_id,count(id) as perprincipal"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '9')
                ->groupBy('institute_id');

            $countprincipal = Employee::select(DB::raw("institute_id,count(id) as countprincipal"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '7')
                ->groupBy('institute_id');

            $schlabour = Employee::select(DB::raw("institute_id,count(id) as schlabour"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '17')
                ->groupBy('institute_id');

            $schwatcher = Employee::select(DB::raw("institute_id,count(id) as schwatcher"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '18')
                ->groupBy('institute_id');

            $sportscoach = Employee::select(DB::raw("institute_id,count(id) as sportscoach"))
                ->where('status', '=', 'Active')
                ->where('designation_id', '=', '26')
                ->groupBy('institute_id');
                
            $subjectclerk = User::select(DB::raw("id,name"));
            
            $institutes = DB::table("institutes as i")
                ->leftjoinSub($principal, 'principal', function ($join) {
                    $join->on('i.id', '=', 'principal.institute_id');
                })
                ->leftjoinSub($teachermale, 'teachermale', function ($join) {
                    $join->on('i.id', '=', 'teachermale.institute_id');
                })
                ->leftjoinSub($teacherfemale, 'teacherfemale', function ($join) {
                    $join->on('i.id', '=', 'teacherfemale.institute_id');
                })
                ->leftjoinSub($grtrainee, 'grtrainee', function ($join) {
                    $join->on('i.id', '=', 'grtrainee.institute_id');
                })
                ->leftjoinSub($labassistant, 'labassistant', function ($join) {
                    $join->on('i.id', '=', 'labassistant.institute_id');
                })
                ->leftjoinSub($libryassistant, 'libryassistant', function ($join) {
                    $join->on('i.id', '=', 'libryassistant.institute_id');
                })
                ->leftjoinSub($perprincipal, 'perprincipal', function ($join) {
                    $join->on('i.id', '=', 'perprincipal.institute_id');
                })
                ->leftjoinSub($countprincipal, 'countprincipal', function ($join) {
                    $join->on('i.id', '=', 'countprincipal.institute_id');
                })
                ->leftjoinSub($schlabour, 'schlabour', function ($join) {
                    $join->on('i.id', '=', 'schlabour.institute_id');
                })
                ->leftjoinSub($schwatcher, 'schwatcher', function ($join) {
                    $join->on('i.id', '=', 'schwatcher.institute_id');
                })
                ->leftjoinSub($sportscoach, 'sportscoach', function ($join) {
                    $join->on('i.id', '=', 'sportscoach.institute_id');
                })
                ->leftjoinSub($subjectclerk, 'subjectclerk', function ($join) {
                    $join->on('i.pfclerk_id', '=', 'subjectclerk.id');
                })
                ->leftjoinSub($subjectclerk, 'acctclerk', function ($join) {
                    $join->on('i.acctclerk_id', '=', 'acctclerk.id');
                })
                ->select(['i.*','subjectclerk.name as subject_clerk','acctclerk.name as account_clerk','tot_boys as stuboys','tot_girls as stugirls','tot_stu as totstu',
                'principal.principal','principal.mobile','teachermale.teachermale','teacherfemale.teacherfemale','grtrainee.grtrainee',
                'labassistant.labassistant','libryassistant.libryassistant','perprincipal.perprincipal','countprincipal.countprincipal',
                'schlabour.schlabour','schwatcher.schwatcher','sportscoach.sportscoach'])
                ->join('student_totals as s', 's.institute_id', '=', 'i.id');

                return $institutes;
    }
    public static function avicdare($institute_array)
    {
        $cadresubjects = Cadresubject::all();

        // Initialize the select array with institute_id
        $select = ['institute_id'];

        // Track aliases to avoid duplicates
        $aliases = [];

        // Loop through cadresubjects to build the query
        foreach ($cadresubjects as $subject) {
            $alias = strtolower(preg_replace('/\s+/', '_', $subject->cadre_code)); // Create a dynamic alias

            // Ensure the alias is unique
            $originalAlias = $alias;
            $counter = 1;
            while (in_array($alias, $aliases)) {
                $alias = $originalAlias . '_' . $counter;
                $counter++;
            }

            $aliases[] = $alias;
            $select[] = DB::raw("sum(if((cadresubject_id = '{$subject->id}'), 1, 0)) AS `avi_{$alias}`");
        }

        // Additional static queries
        $select[] = DB::raw("sum(if((empservice_id = '5'), 1, 0)) as totavi_slps");
        $select[] = DB::raw("(sum(if((empservice_id = '6'), 1, 0)) + sum(if((empservice_id = '7'), 1, 0)) + sum(if((empservice_id = '17'), 1, 0))) as totavi_slts");

        // Build the query
        return Employee::select($select)
            ->where('status', 'Active')
            ->whereIn('institute_id', $institute_array);
    }
}

?>