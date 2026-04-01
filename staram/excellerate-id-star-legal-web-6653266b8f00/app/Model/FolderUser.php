<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FolderUser extends Model
{
    protected $table = 'folder_user';
    protected $fillable = ['folder_id','user_id'];
}
