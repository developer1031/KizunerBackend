<?php


namespace Modules\Search\Domain\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Category\Models\Category;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Skill;
use Modules\Search\Traits\Searchable;

class HelpQuery
{
    use Searchable;

    public function execute()
    {
        $skills = [];
        $categories = [];

        if ($this->query) {
            //$skills = Skill::where('name', 'like', '%'.$this->query.'%')->get()->pluck('id')->toArray();
            //$categories = Category::where('name', 'like', '%'.$this->query.'%')->get()->pluck('id')->toArray();
        }

        if ($this->category) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $categories = $this->category;
        }

        if (app('request')->has('skills')) {
            //$skill = app('request')->input('skills');
            //$skills = Skill::where('name', 'like', '%'.$skill.'%')->get()->pluck('id')->toArray();
            $skills = app('request')->input('skills');
        }

        $age    =   app('request')->input('age');
        $gender =   app('request')->input('gender');

        $sql    =   DB::table('help_helps as helps')
            ->select(
                'helps.id as help_id',
                'helps.title as help_title',
                'helps.description as help_description',
                'helps.start as help_start',
                'helps.end as help_end',
                'helps.budget as help_budget',
                'helps.created_at as help_created_at',
                'helps.updated_at as help_updated_at',
                'helps.available_status as available_status',
                'helps.is_fake as is_fake',
                'helps.cover_img as cover_img',
                'help_uploads.thumb as help_cover_thumb',
                'help_uploads.path as help_cover_path',
                'users.id as user_id',
                'users.name as user_name',
                'user_uploads.thumb as user_avatar',
                'users.social_avatar as social_avatar',
                'helps.is_range_price as help_is_range_price',
                'helps.min_amount as help_min_amount',
                'helps.max_amount as help_max_amount',
                'helps.amount as help_amount',
                'helps.payment_method as payment_method'
            )
            ->join('users', 'users.id', '=', 'helps.user_id')
            ->leftJoin('uploads as user_uploads', 'user_uploads.id', '=', 'users.avatar_id')
            ->leftJoin('uploads as help_uploads', 'help_uploads.uploadable_id', '=', 'helps.id')
            ->leftJoin('skillables as sk', 'sk.skillable_id', '=', 'helps.id')
            ->leftJoin('categoryables as category', 'category.categoryable_id', '=', 'helps.id')
            ->whereNull('helps.deleted_at');
        // ->where(function($query) {
        //     //$query->where('helps.start', '>', Carbon::now());
        //     //$query->orWhereNull('helps.start');
        // });

        $age = app('request')->input('age');
        $gender =   app('request')->input('gender');


        if ($this->amount) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('helps.amount', $this->amount);
        }

        if ($this->paymentMethod) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('helps.payment_method', $this->paymentMethod);
        }

        if ($this->offerType) {
            //$categories = Category::where('name', 'like', '%'.$this->category.'%')->get()->pluck('id')->toArray();
            $sql->where('helps.type', $this->offerType);
        }

        if ($age || $gender) {
            $sql->whereRaw("1 <> 1");
        } else {
            if ($this->query) {
                $sql->where('helps.title', 'like', $this->query . '%');
            }

            $sql->where(function ($query) use ($skills, $categories) {
                //$query->where('helps.title', 'like', '%'.$this->query.'%');
                //$query->orwhere('users.name', 'like', '%'.$this->query.'%');
                //$query->orWhere('users.email', 'like', '%'.$this->query.'%');
                if ($skills) {
                    $query->whereIn('sk.skill_id', $skills);
                }
                if ($categories) {
                    $query->whereIn('category.category_id', $categories);
                }
            })
                ->whereNull('users.deleted')
                ->whereNull('helps.room_id');

            if (app('request')->has('available_status')) {
                $sql->whereIn('helps.available_status', ['online', 'combine']);
            }
            // $sql->where(function($query1) {
            //     $query1->whereDate('helps.end', '>=', Carbon::now());
            //     $query1->orWhereNull('helps.end');
            // });
            // $sql->where(function($query1) {
            //     $query1->whereDate('helps.start', '>=', Carbon::now());
            //     $query1->orWhereNull('helps.start');
            // });


            //            if ($age) {
            //                $ageRange = explode('-', $age);
            //                if( count($ageRange) > 1) {
            //                    $ageMin   = $ageRange[0];
            //                    $ageMax   = $ageRange[1];
            //                    $sql->whereBetween('users.age', [$ageMin, $ageMax]);
            //                }
            //            }
            //            if ($gender) {
            //                $sql->where('users.gender', $gender);
            //            }

            $sql->where('helps.is_completed', 0);
            $sql->whereNull('helps.deleted_at');
            $sql->orderBy('helps.created_at', 'desc')
                //->groupBy('helps.id')
                ->groupBy('helps.title');
        }



        Log::info('helpQuery');
        Log::info(getSql($sql));
        return $sql->paginate($this->perPage);
    }
}
