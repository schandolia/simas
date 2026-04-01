<?php

namespace App\Http\Controllers;

use App\AttachmentModel;
use App\Mail\DocCompletionNotificationEmail;
use App\Mail\DocRequestNotificationEmail;
use App\Mail\DocSubmissionNotificationEmail;
use App\Mail\DocSubmissionRejectNotificationEmail;
use App\Mail\RejectedDocumentNotifEmail;
use App\Mail\RejectedDocumentRevNotivEmail;
use App\Model\AssignmentModel;
use App\Model\DocRequest;
use App\Model\DocRequestNotifModel;
use App\Model\DocType;
use App\Model\RequestSubmissionAuditModel;
use App\Model\RequestSubmissionModel;
use App\Model\RightModel;
use App\Model\RoleModel;
use App\Model\RoleRightModel;
use App\Model\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function GuzzleHttp\json_encode;

class RequestDocController extends Controller
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
        switch($userInfo->getRoleKind())
        {
            case 'USER':
                return view('user.docRequest')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
            case 'ADMIN':
            case 'APPROVER':
                return view('approver.docRequest')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('roleKind',$userInfo->role_id)->
                    with('notif', RequestDocController::_getNotification());
            case 'LEGAL':
                return view('legalPIC.docRequest')->with('userInfo',$userInfo)->
                    with('doctypes',DocType::select('id', 'type')->get())->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
            default:
                return abort(404);
        }
    }

    public function submitRequest(Request $request)
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
                'approval_type'=>'REQUEST',
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

            $akta=$request->input('akta');
            $akta_path = null;
            if($akta!=null && $akta!='undefined')
            {
                $akta = substr($akta,strrpos($akta,'\\')+1);
                $akta_path = 'storage/'. ($request->file('akta')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_AKTA',
                    'filename'=>$akta,
                    'path'=>$akta_path
                ));
            }

            $npwp=$request->input('npwp');
            $npwp_path = null;
            if($npwp!=null && $npwp!='undefined')
            {
                $npwp = substr($npwp,strrpos($npwp,'\\')+1);
                $npwp_path = 'storage/'. ($request->file('npwp')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_NPWP',
                    'filename'=>$npwp,
                    'path'=>$npwp_path
                ));
            }

            $tdp=$request->input('tdp');
            $tdp_path = null;
            if($tdp!=null && $tdp!='undefined')
            {
                $tdp = substr($tdp,strrpos($tdp,'\\')+1);
                $tdp_path = 'storage/'. ($request->file('tdp')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_TDP',
                    'filename'=>$tdp,
                    'path'=>$tdp_path
                ));
            }

            $ktp=$request->input('ktp');
            $ktp_path = null;
            if($ktp!=null && $ktp!='undefined')
            {
                $ktp = substr($ktp,strrpos($ktp,'\\')+1);
                $ktp_path = 'storage/'. ($request->file('ktp')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_KTP',
                    'filename'=>$ktp,
                    'path'=>$ktp_path
                ));
            }

            $proposal=$request->input('proposal');
            $proposal_path = null;
            if($proposal!=null && $proposal!='undefined')
            {
                $proposal = substr($proposal,strrpos($proposal,'\\')+1);
                $proposal_path = 'storage/'. ($request->file('proposal')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_PROPOSAL',
                    'filename'=>$proposal,
                    'path'=>$proposal_path
                ));
            }

            $other=$request->input('others-attach');
            $other_path = null;
            if($other!=null && $other!='undefined')
            {
                $other = substr($other,strrpos($other,'\\')+1);
                $other_path = 'storage/'. ($request->file('others-attach')->store('request'));
                AttachmentModel::create(array(
                    'req_id'=>$doc->id,
                    'kind'=>'KIND_OTHER',
                    'filename'=>$other,
                    'path'=>$other_path
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
                'type'=>'TYPE_REQUEST'
                ));
            //Make Notification to Approver
            $approvers = User::select('users.id','users.email','users.name','users.role_id')->where('roles.kind','=','APPROVER')->
                join('roles','roles.id','=','users.role_id')->
                get();
            
            foreach($approvers as $approver)
            {
                DocRequestNotifModel::create(array(
                    'user_id'=>$approver->id,
                    'req_id'=>$doc->id,
                    'type'=>'TYPE_REQUEST'
                    ));
                try{
                    $roleId = RoleRightModel::where('right_id','=',$doc->nextStatus)->pluck('role_id');
                    if($roleId[0]==$approver->role_id)
                    {
                        if(($roleId[0]==Config::get('Constants.BU_HEAD_ID') && $userInfo->buhead_id==$approver->id)||
                            ($roleId[0]!=Config::get('Constants.BU_HEAD_ID')))
                        {
                            Log::info('Send Request Creation email to'.$approver->email);
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

    public function processRequest(Request $request)
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='LEGAL')
        {
            $doc =DocRequest::find($request->input('req-submission-docId'));
            //check if user is assigned to the request
            if($doc!=null && $doc->owner_id==$userInfo->id)
            {
                //check if request submission is already there
                $reqId = $doc->id;
                $submission = RequestSubmissionModel::where('req_id','=',$reqId)->first();

                if($submission==null)
                {
                    //create new submission
                    $submission = RequestSubmissionModel::create(array(
                        'req_id'=>$reqId,
                        'submitter_id'=>$userInfo->id
                    ));
                }
                else
                {
                    //move last condition to history table
                    $this->LogRequestSubmission($submission);
                }

                $attachment=$request->input('input-attachment');
                $attachment_path = null;
                if($attachment!=null && $attachment!='undefined')
                {
                    $attachment = substr($attachment,strrpos($attachment,'\\')+1);
                    $attachment_path = 'storage/'. ($request->file('input-attachment')->store('request-submission'));
                }

                //register the record to  'request_submission' table
                $submission->date = date("Y-m-d");
                $submission->agreement_number = $request->input('input-agreement-number');
                $submission->parties = $request->input('input-parties');
                $submission->transaction_objective = $request->input('input-objective');
                $submission->time_period = $request->input('input-period');
                $submission->nominal_transaction = $request->input('input-nominal');
                $submission->terms = $request->input('input-terms');
                $submission->other = $request->input('input-other');
                if($attachment!=null)
                    $submission->attachment_name = $attachment;
                if($attachment_path!=null)
                    $submission->attachment_path = $attachment_path;
                $submission->version = $request->input('input-version');
                $submission->status = 'STATE_DONE';
                $submission->save();

                $approvalPaths = json_decode(DocType::find($doc->doc_type)->approval_path);
                $nextState = $doc->nextStatus;

                for($i=0; $i<sizeof($approvalPaths); ++$i)
                {
                    if($doc->nextStatus==(int)$approvalPaths[$i])
                    {
                        if($i+1==sizeof($approvalPaths))
                            $nextState = null;
                        else
                            $nextState = (int)$approvalPaths[$i+1];
                        break;
                    }
                }

                $apprId = RightModel::select('id')->where('right_name','=','Process')->first()->id;
                    AssignmentModel::create(array(
                        'req_id'=>$doc->id,
                        'status_id'=>$apprId,
                        'assignee_id'=>Auth::user()->id,
                        'comments'=>Auth::user()->fullname.' has finalize the request'
                    ));
                //send notification to Legal Head
                $approvers = User::select('users.id','users.email','users.name','users.role_id')->
                    where('users.role_id','=', Config::get('Constants.LEGAL_HEAD_ID'))->
                    get();
                    
                foreach($approvers as $approver)
                {
                    try{
                        Log::info('Send Request Process Submission Email to '.$approver->email);
                        Mail::to($approver->email)->send(new DocSubmissionNotificationEmail($approver->name, $doc));
                    }
                    catch(Exception $e)
                    {
                        Log::error($e->getMessage());
                    }
                }

                if($doc->nextStatus==null)                    
                    return response()->json(['success'=>'done']);

                //change request status
                if(($doc->status==6)&&($doc->status==7))    //don't change last owner after status is process or Final Approval 
                    ;//do nothing
                else
                    $doc->last_owner_id=$userInfo->id;
                $doc->status = $doc->nextStatus;
                $doc->nextStatus = $nextState;
                $doc->save();
                return response()->json(['success'=>'done']);
            }
            else
                abort(404);
        }
        else
            abort(404);
    }

    public function approveRequest(Request $request)
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            //check if user is valid to perform approval
            $doc = DocRequest::find($request->input('request-id'));
            $rights = RoleRightModel::select('right_id')->where('right_id',$doc->nextStatus)->
                where('role_rights.role_id','=',$userInfo->role_id)->
                exists();

            if(($doc->owner_id==null && $rights==false)||
                ($doc->owner_id!=null && $userInfo->id!=$doc->owner_id))
                return response()->json(['fail'=>'You have no access right to approve this request']);

            if($doc->isActive==false)
                return response()->json(['fail'=>'This request has been rejected.']);

            if($request->input('action')=='hold')
            {
                $hold_id=RightModel::select('id')->where('right_name','=','Hold')->first()->id;
                AssignmentModel::create(array(
                    'req_id'=>$request->input('request-id'),
                    'status_id'=>$hold_id,
                    'assignee_id'=>Auth::user()->id,
                    'comments'=>$request->input('notes')
                ));
                $doc->isActive = false;
                $doc->last_owner_id = $userInfo->id;
                $doc->save();
                try
                {
                    $owner = User::find($doc->requester_id);
                    Mail::to($owner->email)->send(new RejectedDocumentNotifEmail($owner->name, $doc));
                }
                catch(Exception $e)
                {
                    Log::error($e->getMessage());
                }
            }
            else if($request->input('action')=='approve')
            {
                if($doc->nextStatus==null)  //Final Approval shall be done on Submission Approval
                    abort(404);
                $doc = DocRequest::find($request->input('request-id'));
                $approvalPaths = json_decode(DocType::find($doc->doc_type)->approval_path);
                $nextState = $doc->nextStatus;
                $roleCol = RightModel::find($nextState)->approval_col;

                for($i=0; $i<sizeof($approvalPaths); ++$i)
                {
                    if($doc->nextStatus==(int)$approvalPaths[$i])
                    {
                        if($i+1==sizeof($approvalPaths))
                            $nextState = null;
                        else
                            $nextState = (int)$approvalPaths[$i+1];
                        break;
                    }
                }

                $nextOwner =null;
                if($nextState!=null)
                {
                    if(RoleModel::find($userInfo->role_id)->role_name=='Legal Head')
                    {
                        $validRole = RoleRightModel::select('roles.role_name')->where('right_id','=',$nextState)->join('roles','roles.id','=','role_rights.role_id')->first()->role_name;
                        if($request->input('assignee')==null)
                            return response()->json(['fail'=>'No valid user is assigned for this request. Please select <b>'.$validRole.'</b> as assignee']);

                        //check if assignee has right privillege
                        $nextOwner = $request->input('assignee');

                        $isValidUser = RoleRightModel::select('right_id')->
                            where('right_id','=',$nextState)->
                            where('users.id','=',$nextOwner)->
                            join('users','users.role_id','=','role_rights.role_id')->distinct()->count()>0;

                        if($isValidUser==false)
                            return response()->json(['fail'=>'The assignee does not have rights to perform further stage. Please select <b>'.$validRole.'</b> as assignee']);
                        //Make Notification to legal PIC
                        DocRequestNotifModel::create(array(
                            'user_id'=>$nextOwner,
                            'req_id'=>$doc->id,
                            'type'=>$doc->approval_type=='REQUEST'?'TYPE_REQUEST':'TYPE_REVIEW'
                            ));
                        try{
                            $owner = User::find($nextOwner);
                            Mail::to($owner->email)->send(new DocRequestNotificationEmail($owner->name, $doc));
                        }
                        catch(Exception $e)
                        {
                            Log::error($e->getMessage());
                        }
                    }
                    else
                    {
                        $roleId = RoleRightModel::where('right_id','=', $nextState)->pluck('role_id')[0];
                        $approvers = User::select('users.id','users.email','users.name','users.role_id')->where('users.role_id','=',$roleId)->
                            get();
                        $buHeadId = User::find($doc->requester_id)->buhead_id;

                        foreach($approvers as $approver)
                        {
                            try{
                                if(($roleId==Config::get('Constants.BU_HEAD_ID') && $buHeadId==$approver->id)||
                                    ($roleId!=Config::get('Constants.BU_HEAD_ID')))
                                {
                                        Log::info('Send Approve Notification Email to '.$approver->email);
                                        Mail::to($approver->email)->send(new DocRequestNotificationEmail($approver->name, $doc));
                                }
                            }
                            catch(Exception $e)
                            {
                                Log::error($e->getMessage());
                            }
                        }
                    }
                }
                AssignmentModel::create(array(
                        'req_id'=>$request->input('request-id'),
                        'status_id'=>$doc->nextStatus,
                        'assignee_id'=>Auth::user()->id,
                        'assigner_id'=>$doc->owner_id,
                        'comments'=>$request->input('notes')
                    ));
                $doc->status = $doc->nextStatus;
                $doc->nextStatus = $nextState;
                $doc->owner_id= $nextOwner;
                if(($doc->status==6)&&($doc->status==7))
                    ;//do nothing
                else
                    $doc->last_owner_id = $userInfo->id;
                $doc->save();
                if($roleCol!=null)
                    DB::update('UPDATE doc_approval SET '.$roleCol.'=1 WHERE req_id='.$doc->id);
                return response()->json(['success'=>'done']);
            }
            else
                abort(404);
        }
        else
            abort(404);
    }

    public function activateRequest(Request $request)
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            $doc = DocRequest::where('id','=',$request->input('request-id'))->firstOrFail();
            $activate_id=RightModel::select('id')->where('right_name','=','Activate')->first()->id;
            AssignmentModel::create(array(
                'req_id'=>$request->input('request-id'),
                'status_id'=>$activate_id,
                'assignee_id'=>Auth::user()->id,
            ));
            $doc->isActive = true;
            $doc->save();
            return response()->json(['success'=>'done']);
        }
        return abort(404);
    }

    public function _getRequestDocs()
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
                    where('requester_id',Auth::user()->id)->
                    where('approval_type','=','REQUEST')->
                    where('is_active','=','1')->
                    leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REQUEST') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
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
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active','submission.status as verify_status',
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('approval_type','=', 'REQUEST')->
                where('is_active','=','1')->
                where('users.buhead_id','=',$userInfo->id)->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REQUEST') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
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
                    'ceo_approved', 'cfo_approved','bu_approved','legal_approved',
                    'next_status_id','is_active','submission.status as verify_status',
                    'doc_request_view.updated_at',
                    DB::raw("if(notif_table.id IS NULL, false, true) as new"))->
                where('approval_type','=', 'REQUEST')->
                where('is_active','=','1')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REQUEST') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
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
                    union($notAssignedRequest)->union($completed),'merged')->
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
                where('approval_type','=', 'REQUEST')->
                where('is_active','=','1')->
                leftJoin(DB::raw("(SELECT * FROM doc_request_notifs WHERE user_id = ".$userInfo->id." AND `type` = 'TYPE_REQUEST') as notif_table"),'notif_table.req_id','=','doc_request_view.id')->
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

            return response()->json(DB::query()->
                fromSub($assignedRequest->union($approvedRequest),'merged')->
                orderBy('updated_at','DESC')->
                groupBy('id')->
                get());
        }
    }

    public static function _getRequestedDocsCount()
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            return DB::table('doc_request_view')->select('id')->
                where('requester_id', Auth::user()->id)->
                    where('approval_type','=','REQUEST')->
                    where('is_active','=','1')->
                    distinct()->count();
        }
        else if($userInfo->role_id==Config::get('Constants.BU_HEAD_ID'))
        {
            return DB::table('doc_request_view')->select('id')->
                where('approval_type','=','REQUEST')->
                where('is_active','=','1')->
                where('users.buhead_id','=',$userInfo->id)->
                join('users', 'users.id','=','doc_request_view.requester_id')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            return DB::table('doc_request_view')->select('id')->
                where('approval_type','=','REQUEST')->
                where('is_active','=','1')->
                distinct()->count();
        }
        else if($userInfo->getRoleKind()=='LEGAL')
        {
            $userId = Auth::user()->id;
            $rights = RoleRightModel::where('role_id','=',Auth::user()->role_id)->pluck('right_id');
            $sql = DocRequest::select('doc_request.id')->where('doc_request.approval_type','=', 'REQUEST')->
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

    public function _getRequestDetails($requestId, $mark=false)
    {
        $userInfo = Auth::user();
        //remove marking from notification
        if($mark=='mark')
        {
            $notif = DocRequestNotifModel::where('req_id','=',$requestId)->where('user_id','=', $userInfo->id);
            $notif->delete();
        }
        $request = DB::table('doc_request_view')->
            select('id','approval_type','doc_type','proposed_by','proposed_date','purpose','parties','description','commercial_terms',
                'transaction_value','late_payment_toleration','condition_precedent','termination_terms',
                'delay_penalty','guarantee','payment_terms','agreement_terms', 'is_active')->
            where('id','=',$requestId);
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            if($userInfo->getRoleKind()=='ADMIN')
                $myRights =json_encode([0]);
            else
                $myRights = json_encode(RoleRightModel::where('role_id','=', $userInfo->role_id)->pluck('right_id'));
            $myRights = str_replace("]","",str_replace("[","",$myRights));
            $request->addSelect(DB::raw("if(next_status_id IN (".$myRights."),true,false) as notif"));
        }
        $request=$request->first();
        if($request==null)
            abort(404);
        $attachments = AttachmentModel::select('id','kind','filename')->where('req_id','=',$requestId)->get();
        $request->attachments=$attachments;
        $attachments = AttachmentModel::select('id','kind','filename')->where('req_id','=',$requestId)->get();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            $history = AssignmentModel::select('rights.right_name as state',
                    'users.fullname','assignments.comments','assignments.updated_at')->
                where('assignments.req_id','=',$requestId)->
                join('rights','rights.id','<=>','assignments.status_id')->
                join('users','users.id','<=>','assignments.assignee_id')->
                union(AssignmentModel::select('rights.right_name as state',
                        DB::raw('NULL as fullname'), DB::raw('NULL as avatar'), DB::raw('NULL as updated_at'))->
                    where('assignments.req_id','=',$requestId)->
                    whereNull('assignments.assignee_id')->
                    join('rights','rights.id','<=>','assignments.status_id')
                )->
                orderBy('updated_at','asc')->
                get();
            $request->history=$history;

            //get Request Submission
            $submission = RequestSubmissionModel::select('status')->where('req_id','=',$requestId)->first();

            $request->submission = $submission;

        }
        if($request!=null)
        {
            return response()->json($request);
        }
    }

    public function _getRequestAttachment($attachmentId)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        $query = null;
        switch($role)
        {
            case 'USER':
                $query = AttachmentModel::select('attachments.filename','attachments.path')->
                    where('doc_request.requester_id','=',Auth::user()->id)->
                    where('attachments.id','=',$attachmentId)->
                    join('doc_request','doc_request.id','=','attachments.req_id')->first();
                break;
            case 'APPROVER':
            case 'ADMIN':
            case 'LEGAL':
                $query = AttachmentModel::select('attachments.filename','attachments.path')->
                    where('attachments.id','=',$attachmentId)->first();
                break;
            default:
                abort(404);
        }
        if($query==null)
            abort(404);
        else
            return response()->download($query->path, $query->filename);
    }

    public function _getLatestSubmission($requestId)
    {
        $submission = RequestSubmissionModel::select('req_id','date','agreement_number','parties','transaction_objective',
                'time_period','nominal_transaction','terms','other','attachment_name')->
            where('req_id','=',$requestId)->firstOrFail();
        return response()->json($submission);
    }

    public function _getSubmissionAttachment($requestId, $submissionId=null)
    {
        $userInfo = Auth::user();
        $role = $userInfo->getRoleKind();
        $query = null;
        if($submissionId==null)
            $query = DB::table('request_submission')->select('attachment_name','attachment_path')->
                where('request_submission.req_id', '=', $requestId)->
                join('doc_request','doc_request.id','=','request_submission.req_id');
        else
            $query = DB::table('request_submission_audit')->select('attachment_name','attachment_path')->
                where('request_submission_audit.id','=',$submissionId)->
                where('request_submission_audit.req_id', '=', $requestId)->
                join('doc_request','doc_request.id','=','request_submission_audit.req_id');
        switch($role)
        {
            case 'USER':
                $query = $query ->where('doc_request.requester_id','=',Auth::user()->id)->
                    first();
                break;
            case 'APPROVER':
            case 'ADMIN':
            case 'LEGAL':
                $query = $query->first();
                break;
            default:
                abort(404);
        }

        if($query==null)
            abort(404);
        else
        {
            try{
                return response()->download($query->attachment_path, $query->attachment_name);
            }
            catch(Exception $e)
            {
                abort(404);
            }
        }
    }

    public function approveRequestSubmission(Request $request)
    {
        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='APPROVER'||$userInfo->getRoleKind()=='ADMIN')
        {
            $submission = RequestSubmissionModel::where('req_id','=', $request->input('sub-request-id'))->
                firstOrFail();
            $submissionState=$submission->status;
            if($request->input('action')=='reject')
            {
                //copy current submission to history
                $this->LogRequestSubmission($submission);
                if($submissionState=='STATE_TOBE_REVISE') {
                    $rejectId = RightModel::select('id')->where('right_name','=','Reject Request Revise')->first()->id;
                    $submission->status = $submission->status = 'STATE_APPROVED';
                }
                else {
                    $rejectId = RightModel::select('id')->where('right_name','=','Reject')->first()->id;
                    $submission->status = $submission->status = 'STATE_REJECTED';
                }
                $submission->submitter_id = $userInfo->id;
                $submission->notes = $request->input('notes');
                $submission->save();

                AssignmentModel::create(array(
                    'req_id'=>$submission->req_id,
                    'status_id'=>$rejectId,
                    'assignee_id'=>$userInfo->id,
                    'comments'=>$request->input('notes')
                ));
                if($submissionState=='STATE_TOBE_REVISE') {
                    $doc = DocRequest::where('id','=', $submission->req_id)->firstOrFail();
                    $owner = User::find($doc->requester_id);
                    Mail::to($owner->email)->send(new RejectedDocumentRevNotivEmail($owner->name, $doc, $request->input('notes')));
                }
                else{
                    //send Notification to Legal PIC
                    $doc = DocRequest::find($submission->req_id);
                    $submitter = User::find($doc->owner_id);
                    try{
                        Log::info('Send Reject Submission Email to '.$submitter->email);
                        Mail::to($submitter->email)->send(new DocSubmissionRejectNotificationEmail($submitter->name, $doc, $request->input('notes')));
                    }
                    catch(Exception $e)
                    {
                        Log::error($e->getMessage());
                    }
                }
                return response()->json(['success'=>'done']);
            }
            else if($request->input('action')=='approve')
            {
                
                //copy current submission to history
                $this->LogRequestSubmission($submission);

                $doc = DocRequest::where('id','=',$submission->req_id)->firstOrFail();
                
                $approvalPaths = json_decode(DocType::find($doc->doc_type)->approval_path);
                $nextState = $doc->nextStatus;
                $roleCol = RightModel::find($nextState);
                if($roleCol!=null)
                    $roleCol = $roleCol->approval_col;

                for($i=0; $i<sizeof($approvalPaths); ++$i)
                {
                    if($doc->nextStatus==(int)$approvalPaths[$i])
                    {
                        if($i+1==sizeof($approvalPaths))
                            $nextState = null;
                        else
                            $nextState = (int)$approvalPaths[$i+1];
                        break;
                    }
                }
                $submission->status = $submission->status=='STATE_DONE'?'STATE_APPROVED':'STATE_NOT_DONE';
                $submission->submitter_id=$userInfo->id;
                $submission->save();

                if($submission->status=='STATE_APPROVED')
                {
                    //Make Notification to User
                    DocRequestNotifModel::create(array(
                        'user_id'=>$doc->requester_id,
                        'req_id'=>$doc->id,
                        'type'=>'TYPE_COMPLETED'
                        ));
                    //Make Notification to Legal
                    DocRequestNotifModel::create(array(
                        'user_id'=>$doc->owner_id,
                        'req_id'=>$doc->id,
                        'type'=>'TYPE_COMPLETED'
                        ));
                    //Make Notification to Approver
                    $approvers = User::where('roles.kind','=','APPROVER')->
                        join('roles','roles.id','=','users.role_id')->
                        pluck('users.id');
                    foreach($approvers as $approver)
                    {
                        DocRequestNotifModel::create(array(
                            'user_id'=>$approver,
                            'req_id'=>$doc->id,
                            'type'=>'TYPE_COMPLETED'
                            ));
                    }
                }
                if($doc->nextStatus==null)
                {
                    if($submission->status=='STATE_APPROVED'){
                        $apprId = RightModel::select('id')->where('right_name','=','Final Approval')->first()->id;       
                    }
                    else{
                        $apprId = RightModel::select('id')->where('right_name','=','Approve Request Revise')->first()->id;
                    }
                    AssignmentModel::create(array(
                        'req_id'=>$doc->id,
                        'status_id'=>$apprId,
                        'assignee_id'=>Auth::user()->id,
                        'assigner_id'=>$doc->owner_id,
                        'comments'=>$request->input('notes')
                    )); 
                    //send notification to user
                    try
                    {
                        $owner = User::find($doc->requester_id);
                        Log::info('Send Request Completion email to '.$owner->email);
                        Mail::to($owner->email)->send(new DocCompletionNotificationEmail($owner->name, $doc));
                    }
                    catch(Exception $e)
                    {
                        Log::error($e->getMessage());
                    }
                    return response()->json(['success'=>'done']);
                }

                AssignmentModel::create(array(
                    'req_id'=>$doc->id,
                    'status_id'=>$doc->nextStatus,
                    'assignee_id'=>Auth::user()->id,
                    'assigner_id'=>$doc->owner_id,
                    'comments'=>$request->input('notes')
                ));

                $doc->status = $doc->nextStatus;
                $doc->nextStatus = $nextState;
                if(($doc->status==6)&&($doc->status==7))
                    ;//do nothing
                else
                    $doc->last_owner_id = $userInfo->id;
                $doc->save();
                if($roleCol!=null)
                    DB::update('UPDATE doc_approval SET '.$roleCol.'=1 WHERE req_id='.$doc->id);
                return response()->json(['success'=>'done']);
            }
            else
                abort(404);
        }
        else
            abort(404);
    }
    public function reviseRequest(Request $request)
    {
        $userId = Auth::user()->id;

        $docExists = DocRequest::where('id','=', $request->input('req-submission-docId'))->
            where('requester_id','=',$userId)->exists();
        if($docExists)
        {
            $submission = RequestSubmissionModel::where('req_id','=',$request->input('req-submission-docId'))->first();
            $this->LogRequestSubmission($submission);
            $submission->submitter_id = $userId;
            $submission->status = 'STATE_NOT_DONE';
            $submission->notes = $request->input('notes');
            $attachment=$request->file('input-attachment');
            if($attachment!=null)
            {
                $submission->attachment_path = 'storage/'. ($attachment->store('request-submission'));
                $submission->attachment_name = $attachment->getClientOriginalName();
            }
            $submission->save();
            
            //log to assignmentModel
            $reqReviseId = RightModel::select('id')->where('right_name','=','Request Revise')->first()->id;
            AssignmentModel::create(array(
                'req_id'=>$submission->req_id,
                'status_id'=>$reqReviseId,
                'assignee_id'=>$userId,
                'comments'=>$request->input('notes')
            ));
            return response()->json(['success'=>'done']);
        }
        else
            abort(404);
    }
    private function LogRequestSubmission($submission)
    {
        RequestSubmissionAuditModel::create(array(
            'submitter_id'=>$submission->submitter_id,
            'req_id'=>$submission->req_id,
            'date'=>$submission->date,
            'agreement_number'=>$submission->agreement_number,
            'parties'=>$submission->parties,
            'transaction_objective'=>$submission->transaction_objective,
            'time_period'=>$submission->time_period,
            'nominal_transaction'=>$submission->nominal_transaction,
            'terms'=>$submission->terms,
            'other'=>$submission->other,
            'attachment_name'=>$submission->attachment_name,
            'attachment_path'=>$submission->attachment_path,
            'status'=>$submission->status,
            'notes'=>$submission->notes,
            'version'=>$submission->version,
            'created_at'=>$submission->created_at,
            'updated_at'=>$submission->updated_at)
        );
    }
    public static function _getNotification()
    {
        $notif = (object)[
            'request'=>0,
            'review'=>0,
            'complete'=>0
        ];
        $notif->request = DocRequestNotifModel::select('req_id')->where('user_id','=', Auth::user()->id)->
            where('type','=','TYPE_REQUEST')->distinct()->count();
        $notif->review = DocRequestNotifModel::select('req_id')->where('user_id','=', Auth::user()->id)->
            where('type','=','TYPE_REVIEW')->distinct()->count();
        $notif->complete = DocRequestNotifModel::select('req_id')->where('user_id','=', Auth::user()->id)->
            where('type','=','TYPE_COMPLETED')->distinct()->count();
        return $notif;
    }
}
