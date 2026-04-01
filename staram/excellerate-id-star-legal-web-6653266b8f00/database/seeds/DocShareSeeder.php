<?php

use App\Model\DocShare;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\File;

class DocShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        //get APPROVER and LEGAL Users
        $ApproverLegal = DB::table('users')->select('users.id')->
            whereIn('roles.kind',['LEGAL','APPROVER'])->
            join('roles','roles.id','=','users.role_id')->
            groupBy('users.id')->get();
        //get all folders
        $folders = DB::table('folder_share')->select('id')->get();
        //get all Document Types
        $docTypes = DB::table('doc_type')->select('id')->get();
        //lets create 67-150 data
        $num = rand(67,150);

        for($i=0;$i<$num;++$i)
        {
            $attachment = $faker->word . '.doc';
            $this->command->info(base_path('storage/app/public/shared-folder/'.$attachment));
            File::copy(base_path('sample.doc'),base_path('storage/app/public/shared-folder/'.$attachment));
            $agreementDate = date("Y-m-d", rand(strtotime("Jan 01 2019"), strtotime("Oct 01 2019")));
            $expiredDate = date("Y-m-d", rand(strtotime("Oct 01 2019"), strtotime("Dec 31 2019")));
            DocShare::create(array(
                "folder_id"=>(rand(0,100)<70) ? $faker->randomElement($folders)->id : null,
                "doc_name"=>$faker->sentence(3).'doc',
                "company_name"=>$faker->company,
                "doc_type"=>$faker->randomElement($docTypes)->id,
                "agreement_date"=>$agreementDate,
                "agreement_number"=>strtoupper(str_replace(' ','',$faker->text(5).'/'.rand(100,999).'/'.$faker->text(7))),
                "parties"=>$faker->company,
                "expire_date"=>$expiredDate,
                "remark"=>(rand(0,100)<60)?$faker->sentence():null,
                "description"=>(rand(0,100)<80)?$faker->sentence():null,
                "attachment"=>'storage/shared-folder/'.$attachment,
                "submitter_id"=>$faker->randomElement($ApproverLegal)->id
            ));
        }
    }
}
