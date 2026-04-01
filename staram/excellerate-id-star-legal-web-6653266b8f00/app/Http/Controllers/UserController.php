<?php

namespace App\Http\Controllers;

use App\Model\DocRequest;
use App\Model\DocType;
use App\Model\RoleModel;
use Illuminate\Http\Request;
use App\Model\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='ADMIN')
        {
            return view('approver.availablePIC')->with('userInfo',$userInfo)->
                with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                with('members', $this->_getMembersWithDetail())->
                with('notif', RequestDocController::_getNotification());
        }
        else
            abort(404);
    }

    public function userSetting()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()!='ADMIN')
            abort(404);
        else
        {
            return view('userSettings')->with('userInfo',$userInfo)->
                    with('doctypes',RoleModel::select('id', 'role_name')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('members', $this->_getMembersWithDetail())->
                    with('buheads', $this->_getBUHeads())->
                    with('notif', RequestDocController::_getNotification());
        }
    }

    public function getAllUser()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()!='ADMIN')
            abort(404);
        else
            return response()->json(User::select('users.id','name', 'users.fullname', 'email', 'avatar','logged_in','last_login', 'role_id', 'roles.role_name as role', 'buhead_id', 'buheads.fullname as buhead_name')->
                join('roles', 'roles.id', '=','users.role_id')->
                leftJoin(DB::raw('(select id, fullname from users) as buheads'),'buheads.id','=','users.buhead_id')->
                get());
    }

    public function changeRole(Request $request)
    {
        if(Auth::user()->getRoleKind()!='ADMIN')
            abort(404);
        else
        {
            $user = User::findOrFail($request->input('user-id'));
            $user->role_id = $request->input('role-id');
            if($user->role_id==1)
            {
                $buheadId = $request->input('buhead-id');
                if($buheadId==0)
                    $user->buhead_id=null;
                else
                    $user->buhead_id=$buheadId;
            }
            else
                $user->buhead_id=null;
            $user->save();
            return response()->json(['success'=>'done']);
        }
    }

    public function _getAuthorizeUser()
    {
        return response()->json(User::select('users.id','users.name','users.fullname','users.avatar','roles.role_name')->
            whereIn('roles.kind', array('APPROVER','LEGAL','ADMIN'))->
            join('roles','roles.id','=','users.role_id')->
            groupBy('users.id')->
            orderBy('users.fullname', 'ASC')->
            get());
    }

    public function _getBUHeads()
    {
        return User::select('users.id','users.name','users.fullname','users.avatar','roles.role_name')->
            where('roles.role_name', 'BU Head')->
            join('roles','roles.id','=','users.role_id')->
            groupBy('users.id')->
            orderBy('users.fullname', 'ASC')->
            get();
    }

    public function _getUser()
    {
        return response()->json(User::select('users.id','name','fullname','avatar','roles.role_name')->
            join('roles','roles.id','=','users.role_id')->
            groupBy('users.id')->
            orderBy('fullname', 'ASC')->
            get());
    }

    public function _getLegalPIC()
    {
        return response()->json(User::select('users.id','users.name','fullname','avatar','roles.role_name')->
            where('roles.role_name','=','Legal PIC')->
            join('roles','roles.id','=','users.role_id')->
            groupBy('users.id')->
            orderBy('fullname', 'ASC')->
            get());
    }

    public function _getMembersWithDetail()
    {
        $legals = User::select('users.id','users.name','roles.role_name as role','email','fullname','avatar', 'created_at','last_login','logged_in')->
            where('roles.role_name','=','Legal PIC')->
            join('roles','roles.id','=','users.role_id')->
            orderBy('users.name', 'ASC')->
            get();
        $legalCnt = sizeof($legals);
        for($i=0;$i<$legalCnt;++$i)
        {
            $legals[$i]->assigned_docs = DocRequest::select('id')->where('owner_id','=', $legals[$i]->id)->count();
            $legals[$i]->completed_docs = DocRequest::select('id')->where('owner_id','=', $legals[$i]->id)->
                whereNull('nextStatus')->count();
            $legals[$i]->notCompleted_docs = DocRequest::select('id')->where('owner_id','=', $legals[$i]->id)->
                whereNotNull('nextStatus')->count();
            $legals[$i]->revise_docs = DocRequest::select('doc_request.id')->where('owner_id','=', $legals[$i]->id)->
                whereIn('request_submission.status',['STATE_NOT_DONE','STATE_REJECTED','STATE_TOBE_REVISE'])->
                join('request_submission','request_submission.req_id','=','doc_request.id')->count();
        }
        return $legals;
    }

    public static function _getMembersCount()
    {
        return User::select('id')->
            where('roles.role_name','=','Legal PIC')->
            join('roles','roles.id','=','users.role_id')->distinct()->count();
    }
}
