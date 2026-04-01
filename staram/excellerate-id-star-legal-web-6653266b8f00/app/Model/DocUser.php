<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DocUser extends Model
{
    protected $table = 'doc_user';
    protected $fillable = ['doc_id','user_id'];
}
