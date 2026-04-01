<?php

use App\Model\FolderShare;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FolderShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $ApproverLegal = DB::table('users')->select('users.id')->
                whereIn('roles.kind',['LEGAL','APPROVER'])->
                join('roles','roles.id','=','users.role_id')->
                groupBy('users.id')->get();
        $folderIdx = 1;
        foreach($ApproverLegal as $user)
        {
            //lets say each user create 3-10 folders
            $folderNum = rand(1,5);
            for($idx=0; $idx<$folderNum; ++$idx)
            {
                $FolderLocations = DB::table('folder_share')->select('id')->get();
                $FolderLocation = null;
                if(rand(0,100)<80)
                {
                    try{
                        $FolderLocation = $faker->randomElement($FolderLocations)->id;
                    }
                    catch(Exception $e)
                    {
                        $FolderLocation=null;
                    }
                }
                FolderShare::create(array(
                    'folder_name'=>'Folder ' . $folderIdx,
                    'folder_id'=>$FolderLocation,
                    'creator_id'=>$user->id,
                    'description'=>$faker->sentence(4)
                ));
                $folderIdx++;
            }
        }
    }
}
