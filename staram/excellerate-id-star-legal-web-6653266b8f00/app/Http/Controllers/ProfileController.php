<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
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

    function index() {
        return view('profile')->with('userInfo', Auth::user())->
            with('newRequestCnt',RequestDocController::_getRequestedDocsCount())->
            with('completedDocsCnt', GenericDocController::_getCompletedDocsCount())->
            with('notif', RequestDocController::_getNotification());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old-password'=>['required',new MatchOldPassword],
            'new-password'=>['required','min:6'],
            'confirm-password'=>['same:new-password']
        ]);
        User::find(Auth::user()->id)->update(['password'=>Hash::make(($request->input('new-password')))]);
        return response()->json(['success'=>'done']);
    }

    public function changeProfile(Request $request)
    {
        $request->validate([
            'username'=>['required','max:256'],
            'fullname'=>['required','max:256'],
            'email'=>['required','email',Rule::unique('users','email')->ignore(Auth::user()->email,'email')],
            'avatar'=>['max:10240']
        ]);
        if($request->input('avatar')!=NULL)
        {

            if(strpos(Auth::user()->avatar,'storage/')===0)
            {
                $oldAvatar=str_replace('storage/','', Auth::user()->avatar);
                Storage::delete($oldAvatar);
            }

            $avatar = $request->file('avatar')->store('avatar');
            User::find(Auth::user()->id)->update([
                "name"=>$request->input('username'),
                "fullname"=>$request->input('fullname'),
                "email"=>$request->input('email'),
                "avatar"=>'storage/'.$avatar
            ]);
        }
        else
        {
            User::find(Auth::user()->id)->update([
                "name"=>$request->input('username'),
                "fullname"=>$request->input('fullname'),
                "email"=>$request->input('email')
            ]);
        }
        return response()->json(['success'=>'done']);
    }
}
