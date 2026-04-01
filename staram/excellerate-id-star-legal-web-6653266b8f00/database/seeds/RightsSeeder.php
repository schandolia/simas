<?php

use App\Model\RightModel;
use Illuminate\Database\Seeder;

class RightsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rights = json_decode(File::get('database/dummy/rights.json'));
        foreach($rights as $right)
        {
            RightModel::create(array(
                'right_name'=>$right->name,
                'description'=>$right->description,
                'approval_col'=>isset($right->column)?$right->column:null
            ));
        }
    }
}
