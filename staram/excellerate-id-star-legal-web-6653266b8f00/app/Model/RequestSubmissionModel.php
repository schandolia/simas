<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestSubmissionModel extends Model
{
    protected $table = 'request_submission';
    protected $fillable = ['req_id','submitter_id', 'date', 'agreement_number', 'parties', 'transaction_objective',
        'time_period', 'nominal_transaction', 'terms', 'other', 'attachment_name', 'attachment_path','version'];
}
