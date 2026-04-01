<?php

namespace App\Console\Commands;

use App\Mail\ShareDocNotifEmail;
use Illuminate\Console\Command;
use App\Model\DocUser;
use App\Model\User;
use App\Model\DocShare;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class sendNotificationEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:sendFileExpirationMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will send notification email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function listAllUsers()
    {
        $query = DocShare::getNearExpiredDocument();

        $authorizedUser = User::select('users.id','users.name','users.fullname','users.email')->
            whereIn('roles.kind', array('APPROVER','LEGAL','ADMIN'))->
            join('roles','roles.id','=','users.role_id')->
            get()->toArray();

        for($i=0;$i<sizeof($authorizedUser);++$i)
        {
            $sharedDocs = clone $query;
            $sharedDocs->where(function($query) use ($authorizedUser,$i){
                $query->where('doc_share_view.submitter_id','=',$authorizedUser[$i]['id'])->
                orWhere('doc_share_view.user_id','=', $authorizedUser[$i]['id']);
            });
            $authorizedUser[$i]['sharedDocs']=($sharedDocs->get()->toArray());
        }

        return $authorizedUser;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = $this->listAllUsers();
        foreach($users as $user)
        {
            if(sizeof($user['sharedDocs'])==0)
	    {
		$this->info($user['email']);
                continue;
            }
	    try{
		$this->info($user['email'].' expired file: '.sizeof($user['sharedDocs']));
               Mail::to($user['email'])->send(new ShareDocNotifEmail($user));
            }
            catch(Exception $e)
            {
                Log::error($e->getMessage());
            }
        }
    }
}
