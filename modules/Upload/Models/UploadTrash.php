<?php

namespace Modules\Upload\Models;

use Illuminate\Database\Eloquent\Model;

class UploadTrash extends Model
{
    protected $table = 'upload_delete_queues';

    protected $fillable = [
        'path',
        'created_at',
        'updated_at'
    ];
}
