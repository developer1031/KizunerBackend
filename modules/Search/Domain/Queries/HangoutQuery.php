<?php


namespace Modules\Search\Domain\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Category\Models\Category;
use Modules\Kizuner\Models\Skill;
use Modules\Search\Traits\Searchable;

class HangoutQuery
{
    use Searchable;

    public function execute()
    {
        $skills = [];
        $categories = [];

        if($this->query) {
            //$skills = Skill::where('name', 'like', '%'.$this->query.'%')->get()->pluck('id')->toArray();
            $categories = Category::where('name', 'like', '%'.$this->query.'%')->get()->pluck('id')->toArray();
        }

        if($this->category) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $categories = $this->category;
        }

        if(app('request')->has('skills')) {
            $skills = app('request')->input('skills');
            //$skills = Skill::where('name', 'like', '%'.$skill.'%')->get()->pluck('id')->toArray();
        }

        //$age    =   app('request')->input('age');
        //$gender =   app('request')->input('gender');
        if($this->location) {

        } else {
            
        }
        $sql    =   DB::table('hangout_hangouts as hangouts')
                        ->select(
                            'hangouts.id as hangout_id',
                            'hangouts.type as hangout_type',
                            'hangouts.title as hangout_title',
                            'hangouts.description as hangout_description',
                            'hangouts.start as hangout_start',
                            'hangouts.end as hangout_end',
                            'hangouts.schedule as hangout_schedule',
                            'hangouts.capacity as hangout_capacity',
                            'hangouts.available as hangout_available',
                            'hangouts.kizuna as hangout_kizuna',
                            'hangouts.created_at as hangout_created_at',
                            'hangouts.updated_at as hangout_updated_at',
                            'hangouts.available_status as available_status',
                            'hangouts.is_fake as is_fake',
                            'hangouts.cover_img as cover_img',
                            'hangout_uploads.thumb as hangout_cover_thumb',
                            'hangout_uploads.path as hangout_cover_path',
                            'users.id as user_id',
                            'users.name as user_name',
                            'user_uploads.thumb as user_avatar',
                            'users.social_avatar as social_avatar',
                            'hangouts.is_range_price as hangout_is_range_price',
                            'hangouts.min_amount as hangout_min_amount',
                            'hangouts.max_amount as hangout_max_amount',
                            'hangouts.amount as hangout_amount',
                            'hangouts.payment_method as payment_method'
                        )
                        ->join('users', 'users.id', '=', 'hangouts.user_id')
                        ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
                        ->leftJoin('uploads as hangout_uploads', 'hangout_uploads.uploadable_id', '=', 'hangouts.id')
                        ->leftJoin('skillables as sk', 'sk.skillable_id', '=', 'hangouts.id')
                        ->leftJoin('categoryables as category', 'category.categoryable_id', '=', 'hangouts.id')
                        ->whereNull('hangouts.deleted_at');
                        // ->where(function($query) {
                        //     //$query->where('hangouts.start', '>', Carbon::now());
                        //     //$query->orWhereNull('hangouts.start');
                        // });

        $age = app('request')->input('age');
        $gender =   app('request')->input('gender');

        if($this->amount) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('hangouts.amount', $this->amount);
        }

        Log::info($this->paymentMethod);
        if($this->paymentMethod) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('hangouts.payment_method', $this->paymentMethod);
        }

        if($this->offerType) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('hangouts.type', $this->offerType);
        }

        if($age || $gender) {
            $sql->whereRaw("1 <> 1");
        }
        else {
            if($this->query) {
                $sql->where('hangouts.title', 'like', $this->query.'%');
            }

            if(app('request')->has('skills') || $categories) {
                $sql->where(function($query) use ($skills, $categories) {
                    //$query->where('hangouts.title', 'like', '%'.$this->query.'%');
                    //$query->orwhere('users.name', 'like', '%'.$this->query.'%');
                    //$query->orWhere('users.email', 'like', '%'.$this->query.'%');
                    if($skills) {
                        $query->whereIn('sk.skill_id', $skills);
                    }
                    if($categories) {
                        $query->whereIn('category.category_id', $categories);
                    }
                });
            }
            if(app('request')->has('available_status')) {
                $sql->whereIn('hangouts.available_status', ['online', 'combine']);
            }

            // $sql->where(function($query1) {
            //     $query1->whereDate('hangouts.end', '>=', Carbon::now());
            //     $query1->orWhereNull('hangouts.end');
            // });
            // $sql->where(function($query1) {
            //     $query1->whereDate('hangouts.start', '>=', Carbon::now());
            //     $query1->orWhereNull('hangouts.start');
            // });

            //$sql->whereNull('hangouts.deleted');
            //$sql->whereNull('hangouts.room_id');

            /*
            if ($age) {
                $ageRange = explode('-', $age);
                if( count($ageRange) > 1) {
                    $ageMin   = $ageRange[0];
                    $ageMax   = $ageRange[1];
                    $sql->whereBetween('users.age', [$ageMin, $ageMax]);
                }
            }
            if ($gender) {
                $sql->where('users.gender', $gender);
            }
            */

            $sql->where('hangouts.is_completed', 0);
            $sql->whereNull('hangouts.deleted_at');
            
            $sql->orderBy('hangouts.created_at', 'desc')
                //->groupBy('hangouts.id')
                ->groupBy('hangouts.title');
        }

        Log::info('HangoutQuery');
        Log::info(getSql($sql));
        return $sql->paginate($this->perPage);
    }
}
