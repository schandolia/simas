<?php

namespace App\Http\Controllers;

use App\Model\RoleModel;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterUserController extends Controller
{
    public function registerUser(Request $request)
    {
        $request->validate([
            'input-username'=>['required','max:256'],
            'input-fullname'=>['required','max:256'],
            'input-email'=>['required','email',Rule::unique('users','email')],
            'input-password'=>['required','min:6'],
            'input-password-conf'=>['same:input-password']]
        );
        $defaultRole = (INT)(RoleModel::select('id')->where('kind','=','USER')->first()->id);

        $avatar = 'assets/images/avatars/users/'.rand(1,70).'.png';
        User::create(array(
            'name'=>$request->input('input-username'),
            'fullname'=>$request->input('input-fullname'),
            'email'=>$request->input('input-email'),
            'password'=>Hash::make($request->input('input-password')),
            'avatar'=> $avatar,
            'role_id'=>$defaultRole
        ));
        return response()->json(['success'=>'done']);
    }
}
