<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $fillable = ['req_id', 'status_id','assignee_id','assigner_id', 'comments'];
}
