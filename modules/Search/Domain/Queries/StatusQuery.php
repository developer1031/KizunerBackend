<?php


namespace Modules\Search\Domain\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Models\Skill;
use Modules\Search\Traits\Searchable;

class StatusQuery
{
    use Searchable;

    public function execute()
    {
        $age    =  app('request')->input('age');
        $gender =  app('request')->input('gender');


        if(!$this->query) {
            // if(app('request')->has('skills')) {
            //     $this->query = app('request')->input('skills');
            // }
            // if(app('request')->has('categories')) {
            //     $this->query = app('request')->input('categories');
            // }
        }

        $sql = DB::table('statuses')
                    ->select(
                        'users.id as user_id',
                        'users.name as user_name',
                        'uploads.thumb as user_avatar',
                        'users.social_avatar as social_avatar',
                        'statuses.id as status_id',
                        'statuses.status as status_status',
                        'status_upload.thumb as status_thumb',
                        'status_upload.path as status_path',
                        'statuses.created_at as status_created_at',
                        'statuses.updated_at as status_updated_at'
                    )
                    ->join('users', 'users.id', '=', 'statuses.user_id')
                    ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                    ->leftJoin('uploads as status_upload', 'status_upload.uploadable_id', '=', 'statuses.id')
                    ->whereNull('statuses.deleted_at')
                    ->where(function($query) {
                        $query->where('statuses.status', 'like', $this->query.'%');
                        //$query->orwhere('users.name', 'like', '%'.$this->query.'%');
                        //$query->orWhere('users.email', 'like', '%'.$this->query.'%');
                    })
                    ->whereNull('users.deleted')
                    ->where(function($query) {
                        $query->where('users.admin', false);
                        $query->orWhereNull('users.admin');
                    })
                    ->groupBy('statuses.id');


        // if ($age) {
        //     $sql->whereRaw("1 <> 1");
        //     /*
        //     $ageRange = explode('-', $age);
        //     if( count($ageRange) > 1) {
        //         $ageMin   = $ageRange[0];
        //         $ageMax   = $ageRange[1];
        //         $sql->whereBetween('users.age', [$ageMin, $ageMax]);
        //     }
        //     */
        // }
        // else {
        //     if ($gender) {
        //         $sql->where('users.gender', $gender);
        //     }
        //     $sql->whereNull('statuses.deleted_at');
        // }

        if(app('request')->has('available_status') || $this->category || app('request')->has('skills')) {
            $sql->whereRaw("1 <> 1");
        }

        $age = app('request')->input('age');
        $gender =   app('request')->input('gender');
        if($age || $gender) {
            $sql->whereRaw("1 <> 1");
        }

        return $sql->paginate($this->perPage);
    }
}
