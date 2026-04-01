<?php

namespace App\Http\Controllers;

use App\Model\DocRequest;
use App\Model\DocReview;
use App\Model\DocType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use League\Flysystem\Config;

class DashboardController extends Controller
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

    public function root()
    {
        return redirect()->route('login');
    }

    public function home()
    {
        return redirect()->route('dashboard');
    }

    public function index()
    {
        $userInfo = Auth::user();
        switch($userInfo->getRoleKind())
        {
            case 'USER':
                return view('user.dashboard')->with('userInfo',$userInfo)->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('newReviewCnt',ReviewController::_getReviewDocsCount())->
                    with('processedDocsCnt',GenericDocController::_getProcessedDocsCnt())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('totalRequestinYear', $this->_getTotalRequestInYear())->
                    with('notif', RequestDocController::_getNotification());
            case 'ADMIN':
            case 'APPROVER':
                return view('approver.dashboard')->with('userInfo',$userInfo)->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('newReviewCnt',ReviewController::_getReviewDocsCount())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('availablePIC', UserController::_getMembersCount())->
                    with('holdDocsCnt',GenericDocController::_getHoldDocsCount())->
                    with('approvedDocsCnt',GenericDocController::_getApprovedDocsCnt())->
                    with('processedDocsCnt',GenericDocController::_getProcessedDocsCnt())->
                    with('tobeApprovedDocsCnt',GenericDocController::_getTobeApprovedDocsCnt())->
                    with('notif', RequestDocController::_getNotification());

            case 'LEGAL':
                return view('legalPIC.dashboard')->with('userInfo',$userInfo)->
                    with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
                    with('newReviewCnt',ReviewController::_getReviewDocsCount())->
                    with('tobeApprovedDocsCnt',GenericDocController::_getTobeApprovedDocsCnt())->
                    with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
                    with('notif', RequestDocController::_getNotification());
            default:
                return abort(404);
        }
    }

    public function emailUs(Request $request)
    {
        $recepients = config('Constants.EMAIL_US_RECEPIENTS');
        $data = array('content'=>$request->input('email-body'));
        $sender = Auth::user();
        Mail::send([], [], function($message) use ($recepients, $request, $sender)
        {
            $message->from( $sender->email, $sender->fullname);
            $message->to($recepients)->subject($request->input('email-title'));
            $message->setBody($request->input('email-body'), 'text/html');
        });

        if (Mail::failures())
            return response()->Fail('Sorry! Please try again latter');
        else
            return response()->json(['success'=>'done']);
    }

    public function _getTotalRequestInYear()
    {
        $docsRequested=(object)array(
            'totalRequested'=>0,
            'detailsRequested'=>[0,0,0,0,0,0,0,0,0,0,0,0],
            'totalCompleted'=>0,
            'detailsCompleted'=>[0,0,0,0,0,0,0,0,0,0,0,0]);
        $currentYear = (INT)date('Y');
        $currentMonth = (INT)date('m');
        $nextYear = $currentYear + 1;
        $nextMonth =  $currentMonth + 1;

        $userInfo = Auth::user();
        if($userInfo->getRoleKind()=='USER')
        {
            $docsRequested->totalRequested = DocRequest::where('requester_id','=',$userInfo->id)->
                whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::where('requester_id','=',$userInfo->id)->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::where('requester_id','=',$userInfo->id)->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            $docsRequested->totalCompleted = DocRequest::where('requester_id','=',$userInfo->id)->
                whereNull('nextStatus')->
                whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::where('requester_id','=',$userInfo->id)->
                        whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::where('requester_id','=',$userInfo->id)->
                        whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            return response()->json($docsRequested);
        }
        else if($userInfo->getRoleKind()=='APPROVER' || $userInfo->getRoleKind()=='ADMIN')
        {
            $docsRequested->totalRequested = DocRequest::whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            $docsRequested->totalCompleted = DocRequest::whereNull('nextStatus')->
                whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            return response()->json($docsRequested);
        }
        else{
            $docsRequested->totalRequested = DocRequest::where('owner_id','=',$userInfo->id)->
                whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::where('owner_id','=',$userInfo->id)->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsRequested[$i-1]=DocRequest::where('owner_id','=',$userInfo->id)->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            $docsRequested->totalCompleted = DocRequest::where('owner_id','=',$userInfo->id)->
                whereNull('nextStatus')->
                whereBetween('created_at', [$currentYear.'-01-01 00:00:00', $nextYear.'-01-01 00:00:00'])->
                count();
            for($i=1; $i<=$nextMonth;++$i)
            {
                if($i<12)
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::where('owner_id','=',$userInfo->id)->
                        whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $currentYear.'-'.($i+1).'-01 00:00:00'])->
                        count();
                }
                else
                {
                    $docsRequested->detailsCompleted[$i-1]=DocRequest::where('owner_id','=',$userInfo->id)->
                        whereNull('nextStatus')->
                        whereBetween('created_at', [$currentYear.'-'.$i.'-01 00:00:00',
                                                    $nextYear.'-'.'-01-01 00:00:00'])->
                        count();
                }
            }
            return response()->json($docsRequested);
        }
    }
}
