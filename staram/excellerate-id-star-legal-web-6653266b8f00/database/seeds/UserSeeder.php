<?php
use App\Model\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $users = json_decode(File::get('database/dummy/users.json'));
        foreach($users as $user)
        {
            User::create(array(
                'name'=>$user->name,
                'fullname'=>$faker->name,
                'email'=>$user->email,
                'role_id'=>$user->role,
                'supervisor_id'=>isset($user->supervisor)?$user->supervisor:null,
                'avatar'=>$user->avatar,
                'password'=>$user->password
            ));
        }
    }
}
