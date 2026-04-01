<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttachmentModel extends Model
{
    protected $table = 'attachments';
    protected $fillable = ['req_id', 'kind', 'filename', 'path'];
}
