<?php

namespace Modules\Search\Domain\Queries;

use App\User;
use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Kizuner\Models\Skill;
use Modules\Search\Traits\Searchable;

class UserQuery
{
  use Searchable;

  public function execute()
  {
    if (app('request')->has('available_status') || $this->category) {
      $sql = DB::table('users')
        ->select(
          'users.id as user_id',
          'users.name as user_name',
          'users.about as user_about',
          'users.birth_date as user_birth_date',
          'users.gender as user_gender',
          'users.address as user_address',
          'locations.address as user_location_address',
          'locations.lat as user_location_lat',
          'locations.lng as user_location_lng',
          'uploads.thumb as user_thumb',
          'uploads.path as user_path',
          'users.social_avatar as social_avatar',
          'users.language as user_language'
        )
        ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
        ->leftJoin('locations', 'users.id', '=', 'locations.locationable_id')
        ->leftJoin('categoryables as category', 'category.categoryable_id', '=', 'users.id')
        ->leftJoin('skillables as skill', 'skill.skillable_id', '=', 'users.id');

      $sql->groupBy('users.id');
      $sql->whereDate('users.created_at', '<', '1990-01-01');
    } else {
      // User specific search
      $age    =   app('request')->input('age');
      $gender =   app('request')->input('gender');
      $language =   app('request')->input('language');

      $categories = [];
      $skills = [];
      if ($this->category) {
        //$categories = Category::where('name', 'like', '%' . $this->category . '%')->get()->pluck('id')->toArray();
        $categories = $this->category;
      }

      if (app('request')->has('skills')) {
        //$skill = app('request')->input('skills');
        $skills = app('request')->input('skills');

        //$skills = Skill::where('name', 'like', '%'.$skill.'%')->get()->pluck('id')->toArray();
        //$skills = Skill::whereIn('id', $skill)->get()->pluck('id')->toArray();
      }

      $sql = DB::table('users')
        ->select(
          'users.id as user_id',
          'users.name as user_name',
          'users.about as user_about',
          'users.birth_date as user_birth_date',
          'users.gender as user_gender',
          'locations.address as user_location_address',
          'locations.lat as user_location_lat',
          'locations.lng as user_location_lng',
          'uploads.thumb as user_thumb',
          'uploads.path as user_path',
          'users.social_avatar as social_avatar',
          'users.language as user_language'
        )
        ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
        ->leftJoin('locations', 'users.id', '=', 'locations.locationable_id')
        ->leftJoin('categoryables as category', 'category.categoryable_id', '=', 'users.id')
        ->leftJoin('skillables as skill', 'skill.skillable_id', '=', 'users.id');

      $sql->where('users.id', '!=', auth()->user()->id)
        ->whereNull('users.deleted')
        ->where(function ($query) {
          $query->where('users.admin', false);
          $query->orWhereNull('users.admin');
        });

      if ($this->query) {
        $sql = $sql->where(function ($query) {
          $query->where('users.name', 'like', $this->query . '%');
          //$query->orWhere('users.email', 'like', '%'.$this->query.'%');
        });
      }

      if ($categories) {
        $sql->where(function ($query) use ($categories) {
          $query->whereIn('category.category_id', $categories);
        });
      }

      if ($skills) {
        $sql->where(function ($query) use ($skills) {
          $query->whereIn('skill.skill_id', $skills);
        });
      }

      if ($age) {
        if ($age == 'all') {
        } else {
          $ageRange = explode('-', $age);
          if (count($ageRange) > 1) {
            $ageMin   = $ageRange[0];
            $ageMax   = $ageRange[1];
            $sql->whereBetween('users.age', [$ageMin, $ageMax]);
          }
        }
      }

      if ($gender) {
        $sql->where('users.gender', $gender);
      }

      if ($language) {
        $sql = $sql->where(function ($query) use ($language) {
          $query->where('users.language', 'like', '%' . $language . '%')->whereNot('users.language', 'en');
        });
      }

      if ($this->location) {
        $address = $this->location['short_address'];
        $address = str_replace(',', '', $address);
        $address = str_replace('-', '', $address);

        $address = explode(" ", $address);

        $sql->where('users.address', 'like', '%' . $address[0] . '%');
      }

      //No query Fake user
      $sql->where('users.is_fake', '<>', 1);
      $sql->groupBy('users.id');
    }


    return $sql->paginate($this->perPage);
  }
}
