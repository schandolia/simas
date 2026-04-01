<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ShareDocNotifEmail;
use App\Model\DocShare;
use App\Model\DocUser;
use App\Model\User;
use DateTime;

class EmailController extends Controller
{
    public function index()
    {
        $dataFull=$this->listAllUsers()[0];
        // dd($data);
        $data = $dataFull['data'];
        $dayRemaining = $dataFull['remainingDays'];
        $username = $dataFull['name'];
        return view('email.expiredFileNotif')->
            with('username',$username)->
            with('docData', $data)->
            with('expirationDay',$dayRemaining);
    }

    public function listAllUsers()
    {
        $emailToSend = array();
        $query = DocShare::getNearExpiredDocument();
        $docsNearExpire = $query->get();
        foreach($docsNearExpire as $doc)
        {
            $now = (new DateTime())->getTimestamp();
            $expiration = (DateTime::createFromFormat('Y-m-d',$doc->expire_date))->getTimestamp();
            $expiredIn = ($expiration - $now)/86400;

            //collect submitter info
            $submitter = User::select('email', 'fullname')->where('id','=', $doc->submitter_id)->first();
            array_push($emailToSend, array(
                "recepient"=>$submitter->email,
                "remainingDays"=>$expiredIn,
                "name"=>$submitter->fullname,
                "data"=>$doc
            ));

            $sharedUsers = DocUser::select('users.fullname','users.email')->where('doc_id','=',$doc->id)->join('users','users.id','=','doc_user.user_id')->get();
            foreach($sharedUsers as $user)
            {
                array_push($emailToSend, array(
                    "recepient"=>$submitter->email,
                    "remainingDays"=>$expiredIn,
                    "name"=>$submitter->fullname,
                    "data"=>$doc
                ));
            }
        }
        return $emailToSend;
    }
}
