<?php

use App\Model\DocType;
use Illuminate\Database\Seeder;

class DocTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $docTypes = json_decode(File::get('database/dummy/docTypes.json'));
        foreach($docTypes as $docType)
        {
            DocType::create(array(
                'type'=>$docType->type,
                'description'=>$docType->description,
                'sla_min'=>$docType->sla_min,
                'sla_max'=>$docType->sla_max,
                'approval_path'=>json_encode($docType->approval_path)
            ));
        }
    }
}
