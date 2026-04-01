<?php

namespace App\Http\Controllers;

use App\Model\AssignmentModel;
use App\Model\DocRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\DocType;
use App\Model\RightModel;
use App\Model\RoleRightModel;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class GenericDocController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function docProcessed()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            return view('user.docProcessed')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
        }
        else if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='ADMIN')
        {
            return view('approver.docProcessed')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
        }
        else
            abort(404);
    }

    public function approved()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='ADMIN')
        {
            return view('approver.docApproved')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
        }
        else
            abort(404);
    }

    public function hold()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            return view('approver.docHold')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
        }
        else
            abort(404);
    }

    public function tobeApproved()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            return view('approver.docTobeApproved')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            return view('legalPIC.docTobeApproved')->with('userInfo',$userInfo)->
            with('doctypes',DocType::select('id', 'type')->get())->
            with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
            with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
            with('roleKind',$userInfo->role_id)->
            with('notif', RequestDocController::_getNotification());
        }
        else
            abort(404);
    }

    public function complete()
    {
        $userInfo = Auth::user();
        switch($userInfo->getRoleKind())
        {
            case 'USER':
                return view('user.docComplete')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
            case 'ADMIN':
            case 'APPROVER':
                return view('approver.docComplete')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
            case 'LEGAL':
                return view('legalPIC.docComplete')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
            default:
                return abort(404);
        }
    }

    public function _getProcessedDocs()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            return response()->json(DB::table('doc_request_view')->
                select('id','approval_type','doc_type','purpose','parties','description',
                    DB::raw("if(status_id=1,true,false) as notif"),
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN status_id<5 THEN 'Pending'
                        WHEN status_id<8 THEN 'Verifying'
                        WHEN status_id=8 THEN 'Hold'
                    END) as status"),'created_at')->
                where('requester_id',Auth::user()->id)->
                whereNotNull('next_status_id')->
                where('status_id','<>','1')->
                where('is_active','=','1')->
                get()
            );
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            $userId = $userInfo->id;
            $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
            if($userInfo->getRoleKind()=='ADMIN')
                $rights = [0];
            else
                $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $rightsStr = str_replace("]","",str_replace("[","", json_encode($rights)));
            $sql = DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'doc_request_view.updated_at')->
                where('is_active','=', '1')->
                whereNotNull('next_status_id')->
                where('status_id','<>',$beginState)->
                where('is_active','=','1')->
                where('users.buhead_id','=',$userId)->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                join('users', 'users.id','=','doc_request_view.requester_id');
            $sql->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"));
            return response()->json($sql->groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            $userId = $userInfo->id;
            $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
            if($userInfo->getRoleKind()=='ADMIN')
                $rights = [0];
            else
                $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $rightsStr = str_replace("]","",str_replace("[","", json_encode($rights)));
            $sql = DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'doc_request_view.updated_at')->
                where('is_active','=', '1')->
                whereNotNull('next_status_id')->
                where('status_id','<>',$beginState)->
                where('is_active','=','1')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');
                $assignedRequest = clone $sql;

            $assignedRequest->where('doc_request_view.owner_id','=', $userId)->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"))->
                addSelect(DB::raw("false as approved"));

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereIn('next_status_id',$rights)->
                addSelect(DB::raw('true as notif'))->
                addSelect(DB::raw("false as approved"));

            $notAssignedRequest2 = clone $sql;
            $notAssignedRequest2->whereNotIn('next_status_id',$rights)->
                addSelect(DB::raw('false as notif'))->
                addSelect(DB::raw("false as approved"));

            return response()->json(DB::query()->
                fromSub($assignedRequest->union($notAssignedRequest)->union($notAssignedRequest2),'merged')->
                groupBy('id')->
                get());
        }
        else
            abort(404);
    }

    public function _getCompletedDocs()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            return response()->json(DB::table('doc_request_view')->
                select('doc_request_view.id','doc_request_view.approval_type','doc_request_view.doc_type',
                    'doc_request_view.purpose','doc_request_view.parties','doc_request_view.description',
                    DB::raw("if(status_id=1,true,false) as notif"),
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN status_id<5 THEN 'Pending'
                        WHEN status_id<8 THEN 'Verifying'
                        WHEN status_id=8 THEN 'Hold'
                    END) as status"),
                    'request_submission.attachment_name',
                    'request_submission.status as verify_status','request_submission.version',
                    'request_submission.date',
                    'doc_request_view.created_at','doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('requester_id',Auth::user()->id)->
                whereNull('next_status_id')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_COMPLETED') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                join('request_submission','request_submission.req_id','=','doc_request_view.id')->
                groupBy('doc_request_view.id')->
                get());
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            return response()->json(DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status','submission.version',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                whereNull('next_status_id')->
                where('users.buhead_id','=',Auth::user()->id)->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_COMPLETED') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status, version FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                join('users', 'users.id','=','doc_request_view.requester_id')->
                groupBy('doc_request_view.id')->
                get());
        }
        else if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='ADMIN')
        {
            return response()->json(DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status','submission.version',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                whereNull('next_status_id')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_COMPLETED') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status, version FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                groupBy('doc_request_view.id')->
                get());
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            return response()->json(DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type',
                    'doc_request_view.purpose','doc_request_view.parties','doc_request_view.description',
                    'requester_name','requester_avatar','l_owner_name','l_owner_avatar',
                    'status_name','next_status_id',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'submission.status as verify_status','submission.notes','is_active',
                    'doc_request_view.created_at','doc_request_view.updated_at',
                    'submission.version',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('assignments.assignee_id', '=', $userInfo->id)->
                whereNull('next_status_id')->
                join('assignments','assignments.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_COMPLETED') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status, notes, version FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                groupBy('doc_request_view.id')->
                get());
        }
        else
            abort(404);
    }

    public static function _getCompletedDocsCount()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            return DocRequest::select('doc_request.id')->
                where('requester_id',Auth::user()->id)->
                whereNull('doc_request.nextStatus')->
                count();
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            return DocRequest::select('doc_request.id')->
                whereNull('doc_request.nextStatus')->
                where('users.buhead_id','=',$userInfo->id)->
                join('users', 'users.id','=','doc_request.requester_id')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='APPROVER' ||$userInfo->getRoleKind()=='ADMIN')
        {
            return DocRequest::select('doc_request.id')->
                whereNull('doc_request.nextStatus')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            return DocRequest::select('doc_request.id')->
                where('doc_request.owner_id','=', $userInfo->id)->
                whereNull('doc_request.nextStatus')->
                distinct()->count();
        }
        return 0;
    }

    public function _getHoldDocs()
    {
        $userInfo = Auth::user();
        $holdId = RightModel::where('right_name','=','Hold')->pluck('id')->first();
        if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            $sql = DB::table('doc_request_view')->
            select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'l_owner_name as holder_name', 'l_owner_avatar as holder_avatar',
                    'doc_request_view.updated_at',
                    DB::raw('if(doc_request_view.last_owner_id='.$userInfo->id.',true,false) as notif'))->
                where('is_active','=', '0')->
                where('assignments.status_id','=', $holdId)->
                where('users.buhead_id','=',$userInfo->id)->
                join('assignments','assignments.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                join('users', 'users.id','=','doc_request_view.requester_id');

            return response()->json($sql->
                groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='APPROVER' ||$userInfo->getRoleKind()=='ADMIN')
        {
            $sql = DB::table('doc_request_view')->
            select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    'doc_request_view.created_at',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active',
                    'l_owner_name as holder_name', 'l_owner_avatar as holder_avatar',
                    'doc_request_view.updated_at',
                    DB::raw('if(doc_request_view.last_owner_id='.$userInfo->id.',true,false) as notif'))->
                where('is_active','=', '0')->
                where('assignments.status_id','=', $holdId)->
                join('assignments','assignments.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');

            return response()->json($sql->
                groupBy('id')->
                get());
        }
        else
            abort(404);
    }

    public static function _getHoldDocsCount()
    {
        $userInfo = Auth::user();
        if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            return DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '0')->
                where('users.buhead_id','=',$userInfo->id)->
                join('users', 'users.id','=','doc_request.requester_id')->
                distinct()->count();
        }
        else
            return DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '0')->
                distinct()->count();
    }

    public function _getApprovedDocs()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            $ApprovalRightIds = RightModel::whereNotNull('approval_col')->pluck('id');
            return response()->json(DB::table('doc_request_view')->
                        select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                            'requester_name','requester_avatar','owner_name','owner_avatar',
                            'doc_request_view.created_at',
                            DB::raw("(CASE
                                WHEN next_status_id is NULL THEN 'Completed'
                                WHEN doc_request_view.status_id<5 THEN 'Pending'
                                WHEN doc_request_view.status_id<8 THEN 'Verifying'
                                WHEN doc_request_view.status_id=8 THEN 'Hold'
                            END) as status"),
                            'submission.status as verify_status',
                            'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                            'next_status_id','is_active',
                            'doc_request_view.updated_at',
                            DB::raw('false as notif'), DB::raw('true as approved'))->
                        where('assignments.assignee_id','=', $userInfo->id)->
                        whereIn('assignments.status_id',$ApprovalRightIds)->
                        join('assignments','assignments.req_id','=','doc_request_view.id')->
                        leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                        groupBy('doc_request_view.id')->
                        where('is_active','=','1')->
                        get());
        }
        else
            abort(404);
    }

    public static function _getApprovedDocsCnt()
    {
        $ApprovalRightIds = RightModel::whereNotNull('approval_col')->pluck('id');
        return DocRequest::select('doc_request.id')->
            where('assignments.assignee_id','=', Auth::user()->id)->
            whereIn('assignments.status_id',$ApprovalRightIds)->
            where('isActive','=','1')->
            join('assignments','assignments.req_id','=','doc_request.id')->distinct()->
            count();
    }

    public function _getTobeApprovedDocs()
    {
        $userInfo = Auth::user();
        if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');

            $sql = DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'doc_request_view.created_at','is_active',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    DB::raw('true as notif'), DB::raw('false as approved'))->
                where('is_active','=', '1')->
                whereIn('next_status_id',$rights)->
                where('users.buhead_id','=',$userInfo->id)->
                where('is_active','=','1')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
