<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Support\Facades\Artisan;

class DeploymentController extends Controller
{
    public function install(){
        if(User::count()==0)
        {
            User::create(array(
                'name'=>'admin',
                'fullname'=>'Admin',
                'email'=>'admin@foo.com',
                'role_id'=>7,
                'avatar'=>'assets/images/avatars/users/14.png',
                'password'=>'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
            ));
            Artisan::call('route:cache');
            Artisan::call('storage:link');
            Artisan::call('schedule:run');
            dd('Installation successfull');
        }
        else
            abort(404);
    }
}
