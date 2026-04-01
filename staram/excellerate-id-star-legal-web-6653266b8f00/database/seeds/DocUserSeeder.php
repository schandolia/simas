<?php

use Illuminate\Database\Seeder;
use App\Model\DocUser;
use Faker\Factory as Faker;
class DocUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        //get All files
        $docs = DB::table('doc_share')->select('id','submitter_id')->get();
        foreach($docs as $doc)
        {
            $userNum = rand(0,5);
            for($i=0;$i<$userNum;)
            {
                try{
                    //get APPROVER and LEGAL except the creator
                    $ApproverLegals = DB::table('users')->select('users.id')->
                        where('users.id', '!=', $doc->submitter_id)->
                        whereIn('roles.kind',['LEGAL','APPROVER'])->
                        join('roles','roles.id','=','users.role_id')->
                        groupBy('users.id')->get();

                    DocUser::create(array(
                        "doc_id"=>$doc->id,
                        "user_id"=>$faker->randomElement($ApproverLegals)->id
                    ));
                    $i++;
                }
                catch(Exception $e)
                {
                    //do nothing
                }
            }
        }
    }
}
