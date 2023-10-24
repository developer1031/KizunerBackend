<?php


namespace Modules\Search\Domain\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Guide\Domains\Entities\GuideEntity;
use Modules\Kizuner\Models\Skill;
use Modules\Search\Traits\Searchable;

class VideoQuery
{
    use Searchable;

    public function execute()
    {
        $age = app('request')->input('age');
        $gender =   app('request')->input('gender');
        if(app('request')->has('available_status') || $age || $gender || app('request')->has('skills')) {
            $sql = GuideEntity::whereDate('created_at', '<', '1990-01-01');
        }
        else {
            $query   = app('request')->input('query');
            $video_category = app('request')->input('video_category');
            $categories = app('request')->input('categories');

            /*
            if(!$this->query) {
                if(app('request')->has('skills')) {
                    $query = app('request')->input('skills');
                }
                if(app('request')->has('categories')) {
                    $query = app('request')->input('categories');
                }
            }
            */

            $sql = GuideEntity::query();
            if($query) {
                $sql->where('text', 'like', "$query%");
            }

            if($video_category) {
                $sql = $sql->whereHas('categories', function($query) use ($video_category) {
                    $query->where('categories.id', $video_category);
                });
            }

            //$categories_ids = Category::where('name', 'like', '%'.$query.'%')->where('type', 'video')->get()->pluck('id')->toArray();
            $categories_ids = $categories;
            if($categories_ids) {
                $sql = $sql->orWhereHas('categories', function($query) use ($categories_ids) {
                    $query->whereIn('categories.id', $categories_ids);
                });
            }

            $sql = $sql->orderBy('text');
        }


        return $sql->paginate($this->perPage);
    }
}
