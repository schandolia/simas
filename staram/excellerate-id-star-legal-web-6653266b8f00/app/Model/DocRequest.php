<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocRequest extends Model
{
    protected $table = 'doc_request';
    protected $fillable = [
        'doc_type','approval_type', 'proposed_by','proposed_date','purpose', 'parties','description',
        'commercial_terms','transaction_value','late_payment_toleration',
        'condition_precedent','termination_terms','payment_terms','delay_penalty','guarantee','agreement_terms',
        'status','nextStatus','requester_id','created_at','updated_at'
    ];
}
