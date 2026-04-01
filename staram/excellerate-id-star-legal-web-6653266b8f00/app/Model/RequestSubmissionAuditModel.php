<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RequestSubmissionAuditModel extends Model
{
    protected $table = 'request_submission_audit';
    public $timestamps = false;
    protected $fillable = ['submitter_id', 'req_id', 'date', 'agreement_number', 'parties', 'transaction_objective',
         'time_period', 'nominal_transaction', 'terms', 'other', 'attachment_name', 'attachment_path', 'status',
         'notes', 'version', 'created_at', 'updated_at'];
}
