<?php

namespace Modules\Report;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'report_reports';

    protected $fillable = [
        'user_id',
        'reference_id',
        'reason',
        'type',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