join('users', 'users.id','=','doc_request_view.requester_id');
            return response()->json($sql->
                groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            if($userInfo->getRoleKind()=='ADMIN')
                $rights = [0];
            else
                $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');

            $sql = DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type','purpose','parties','description',
                    'doc_request_view.created_at','is_active',
                    DB::raw("(CASE
                        WHEN next_status_id is NULL THEN 'Completed'
                        WHEN doc_request_view.status_id<5 THEN 'Pending'
                        WHEN doc_request_view.status_id<8 THEN 'Verifying'
                        WHEN doc_request_view.status_id=8 THEN 'Hold'
                    END) as status"),
                    'submission.status as verify_status',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id',
                    'requester_name','requester_avatar','owner_name','owner_avatar',
                    DB::raw('true as notif'), DB::raw('false as approved'))->
                where('is_active','=', '1')->
                whereIn('next_status_id',$rights)->
                where('is_active','=','1')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');
            return response()->json($sql->
                groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            $sql = DB::table('doc_request_view')->
                select('doc_request_view.id','approval_type','doc_type',
                    'doc_request_view.purpose','doc_request_view.parties','doc_request_view.description',
                    'requester_name','requester_avatar','l_owner_name','l_owner_avatar',
                    'status_name','next_status_id',
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'submission.status as verify_status','submission.notes','is_active',
                    'doc_request_view.created_at','doc_request_view.updated_at')->
                where('doc_request_view.owner_id', '=', $userInfo->id)->
                where('is_active','=','1')->
                leftJoin(DB::raw('(SELECT req_id, status, notes FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');
            $notSubmitted = clone $sql;
            $notSubmitted->whereNull('submission.status');

            $submitted = clone $sql;
            $submitted->whereIn('submission.status',['STATE_NOT_DONE','STATE_REJECTED']);
            return response()->json(DB::query()->
                fromSub($notSubmitted->union($submitted),'merged')->
                orderBy('updated_at','DESC')->
                groupBy('id')->
                get());
        }
        else
            abort(404);
    }

    public static function _getTobeApprovedDocsCnt()
    {
        $userInfo = Auth::user();
        $userId = $userInfo->id;

        if($userInfo=='LEGAL')
        {
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $sql = DocRequest::select('doc_request.id,request_submission.req_id')->where('doc_request.isActive','=', '1')->
                leftJoin('request_submission','request_submission.req_id','=','doc_request.id')->
                where('doc_request.owner_id','=', $userId)->
                where('isActive','=','1')->
                whereIn('request_submission.status',['STATE_NOT_DONE','STATE_REJECTED']);

           return $sql->distinct()->count();
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            $rights = RoleRightModel::where('role_id','=',$userInfo->role_id)->pluck('right_id');
            $sql = DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '1')->
                where('users.buhead_id','=',$userId)->
                join('users', 'users.id','=','doc_request.requester_id');

            $assignedRequest = clone $sql;
            $assignedRequest->where('doc_request.owner_id','=', $userId);

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereIn('nextStatus',$rights);
            return DB::query()->fromSub($assignedRequest->union($notAssignedRequest),'merged')->
                distinct()->count();
        }
        else
        {
            if($userInfo=='ADMIN')
                $rights = [0];
            else
                $rights = RoleRightModel::where('role_id','=', $userInfo->role_id)->pluck('right_id');
            $sql = DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '1');

            $assignedRequest = clone $sql;
            $assignedRequest->where('doc_request.owner_id','=', $userId);

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereIn('nextStatus',$rights);
            return DB::query()->fromSub($assignedRequest->union($notAssignedRequest),'merged')->
                distinct()->count();
        }
    }

    public static function _getProcessedDocsCnt()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
            return DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '1')
                ->whereNotNull('doc_request.nextStatus')
                ->where('doc_request.status','<>',$beginState)
                ->where('requester_id','=',$userInfo->id)
                ->where('isActive','=','1')
                ->distinct()->count();
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
            return DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '1')
                ->whereNotNull('doc_request.nextStatus')
                ->where('doc_request.status','<>',$beginState)
                ->where('isActive','=','1')
                ->where('users.buhead_id','=',$userInfo->id)
                ->join('users', 'users.id','=','doc_request.requester_id')
                ->distinct()->count();
        }
        else
        {
            $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
            return DocRequest::select('doc_request.id')->where('doc_request.isActive','=', '1')
                ->whereNotNull('doc_request.nextStatus')
                ->where('doc_request.status','<>',$beginState)
                ->where('isActive','=','1')->distinct()->count();
        }
    }
}
