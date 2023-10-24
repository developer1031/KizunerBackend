<?php

namespace Modules\Helps\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Category\Models\Category;
use Modules\Kizuner\Models\Skill;
use Modules\Upload\Models\Upload;

class SampleHelp extends Model
{
    protected $table = 'sample_help';

    protected $fillable = [
        'title',
        'description',
        'type'
    ];

    public function skills()
    {
        return $this->morphToMany(Skill::class, 'skillable');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function media()
    {
        return $this->morphOne(Upload::class, 'uploadable');
    }
}
