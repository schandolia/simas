<?php

use App\Model\RoleModel;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = json_decode(File::get('database/dummy/roles.json'));
        foreach($roles as $role)
        {
            RoleModel::create(array(
                'role_name'=>$role->name,
                'kind'=>$role->kind
            ));
        }
    }
}
