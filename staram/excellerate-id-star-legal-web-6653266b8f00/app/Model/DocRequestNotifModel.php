<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocRequestNotifModel extends Model
{
    protected $table = 'doc_request_notifs';
    public $timestamps = false;
    protected $fillable = ['user_id', 'req_id', 'type'];
}
