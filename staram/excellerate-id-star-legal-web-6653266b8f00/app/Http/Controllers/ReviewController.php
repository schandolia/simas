<?php

namespace App\Http\Controllers;

use App\AttachmentModel;
use App\Mail\DocRequestNotificationEmail;
use App\Model\AssignmentModel;
use App\Model\DocRequest;
use App\Model\DocRequestNotifModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\DocType;
use App\Model\RightModel;
use App\Model\RoleRightModel;
use App\Model\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
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

    public static function getApprovalPath()
    {
        return [1, 5, 6, 7];
    }

    public function index()
    {
        $userInfo = Auth::user();
        switch($userInfo->getRoleKind())
        {
            case 'USER':
                return view('user.docReview')->with('userInfo',$userInfo)->
                with('doctypes',DocType::select('id', 'type')->get())->
                with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                with('notif', RequestDocController::_getNotification());
            case 'APPROVER':
            case 'ADMIN':
                return view('approver.docReview')->with('userInfo',$userInfo)->
                with('doctypes',DocType::select('id', 'type')->get())->
                with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                with('roleKind',$userInfo->role_id)->
                with('notif', RequestDocController::_getNotification());
            case 'LEGAL':
                return view('legalPIC.docReview')->with('userInfo',$userInfo)->
                with('doctypes',DocType::select('id', 'type')->get())->
                with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                with('notif', RequestDocController::_getNotification());
            default:
                return abort(404);
        }
    }

    public function submitReview(Request $request)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        if($role=='USER')
        {
            $request->validate([
                'purpose'=>'required|min:0'
            ]);

            $approvalPaths = json_decode(DocType::select('approval_path')->where('id','=',$request->input('document-type'))->first()->approval_path);
            $approvalCols = RightModel::whereIn('id',$approvalPaths)->
                            whereNotNull('approval_col')->pluck('approval_col');
            $colStr = '';
            $valStr = '';
            foreach($approvalCols as $approvalCol)
            {
                $colStr = $colStr.','.$approvalCol;
                $valStr = ',false'.$valStr;
            }

            $doc = DocRequest::create(array(
                'doc_type'=>$request->input('document-type'),
                'approval_type'=>'REVIEW',
                'proposed_by'=>$request->input('proposed-by'),
                'proposed_date'=>$request->input('date'),
                'purpose'=>$request->input('purpose'),
                'parties'=>$request->input('parties'),
                'description'=>$request->input('description'),
                'commercial_terms'=>$request->input('commercial-terms'),
                'transaction_value'=>$request->input('transaction-value'),
                'late_payment_toleration'=>$request->input('toleration-late-payment'),
                'condition_precedent'=>$request->input('condition-precedent'),
                'termination_terms'=>$request->input('termination-terms'),
                'payment_terms'=>$request->input('payment-terms'),
                'delay_penalty'=>$request->input('delay-terms'),
                'guarantee'=>$request->input('guarantee-security'),
                'agreement_terms'=>$request->input('agreement-terms'),
                'status'=>$approvalPaths[0],
                'nextStatus'=>$approvalPaths[1],
                'requester_id'=>Auth::user()->id
            ));

            //insert into Approval
            DB::insert('insert into doc_approval (req_id'.$colStr.') values ('.$doc->id.$valStr.')');

            $attachment=$request->input('attachment');
            $attachment_path = null;
            if($attachment!=null && $attachment!='undefined')
            {
                $attachment = substr($attachment,strrpos($attachment,'\\')+1);
                $attachment_path = 'storage/'. ($request->file('attachment')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_OTHER',
                    'filename'=>$attachment,
                    'path'=>$attachment_path
                ));
            }

            AssignmentModel::create(array(
                'req_id'=>$doc->id,
                'status_id'=>$approvalPaths[0],
                'assignee_id'=>Auth::user()->id
            ));
            //Make Notification to User
            DocRequestNotifModel::create(array(
                'user_id'=>$userInfo->id,
                'req_id'=>$doc->id,
                'type'=>'TYPE_REVIEW'
                ));
            //Make Notification to Approver
            $approvers = User::select('users.id','users.email','users.name','users.role_id')->whereIn('roles.kind',['APPROVER','ADMIN'])->
                join('roles','roles.id','=','users.role_id')->
                get();
            foreach($approvers as $approver)
            {
                DocRequestNotifModel::create(array(
                    'user_id'=>$approver->id,
                    'req_id'=>$doc->id,
                    'type'=>'TYPE_REVIEW'
                    ));
                try{
                    $roleId = RoleRightModel::where('right_id','=',$doc->nextStatus)->pluck('role_id');
                    if($roleId[0]==$approver->role_id)
                    {
                        if(($roleId[0]==Config::get('Constants.BU_HEAD_ID') && $userInfo->buhead_id==$approver->id)||
                            ($roleId[0]!=Config::get('Constants.BU_HEAD_ID')))
                        {
                            Log::info('Send Review Creation email to'.$approver->email);
                            Mail::to($approver->email)->send(new DocRequestNotificationEmail($approver->name, $doc));
                        }
                    }
                }
                catch(Exception $e)
                {
                    Log::error($e->getMessage());
                }
            }
            return response()->json(['success'=>'done']);
        }
        return abort(404);
    }

    public function _getReviewDocs()
    {
        $userInfo = Auth::user();
        $userId = Auth::user()->id;
        if($userInfo->getRoleKind()=='ADMIN')
            $rights = [0];
        else
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
        $rightsStr = str_replace("]","",str_replace("[","", json_encode($rights)));
            
        if($userInfo->getRoleKind()=='USER')
        {
            return response()->json(DB::table('doc_request_view')->
                select('doc_request_view.id','doc_type','purpose','parties','description',
                        DB::raw("if(status_id=1,true,false) as notif"),
                        DB::raw("(CASE
                            WHEN next_status_id is NULL THEN 'Completed'
                            WHEN status_id<5 THEN 'Pending'
                            WHEN status_id<8 THEN 'Verifying'
                            WHEN status_id=8 THEN 'Hold'
                        END) as status"),'created_at',
                        DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('requester_id', $userInfo->id)->
                where('approval_type','=','REVIEW')->
                where('is_active','=','1')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REVIEW') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                groupBy('doc_request_view.id')->
                get());
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
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
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('approval_type','=', 'REVIEW')->
                where('is_active','=','1')->
                where('users.buhead_id','=',$userInfo->id)->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REVIEW') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id')->
                join('users', 'users.id','=','doc_request_view.requester_id');
            $sql->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"));
            return response()->json($sql->
                orderBy('updated_at','DESC')->
                groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
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
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('approval_type','=', 'REVIEW')->
                where('is_active','=','1')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REVIEW') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
                leftJoin(DB::raw('(SELECT req_id, status FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');

            $assignedRequest = clone $sql;
            $assignedRequest->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"))->
                addSelect(DB::raw("false as approved"));

            $approvedRequest = clone $sql;
            $approvedRequest->where('assignments.assignee_id','=',$userId)->
                join('assignments','assignments.req_id','=','doc_request_view.id')->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"))->
                addSelect(DB::raw("true as approved"));

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereNotIn('next_status_id',$rights)->
                    addSelect(DB::raw('false as notif'))->
                    addSelect(DB::raw("false as approved"));
            $completed = clone $sql;
            $completed->whereNull('next_status_id')->
                addSelect(DB::raw('false as notif'))->
                addSelect(DB::raw("true as approved"));

            return response()->json(DB::query()->
                fromSub($assignedRequest->union($approvedRequest)->
                    union($notAssignedRequest)->
                    union($completed), 'merged')->
                orderBy('updated_at','DESC')->
                groupBy('id')->
                get());
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            $userId = Auth::user()->id;
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $rightsStr = str_replace("]","",str_replace("[","", json_encode($rights)));
            $sql = DB::table('doc_request_view')->
            select('doc_request_view.id','approval_type','doc_type',
                'doc_request_view.purpose','doc_request_view.parties','doc_request_view.description',
                'requester_name','requester_avatar','l_owner_name','l_owner_avatar',
                'status_name','next_status_id',
                'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                'submission.status as verify_status','submission.notes','is_active',
                'doc_request_view.created_at','doc_request_view.updated_at',
                DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
            where('approval_type','=', 'REVIEW')->
            where('is_active','=','1')->
            leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REVIEW') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
            leftJoin(DB::raw('(SELECT req_id, status, notes FROM request_submission) as submission'), 'submission.req_id','=','doc_request_view.id');

            $assignedRequest = clone $sql;
            $assignedRequest->where('doc_request_view.owner_id','=', $userId)->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"))->
                addSelect(DB::raw("false as approved"));

            $approvedRequest = clone $sql;
            $approvedRequest->where('assignments.assignee_id','=',$userId)->
                join('assignments','assignments.req_id','=','doc_request_view.id')->
                addSelect(DB::raw("if(next_status_id IN (".$rightsStr."),true,false) as notif"))->
                addSelect(DB::raw("true as approved"));

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereNull('doc_request_view.owner_id')->whereIn('next_status_id',$rights)->
                addSelect(DB::raw('true as notif'))->
                addSelect(DB::raw("false as approved"));

            return response()->json(DB::query()->
                fromSub($assignedRequest->union($approvedRequest)->union($notAssignedRequest),'merged')->
                orderBy('updated_at','DESC')->
                groupBy('id')->
                get());
        }
        return abort(404);
    }

    public static function _getReviewDocsCount()
    {
        $userInfo = Auth::user();
        $beginState = RightModel::select('id')->orderBy('id','ASC')->first()->id;
        if($userInfo->getRoleKind()=='USER')
        {
            return DB::table('doc_request_view')->select('id')->
                where('requester_id', Auth::user()->id)->
                    where('approval_type','=','REVIEW')->
                    where('is_active','=','1')->
                    distinct()->count();
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            return DB::table('doc_request_view')->select('id')->
                where('approval_type','=','REVIEW')->
                where('is_active','=','1')->
                where('users.buhead_id','=',$userInfo->id)->
                join('users', 'users.id','=','doc_request_view.requester_id')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            return DB::table('doc_request_view')->select('id')->
                where('approval_type','=','REVIEW')->
                where('is_active','=','1')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            $userId = Auth::user()->id;
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $sql = DocRequest::select('doc_request.id')->where('doc_request.approval_type','=', 'REVIEW')->
            where('isActive','=','1');

            $assignedRequest = clone $sql;
            $assignedRequest->where('doc_request.owner_id','=', $userId);

            $approvedRequest = clone $sql;
            $approvedRequest->where('assignments.assignee_id','=',$userId)->
                join('assignments','assignments.req_id','=','doc_request.id');

            $notAssignedRequest = clone $sql;
            $notAssignedRequest->whereNull('doc_request.owner_id')->whereIn('nextStatus',$rights);
            // dd('ini');
            return DB::query()->
                fromSub($assignedRequest->union($approvedRequest)->union($notAssignedRequest),'merged')->
                distinct()->count();
        }
        else
            return 0;
    }
}
