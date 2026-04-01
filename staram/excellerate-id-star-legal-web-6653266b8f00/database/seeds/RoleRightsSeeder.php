<?php

use App\Model\RoleRightModel;
use Illuminate\Database\Seeder;

class RoleRightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleRights = json_decode(File::get('database/dummy/role_rights.json'));
        foreach($roleRights as $roleRight)
        {
            RoleRightModel::create(array(
                "role_id"=>$roleRight->role_id,
                "right_id"=>$roleRight->right_id
            ));
        }
    }
}
